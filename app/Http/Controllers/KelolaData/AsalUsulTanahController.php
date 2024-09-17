<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AsalUsul;
use Illuminate\Support\Facades\Crypt;

use Yajra\DataTables\Facades\DataTables;

class AsalUsulTanahController extends Controller
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
        return view('kelola_data.asalUsul.index');
    }

    public function load(Request $request)
    {
        if ($request->ajax()) {
            $data = AsalUsul::select('id','nama');
            $search = $request->search;

            if ($search) {
                $data = $data->where(function ($query) use ($search) {
                    $query->where('nama', 'like', "%" . $search . "%");
                });
            }

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {


                $btn = '<a href="' . route("kelola_data.asalUsul.edit", Crypt::encrypt($row->id)) . '" class="btn btn-md btn-warning" style="margin-right:4px">Edit</a>';
                $btn .= '<a href="javascript:void(0)" onclick="hapus(\'' . $row->id . '\')" class="btn btn-md btn-danger">Delete</a>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
        }
    }

    public function create()
    {
        return view('kelola_data.asalUsul.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'nama' => 'required|string|max:255',
        ]);


        AsalUsul::create([
            'nama' => $request->nama,
        ]);

        return redirect()->route('kelola_data.asalUsul.index')->with('message', 'Data berhasil ditambahkan.');
    }

    public function edit(string $id)
    {

        $id = Crypt::decrypt($id);
        $asalUsul = AsalUsul::where('id', $id)->firstOrFail();

        return view('kelola_data.asalUsul.edit', compact('asalUsul'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $id = Crypt::decrypt($id);
        $asalUsul = asalUsul::where('id', $id)->firstOrFail();
        $asalUsul->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('kelola_data.asalUsul.index')->with('message', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {

            $item = asalUsul::findOrFail($id);
            $item->delete();

            return response()->json(['success' => true, 'message' => 'Item deleted successfully.']);
        } catch (\Exception $e) {

            \Log::error('Delete error: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to delete item.']);
        }
    }
}
