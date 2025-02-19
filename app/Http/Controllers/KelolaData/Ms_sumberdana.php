<?php

namespace App\Http\Controllers\KelolaData;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\mssumberdanaImport;
use Illuminate\Support\Facades\Response;

class Ms_sumberdana extends Controller
{
    // Tampilkan daftar produk
    public function index()
    {
        return view('kelola_data.mssumberdana.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('ms_sumberdana')
            ->select(['id','kd_dana', 'nm_dana','anggaran_tahun','anggaran_tw1','anggaran_tw2','anggaran_tw3','anggaran_tw4']);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('kd_dana', 'like', "%{$search}%")
                  ->orWhere('nm_dana', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn() // Menambahkan kolom nomor
            ->addColumn('aksi', function ($row) {
                $editButton = '<a href="' . route('kelola_data.mssumberdana.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-primary">Edit</a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('kelola_data.mssumberdana.destroy', Crypt::encrypt($row->id)) . '">Hapus</button>';
                return $editButton . ' ' . $deleteButton;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function downloadFormat()
{
    $filePath = public_path('template/format_ms_sumberdana.xlsx'); // Pastikan file template ada di folder public/template/

    if (!file_exists($filePath)) {
        return redirect()->back()->with('message', 'File format tidak ditemukan!');
    }

    return Response::download($filePath, 'Format_Ms_sumberdana.xlsx');
}

public function upload(Request $request) {
    $request->validate([
        'file' => 'required|mimes:xlsx,xls|max:2048'
    ]);

    if (!$request->hasFile('file')) {
        return response()->json([
            'status' => 'error',
            'message' => 'Tidak ada file yang diunggah.'
        ], 400);
    }

    DB::beginTransaction();

    try {
        Excel::import(new mssumberdanaImport, $request->file('file'));

        DB::commit();
        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diupload!'
        ]);
    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        DB::rollBack();
        $failures = $e->failures();
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengupload data: ' . json_encode($failures)
        ], 400);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengupload data: ' . $e->getMessage()
        ], 400);
    }
}
    // Tampilkan form tambah produk
    public function create()
    {
        return view('kelola_data.mssumberdana.create');
    }



public function store(Request $request)
{
    // Validasi input
    $validatedData = $request->validate([
        'kd_dana' => 'required',
        'nm_dana' => 'required|string|max:255',
        'anggaran_tahun' => 'nullable',
        'anggaran_tw1' => 'nullable',
        'anggaran_tw2' => 'nullable',
        'anggaran_tw3' => 'nullable',
        'anggaran_tw4' => 'nullable',
        'rek1' => 'nullable',
        'rek2' => 'nullable',
        'rek3' => 'nullable',
        'rek4' => 'nullable',
        'rek5' => 'nullable',
        'rek6' => 'nullable',
        'rek7' => 'nullable',
        'rek8' => 'nullable',
        'rek9' => 'nullable',
        'rek10' => 'nullable',
        'rek11' => 'nullable',
        'rek12' => 'nullable',

    ], [
        'kd_dana.required' => 'Kode rekening harus diisi.',
        'nm_dana.required' => 'Nama rekening harus diisi.',
    ]);

    // Format nilai anggaran menjadi integer
    $data = [
        'kd_dana' => $validatedData['kd_dana'],
        'nm_dana' => $validatedData['nm_dana'],
        'anggaran_tahun' => isset($validatedData['anggaran_tahun']) ? (int) str_replace(['.', ','], '', $validatedData['anggaran_tahun']) : 0,
        'anggaran_tw1' => isset($validatedData['anggaran_tw1']) ? (int) str_replace(['.', ','], '', $validatedData['anggaran_tw1']) : 0,
        'anggaran_tw2' => isset($validatedData['anggaran_tw2']) ? (int) str_replace(['.', ','], '', $validatedData['anggaran_tw2']) : 0,
        'anggaran_tw3' => isset($validatedData['anggaran_tw3']) ? (int) str_replace(['.', ','], '', $validatedData['anggaran_tw3']) : 0,
        'anggaran_tw4' => isset($validatedData['anggaran_tw4']) ? (int) str_replace(['.', ','], '', $validatedData['anggaran_tw4']) : 0,
        'rek1' => isset($validatedData['rek1']) ? (int) str_replace(['.', ','], '', $validatedData['rek1']) : 0,
        'rek2' => isset($validatedData['rek2']) ? (int) str_replace(['.', ','], '', $validatedData['rek2']) : 0,
        'rek3' => isset($validatedData['rek3']) ? (int) str_replace(['.', ','], '', $validatedData['rek3']) : 0,
        'rek4' => isset($validatedData['rek4']) ? (int) str_replace(['.', ','], '', $validatedData['rek4']) : 0,
        'rek5' => isset($validatedData['rek5']) ? (int) str_replace(['.', ','], '', $validatedData['rek5']) : 0,
        'rek6' => isset($validatedData['rek6']) ? (int) str_replace(['.', ','], '', $validatedData['rek6']) : 0,
        'rek7' => isset($validatedData['rek7']) ? (int) str_replace(['.', ','], '', $validatedData['rek7']) : 0,
        'rek8' => isset($validatedData['rek8']) ? (int) str_replace(['.', ','], '', $validatedData['rek8']) : 0,
        'rek9' => isset($validatedData['rek9']) ? (int) str_replace(['.', ','], '', $validatedData['rek9']) : 0,
        'rek10' => isset($validatedData['rek10']) ? (int) str_replace(['.', ','], '', $validatedData['rek10']) : 0,
        'rek11' => isset($validatedData['rek11']) ? (int) str_replace(['.', ','], '', $validatedData['rek11']) : 0,
        'rek12' => isset($validatedData['rek12']) ? (int) str_replace(['.', ','], '', $validatedData['rek12']) : 0,

    ];

    // Insert data ke dalam database
    DB::table('ms_sumberdana')->insert($data);

    // Redirect dengan pesan sukses
    return redirect()->route('kelola_data.mssumberdana.index')->with('message', 'Data Anggaran berhasil ditambahkan.');
}


