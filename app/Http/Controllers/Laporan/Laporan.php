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

    $data = [
        'dataSkpd' => $dataSkpd,
        'trhtransout' => $trhtransout,
        'detailGrouped' => $detailGrouped, // Kirim data detail yang sudah dikelompokkan
        'tipe' => $jenis_print,
        'tanggalTtd' => $tanggalTtd,
        'tanggalawal' => $tanggalawal,
        'tanggalakhir' => $tanggalakhir,
        'saldoLalu' => $saldo
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


    $data = [
        'dataSkpd' => $dataSkpd,
        'trhtransout' => $trhtransout,
        'tipe' => $jenis_print,
        'tanggalTtd' => $tanggalTtd,
        'tanggalawal' => $tanggalawal,
        'tanggalakhir' => $tanggalakhir,
        'saldoLalu' => $saldo
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
    ->where(function ($query) {
        $query->where('trdstrpot.nm_rek6', 'LIKE', '%PPN%')
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



    // Ambil data detail dari trdtransout yang sesuai dengan no_bukti dari trhtransout
    $trhtransout = DB::table('trhstrpot')
    ->join('trdstrpot', 'trhstrpot.no_bukti', '=', 'trdstrpot.no_bukti')
    ->join('trhtrmpot', 'trhstrpot.no_terima', '=', 'trhtrmpot.no_bukti')  // Ganti 'some_column' dengan kolom yang relevan untuk join
    ->join('trhtransout', 'trhtrmpot.id_trhtransout', '=', 'trhtransout.no_bukti')  // Ganti 'some_other_column' dengan kolom yang relevan untuk join
    ->where('trhstrpot.kd_skpd', $kd_skpd)
    ->where(function ($query) {
        $query->where('trdstrpot.nm_rek6', 'LIKE', '%PPN%')
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

public function tandaTangan(Request $request)
{
    $query = DB::table('masterTtd');

    if (!empty($request->kodeSkpd)) {
        $query->where('kodeSkpd', $request->kodeSkpd)
              ->where('jabatan', 'Bendahara Pengeluaran'); // Jika 'bendahara' adalah string, gunakan tanda kutip
    }

    $ttd = $query->get();

    return response()->json($ttd);
}


public function tandaTanganPa(Request $request)
{
    $query = DB::table('masterTtd');

    if (!empty($request->kodeSkpd)) {
        $query->where('kodeSkpd', $request->kodeSkpd)
              ->where('jabatan', 'Pengguna Anggaran'); // Jika 'bendahara' adalah string, gunakan tanda kutip
    }

    $ttd = $query->get();

    return response()->json($ttd);
}


}
