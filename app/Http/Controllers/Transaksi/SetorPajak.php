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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SetorPajak extends Controller
{



    public function index()
    {

        return view('setor_potongan.index');
    }

    public function load(Request $request)
{
    if ($request->ajax()) {
        $search = $request->search;

        $data = DB::table('trhstrpot')
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
                $btn = '<a href="' . route('strpot.edit', Crypt::encrypt($row->no_bukti)) . '" class="btn btn-primary btn-sm" style="margin-right:4px"><i class="fas fa-eye"></i></a>';
                $btn .= '<a href="' . route('strpot.ubah', Crypt::encrypt($row->no_bukti)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="fas fa-edit"></i></a>';
                $btn .= '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('strpot.destroy', Crypt::encrypt($row->no_bukti)) . '"><i class="fas fa-trash-alt"></i></button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}

    public function edit($no_bukti)
    {
        // Dekripsi ID yang terenkripsi
        $decryptedId = Crypt::decrypt($no_bukti);


        // Ambil data pajak berdasarkan ID menggunakan Query Builder
        $strpot = DB::table('trhstrpot')->where('no_bukti', $decryptedId)->first();

        $potonganDetails = DB::table('trdstrpot')
                ->where('no_bukti', $decryptedId)
                ->select(
                    'kd_rek6',
                    'nm_rek6',
                    'rekanan',
                    'npwp',
                    'ntpn',
                    'ebilling',
                    'nilai'
                )
                ->get();
        // Cek apakah data ditemukan
        if (!$strpot) {
            return redirect()->route('strpot.index')->with('message', 'Data Terima Potongan Pajak tidak ditemukan.');
        }

        // Tampilkan view untuk mengedit data
        return view('setor_potongan.edit', compact('strpot','potonganDetails'));
    }

    public function create()
    {
        $lastNoBukti = DB::table('trhstrpot')->max('no_bukti');
        $newNoBukti = $lastNoBukti ? $lastNoBukti + 1 : 1;

        $kd_skpd = auth()->user()->kd_skpd;
        $nm_skpd = Auth::user()->name;
        $rek_pengeluaran = Auth::user()->rek_pengeluaran;
        $kd_sub_kegiatan = DB::table('ms_sub_kegiatan')
                            ->select('kd_sub_kegiatan','nm_sub_kegiatan') // Ambil hanya kolom yang diperlukan
                            ->distinct() // Hilangkan duplikasi
                            ->get();

        return view('setor_potongan.create', compact('kd_skpd','nm_skpd','newNoBukti','rek_pengeluaran','kd_sub_kegiatan'));
    }

    public function getnoterima(Request $request)
    {
        $search = $request->q;
        $kd_skpd = auth()->user()->kd_skpd;

        $usedNoBukti = DB::table('trhstrpot')->pluck('no_terima')->toArray();

        $sertifikat = DB::table('trhtrmpot')
            ->join('trdtrmpot', 'trhtrmpot.no_bukti', '=', 'trdtrmpot.no_bukti')
            ->select(
                'trhtrmpot.id_trhtransout',
                'trhtrmpot.no_bukti',
                'trhtrmpot.kd_sub_kegiatan',
                'trhtrmpot.nm_sub_kegiatan',
                'trhtrmpot.kd_rek6',
                'trhtrmpot.nm_rek6',
                'trhtrmpot.nmrekan',
                'trhtrmpot.pimpinan',
                'trhtrmpot.beban',
                'trhtrmpot.npwp',
                'trhtrmpot.alamat',
                'trhtrmpot.ket',
                'trhtrmpot.tgl_bukti',
                'trhtrmpot.no_sp2d',
                'trhtrmpot.pay',
            )
            ->where('trhtrmpot.kd_skpd', $kd_skpd)
            ->when(!empty($usedNoBukti), function ($query) use ($usedNoBukti) {
                return $query->whereNotIn('trhtrmpot.no_bukti', $usedNoBukti);
            })
            ->when(!empty($search), function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('trhtrmpot.no_bukti', 'LIKE', "%{$search}%")
                    ->orWhere('trhtrmpot.ket', 'LIKE', "%{$search}%");
                });
            })
            ->groupBy(
                'trhtrmpot.no_bukti',
                'trhtrmpot.kd_sub_kegiatan',
                'trhtrmpot.nm_sub_kegiatan',
                'trhtrmpot.kd_rek6',
                'trhtrmpot.nm_rek6',
                'trhtrmpot.nmrekan',
                'trhtrmpot.pimpinan',
                'trhtrmpot.beban',
                'trhtrmpot.npwp',
                'trhtrmpot.alamat',
                'trhtrmpot.ket',
                'trhtrmpot.tgl_bukti',
                'trhtrmpot.no_sp2d',
                'trhtrmpot.pay',
                'trhtrmpot.id_trhtransout'

            )
            ->limit(1000)
            ->get();

        $data = $sertifikat->map(function ($item) {
            return [
                'id' => $item->no_bukti,
                'text' => "{$item->no_bukti} | {$item->ket}",
                'no_sp2d' => $item->no_sp2d,
                'pay' => $item->pay,
                'kd_sub_kegiatan' => $item->kd_sub_kegiatan,
                'nm_sub_kegiatan' => $item->nm_sub_kegiatan,
                'kd_rekening' => $item->kd_rek6,
                'nm_rekening' => $item->nm_rek6,
                'nmrekan' => $item->nmrekan,
                'pimpinan' => $item->pimpinan,
                'beban' => $item->beban,
                'npwp' => $item->npwp,
                'alamat' => $item->alamat,
                'ket' => $item->ket,
                'id_trhtransout' => $item->id_trhtransout,
            ];
        });

        return response()->json($data);
    }


    public function getpotongandata(Request $request)
    {
        $noTerima = $request->no_terima;

        // Ambil data dari tabel utama berdasarkan no_terima
        $data = DB::table('trhtrmpot')->where('no_bukti', $noTerima)->first();

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        // Ambil data dari trdtrmpot berdasarkan no_bukti yang sesuai
        $trdtrmpot = DB::table('trdtrmpot')->where('no_bukti', $noTerima)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'ntpn' => $data->ntpn ?? '',
                'no_sp2d' => $data->no_sp2d ?? '',
                'pay' => $data->pay ?? '',
                'kd_sub_kegiatan' => $data->kd_sub_kegiatan ?? '',
                'nm_sub_kegiatan' => $data->nm_sub_kegiatan ?? '',
                'kd_rekening' => $data->kd_rek6 ?? '',
                'nm_rekening' => $data->nm_rek6 ?? '',
                'nmrekan' => $data->nmrekan ?? '',
                'pimpinan' => $data->pimpinan ?? '',
                'beban' => $data->beban ?? '',
                'npwp' => $data->npwp ?? '',
                'alamat' => $data->alamat ?? '',
                'ket' => $data->ket ?? '',
                'id_trhtransout' => $data->id_trhtransout ?? '',
            ],
            'trdtrmpot' => $trdtrmpot
        ]);
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

        $sertifikat = DB::table('ms_rekening')
            ->select('kd_rek6','nm_rek6')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('kd_rek6', 'LIKE', "%{$search}%")
                      ->orWhere('nm_rek6', 'LIKE', "%{$search}%");
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

        $sertifikat = DB::table('ms_rekan')
            ->select('Rekanan','Pimpinan','NPWP')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('Rekanan', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $sertifikat->map(function ($item) {
            return [
                'id' => $item->Rekanan,
                'text' => $item->Rekanan,
                'pimpinan' => $item->Pimpinan,
                'npwp' => $item->NPWP,
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

        $ntpnArray = [];
        foreach ($potonganData as $potongan) {
            // Use null coalescing operator to provide a default empty string if 'ntpn' is not set
            $ntpnArray[] = $potongan['ntpn'] ?? '';
        }
        $ntpnString = implode(', ', array_filter($ntpnArray));
        // Create header record
        $headerId = DB::table('trhstrpot')->insertGetId([
            'no_bukti' => $request->no_bukti,
            'tgl_bukti' => $request->tgl_bukti,
            'ket' => $request->ket,
            'username' => auth()->user()->username,
            'kd_skpd' => $request->kd_skpd,
            'nm_skpd' => $request->nm_skpd,
            'no_terima' => $request->no_terima,
            'nilai' => $totalNilai,
            'npwp' => $request->npwp,
            'no_sp2d' => $request->no_sp2d,
            'kd_sub_kegiatan' => $request->kd_sub_kegiatan,
            'nm_sub_kegiatan' => $request->nm_sub_kegiatan,
            'kd_rek6' => $request->kd_rek6,
            'nm_rek6' => $request->nm_rek6,
            'nmrekan' => $request->nmrekan,
            'pimpinan' => $request->pimpinan,
            'alamat' => $request->alamat,
            'no_ntpn' => $ntpnString,
            'pay' => $request->pay,
            'beban' => $request->beban
        ]);

        $trhbppajak = DB::table('trhbppajak')->insert([
            'no_bukti' => $request->no_bukti,
            'tgl_bukti' => $request->tgl_bukti,
            'uraian' => $request->ket,
            'kd_rek' => $request->kd_rek6,
            'nm_rek' => $request->nm_rek6,
            'ntpn' => $ntpnString,
            'kd_skpd' => $request->kd_skpd,
            'nm_skpd' => $request->nm_skpd,
            'id_user' => auth()->user()->id,
            'keluar' => $totalNilai,
            'no_sp2d' => $request->no_sp2d,
            'created_at' => Carbon::now('Asia/Jakarta'),
            'no_strpot' => $request->no_bukti,

        ]);

        $trhbku = DB::table('trhbku')->insert([
            'no_kas' => $request->no_bukti,
            'tgl_kas' => $request->tgl_bukti,
            'uraian' => $request->ket,
            'no_sp2d' => $request->no_sp2d,
            'kd_skpd' => $request->kd_skpd,
            'nm_skpd' => $request->nm_skpd,
            'id_user' => auth()->user()->id,
            'keluar' => $totalNilai,
            'created_at' => Carbon::now('Asia/Jakarta'),
            'id_strpot' => $request->no_bukti,
        ]);

        $saldo_awal = DB::table('masterSkpd')
            ->where('kodeSkpd', auth()->user()->kd_skpd)
            ->value('saldoawal');


            if ($saldo_awal === null) {
                $saldo_awal = 0;
            }


            $total_belanja= $totalNilai;
            // **Mengupdate saldo awal berdasarkan jenis penerimaan**
            $saldo_baru = $saldo_awal - $total_belanja;

            DB::table('masterSkpd')
                ->where('kodeSkpd', auth()->user()->kd_skpd)
                ->update([
                    'saldoawal' => $saldo_baru,
                ]);



        $trhbppajak = DB::table('trdtrmpot')
        ->where('no_bukti', $request->no_terima)
        ->update([
            'ntpn' => $potonganData['ntpn'] ?? '',
        ]);

        $status = DB::table('trhtrmpot')
        ->where('no_bukti', $request->no_terima)
        ->update([
            'status' => "1",
        ]);


        // Create detail records
        foreach ($potonganData as $potongan) {
            DB::table('trdstrpot')->insert([
                'no_bukti' => $request->no_bukti,
                'kd_rek6' => $potongan['kdrekpot'],
                'nm_rek6' => $potongan['nmrekpot'],
                'nilai' => floatval($potongan['nilai']),
                'kd_skpd' => $request->kd_skpd,
                'kd_rek_trans' => $request->kd_rek6,
                'ebilling' => $potongan['ebilling'],
                'rekanan' => $potongan['nmrekan'],
                'npwp' => $potongan['npwp'],
                'ntpn' => $potongan['ntpn'] ?? '',
                'id_terima' => intval($potongan['id_trdtrmpot']),
                'no_sp2d' => $request->no_sp2d,
            ]);

            DB::table('trdbku')->insert([
                'no_kas' => $request->no_bukti,
                'kd_sub_kegiatan' => $request->kd_sub_kegiatan,
                'nm_sub_kegiatan' => $request->nm_sub_kegiatan,
                'kd_rek6' => $potongan['kdrekpot'],
                'nm_rek6' => $potongan['nmrekpot'],
                'keluar' => floatval($potongan['nilai']),
                'kd_skpd' => $request->kd_skpd,
                'id_strpot' => $request->no_bukti,
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'redirect' => route('strpot.index')
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
        // Dekripsi hanya sekali
        $decryptedId = Crypt::decrypt($no_bukti);

        // Cek apakah data ada
        $strpot = DB::table('trhstrpot')
                ->where('no_bukti', $decryptedId)
                ->first();

        if (!$strpot) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        // Mulai transaksi untuk memastikan data dihapus dengan benar
        DB::beginTransaction();

        $total = $strpot->nilai;
        $kodeSkpd = auth()->user()->kd_skpd;
        $noTerima = $strpot->no_terima;

        // Hapus data di beberapa tabel
        DB::table('trhstrpot')->where('no_bukti', $decryptedId)->delete();
        DB::table('trhbppajak')->where('no_bukti', $decryptedId)->where('no_strpot', $decryptedId)->delete();
        DB::table('trdstrpot')->where('no_bukti', $decryptedId)->delete();
        DB::table('trhbku')->where('no_kas', $decryptedId)->where('id_strpot', $decryptedId)->delete();
        DB::table('trdbku')->where('no_kas', $decryptedId)->where('id_strpot', $decryptedId)->delete();

        // Update saldo awal
        DB::table('masterSkpd')
                ->where('kodeSkpd', $kodeSkpd)
                ->update([
                    'saldoawal' => DB::raw("saldoawal + $total")
                ]);

        // Update status trhtrmpot
        DB::table('trhtrmpot')
                ->where('kd_skpd', $kodeSkpd)
                ->where('no_bukti', $noTerima)
                ->update([
                    'status' => "0"
                ]);

        // Commit transaksi jika semua berhasil
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Setor Potongan berhasil dihapus.'
        ]);
    } catch (\Exception $e) {
        // Rollback jika terjadi kesalahan
        DB::rollBack();

        // Logging error untuk debugging
        Log::error('Gagal menghapus Setor Potongan: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus Setor Potongan.',
            'error' => app()->environment('local') ? $e->getMessage() : 'Terjadi kesalahan'
        ], 500);
    }
}

public function ubah($no_bukti)
{

    // Dekripsi ID yang terenkripsi
    $decryptedId = Crypt::decrypt($no_bukti);


    // Ambil data pajak berdasarkan ID menggunakan Query Builder
    $strpot = DB::table('trhstrpot')->where('no_bukti', $decryptedId)->first();

    $potonganDetails = DB::table('trdstrpot')
            ->where('no_bukti', $decryptedId)
            ->select(
                'id',
                'kd_rek6',
                    'nm_rek6',
                    'rekanan',
                    'npwp',
                    'ntpn',
                    'ebilling',
                    'nilai'
            )
            ->get();
    // Cek apakah data ditemukan
    if (!$strpot) {
        return redirect()->route('strpot.index')->with('message', 'Data Terima Potongan Pajak tidak ditemukan.');
    }

    // Tampilkan view untuk mengedit data
    return view('setor_potongan.ubah', compact('strpot','potonganDetails'));
}

public function update(Request $request, $no_bukti)
{
    // Remove the dd() that interrupts the JSON response
    // dd($request->all());

    $potonganData = json_decode($request->potongan_data, true);

    if (!is_array($potonganData) || empty($potonganData)) {
        return response()->json([
            'success' => false,
            'message' => 'Data potongan tidak valid atau kosong.'
        ], 400);
    }

    $ntpnArray = [];
    foreach ($potonganData as $potongan) {
        // Use null coalescing operator to provide a default empty string if 'ntpn' is not set
        $ntpnArray[] = $potongan['ntpn'] ?? '';
    }
    $ntpnString = implode(', ', array_filter($ntpnArray));

    // Hitung total nilai potongan dengan pengecekan yang lebih aman
    $totalNilai = array_reduce($potonganData, function ($carry, $item) {
        $nilai = isset($item['nilai']) ? str_replace(['Rp', '.', ','], '', $item['nilai']) : 0;
        return $carry + (float) $nilai;
    }, 0);

    DB::beginTransaction();
    try {
        // Ambil data lama dari `trhtrmpot`
        $strpot = DB::table('trhstrpot')->where('no_bukti', $no_bukti)->first();

        if (!$strpot) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        $kd_skpd = $request->kd_skpd ?? $strpot->kd_skpd;
        $nilaiLama = (float) $strpot->nilai;

        // Update data Trmpot
        DB::table('trhstrpot')->where('no_bukti', $no_bukti)->update([
            'no_ntpn' => $ntpnString,
        ]);

        DB::table('trhbppajak')->where('no_strpot', $no_bukti)->update([
            'ntpn' => $ntpnString,
        ]);

        foreach ($potonganData as $potongan) {
            DB::table('trdstrpot')
                ->where('no_bukti', $no_bukti)
                ->where('id', $potongan['id']) // Gunakan id untuk mengidentifikasi baris yang akan diupdate
                ->update([
                    'ntpn' => $potongan['ntpn'] ?? '', // Update NTPN untuk setiap potongan
                ]);
        }

        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'redirect' => route('strpot.index')
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
