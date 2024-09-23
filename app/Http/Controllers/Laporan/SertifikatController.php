<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class SertifikatController extends Controller
{
    public function index()
    {
        $data = [
            'daftar_skpd' => DB::table('masterSkpd')
                ->get(),
            'daftarTtd' => DB::table('masterTtd')
                ->where('kodeSkpd', '5.02.0.00.0.00.02.0000')
                ->get(),
        ];

        return view('laporan.sertifikat.index')->with($data);
    }

    public function balikNama(Request $request)
    {
        $balikNama1 = DB::table('masterSertifikat')
            ->select('balikNama as balikNama')
            ->where(function ($query) use ($request) {
                if ($request->kodeSkpd) {
                    $query->where('kodeSkpd', $request->kodeSkpd);
                }
            })
            ->groupBy('balikNama');

        $balikNama2 = DB::query()
            ->select(DB::raw("'Keseluruhan' as balikNama"))->unionAll($balikNama1);

        $daftarbalikNama = DB::table(DB::raw("({$balikNama2->toSql()}) AS sub"))
            ->select('balikNama')
            ->mergeBindings($balikNama2)
            ->get();

        return response()->json($daftarbalikNama);
    }

    public function hak(Request $request)
    {

        $hak1 = DB::table('masterSertifikat')
            ->select('Hak')
            ->where(function ($query) use ($request) {
                if ($request->kodeSkpd) {
                    $query->where('kodeSkpd', $request->kodeSkpd);
                }
            })
            ->groupBy('Hak');

        $hak2 = DB::query()
            ->select(DB::raw("'Keseluruhan' as Hak"))
            ->unionAll($hak1);

        $daftarhak = DB::table(DB::raw("({$hak2->toSql()}) AS sub"))
            ->select('Hak')
            ->mergeBindings($hak2)
            ->get();

        return response()->json($daftarhak);
    }

    public function asalUsul(Request $request)
    {

        $asalUsul1 = DB::table('masterSertifikat')
            ->select('asalUsul')
            ->where(function ($query) use ($request) {
                if ($request->kodeSkpd) {
                    $query->where('kodeSkpd', $request->kodeSkpd);
                }
            })
            ->where(function ($query) use ($request) {
                if ($request->Hak) {
                    $query->where('Hak', $request->Hak);
                }
            })
            ->groupBy('asalUsul');

        $asalUsul2 = DB::query()
            ->select(DB::raw("'Keseluruhan' as asalUsul"))
            ->unionAll($asalUsul1);

        $daftarasalUsul = DB::table(DB::raw("({$asalUsul2->toSql()}) AS sub"))
            ->select('asalUsul')
            ->mergeBindings($asalUsul2)
            ->get();

        return response()->json($daftarasalUsul);
    }

    public function tandaTangan(Request $request)
    {

        $ttd = DB::table('masterTtd')
            ->where(function ($query) use ($request) {
                if ($request->kodeSkpd) {
                    $query->where('kodeSkpd', $request->kodeSkpd);
                }
            })
            ->get();

        return response()->json($ttd);
    }

    public function cetakRekapSertifikat(Request $request)
    {
        $pilihan = $request->pilihan;
        $kd_skpd = $request->kd_skpd;
        $balikNama = $request->balikNama;
        $Hak = $request->Hak;
        $asalUsul = $request->asalUsul;
        $ttd = $request->ttd;
        $tanggalTtd = $request->tanggalTtd;
        $jenis_print = $request->jenis_print;

        $data = [
            'dataSkpd' => DB::table('masterSkpd')
                ->select('namaSkpd')
                ->where(function ($query) use ($kd_skpd) {
                    if ($kd_skpd != 'null') {
                        $query->where('kodeSkpd', $kd_skpd);
                    } else {
                        $query->where('kodeSkpd', '5.02.0.00.0.00.02.0000');
                    }
                })
                ->first(),
            'dataSertifikat' => DB::table('masterSertifikat as a')
                ->selectRaw("a.*, (select TOP 1 namaKsbtgn from pinjamanSertifikat b where a.nomorRegister=b.nomorRegister and a.kodeSkpd=b.kodeSkpd order by id) AS namaPemakai,(select namaSkpd from masterSkpd where a.kodeSkpd=kodeSkpd) as namaSkpd")
                ->where(function ($query) use ($kd_skpd, $balikNama, $Hak, $asalUsul) {
                    if ($kd_skpd != 'null') {
                        $query->where('a.kodeSkpd', $kd_skpd);
                    }
                    if ($balikNama != 'Keseluruhan') {
                        $query->where('a.balikNama', $balikNama);
                    }
                    if ($Hak != 'Keseluruhan') {
                        $query->where('a.Hak', $Hak);
                    }
                    if ($asalUsul != 'Keseluruhan') {
                        $query->where('a.asalUsul', $asalUsul);
                    }
                })
                ->get(),
            'tandaTangan' => DB::table('masterTtd')
                ->where(['nip' => $ttd])
                ->first(),
            'tipe' => $jenis_print,
            'tanggalTtd' => $tanggalTtd,
            'pilihan' => $pilihan
        ];

        $view = view('laporan.sertifikat.rekapSertifikat')->with($data);

        if ($jenis_print == 'layar') {
            return $view;
        } else if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('LaporanRekapSertifikat.pdf');
        }
    }

    public function cetakRekapPeminjaman(Request $request)
    {
        $pilihan = $request->pilihan;
        $kd_skpd = $request->kd_skpd;
        $balikNama = $request->balikNama;
        $Hak = $request->Hak;
        $asalUsul = $request->asalUsul;
        $ttd = $request->ttd;
        $tanggalTtd = $request->tanggalTtd;
        $jenis_print = $request->jenis_print;
        $tanggal_awal = $request->tanggal_awal;
        $tanggal_akhir = $request->tanggal_akhir;
        $data = [
            'dataSkpd' => DB::table('masterSkpd')
                ->select('namaSkpd')
                ->where(function ($query) use ($kd_skpd) {
                    if ($kd_skpd != 'null') {
                        $query->where('kodeSkpd', $kd_skpd);
                    } else {
                        $query->where('kodeSkpd', '5.02.0.00.0.00.02.0000');
                    }
                })
                ->first(),
            'dataSertifikat' => DB::table('masterSertifikat as a')
            ->selectRaw("a.*,
                (select TOP 1 namaKsbtgn
                 from pinjamanSertifikat b
                 where a.nomorRegister = b.nomorRegister
                   and a.kodeSkpd = b.kodeSkpd
                   and b.tanggalPinjam is not null
                 order by b.id) AS namaPemakai,
                (select namaSkpd from masterSkpd
                 where a.kodeSkpd = kodeSkpd) as namaSkpd")
            ->where(function ($query) use ($kd_skpd, $Hak, $asalUsul, $balikNama) {
                if ($kd_skpd != 'null') {
                    $query->where('a.kodeSkpd', $kd_skpd);
                }
                if ($Hak != 'Keseluruhan') {
                    $query->where('a.Hak', $Hak);
                }
                if ($asalUsul != 'Keseluruhan') {
                    $query->where('a.asalUsul', $asalUsul);
                }
                if ($balikNama != 'Keseluruhan') {
                    $query->where('a.balikNama', $balikNama);
                }
            })
            ->leftJoin('pinjamanSertifikat as b', function ($join) use ($tanggal_awal, $tanggal_akhir) {
                $join->on('a.nomorRegister', '=', 'b.nomorRegister')
                     ->on('a.kodeSkpd', '=', 'b.kodeSkpd');
                // Kondisi tanggal harus dalam join untuk mengakses 'b.tanggalPinjam'
                if ($tanggal_awal && $tanggal_akhir) {
                    $join->whereBetween('b.tanggalPinjam', [$tanggal_awal, $tanggal_akhir]);
                }
            })
            ->get(),
            'tandaTangan' => DB::table('masterTtd')
                ->where(['nip' => $ttd])
                ->first(),
            'tipe' => $jenis_print,
            'tanggalTtd' => $tanggalTtd,
            'pilihan' => $pilihan
        ];

        $view = view('laporan.sertifikat.rekapPeminjaman')->with($data);

        if ($jenis_print == 'layar') {
            return $view;
        } else if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('LaporanRekapPeminjaman.pdf');
        }
    }
}
