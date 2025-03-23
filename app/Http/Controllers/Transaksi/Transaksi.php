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

class Transaksi extends Controller
{



    public function index()
    {

        return view('transaksi.index');
    }

    public function load(Request $request)
{
    if ($request->ajax()) {
        $search = $request->search;

        $data = DB::table('trhtransout')
            ->where('kd_skpd', Auth::user()->kd_skpd);

        if ($search) {
            $data = $data->where(function ($query) use ($search) {
                $query->where('no_bukti', 'like', "%" . $search . "%")
                      ->orWhere('nm_skpd', 'like', "%" . $search . "%")
                      ->orWhere('ket', 'like', "%" . $search . "%"); // Tambahan untuk pencarian lebih fleksibel
            });
        }

        return DataTables::of($data->get()) // Tambahkan get() di sini
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $btn = '<a href="' . route('transaksi.edit', Crypt::encrypt($row->no_bukti)) . '" class="btn btn-primary btn-sm" style="margin-right:4px"><i class="fas fa-eye"></i></a>';
                $btn .= '<button class="btn btn-sm btn-success print-btn" data-no-lpj="' . $row->no_bukti . '"><i class="fas fa-print"></i></button>';
                $btn .= '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('transaksi.destroy', Crypt::encrypt($row->no_bukti)) . '"><i class="fas fa-trash-alt"></i></button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}


    public function create()
    {
        $lastNoBukti = DB::table('trhtransout')->max('no_bukti');
        $newNoBukti = $lastNoBukti ? $lastNoBukti + 1 : 1;

        $kd_skpd = auth()->user()->kd_skpd;
        $nm_skpd = Auth::user()->name;
        $rek_pengeluaran = Auth::user()->rek_pengeluaran;
        $kd_sub_kegiatan = DB::table('ms_sub_kegiatan')
                            ->select('kd_sub_kegiatan','nm_sub_kegiatan') // Ambil hanya kolom yang diperlukan
                            ->distinct() // Hilangkan duplikasi
                            ->get();

          $saldo_awal = DB::table('masterSkpd')
                            ->where('kodeSkpd', $kd_skpd) // Filter berdasarkan kd_skpd
                            ->value('saldoawal');




        return view('transaksi.create', compact('kd_skpd','nm_skpd','newNoBukti','rek_pengeluaran','kd_sub_kegiatan','saldo_awal'));
    }

    public function getno_transaksi(Request $request)
    {
        $search = $request->q;
        $kd_skpd = auth()->user()->kd_skpd;


        $sertifikat = DB::table('trhtransout')
            ->join('trdtransout', 'trhtransout.no_bukti', '=', 'trdtransout.no_bukti')
            ->select(
                'trhtransout.no_bukti',
                'trhtransout.tgl_bukti',
                'trhtransout.ket',
            )
            ->where('trhtransout.kd_skpd', $kd_skpd)
            ->where('trhtransout.jenis_terima_sp2d', "1")
            ->where(function($query) {
                $query->where('trhtransout.jenis_beban', 'GAJI')
                      ->orWhere('trhtransout.jenis_beban', 'Barang & Jasa');
            })
            ->when(!empty($search), function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('trhtransout.no_bukti', 'LIKE', "%{$search}%")
                    ->orWhere('trhtransout.tgl_bukti', 'LIKE', "%{$search}%")
                    ->orWhere('trhtransout.ket', 'LIKE', "%{$search}%");
                });
            })
            ->groupBy(
                'trhtransout.no_bukti',
                'trhtransout.tgl_bukti',
                'trhtransout.ket',

            )
            ->limit(1000)
            ->get();

        $data = $sertifikat->map(function ($item) {
            return [
                'id' => $item->no_bukti,
                'text' => "{$item->no_bukti} | {$item->tgl_bukti} | {$item->ket}",
            ];
        });

