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

class SetorKas extends Controller
{



    public function index()
    {

        return view('setorkas.index');
    }

    public function load(Request $request)
{
    if ($request->ajax()) {
        $search = $request->search;

        $data = DB::table('trhkasin_pkd')
            ->where('kd_skpd', Auth::user()->kd_skpd)
            ->select([
                'no_sts',
                DB::raw("COALESCE(tgl_sts, '') as tgl_sts"), // Pastikan tgl_sts tidak null
                'keterangan',
                'kd_skpd',
                DB::raw("'trhkasin_pkd' as sumber_data")
            ]);

        if ($search) {
            $data = $data->where(function ($query) use ($search) {
                $query->where('no_sts', 'like', "%" . $search . "%")
                      ->orWhere('tgl_sts', 'like', "%" . $search . "%")
                      ->orWhere('keterangan', 'like', "%" . $search . "%"); // Tambahan untuk pencarian lebih fleksibel
            });
        }
        $data1 = $data->get(); // Eksekusi query

        // Ambil data dari trdbku yang mengandung "utang belanja"
        $data2 = DB::table('trdbku')
        ->where('trdbku.nm_rek6', 'like', '%utang belanja%')
        ->select([
            'trdbku.no_kas as no_sts',
            'trdbku.kd_skpd',
            'trdbku.nm_rek6 as keterangan',
            DB::raw("'' as tgl_sts"),
            DB::raw("'trdbku' as sumber_data")
        ])
        ->get();




        // Gabungkan data menjadi satu array
        $mergedData = collect($data1)->merge($data2);

        return DataTables::of($mergedData)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                if ($row->sumber_data === 'trhkasin_pkd') {
                    return '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('setorkas.destroy', Crypt::encrypt($row->no_sts)) . '"><i class="fas fa-trash-alt"></i></button>';
                }
                return '-'; // Jika data dari trdbku, tidak ada tombol aksi
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}


    public function create()
    {
        $lastNoBukti = DB::table('trhkasin_pkd')->max('no_sts');
        $newNoBukti = $lastNoBukti ? $lastNoBukti + 1 : 1;

        $kd_skpd = auth()->user()->kd_skpd;
        $nm_skpd = Auth::user()->name;
        $rek_pengeluaran = Auth::user()->rek_pengeluaran;
        $kd_sub_kegiatan = DB::table('ms_sub_kegiatan')
                            ->select('kd_sub_kegiatan','nm_sub_kegiatan') // Ambil hanya kolom yang diperlukan
                            ->distinct() // Hilangkan duplikasi
                            ->get();

        return view('setorkas.create', compact('kd_skpd','nm_skpd','newNoBukti','rek_pengeluaran','kd_sub_kegiatan'));
    }


    public function getnosp2d(Request $request)
{
    $search = $request->q;
    $jns_trans = $request->jns_trans; // Ambil jenis transaksi

    $sertifikat = DB::table('trhtransout')
        ->select('no_sp2d')
        ->distinct()
        ->when(!empty($search), function ($query) use ($search) {
            $query->where('no_sp2d', 'LIKE', "%{$search}%");
        })
        ->when($jns_trans == "1", function ($query) { // Jika jenis transaksi 1, ambil SP2D yang ada "LS"
            $query->where('no_sp2d', 'LIKE', '%LS%');
        })
        ->when($jns_trans == "2", function ($query) { // Jika jenis transaksi 2, ambil SP2D yang ada "UP", "GU", "TU"
            $query->where(function($q) {
                $q->where('no_sp2d', 'LIKE', '%UP%')
                  ->orWhere('no_sp2d', 'LIKE', '%GU%')
                  ->orWhere('no_sp2d', 'LIKE', '%TU%');
            });
        })
        ->orderBy('no_sp2d')
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



public function getrekening(Request $request)
{
    try {

        $query = DB::table('trhtransout')
            ->join('trdtransout', 'trhtransout.no_bukti', '=', 'trdtransout.no_bukti')
            ->join('masterSkpd', 'trdtransout.kd_skpd', '=', 'masterSkpd.kodeSkpd')
            ->select('trdtransout.kd_rek6', 'trdtransout.nm_rek6','trdtransout.nilai', 'masterSkpd.saldoawal')
            ->where('trhtransout.kd_skpd', auth()->user()->kd_skpd)
            ->where('trhtransout.jenis_terima_sp2d', "1");

        // Filter berdasarkan no_sp2d jika dipilih
        if ($request->has('tgl_kas')) { // Periksa 'tgl_kas' bukan 'tgl_bukti'
            $bulan = date('m', strtotime($request->tgl_kas));
            $query->whereRaw('MONTH(trhtransout.tgl_bukti) BETWEEN 1 AND ' . (int) $bulan);
        }


        if ($request->has('no_sp2d')) {
            $query->where('trhtransout.no_sp2d', $request->no_sp2d);
        }


        if ($request->has('no_sp2d')) {
            $no_sp2d = $request->no_sp2d;
            $query->where('trhtransout.no_sp2d', $no_sp2d);

            // Cek apakah no_sp2d mengandung LS, UP, GU, atau TU
            if (stripos($no_sp2d, 'LS') !== false) {
                // Jika mengandung LS, kd_sub_kegiatan wajib ada
                if (!$request->has('kd_sub_kegiatan')) {
                    return response()->json(['error' => 'kd_sub_kegiatan wajib diisi untuk SP2D jenis LS'], 400);
                }
                $query->where('trdtransout.kd_sub_kegiatan', $request->kd_sub_kegiatan);
            } elseif (
                stripos($no_sp2d, 'UP') !== false ||
                stripos($no_sp2d, 'GU') !== false ||
                stripos($no_sp2d, 'TU') !== false
            ) {
                // Jika mengandung UP, GU, atau TU, kd_sub_kegiatan boleh ada tetapi tidak wajib
                if ($request->has('kd_sub_kegiatan')) {
                    $query->where('trdtransout.kd_sub_kegiatan', $request->kd_sub_kegiatan);
                }
            }
        }

        // Filter berdasarkan bulan dari tgl_kas jika diinput


        // Pencarian berdasarkan input
        if ($request->has('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('kd_rek6', 'like', '%' . $request->q . '%')
                    ->orWhere('nm_rek6', 'like', '%' . $request->q . '%');
            });
        }


         $rekening = $query->orderBy('kd_rek6')
            ->get()
            ->map(function ($item) use ($request) {
                $nilai_keluar = DB::table('trdtransout')
                    ->where('no_sp2d', $request->no_sp2d)
                    ->where('kd_sub_kegiatan', $request->kd_sub_kegiatan)
                    ->where('kd_skpd', auth()->user()->kd_skpd)
                    ->where('kd_rek6', $item->kd_rek6)
                    ->where('jenis_terima_sp2d', 0)
                    ->sum('nilai'); // Sum nilai for keluar

                $nilai_keluar = $nilai_keluar ?: 0; // Ensure nilai_keluar is 0 if null

                $sisa = $item->nilai - $nilai_keluar;

                return [
                    'id' => $item->kd_rek6,
                    'text' => $item->kd_rek6 . ' | ' . $item->nm_rek6 . ' | ' . $item->nilai . ' | ' . $nilai_keluar . ' | ' . $sisa,
                    'saldoawal' => $item->saldoawal,
                    'nm_rek' => $item->nm_rek6,
                    'sisa' => $sisa
                ];
            });

        return response()->json($rekening);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Gagal mengambil data rekening',
            'message' => $e->getMessage()
        ], 500);
    }
}

    public function getsubkegiatan(Request $request)
    {

        $search = $request->q;
        $no_sp2d = $request->no_sp2d;


        $sertifikat = DB::table('trdtransout')
            ->select('kd_sub_kegiatan','nm_sub_kegiatan')
            ->where('no_sp2d', $no_sp2d)
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('kd_sub_kegiatan', 'LIKE', "%{$search}%")
                      ->orWhere('nm_sub_kegiatan', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->distinct('kd_sub_kegiatan')
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



    public function store(Request $request)
    {

        $details = json_decode($request->input('details'), true) ?? [];

        $validator = Validator::make([
            'tgl_kas' => $request->tgl_kas,
            'jns_cp' => $request->jns_cp,
            'bank' => $request->bank,
            'jns_trans' => $request->jns_trans,
            'no_sp2d' => $request->no_sp2d,
            'kd_sub_kegiatan' => $request->kd_sub_kegiatan,
            'Keterangan' => $request->Keterangan,
            'details' => $details
        ], [
            'tgl_kas' => 'required|date',
            'jns_cp' => 'required',
            'bank' => 'required',
            'jns_trans' => 'required',
            'no_sp2d' => 'required',
            'kd_sub_kegiatan' => 'required',
            'details' => 'required|array|min:1'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $total = array_sum(array_map(function($detail) {
                return str_replace(['Rp', '.', ' '], '', $detail['total'] ?? $detail['nilai']);
            }, $details));

            $transaksiId = DB::table('trhkasin_pkd')->insertGetId([
                'no_sts' => $request->no_kas,
                'kd_skpd' => auth()->user()->kd_skpd,
                'tgl_sts' => $request->tgl_kas,
                'keterangan' => $request->keterangan,
                'total' => $request->total_belanja,
                'kd_sub_kegiatan' => $request->kd_sub_kegiatan,
                'jns_trans' => $request->jns_trans,
                'no_kas' => $request->no_kas,
                'tgl_kas' => $request->tgl_kas,
                'jns_cp' => $request->jns_cp,
                'no_sp2d' => $request->no_sp2d,
                'bank' => $request->bank,
            ]);

            $trhbku = DB::table('trhbku')->insert([
                'no_kas' => $request->no_kas,
                'tgl_kas' => $request->tgl_kas,
                'uraian' => $request->keterangan,
                'no_sp2d' => $request->no_sp2d,
                'kd_skpd' => auth()->user()->kd_skpd,
                'nm_skpd' => auth()->user()->name,
                'jns_trans' => $request->jns_trans,
                'id_user' => auth()->user()->id,
                'keluar' => $request->total_belanja,
                'created_at' => Carbon::now('Asia/Jakarta'),
                'id_trhkasin_pkd' => $request->no_kas,
            ]);

            $saldo_awal = DB::table('masterSkpd')
            ->where('kodeSkpd', auth()->user()->kd_skpd)
            ->value('saldoawal');

            if ($saldo_awal === null) {
                $saldo_awal = 0;
            }

            $total_belanja= $request->total_belanja;
            // **Mengupdate saldo awal berdasarkan jenis penerimaan**
            $saldo_baru = $saldo_awal - $total_belanja;

            DB::table('masterSkpd')
                ->where('kodeSkpd', auth()->user()->kd_skpd)
                ->update([
                    'saldoawal' => $saldo_baru,
                ]);


            $detailInserts = array_map(function($detail) use ($request) {
                return [
                    'kd_skpd' => auth()->user()->kd_skpd,
                    'no_sts' => $request->no_kas,
                    'kd_rek6' => $detail['kd_rek'],
                    'rupiah' => str_replace(['Rp', '.', ' '], '', $detail['nilai']),
                    'kd_sub_kegiatan' => $request->kd_sub_kegiatan,
                ];
            }, $details);

            if (!empty($detailInserts)) {
                DB::table('trdkasin_pkd')->insert($detailInserts);
            }

            $detailInserts1 = array_map(function($detail) use ($request) {
                $jenis_terima_sp2d = $request->has('jenis_terima_sp2d') ? 1 : 0;
                return [
                    'no_kas' => $request->no_kas,
                    'kd_skpd' => auth()->user()->kd_skpd,
                    'kd_sub_kegiatan' => $request->kd_sub_kegiatan,
                    'nm_sub_kegiatan' => $request->nm_sub_kegiatan,
                    'kd_rek6' => $detail['kd_rek'],
                    'nm_rek6' => $detail['nm_rek'],
                    'keluar' => str_replace(['Rp', '.', ' '], '', $detail['nilai']),
                    'id_trhkasin_pkd' => $request->no_kas,
                ];
            }, $details);
            if (!empty($detailInserts1)) {
                DB::table('trdbku')->insert($detailInserts1);
            }



            DB::commit();
            return redirect()->route('setorkas.index')
                ->with('success', 'Setor Sisa Kas berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('message', 'Gagal menyimpan Setor Sisa Kas: ' . $e->getMessage())
                ->withInput();
        }
    }

public function destroy($no_sts)
{
    try {
        $decryptedId = Crypt::decrypt($no_sts);

        $kasin_pkd = DB::table('trhkasin_pkd')
                ->where('no_sts', $decryptedId)
                ->first();

        $total = $kasin_pkd->total;
        $kodeSkpd = auth()->user()->kd_skpd;
        DB::table('trhkasin_pkd')->where('no_sts', $decryptedId)->delete();
        DB::table('trdkasin_pkd')->where('no_sts', $decryptedId)->delete();
        DB::table('trhbku')
        ->where('no_kas', $decryptedId)
        ->where('id_trhkasin_pkd', $decryptedId)
        ->delete();

        DB::table('trdbku')
        ->where('no_kas', $decryptedId)
        ->where('id_trhkasin_pkd', $decryptedId)
        ->delete();

        DB::table('masterSkpd')
                    ->where('kodeSkpd', $kodeSkpd)
                    ->update([
                        'saldoawal' => DB::raw("saldoawal + $total")
                    ]);

        return response()->json([
            'success' => true,
            'message' => 'Setor Sisa Kas berhasil dihapus.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus Setor Sisa Kas.'
        ], 500);
    }
}

public function edit($no_sts)
{
    // Dekripsi ID yang terenkripsi
    $decryptedId = Crypt::decrypt($no_sts);


    // Ambil data pajak berdasarkan ID menggunakan Query Builder
    $trmpot = DB::table('trhkasin_pkd')->where('no_sts', $decryptedId)->first();
    $trhtransout = DB::table('trhtransout')->where('no_sts', $trmpot->id_trhtransout)->first();

    $potonganDetails = DB::table('trdtrmpot')
            ->where('no_sts', $decryptedId)
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

public function update(Request $request, $no_sts)
{


    // Validasi input
    $validator = Validator::make($request->all(), [
        'tgl_sts' => 'required|date',
        'id_trhtransout' => 'nullable|exists:trhtransout,no_sts',
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
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    // Decode JSON dan pastikan valid
    $potonganData = json_decode($request->potongan_data, true);

    if (!is_array($potonganData) || empty($potonganData)) {
        return redirect()->back()
            ->with('error', 'Data potongan tidak valid atau kosong.')
            ->withInput();
    }

    // Hitung total nilai potongan dengan pengecekan yang lebih aman
    $totalNilai = array_reduce($potonganData, function ($carry, $item) {
        $nilai = isset($item['nilai']) ? str_replace(['Rp', '.', ','], '', $item['nilai']) : 0;
        return $carry + (float) $nilai;
    }, 0);

    DB::beginTransaction();
    try {
        // Ambil data lama dari `trhkasin_pkd`
        $trmpot = DB::table('trhkasin_pkd')->where('no_sts', $no_sts)->first();

        if (!$trmpot) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Update data Trmpot
        DB::table('trhkasin_pkd')->where('no_sts', $no_sts)->update([
            'tgl_sts' => $request->tgl_sts,
            'ket' => $request->ket,
            'username' => auth()->user()->username,
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
            'no_kas' => $no_sts,
            'pay' => $request->pay,
            'ebilling' => $request->ebilling,
            'id_trhtransout' => $request->id_trhtransout,
        ]);

        // Hapus potongan details lama
        DB::table('trdtrmpot')->where('no_sts', $no_sts)->delete();

        // Simpan potongan details baru
        $potonganInsertData = array_map(function ($detail) use ($request, $no_sts, $trmpot) {
            return [
                'kd_skpd' => $request->kd_skpd ?? $trmpot->kd_skpd,
                'no_sts' => $no_sts,
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
