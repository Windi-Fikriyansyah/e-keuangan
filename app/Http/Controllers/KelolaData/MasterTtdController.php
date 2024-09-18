<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterTtd;
use App\Models\Skpd;
use Illuminate\Support\Facades\Crypt;

use Yajra\DataTables\Facades\DataTables;

class MasterTtdController extends Controller
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
        return view('kelola_data.master_ttd.index');
    }

    public function create()
    {
        $masterskpd = Skpd::all();
        return view('kelola_data.master_ttd.create', compact('masterskpd'));
    }
    public function load(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterTtd::select('id', 'nip', 'nama', 'jabatan', 'pangkat', 'kodeSkpd');

            $search = $request->search;

            if ($search) {
                $data = $data->where(function ($query) use ($search) {
                    $query->where('nip', 'like', "%" . $search . "%")
                          ->orWhere('nama', 'like', "%" . $search . "%")
                          ->orWhere('jabatan', 'like', "%" . $search . "%")
                          ->orWhere('pangkat', 'like', "%" . $search . "%")
                          ->orWhere('kodeSkpd', 'like', "%" . $search . "%");
                });
            }

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {


                $btn = '<a href="' . route("kelola_data.master_ttd.edit", Crypt::encrypt($row->id)) . '" class="btn btn-md btn-warning" style="margin-right:4px">Edit</a>';
                $btn .= '<a href="javascript:void(0)" onclick="hapus(\'' . $row->id . '\')" class="btn btn-md btn-danger">Delete</a>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
            }


    }

    public function store(Request $request)
    {

        $request->validate([
            'nip' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'pangkat' => 'required|string|max:255',
        ]);


        MasterTtd::create([
            'nip' => $request->nip,
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'pangkat' => $request->pangkat,
            'kodeSkpd' => $request->kodeSkpd,
        ]);

        return redirect()->route('kelola_data.master_ttd.index')->with('message', 'Data berhasil ditambahkan.');
    }

    public function edit(string $id)
    {

        $id = Crypt::decrypt($id);
        $MasterTtd = MasterTtd::find($id);
        $masterskpd = Skpd::all();

        return view('kelola_data.master_ttd.edit', compact('MasterTtd','masterskpd'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nip' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'pangkat' => 'required|string|max:255',
        ]);

        $id = Crypt::decrypt($id);
        $MasterTtd = MasterTtd::findOrFail($id);
        $MasterTtd->update([

            'nip' => $request->nip,
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'pangkat' => $request->pangkat,
            'kodeSkpd' => $request->kodeSkpd,
        ]);

        return redirect()->route('kelola_data.master_ttd.index')->with('message', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {

            $item = MasterTtd::findOrFail($id);
            $item->delete();

            return response()->json(['success' => true, 'message' => 'Item deleted successfully.']);
        } catch (\Exception $e) {

            \Log::error('Delete error: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to delete item.']);
        }
    }

}
