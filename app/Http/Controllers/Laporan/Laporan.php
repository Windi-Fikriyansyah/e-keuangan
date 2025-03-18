<?php

namespace App\Http\Controllers\Laporan;

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
                    ->select('ebilling','ntpn','kd_rek6','nm_rek6')
                    ->get();
            } else {
                // Jika tidak ada, gunakan no_strpot untuk ambil dari trdstrpot
                $billingData = DB::table('trdstrpot')
                    ->where('no_bukti', $row->no_strpot)
                    ->select('ebilling','ntpn','kd_rek6','nm_rek6')
                    ->get();
            }

            // Pastikan semua data dimasukkan dalam array
            foreach ($billingData as $billing) {
                $ebillingList[] = $billing->ebilling ?? '-';
                $kd_rek_pajak [] = $billing->kd_rek6 ?? '-';
                $nm_rek_pajak [] = $billing->nm_rek6 ?? '-';
                $ntpnlist [] = $billing->ntpn ?? '-';
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
                ->select('ebilling','ntpn','kd_rek6 as kd_potong','nm_rek6 as nm_potong','nilai')
                ->get();



        // Pastikan semua data dimasukkan dalam array
        foreach ($billingData as $billing) {
            $ebillingList[] = $billing->ebilling ?? '-';
            $kd_rek_potong [] = $billing->kd_potong ?? '-';
            $nm_rek_potong [] = $billing->nm_potong ?? '-';
            $ntpnlist [] = $billing->ntpn ?? '-';
            $nilai [] = $billing->nilai ?? '-';
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
        ->leftJoin('trdtransout', function ($join) use ($kd_skpd, $tanggalawal, $tanggalakhir) {
            $join->on(
                    DB::raw("CAST(ms_anggaran.kd_rek AS NVARCHAR(100)) COLLATE DATABASE_DEFAULT"),
                    '=',
                    DB::raw("CAST(trdtransout.kd_rek6 AS NVARCHAR(100)) COLLATE DATABASE_DEFAULT")
                )
                ->on(
                    DB::raw("CAST(ms_anggaran.kd_sub_kegiatan AS NVARCHAR(100)) COLLATE DATABASE_DEFAULT"),
                    '=',
                    DB::raw("CAST(trdtransout.kd_sub_kegiatan AS NVARCHAR(100)) COLLATE DATABASE_DEFAULT")
                )
                ->where('trdtransout.kd_skpd', '=', $kd_skpd)
                ->where('trdtransout.jenis_terima_sp2d', '=', "0")
                ->whereBetween('trdtransout.tgl_bukti', [$tanggalawal, $tanggalakhir]);
        })
        ->leftJoin('ms_sub_kegiatan', function ($join) {
            $join->on(
                DB::raw("CAST(ms_anggaran.kd_sub_kegiatan AS NVARCHAR(100)) COLLATE DATABASE_DEFAULT"),
                '=',
                DB::raw("CAST(ms_sub_kegiatan.kd_sub_kegiatan AS NVARCHAR(100)) COLLATE DATABASE_DEFAULT")
            );
        })
        ->select(
            'ms_anggaran.kd_sub_kegiatan as kd_kegiatan',
            'ms_anggaran.nm_sub_kegiatan as nm_kegiatan',
            'ms_anggaran.kd_rek as kd_rek5',
            'ms_anggaran.nm_rek as nm_rek5',
            'ms_anggaran.anggaran_tahun',
            DB::raw("SUM(trdtransout.nilai) as nilai") // Menjumlahkan nilai jika kd_rek5 sama
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
    }
}

public function cetakspj(Request $request)
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

    // Ambil data transaksi berdasarkan kd_sub_kegiatan dan kd_rek
    $trhtransout = DB::table('ms_anggaran')
        ->leftJoin('trdtransout', function ($join) use ($kd_skpd, $tanggalawal, $tanggalakhir) {
            $join->on('ms_anggaran.kd_rek', '=', 'trdtransout.kd_rek6')
                ->on('ms_anggaran.kd_sub_kegiatan', '=', 'trdtransout.kd_sub_kegiatan')
                ->where('trdtransout.kd_skpd', '=', $kd_skpd)
                ->whereBetween('trdtransout.tgl_bukti', [$tanggalawal, $tanggalakhir]);
        })
        ->leftJoin('trdkasin_pkd', function ($join) use ($kd_skpd, $tanggalawal, $tanggalakhir) {
            $join->on('ms_anggaran.kd_rek', '=', 'trdkasin_pkd.kd_rek6')
                ->on('ms_anggaran.kd_sub_kegiatan', '=', 'trdkasin_pkd.kd_sub_kegiatan')
                ->where('trdkasin_pkd.kd_skpd', '=', $kd_skpd)
                ->whereBetween('trdkasin_pkd.tgl_bukti', [$tanggalawal, $tanggalakhir]);
        })
        ->leftJoin('trdtrmpot', function ($join) use ($kd_skpd, $tanggalawal, $tanggalakhir) {
            $join->on('ms_anggaran.kd_rek', '=', 'trdtrmpot.kd_rek6')
                ->on('ms_anggaran.kd_sub_kegiatan', '=', 'trdtrmpot.kd_sub_kegiatan')
                ->where('trdtrmpot.kd_skpd', '=', $kd_skpd)
                ->whereBetween('trdtrmpot.tgl_bukti', [$tanggalawal, $tanggalakhir]);
        })
        ->leftJoin('trdstrpot', function ($join) use ($kd_skpd, $tanggalawal, $tanggalakhir) {
            $join->on('ms_anggaran.kd_rek', '=', 'trdstrpot.kd_rek6')
                ->on('ms_anggaran.kd_sub_kegiatan', '=', 'trdstrpot.kd_sub_kegiatan')
                ->where('trdstrpot.kd_skpd', '=', $kd_skpd)
                ->whereBetween('trdstrpot.tgl_bukti', [$tanggalawal, $tanggalakhir]);
        })
        ->select(
            'ms_anggaran.kd_sub_kegiatan',
            'ms_anggaran.nm_sub_kegiatan',
            'ms_anggaran.kd_rek',
            'ms_anggaran.nm_rek',
            'ms_anggaran.anggaran_tahun',
            DB::raw("COALESCE(SUM(CASE WHEN trdtransout.jns_beban = 'LS' THEN trdtransout.nilai ELSE 0 END), 0) as total_ls"),
            DB::raw("COALESCE(SUM(CASE WHEN trdkasin_pkd.jns_beban = 'UP' THEN trdkasin_pkd.nilai ELSE 0 END), 0) as total_up"),
            DB::raw("COALESCE(SUM(CASE WHEN trdtrmpot.jns_beban = 'GU' THEN trdtrmpot.nilai ELSE 0 END), 0) as total_gu"),
            DB::raw("COALESCE(SUM(CASE WHEN trdstrpot.jns_beban = 'TU' THEN trdstrpot.nilai ELSE 0 END), 0) as total_tu")
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

        return $pdf->stream('Laporan_DTH.pdf');
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
    ->select('kd_sub_kegiatan','nm_sub_kegiatan')
    ->first();
    $jumlah = DB::table('ms_anggaran')
    ->where('kd_rek', $akun_belanja)
    ->where('kd_sub_kegiatan', $sub_kegiatan)
    ->select('*')
    ->first();

    // Ambil data detail dari trdtransout yang sesuai dengan no_bukti dari trhtransout
    $trhtransout = DB::table('trhtransout')
    ->join('trdtransout', 'trhtransout.no_bukti', '=', 'trdtransout.no_bukti')
    ->where('trhtransout.kd_skpd', $kd_skpd)
    ->where('trhtransout.jenis_terima_sp2d', "0")
    ->where('trdtransout.kd_sub_kegiatan', $sub_kegiatan)
    ->where('trdtransout.kd_rek6', $akun_belanja)
    ->whereBetween('trhtransout.tgl_bukti', [$tanggalawal, $tanggalakhir])
    ->select('trdtransout.*','trhtransout.tgl_bukti','trhtransout.ket')
    ->get();

    $kd_dana_list = $trhtransout->pluck('sumber')->unique()->filter();

    $sumber = DB::table('ms_sumberdana')
    ->whereIn('id', $kd_dana_list->toArray())
    ->get()
    ->keyBy('id');

    $trhtransout->transform(function ($item) use ($sumber) {
        $item->sumber_dana = $sumber[$item->sumber]->sumber_dana ?? 'Tidak Ada Sumber Dana';
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
        $query->where(function($q) use ($search) {
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
        $query->where(function($q) use ($search) {
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

}
