<?php

namespace App\Http\Controllers\KelolaData;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class Ms_program extends Controller
{
    // Tampilkan daftar produk
    public function index()
    {
        return view('kelola_data.program.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('ms_program')
            ->select(['id', 'kd_program', 'nm_program']);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('kd_program', 'like', "%{$search}%")
                  ->orWhere('nm_program', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn() // Menambahkan kolom nomor
            ->addColumn('aksi', function ($row) {
                $editButton = '<a href="' . route('kelola_data.program.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-primary">Edit</a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('kelola_data.program.destroy', Crypt::encrypt($row->id)) . '">Hapus</button>';
                return $editButton . ' ' . $deleteButton;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    // Tampilkan form tambah produk
    public function create()
    {
        return view('kelola_data.program.create');
    }

    // Simpan produk baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'kd_program' => 'required',
            'nm_program' => 'required|string|max:255',
        ], [
            'kd_program.required' => 'Kode program harus diisi.',
            'nm_program.required' => 'Nama program harus diisi.',
        ]);

        // Simpan data ke database menggunakan Query Builder
        DB::table('ms_program')->insert([
            'kd_program' => $request->kd_program,
            'nm_program' => $request->nm_program,
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('kelola_data.program.index')->with('message', 'Data program berhasil ditambahkan.');
    }


    public function edit($id)
    {
        // Dekripsi ID yang terenkripsi
        $decryptedId = Crypt::decrypt($id);

        // Ambil data pajak berdasarkan ID menggunakan Query Builder
        $pajak = DB::table('ms_program')->where('id', $decryptedId)->first();

        // Cek apakah data ditemukan
        if (!$pajak) {
            return redirect()->route('kelola_data.program.index')->with('message', 'Data program tidak ditemukan.');
        }

        // Tampilkan view untuk mengedit data
        return view('kelola_data.program.create', compact('pajak'));
    }


    // Update produk
    public function update(Request $request, $id)
    {
        $pajak = DB::table('ms_program')->where('id', $id)->first();

        if (!$pajak) {
            return redirect()->route('kelola_data.program.index')->with('message', 'program tidak ditemukan.');
        }

        $request->validate([
            'kd_program' => 'required',
            'nm_program' => 'required|string|max:255',
        ], [
            'kd_program.required' => 'Kode program harus diisi.',
            'nm_program.required' => 'Nama program harus diisi.',
        ]);

        $data = $request->only(['kd_program', 'nm_program']);

        DB::table('ms_program')->where('id', $id)->update($data);

        return redirect()->route('kelola_data.program.index')->with('message', 'Data program berhasil diubah.');
    }

    // Hapus produk
    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            DB::table('ms_program')->where('id', $decryptedId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'program berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus program.'
            ], 500);
        }
    }

}
