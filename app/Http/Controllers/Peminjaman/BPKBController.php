<?php

namespace App\Http\Controllers\Peminjaman;

use App\Http\Controllers\Controller;
use App\Http\Requests\Peminjaman\BPKB\EditRequest;
use App\Http\Requests\Peminjaman\BPKB\TambahRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use PDF;

class BPKBController extends Controller
{
    public function index()
    {
        $daftarTandaTangan = DB::table('masterTtd')
            ->where(['kodeSkpd' => Auth::user()->kd_skpd])
            ->get();

        return view('kelola_peminjaman.bpkb.index', compact('daftarTandaTangan'));
    }

    public function load(Request $request)
    {
        // Page Length
        // $pageNumber = ($request->start / $request->length) + 1;
        // $pageLength = $request->length;
        // $skip       = ($pageNumber - 1) * $pageLength;

        // // Page Order
        // $orderColumnIndex = $request->order[0]['column'] ?? '0';
        // $orderBy = $request->order[0]['dir'] ?? 'desc';

        // get data from products table
        $query = DB::table('pinjamanBpkb as a')
            ->select('a.nomorSurat', 'a.statusTolak', 'a.nomorRegister', 'a.nomorBpkb', 'a.nomorPolisi', 'a.kodeSkpd', 'b.namaSkpd', 'a.file', 'a.statusPengajuan', 'statusVerifikasiOperator')
            ->leftJoin('masterSkpd as b', 'a.kodeSkpd', '=', 'b.kodeSkpd')
            ->get();

        // Search
        // $search = $request->search;
        // $query = $query->where(function ($query) use ($search) {
        //     $query->orWhere('nomorSurat', 'like', "%" . $search . "%");
        // });

        // $orderByName = 'nomorSurat';
        // switch ($orderColumnIndex) {
        //     case '0':
        //         $orderByName = 'nomorSurat';
        //         break;
        // }
        // $query = $query->orderBy($orderByName, $orderBy);
        // $recordsFiltered = $recordsTotal = $query->count();
        // $users = $query->skip($skip)->take($pageLength)->get();

        return DataTables::of($query)
            ->addColumn('aksi', function ($row) {
                if ($row->statusTolak == '1') {
                    $btn = '<a onclick="pengajuan(\'' . $row->nomorSurat . '\',\'' . $row->nomorRegister . '\',\'' . $row->kodeSkpd . '\',\'' . $row->file . '\',\'' . $row->statusPengajuan . '\', \'' . $row->statusTolak . '\', \'' . $row->statusVerifikasiOperator . '\')" class="btn btn-md btn-danger" title="Pengajuan Peminjaman Ditolak"><span class="fa-fw select-all fas"></span></a>';
                } else {
                    $btn = '<a href="' . route("peminjaman.bpkb.edit", ['no_surat' => Crypt::encrypt($row->nomorSurat), 'kd_skpd' => Crypt::encrypt($row->kodeSkpd)]) . '" class="btn btn-md btn-warning" style="margin-right:4px"><span class="fa-fw select-all fas"></span></a>';
                    if ($row->statusPengajuan == '0') {
                        $btn .= '<a onclick="hapus(\'' . $row->nomorSurat . '\',\'' . $row->nomorRegister . '\',\'' . $row->kodeSkpd . '\')" class="btn btn-md btn-danger" style="margin-right:4px"><span class="fa-fw select-all fas"></span></a>';
                    } else {
                        $btn .= '';
                    }
                    $btn .= '<a onclick="cetak(\'' . $row->nomorSurat . '\',\'' . $row->nomorRegister . '\',\'' . $row->kodeSkpd . '\')" class="btn btn-md btn-dark" style="margin-right:4px"><span class="fa-fw select-all fas"></span></a>';
                    $btn .= '<a onclick="pengajuan(\'' . $row->nomorSurat . '\',\'' . $row->nomorRegister . '\',\'' . $row->kodeSkpd . '\',\'' . $row->file . '\',\'' . $row->statusPengajuan . '\',\'' . $row->statusVerifikasiOperator . '\', \'' . $row->statusTolak . '\')" class="btn btn-md btn-primary"><span class="fa-fw select-all fas"></span></a>';
                }
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        return view('kelola_peminjaman.bpkb.create');
    }

    public function loadBpkb(Request $request)
    {
        $term = trim($request->q);
        $kodeSkpd = auth()->user()->kd_skpd;
        $formatted_tags = [];

        if (empty($term)) {
            $tags = DB::table('masterBpkb')
                ->where(function ($query) {
                    $query->where('statusPinjam', '=', '0')->orWhereNull('statusPinjam');
                })
                ->where('kodeSkpd', $kodeSkpd)
                ->limit(100)->get();
        } else {
            $tags = DB::table('masterBpkb')
                ->where(function ($query) {
                    $query->where('statusPinjam', '=', '0')->orWhereNull('statusPinjam');
                })
                ->where('kodeSkpd', $kodeSkpd)
                ->where(function ($query) use ($term) {
                    $query->where('nomorRegister', 'like', "%$term%")
                        ->orWhere('nomorPolisi', 'like', "%$term%")
                        ->orWhere('nomorBpkb', 'like', "%$term%")
                        ->orWhere('kodeSkpd', 'like', "%$term%");
                })
                ->limit(5)
                ->get();
        }

        foreach ($tags as $tag) {
            $formatted_tags[] = [
                'nomorRegister' => $tag->nomorRegister,
                'nomorPolisi' => $tag->nomorPolisi,
                'nomorBpkb' => $tag->nomorBpkb,
                'kodeSkpd' => $tag->kodeSkpd,
                'namaPemilik' => $tag->namaPemilik,
                'jenis' => $tag->jenis,
                'merk' => $tag->merk,
                'tipe' => $tag->tipe,
                'model' => $tag->model,
                'tahunPembuatan' => $tag->tahunPembuatan,
                'tahunPerakitan' => $tag->tahunPerakitan,
                'isiSilinder' => $tag->isiSilinder,
                'warna' => $tag->warna,
                'alamat' => $tag->alamat,
                'nomorRangka' => $tag->nomorRangka,
                'nomorMesin' => $tag->nomorMesin,
                'keterangan' => $tag->keterangan,
                'nomorPolisiLama' => $tag->nomorPolisiLama,
                'nomorBpkbLama' => $tag->nomorBpkbLama,
            ];
        }

        return \Response::json($formatted_tags);
    }

    public function store(TambahRequest $request)
    {
        $request = $request->validated();

        DB::beginTransaction();
        try {
            DB::table('masterBpkb')
                ->where([
                    'nomorRegister' => $request['nomorRegister'],
                    'kodeSkpd' => Auth::user()->kd_skpd
                ])
                ->lockForUpdate()
                ->first();

            DB::table('pinjamanBpkb')
                ->lockForUpdate()
                ->get();

            $nomorBaru = DB::table('pinjamanBpkb')
                ->selectRaw("ISNULL(MAX(nomorUrut),0)+1 as nomor")
                ->first();

            if (Str::length($nomorBaru->nomor) == '1') {
                $nomor = '00000' . $nomorBaru->nomor;
            } else if (Str::length($nomorBaru->nomor) == '2') {
                $nomor = '0000' . $nomorBaru->nomor;
            } else if (Str::length($nomorBaru->nomor) == '3') {
                $nomor = '000' . $nomorBaru->nomor;
            } else if (Str::length($nomorBaru->nomor) == '4') {
                $nomor = '00' . $nomorBaru->nomor;
            } else if (Str::length($nomorBaru->nomor) == '5') {
                $nomor = '0' . $nomorBaru->nomor;
            } else if (Str::length($nomorBaru->nomor) == '6') {
                $nomor = $nomorBaru->nomor;
            }

            DB::table('pinjamanBpkb')
                ->insert([
                    'kodeSkpd' => Auth::user()->kd_skpd,
                    'nomorUrut' => $nomorBaru->nomor,
                    'nomorSurat' => '000.2.3.2/' . $nomor . '/BPKAD-Aset',
                    'tanggalPinjam' => $request['tanggalPinjam'],
                    'nomorRegister' => $request['nomorRegister'],
                    'nomorPolisi' => $request['nomorPolisi'],
                    'nomorRangka' => $request['nomorRangka'],
                    'nomorBpkb' => $request['nomorBpkb'],
                    'namaPbp' => $request['namaPbp'],
                    'nipPbp' => $request['nipPbp'],
                    'nomorTelpPbp' => $request['nomorTelpPbp'],
                    'statusPengajuan' => '0',
                    'createdDate' => date('Y-m-d H:i:s'),
                    'createdUsername' => Auth::user()->name,
                    'updatedDate' => date('Y-m-d H:i:s'),
                    'updatedUsername' => Auth::user()->name,
                    'keperluan' => $request['keperluan']
                ]);

            DB::table('masterBpkb')
                ->where([
                    'nomorRegister' => $request['nomorRegister'],
                    'kodeSkpd' => Auth::user()->kd_skpd
                ])
                ->update([
                    'statusBpkb' => '1',
                    'statusPinjam' => '1'
                ]);

            // PROTEKSI SEMUA PEMINJAMAN LAINNYA SELAIN PEMINJAMAN YANG DIBUAT
            $pinjamanSebelumnya = DB::table('pinjamanBpkb')
                ->where([
                    'nomorRegister' => $request['nomorRegister'],
                    'kodeSkpd' => Auth::user()->kd_skpd,
                ])
                ->where('nomorUrut', '<', $nomorBaru->nomor)
                ->orderByDesc('nomorUrut')
                ->first();

            DB::table('pinjamanBpkb')
                ->where([
                    'nomorSurat' => $pinjamanSebelumnya->nomorSurat,
                    'nomorRegister' => $pinjamanSebelumnya->nomorRegister,
                    'kodeSkpd' => $pinjamanSebelumnya->kodeSkpd,
                ])
                ->update([
                    'statusPinjamLagi' => '1'
                ]);

            DB::commit();
            return redirect()
                ->route('peminjaman.bpkb.index')
                ->with('message', 'Peminjaman BPKB berhasil disimpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('peminjaman.bpkb.create')
                ->withInput()
                ->with('message', 'Peminjaman BPKB gagal disimpan!' . $e->getMessage());
        }
    }

    public function edit($nomorSurat, $kodeSkpd)
    {
        $nomorSurat = Crypt::decrypt($nomorSurat);
        $kodeSkpd = Crypt::decrypt($kodeSkpd);

        $dataPinjam = DB::table('pinjamanBpkb')
            ->where(['nomorSurat' => $nomorSurat, 'kodeSkpd' => $kodeSkpd])
            ->first();

        $dataBpkb = DB::table('masterBpkb')
            ->where(['nomorRegister' => $dataPinjam->nomorRegister, 'kodeSkpd' => $kodeSkpd])
            ->first();

        return view('kelola_peminjaman.bpkb.edit', compact('dataPinjam', 'dataBpkb'));
    }

    public function update(EditRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            DB::table('pinjamanBpkb')
                ->where([
                    'id' => $id,
                    'nomorSurat' => $request['nomorSurat'],
                    'kodeSkpd' => Auth::user()->kd_skpd
                ])
                ->lockForUpdate()
                ->first();

            DB::table('pinjamanBpkb')
                ->where([
                    'id' => $id,
                    'nomorSurat' => $request['nomorSurat'],
                    'kodeSkpd' => Auth::user()->kd_skpd
                ])
                ->update([
                    'tanggalPinjam' => $request['tanggalPinjam'],
                    'namaPbp' => $request['namaPbp'],
                    'nipPbp' => $request['nipPbp'],
                    'nomorTelpPbp' => $request['nomorTelpPbp'],
                    'updatedDate' => date('Y-m-d H:i:s'),
                    'updatedUsername' => Auth::user()->name,
                    'keperluan' => $request['keperluan']
                ]);

            DB::commit();
            return redirect()
                ->route('peminjaman.bpkb.index')
                ->with('message', 'Peminjaman BPKB berhasil diupdate!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->route(
                    'peminjaman.bpkb.edit',
                    [
                        'no_surat' => Crypt::encrypt($request['nomorSurat']),
                        'kd_skpd' => Crypt::encrypt($request['kodeSkpd']),
                    ]
                )
                ->withInput()
                ->with('message', 'Peminjaman BPKB gagal diupdate!');
        }
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {
            DB::table('pinjamanBpkb')
                ->where([
                    'nomorSurat' => $request->nomorSurat,
                    'kodeSkpd' => $request->kodeSkpd
                ])
                ->lockForUpdate()
                ->first();

            $dataPinjaman = DB::table('pinjamanBpkb')
                ->where([
                    'nomorSurat' => $request->nomorSurat,
                    'kodeSkpd' => $request->kodeSkpd
                ])
                ->first();

            $pinjamanSebelumnya = DB::table('pinjamanBpkb')
                ->where([
                    'nomorRegister' => $dataPinjaman->nomorRegister,
                    'kodeSkpd' => $dataPinjaman->kodeSkpd,
                ])
                ->where('nomorUrut', '<', $dataPinjaman->nomorUrut)
                ->orderByDesc('nomorUrut')
                ->first();

            DB::table('pinjamanBpkb')
                ->where([
                    'nomorSurat' => $request->nomorSurat,
                    'kodeSkpd' => $request->kodeSkpd
                ])
                ->delete();

            $cekBpkb = DB::table('pinjamanBpkb')
                ->where([
                    'nomorRegister' => $request->nomorRegister,
                    'kodeSkpd' => $request->kodeSkpd
                ])
                ->count();

            $masterBpkb = DB::table('masterBpkb')
                ->where([
                    'nomorRegister' => $request->nomorRegister,
                    'kodeSkpd' => $request->kodeSkpd
                ]);

            if ($cekBpkb > 0) {
                $masterBpkb
                    ->update([
                        'statusPinjam' => '0'
                    ]);
            } else {
                $masterBpkb
                    ->update([
                        'statusPinjam' => '0',
                        'statusBpkb' => '0'
                    ]);
            }

            DB::table('pinjamanBpkb')
                ->where([
                    'nomorSurat' => $pinjamanSebelumnya->nomorSurat,
                    'nomorRegister' => $pinjamanSebelumnya->nomorRegister,
                    'kodeSkpd' => $pinjamanSebelumnya->kodeSkpd,
                ])
                ->update([
                    'statusPinjamLagi' => '0'
                ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus!'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Data gagal dihapus!'
            ], 500);
        }
    }

    public function cetakPeminjaman(Request $request)
    {
        $nomorSurat = $request->nomorSurat;
        $nomorRegister = $request->nomorRegister;
        $tandaTangan = $request->tandaTangan;
        $tipe = $request->tipe;
        $kodeSkpd = Auth::user()->kd_skpd;

        $data = [
            'dataSkpd' => DB::table('masterSkpd')
                ->select('namaSkpd')
                ->where(['kodeSkpd' => $kodeSkpd])
                ->first(),
            'dataPeminjaman' => DB::table('pinjamanBpkb')
                ->where([
                    'nomorSurat' => $nomorSurat,
                    'nomorRegister' => $nomorRegister,
                    'kodeSkpd' => $kodeSkpd
                ])
                ->first(),
            'tandaTangan' => DB::table('masterTtd')
                ->where(['nip' => $tandaTangan])
                ->first(),
            'tipe' => $tipe
        ];

        $view = view('kelola_peminjaman.bpkb.cetak')->with($data);

        if ($tipe == 'layar') {
            return $view;
        } else if ($tipe == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOrientation('portrait')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('FormPeminjamanBPKB.pdf');
        }
    }

    public function handleFile($file, $tipeFile, $request)
    {
        $extension = $file->extension();

        $name =  $tipeFile . '_' . explode("/", $request->nomorSuratPengajuan)[1] . '_' . Auth::user()->kd_skpd . '_' . date('dmyHis') . '.' . $extension;

        // TIPE FILE => CONTOH TIFE FILE NYA SPM UP
        // NO SPM => NOMOR URUT SPM
        // KD SKPD => KODE SKPD SPM
        // DATE => RANDOM TANGGAL DAN WAKTU
        // EXTENSION => EXTENSION FILE

        return $name;
    }

    public function pengajuanPeminjaman(Request $request)
    {
        $nomorSuratPengajuan =  $request->nomorSuratPengajuan;
        $nomorRegisterPengajuan =  $request->nomorRegisterPengajuan;
        $filePengajuan =  $request->filePengajuan;

        $dataPeminjaman = DB::table('pinjamanBpkb')
            ->where([
                'nomorSurat' => $nomorSuratPengajuan,
                'nomorRegister' => $nomorRegisterPengajuan,
                'kodeSkpd' => Auth::user()->kd_skpd
            ])
            ->first();

        // CEK TELAH VERIFIKASI OPERATOR
        if ($dataPeminjaman->statusVerifikasiOperator == '1') {
            return response()->json([
                'status' => true,
                'message' => 'Peminjaman tidak dapat dibatalkan, data telah diverifikasi oleh operator! Silahkan refresh!'
            ], 500);
        }

        $request->validate([
            'nomorSuratPengajuan' => 'required',
            'nomorRegisterPengajuan' => 'required',
            'filePengajuan' => $dataPeminjaman->statusPengajuan == '0' ? 'required|mimes:pdf|max:5000' : 'nullable',
        ], [
            'nomorSuratPengajuan.required' => 'Nomor surat pengajuan tidak boleh kosong',
            'nomorRegisterPengajuan.required' => 'Nomor register pengajuan tidak boleh kosong',
            'filePengajuan.required' => 'File pengajuan tidak boleh kosong',
            'filePengajuan.mimes' => 'File pengajuan wajib PDF',
            'filePengajuan.max' => 'File pengajuan maksimal 5MB',
        ]);

        DB::beginTransaction();
        try {
            $file = isset($filePengajuan) ? $this->handleFile($filePengajuan, 'BPKB', $request) : null;

            if ($dataPeminjaman->statusPengajuan == '0') {
                DB::table('pinjamanBpkb')
                    ->where([
                        'nomorSurat' => $nomorSuratPengajuan,
                        'nomorRegister' => $nomorRegisterPengajuan,
                        'kodeSkpd' => Auth::user()->kd_skpd
                    ])
                    ->update([
                        'statusPengajuan' => '1',
                        'file' => $file
                    ]);

                Storage::putFileAs('public/images/Peminjaman/BPKB/' . Auth::user()->kd_skpd . '/', $filePengajuan, $file);
            } else {
                DB::table('pinjamanBpkb')
                    ->where([
                        'nomorSurat' => $nomorSuratPengajuan,
                        'nomorRegister' => $nomorRegisterPengajuan,
                        'kodeSkpd' => Auth::user()->kd_skpd
                    ])
                    ->update([
                        'statusPengajuan' => '0',
                        'file' => ''
                    ]);

                unlink(storage_path('app/public/images/Peminjaman/BPKB/' . Auth::user()->kd_skpd . '/' . $dataPeminjaman->file));
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => $dataPeminjaman->statusPengajuan == '0' ? 'Pengajuan berhasil diajukan' : 'Pengajuan berhasil dibatalkan'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => true,
                'message' => $dataPeminjaman->statusPengajuan == '0' ? 'Pengajuan tidak berhasil diajukan' : 'Pengajuan tidak berhasil dibatalkan'
            ], 500);
        }
    }
}
