<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class Lpj extends Controller
{



    public function index()
    {

        return view('lpj.index');
    }

    public function load(Request $request)
{
    if ($request->ajax()) {
        $search = $request->search;

        $data = DB::table('trhlpj')
            ->where('kd_skpd', Auth::user()->kd_skpd)
            ->whereIn('jenis', ["UP", "GU"]);

        if ($search) {
            $data = $data->where(function ($query) use ($search) {
                $query->where('no_lpj', 'like', "%" . $search . "%")
                      ->orWhere('tgl_lpj', 'like', "%" . $search . "%")
                      ->orWhere('keterangan', 'like', "%" . $search . "%"); // Tambahan untuk pencarian lebih fleksibel
            });
        }

        return DataTables::of($data->get()) // Tambahkan get() di sini
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                    $btn = '<a href="' . route('lpj.edit', Crypt::encrypt($row->no_lpj)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="fas fa-edit"></i></a>';
                    $btn .= '<button class="btn btn-sm btn-success print-btn" data-no-lpj="' . $row->no_lpj . '"><i class="fas fa-print"></i></button>';
                    $btn .= '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('lpj.destroy', Crypt::encrypt($row->no_lpj)) . '"><i class="fas fa-trash-alt"></i></button>';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}

public function getSp2d(Request $request)
{
    $noLpj = $request->no_lpj;

    // Get SP2D number related to the LPJ
    $data = DB::table('trhlpj')
        ->where('no_lpj', $noLpj)
        ->first();

    if ($data) {
        return response()->json([
            'success' => true,
            'no_sp2d' => $data->no_sp2d ?? '-' // Assuming there's a no_sp2d column
        ]);
    }

    return response()->json([
        'success' => false
    ]);
}

public function print(Request $request)
{

    $noLpj = $request->no_lpj;
    $noSp2d = $request->no_sp2d;
    $tanggal_ttd = $request->tgl_ttd;
    $printType = $request->print_type; // 'lpj_tu' or 'sptb'
    $ttdBendahara = $request->ttdbendaharabku;
    $ttdPaKa = $request->ttdpaka;
    $kd_skpd = auth()->user()->kd_skpd;
    $nm_skpd = auth()->user()->name;
    // Ambil data dari database sesuai kebutuhan
    $bendahara = DB::table('masterTtd')
    ->where('kodeSkpd', $kd_skpd)
    ->where('nip', $ttdBendahara)
    ->first();

    $pa_kpa = DB::table('masterTtd')
    ->where('kodeSkpd', $kd_skpd)
    ->where('nip', $ttdPaKa)
    ->first();

    $jumlah_belanja = DB::table('trlpj')
        ->where('no_lpj', $noLpj)
        ->sum('nilai');

    $lpj = DB::table('trhlpj')
        ->where('no_lpj', $noLpj)
        ->first();

    $data_lpj = DB::table('trlpj')
        ->select(
            'trlpj.kd_sub_kegiatan',
            'ms_sub_kegiatan.nm_sub_kegiatan',
            'trlpj.kd_rek6',
            'trlpj.nm_rek6',
            'trlpj.nilai'
        )
        ->leftJoin('ms_sub_kegiatan', 'trlpj.kd_sub_kegiatan', '=', 'ms_sub_kegiatan.kd_sub_kegiatan')
        ->where('trlpj.no_lpj', $noLpj)
        ->orderBy('trlpj.kd_sub_kegiatan')
        ->orderBy('trlpj.kd_rek6')
        ->get()
        ->groupBy('kd_sub_kegiatan');

    if ($printType === 'sptb') {
        return view('lpj.cetak.sptb', compact('kd_skpd', 'nm_skpd', 'jumlah_belanja', 'bendahara', 'tanggal_ttd'));
    } else{
        return view('lpj.cetak.rincian', compact('kd_skpd', 'nm_skpd', 'jumlah_belanja', 'bendahara', 'tanggal_ttd','lpj','data_lpj','pa_kpa'));
    }

    return back()->with('message', 'Dokumen berhasil dicetak.');
}

public function tandaTangan(Request $request)
{
    $query = DB::table('masterTtd');

    if (!empty(auth()->user()->kd_skpd)) {
        $query->where('kodeSkpd', auth()->user()->kd_skpd)
              ->where('kode', 'BK'); // Jika 'bendahara' adalah string, gunakan tanda kutip
    }

    $ttd = $query->get();

    return response()->json($ttd);
}


public function tandaTanganPa(Request $request)
{
    $query = DB::table('masterTtd');

    if (!empty(auth()->user()->kd_skpd)) {
        $query->where('kodeSkpd', auth()->user()->kd_skpd)
              ->where('kode', 'PA'); // Jika 'bendahara' adalah string, gunakan tanda kutip
    }

    $ttd = $query->get();

    return response()->json($ttd);
}

    public function create()
    {

        $kd_skpd = auth()->user()->kd_skpd;
        $nm_skpd = Auth::user()->name;
        $rek_pengeluaran = Auth::user()->rek_pengeluaran;
        $kd_sub_kegiatan = DB::table('ms_sub_kegiatan')
                            ->select('kd_sub_kegiatan','nm_sub_kegiatan') // Ambil hanya kolom yang diperlukan
                            ->distinct() // Hilangkan duplikasi
                            ->get();

        return view('lpj.create', compact('kd_skpd','nm_skpd','rek_pengeluaran','kd_sub_kegiatan'));
    }

    public function hapus_tabel($id)
{
    try {
        // Find the transaction
        $transaction = DB::table('trhtransout')->where('id', $id)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        $no_bukti = $transaction->no_bukti;

        // Begin transaction
        DB::beginTransaction();

        // Delete from detail table first (trdtransout)
        DB::table('trdtransout')->where('no_bukti', $no_bukti)->delete();

        // Delete from header table (trhtransout)
        DB::table('trhtransout')->where('id', $id)->delete();

        // Commit transaction
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    } catch (\Exception $e) {
        // Rollback in case of error
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus data: ' . $e->getMessage()
        ], 500);
    }
}

    public function getData(Request $request)
{
    $request->validate([
        'tgl_awal' => 'required|date',
        'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
    ]);

    $tgl_awal = $request->tgl_awal;
    $tgl_akhir = $request->tgl_akhir;
    $kd_skpd = auth()->user()->kd_skpd; // Assuming the current user's SKPD is used

    // Query to get data from trhtransout and trdtransout tables
    $data = DB::table('trhtransout')
        ->join('trdtransout', 'trhtransout.no_bukti', '=', 'trdtransout.no_bukti')
        ->leftJoin('trlpj', 'trhtransout.no_bukti', '=', 'trlpj.no_bukti')
        ->select(
            'trhtransout.kd_skpd',
            'trhtransout.no_bukti',
            'trdtransout.kd_sub_kegiatan',
            'trdtransout.nm_sub_kegiatan',
            'trdtransout.kd_rek6',
            'trdtransout.nm_rek6',
            'trdtransout.nilai',
            'trdtransout.no_sp2d',
            'trhtransout.jenis_beban',
        )
        ->where('trhtransout.kd_skpd', $kd_skpd)
        ->where('trhtransout.jenis_terima_sp2d', '0')
        ->where('trhtransout.perlimpahan', '0')
        ->whereIn('trhtransout.jenis_beban', ['UP', 'GU'])
        ->whereBetween('trhtransout.tgl_bukti', [$tgl_awal, $tgl_akhir])
        ->whereNull('trlpj.no_bukti')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}

    public function getNotransaksi(Request $request)
{
    $search = $request->q;

    $usedNoBukti = DB::table('trhlpj')->pluck('id_trhtransout')->toArray();

    $kd_skpd = auth()->user()->kd_skpd;
    $sertifikat = DB::table('trhtransout')
        ->select('no_bukti', 'tgl_bukti', 'no_sp2d', 'total', 'ket')
        ->where('kd_skpd', $kd_skpd)
        ->when(!empty($usedNoBukti), function ($query) use ($usedNoBukti) {
            return $query->whereNotIn('no_bukti', $usedNoBukti);
        })
        ->when(!empty($search), function ($query) use ($search) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('no_bukti', 'LIKE', "%{$search}%")
                         ->orWhere('tgl_bukti', 'LIKE', "%{$search}%")
                         ->orWhere('no_sp2d', 'LIKE', "%{$search}%")
                         ->orWhere('total', 'LIKE', "%{$search}%")
                         ->orWhere('ket', 'LIKE', "%{$search}%");
            });
        })
        ->limit(1000)
        ->get();

    $data = $sertifikat->map(function ($item) {
        return [
            'id' => $item->no_bukti,
            'text' => implode(' | ', [
                $item->no_bukti,
                $item->tgl_bukti,
                $item->no_sp2d,
                'Rp ' . number_format((float) $item->total, 0, ',', '.'), // Pastikan total dikonversi ke string
                $item->ket
            ]),
            'tgl_bukti' => $item->tgl_bukti,
            'no_sp2d' => $item->no_sp2d
        ];
    });

    return response()->json($data);
}






    public function getsumberdana(Request $request)
    {
    try {
        $query = DB::table('ms_sumber_dana')
            ->select('kd_dana', 'nm_dana');

        // Search both kd_sub_kegiatan and nm_sub_kegiatan
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('kd_dana', 'like', '%' . $request->search . '%')
                    ->orWhere('nm_dana', 'like', '%' . $request->search . '%');
            });
        }
        $subKegiatan = $query->orderBy('kd_dana')
            ->get();

        return response()->json($subKegiatan);
    } catch (\Exception $e) {

        return response()->json([
            'error' => 'Gagal mengambil data Sumber dana',
            'message' => 'Terjadi kesalahan dalam mengambil data'
        ], 500);
    }
    }

    public function getrekening(Request $request)
    {
        $search = $request->q;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;

        $sertifikat = DB::table('ms_anggaran')
            ->select('kd_rek as kd_rek6','nm_rek as nm_rek6')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('kd_rek', 'LIKE', "%{$search}%")
                      ->orWhere('nm_rek', 'LIKE', "%{$search}%");
            })
            ->when(!empty($kd_sub_kegiatan), function ($query) use ($kd_sub_kegiatan) {
                $query->where('kd_sub_kegiatan', $kd_sub_kegiatan); // Filter berdasarkan kode kegiatan
            })
            ->limit(10)
            ->get();

        $data = $sertifikat->map(function ($item) {
            return [
                'id' => $item->kd_rek6,
                'text' => implode(' | ', [
                    $item->kd_rek6,
                    $item->nm_rek6,

                ]),
                'nm_rek6' => $item->nm_rek6
            ];
        });

        return response()->json($data);

    }

    public function getrekan(Request $request)
    {
        $search = $request->q;

        $sertifikat = DB::table('ms_rekening_bank_online')
            ->select('nmrekan as Rekanan','pimpinan as Pimpinan','npwp as NPWP','alamat as Alamat','kd_skpd','nm_rekening')
            ->where('kd_skpd', Auth::user()->kd_skpd)
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('nm_rekening', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $sertifikat->map(function ($item) {
            return [
                'id' => $item->nm_rekening,
                'text' => $item->nm_rekening,
                'pimpinan' => $item->Pimpinan,
                'npwp' => $item->NPWP,
                'alamat' => $item->Alamat,
            ];
        });

        return response()->json($data);

    }

    public function getmspot(Request $request)
    {
        $search = $request->q;

        $sertifikat = DB::table('ms_pot')
            ->select('kd_rek6','nm_rek6')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('kd_rek6', 'LIKE', "%{$search}%")
                        ->orwhere('nm_rek6', 'LIKE', "%{$search}%");

            })
            ->limit(10)
            ->get();

        $data = $sertifikat->map(function ($item) {
            return [
                'id' => $item->kd_rek6,
                'text' => implode(' | ', [
                    $item->kd_rek6,
                    $item->nm_rek6,

                ]),
                'nmrekpot' => $item->nm_rek6,
            ];
        });

        return response()->json($data);

    }

    public function getsubkegiatan(Request $request)
    {

        $search = $request->q;

        $sertifikat = DB::table('ms_sub_kegiatan')
            ->select('kd_sub_kegiatan','nm_sub_kegiatan')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('kd_sub_kegiatan', 'LIKE', "%{$search}%")
                      ->orWhere('nm_sub_kegiatan', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $sertifikat->map(function ($item) {
            return [
                'id' => $item->kd_sub_kegiatan,
                'text' => implode(' | ', [
                    $item->kd_sub_kegiatan,
                    $item->nm_sub_kegiatan,

                ]),
                'nm_sub_kegiatan' => $item->nm_sub_kegiatan
            ];
        });

        return response()->json($data);

    }


    // public function getsp2d(Request $request)
    // {

    //     $search = $request->q;

    //     $sertifikat = DB::table('trhsp2d')
    //         ->select('no_sp2d')
    //         ->when(!empty($search), function ($query) use ($search) {
    //             $query->where('no_sp2d', 'LIKE', "%{$search}%");
    //         })
    //         ->limit(10)
    //         ->get();

    //     $data = $sertifikat->map(function ($item) {
    //         return [
    //             'id' => $item->no_sp2d,
    //             'text' => $item->no_sp2d,
    //         ];
    //     });

    //     return response()->json($data);

    // }


    public function store(Request $request)
{



    try {
        DB::beginTransaction();

        // Check if request is AJAX
        if (!$request->ajax()) {
            throw new Exception('Invalid request method');
        }

        // Validate header data
        $validator = Validator::make($request->all(), [
            'kd_skpd' => 'required',
            'tgl_lpj' => 'required|date',
            'no_lpj' => 'required',
            'keterangan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }


        // Get potongan data from request
        $potonganData = json_decode($request->potongan_data, true);
        if (empty($potonganData)) {
            return response()->json([
                'success' => false,
                'message' => 'Data potongan tidak boleh kosong'
            ], 422);
        }

        // Calculate total nilai from potongan data
        $totalNilai = array_sum(array_map(function($item) {
            return (float) str_replace(['Rp', '.', ','], '', $item['nilai']);
        }, $potonganData));

        // Create header record
        $headerId = DB::table('trhlpj')->insertGetId([
            'no_lpj' => $request->no_lpj,
            'tgl_lpj' => $request->tgl_lpj,
            'keterangan' => $request->keterangan,
            'tgl_awal' => $request->tgl_awal,
            'kd_skpd' => $request->kd_skpd,
            'tgl_akhir' => $request->tgl_akhir,
            'no_sp2d' => $potonganData[0]['no_sp2d'],
            'jenis' => $potonganData[0]['jenis_beban'],
        ]);



        // Create detail records
        foreach ($potonganData as $potongan) {
            DB::table('trlpj')->insert([
                'no_lpj' => $request->no_lpj,
                'no_bukti' => $potongan['no_bukti'],
                'tgl_lpj' => $request->tgl_lpj,
                'keterangan' => $request->keterangan,
                'kd_skpd' => $request->kd_skpd,
                'kd_sub_kegiatan' => $potongan['kd_sub_kegiatan'],
                'kd_rek6' => $potongan['kd_rek6'],
                'nm_rek6' => $potongan['nm_rek6'],
                'username' => Auth()->user()->username,
                'tgl_update' => now(),
                'nilai' => str_replace(['Rp', '.', ','], '', $potongan['nilai']),
            ]);


        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'redirect' => route('lpj.index')
        ]);

    } catch (Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan data: ' . $e->getMessage()
        ], 500);
    }
}