        return response()->json($data);
    }

    public function getpotongandata(Request $request)
    {
        $no_transaksi = $request->no_transaksi;



        // Ambil data dari trdtrmpot berdasarkan no_bukti yang sesuai
        $trdtrmpot = DB::table('trdtransout')
        ->join('ms_sumberdana', 'trdtransout.sumber', '=', 'ms_sumberdana.id')
        ->where('trdtransout.no_bukti', $no_transaksi)
        ->select(
            'trdtransout.*', // Ambil semua kolom dari trdtransout
            'ms_sumberdana.sumber_dana as nm_dana',
            'ms_sumberdana.id as id_dana' // Ambil nama sumber dana dari ms_sumberdana
        )
        ->get();


        return response()->json([
            'success' => true,
            'trdtrmpot' => $trdtrmpot
        ]);
    }



    public function getSubKegiatan(Request $request)
    {
    try {
        $query = DB::table('ms_sub_kegiatan')
            ->select('kd_sub_kegiatan', 'nm_sub_kegiatan');

        // Search both kd_sub_kegiatan and nm_sub_kegiatan
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('kd_sub_kegiatan', 'like', '%' . $request->search . '%')
                    ->orWhere('nm_sub_kegiatan', 'like', '%' . $request->search . '%');
            });
        }

        $subKegiatan = $query->orderBy('kd_sub_kegiatan')
            ->get();

        return response()->json($subKegiatan);
    } catch (\Exception $e) {

        return response()->json([
            'error' => 'Gagal mengambil data sub kegiatan',
            'message' => 'Terjadi kesalahan dalam mengambil data'
        ], 500);
    }
    }

    public function getsumberdana(Request $request)
    {

    try {
        $query = DB::table('ms_sumberdana')
            ->select('id','kd_dana', 'nm_dana','anggaran_tahun');

        if ($request->has('id_sumberdana')) {
                $query->where('id', $request->id_sumberdana);
            }
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

    public function gettotalnilai(Request $request)
    {
        // $kd_rek = $request->kd_rek;

        $kd_rek = $request->input('kd_rek');
        // Ambil total nilai dari tabel `trdtransout` berdasarkan kd_rek
        $totalNilai = DB::table('trdtransout')
            ->where('kd_rek6', $kd_rek)
            ->where('jenis_terima_sp2d', "0")
            ->sum('nilai');

        return response()->json([
            'total_nilai' => $totalNilai
        ]);
    }

    public function getrekening(Request $request)
{
    try {
        // Get the selected kd_sub_kegiatan from the request
        $kd_sub_kegiatan = $request->input('kd_sub_kegiatan');
        $jenis_pergeseran = $request->input('jenis_pergeseran');
        // Initialize query from ms_anggaran table
        $query = DB::table('ms_anggaran');

        // Filter by kd_sub_kegiatan if it's provided
        if ($kd_sub_kegiatan) {
            $query->where('kd_sub_kegiatan', $kd_sub_kegiatan);
        }

        if ($jenis_pergeseran) {
            $query->where('jenis_anggaran', $jenis_pergeseran);
        }

        // Select all the required fields
        $query->select(
            'kd_rek',
            'nm_rek',
            'anggaran_tahun',
            'anggaran_tw1',
            'anggaran_tw2',
            'anggaran_tw3',
            'anggaran_tw4',
            'rek1',
            'rek2',
            'rek3',
            'rek4',
            'rek5',
            'rek6',
            'rek7',
            'rek8',
            'rek9',
            'rek10',
            'rek11',
            'rek12',
            'status_anggaran',
            'status_anggaran_kas',
            'id_sumberdana'
        );

        // Handle search functionality if search parameter is provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kd_rek', 'like', '%' . $search . '%')
                  ->orWhere('nm_rek', 'like', '%' . $search . '%');
            });
        }

        // Order the results by kd_rek
        $rekening = $query->orderBy('kd_rek')->get();

        // Return the results as JSON
        return response()->json($rekening);

    } catch (\Exception $e) {
        // Log the error for debugging
        \Log::error('Error in getrekening: ' . $e->getMessage());

        // Return a proper error response
        return response()->json([
            'error' => 'Gagal mengambil data rekening',
            'message' => 'Terjadi kesalahan dalam mengambil data: ' . $e->getMessage()
        ], 500);
    }
}




    public function getsp2d(Request $request)
    {
        try {
            $query = DB::table('trhsp2d')
                ->select('no_sp2d', 'tgl_sp2d')
                ->where('kd_skpd', auth()->user()->kd_skpd);

            // Validasi dan pencarian data SP2D jika ada parameter 'search'
            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->where(function($q) use ($searchTerm) {
                    $q->where('no_sp2d', 'like', '%' . $searchTerm . '%')
                      ->orWhere('tgl_sp2d', 'like', '%' . $searchTerm . '%');
                });
            }

            // Ambil data SP2D yang sudah difilter
            $sp2dList = $query->orderBy('no_sp2d')->get();

            return response()->json($sp2dList);
        } catch (\Exception $e) {
            // Tambahkan detail error untuk debugging (hapus saat production)
            return response()->json([
                'error' => 'Gagal mengambil data SP2D',
                'message' => $e->getMessage()  // Bisa dihapus di production untuk keamanan
            ], 500);
        }
    }


    public function store(Request $request)
    {


        $details = json_decode($request->input('details'), true) ?? [];
        $details_tujuan = json_decode($request->input('details_tujuan'), true) ?? [];
        $jenis_terima_sp2d = $request->has('jenis_terima_sp2d') ? 1 : 0;
        $perlimpahan = $request->has('jenis_perlimpahan') ? 1 : 0;

        $validator = Validator::make([
            'tgl_bukti' => $request->tgl_bukti,
            'jenis_beban' => $request->jenis_beban,
            'ket' => $request->ket,
            'details' => $details
        ], [
            'tgl_bukti' => 'required|date',
            'jenis_beban' => 'required',
            'details' => 'required|array|min:1',
            'ket' => 'required',
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

            $nilai_transfer = array_sum(array_map(function($detail_tujuan) {
                return str_replace(['Rp', '.', ' '], '', $detail_tujuan['nilai_transfer']);
            }, $details_tujuan));

            $transaksiId = DB::table('trhtransout')->insertGetId([
                'no_kas' => $request->no_bukti,
                'tgl_kas' => $request->tgl_bukti,
                'no_bukti' => $request->no_bukti,
                'tgl_bukti' => $request->tgl_bukti,
                'kd_skpd' => auth()->user()->kd_skpd,
                'nm_skpd' => auth()->user()->name,
                'no_sp2d' => $details[0]['no_sp2d'] ?? null,
                'ket' => $request->ket,
                'total' => $request->total_belanja, // Now a clean decimal value
                'jenis_beban' => $request->jenis_beban,
                'jenis_terima_sp2d' => $jenis_terima_sp2d,
                'perlimpahan' => $perlimpahan,
                'username' => auth()->user()->kd_skpd,
                'tgl_update' => now(),
            ]);

            $trhbku = DB::table('trhbku')->insert([
                'no_kas' => $request->no_bukti,
                'tgl_kas' => $request->tgl_bukti,
                'uraian' => $request->ket,
                'no_sp2d' => $details[0]['no_sp2d'] ?? null,
                'kd_skpd' => auth()->user()->kd_skpd,
                'nm_skpd' => auth()->user()->name,
                'jns_trans' => "6",
                'id_user' => auth()->user()->id,
                'terima' => $jenis_terima_sp2d == 1 ? $request->total_belanja : 0, // Jika 1, simpan di 'terima'
                'keluar' => $jenis_terima_sp2d == 0 ? $request->total_belanja : 0,
                'created_at' => Carbon::now('Asia/Jakarta'),
                'id_trhtransout' => $request->no_bukti,
            ]);






            $saldo_awal = DB::table('masterSkpd')
            ->where('kodeSkpd', auth()->user()->kd_skpd)
            ->value('saldoawal');

            $realisasi = DB::table('masterSkpd')
            ->where('kodeSkpd', auth()->user()->kd_skpd)
            ->value('realisasi');


            if ($saldo_awal === null) {
                $saldo_awal = 0;
            }
            if ($realisasi === null) {
                $realisasi = 0;
            }

            $total_belanja= $request->total_belanja;
            // **Mengupdate saldo awal berdasarkan jenis penerimaan**
            $saldo_baru = ($jenis_terima_sp2d == 1) ? ($saldo_awal + $total_belanja) : ($saldo_awal - $total_belanja);
            if ($jenis_terima_sp2d == 0) {
                $realisasi += $total_belanja;
            }
            DB::table('masterSkpd')
                ->where('kodeSkpd', auth()->user()->kd_skpd)
                ->update([
                    'saldoawal' => $saldo_baru,
                    'realisasi' => $realisasi
                ]);


            $detailInserts = array_map(function($detail) use ($request) {
                $jenis_terima_sp2d = $request->has('jenis_terima_sp2d') ? 1 : 0;
                $perlimpahan = $request->has('jenis_perlimpahan') ? 1 : 0;
                return [
                    'no_bukti' => $request->no_bukti,
                    'tgl_bukti' => $request->tgl_bukti,
                    'kd_skpd' => auth()->user()->kd_skpd,
                    'jenis_terima_sp2d' => $jenis_terima_sp2d,
                    'perlimpahan' => $perlimpahan,
                    'kd_sub_kegiatan' => $detail['kd_sub_kegiatan'],
                    'nm_sub_kegiatan' => $detail['nm_sub_kegiatan'],
                    'kd_rek6' => $detail['kd_rek'],
                    'nm_rek6' => $detail['nm_rek'],
                    'nilai' => str_replace(['Rp', '.', ' '], '', $detail['nilai']),
                    'no_sp2d' => $detail['no_sp2d'],
                    'sumber' => $detail['kd_dana'],
                    'volume' => $detail['volume'] ?? 0,
                    'satuan' => $detail['satuan'] ?? null,
                    'total_spd' => str_replace(['Rp', '.', ' '], '', $detail['totalSPD'] ?? '0') ?: '0',
                    'realisasi_spd' => str_replace(['Rp', '.', ' '], '', $detail['realisasiSPD'] ?? '0') ?: '0',
                    'sisa_spd' => str_replace(['Rp', '.', ' '], '', $detail['sisaSPD'] ?? '0') ?: '0',
                    'total_anggaran_kas' => str_replace(['Rp', '.', ' '], '', $detail['totalAnggaranKas'] ?? '0') ?: '0',
                    'realisasi_anggaran_kas' => str_replace(['Rp', '.', ' '], '', $detail['realisasiAnggaranKas'] ?? '0') ?: '0',
                    'sisa_anggaran_kas' => str_replace(['Rp', '.', ' '], '', $detail['sisaAnggaranKas'] ?? '0') ?: '0',
                    'anggaran' => str_replace(['Rp', '.', ' '], '', $detail['anggaran'] ?? '0') ?: '0',
                    'realisasi_anggaran' => str_replace(['Rp', '.', ' '], '', $detail['realisasiAnggaran'] ?? '0') ?: '0',
                    'sisa_anggaran' => str_replace(['Rp', '.', ' '], '', $detail['sisaAnggaran'] ?? '0') ?: '0',
                    'rencana_pergeseran_anggaran' => str_replace(['Rp', '.', ' '], '', $detail['rencanaPergeseranAnggaran'] ?? '0') ?: '0',
                    'realisasi_rencana_pergeseran_anggaran' => str_replace(['Rp', '.', ' '], '', $detail['realisasiPergeseranAnggaran'] ?? '0') ?: '0',
                    'sisa_rencana_pergeseran_anggaran' => str_replace(['Rp', '.', ' '], '', $detail['sisaPergeseranAnggaran'] ?? '0') ?: '0',
                    'nilai_sumber_dana' => str_replace(['Rp', '.', ' '], '', $detail['nilaisumberdana'] ?? '0') ?: '0',
                    'realisasi_sumber_dana' => str_replace(['Rp', '.', ' '], '', $detail['realisasinilaisumberdana'] ?? '0') ?: '0',
                    'sisa_sumber_dana' => str_replace(['Rp', '.', ' '], '', $detail['sisanilaisumberdana'] ?? '0') ?: '0',
                ];
            }, $details);

            if (!empty($detailInserts)) {
                DB::table('trdtransout')->insert($detailInserts);
            }

            $detailInserts1 = array_map(function($detail) use ($request) {
                $jenis_terima_sp2d = $request->has('jenis_terima_sp2d') ? 1 : 0;
                return [
                    'no_kas' => $request->no_bukti,
                    'kd_skpd' => auth()->user()->kd_skpd,
                    'kd_sub_kegiatan' => $detail['kd_sub_kegiatan'],
                    'nm_sub_kegiatan' => $detail['nm_sub_kegiatan'],
                    'kd_rek6' => $detail['kd_rek'],
                    'nm_rek6' => $detail['nm_rek'],
                    'terima' => $jenis_terima_sp2d == 1 ? str_replace(['Rp', '.', ' '], '', $detail['nilai']) : 0, // Jika 1, simpan di 'terima'
                    'keluar' => $jenis_terima_sp2d == 0 ? str_replace(['Rp', '.', ' '], '', $detail['nilai']) : 0,
                    'id_trhtransout' => $request->no_bukti,
                ];
            }, $details);
            if (!empty($detailInserts1)) {
                DB::table('trdbku')->insert($detailInserts1);
            }


            $detailInserts3 = array_map(function($details_tujuan) use ($request) {
                return [
                    'no_voucher' => $request->no_bukti,
                    'tgl_voucher' => $request->tgl_bukti,
                    'rekening_awal' => $request->rek_pengeluaran,
                    'nm_rekening_tujuan' => $details_tujuan['nm_rekening'],
                    'rekening_tujuan' => $details_tujuan['rekeningtujuan'],
                    'bank_tujuan' => $details_tujuan['bank'],
                    'ket_tpp' => $details_tujuan['ket_tpp'],
                    'kd_skpd' => auth()->user()->kd_skpd,
                    'nilai' =>str_replace(['Rp', '.', ' '], '', $details_tujuan['nilai_transfer']),


                ];
            }, $details_tujuan);
            if (!empty($detailInserts3)) {
                DB::table('trdtransout_transfercms')->insert($detailInserts3);
            }



            DB::commit();
            return redirect()->route('transaksi.index')
                ->with('success', 'Transaksi berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('message', 'Gagal menyimpan transaksi: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function getrekeningtujuan(Request $request)
    {
        $search = $request->q;
        $kd_skpd = auth()->user()->kd_skpd;

        $sertifikat = DB::table('ms_rekening_bank_online')
            ->select('*')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('rekening', 'LIKE', "%{$search}%")
                      ->orWhere('nm_rekening', 'LIKE', "%{$search}%");
            })
            ->when(!empty($kd_skpd), function ($query) use ($kd_skpd) {
                $query->where('kd_skpd', $kd_skpd); // Filter berdasarkan kode kegiatan
            })
            ->limit(10)
            ->get();

        $data = $sertifikat->map(function ($item) {
            return [
                'id' => $item->rekening,
                'text' => implode(' | ', [
                    $item->rekening,
                    $item->nm_rekening,

                ]),
                'nm_rekening' => $item->nm_rekening,
                'nm_bank' => $item->nm_bank,
                'bank' => $item->bank
            ];
        });

        return response()->json($data);

    }

    public function print(Request $request)
{

    $no_bukti = $request->no_bukti;
    $jenis_cetak = $request->jenis_cetak;
    $jenis = $request->jenis_print;

    $data_lpj = DB::table('trdtransout_transfercms')
        ->select(
            'trdtransout_transfercms.*',
            'ms_bank.nama',
        )
        ->leftJoin('ms_bank', 'trdtransout_transfercms.bank_tujuan', '=', 'ms_bank.kode')
        ->where('trdtransout_transfercms.no_voucher', $no_bukti);

    // Filter berdasarkan jenis cetak
    if ($jenis_cetak == 'OB') {
        // Untuk OB, tampilkan hanya data yang mengandung kata "kalbar" atau "kalimantan barat"
        $data_lpj = $data_lpj->where(function($query) {
            $query->whereRaw('LOWER(ms_bank.nama) LIKE ?', ['%kalbar%']);
        });
    } elseif ($jenis_cetak == 'SKN') {
        // Untuk SKN, tampilkan data yang TIDAK mengandung kata "kalbar" atau "kalimantan barat"
        $data_lpj = $data_lpj->where(function($query) {
            $query->whereRaw('LOWER(ms_bank.nama) NOT LIKE ?', ['%kalbar%']);
        });
    }

    $data = $data_lpj->get();

    if ($data->isEmpty()) {
        return back()->with('error', 'Data tidak ditemukan.');
    }
    $firstData = $data->first();
    $ket_tpp = $firstData->ket_tpp;

    // Format tanggal hari ini
    $tanggal = date('dmY'); // Format: tglbulantahun (misal: 25102023)

    // Ambil atau inisialisasi no_urut dari session
    $no_urut = $request->session()->get('print_count', 0) + 1;
    $request->session()->put('print_count', $no_urut);

    // Format no_urut menjadi 3 digit (001, 002, dst)
    $no_urut_formatted = str_pad($no_urut, 3, '0', STR_PAD_LEFT);

    // Buat nama file sesuai format
    $filename = "{$jenis_cetak}_DINKESKB_{$tanggal}_{$no_urut_formatted}_{$ket_tpp}.xls";

    // View berdasarkan jenis cetak
    if ($jenis_cetak == 'OB') {
        $view = view('transaksi.print.ob', [
            'data' => $data,
            'jenis_cetak' => $jenis_cetak
        ]);
    } elseif ($jenis_cetak == 'SKN') {
        $view = view('transaksi.print.skn', [
            'data' => $data,
            'jenis_cetak' => $jenis_cetak
        ]);
    } else {
        return back()->with('error', 'Jenis cetakan tidak valid.');
    }

    // Output berdasarkan jenis (excel, pdf, atau layar)
    if ($jenis == 'layar') {
        return $view;
    } elseif ($jenis == 'pdf') {
        $pdf = PDF::loadHtml($view->render())
            ->setPaper('legal')
            ->setOrientation('landscape')
            ->setOption('margin-left', 15)
            ->setOption('margin-right', 15);

        $filename = $jenis_cetak . '_' . date('Ymd_His') . '.pdf';
        return $pdf->stream($filename);
    } elseif ($jenis == 'excel') {
        return response($view->render())
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename=' . $filename);
    }

    return back()->with('message', 'Dokumen berhasil dicetak.');
}


    public function edit($no_bukti)
    {
        // Dekripsi ID yang terenkripsi
        $decryptedId = Crypt::decrypt($no_bukti);


        // Ambil data pajak berdasarkan ID menggunakan Query Builder
        $transaksi = DB::table('trhtransout')->where('no_bukti', $decryptedId)->first();

        $potonganDetails = DB::table('trdtransout')
        ->leftJoin('ms_sumberdana', function ($join) {
            $join->on(
                DB::raw('CAST(trdtransout.sumber AS INT)'),
                '=',
                'ms_sumberdana.id'
            );
        })
        ->where('trdtransout.no_bukti', $decryptedId)
        ->select(
            'trdtransout.nm_sub_kegiatan',
            'trdtransout.kd_rek6',
            'trdtransout.nm_rek6',
            'trdtransout.sumber',
            'trdtransout.nilai',
            'ms_sumberdana.nm_dana'
        )
        ->distinct() // Tambahkan ini
        ->get();


        // Cek apakah data ditemukan
        if (!$transaksi) {
            return redirect()->route('transaksi.index')->with('message', 'Data Terima Potongan Pajak tidak ditemukan.');
        }
        $rek_pengeluaran = Auth::user()->rek_pengeluaran;

        // Tampilkan view untuk mengedit data
        return view('transaksi.edit', compact('transaksi','potonganDetails','rek_pengeluaran'));
    }

    public function getrealisasi(Request $request)
    {

        $kd_rek = $request->kd_rek;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;

        $realisasi = DB::table('trdtransout')
            ->where('kd_skpd', auth()->user()->kd_skpd)
            ->where('kd_rek6', $kd_rek)
            ->where('kd_sub_kegiatan', $kd_sub_kegiatan)
            ->where('jenis_terima_sp2d', "0")
            ->select(
                DB::raw('SUM(nilai) as realisasi')
            )
            ->first();

        // Pastikan nilai realisasi tidak null
        $response = [
            'success' => true,
            'realisasiSPD' => $realisasi->realisasi ?? 0,
            'realisasiAnggaranKas' => $realisasi->realisasi ?? 0, // Gantilah jika ada kolom berbeda
            'realisasiAnggaran' => $realisasi->realisasi ?? 0,
            'realisasiSumberDana' => $realisasi->realisasi ?? 0,
        ];

        return response()->json($response);
    }

    public function destroy($no_bukti)
    {
        try {
            $decryptedId = Crypt::decrypt($no_bukti);

            // Ambil data transaksi sebelum menghapusnya
            $transaksi = DB::table('trhtransout')
                ->where('no_bukti', $decryptedId)
                ->first();

            if (!$transaksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan.'
                ], 404);
            }

            $jenis_terima_sp2d = $transaksi->jenis_terima_sp2d;
            $total = $transaksi->total;
            $kodeSkpd = auth()->user()->kd_skpd;

            // Hapus data dari tabel terkait
            DB::table('trhtransout')->where('no_bukti', $decryptedId)->delete();
            DB::table('trdtransout')->where('no_bukti', $decryptedId)->delete();
            DB::table('trdtransout_transfercms')->where('no_voucher', $decryptedId)->delete();
            DB::table('trhbku')->where('no_kas', $decryptedId)->where('id_trhtransout', $decryptedId)->delete();
            DB::table('trdbku')->where('no_kas', $decryptedId)->where('id_trhtransout', $decryptedId)->delete();

            // Update saldoawal di masterSkpd sesuai jenis_terima_sp2d
            if ($jenis_terima_sp2d == 1) {
                DB::table('masterSkpd')
                    ->where('kodeSkpd', $kodeSkpd)
                    ->update([
                        'saldoawal' => DB::raw("saldoawal - $total")
                    ]);
            } elseif ($jenis_terima_sp2d == 0) {
                DB::table('masterSkpd')
                    ->where('kodeSkpd', $kodeSkpd)
                    ->update([
                        'saldoawal' => DB::raw("saldoawal + $total"),
                        'realisasi' => DB::raw("realisasi - $total")
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Subkegiatan berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus subkegiatan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
