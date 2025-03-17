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

class Terimapot extends Controller
{



    public function index()
    {

        return view('trmpot.index');
    }

    public function load(Request $request)
{
    if ($request->ajax()) {
        $search = $request->search;

        $data = DB::table('trhtrmpot')
            ->where('kd_skpd', Auth::user()->kd_skpd);

        if ($search) {
            $data = $data->where(function ($query) use ($search) {
                $query->where('no_bukti', 'like', "%" . $search . "%")
                      ->orWhere('tgl_bukti', 'like', "%" . $search . "%")
                      ->orWhere('ket', 'like', "%" . $search . "%"); // Tambahan untuk pencarian lebih fleksibel
            });
        }

        return DataTables::of($data->get()) // Tambahkan get() di sini
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $btn = '<a href="' . route('trmpot.edit', Crypt::encrypt($row->no_bukti)) . '" class="btn btn-primary btn-sm" style="margin-right:4px"><i class="fas fa-eye"></i></a>';

                if ($row->status != 1) {
                    $btn .= '<a href="' . route('trmpot.ubah', Crypt::encrypt($row->no_bukti)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="fas fa-edit"></i></a>';
                    $btn .= '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('trmpot.destroy', Crypt::encrypt($row->no_bukti)) . '"><i class="fas fa-trash-alt"></i></button>';
                }

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}


    public function create()
    {
        $lastNoBukti = DB::table('trhtrmpot')->max('no_bukti');
        $newNoBukti = $lastNoBukti ? $lastNoBukti + 1 : 1;

        $kd_skpd = auth()->user()->kd_skpd;
        $nm_skpd = Auth::user()->name;
        $rek_pengeluaran = Auth::user()->rek_pengeluaran;
        $kd_sub_kegiatan = DB::table('ms_sub_kegiatan')
                            ->select('kd_sub_kegiatan','nm_sub_kegiatan') // Ambil hanya kolom yang diperlukan
                            ->distinct() // Hilangkan duplikasi
                            ->get();

        return view('trmpot.create', compact('kd_skpd','nm_skpd','newNoBukti','rek_pengeluaran','kd_sub_kegiatan'));
    }

    public function getNotransaksi(Request $request)
{
    $search = $request->q;

    $usedNoBukti = DB::table('trhtrmpot')->pluck('id_trhtransout')->toArray();

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


    public function getsp2d(Request $request)
    {

        $search = $request->q;

        $sertifikat = DB::table('trhsp2d')
            ->select('no_sp2d')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('no_sp2d', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $sertifikat->map(function ($item) {
            return [
                'id' => $item->no_sp2d,
                'text' => $item->no_sp2d,
            ];
        });

        return response()->json($data);

    }


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
            'tgl_bukti' => 'required|date',
            'id_trhtransout' => 'required',
            'no_sp2d' => 'required',
            'pay' => 'required',
            'kd_sub_kegiatan' => 'required',
            'kd_rek6' => 'required',
            'nmrekan' => 'nullable',
            'beban' => 'required',
            'alamat' => 'nullable',
            'ket' => 'required',
            'npwp' => 'nullable',
            'nm_sub_kegiatan' => 'required',
            'nm_rek6' => 'required',
            'pimpinan' => 'nullable',
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
        $headerId = DB::table('trhtrmpot')->insertGetId([
            'no_bukti' => $request->no_bukti,
            'tgl_bukti' => $request->tgl_bukti,
            'ket' => $request->ket,
            'username' => auth()->user()->username,
            'kd_skpd' => $request->kd_skpd,
            'nm_skpd' => $request->nm_skpd,
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
            'no_kas' => $request->no_bukti,
            'pay' => $request->pay,
            'ebilling' => $request->ebilling,
            'id_trhtransout' => $request->id_trhtransout,
            'beban' => $request->beban,
        ]);

        $trhbppajak = DB::table('trhbppajak')->insert([
            'no_bukti' => $request->no_bukti,
            'tgl_bukti' => $request->tgl_bukti,
            'uraian' => $request->ket,
            'kd_rek' => $request->kd_rek6,
            'nm_rek' => $request->nm_rek6,
            'ebilling' => $request->ebilling,
            'kd_skpd' => $request->kd_skpd,
            'nm_skpd' => $request->nm_skpd,
            'id_user' => auth()->user()->id,
            'terima' => $totalNilai,
            'no_sp2d' => $request->no_sp2d,
            'created_at' => Carbon::now('Asia/Jakarta'),
            'no_trmpot' => $request->no_bukti,

        ]);

        $trhbku = DB::table('trhbku')->insert([
            'no_kas' => $request->no_bukti,
            'tgl_kas' => $request->tgl_bukti,
            'uraian' => $request->ket,
            'no_sp2d' => $request->no_sp2d,
            'kd_skpd' => auth()->user()->kd_skpd,
            'nm_skpd' => auth()->user()->name,
            'id_user' => auth()->user()->id,
            'terima' => $totalNilai,
            'created_at' => Carbon::now('Asia/Jakarta'),
            'id_trmpot' => $request->no_bukti,
        ]);

        $saldo_awal = DB::table('masterSkpd')
            ->where('kodeSkpd', auth()->user()->kd_skpd)
            ->value('saldoawal');


            if ($saldo_awal === null) {
                $saldo_awal = 0;
            }


            $total_belanja= $totalNilai;
            // **Mengupdate saldo awal berdasarkan jenis penerimaan**
            $saldo_baru = $saldo_awal + $total_belanja;

            DB::table('masterSkpd')
                ->where('kodeSkpd', auth()->user()->kd_skpd)
                ->update([
                    'saldoawal' => $saldo_baru,
                ]);


        // Create detail records
        foreach ($potonganData as $potongan) {
            DB::table('trdtrmpot')->insert([
                'no_bukti' => $request->no_bukti,
                'kd_rek6' => $potongan['kdrekpot'],
                'nm_rek6' => $potongan['nmrekpot'],
                'nilai' => str_replace(['Rp', '.', ','], '', $potongan['nilai']),
                'kd_skpd' => $request->kd_skpd,
                'kd_rek_trans' => $potongan['kd_rek6'],
                'ebilling' => $potongan['ebilling'],
                'rekanan' => $potongan['nmrekan'],
                'npwp' => $potongan['npwp'],
                'ntpn' => $potongan['ntpn'] ?? null,
            ]);

            DB::table('trdbku')->insert([
                'no_kas' => $request->no_bukti,
                'kd_sub_kegiatan' => $request->kd_sub_kegiatan,
                'nm_sub_kegiatan' => $request->nm_sub_kegiatan,
                'kd_rek6' => $potongan['kdrekpot'],
                'nm_rek6' => $potongan['nmrekpot'],
                'terima' => str_replace(['Rp', '.', ','], '', $potongan['nilai']),
                'kd_skpd' => $request->kd_skpd,
                'id_trmpot' => $request->no_bukti,
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'redirect' => route('trmpot.index')
        ]);

    } catch (Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan data: ' . $e->getMessage()
        ], 500);
    }
}

public function destroy($no_bukti)
{
    try {
        $decryptedId = Crypt::decrypt($no_bukti);

        $trmpot = DB::table('trhtrmpot')
                ->where('no_bukti', $decryptedId)
                ->first();

            if (!$trmpot) {
                return response()->json([
                    'success' => false,
                    'message' => 'trmpot tidak ditemukan.'
                ], 404);
            }


            $total = $trmpot->nilai;
            $kodeSkpd = auth()->user()->kd_skpd;

        DB::table('trhtrmpot')->where('no_bukti', $decryptedId)->delete();
        DB::table('trhbppajak')->where('no_bukti', $decryptedId)->where('no_trmpot', $decryptedId)->delete();
        $decryptedId = Crypt::decrypt($no_bukti);
        DB::table('trdtrmpot')->where('no_bukti', $decryptedId)->delete();
        DB::table('trhbku')->where('no_kas', $decryptedId)->where('id_trmpot', $decryptedId)->delete();
        DB::table('trdbku')->where('no_kas', $decryptedId)->where('id_trmpot', $decryptedId)->delete();



            DB::table('masterSkpd')
                ->where('kodeSkpd', $kodeSkpd)
                ->update([
                    'saldoawal' => DB::raw("saldoawal - $total")
                ]);


        return response()->json([
            'success' => true,
            'message' => 'subkegiatan berhasil dihapus.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus subkegiatan.'
        ], 500);
    }
}

public function edit($no_bukti)
{
    // Dekripsi ID yang terenkripsi
    $decryptedId = Crypt::decrypt($no_bukti);


    // Ambil data pajak berdasarkan ID menggunakan Query Builder
    $trmpot = DB::table('trhtrmpot')->where('no_bukti', $decryptedId)->first();
    $trhtransout = DB::table('trhtransout')->where('no_bukti', $trmpot->id_trhtransout)->first();

    $potonganDetails = DB::table('trdtrmpot')
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
    if (!$trmpot) {
        return redirect()->route('trmpot.index')->with('message', 'Data Terima Potongan Pajak tidak ditemukan.');
    }

    // Tampilkan view untuk mengedit data
    return view('trmpot.edit', compact('trmpot','trhtransout','potonganDetails'));
}

public function ubah($no_bukti)
{
    // Dekripsi ID yang terenkripsi
    $decryptedId = Crypt::decrypt($no_bukti);


    // Ambil data pajak berdasarkan ID menggunakan Query Builder
    $trmpot = DB::table('trhtrmpot')->where('no_bukti', $decryptedId)->first();
    $trhtransout = DB::table('trhtransout')->where('no_bukti', $trmpot->id_trhtransout)->first();

    $potonganDetails = DB::table('trdtrmpot')
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
    if (!$trmpot) {
        return redirect()->route('trmpot.index')->with('message', 'Data Terima Potongan Pajak tidak ditemukan.');
    }

    // Tampilkan view untuk mengedit data
    return view('trmpot.ubah', compact('trmpot','trhtransout','potonganDetails'));
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
        // Ambil data lama dari `trhtrmpot`
        $trmpot = DB::table('trhtrmpot')->where('no_bukti', $no_bukti)->first();

        if (!$trmpot) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        $kd_skpd = $request->kd_skpd ?? $trmpot->kd_skpd;
        $nilaiLama = (float) $trmpot->nilai;

        // Update saldoawal di masterskpd jika nilai berubah
        if ($nilaiLama !== $totalNilai) {
            DB::table('masterSkpd')
                ->where('kodeSkpd', $kd_skpd)
                ->update([
                    'saldoawal' => DB::raw("saldoawal - $nilaiLama + $totalNilai")
                ]);
        }

        // Update data Trmpot
        DB::table('trhtrmpot')->where('no_bukti', $no_bukti)->update([
            'tgl_bukti' => $request->tgl_bukti,
            'ket' => $request->ket,
            'username' => Auth()->user()->username,
            'kd_skpd' => $request->kd_skpd ?? $trmpot->kd_skpd,
            'nm_skpd' => $request->nm_skpd ?? $trmpot->nm_skpd,
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

        DB::table('trhbppajak')->where('no_bukti', $no_bukti)->where('no_trmpot', $no_bukti)->update([
            'tgl_bukti' => $request->tgl_bukti,
            'uraian' => $request->ket,
            'kd_rek' => $request->kd_rek6,
            'nm_rek' => $request->nm_rek6,
            'ebilling' => $request->ebilling,
            'kd_skpd' => $request->kd_skpd ?? $trmpot->kd_skpd,
            'nm_skpd' => $request->nm_skpd ?? $trmpot->nm_skpd,
            'id_user' => Auth()->user()->id,
            'terima' => $totalNilai,
            'no_sp2d' => $request->no_sp2d,
            'created_at' => Carbon::now('Asia/Jakarta'),
        ]);

        DB::table('trhbku')->where('no_kas', $no_bukti)->where('id_trmpot', $no_bukti)->update([
            'tgl_kas' => $request->tgl_bukti,
            'uraian' => $request->ket,
            'no_sp2d' => $request->no_sp2d,
            'kd_skpd' => $request->kd_skpd ?? $trmpot->kd_skpd,
            'nm_skpd' => $request->nm_skpd ?? $trmpot->nm_skpd,
            'id_user' => auth()->user()->id,
            'terima' => $totalNilai,
            'created_at' => Carbon::now('Asia/Jakarta'),
        ]);



        // Hapus potongan details lama
        DB::table('trdtrmpot')->where('no_bukti', $no_bukti)->delete();
        DB::table('trdbku')->where('no_kas', $no_bukti)->where('id_trmpot', $no_bukti)->delete();

        // Simpan potongan details baru
        $potonganInsertData = array_map(function ($detail) use ($request, $no_bukti, $trmpot) {
            return [
                'kd_skpd' => $request->kd_skpd ?? $trmpot->kd_skpd,
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

        DB::table('trdtrmpot')->insert($potonganInsertData);


        $potonganInsertData1 = array_map(function ($detail) use ($request, $no_bukti, $trmpot) {
            return [
                'no_kas' => $no_bukti,
                'kd_sub_kegiatan' => $request->kd_sub_kegiatan,
                'nm_sub_kegiatan' => $request->nm_sub_kegiatan,
                'kd_rek6' => $detail['kdrekpot'],
                'nm_rek6' => $detail['nmrekpot'],
                'terima' => str_replace(['Rp', '.', ','], '', $detail['nilai']),
                'kd_skpd' => $request->kd_skpd ?? $trmpot->kd_skpd,
                'id_trmpot' => $no_bukti,


            ];
        }, $potonganData);

        DB::table('trdbku')->insert($potonganInsertData1);

        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'redirect' => route('trmpot.index')
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
