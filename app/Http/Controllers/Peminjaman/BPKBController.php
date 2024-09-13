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
use Yajra\DataTables\Facades\DataTables;

class BPKBController extends Controller
{
    public function index()
    {
        return view('kelola_peminjaman.bpkb.index');
    }

    public function load(Request $request)
    {
        // Page Length
        $pageNumber = ($request->start / $request->length) + 1;
        $pageLength = $request->length;
        $skip       = ($pageNumber - 1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        // get data from products table
        $query = DB::table('pinjamanBpkb as a')
            ->select('a.nomorSurat', 'a.nomorRegister', 'a.nomorBpkb', 'a.nomorPolisi', 'a.kodeSkpd', 'b.namaSkpd')
            ->leftJoin('masterSkpd as b', 'a.kodeSkpd', '=', 'b.kodeSkpd');

        // Search
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
                $btn = '<a href="' . route("peminjaman.bpkb.edit", ['no_surat' => Crypt::encrypt($row->nomorSurat), 'kd_skpd' => Crypt::encrypt($row->kodeSkpd)]) . '" class="btn btn-md btn-warning" style="margin-right:4px">Edit</a>';
                $btn .= '<a onclick="hapus(\'' . $row->nomorSurat . '\',\'' . $row->nomorRegister . '\',\'' . $row->kodeSkpd . '\')" class="btn btn-md btn-danger">Delete</a>';
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

        $formatted_tags = [];

        if (empty($term)) {
            $tags = DB::table('masterBpkb')
                ->where(function ($query) {
                    $query->where('statusPinjam', '=', '0')->orWhereNull('statusPinjam');
                })->limit(100)->get();
        } else {
            $tags = DB::table('masterBpkb')
                ->where(function ($query) {
                    $query->where('statusPinjam', '=', '0')->orWhereNull('statusPinjam');
                })
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

            DB::table('pinjamanBpkb')
                ->insert([
                    'kodeSkpd' => Auth::user()->kd_skpd,
                    'nomorUrut' => $nomorBaru->nomor,
                    'nomorSurat' => '000.2.3.2/' . $nomorBaru->nomor . '/BPKAD-Aset',
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