public function destroy($no_lpj)
{
    try {

        $decryptedId = Crypt::decrypt($no_lpj);
        DB::table('trhlpj')->where('no_lpj', $decryptedId)->delete();
        DB::table('trlpj')->where('no_lpj', $decryptedId)->delete();


        return response()->json([
            'success' => true,
            'message' => 'LPJ UP/GU berhasil dihapus.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus LPJ UP/GU.'
        ], 500);
    }
}

public function edit($no_bukti)
{
    // Dekripsi ID yang terenkripsi
    $decryptedId = Crypt::decrypt($no_bukti);


    // Ambil data pajak berdasarkan ID menggunakan Query Builder
    $lpj = DB::table('trhlpj')->where('no_bukti', $decryptedId)->first();
    $trhtransout = DB::table('trhtransout')->where('no_bukti', $lpj->id_trhtransout)->first();

    $potonganDetails = DB::table('trdlpj')
            ->where('no_bukti', $decryptedId)
            ->select(
                'kd_rek_trans',
                'kd_rek6',
                'nm_rek6',
                'rekanan',
                'npwp',
                'ebilling',
                'nilai'
            )
            ->get();
    // Cek apakah data ditemukan
    if (!$lpj) {
        return redirect()->route('lpj.index')->with('message', 'Data Terima Potongan Pajak tidak ditemukan.');
    }

    // Tampilkan view untuk mengedit data
    return view('lpj.edit', compact('lpj','trhtransout','potonganDetails'));
}