    public function edit($id)
    {
        // Dekripsi ID yang terenkripsi
        $decryptedId = Crypt::decrypt($id);

        // Ambil data pajak berdasarkan ID menggunakan Query Builder
        $pajak = DB::table('ms_sumberdana')->where('id', $decryptedId)->first();

        // Cek apakah data ditemukan
        if (!$pajak) {
            return redirect()->route('kelola_data.mssumberdana.index')->with('message', 'Data mssumberdana tidak ditemukan.');
        }

        // Tampilkan view untuk mengedit data
        return view('kelola_data.mssumberdana.create', compact('pajak'));
    }


    // Update produk
    public function update(Request $request, $id)
    {
        $pajak = DB::table('ms_sumberdana')->where('id', $id)->first();

        if (!$pajak) {
            return redirect()->route('kelola_data.mssumberdana.index')->with('message', 'mssumberdana tidak ditemukan.');
        }

        // Validasi input
        $validatedData = $request->validate([
            'kd_dana' => 'required',
            'nm_dana' => 'required|string|max:255',
            'rek1' => 'nullable',
            'rek2' => 'nullable',
            'rek3' => 'nullable',
            'rek4' => 'nullable',
            'rek5' => 'nullable',
            'rek6' => 'nullable',
            'rek7' => 'nullable',
            'rek8' => 'nullable',
            'rek9' => 'nullable',
            'rek10' => 'nullable',
            'rek11' => 'nullable',
            'rek12' => 'nullable',
            'anggaran_tahun' => 'nullable',
            'anggaran_tw1' => 'nullable',
            'anggaran_tw2' => 'nullable',
            'anggaran_tw3' => 'nullable',
            'anggaran_tw4' => 'nullable',

        ], [
            'kd_dana.required' => 'Kode mssumberdana harus diisi.',
            'nm_dana.required' => 'Nama mssumberdana harus diisi.',
        ]);

        // Format angka (menghapus titik dan koma) sebelum disimpan
        $formattedData = [
            'kd_dana' => $request->kd_dana,
            'nm_dana' => $request->nm_dana,
            'anggaran_tahun' => str_replace(['.', ','], '', $request->anggaran_tahun ?? 0),
            'anggaran_tw1' => str_replace(['.', ','], '', $request->anggaran_tw1 ?? 0),
            'anggaran_tw2' => str_replace(['.', ','], '', $request->anggaran_tw2 ?? 0),
            'anggaran_tw3' => str_replace(['.', ','], '', $request->anggaran_tw3 ?? 0),
            'anggaran_tw4' => str_replace(['.', ','], '', $request->anggaran_tw4 ?? 0),

        ];

        // Mengonversi nilai rek menjadi angka atau default ke 0 jika null
        for ($i = 1; $i <= 12; $i++) {
            $rekKey = 'rek' . $i;
            $formattedData[$rekKey] = $request->filled($rekKey) ? (int) str_replace(['.', ','], '', $request->input($rekKey)) : 0;
        }

        // Update data
        DB::table('ms_sumberdana')->where('id', $id)->update($formattedData);

        return redirect()->route('kelola_data.mssumberdana.index')->with('message', 'Data mssumberdana berhasil diubah.');
    }


    // Hapus produk
    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            DB::table('ms_sumberdana')->where('id', $decryptedId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'mssumberdana berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus mssumberdana.'
            ], 500);
        }
    }


}
