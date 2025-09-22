<?php

namespace App\Http\Controllers\Laporan;

use App\Exports\BPPajakExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Exports\SalesReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use PDF;

class Laporan extends Controller
{
    public function index()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $daftar_skpd = DB::table('users')
            ->where('kd_skpd', $kd_skpd)
            ->first(); // Mengambil satu data saja


        return view('laporan.laporan.index', compact('daftar_skpd'));
    }



    public function cetakbku(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $tanggalawal = $request->tanggalawal;
        $tanggalakhir = $request->tanggalakhir;
        $tanggalTtd = $request->tanggalTtd;
        $jenis_print = $request->jenis_print;
        $ttdbendaharadth = $request->ttdbendaharadth;
        $ttdpa_kpa = $request->ttdpa_kpa;

        if ($kd_skpd == 'null' || empty($kd_skpd)) {
            $kd_skpd = '4.01.2.10.0.00.01.0000';
        }

        // Format tanggal
        $tanggalawal = date('Y-m-d', strtotime($tanggalawal));
        $tanggalakhir = date('Y-m-d', strtotime($tanggalakhir));

        // Ambil data SKPD
        $dataSkpd = DB::table('users')
            ->select('name')
            ->where('kd_skpd', $kd_skpd)
            ->first();

        // Hitung saldo awal sebelum periode laporan
        $saldoLalu = DB::table('trhbku')
            ->where('kd_skpd', $kd_skpd)
            ->where('tgl_kas', '<', $tanggalawal)
            ->select(DB::raw('SUM(terima) as total_terima, SUM(keluar) as total_keluar'))
            ->first();

        // Hitung saldo awal
        $saldo = ($saldoLalu->total_terima ?? 0) - ($saldoLalu->total_keluar ?? 0);

        // Ambil data utama dari trhbku untuk periode yang dipilih
        $trhtransout = DB::table('trhbku')
            ->where('kd_skpd', $kd_skpd)
            ->whereBetween('tgl_kas', [$tanggalawal, $tanggalakhir])
            ->orderBy('tgl_kas', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mengumpulkan data untuk mencari detail
        $whereInData = [];
        foreach ($trhtransout as $header) {
            $whereInData[] = [
                'no_kas' => $header->no_kas,
                'id_trhkasin_pkd' => $header->id_trhkasin_pkd ?? null,
                'id_trhtransout' => $header->id_trhtransout ?? null,
                'id_trmpot' => $header->id_trmpot ?? null,
                'id_strpot' => $header->id_strpot ?? null,
            ];
        }

        // Ambil data detail dari trdbku berdasarkan kriteria
        $trdbku = collect();
        foreach ($whereInData as $data) {
            $query = DB::table('trdbku')
                ->where('kd_skpd', $kd_skpd)
                ->where('no_kas', $data['no_kas']);

            // Tambahkan kondisi untuk ID jika ada
            if (!empty($data['id_trhkasin_pkd'])) {
                $query->where('id_trhkasin_pkd', $data['id_trhkasin_pkd']);
            }
            if (!empty($data['id_trhtransout'])) {
                $query->where('id_trhtransout', $data['id_trhtransout']);
            }
            if (!empty($data['id_trmpot'])) {
                $query->where('id_trmpot', $data['id_trmpot']);
            }
            if (!empty($data['id_strpot'])) {
                $query->where('id_strpot', $data['id_strpot']);
            }

            $results = $query->get();
            $trdbku = $trdbku->merge($results);
        }

        // Mengelompokkan data detail berdasarkan no_kas
        $detailGrouped = [];
        foreach ($trdbku as $detail) {
            $key = $detail->no_kas . '-' .
                ($detail->id_trhkasin_pkd ?? '0') . '-' .
                ($detail->id_trhtransout ?? '0') . '-' .
                ($detail->id_trmpot ?? '0') . '-' .
                ($detail->id_strpot ?? '0');

            // Simpan detail berdasarkan kunci uniknya
            if (!isset($detailGrouped[$key])) {
                $detailGrouped[$key] = [];
            }
            $detailGrouped[$key][] = $detail;
        }

        // Ambil data tanda tangan
        $ttdbendahara = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdbendaharadth)
            ->first();

        $ttdpa_kpa1 = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdpa_kpa)
            ->first();


        $data = [
            'dataSkpd' => $dataSkpd,
            'trhtransout' => $trhtransout,
            'detailGrouped' => $detailGrouped,
            'tipe' => $jenis_print,
            'tanggalTtd' => $tanggalTtd,
            'tanggalawal' => $tanggalawal,
            'tanggalakhir' => $tanggalakhir,
            'saldoLalu' => $saldo,
            'bendahara' => $ttdbendahara,
            'pa_kpa' => $ttdpa_kpa1,
        ];

        $view = view('laporan.laporan.cetak_bku', $data);

        if ($jenis_print == 'layar') {
            return $view;
        } elseif ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view->render())
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);

            return $pdf->stream('Laporan_BKU.pdf');
        } else if ($jenis_print == 'excel') {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=laporan_BKU.xls");
            return $view;
        }
    }

    public function cetakbpp(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $tanggalawal = $request->tanggalawal;
        $tanggalakhir = $request->tanggalakhir;
        $tanggalTtd = $request->tanggalTtd;
        $jenis_print = $request->jenis_print;
        $ttdbendaharadth = $request->ttdbendaharadth;
        $ttdpa_kpa = $request->ttdpa_kpa;


        if ($kd_skpd == 'null' || empty($kd_skpd)) {
            $kd_skpd = '4.01.2.10.0.00.01.0000';
        }

        // Format tanggal
        $tanggalawal = date('Y-m-d', strtotime($tanggalawal));
        $tanggalakhir = date('Y-m-d', strtotime($tanggalakhir));

        $saldoLalu = DB::table('trhbppajak')
            ->where('kd_skpd', $kd_skpd)
            ->where('tgl_bukti', '<', $tanggalawal)
            ->select(DB::raw('SUM(terima) as total_terima, SUM(keluar) as total_keluar'))
            ->first();

        // Kemudian hitung saldonya
        $saldo = ($saldoLalu->total_terima ?? 0) - ($saldoLalu->total_keluar ?? 0);

        // Ambil data SKPD
        $dataSkpd = DB::table('users')
            ->select('name')
            ->where('kd_skpd', $kd_skpd)
            ->first();


        // Ambil data utama dari trhtransout
        $trhtransout = DB::table('trhbppajak')
            ->where('kd_skpd', $kd_skpd)
            ->whereBetween('tgl_bukti', [$tanggalawal, $tanggalakhir])
            ->orderBy('created_at', 'asc')
            ->get();


        foreach ($trhtransout as $row) {
            $ebillingList = [];
            $kd_rek_pajak = [];
            $nm_rek_pajak = [];
            $ntpnlist = [];
            if (!empty($row->no_trmpot)) {
                // Jika ada no_trmpot, ambil semua dari trdtrmpot
                $billingData = DB::table('trdtrmpot')
                    ->where('no_bukti', $row->no_trmpot)
                    ->select('ebilling', 'ntpn', 'kd_rek6', 'nm_rek6')
                    ->get();
            } else {
                // Jika tidak ada, gunakan no_strpot untuk ambil dari trdstrpot
                $billingData = DB::table('trdstrpot')
                    ->where('no_bukti', $row->no_strpot)
                    ->select('ebilling', 'ntpn', 'kd_rek6', 'nm_rek6')
                    ->get();
            }

            // Pastikan semua data dimasukkan dalam array
            foreach ($billingData as $billing) {
                $ebillingList[] = $billing->ebilling ?? '-';
                $kd_rek_pajak[] = $billing->kd_rek6 ?? '-';
                $nm_rek_pajak[] = $billing->nm_rek6 ?? '-';
                $ntpnlist[] = $billing->ntpn ?? '-';
            }

            // Gabungkan array menjadi string dengan pemisah koma
            $row->ebilling = !empty($ebillingList) ? implode("<br>", $ebillingList) : '-';
            $row->kd_rek6 = !empty($kd_rek_pajak) ? implode("<br>", $kd_rek_pajak) : '-';
            $row->nm_rek6 = !empty($nm_rek_pajak) ? implode("<br>", $nm_rek_pajak) : '-';
            $row->ntpn = !empty($ntpnlist) ? implode("<br>", $ntpnlist) : '-';
        }


        $ttdbendahara = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdbendaharadth)
            ->first(); // Mengambil satu baris data

        $ttdpa_kpa1 = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdpa_kpa)
            ->first();
        $data = [
            'dataSkpd' => $dataSkpd,
            'trhtransout' => $trhtransout,
            'tipe' => $jenis_print,
            'tanggalTtd' => $tanggalTtd,
            'tanggalawal' => $tanggalawal,
            'tanggalakhir' => $tanggalakhir,
            'saldoLalu' => $saldo,
            'bendahara' => $ttdbendahara,
            'pa_kpa' => $ttdpa_kpa1,
        ];

        $view = view('laporan.laporan.cetak_bpp', $data);

        if ($jenis_print == 'layar') {
            return $view;
        } elseif ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view->render())
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);

            return $pdf->stream('Laporan_BPPajak.pdf');
        } else if ($jenis_print == 'excel') {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=laporan_buku_pembantu_pajak.xls");
            return $view;
        }
    }


    public function cetakrp(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $tanggalawal = $request->tanggalawal;
        $tanggalakhir = $request->tanggalakhir;
        $tanggalTtd = $request->tanggalTtd;
        $jenis_print = $request->jenis_print;
        $ttdbendaharadth = $request->ttdbendaharadth;
        $ttdpa_kpa = $request->ttdpa_kpa;
        $jenis_cetakan = $request->jenis_cetakan;

        if ($kd_skpd == 'null' || empty($kd_skpd)) {
            $kd_skpd = '4.01.2.10.0.00.01.0000';
        }

        // Format tanggal
        $tanggalawal = date('Y-m-d', strtotime($tanggalawal));
        $tanggalakhir = date('Y-m-d', strtotime($tanggalakhir));

        $dataSkpd = DB::table('users')
            ->select('name')
            ->where('kd_skpd', $kd_skpd)
            ->first();

        // Get data based on print type
        if ($jenis_cetakan == 'rekap') {
            // Get monthly summary data
            $monthlyData = DB::table('trdstrpot')
                ->join('trhstrpot', 'trdstrpot.no_bukti', '=', 'trhstrpot.no_bukti')
                ->select(
                    DB::raw('MONTH(trhstrpot.tgl_bukti) as bulan'), // Ubah alias menjadi 'bulan'
                    DB::raw("SUM(CASE
        WHEN trdstrpot.nm_rek6 LIKE '%PPh%21%' OR
             trdstrpot.nm_rek6 LIKE '%PPh 21%' OR
             trdstrpot.nm_rek6 LIKE '%PPh Pasal 21%'
        THEN trdstrpot.nilai ELSE 0 END) as pph21"),
                    DB::raw("SUM(CASE
        WHEN trdstrpot.nm_rek6 LIKE '%PPh%22%' OR
             trdstrpot.nm_rek6 LIKE '%PPh 22%' OR
             trdstrpot.nm_rek6 LIKE '%PPh Pasal 22%'
        THEN trdstrpot.nilai ELSE 0 END) as pph22"),
                    DB::raw("SUM(CASE
        WHEN trdstrpot.nm_rek6 LIKE '%PPh%23%' OR
             trdstrpot.nm_rek6 LIKE '%PPh 23%' OR
             trdstrpot.nm_rek6 LIKE '%PPh Pasal 23%'
        THEN trdstrpot.nilai ELSE 0 END) as pph23"),
                    DB::raw("SUM(CASE
        WHEN trdstrpot.nm_rek6 LIKE '%PPh%4%' OR
             trdstrpot.nm_rek6 LIKE '%PPh 4%' OR
             trdstrpot.nm_rek6 LIKE '%PPh Pasal 4%' OR
             trdstrpot.nm_rek6 LIKE '%PPh Final%'
        THEN trdstrpot.nilai ELSE 0 END) as pph4"),
                    DB::raw("SUM(CASE WHEN trdstrpot.nm_rek6 LIKE '%PPN%' THEN trdstrpot.nilai ELSE 0 END) as ppn")
                )
                ->where('trhstrpot.kd_skpd', $kd_skpd)
                ->whereBetween('trhstrpot.tgl_bukti', [$tanggalawal, $tanggalakhir])
                ->where(function ($query) {
                    $query->where('trdstrpot.nm_rek6', 'LIKE', '%PPh%')
                        ->orWhere('trdstrpot.nm_rek6', 'LIKE', '%PPN%');
                })
                ->groupBy(DB::raw('MONTH(trhstrpot.tgl_bukti)')) // Gunakan ekspresi yang sama dengan SELECT
                ->orderBy('bulan')
                ->get();

            // Get NTPN counts
            $ntpnData = DB::table('trdstrpot')
                ->join('trhstrpot', 'trdstrpot.no_bukti', '=', 'trhstrpot.no_bukti')
                ->select(
                    DB::raw("COUNT(CASE WHEN (trdstrpot.ntpn IS NULL OR trdstrpot.ntpn = '') AND
             (trdstrpot.nm_rek6 LIKE '%PPh%' OR trdstrpot.nm_rek6 LIKE '%PPN%')
             THEN 1 END) as belum_terinput"),
                    DB::raw("COUNT(CASE WHEN trdstrpot.ntpn IS NOT NULL AND trdstrpot.ntpn != '' AND
             (trdstrpot.nm_rek6 LIKE '%PPh%' OR trdstrpot.nm_rek6 LIKE '%PPN%')
             THEN 1 END) as sudah_terinput"),
                    DB::raw('MAX(MONTH(trhstrpot.tgl_bukti)) as bulan_terakhir')
                )
                ->where('trhstrpot.kd_skpd', $kd_skpd)
                ->whereBetween('trhstrpot.tgl_bukti', [$tanggalawal, $tanggalakhir])
                ->where(function ($query) {
                    $query->where('trdstrpot.nm_rek6', 'LIKE', '%PPh%')
                        ->orWhere('trdstrpot.nm_rek6', 'LIKE', '%PPN%');
                })
                ->first();

            // Prepare monthly data for view
            $months = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];

            $monthlySummary = [];
            $totalPph21 = 0;
            $totalPph22 = 0;
            $totalPph23 = 0;
            $totalPph4 = 0;
            $totalPpn = 0;

            foreach ($months as $monthNum => $monthName) {
                $monthData = $monthlyData->firstWhere('bulan', $monthNum);

                $pph21 = $monthData ? $monthData->pph21 : 0;
                $pph22 = $monthData ? $monthData->pph22 : 0;
                $pph23 = $monthData ? $monthData->pph23 : 0;
                $pph4 = $monthData ? $monthData->pph4 : 0;
                $ppn = $monthData ? $monthData->ppn : 0;
                $jumlah = $pph21 + $pph22 + $pph23 + $pph4 + $ppn;

                $monthlySummary[] = [
                    'no' => $monthNum,
                    'bulan' => $monthName,
                    'pph21' => $pph21,
                    'pph22' => $pph22,
                    'pph23' => $pph23,
                    'pph4' => $pph4,
                    'ppn' => $ppn,
                    'jumlah' => $jumlah
                ];

                $totalPph21 += $pph21;
                $totalPph22 += $pph22;
                $totalPph23 += $pph23;
                $totalPph4 += $pph4;
                $totalPpn += $ppn;
            }

            $totalJumlah = $totalPph21 + $totalPph22 + $totalPph23 + $totalPph4 + $totalPpn;

            // Add totals row
            $monthlySummary[] = [
                'no' => '',
                'bulan' => 'Jumlah',
                'pph21' => $totalPph21,
                'pph22' => $totalPph22,
                'pph23' => $totalPph23,
                'pph4' => $totalPph4,
                'ppn' => $totalPpn,
                'jumlah' => $totalJumlah
            ];

            $ttdbendahara = DB::table('masterTtd')
                ->where('kodeSkpd', $kd_skpd)
                ->where('nip', $ttdbendaharadth)
                ->first();

            $ttdpa_kpa1 = DB::table('masterTtd')
                ->where('kodeSkpd', $kd_skpd)
                ->where('nip', $ttdpa_kpa)
                ->first();

            $data = [
                'dataSkpd' => $dataSkpd,
                'monthlySummary' => $monthlySummary,
                'ntpnData' => $ntpnData,
                'tipe' => $jenis_print,
                'tanggalTtd' => $tanggalTtd,
                'tanggalawal' => $tanggalawal,
                'tanggalakhir' => $tanggalakhir,
                'bendahara' => $ttdbendahara,
                'pa_kpa' => $ttdpa_kpa1,
            ];

            $view = view('laporan.laporan.cetak_rp_rekap', $data);
        } else {
            // Original detailed view logic
            $trhstrpot = DB::table('trhstrpot')
                ->where('kd_skpd', $kd_skpd)
                ->whereBetween('tgl_bukti', [$tanggalawal, $tanggalakhir])
                ->orderBy('tgl_bukti', 'asc')
                ->get();

            $ttdbendahara = DB::table('masterTtd')
                ->where('kodeSkpd', $kd_skpd)
                ->where('nip', $ttdbendaharadth)
                ->first();

            $ttdpa_kpa1 = DB::table('masterTtd')
                ->where('kodeSkpd', $kd_skpd)
                ->where('nip', $ttdpa_kpa)
                ->first();

            $data = [
                'dataSkpd' => $dataSkpd,
                'trhstrpot' => $trhstrpot,
                'tipe' => $jenis_print,
                'tanggalTtd' => $tanggalTtd,
                'tanggalawal' => $tanggalawal,
                'tanggalakhir' => $tanggalakhir,
                'bendahara' => $ttdbendahara,
                'pa_kpa' => $ttdpa_kpa1,
            ];

            $view = view('laporan.laporan.cetak_rp_rincian', $data);
        }

        if ($jenis_print == 'layar') {
            return $view;
        } elseif ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view->render())
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);

            return $pdf->stream('Laporan_BPPajak.pdf');
        } else if ($jenis_print == 'excel') {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=laporan_buku_pembantu_pajak.xls");
            return $view;
        }
    }


    public function cetakbpbank(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $tanggalawal = $request->tanggalawal;
        $tanggalakhir = $request->tanggalakhir;
        $tanggalTtd = $request->tanggalTtd;
        $jenis_print = $request->jenis_print;
        $ttdbendaharadth = $request->ttdbendaharadth;
        $ttdpa_kpa = $request->ttdpa_kpa;

        if ($kd_skpd == 'null' || empty($kd_skpd)) {
            $kd_skpd = '4.01.2.10.0.00.01.0000';
        }

        // Format tanggal
        $tanggalawal = date('Y-m-d', strtotime($tanggalawal));
        $tanggalakhir = date('Y-m-d', strtotime($tanggalakhir));

        // Ambil data SKPD
        $dataSkpd = DB::table('users')
            ->select('name')
            ->where('kd_skpd', $kd_skpd)
            ->first();



        $saldoLalu = DB::table('trhbku')
            ->where('kd_skpd', $kd_skpd)
            ->where('tgl_kas', '<', $tanggalawal)
            ->select(DB::raw('SUM(terima) as total_terima, SUM(keluar) as total_keluar'))
            ->first();

        // Kemudian hitung saldonya
        $saldo = ($saldoLalu->total_terima ?? 0) - ($saldoLalu->total_keluar ?? 0);
        // Ambil data utama dari trhtransout
        $trhtransout = DB::table('trhbku')
            ->where('kd_skpd', $kd_skpd)
            ->whereBetween('tgl_kas', [$tanggalawal, $tanggalakhir])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mengambil semua no_bukti dari trhtransout
        $noBuktiList = $trhtransout->pluck('no_kas')->toArray();

        // Ambil data detail dari trdtransout yang sesuai dengan no_bukti dari trhtransout
        $trdtransout = DB::table('trdtransout')
            ->where('kd_skpd', $kd_skpd)
            ->whereIn('no_bukti', $noBuktiList)
            ->get();

        // Mengelompokkan data detail berdasarkan no_bukti
        $detailGrouped = [];
        foreach ($trdtransout as $detail) {
            $detailGrouped[$detail->no_bukti][] = $detail;
        }

        $ttdbendahara = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdbendaharadth)
            ->first(); // Mengambil satu baris data

        $ttdpa_kpa1 = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdpa_kpa)
            ->first();

        $data = [
            'dataSkpd' => $dataSkpd,
            'trhtransout' => $trhtransout,
            'detailGrouped' => $detailGrouped, // Kirim data detail yang sudah dikelompokkan
            'tipe' => $jenis_print,
            'tanggalTtd' => $tanggalTtd,
            'tanggalawal' => $tanggalawal,
            'tanggalakhir' => $tanggalakhir,
            'saldoLalu' => $saldo,
            'bendahara' => $ttdbendahara,
            'pa_kpa' => $ttdpa_kpa1,
        ];

        $view = view('laporan.laporan.cetak_bpbank', $data);

        if ($jenis_print == 'layar') {
            return $view;
        } elseif ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view->render())
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);

            return $pdf->stream('Laporan_BPBANK.pdf');
        } else if ($jenis_print == 'excel') {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=laporan_buku_pembantu_bank.xls");
            return $view;
        }
    }



    public function cetakdth(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $tanggalawal = $request->tanggalawal;
        $tanggalakhir = $request->tanggalakhir;
        $tanggalTtd = $request->tanggalTtd;
        $ttdbendaharadth = $request->ttdbendaharadth;
        $ttdpa_kpa = $request->ttdpa_kpa;
        $jenis_print = $request->jenis_print;

        if ($kd_skpd == 'null' || empty($kd_skpd)) {
            $kd_skpd = '4.01.2.10.0.00.01.0000';
        }

        // Format tanggal
        $tanggalawal = date('Y-m-d', strtotime($tanggalawal));
        $tanggalakhir = date('Y-m-d', strtotime($tanggalakhir));

        // Ambil data SKPD
        $dataSkpd = DB::table('users')
            ->select('name')
            ->where('kd_skpd', $kd_skpd)
            ->first();



        // Ambil data detail dari trdtransout yang sesuai dengan no_bukti dari trhtransout
        $trhtransout = DB::table('trhstrpot')
            ->join('trdstrpot', 'trhstrpot.no_bukti', '=', 'trdstrpot.no_bukti')
            ->join('trhtrmpot', 'trhstrpot.no_terima', '=', 'trhtrmpot.no_bukti')  // Ganti 'some_column' dengan kolom yang relevan untuk join
            ->join('trhtransout', 'trhtrmpot.id_trhtransout', '=', 'trhtransout.no_bukti')  // Ganti 'some_other_column' dengan kolom yang relevan untuk join
            ->where('trhstrpot.kd_skpd', $kd_skpd)
            ->whereBetween('trhstrpot.tgl_bukti', [$tanggalawal, $tanggalakhir])
            ->where(function ($query) {
                $query->where('trdstrpot.nm_rek6', 'LIKE', '%utang PPN%')
                    ->orWhere('trdstrpot.nm_rek6', 'LIKE', '%PPH%');
            })
            ->select('trhstrpot.*', 'trhtransout.total')
            ->distinct()
            ->get();

        foreach ($trhtransout as $row) {
            $ebillingList = [];
            $kd_rek_potong = [];
            $nm_rek_potong = [];
            $ntpnlist = [];
            $nilai = [];

            $billingData = DB::table('trdstrpot')
                ->where('no_bukti', $row->no_bukti)
                ->where(function ($query) {
                    $query->where('trdstrpot.nm_rek6', 'LIKE', '%PPN%')
                        ->orWhere('trdstrpot.nm_rek6', 'LIKE', '%PPH%');
                })
                ->select('ebilling', 'ntpn', 'kd_rek6 as kd_potong', 'nm_rek6 as nm_potong', 'nilai')
                ->get();



            // Pastikan semua data dimasukkan dalam array
            foreach ($billingData as $billing) {
                $ebillingList[] = $billing->ebilling ?? '-';
                $kd_rek_potong[] = $billing->kd_potong ?? '-';
                $nm_rek_potong[] = $billing->nm_potong ?? '-';
                $ntpnlist[] = $billing->ntpn ?? '-';
                $nilai[] = $billing->nilai ?? '-';
            }

            // Gabungkan array menjadi string dengan pemisah koma
            $row->ebilling = !empty($ebillingList) ? implode("<br><hr>", $ebillingList) : '-';
            $row->kd_potong = !empty($kd_rek_potong) ? implode("<br><hr>", $kd_rek_potong) : '-';
            $row->nm_potong = !empty($nm_rek_potong) ? implode("<br><hr>", $nm_rek_potong) : '-';
            $row->ntpn = !empty($ntpnlist) ? implode("<br><hr>", $ntpnlist) : '-';
            $row->nilai = !empty($nilai)
                ? implode("<br><hr>", array_map(fn($n) => 'Rp. ' . number_format(floatval($n), 0, ',', '.'), $nilai))
                : '-';
        }

        $ttdbendahara = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdbendaharadth)
            ->first(); // Mengambil satu baris data

        $ttdpa_kpa1 = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdpa_kpa)
            ->first();

        $data = [
            'dataSkpd' => $dataSkpd,
            'dth' => $trhtransout,
            'tipe' => $jenis_print,
            'tanggalTtd' => $tanggalTtd,
            'tanggalawal' => $tanggalawal,
            'tanggalakhir' => $tanggalakhir,
            'bendahara' => $ttdbendahara,
            'pa_kpa' => $ttdpa_kpa1,
        ];

        $view = view('laporan.laporan.cetak_dth', $data);

        if ($jenis_print == 'layar') {
            return $view;
        } elseif ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view->render())
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);

            return $pdf->stream('Laporan_DTH.pdf');
        } else if ($jenis_print == 'excel') {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=laporan_DTH.xls");
            return $view;
        }
    }

    public function cetakrealisasi(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $tanggalawal = $request->tanggalawal;
        $tanggalakhir = $request->tanggalakhir;
        $tanggalTtd = $request->tanggalTtd;
        $ttdbendaharadth = $request->ttdbendaharadth;
        $ttdpa_kpa = $request->ttdpa_kpa;
        $jenis_print = $request->jenis_print;
        $jenis_anggaran = $request->jenis_anggaran;


        if ($kd_skpd == 'null' || empty($kd_skpd)) {
            $kd_skpd = '4.01.2.10.0.00.01.0000';
        }

        // Format tanggal
        $tanggalawal = date('Y-m-d', strtotime($tanggalawal));
        $tanggalakhir = date('Y-m-d', strtotime($tanggalakhir));

        // Ambil data SKPD
        $dataSkpd = DB::table('users')
            ->select('name')
            ->where('kd_skpd', $kd_skpd)
            ->first();

        // Query untuk mengambil data anggaran
        $trhtransout = DB::table(
            DB::raw("(
            SELECT
                kd_sub_kegiatan,
                nm_sub_kegiatan,
                kd_rek,
                nm_rek,
                MAX(anggaran_tahun) as anggaran_tahun
            FROM
                ms_anggaran
            WHERE
                (jenis_anggaran = ? OR jenis_anggaran IS NULL)
            GROUP BY
                kd_sub_kegiatan,
                nm_sub_kegiatan,
                kd_rek,
                nm_rek
        ) as anggaran")
        )
            ->leftJoin(
                DB::raw("(
            SELECT
                kd_rek6,
                kd_sub_kegiatan,
                SUM(nilai) as total_nilai
            FROM
                trdtransout
            WHERE
                kd_skpd = '$kd_skpd'
                AND jenis_terima_sp2d = '0'
                AND tgl_bukti BETWEEN '$tanggalawal' AND '$tanggalakhir'
            GROUP BY
                kd_rek6,
                kd_sub_kegiatan
        ) as trdtransout"),
                function ($join) {
                    $join->on(
                        DB::raw("CAST(anggaran.kd_rek AS NVARCHAR(100)) COLLATE DATABASE_DEFAULT"),
                        '=',
                        DB::raw("CAST(trdtransout.kd_rek6 AS NVARCHAR(100)) COLLATE DATABASE_DEFAULT")
                    )
                        ->on(
                            DB::raw("CAST(anggaran.kd_sub_kegiatan AS NVARCHAR(100)) COLLATE DATABASE_DEFAULT"),
                            '=',
                            DB::raw("CAST(trdtransout.kd_sub_kegiatan AS NVARCHAR(100)) COLLATE DATABASE_DEFAULT")
                        );
                }
            )
            ->select(
                'anggaran.kd_sub_kegiatan as kd_kegiatan',
                'anggaran.nm_sub_kegiatan as nm_kegiatan',
                'anggaran.kd_rek as kd_rek5',
                'anggaran.nm_rek as nm_rek5',
                'anggaran.anggaran_tahun',
                DB::raw("COALESCE(trdtransout.total_nilai, 0) as nilai")
            )
            ->orderBy('anggaran.kd_sub_kegiatan')
            ->orderBy('anggaran.kd_rek')
            ->setBindings([$jenis_anggaran])
            ->get();

        $ttdbendahara = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdbendaharadth)
            ->first();

        $ttdpa_kpa1 = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdpa_kpa)
            ->first();

        $data = [
            'dataSkpd' => $dataSkpd,
            'trhtransout' => $trhtransout,
            'tipe' => $jenis_print,
            'tanggalTtd' => $tanggalTtd,
            'tanggalawal' => $tanggalawal,
            'tanggalakhir' => $tanggalakhir,
            'bendahara' => $ttdbendahara,
            'pa_kpa' => $ttdpa_kpa1,
        ];

        $view = view('laporan.laporan.cetak_realisasi', $data);

        if ($jenis_print == 'layar') {
            return $view;
        } elseif ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view->render())
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);

            return $pdf->stream('Laporan_DTH.pdf');
        } else if ($jenis_print == 'excel') {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=laporan_realisasi.xls");
            return $view;
        }
    }


    public function cetakspj(Request $request)
    {
        // dd($request->all());
        $kd_skpd = $request->kd_skpd;
        $tanggalawal = $request->tanggalawal;
        $tanggalakhir = $request->tanggalakhir;
        $tanggalTtd = $request->tanggalTtd;
        $ttdbendaharadth = $request->ttdbendaharadth;
        $ttdpa_kpa = $request->ttdpa_kpa;
        $jenis_anggaran_spj = $request->jenis_anggaran_spj;
        $jenis_print = $request->jenis_print;

        if ($kd_skpd == 'null' || empty($kd_skpd)) {
            $kd_skpd = '4.01.2.10.0.00.01.0000';
        }

        // Format tanggal
        $tanggalawal = date('Y-m-d', strtotime($tanggalawal));
        $tanggalakhir = date('Y-m-d', strtotime($tanggalakhir));

        // Ambil data SKPD
        $dataSkpd = DB::table('users')
            ->select('name')
            ->where('kd_skpd', $kd_skpd)
            ->first();

        $trhtransout = DB::table('ms_anggaran')
            ->where('ms_anggaran.jenis_anggaran', $jenis_anggaran_spj)
            ->leftJoin(DB::raw('trdtransout WITH (NOLOCK)'), function ($join) use ($kd_skpd, $tanggalawal, $tanggalakhir) {
                $join->on(
                    DB::raw('CAST(ms_anggaran.kd_rek AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS'),
                    '=',
                    DB::raw('CAST(trdtransout.kd_rek6 AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS')
                )
                    ->on(
                        DB::raw('CAST(ms_anggaran.kd_sub_kegiatan AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS'),
                        '=',
                        DB::raw('CAST(trdtransout.kd_sub_kegiatan AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS')
                    )
                    ->where('trdtransout.kd_skpd', '=', $kd_skpd)
                    ->whereBetween('trdtransout.tgl_bukti', [$tanggalawal, $tanggalakhir]);
            })
            ->leftJoin(DB::raw('trhtransout WITH (NOLOCK)'), function ($join) use ($tanggalawal, $tanggalakhir) {
                $join->on(
                    DB::raw('CAST(trdtransout.no_bukti AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS'),
                    '=',
                    DB::raw('CAST(trhtransout.no_bukti AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS')
                );
            })
            ->leftJoin(DB::raw('trdkasin_pkd WITH (NOLOCK)'), function ($join) use ($kd_skpd) {
                $join->on(
                    DB::raw('CAST(ms_anggaran.kd_rek AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS'),
                    '=',
                    DB::raw('CAST(trdkasin_pkd.kd_rek6 AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS')
                )
                    ->on(
                        DB::raw('CAST(ms_anggaran.kd_sub_kegiatan AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS'),
                        '=',
                        DB::raw('CAST(trdkasin_pkd.kd_sub_kegiatan AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS')
                    )
                    ->where('trdkasin_pkd.kd_skpd', '=', $kd_skpd);
            })
            ->leftJoin(DB::raw('trhkasin_pkd WITH (NOLOCK)'), function ($join) use ($tanggalawal, $tanggalakhir) {
                $join->on(
                    DB::raw('CAST(trdkasin_pkd.no_sts AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS'),
                    '=',
                    DB::raw('CAST(trhkasin_pkd.no_sts AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS')
                )
                    ->whereBetween('trhkasin_pkd.tgl_sts', [$tanggalawal, $tanggalakhir]);
            })
            ->leftJoin(DB::raw('trhtrmpot WITH (NOLOCK)'), function ($join) use ($kd_skpd, $tanggalawal, $tanggalakhir) {
                $join->on(
                    DB::raw('CAST(ms_anggaran.kd_rek AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS'),
                    '=',
                    DB::raw('CAST(trhtrmpot.kd_rek6 AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS')
                )
                    ->on(
                        DB::raw('CAST(ms_anggaran.kd_sub_kegiatan AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS'),
                        '=',
                        DB::raw('CAST(trhtrmpot.kd_sub_kegiatan AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS')
                    )
                    ->where('trhtrmpot.kd_skpd', '=', $kd_skpd)
                    ->whereBetween('trhtrmpot.tgl_bukti', [$tanggalawal, $tanggalakhir]);
            })
            ->leftJoin(DB::raw('trdtrmpot WITH (NOLOCK)'), function ($join) use ($tanggalawal, $tanggalakhir) {
                $join->on(
                    DB::raw('CAST(trhtrmpot.no_bukti AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS'),
                    '=',
                    DB::raw('CAST(trdtrmpot.no_bukti AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS')
                );
            })
            ->leftJoin(DB::raw('trhstrpot WITH (NOLOCK)'), function ($join) use ($kd_skpd, $tanggalawal, $tanggalakhir) {
                $join->on(
                    DB::raw('CAST(ms_anggaran.kd_rek AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS'),
                    '=',
                    DB::raw('CAST(trhstrpot.kd_rek6 AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS')
                )
                    ->on(
                        DB::raw('CAST(ms_anggaran.kd_sub_kegiatan AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS'),
                        '=',
                        DB::raw('CAST(trhstrpot.kd_sub_kegiatan AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS')
                    )
                    ->where('trhstrpot.kd_skpd', '=', $kd_skpd)
                    ->whereBetween('trhstrpot.tgl_bukti', [$tanggalawal, $tanggalakhir]);
            })
            ->leftJoin(DB::raw('trdstrpot WITH (NOLOCK)'), function ($join) use ($tanggalawal, $tanggalakhir) {
                $join->on(
                    DB::raw('CAST(trhstrpot.no_bukti AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS'),
                    '=',
                    DB::raw('CAST(trdstrpot.no_bukti AS NVARCHAR(100)) COLLATE SQL_Latin1_General_CP1_CI_AS')
                );
            })
            ->select(
                'ms_anggaran.kd_sub_kegiatan',
                'ms_anggaran.nm_sub_kegiatan',
                'ms_anggaran.kd_rek',
                'ms_anggaran.nm_rek',
                'ms_anggaran.anggaran_tahun',

                // SPJ-LS Gaji (s.d Bulan lalu)
                DB::raw("COALESCE(SUM(CASE
                WHEN (trhtransout.jenis_beban = 'GAJI'  AND trhtransout.tgl_bukti < '$tanggalawal') THEN trdtransout.nilai
                WHEN (trhkasin_pkd.jns_cp = 'GAJI' AND trhkasin_pkd.tgl_sts < '$tanggalawal') THEN trdkasin_pkd.rupiah
                WHEN (trhtrmpot.beban = 'GAJI' AND trhtrmpot.tgl_bukti < '$tanggalawal') THEN trdtrmpot.nilai
                WHEN (trhstrpot.beban = 'GAJI' AND trhstrpot.tgl_bukti < '$tanggalawal') THEN trdstrpot.nilai
                ELSE 0 END), 0) as spj_ls_gaji_sd_bulan_lalu"),

                // SPJ-LS Gaji (Bulan ini)
                DB::raw("COALESCE(SUM(CASE
                WHEN (trhtransout.jenis_beban = 'GAJI'  AND trhtransout.tgl_bukti BETWEEN '$tanggalawal' AND '$tanggalakhir') THEN trdtransout.nilai
                WHEN (trhkasin_pkd.jns_cp = 'GAJI' AND trhkasin_pkd.tgl_sts BETWEEN '$tanggalawal' AND '$tanggalakhir') THEN trdkasin_pkd.rupiah
                WHEN (trhtrmpot.beban = 'GAJI'  AND trhtrmpot.tgl_bukti BETWEEN '$tanggalawal' AND '$tanggalakhir') THEN trdtrmpot.nilai
                WHEN (trhstrpot.beban = 'GAJI' AND trhstrpot.tgl_bukti BETWEEN '$tanggalawal' AND '$tanggalakhir') THEN trdstrpot.nilai
                ELSE 0 END), 0) as spj_ls_gaji_bulan_ini"),

                // SPJ-LS Barang & Jasa (s.d Bulan lalu)
                DB::raw("COALESCE(SUM(CASE
                WHEN (trhtransout.jenis_beban = 'Barang & Jasa' AND trhtransout.tgl_bukti < '$tanggalawal') THEN trdtransout.nilai
                WHEN (trhkasin_pkd.jns_cp = 'Barang & Jasa' AND trhkasin_pkd.tgl_sts < '$tanggalawal') THEN trdkasin_pkd.rupiah
                WHEN (trhtrmpot.beban = 'Barang & Jasa' AND trhtrmpot.tgl_bukti < '$tanggalawal') THEN trdtrmpot.nilai
                WHEN (trhstrpot.beban = 'Barang & Jasa' AND trhstrpot.tgl_bukti < '$tanggalawal') THEN trdstrpot.nilai
                ELSE 0 END), 0) as spj_ls_barang_sd_bulan_lalu"),

                // SPJ-LS Barang & Jasa (Bulan ini)
                DB::raw("COALESCE(SUM(CASE
                WHEN (trhtransout.jenis_beban = 'Barang & Jasa'  AND trhtransout.tgl_bukti BETWEEN '$tanggalawal' AND '$tanggalakhir') THEN trdtransout.nilai
                WHEN (trhkasin_pkd.jns_cp = 'Barang & Jasa' AND trhkasin_pkd.tgl_sts BETWEEN '$tanggalawal' AND '$tanggalakhir') THEN trdkasin_pkd.rupiah
                WHEN (trhtrmpot.beban = 'Barang & Jasa' AND trhtrmpot.tgl_bukti BETWEEN '$tanggalawal' AND '$tanggalakhir') THEN trdtrmpot.nilai
                WHEN (trhstrpot.beban = 'Barang & Jasa' AND trhstrpot.tgl_bukti BETWEEN '$tanggalawal' AND '$tanggalakhir') THEN trdstrpot.nilai
                ELSE 0 END), 0) as spj_ls_barang_bulan_ini"),

                // SPJ UP/GU/TU (s.d Bulan lalu)
                DB::raw("COALESCE(SUM(CASE
                WHEN (trhtransout.jenis_beban IN ('UP', 'GU', 'TU') AND trhtransout.tgl_bukti < '$tanggalawal') THEN trdtransout.nilai
                WHEN (trhkasin_pkd.jns_cp IN ('UP', 'GU', 'TU') AND trhkasin_pkd.tgl_sts < '$tanggalawal') THEN trdkasin_pkd.rupiah
                WHEN (trhtrmpot.beban IN ('UP', 'GU', 'TU') AND trhtrmpot.tgl_bukti < '$tanggalawal') THEN trdtrmpot.nilai
                WHEN (trhstrpot.beban IN ('UP', 'GU', 'TU') AND trhstrpot.tgl_bukti < '$tanggalawal') THEN trdstrpot.nilai
                ELSE 0 END), 0) as spj_up_gu_tu_sd_bulan_lalu"),

                // SPJ UP/GU/TU (Bulan ini)
                DB::raw("COALESCE(SUM(CASE
                WHEN (trhtransout.jenis_beban IN ('UP', 'GU', 'TU') AND trhtransout.tgl_bukti BETWEEN '$tanggalawal' AND '$tanggalakhir') THEN trdtransout.nilai
                WHEN (trhkasin_pkd.jns_cp IN ('UP', 'GU', 'TU') AND trhkasin_pkd.tgl_sts BETWEEN '$tanggalawal' AND '$tanggalakhir') THEN trdkasin_pkd.rupiah
                WHEN (trhtrmpot.beban IN ('UP', 'GU', 'TU') AND trhtrmpot.tgl_bukti BETWEEN '$tanggalawal' AND '$tanggalakhir') THEN trdtrmpot.nilai
                WHEN (trhstrpot.beban IN ('UP', 'GU', 'TU') AND trhstrpot.tgl_bukti BETWEEN '$tanggalawal' AND '$tanggalakhir') THEN trdstrpot.nilai
                ELSE 0 END), 0) as spj_up_gu_tu_bulan_ini")
            )
            ->groupBy(
                'ms_anggaran.kd_sub_kegiatan',
                'ms_anggaran.nm_sub_kegiatan',
                'ms_anggaran.kd_rek',
                'ms_anggaran.nm_rek',
                'ms_anggaran.anggaran_tahun'
            )
            ->orderBy('ms_anggaran.kd_sub_kegiatan')
            ->orderBy('ms_anggaran.kd_rek')
            ->get();
        // Ambil tanda tangan bendahara dan KPA
        $ttdbendahara = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdbendaharadth)
            ->first();

        $ttdpa_kpa1 = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdpa_kpa)
            ->first();

        // Data yang akan dikirim ke view
        $data = [
            'dataSkpd' => $dataSkpd,
            'trhtransout' => $trhtransout,
            'tipe' => $jenis_print,
            'tanggalTtd' => $tanggalTtd,
            'tanggalawal' => $tanggalawal,
            'tanggalakhir' => $tanggalakhir,
            'bendahara' => $ttdbendahara,
            'pa_kpa' => $ttdpa_kpa1,
        ];

        $view = view('laporan.laporan.cetak_spj', $data);

        if ($jenis_print == 'layar') {
            return $view;
        } elseif ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view->render())
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);

            return $pdf->stream('Laporan_SPJ.pdf');
        } else if ($jenis_print == 'excel') {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=laporan_SPJ.xls");
            return $view;
        }
    }




    public function cetakobjek(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $tanggalawal = $request->tanggalawal;
        $tanggalakhir = $request->tanggalakhir;
        $tanggalTtd = $request->tanggalTtd;
        $ttdbendaharadth = $request->ttdbendaharadth;
        $ttdpa_kpa = $request->ttdpa_kpa;
        $jenis_print = $request->jenis_print;
        $jenis_anggaran = $request->jenis_anggaran;
        $sub_kegiatan = $request->sub_kegiatan;
        $jenis = $request->jenis;
        $akun_belanja = $request->akun_belanja;

        if ($kd_skpd == 'null' || empty($kd_skpd)) {
            $kd_skpd = '4.01.2.10.0.00.01.0000';
        }

        // Format tanggal
        $tanggalawal = date('Y-m-d', strtotime($tanggalawal));
        $tanggalakhir = date('Y-m-d', strtotime($tanggalakhir));

        // Ambil data SKPD
        $dataSkpd = DB::table('users')
            ->select('name')
            ->where('kd_skpd', $kd_skpd)
            ->first();



        $kegiatan = DB::table('trdtransout')
            ->where('kd_sub_kegiatan', $sub_kegiatan)
            ->select('kd_sub_kegiatan', 'nm_sub_kegiatan')
            ->first();
        // $jumlah = DB::table('ms_anggaran')
        //     ->where('kd_rek', $akun_belanja)
        //     ->where('kd_sub_kegiatan', $sub_kegiatan)
        //     ->where('jenis_anggaran', $jenis_anggaran)
        //     ->select(
        //         'kd_rek',
        //         'nm_rek', // pastikan kolom ini ada di tabel ms_anggaran
        //         DB::raw('SUM(anggaran_tahun) as total_anggaran')
        //     )
        //     ->groupBy('kd_rek', 'nm_rek') // karena Anda pakai SUM dan juga ambil kolom lain
        //     ->first();


        $jumlah = DB::table('ms_anggaran')
            ->where('kd_rek', $akun_belanja)
            ->where('kd_sub_kegiatan', $sub_kegiatan)
            ->where('jenis_anggaran', $jenis_anggaran)
            ->select('kd_rek', 'nm_rek', 'anggaran_tahun as total_anggaran')
            ->first();



        // Ambil data detail dari trdtransout yang sesuai dengan no_bukti dari trhtransout
        $trhtransout = DB::table('trhtransout')
            ->join('trdtransout', 'trhtransout.no_bukti', '=', 'trdtransout.no_bukti')
            ->where('trhtransout.kd_skpd', $kd_skpd)
            ->where('trhtransout.jenis_terima_sp2d', "0")
            ->where('trdtransout.kd_sub_kegiatan', $sub_kegiatan)
            ->where('trdtransout.kd_rek6', $akun_belanja)
            ->whereBetween('trhtransout.tgl_bukti', [$tanggalawal, $tanggalakhir])
            ->select('trdtransout.*', 'trhtransout.tgl_bukti', 'trhtransout.ket')
            ->get();

        $trhtransout->transform(function ($item) {
            $item->sumber_dana = $item->nm_dana ?? 'Tidak Ada Sumber Dana';
            return $item;
        });





        $ttdbendahara = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdbendaharadth)
            ->first(); // Mengambil satu baris data

        $ttdpa_kpa1 = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdpa_kpa)
            ->first();

        $data = [
            'dataSkpd' => $dataSkpd,
            'trhtransout' => $trhtransout,
            'tipe' => $jenis_print,
            'tanggalTtd' => $tanggalTtd,
            'tanggalawal' => $tanggalawal,
            'tanggalakhir' => $tanggalakhir,
            'bendahara' => $ttdbendahara,
            'pa_kpa' => $ttdpa_kpa1,
            'jumlah_anggaran' => $jumlah,
            'kegiatan' => $kegiatan
        ];

        $view = view('laporan.laporan.cetak_objek', $data);

        if ($jenis_print == 'layar') {
            return $view;
        } elseif ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view->render())
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);

            return $pdf->stream('Laporan_rincian_objek.pdf');
        } else if ($jenis_print == 'excel') {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=laporan_rincian_objek.xls");
            return $view;
        }
    }


    public function tandaTangan(Request $request)
    {
        $query = DB::table('masterTtd');

        if (!empty($request->kodeSkpd)) {
            $query->where('kodeSkpd', $request->kodeSkpd)
                ->where('kode', 'BK'); // Jika 'bendahara' adalah string, gunakan tanda kutip
        }

        $ttd = $query->get();

        return response()->json($ttd);
    }


    public function tandaTanganPa(Request $request)
    {
        $query = DB::table('masterTtd');

        if (!empty($request->kodeSkpd)) {
            $query->where('kodeSkpd', $request->kodeSkpd)
                ->where('kode', 'PA'); // Jika 'bendahara' adalah string, gunakan tanda kutip
        }

        $ttd = $query->get();

        return response()->json($ttd);
    }


    public function getsubkegiatan(Request $request)
    {
        $kodeSkpd = $request->kodeSkpd;
        $search = $request->q;

        if (!$kodeSkpd) {
            return response()->json([], 400);
        }

        $query = DB::table('trdtransout')
            ->where('kd_skpd', $kodeSkpd)
            ->select('kd_sub_kegiatan', 'nm_sub_kegiatan')
            ->distinct();

        // Tambahkan pencarian jika parameter q ada
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kd_sub_kegiatan', 'like', "%{$search}%")
                    ->orWhere('nm_sub_kegiatan', 'like', "%{$search}%");
            });
        }

        // Ambil beberapa hasil untuk pilihan dropdown
        $results = $query->limit(10)->get();

        return response()->json($results);
    }


    public function getakunbelanja(Request $request)
    {
        $kodeSkpd = $request->kodeSkpd;
        $search = $request->q;

        if (!$kodeSkpd) {
            return response()->json([], 400);
        }

        $query = DB::table('trdtransout')
            ->where('kd_skpd', $kodeSkpd)
            ->select('kd_rek6', 'nm_rek6')
            ->distinct();

        // Tambahkan pencarian jika parameter q ada
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kd_rek6', 'like', "%{$search}%")
                    ->orWhere('nm_rek6', 'like', "%{$search}%");
            });
        }

        // Ambil beberapa hasil untuk pilihan dropdown
        $results = $query->limit(10)->get();

        return response()->json($results);
    }

    public function cetakrinciancp(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $tanggalawal = $request->tanggalawal;
        $tanggalakhir = $request->tanggalakhir;
        $tanggalTtd = $request->tanggalTtd;
        $jenis_print = $request->jenis_print ?? 'layar';
        $ttdbendaharadth = $request->ttdbendaharadth;
        $ttdpa_kpa = $request->ttdpa_kpa;
        $bulanTerpilih = date('m', strtotime($tanggalakhir));
        $tahunTerpilih = date('Y', strtotime($tanggalakhir));

        // Gunakan default jika kd_skpd kosong atau 'null'
        if (empty($kd_skpd) || $kd_skpd == 'null') {
            $kd_skpd = '4.01.2.10.0.00.01.0000';
        }

        // Validasi dan format tanggal
        try {
            $tanggalawal = date('Y-m-d', strtotime($tanggalawal));
            $tanggalakhir = date('Y-m-d', strtotime($tanggalakhir));
        } catch (\Exception $e) {
            return back()->with('error', 'Format tanggal tidak valid.');
        }

        // Ambil nama SKPD
        $dataSkpd = DB::table('users')
            ->select('name')
            ->where('kd_skpd', $kd_skpd)
            ->first();

        // Ambil data transaksi utama dari trhkasin_pkd dengan kolom yang diperlukan
        $trhtransout = DB::table('trhkasin_pkd as trh')
            ->leftJoin('trdkasin_pkd as trd', 'trh.no_sts', '=', 'trd.no_sts')
            ->select(
                'trh.no_sts',
                'trh.tgl_sts',
                'trh.no_sp2d',
                'trh.keterangan',
                'trh.jns_cp',
                DB::raw('SUM(trd.rupiah) as total'),
                DB::raw("CASE WHEN trh.jns_cp = 'UP' THEN SUM(trd.rupiah) ELSE 0 END as up"),
                DB::raw("CASE WHEN trh.jns_cp = 'GU' THEN SUM(trd.rupiah) ELSE 0 END as GU"),
                DB::raw("CASE WHEN trh.jns_cp = 'TU' THEN SUM(trd.rupiah) ELSE 0 END as TU"),
                DB::raw("CASE WHEN trh.jns_cp = 'LS GAJI' THEN SUM(trd.rupiah) ELSE 0 END as gaji"),
                DB::raw("CASE WHEN trh.jns_cp = 'LS Barang & Jasa' THEN SUM(trd.rupiah) ELSE 0 END as barang_jasa"),
            )
            ->where('trh.kd_skpd', $kd_skpd)
            ->whereBetween('trh.tgl_sts', [$tanggalawal, $tanggalakhir])
            ->groupBy('trh.no_sts', 'trh.tgl_sts', 'trh.no_sp2d', 'trh.keterangan', 'trh.jns_cp')
            ->get();

        // Ambil data dari trhbku untuk tambahan data yang belum tercatat di trhkasin_pkd
        $trhbku_data = DB::table('trhbku as trh')
            ->leftJoin('trdbku as trd', 'trh.no_kas', '=', 'trd.no_kas')
            ->leftJoin('trhtrmpot as trm', 'trh.id_trmpot', '=', 'trm.no_bukti')
            ->selectRaw("
            trh.no_kas as no_sts,
            trh.tgl_kas as tgl_sts,
            trh.no_sp2d,
            trd.nm_rek6 as keterangan,
            trd.terima as total,
            trm.beban as jns_cp,
            CASE WHEN trm.beban = 'UP' THEN trd.terima ELSE 0 END as UP,
            CASE WHEN trm.beban = 'GU' THEN 0 ELSE 0 END as GU,
            CASE WHEN trm.beban = 'TU' THEN trd.terima ELSE 0 END as TU,
            CASE WHEN trm.beban = 'LS GAJI' THEN trd.terima ELSE 0 END as gaji,
            CASE WHEN trm.beban = 'LS Barang & Jasa' THEN trd.terima ELSE 0 END as barang_jasa
        ")
            ->where('trh.kd_skpd', $kd_skpd)
            ->where('trd.nm_rek6', 'like', "%Utang Belanja%")
            ->whereNotNull('trh.id_trmpot')
            ->whereNotNull('trd.terima')
            ->whereNull('trh.id_trhkasin_pkd')
            ->where('trd.terima', '!=', '0')
            ->whereBetween('trh.tgl_kas', [$tanggalawal, $tanggalakhir])
            ->get();

        // Gabungkan kedua hasil query
        $trhtransout = $trhtransout->concat($trhbku_data)->sortBy('tgl_sts')->values();

        // Menghitung total per bulan
        $bulanAwal = date('n', strtotime($tanggalawal));
        $bulanAkhir = date('n', strtotime($tanggalakhir));
        $tahun = date('Y', strtotime($tanggalawal));

        $totalPerBulan = [];

        for ($bulan = $bulanAwal; $bulan <= $bulanAkhir; $bulan++) {
            $bulanData = $trhtransout->filter(function ($item) use ($bulan, $tahun) {
                $tglItem = date('Y-n', strtotime($item->tgl_sts));
                return $tglItem == "$tahun-$bulan";
            });

            $namaBulan = date('F', mktime(0, 0, 0, $bulan, 1, $tahun));

            $totalPerBulan[$namaBulan] = [
                'gaji' => $bulanData->sum('gaji'),
                'pot_lain' => $bulanData->sum('pot_lain'),
                'barang_jasa' => $bulanData->sum('barang_jasa'),
                'pihak_ketiga' => $bulanData->sum('pihak_ketiga'),
                'UP' => $bulanData->sum('UP'),
                'GU' => $bulanData->sum('GU'),
                'TU' => $bulanData->sum('TU'),
                'total' => $bulanData->sum('total')
            ];
        }

        $jumlahPeriodeTerpilih1 = DB::table('trhkasin_pkd as trh')
            ->join('trdkasin_pkd as trd', 'trh.no_sts', '=', 'trd.no_sts')
            ->where('trh.kd_skpd', $kd_skpd)
            ->whereBetween('trh.tgl_sts', [$tanggalawal, $tanggalakhir])
            ->sum('trh.total');

        $jumlahPeriodeTerpilih2 = DB::table('trhbku as trh')
            ->leftJoin('trdbku as trd', 'trh.no_kas', '=', 'trd.no_kas')
            ->leftJoin('trhtrmpot as trm', 'trh.id_trmpot', '=', 'trm.no_bukti')
            ->where('trh.kd_skpd', $kd_skpd)
            ->where('trd.nm_rek6', 'like', "%Utang Belanja%")
            ->whereNotNull('trh.id_trmpot')
            ->whereNotNull('trd.terima')
            ->whereNull('trh.id_trhkasin_pkd')
            ->where('trd.terima', '!=', '0')
            ->whereBetween('trh.tgl_kas', [$tanggalawal, $tanggalakhir])
            ->sum('trd.terima'); // Menghapus alias 'as total' karena sum() hanya menerima nama kolom

        // Menjumlahkan hasil kedua query
        $jumlahPeriodeTerpilih = $jumlahPeriodeTerpilih1 + $jumlahPeriodeTerpilih2;
        // Hitung jumlah transaksi sampai bulan sebelumnya

        // Hitung jumlah transaksi sampai bulan yang dipilih
        $jumlahSampaiBulanTerpilih1 = DB::table('trhkasin_pkd as trh')
            ->join('trdkasin_pkd as trd', 'trh.no_sts', '=', 'trd.no_sts')
            ->where('trh.kd_skpd', $kd_skpd)
            ->whereBetween('trh.tgl_sts', [Carbon::parse('2025-01-01'), $tanggalakhir])
            ->sum('trh.total');

        $jumlahSampaiBulanTerpilih2 = DB::table('trhbku as trh')
            ->leftJoin('trdbku as trd', 'trh.no_kas', '=', 'trd.no_kas')
            ->leftJoin('trhtrmpot as trm', 'trh.id_trmpot', '=', 'trm.no_bukti')
            ->where('trh.kd_skpd', $kd_skpd)
            ->where('trd.nm_rek6', 'like', "%Utang Belanja%")
            ->whereNotNull('trh.id_trmpot')
            ->whereNotNull('trd.terima')
            ->whereNull('trh.id_trhkasin_pkd')
            ->where('trd.terima', '!=', '0')
            ->whereBetween('trh.tgl_kas', [Carbon::parse('2025-01-01'), $tanggalakhir])
            ->sum('trd.terima'); // Menghapus alias 'as total' karena sum() hanya menerima nama kolom

        // Menjumlahkan hasil kedua query
        $jumlahSampaiBulanTerpilih = $jumlahSampaiBulanTerpilih1 + $jumlahSampaiBulanTerpilih2;

        // Total keseluruhan
        $totalKeseluruhan = [
            'gaji' => $trhtransout->sum('gaji'),
            'pot_lain' => $trhtransout->sum('pot_lain'),
            'barang_jasa' => $trhtransout->sum('barang_jasa'),
            'pihak_ketiga' => $trhtransout->sum('pihak_ketiga'),
            'up' => $trhtransout->sum('up'),
            'gu' => $trhtransout->sum('gu'),
            'tu' => $trhtransout->sum('tu'),
            'total' => $trhtransout->sum('total')
        ];

        // Ambil tanda tangan bendahara dan PA/KPA
        $ttdbendahara = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdbendaharadth)
            ->first();

        $ttdpa_kpa1 = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdpa_kpa)
            ->first();

        // Prepare fallback data for empty results
        $trhbku = null;
        if ($trhtransout->isEmpty()) {
            $trhbku = DB::table('trhbku')->where('kd_skpd', $kd_skpd)->first();
        }

        $data = [
            'dataSkpd' => $dataSkpd,
            'trhtransout' => $trhtransout,
            'jumlahPeriodeTerpilih' => $jumlahPeriodeTerpilih,
            'jumlahSampaiBulanTerpilih' => $jumlahSampaiBulanTerpilih,
            'bulanAwal' => date('F Y', strtotime($tanggalawal)),
            'bulanAkhir' => date('F Y', strtotime($tanggalakhir)),
            'tipe' => $jenis_print,
            'tanggalTtd' => $tanggalTtd,
            'tanggalawal' => $tanggalawal,
            'tanggalakhir' => $tanggalakhir,
            'bendahara' => $ttdbendahara,
            'pa_kpa' => $ttdpa_kpa1,
            'trhbku' => $trhbku, // For fallback data
        ];

        // Pilih metode cetak
        if ($jenis_print == 'layar') {
            return view('laporan.laporan.cetak_rinciancp', $data);
        } elseif ($jenis_print == 'pdf') {
            $pdf = PDF::loadView('laporan.laporan.cetak_rinciancp', $data)
                ->setPaper('legal', 'landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);

            return $pdf->stream('Laporan_rinciancp.pdf');
        }
    }


    public function cetakregistersp2d(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $tanggalawal = $request->tanggalawal;
        $tanggalakhir = $request->tanggalakhir;
        $tanggalTtd = $request->tanggalTtd;
        $jenis_print = $request->jenis_print ?? 'layar';
        $ttdbendaharadth = $request->ttdbendaharadth;
        $ttdpa_kpa = $request->ttdpa_kpa;
        $jenis_terima_sp2d = $request->jenis_terima_sp2d ?? '1'; // Default to 1 if not provided

        // Gunakan default jika kd_skpd kosong atau 'null'
        if (empty($kd_skpd) || $kd_skpd == 'null') {
            $kd_skpd = '4.01.2.10.0.00.01.0000';
        }

        // Validasi dan format tanggal
        try {
            $tanggalawal = date('Y-m-d', strtotime($tanggalawal));
            $tanggalakhir = date('Y-m-d', strtotime($tanggalakhir));
        } catch (\Exception $e) {
            return back()->with('error', 'Format tanggal tidak valid.');
        }

        // Ambil nama SKPD
        $dataSkpd = DB::table('users')
            ->select('name')
            ->where('kd_skpd', $kd_skpd)
            ->first();

        // Ambil data transaksi utama dari trhkasin_pkd dengan kolom yang diperlukan
        $rawTransactions = DB::table('trhtransout as trh')
            ->leftJoin('trdtransout as trd', 'trh.no_bukti', '=', 'trd.no_bukti')
            ->leftJoin('trhtransout as trh_bj', function ($join) {
                $join->on('trh.no_sp2d', '=', 'trh_bj.no_sp2d')
                    ->where('trh_bj.jenis_terima_sp2d', '0');
            })
            ->select(
                'trh.no_bukti',
                'trh.tgl_bukti',
                'trh.no_sp2d',
                DB::raw("CASE WHEN trh.jenis_beban = 'Barang & Jasa' THEN trh_bj.ket ELSE trh.ket END as ket"),
                'trh.jenis_beban',
                DB::raw('SUM(trd.nilai) as total'),
                DB::raw("CASE WHEN trh.jenis_beban = 'UP' THEN SUM(trd.nilai) ELSE 0 END as UP"),
                DB::raw("CASE WHEN trh.jenis_beban = 'GU' THEN SUM(trd.nilai) ELSE 0 END as GU"),
                DB::raw("CASE WHEN trh.jenis_beban = 'TU' THEN SUM(trd.nilai) ELSE 0 END as TU"),
                DB::raw("CASE WHEN trh.jenis_beban = 'GAJI' THEN SUM(trd.nilai) ELSE 0 END as gaji"),
                DB::raw("CASE WHEN trh.jenis_beban = 'Barang & Jasa' THEN SUM(trd.nilai) ELSE 0 END as barang_jasa"),
                DB::raw("CASE WHEN trh.jenis_beban = 'Pihak Ketiga' THEN SUM(trd.nilai) ELSE 0 END as pihak_ketiga"),
                DB::raw("CASE WHEN trh.jenis_beban = 'Potongan Lain' THEN SUM(trd.nilai) ELSE 0 END as pot_lain")
            )
            ->where('trh.kd_skpd', $kd_skpd)
            ->where('trh.jenis_terima_sp2d', $jenis_terima_sp2d)
            ->whereBetween('trh.tgl_bukti', [$tanggalawal, $tanggalakhir])
            ->groupBy('trh.no_bukti', 'trh.tgl_bukti', 'trh.no_sp2d', 'trh.ket', 'trh_bj.ket', 'trh.jenis_beban')
            ->get();

        // Gabungkan data dengan no_sp2d yang sama
        $trhtransout = collect();
        $groupedByNoSp2d = $rawTransactions->groupBy('no_sp2d');

        foreach ($groupedByNoSp2d as $no_sp2d => $transactions) {
            if (count($transactions) > 1) {
                // Jika ada lebih dari satu transaksi dengan no_sp2d yang sama, gabungkan
                $combinedTransaction = (object)[
                    'no_bukti' => $transactions->first()->no_bukti,
                    'tgl_bukti' => $transactions->first()->tgl_bukti,
                    'no_sp2d' => $no_sp2d,
                    'ket' => $transactions->first()->ket, // Gunakan keterangan dari transaksi pertama
                    'jenis_beban' => $transactions->first()->jenis_beban, // Gunakan jenis beban dari transaksi pertama
                    'total' => $transactions->sum('total'),
                    'UP' => $transactions->sum('UP'),
                    'GU' => $transactions->sum('GU'),
                    'TU' => $transactions->sum('TU'),
                    'gaji' => $transactions->sum('gaji'),
                    'barang_jasa' => $transactions->sum('barang_jasa'),
                    'pihak_ketiga' => $transactions->sum('pihak_ketiga'),
                    'pot_lain' => $transactions->sum('pot_lain')
                ];
                $trhtransout->push($combinedTransaction);
            } else {
                // Jika hanya ada satu transaksi, tambahkan langsung
                $trhtransout->push($transactions->first());
            }
        }

        $bulanAwal = date('n', strtotime($tanggalawal));
        $bulanAkhir = date('n', strtotime($tanggalakhir));
        $tahun = date('Y', strtotime($tanggalawal));

        $totalPerBulan = [];

        for ($bulan = $bulanAwal; $bulan <= $bulanAkhir; $bulan++) {
            $bulanData = $trhtransout->filter(function ($item) use ($bulan, $tahun) {
                $tglItem = date('Y-n', strtotime($item->tgl_bukti));
                return $tglItem == "$tahun-$bulan";
            });

            $namaBulan = date('F', mktime(0, 0, 0, $bulan, 1, $tahun));

            $totalPerBulan[$namaBulan] = [
                'gaji' => $bulanData->sum('gaji'),
                'pot_lain' => $bulanData->sum('pot_lain'),
                'barang_jasa' => $bulanData->sum('barang_jasa'),
                'pihak_ketiga' => $bulanData->sum('pihak_ketiga'),
                'UP' => $bulanData->sum('UP'),
                'GU' => $bulanData->sum('GU'),
                'TU' => $bulanData->sum('TU'),
                'total' => $bulanData->sum('total')
            ];
        }

        $jumlahPeriodeTerpilih1 = DB::table('trhtransout as trh')
            ->join('trdtransout as trd', 'trh.no_bukti', '=', 'trd.no_bukti')
            ->where('trh.kd_skpd', $kd_skpd)
            ->where('trh.jenis_terima_sp2d', $jenis_terima_sp2d)
            ->whereBetween('trh.tgl_bukti', [$tanggalawal, $tanggalakhir])
            ->sum('trd.nilai');

        // Hitung jumlah transaksi sampai bulan yang dipilih
        $jumlahSampaiBulanTerpilih1 = DB::table('trhtransout as trh')
            ->join('trdtransout as trd', 'trh.no_bukti', '=', 'trd.no_bukti')
            ->where('trh.kd_skpd', $kd_skpd)
            ->where('trh.jenis_terima_sp2d', $jenis_terima_sp2d)
            ->whereBetween('trh.tgl_bukti', [Carbon::parse('2025-01-01'), $tanggalakhir])
            ->sum('trd.nilai');

        // Total keseluruhan
        $totalKeseluruhan = [
            'gaji' => $trhtransout->sum('gaji'),
            'pot_lain' => $trhtransout->sum('pot_lain'),
            'barang_jasa' => $trhtransout->sum('barang_jasa'),
            'pihak_ketiga' => $trhtransout->sum('pihak_ketiga'),
            'up' => $trhtransout->sum('UP'),
            'gu' => $trhtransout->sum('GU'),
            'tu' => $trhtransout->sum('TU'),
            'total' => $trhtransout->sum('total')
        ];

        // Ambil tanda tangan bendahara dan PA/KPA
        $ttdbendahara = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdbendaharadth)
            ->first();

        $ttdpa_kpa1 = DB::table('masterTtd')
            ->where('kodeSkpd', $kd_skpd)
            ->where('nip', $ttdpa_kpa)
            ->first();

        // Prepare fallback data for empty results
        $trhbku = null;
        if ($trhtransout->isEmpty()) {
            $trhbku = DB::table('trhbku')->where('kd_skpd', $kd_skpd)->first();
        }

        $data = [
            'dataSkpd' => $dataSkpd,
            'trhtransout' => $trhtransout,
            'jumlahPeriodeTerpilih' => $jumlahPeriodeTerpilih1,
            'jumlahSampaiBulanTerpilih' => $jumlahSampaiBulanTerpilih1,
            'bulanAwal' => date('F Y', strtotime($tanggalawal)),
            'bulanAkhir' => date('F Y', strtotime($tanggalakhir)),
            'tipe' => $jenis_print,
            'tanggalTtd' => $tanggalTtd,
            'tanggalawal' => $tanggalawal,
            'tanggalakhir' => $tanggalakhir,
            'bendahara' => $ttdbendahara,
            'pa_kpa' => $ttdpa_kpa1,
            'trhbku' => $trhbku, // For fallback data
        ];




        $view = view('laporan.laporan.cetak_registersp2d', $data);
        // Pilih metode cetak
        if ($jenis_print == 'layar') {
            return view('laporan.laporan.cetak_registersp2d', $data);
        } elseif ($jenis_print == 'pdf') {
            $pdf = PDF::loadView('laporan.laporan.cetak_registersp2d', $data)
                ->setPaper('legal', 'landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);

            return $pdf->stream('Laporan_registersp2d.pdf');
        } else if ($jenis_print == 'excel') {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=laporan_BKU.xls");
            return $view;
        }
    }
}
