<?php

namespace App\Http\Controllers\KelolaData;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MsAnggaranImport;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class Ms_bank extends Controller
{
    // Tampilkan daftar produk
    public function index()
    {
        return view('kelola_data.ms_bank.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('ms_bank')
            ->select(['kode','nama','bic']);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");

            });
        }

        return DataTables::of($query)
            ->addIndexColumn() // Menambahkan kolom nomor
            ->addColumn('aksi', function ($row) {
                $editUrl = route('kelola_data.ms_bank.edit', Crypt::encrypt($row->kode));
            $deleteUrl = route('kelola_data.ms_bank.destroy', Crypt::encrypt($row->kode));

            $editButton = '<a href="' . $editUrl . '" class="btn btn-warning btn-sm d-inline-block me-1">
                            <i class="fas fa-edit"></i>
                           </a>';
            $deleteButton = '<button class="btn btn-sm btn-danger delete-btn d-inline-block" data-url="' . $deleteUrl . '" data-id="' . $row->kode . '">
                              <i class="fas fa-trash-alt"></i>
                             </button>';

            return '<div class="d-flex">' . $editButton . $deleteButton . '</div>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }





    // Tampilkan form tambah produk
    public function create()
    {


        return view('kelola_data.ms_bank.create');
    }





public function store(Request $request)
{
    // Validasi input
    $validatedData = $request->validate([
        'kode' => 'required|unique:ms_bank,kode',
        'nama' => 'required|string|max:255',
        'bic' => 'nullable',
    ], [
        'kode.required' => 'kode harus diisi.',
        'kode.unique' => 'Kode sudah ada, gunakan kode lain.',
        'nama.required' => 'nama harus diisi.',
    ]);

    // Format nilai anggaran menjadi integer
    $data = [
        'kode' => $validatedData['kode'],
        'nama' => $validatedData['nama'],
        'bic' => $validatedData['bic'],
    ];

    // Insert data ke dalam database
    DB::table('ms_bank')->insert($data);

    // Redirect dengan pesan sukses
    return redirect()->route('kelola_data.ms_bank.index')->with('message', 'Data ms bank berhasil ditambahkan.');
}


    public function edit($kode)
    {
        // Dekripsi ID yang terenkripsi
        $decryptedId = Crypt::decrypt($kode);

        // Ambil data pajak berdasarkan ID menggunakan Query Builder
        $pajak = DB::table('ms_bank')->where('kode', $decryptedId)->first();

        // Cek apakah data ditemukan
        if (!$pajak) {
            return redirect()->route('kelola_data.ms_bank.index')->with('message', 'Data ms bank tidak ditemukan.');
        }

        // Tampilkan view untuk mengedit data
        return view('kelola_data.ms_bank.create', compact('pajak'));
    }


    // Update produk
    public function update(Request $request, $kode)
    {
        $pajak = DB::table('ms_bank')->where('kode', $kode)->first();

        if (!$pajak) {
            return redirect()->route('kelola_data.ms_bank.index')->with('message', 'ms_bank tidak ditemukan.');
        }

        // Validasi input
        $validatedData = $request->validate([
        'kode' => 'required',
        'nama' => 'required|string|max:255',
        'bic' => 'nullable',
        ], [
            'kode.required' => 'kode harus diisi.',
            'nama.required' => 'nama harus diisi.',
        ]);
        // Format angka (menghapus titik dan koma) sebelum disimpan
        $formattedData = [
            'kode' => $validatedData['kode'],
            'nama' => $validatedData['nama'],
            'bic' => $validatedData['bic'],
        ];



        // Update data
        DB::table('ms_bank')->where('kode', $kode)->update($formattedData);

        return redirect()->route('kelola_data.ms_bank.index')->with('message', 'Data ms_bank berhasil diubah.');
    }


    // Hapus produk
    public function destroy($kode)
    {
        try {
            $decryptedId = Crypt::decrypt($kode);
            DB::table('ms_bank')->where('kode', $decryptedId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'ms bank berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus ms bank.'
            ], 500);
        }
    }


}
