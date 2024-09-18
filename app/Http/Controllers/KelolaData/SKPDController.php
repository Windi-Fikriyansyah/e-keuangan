<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skpd;
use Illuminate\Support\Facades\Crypt;

use Yajra\DataTables\Facades\DataTables;

class SKPDController extends Controller
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
        return view('kelola_data.skpd.index');
    }

    public function load(Request $request)
    {
        if ($request->ajax()) {
            $data = Skpd::select('id','kodeSkpd', 'namaSkpd');
            $search = $request->search;

            if ($search) {
                $data = $data->where(function ($query) use ($search) {
                    $query->where('kodeSkpd', 'like', "%" . $search . "%")
                          ->orWhere('namaSkpd', 'like', "%" . $search . "%");
                });
            }

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {


                $btn = '<a href="' . route("kelola_data.skpd.edit", Crypt::encrypt($row->kodeSkpd)) . '" class="btn btn-md btn-warning" style="margin-right:4px">Edit</a>';
                $btn .= '<a href="javascript:void(0)" onclick="hapus(\'' . $row->id . '\')" class="btn btn-md btn-danger">Delete</a>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
        }
    }

    public function create()
    {
        return view('kelola_data.skpd.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'kodeSKPD' => 'required|string|max:255',
            'namaSKPD' => 'required|string|max:255',
        ]);


        Skpd::create([
            'kodeSkpd' => $request->kodeSKPD,
            'namaSkpd' => $request->namaSKPD,
        ]);

        return redirect()->route('kelola_data.skpd.index')->with('message', 'Data berhasil ditambahkan.');
    }

    public function edit(string $kodeSkpd)
    {

        $kodeSkpd = Crypt::decrypt($kodeSkpd);
        $skpd = skpd::where('kodeSkpd', $kodeSkpd)->firstOrFail();

        return view('kelola_data.skpd.edit', compact('skpd'));
    }

    public function update(Request $request, $kodeSkpd)
    {
        $request->validate([
            'kodeSkpd' => 'required|string|max:255',
            'namaSkpd' => 'required|string|max:255',
        ]);

        $kodeSkpd = Crypt::decrypt($kodeSkpd);
        $skpd = skpd::where('kodeSkpd', $kodeSkpd)->firstOrFail();
        $skpd->update([

            'kodeSkpd' => $request->kodeSkpd,
            'namaSkpd' => $request->namaSkpd,
        ]);

        return redirect()->route('kelola_data.skpd.index')->with('message', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {

            $item = skpd::findOrFail($id);
            $item->delete();

            return response()->json(['success' => true, 'message' => 'Item deleted successfully.']);
        } catch (\Exception $e) {

            \Log::error('Delete error: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to delete item.']);
        }
    }
}