public function ubah($no_bukti)
{
    // Dekripsi ID yang terenkripsi
    $decryptedId = Crypt::decrypt($no_bukti);


    // Ambil data pajak berdasarkan ID menggunakan Query Builder
    $lpj = DB::table('trhlpj')->where('no_bukti', $decryptedId)->first();
    $trhtransout = DB::table('trhtransout')->where('no_bukti', $lpj->id_trhtransout)->first();

    $potonganDetails = DB::table('trdlpj')
            ->where('no_bukti', $decryptedId)
            ->select(
                'kd_rek_trans',
                'kd_rek6',
                'nm_rek6',
                'rekanan',
                'npwp',
                'ebilling',
                'nilai'
            )
            ->get();
    // Cek apakah data ditemukan
    if (!$lpj) {
        return redirect()->route('lpj.index')->with('message', 'Data Terima Potongan Pajak tidak ditemukan.');
    }

    // Tampilkan view untuk mengedit data
    return view('lpj.ubah', compact('lpj','trhtransout','potonganDetails'));
}


public function update(Request $request, $no_bukti)
{
    // Validasi input
    $validator = Validator::make($request->all(), [
        'tgl_bukti' => 'required|date',
        'id_trhtransout' => 'nullable|exists:trhtransout,no_bukti',
        'no_sp2d' => 'required',
        'pay' => 'required',
        'kd_sub_kegiatan' => 'required',
        'kd_rek6' => 'required',
        'nmrekan' => 'required',
        'beban' => 'required',
        'alamat' => 'nullable',
        'ket' => 'nullable',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // Decode JSON dan pastikan valid
    $potonganData = json_decode($request->potongan_data, true);

    if (!is_array($potonganData) || empty($potonganData)) {
        return response()->json([
            'success' => false,
            'message' => 'Data potongan tidak valid atau kosong.'
        ], 400);
    }

    // Hitung total nilai potongan dengan pengecekan yang lebih aman
    $totalNilai = array_reduce($potonganData, function ($carry, $item) {
        $nilai = isset($item['nilai']) ? str_replace(['Rp', '.', ','], '', $item['nilai']) : 0;
        return $carry + (float) $nilai;
    }, 0);

    DB::beginTransaction();
    try {
        // Ambil data lama dari `trhlpj`
        $lpj = DB::table('trhlpj')->where('no_bukti', $no_bukti)->first();

        if (!$lpj) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        $kd_skpd = $request->kd_skpd ?? $lpj->kd_skpd;
        $nilaiLama = (float) $lpj->nilai;

        // Update saldoawal di masterskpd jika nilai berubah
        if ($nilaiLama !== $totalNilai) {
            DB::table('masterSkpd')
                ->where('kodeSkpd', $kd_skpd)
                ->update([
                    'saldoawal' => DB::raw("saldoawal - $nilaiLama + $totalNilai")
                ]);
        }

        // Update data lpj
        DB::table('trhlpj')->where('no_bukti', $no_bukti)->update([
            'tgl_bukti' => $request->tgl_bukti,
            'ket' => $request->ket,
            'username' => Auth()->user()->username,
            'kd_skpd' => $request->kd_skpd ?? $lpj->kd_skpd,
            'nm_skpd' => $request->nm_skpd ?? $lpj->nm_skpd,
            'no_sp2d' => $request->no_sp2d,
            'nilai' => $totalNilai,
            'npwp' => $request->npwp,
            'kd_sub_kegiatan' => $request->kd_sub_kegiatan,
            'nm_sub_kegiatan' => $request->nm_sub_kegiatan,
            'kd_rek6' => $request->kd_rek6,
            'nm_rek6' => $request->nm_rek6,
            'nmrekan' => $request->nmrekan,
            'pimpinan' => $request->pimpinan,
            'alamat' => $request->alamat,
            'no_kas' => $no_bukti,
            'pay' => $request->pay,
            'ebilling' => $request->ebilling,
            'id_trhtransout' => $request->id_trhtransout,
        ]);

        // Hapus potongan details lama
        DB::table('trdlpj')->where('no_bukti', $no_bukti)->delete();

        // Simpan potongan details baru
        $potonganInsertData = array_map(function ($detail) use ($request, $no_bukti, $lpj) {
            return [
                'kd_skpd' => $request->kd_skpd ?? $lpj->kd_skpd,
                'no_bukti' => $no_bukti,
                'kd_rek_trans' => $request->kd_rek6,
                'kd_rek6' => $detail['kdrekpot'],
                'nm_rek6' => $detail['nmrekpot'],
                'rekanan' => $detail['nmrekan'],
                'ntpn' => $detail['ntpn'] ?? null,
                'npwp' => $detail['npwp'],
                'ebilling' => $detail['ebilling'],
                'nilai' => str_replace(['Rp', '.', ','], '', $detail['nilai']),
            ];
        }, $potonganData);

        DB::table('trdlpj')->insert($potonganInsertData);

        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'redirect' => route('lpj.index')
        ]);

    } catch (Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan data: ' . $e->getMessage()
        ], 500);
    }
}




}
