<?php

namespace App\Http\Controllers\KelolaData;
namespace App\Http\Controllers\KelolaData;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class PajakController extends Controller
{
    // Tampilkan daftar produk
    public function index()
    {
        return view('kelola_data.pajak.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('trd_pajak')
            ->select(['id', 'kd_pajak', 'nm_pajak']);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('kd_pajak', 'like', "%{$search}%")
                  ->orWhere('nm_pajak', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn() // Menambahkan kolom nomor
            ->addColumn('aksi', function ($row) {
                $editButton = '<a href="' . route('kelola_data.pajak.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-primary">Edit</a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('kelola_data.pajak.destroy', Crypt::encrypt($row->id)) . '">Hapus</button>';
                return $editButton . ' ' . $deleteButton;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    // Tampilkan form tambah produk
    public function create()
    {
        return view('kelola_data.pajak.create');
    }

    // Simpan produk baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'kd_pajak' => 'required',
            'nm_pajak' => 'required|string|max:255',
        ], [
            'kd_pajak.required' => 'Kode Pajak harus diisi.',
            'nm_pajak.required' => 'Nama Pajak harus diisi.',
        ]);

        // Simpan data ke database menggunakan Query Builder
        DB::table('trd_pajak')->insert([
            'kd_pajak' => $request->kd_pajak,
            'nm_pajak' => $request->nm_pajak,
            'created_at' => now(), // Opsional jika ada kolom timestamp
            'updated_at' => now(), // Opsional jika ada kolom timestamp
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('kelola_data.pajak.index')->with('message', 'Data Pajak berhasil ditambahkan.');
    }


    public function edit($id)
    {
        // Dekripsi ID yang terenkripsi
        $decryptedId = Crypt::decrypt($id);

        // Ambil data pajak berdasarkan ID menggunakan Query Builder
        $pajak = DB::table('trd_pajak')->where('id', $decryptedId)->first();

        // Cek apakah data ditemukan
        if (!$pajak) {
            return redirect()->route('kelola_data.pajak.index')->with('message', 'Data Pajak tidak ditemukan.');
        }

        // Tampilkan view untuk mengedit data
        return view('kelola_data.pajak.create', compact('pajak'));
    }


    // Update produk
    public function update(Request $request, $id)
    {
        $pajak = DB::table('trd_pajak')->where('id', $id)->first();

        if (!$pajak) {
            return redirect()->route('kelola_data.pajak.index')->with('message', 'Pajak tidak ditemukan.');
        }

        $request->validate([
            'kd_pajak' => 'required',
            'nm_pajak' => 'required|string|max:255',
        ], [
            'kd_pajak.required' => 'Kode Pajak harus diisi.',
            'nm_pajak.required' => 'Nama Pajak harus diisi.',
        ]);

        $data = $request->only(['kd_pajak', 'nm_pajak']);

        DB::table('trd_pajak')->where('id', $id)->update($data);

        return redirect()->route('kelola_data.pajak.index')->with('message', 'Data Pajak berhasil diubah.');
    }

    // Hapus produk
    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            DB::table('trd_pajak')->where('id', $decryptedId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'pajak berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pajak.'
            ], 500);
        }
    }

}
