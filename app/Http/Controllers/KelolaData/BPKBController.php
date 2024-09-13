<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use App\Http\Requests\KelolaData\BPKB\EditRequest;
use App\Http\Requests\KelolaData\BPKB\TambahRequest;
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
        return view('kelola_data.bpkb.index');
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
        $query = DB::table('masterBpkb as a')
            ->select('a.nomorRegister', 'a.nomorBpkb', 'a.nomorPolisi', 'a.kodeSkpd', 'b.namaSkpd', 'a.statusBpkb')
            ->leftJoin('masterSkpd as b', 'a.kodeSkpd', '=', 'b.kodeSkpd');

        // Search
        $search = $request->search;
        $query = $query->where(function ($query) use ($search) {
            $query->orWhere('nomorRegister', 'like', "%" . $search . "%");
        });

        $orderByName = 'nomorRegister';
        switch ($orderColumnIndex) {
            case '0':
                $orderByName = 'nomorRegister';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $users = $query->skip($skip)->take($pageLength)->get();

        return DataTables::of($users)
            ->addColumn('aksi', function ($row) {
                $btn = '<a href="' . route("kelola_data.bpkb.edit", ['no_register' => Crypt::encrypt($row->nomorRegister), 'kd_skpd' => Crypt::encrypt($row->kodeSkpd)]) . '" class="btn btn-md btn-warning" style="margin-right:4px">Edit</a>';

                if ($row->statusBpkb == '0') {
                    $btn .= '<a onclick="hapus(\'' . $row->nomorRegister . '\',\'' . $row->kodeSkpd . '\')" class="btn btn-md btn-danger">Delete</a>';
                }
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $daftarSkpd = DB::table('masterSkpd')
            ->get();

        return view('kelola_data.bpkb.create', compact('daftarSkpd'));
    }

    public function store(TambahRequest $request)
    {
        $request = $request->validated();

        DB::beginTransaction();
        try {
            DB::table('masterBpkb')->lockForUpdate()->get();

            $nomorBaru = DB::table('masterBpkb')
                ->selectRaw("ISNULL(MAX(nomorRegister),0)+1 as nomor")
                ->first();

            DB::table('masterBpkb')
                ->insert([
                    'kodeSkpd' => $request['kodeSkpd'],
                    'nomorRegister' => $nomorBaru->nomor,
                    'nomorBpkb' => $request['nomorBpkb'],
                    'nomorPolisi' => $request['nomorPolisi'],
                    'namaPemilik' => $request['namaPemilik'],
                    'jenis' => $request['jenis'],
                    'merk' => $request['merk'],
                    'tipe' => $request['tipe'],
                    'model' => $request['model'],
                    'tahunPembuatan' => $request['tahunPembuatan'],
                    'tahunPerakitan' => $request['tahunPerakitan'],
                    'isiSilinder' => $request['isiSilinder'],
                    'warna' => $request['warna'],
                    'alamat' => $request['alamat'],
                    'nomorRangka' => $request['nomorRangka'],
                    'nomorMesin' => $request['nomorMesin'],
                    'keterangan' => $request['keterangan'],
                    'nomorPolisiLama' => $request['nomorPolisiLama'],
                    'nomorBpkbLama' => $request['nomorBpkbLama'],
                    'createdDate' => date('Y-m-d H:i:s'),
                    'createdUsername' => Auth::user()->name,
                    'updatedDate' => date('Y-m-d H:i:s'),
                    'updatedUsername' => Auth::user()->name,
                    'statusBpkb' => '0',
                    'statusPinjam' => '0',
                ]);

            DB::commit();
            return redirect()
                ->route('kelola_data.bpkb.index')
                ->with('message', 'Data BPKB berhasil disimpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('kelola_data.bpkb.create')
                ->withInput()
                ->with('message', 'Data BPKB gagal disimpan!' . $e->getMessage());
        }
    }

    public function edit($nomorRegister, $kodeSkpd)
    {
        $nomorRegister = Crypt::decrypt($nomorRegister);
        $kodeSkpd = Crypt::decrypt($kodeSkpd);

        $daftarSkpd = DB::table('masterSkpd')
            ->get();

        $dataBpkb = DB::table('masterBpkb')
            ->where(['nomorRegister' => $nomorRegister, 'kodeSkpd' => $kodeSkpd])
            ->first();

        return view('kelola_data.bpkb.edit', compact('daftarSkpd', 'dataBpkb'));
    }

    public function update(EditRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            DB::table('masterBpkb')
                ->where([
                    'id' => $id,
                    'nomorRegister' => $request['nomorRegister'],
                    'kodeSkpd' => $request['kodeSkpd']
                ])
                ->lockForUpdate()
                ->first();

            DB::table('masterBpkb')
                ->where([
                    'id' => $id,
                    'nomorRegister' => $request['nomorRegister'],
                    'kodeSkpd' => $request['kodeSkpd']
                ])
                ->update([
                    'nomorBpkb' => $request['nomorBpkb'],
                    'nomorPolisi' => $request['nomorPolisi'],
                    'namaPemilik' => $request['namaPemilik'],
                    'jenis' => $request['jenis'],
                    'merk' => $request['merk'],
                    'tipe' => $request['tipe'],
                    'model' => $request['model'],
                    'tahunPembuatan' => $request['tahunPembuatan'],
                    'tahunPerakitan' => $request['tahunPerakitan'],
                    'isiSilinder' => $request['isiSilinder'],
                    'warna' => $request['warna'],
                    'alamat' => $request['alamat'],
                    'nomorRangka' => $request['nomorRangka'],
                    'nomorMesin' => $request['nomorMesin'],
                    'keterangan' => $request['keterangan'],
                    'nomorPolisiLama' => $request['nomorPolisiLama'],
                    'nomorBpkbLama' => $request['nomorBpkbLama'],
                    'updatedDate' => date('Y-m-d H:i:s'),
                    'updatedUsername' => Auth::user()->name,
                ]);

            DB::commit();
            return redirect()
                ->route('kelola_data.bpkb.index')
                ->with('message', 'Data BPKB berhasil diupdate!');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()
                ->route(
                    'kelola_data.bpkb.edit',
                    [
                        'no_register' => Crypt::encrypt($request['nomorRegister']),
                        'kd_skpd' => Crypt::encrypt($request['kodeSkpd']),
                    ]
                )
                ->withInput()
                ->with('message', 'Data BPKB gagal diupdate!');
        }
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {
            DB::table('masterBpkb')
                ->where([
                    'nomorRegister' => $request['nomorRegister'],
                    'kodeSkpd' => $request['kodeSkpd']
                ])
                ->lockForUpdate()
                ->first();

            DB::table('masterBpkb')
                ->where([
                    'nomorRegister' => $request->nomorRegister,
                    'kodeSkpd' => $request->kodeSkpd
                ])
                ->delete();

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
