<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterSertifikat;
use App\Models\Skpd;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Http\Requests\KelolaData\Sertifikat\EditRequest;
use App\Http\Requests\KelolaData\Sertifikat\TambahRequest;
use App\Models\AsalUsul;
use Yajra\DataTables\Facades\DataTables;

class SertifikatController extends Controller
{
    public function __construct()
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('kelola_data.sertifikat.index');
    }

    public function create()
    {
        $lastNumber = DB::table('masterSertifikat')->max('nomorRegister');
        $newNumber = $lastNumber ? intval($lastNumber) + 1 : 1;
        $formattedNumber = str_pad($newNumber, 6, '0', STR_PAD_LEFT);
        $daftarSkpd = Skpd::all();
        $daftarasalUsul = asalUsul::all();
        return view('kelola_data.sertifikat.create', compact('daftarSkpd','formattedNumber','daftarasalUsul'));
    }
    public function load(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterSertifikat::select('id','nomorRegister','kodeSkpd', 'nib','nomorSertifikat','tanggalSertifikat','luas','hak', 'createdDate','createdUsername','updatedDate','updatedUsername');
            $search = $request->search;

            if ($search) {
                $data = $data->where(function ($query) use ($search) {
                    $query->where('nomorRegister', 'like', "%" . $search . "%")
                          ->orWhere('nib', 'like', "%" . $search . "%")
                          ->orWhere('nomorSertifikat', 'like', "%" . $search . "%");
                });
            }

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {


                $btn = '<a href="' . route("kelola_data.sertifikat.edit", ['no_register' => Crypt::encrypt($row->nomorRegister), 'kd_skpd' => Crypt::encrypt($row->kodeSkpd)]) . '" class="btn btn-md btn-warning" style="margin-right:4px">Edit</a>';

                if ($row->statusSertifikat == '0' && $row->statusPinjam == '0') {
                    $btn .= '<a onclick="hapus(\'' . $row->nomorRegister . '\',\'' . $row->kodeSkpd . '\')" class="btn btn-md btn-danger">Delete</a>';
                }
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
            }


    }

    public function store(TambahRequest $request)
    {
        $request = $request->validated();

        DB::beginTransaction();
        try {
            DB::table('masterSertifikat')->lockForUpdate()->get();



            DB::table('masterSertifikat')
                ->insert([
                    'kodeSkpd' => $request['kodeSkpd'],
                    'nomorRegister' => $request['nomorRegister'],
                    'nib' => $request['nib'],
                    'nomorSertifikat' => $request['nomorSertifikat'],
                    'tanggalSertifikat' => $request['tanggalSertifikat'],
                    'luas' => $request['luas'],
                    'hak' => $request['hak'],
                    'pemegangHak' => $request['pemegangHak'],
                    'asalUsul' => $request['asalUsul'],
                    'alamat' => $request['alamat'],
                    'sertifikatAsli' => $request['sertifikatAsli'],
                    'balikNama' => $request['balikNama'],
                    'penggunaan' => $request['penggunaan'],
                    'keterangan' => $request['keterangan'],
                    'createdDate' => date('Y-m-d H:i:s'),
                    'createdUsername' => Auth::user()->name,
                    'updatedDate' => date('Y-m-d H:i:s'),
                    'updatedUsername' => Auth::user()->name,
                    'statusSertifikat' => '0',
                    'statusPinjam' => '0',
                ]);

            DB::commit();
            return redirect()
                ->route('kelola_data.sertifikat.index')
                ->with('message', 'Data Sertifikat berhasil disimpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('kelola_data.sertifikat.create')
                ->withInput()
                ->with('message', 'Data Sertifikat gagal disimpan!' . $e->getMessage());
        }
    }


    public function edit($nomorRegister, $kodeSkpd)
    {
        $nomorRegister = Crypt::decrypt($nomorRegister);
        $kodeSkpd = Crypt::decrypt($kodeSkpd);

        $daftarSkpd = DB::table('masterSkpd')
            ->get();

        $dataSertifikat = DB::table('masterSertifikat')
            ->where(['nomorRegister' => $nomorRegister, 'kodeSkpd' => $kodeSkpd])
            ->first();
        $daftarasalUsul = asalUsul::all();
        return view('kelola_data.sertifikat.edit', compact('daftarSkpd', 'dataSertifikat','daftarasalUsul'));
    }

    public function update(EditRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            DB::table('masterSertifikat')
                ->where([
                    'id' => $id,
                    'nomorRegister' => $request['nomorRegister'],
                    'kodeSkpd' => $request['kodeSkpd']
                ])
                ->lockForUpdate()
                ->first();

            DB::table('masterSertifikat')
                ->where([
                    'id' => $id,
                    'nomorRegister' => $request['nomorRegister'],
                    'kodeSkpd' => $request['kodeSkpd']
                ])
                ->update([
                    'nib' => $request['nib'],
                    'nomorSertifikat' => $request['nomorSertifikat'],
                    'tanggalSertifikat' => $request['tanggalSertifikat'],
                    'luas' => $request['luas'],
                    'hak' => $request['hak'],
                    'pemegangHak' => $request['pemegangHak'],
                    'asalUsul' => $request['asalUsul'],
                    'alamat' => $request['alamat'],
                    'sertifikatAsli' => $request['sertifikatAsli'],
                    'balikNama' => $request['balikNama'],
                    'penggunaan' => $request['penggunaan'],
                    'keterangan' => $request['keterangan'],
                    'updatedDate' => date('Y-m-d H:i:s'),
                    'updatedUsername' => Auth::user()->name,
                ]);

            DB::commit();
            return redirect()
                ->route('kelola_data.sertifikat.index')
                ->with('message', 'Data Sertifikat berhasil diupdate!');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()
                ->route(
                    'kelola_data.sertifikat.edit',
                    [
                        'no_register' => Crypt::encrypt($request['nomorRegister']),
                        'kd_skpd' => Crypt::encrypt($request['kodeSkpd']),
                    ]
                )
                ->withInput()
                ->with('message', 'Data Sertifikat gagal diupdate!');
        }
    }

    public function destroy($id)
    {
        try {

            $item = MasterSertifikat::findOrFail($id);
            $item->delete();

            return response()->json(['success' => true, 'message' => 'Item deleted successfully.']);
        } catch (\Exception $e) {

            \Log::error('Delete error: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to delete item.']);
        }
    }

}
