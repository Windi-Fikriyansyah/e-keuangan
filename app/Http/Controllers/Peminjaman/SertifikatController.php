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

class SertifikatController extends Controller
{
    public function index()
    {
        return view('kelola_peminjaman.sertifikat.index');
    }

    public function load(Request $request)
    {
        $pageNumber = ($request->start / $request->length) + 1;
        $pageLength = $request->length;
        $skip       = ($pageNumber - 1) * $pageLength;

        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        $query = DB::table('pinjamanSertifikat as a')
            ->select('a.nomorSurat', 'a.nomorRegister', 'a.nomorSertifikat', 'a.NIB', 'a.kodeSkpd', 'b.namaSkpd')
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
                $btn = '<a href="' . route("peminjaman.sertifikat.edit", ['no_surat' => Crypt::encrypt($row->nomorSurat), 'kd_skpd' => Crypt::encrypt($row->kodeSkpd)]) . '" class="btn btn-md btn-warning" style="margin-right:4px">Edit</a>';
                $btn .= '<a onclick="hapus(\'' . $row->nomorSurat . '\',\'' . $row->nomorRegister . '\',\'' . $row->kodeSkpd . '\')" class="btn btn-md btn-danger">Delete</a>';
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

                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $nomorUrut = $this->generateNomorUrut();
                    $fileName = 'sertifikat_' . $nomorUrut . '_' . Auth::user()->kd_skpd . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs($fileName, 'public');
                    $validatedData['nomorUrut'] = $nomorUrut;
                } else {
                    throw new \Exception('File tidak ditemukan');
                }

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
                    'file' => $filePath,
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

        $dataPinjam = DB::table('pinjamanBpkb')
            ->where(['nomorSurat' => $nomorSurat, 'kodeSkpd' => $kodeSkpd])
            ->first();

        $dataBpkb = DB::table('masterSertifikat')
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

            $masterSertifikat = DB::table('masterSertifikat')
                ->where([
                    'nomorRegister' => $request->nomorRegister,
                    'kodeSkpd' => $request->kodeSkpd
                ]);

            if ($cekBpkb > 0) {
                $masterSertifikat
                    ->update([
                        'statusPinjam' => '0'
                    ]);
            } else {
                $masterSertifikat
                    ->update([
                        'statusPinjam' => '0',
                        'statusBpkb' => '0'
                    ]);
            }

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
}
