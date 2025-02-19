<?php

namespace App\Http\Controllers\KelolaData;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class Subkegiatan extends Controller
{
    // Tampilkan daftar produk
    public function index()
    {
        return view('kelola_data.subkegiatan.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('ms_sub_kegiatan')
            ->select(['id','kd_sub_kegiatan', 'nm_sub_kegiatan']);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('kd_sub_kegiatan', 'like', "%{$search}%")
                  ->orWhere('nm_sub_kegiatan', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn() // Menambahkan kolom nomor
            ->addColumn('aksi', function ($row) {
                $editButton = '<a href="' . route('kelola_data.subkegiatan.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-primary">Edit</a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('kelola_data.subkegiatan.destroy', Crypt::encrypt($row->id)) . '">Hapus</button>';
                return $editButton . ' ' . $deleteButton;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    // Tampilkan form tambah produk
    public function create()
    {
        return view('kelola_data.subkegiatan.create');
    }


    public function getprogram(Request $request)
    {

        $search = $request->q;

        $sertifikat = DB::table('ms_program')
            ->select('kd_program','nm_program')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('kd_program', 'LIKE', "%{$search}%")
                      ->orWhere('nm_program', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $sertifikat->map(function ($item) {
            return [
                'id' => $item->kd_program,
                'text' => implode(' | ', [
                    $item->kd_program,
                    $item->nm_program,

                ]),
                'nm_program' => $item->nm_program
            ];
        });

        return response()->json($data);

    }


    // Simpan produk baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'kd_sub_kegiatan' => 'required',
            'nm_sub_kegiatan' => 'required|string|max:255',
            'kd_program' => 'required',
            'nm_program' => 'required|string|max:255',
        ], [
            'kd_sub_kegiatan.required' => 'Kode sub_kegiatan harus diisi.',
            'nm_sub_kegiatan.required' => 'Nama sub_kegiatan harus diisi.',
            'kd_program.required' => 'Kode sub_kegiatan harus diisi.',
            'nm_program.required' => 'Nama sub_kegiatan harus diisi.',
        ]);

        // Simpan data ke database menggunakan Query Builder
        DB::table('ms_sub_kegiatan')->insert([
            'kd_sub_kegiatan' => $request->kd_sub_kegiatan,
            'nm_sub_kegiatan' => $request->nm_sub_kegiatan,
            'kd_program' => $request->kd_program,
            'nm_program' => $request->nm_program,
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('kelola_data.subkegiatan.index')->with('message', 'Data Subkegiatan berhasil ditambahkan.');
    }


    public function edit($id)
    {
        // Dekripsi ID yang terenkripsi
        $decryptedId = Crypt::decrypt($id);

        // Ambil data pajak berdasarkan ID menggunakan Query Builder
        $pajak = DB::table('ms_sub_kegiatan')->where('id', $decryptedId)->first();

        // Cek apakah data ditemukan
        if (!$pajak) {
            return redirect()->route('kelola_data.subkegiatan.index')->with('message', 'Data subkegiatan tidak ditemukan.');
        }

        // Tampilkan view untuk mengedit data
        return view('kelola_data.subkegiatan.create', compact('pajak'));
    }


    // Update produk
    public function update(Request $request, $id)
    {
        $pajak = DB::table('ms_sub_kegiatan')->where('id', $id)->first();

        if (!$pajak) {
            return redirect()->route('kelola_data.subkegiatan.index')->with('message', 'subkegiatan tidak ditemukan.');
        }

        $request->validate([
            'kd_sub_kegiatan' => 'required',
            'nm_sub_kegiatan' => 'required|string|max:255',
            'kd_program' => 'required',
            'nm_program' => 'required|string|max:255',
        ], [
            'kd_sub_kegiatan.required' => 'Kode sub_kegiatan harus diisi.',
            'nm_sub_kegiatan.required' => 'Nama sub_kegiatan harus diisi.',
            'kd_program.required' => 'Kode sub_kegiatan harus diisi.',
            'nm_program.required' => 'Nama sub_kegiatan harus diisi.',
        ]);



        $data = $request->only(['kd_sub_kegiatan', 'nm_sub_kegiatan','kd_program','nm_program']);

        DB::table('ms_sub_kegiatan')->where('id', $id)->update($data);

        return redirect()->route('kelola_data.subkegiatan.index')->with('message', 'Data subkegiatan berhasil diubah.');
    }

    // Hapus produk
    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            DB::table('ms_sub_kegiatan')->where('id', $decryptedId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'subkegiatan berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus subkegiatan.'
            ], 500);
        }
    }


}
