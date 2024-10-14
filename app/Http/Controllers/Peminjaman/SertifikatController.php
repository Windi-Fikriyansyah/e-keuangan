<?php

namespace App\Http\Controllers\Peminjaman;

use App\Http\Controllers\Controller;
use App\Http\Requests\Peminjaman\Sertifikat\EditRequest;
use App\Http\Requests\Peminjaman\Sertifikat\TambahRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PDF;

class SertifikatController extends Controller
{
    public function index()
    {
        $daftarTandaTangan = DB::table('masterTtd')
            ->where(['kodeSkpd' => Auth::user()->kd_skpd])
            ->get();
        return view('kelola_peminjaman.sertifikat.index', compact('daftarTandaTangan'));
    }

    public function load(Request $request)
    {
        $pageNumber = ($request->start / $request->length) + 1;
        $pageLength = $request->length;
        $skip       = ($pageNumber - 1) * $pageLength;

        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        $query = DB::table('pinjamanSertifikat as a')
            ->select('a.nomorSurat','a.statusTolak', 'a.nomorRegister','a.statusVerifikasiOperator', 'a.nomorSertifikat','a.statusPengajuan', 'a.NIB','a.file', 'a.kodeSkpd', 'b.namaSkpd')
            ->leftJoin('masterSkpd as b', 'a.kodeSkpd', '=', 'b.kodeSkpd');

        $search = $request->search;
        $query = $query->where(function ($query) use ($search) {
            $query->orWhere('nomorSurat', 'like', "%" . $search . "%");
        });

        $orderByName = 'nomorSurat';
        switch ($orderColumnIndex) {
            case '0':
                $orderByName = 'nomorSurat';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $users = $query->skip($skip)->take($pageLength)->get();

        return DataTables::of($users)
        ->addColumn('aksi', function ($row) {
            if ($row->statusTolak == '1'){
                $btn = '<a onclick="pengajuan(\'' . $row->nomorSurat . '\',\'' . $row->nomorRegister . '\',\'' . $row->kodeSkpd . '\',\'' . $row->file . '\',\'' . $row->statusPengajuan . '\', \'' . $row->statusTolak . '\', \'' . $row->statusVerifikasiOperator . '\')" class="btn btn-md btn-danger" title="Pengajuan Peminjaman Ditolak"><span class="fa-fw select-all fas"></span></a>';
            }  else{
                $btn = '<a href="' . route("peminjaman.sertifikat.edit", ['no_surat' => Crypt::encrypt($row->nomorSurat), 'kd_skpd' => Crypt::encrypt($row->kodeSkpd)]) . '" class="btn btn-md btn-warning" style="margin-right:4px"><span class="fa-fw select-all fas"></span></a>';

                if ($row->statusPengajuan == '0') {
                    $btn .= '<a onclick="hapus(\'' . $row->nomorSurat . '\',\'' . $row->nomorRegister . '\',\'' . $row->kodeSkpd . '\')" class="btn btn-md btn-danger" style="margin-right:4px"><span class="fa-fw select-all fas"></span></a>';
                } else {
                    $btn .= '';
                }

                $btn .= '<a onclick="cetak(\'' . $row->nomorSurat . '\',\'' . $row->nomorRegister . '\',\'' . $row->kodeSkpd . '\')" class="btn btn-md btn-dark" style="margin-right:4px"><span class="fa-fw select-all fas"></span></a>';

                $btn .= '<a onclick="pengajuan(\'' . $row->nomorSurat . '\',\'' . $row->nomorRegister . '\',\'' . $row->kodeSkpd . '\',\'' . $row->file . '\',\'' . $row->statusPengajuan . '\', \'' . $row->statusTolak . '\', \'' . $row->statusVerifikasiOperator . '\')" class="btn btn-md btn-primary"><span class="fa-fw select-all fas"></span></a>';


            }

            return $btn;

        })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    protected function generateNomorUrut()
    {

        DB::beginTransaction();
        try {
            $lastRecord = DB::table('pinjamanSertifikat')->orderBy('nomorUrut', 'desc')->first();
            $lastNomorUrut = $lastRecord ? intval($lastRecord->nomorUrut) : 0;
            $newNomorUrut = str_pad($lastNomorUrut + 1, 6, '0', STR_PAD_LEFT);
            DB::commit();

            return $newNomorUrut;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function create()
    {
        $nomorUrut = $this->generateNomorUrut();
        $nomorSurat = '000.2.3.2/' . $nomorUrut . '/BPKAD-Aset';
        return view('kelola_peminjaman.sertifikat.create', compact('nomorSurat'));
    }

    public function loadSertifikat(Request $request)
    {
        $term = trim($request->q);
        $kodeSkpd = auth()->user()->kd_skpd;
        $formatted_tags = [];

        if (empty($term)) {
            $tags = DB::table('masterSertifikat')
            ->where(function ($query) {
                $query->where('statusPinjam', '=', '0')->orWhereNull('statusPinjam');
            })
            ->where('kodeSkpd', $kodeSkpd)
            ->limit(100)
            ->get();
        } else {
            $tags = DB::table('masterSertifikat')
            ->where(function ($query) {
                $query->where('statusPinjam', '=', '0')->orWhereNull('statusPinjam');
            })
            ->where('kodeSkpd', $kodeSkpd)
            ->where(function ($query) use ($term) {
                $query->where('nomorRegister', 'like', "%$term%")
                    ->orWhere('nib', 'like', "%$term%")
                    ->orWhere('nomorSertifikat', 'like', "%$term%")
                    ->orWhere('kodeSkpd', 'like', "%$term%");
            })
            ->limit(5)
            ->get();
        }

        foreach ($tags as $tag) {
            $formatted_tags[] = [
                'nomorRegister' => $tag->nomorRegister,
                'nib' => $tag->nib,
                'nomorSertifikat' => $tag->nomorSertifikat,
                'kodeSkpd' => $tag->kodeSkpd,
                'tanggalSertifikat' => $tag->tanggalSertifikat,
                'luas' => $tag->luas,
                'hak' => $tag->hak,
                'pemegangHak' => $tag->pemegangHak,
                'asalUsul' => $tag->asalUsul,
                'alamat' => $tag->alamat,
                'sertifikatAsli' => $tag->sertifikatAsli == 1 ? 'Ya' : 'Tidak',
                'balikNama' => $tag->balikNama == 1 ? 'Sudah' : 'Belum',
                'penggunaan' => $tag->penggunaan,
                'keterangan' => $tag->keterangan,

            ];
        }

        return \Response::json($formatted_tags);
    }
    public function store(TambahRequest $request)
    {
        $validatedData = $request->validated();

        DB::beginTransaction();
        try {
            DB::table('masterSertifikat')
                ->where([
                    'nomorRegister' => $validatedData['nomorRegister'],
                    'kodeSkpd' => Auth::user()->kd_skpd
                ])
                ->lockForUpdate()
                ->first();

            DB::table('pinjamanSertifikat')
                ->lockForUpdate()
                ->get();



            $nomorUrut = $this->generateNomorUrut();

            DB::table('pinjamanSertifikat')
                ->insert([
                    'kodeSkpd' => Auth::user()->kd_skpd,
                    'nomorSurat' => $validatedData['nomorSurat'],
                    'tanggalPinjam' => $validatedData['tanggalPinjam'],
                    'nomorRegister' => $validatedData['nomorRegister'],
                    'nomorSertifikat' => $validatedData['nomorSertifikat'],
                    'NIB' => $validatedData['nib'],
                    'tanggal' => $validatedData['tanggal'],
                    'pemegangHak' => $validatedData['pemegangHak'],
                    'luas' => $validatedData['luas'],
                    'peruntukan' => $validatedData['peruntukan'],
                    'namaKsbtgn' => $validatedData['namaKsbtgn'],
                    'nipKsbtgn' => $validatedData['nipKsbtgn'],
                    'noTelpKsbtgn' => $validatedData['noTelpKsbtgn'],
                    'file' => 'N/A',
                    'statusPengajuan' => '0',
                    'nomorUrut' => $nomorUrut,
                    'createdDate' => now()->setTimezone('Asia/Jakarta'),
                    'createdUsername' => Auth::user()->name,
                    'updatedDate' => now()->setTimezone('Asia/Jakarta'),
                    'updatedUsername' => Auth::user()->name,
                ]);

            DB::table('masterSertifikat')
                ->where([
                    'nomorRegister' => $validatedData['nomorRegister'],
                    'kodeSkpd' => Auth::user()->kd_skpd
                ])
                ->update([
                    'statusSertifikat' => '1',
                    'statusPinjam' => '1'
                ]);

            $pinjamanSebelumnya = DB::table('pinjamanSertifikat')
                ->where([
                    'nomorRegister' => $request['nomorRegister'],
                    'kodeSkpd' => Auth::user()->kd_skpd,
                ])
                ->where('nomorUrut', '<', $nomorUrut)
                ->orderByDesc('nomorUrut')
                ->first();

            DB::table('pinjamanSertifikat')
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
                ->route('peminjaman.sertifikat.index')
                ->with('message', 'Peminjaman Sertifikat berhasil disimpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('peminjaman.sertifikat.create')
                ->withInput()
                ->with('message', 'Peminjaman Sertifikat gagal disimpan! ' . $e->getMessage());
        }
    }
    public function edit($nomorSurat, $kodeSkpd)
    {
        $nomorSurat = Crypt::decrypt($nomorSurat);
        $kodeSkpd = Crypt::decrypt($kodeSkpd);

        $dataPinjam = DB::table('pinjamanSertifikat')
            ->where(['nomorSurat' => $nomorSurat, 'kodeSkpd' => $kodeSkpd])
            ->first();

        $dataSertifikat = DB::table('masterSertifikat')
            ->where(['nomorRegister' => $dataPinjam->nomorRegister, 'kodeSkpd' => $kodeSkpd])
            ->first();

        return view('kelola_peminjaman.sertifikat.edit', compact('dataPinjam', 'dataSertifikat'));
    }

    public function update(EditRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            DB::table('pinjamanSertifikat')
                ->where([
                    'id' => $id,
                    'nomorSurat' => $request['nomorSurat'],
                    'kodeSkpd' => Auth::user()->kd_skpd
                ])
                ->lockForUpdate()
                ->first();

            DB::table('pinjamanSertifikat')
                ->where([
                    'id' => $id,
                    'nomorSurat' => $request['nomorSurat'],
                    'kodeSkpd' => Auth::user()->kd_skpd
                ])
                ->update([
                    'tanggalPinjam' => $request['tanggalPinjam'],
                    'namaKsbtgn' => $request['namaKsbtgn'],
                    'nipKsbtgn' => $request['nipKsbtgn'],
                    'noTelpKsbtgn' => $request['noTelpKsbtgn'],
                    'peruntukan' => $request['peruntukan'],
                    'updatedDate' => date('Y-m-d H:i:s'),
                    'updatedUsername' => Auth::user()->name,
                ]);

            DB::commit();
            return redirect()
                ->route('peminjaman.sertifikat.index')
                ->with('message', 'Peminjaman Sertifikat berhasil diupdate!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->route(
                    'peminjaman.sertifikat.edit',
                    [
                        'no_surat' => Crypt::encrypt($request['nomorSurat']),
                        'kd_skpd' => Crypt::encrypt($request['kodeSkpd']),
                    ]
                )
                ->withInput()
                ->with('message', 'Peminjaman Sertifikat gagal diupdate!');
        }
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {
           $dataPinjaman = DB::table('pinjamanSertifikat')
            ->where([
                'nomorSurat' => $request->nomorSurat,
                'kodeSkpd' => $request->kodeSkpd
            ])
            ->lockForUpdate()
            ->first();


            DB::table('pinjamanSertifikat')
                ->where([
                    'nomorSurat' => $request->nomorSurat,
                    'kodeSkpd' => $request->kodeSkpd
                ])
                ->delete();


            $pinjamanSebelumnya = DB::table('pinjamanSertifikat')
                ->where([
                    'nomorRegister' => $dataPinjaman->nomorRegister,
                    'kodeSkpd' => $dataPinjaman->kodeSkpd,
                ])
                ->where('nomorUrut', '<', $dataPinjaman->nomorUrut)
                ->orderByDesc('nomorUrut')
                ->first();

            DB::table('pinjamanSertifikat')
                ->where([
                    'nomorSurat' => $request->nomorSurat,
                    'kodeSkpd' => $request->kodeSkpd
                ])
                ->delete();

            $cekSertifikat = DB::table('pinjamanSertifikat')
                ->where([
                    'nomorRegister' => $request->nomorRegister,
                    'kodeSkpd' => $request->kodeSkpd
                ])
                ->count();

            $masterSertifikat = DB::table('masterSertifikat')
                ->where([
                    'nomorRegister' => $request->nomorRegister,
                    'kodeSkpd' => $request->kodeSkpd
                ]);

            if ($cekSertifikat > 0) {
                $masterSertifikat
                    ->update([
                        'statusPinjam' => '0'
                    ]);
            } else {
                $masterSertifikat
                    ->update([
                        'statusPinjam' => '0',
                        'statusSertifikat' => '0'
                    ]);
            }

            DB::table('pinjamanSertifikat')
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
            'dataPeminjaman' => DB::table('pinjamanSertifikat')
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

        $view = view('kelola_peminjaman.sertifikat.cetak')->with($data);

        if ($tipe == 'layar') {
            return $view;
        } else if ($tipe == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOrientation('portrait')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('FormPeminjamanSertifikat.pdf');
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

        DB::beginTransaction();
        try {
            $dataPeminjaman = DB::table('pinjamanSertifikat')
                ->where([
                    'nomorSurat' => $nomorSuratPengajuan,
                    'nomorRegister' => $nomorRegisterPengajuan,
                    'kodeSkpd' => Auth::user()->kd_skpd
                ])
                ->first();

            $file = isset($filePengajuan) ? $this->handleFile($filePengajuan, 'Sertifikat', $request) : null;

            if ($dataPeminjaman->statusPengajuan == '0') {
                DB::table('pinjamanSertifikat')
                    ->where([
                        'nomorSurat' => $nomorSuratPengajuan,
                        'nomorRegister' => $nomorRegisterPengajuan,
                        'kodeSkpd' => Auth::user()->kd_skpd
                    ])
                    ->update([
                        'statusPengajuan' => '1',
                        'file' => $file
                    ]);

                Storage::putFileAs('public/images/Peminjaman/Sertifikat/' . Auth::user()->kd_skpd . '/', $filePengajuan, $file);
            } else {
                DB::table('pinjamanSertifikat')
                    ->where([
                        'nomorSurat' => $nomorSuratPengajuan,
                        'nomorRegister' => $nomorRegisterPengajuan,
                        'kodeSkpd' => Auth::user()->kd_skpd
                    ])
                    ->update([
                        'statusPengajuan' => '0',
                        'file' => ''
                    ]);

                unlink(storage_path('app/public/images/Peminjaman/Sertifikat/' . Auth::user()->kd_skpd . '/' . '/' . $dataPeminjaman->file));
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
