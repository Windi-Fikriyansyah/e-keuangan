<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class BPKBController extends Controller
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

        return view('laporan.bpkb.index')->with($data);
    }

    public function tahun(Request $request)
    {
        $tahun1 = DB::table('masterBpkb')
            ->select('tahunPembuatan as tahun')
            ->where(function ($query) use ($request) {
                if ($request->kodeSkpd) {
                    $query->where('kodeSkpd', $request->kodeSkpd);
                }
            })
            ->groupBy('tahunPembuatan');

        $tahun2 = DB::query()
            ->select(DB::raw("'Keseluruhan' as tahun"))->unionAll($tahun1);

        $daftarTahun = DB::table(DB::raw("({$tahun2->toSql()}) AS sub"))
            ->select('tahun')
            ->mergeBindings($tahun2)
            ->get();

        return response()->json($daftarTahun);
    }

    public function jenis(Request $request)
    {

        $jenis1 = DB::table('masterBpkb')
            ->select('jenis')
            ->where(function ($query) use ($request) {
                if ($request->kodeSkpd) {
                    $query->where('kodeSkpd', $request->kodeSkpd);
                }
            })
            ->groupBy('jenis');

        $jenis2 = DB::query()
            ->select(DB::raw("'Keseluruhan' as jenis"))
            ->unionAll($jenis1);

        $daftarJenis = DB::table(DB::raw("({$jenis2->toSql()}) AS sub"))
            ->select('jenis')
            ->mergeBindings($jenis2)
            ->get();

        return response()->json($daftarJenis);
    }

    public function merk(Request $request)
    {

        $merk1 = DB::table('masterBpkb')
            ->select('merk')
            ->where(function ($query) use ($request) {
                if ($request->kodeSkpd) {
                    $query->where('kodeSkpd', $request->kodeSkpd);
                }
            })
            ->where(function ($query) use ($request) {
                if ($request->jenis) {
                    $query->where('jenis', $request->jenis);
                }
            })
            ->groupBy('merk');

        $merk2 = DB::query()
            ->select(DB::raw("'Keseluruhan' as merk"))
            ->unionAll($merk1);

        $daftarMerk = DB::table(DB::raw("({$merk2->toSql()}) AS sub"))
            ->select('merk')
            ->mergeBindings($merk2)
            ->get();

        return response()->json($daftarMerk);
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

    public function cetakRekapBpkb(Request $request)
    {
        $pilihan = $request->pilihan;
        $kd_skpd = $request->kd_skpd;
        $tahun = $request->tahun;
        $jenis = $request->jenis;
        $merk = $request->merk;
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
            'dataBpkb' => DB::table('masterBpkb as a')
                ->selectRaw("a.*, (select TOP 1 namaPbp from pinjamanBpkb b where a.nomorRegister=b.nomorRegister and a.kodeSkpd=b.kodeSkpd order by id) AS namaPemakai,(select namaSkpd from masterSkpd where a.kodeSkpd=kodeSkpd) as namaSkpd")
                ->where(function ($query) use ($kd_skpd, $tahun, $jenis, $merk) {
                    if ($kd_skpd != 'null') {
                        $query->where('a.kodeSkpd', $kd_skpd);
                    }
                    if ($tahun != 'Keseluruhan') {
                        $query->where('a.tahunPembuatan', $tahun);
                    }
                    if ($jenis != 'Keseluruhan') {
                        $query->where('a.jenis', $jenis);
                    }
                    if ($merk != 'Keseluruhan') {
                        $query->where('a.merk', $merk);
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

        $view = view('laporan.bpkb.rekapBpkb')->with($data);

        if ($jenis_print == 'layar') {
            return $view;
        } else if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('LaporanRekapBPKB.pdf');
        }
    }

    public function cetakRekapPeminjaman(Request $request)
    {
        $pilihan = $request->pilihan;
        $kd_skpd = $request->kd_skpd;
        $jenis = $request->jenis;
        $merk = $request->merk;
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
            'dataBpkb' => DB::table('masterBpkb as a')
                ->selectRaw("a.*, (select TOP 1 namaPbp from pinjamanBpkb b where a.nomorRegister=b.nomorRegister and a.kodeSkpd=b.kodeSkpd order by id) AS namaPemakai,(select namaSkpd from masterSkpd where a.kodeSkpd=kodeSkpd) as namaSkpd")
                ->where(function ($query) use ($kd_skpd, $jenis, $merk) {
                    if ($kd_skpd != 'null') {
                        $query->where('a.kodeSkpd', $kd_skpd);
                    }
                    if ($jenis != 'Keseluruhan') {
                        $query->where('a.jenis', $jenis);
                    }
                    if ($merk != 'Keseluruhan') {
                        $query->where('a.merk', $merk);
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

        $view = view('laporan.bpkb.rekapPeminjaman')->with($data);

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
