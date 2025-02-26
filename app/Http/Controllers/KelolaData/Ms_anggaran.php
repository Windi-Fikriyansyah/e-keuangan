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

class Ms_anggaran extends Controller
{
    // Tampilkan daftar produk
    public function index()
    {
        return view('kelola_data.msanggaran.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('ms_anggaran')
            ->select(['id','kd_rek', 'nm_rek','anggaran_tahun','anggaran_tw1','anggaran_tw2','anggaran_tw3','anggaran_tw4']);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('kd_rek', 'like', "%{$search}%")
                  ->orWhere('nm_rek', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn() // Menambahkan kolom nomor
            ->addColumn('aksi', function ($row) {
                $editButton = '<a href="' . route('kelola_data.msanggaran.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-primary">Edit</a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('kelola_data.msanggaran.destroy', Crypt::encrypt($row->id)) . '">Hapus</button>';
                return $editButton . ' ' . $deleteButton;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function getsubkegiatan(Request $request)
    {

        $search = $request->q;

        $sertifikat = DB::table('ms_sub_kegiatan')
            ->select('kd_sub_kegiatan','nm_sub_kegiatan','nm_program','kd_program')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('kd_sub_kegiatan', 'LIKE', "%{$search}%")
                      ->orWhere('nm_sub_kegiatan', 'LIKE', "%{$search}%")
                      ->orWhere('nm_program', 'LIKE', "%{$search}%");
            })
            ->limit(100)
            ->get();

        $data = $sertifikat->map(function ($item) {
            return [
                'id' => $item->kd_sub_kegiatan,
                'text' => implode(' | ', [
                    $item->kd_sub_kegiatan,
                    $item->nm_sub_kegiatan,
                    $item->nm_program,
                    $item->kd_program,

                ]),
                'nm_sub_kegiatan' => $item->nm_sub_kegiatan,
                'nm_program' => $item->nm_program,
                'kd_program' => $item->kd_program
            ];
        });

        return response()->json($data);

    }


    public function getsumberdana(Request $request)
    {

        $search = $request->q;

        $sertifikat = DB::table('ms_sumberdana')
            ->select('id','kd_dana','sumber_dana','anggaran_tahun')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('kd_dana', 'LIKE', "%{$search}%")
                      ->orWhere('sumber_dana', 'LIKE', "%{$search}%");
            })
            ->limit(100)
            ->get();

        $data = $sertifikat->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => implode(' | ', [
                    $item->id,
                    $item->kd_dana,
                    $item->sumber_dana,
                    $item->anggaran_tahun,

                ]),
                'nm_sumberdana' => $item->sumber_dana,
            ];
        });

        return response()->json($data);

    }


    public function downloadFormat()
{
    $filePath = public_path('template/format_ms_anggaran.xlsx'); // Pastikan file template ada di folder public/template/

    if (!file_exists($filePath)) {
        return redirect()->back()->with('message', 'File format tidak ditemukan!');
    }

    return Response::download($filePath, 'Format_Ms_Anggaran.xlsx');
}


public function upload(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls|max:2048' // Validasi format file
    ]);

    if (!$request->hasFile('file')) {
        return redirect()->back()->with('error', 'Tidak ada file yang diunggah.');
    }

    DB::beginTransaction();

    try {
        Excel::import(new MsAnggaranImport, $request->file('file'));
        DB::commit();

        return redirect()->back()->with('success', 'Data berhasil diupload!');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Gagal mengupload data: ' . $e->getMessage());
    }
}

    // Tampilkan form tambah produk
    public function create()
    {
        return view('kelola_data.msanggaran.create');
    }



public function store(Request $request)
{
    // Validasi input
    $validatedData = $request->validate([
        'kd_rek' => 'required',
        'nm_rek' => 'required|string|max:255',
        'kd_sub_kegiatan' => 'required',
        'nm_sub_kegiatan' => 'required|string|max:255',
        'kd_program' => 'nullable',
        'nm_program' => 'nullable|string|max:255',
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
        'status_anggaran' => 'nullable|string',
        'status_anggaran_kas' => 'nullable|string',
        'id_sumberdana' => 'nullable',
    ], [
        'kd_rek.required' => 'Kode rekening harus diisi.',
        'nm_rek.required' => 'Nama rekening harus diisi.',
    ]);

    // Format nilai anggaran menjadi integer
    $data = [
        'kd_rek' => $validatedData['kd_rek'],
        'nm_rek' => $validatedData['nm_rek'],
        'kd_sub_kegiatan' => $validatedData['kd_sub_kegiatan'],
        'nm_sub_kegiatan' => $validatedData['nm_sub_kegiatan'],
        'kd_program' => $validatedData['kd_program'],
        'nm_program' => $validatedData['nm_program'],
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
        'status_anggaran' => $validatedData['status_anggaran'],
        'status_anggaran_kas' => $validatedData['status_anggaran_kas'],
        'id_sumberdana' => $validatedData['id_sumberdana'],
    ];

    // Insert data ke dalam database
    DB::table('ms_anggaran')->insert($data);

    // Redirect dengan pesan sukses
    return redirect()->route('kelola_data.msanggaran.index')->with('message', 'Data Anggaran berhasil ditambahkan.');
}


    public function edit($id)
    {
        // Dekripsi ID yang terenkripsi
        $decryptedId = Crypt::decrypt($id);

        // Ambil data pajak berdasarkan ID menggunakan Query Builder
        $pajak = DB::table('ms_anggaran')->where('id', $decryptedId)->first();

        // Cek apakah data ditemukan
        if (!$pajak) {
            return redirect()->route('kelola_data.msanggaran.index')->with('message', 'Data msanggaran tidak ditemukan.');
        }

        // Tampilkan view untuk mengedit data
        return view('kelola_data.msanggaran.create', compact('pajak'));
    }


    // Update produk
    public function update(Request $request, $id)
    {
        $pajak = DB::table('ms_anggaran')->where('id', $id)->first();

        if (!$pajak) {
            return redirect()->route('kelola_data.msanggaran.index')->with('message', 'msanggaran tidak ditemukan.');
        }

        // Validasi input
        $validatedData = $request->validate([
            'kd_rek' => 'required',
            'nm_rek' => 'required|string|max:255',
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
            'status_anggaran' => 'nullable|string',
            'status_anggaran_kas' => 'nullable|string',
        ], [
            'kd_rek.required' => 'Kode msanggaran harus diisi.',
            'nm_rek.required' => 'Nama msanggaran harus diisi.',
        ]);

        // Format angka (menghapus titik dan koma) sebelum disimpan
        $formattedData = [
            'kd_rek' => $request->kd_rek,
            'nm_rek' => $request->nm_rek,
            'anggaran_tahun' => str_replace(['.', ','], '', $request->anggaran_tahun ?? 0),
            'anggaran_tw1' => str_replace(['.', ','], '', $request->anggaran_tw1 ?? 0),
            'anggaran_tw2' => str_replace(['.', ','], '', $request->anggaran_tw2 ?? 0),
            'anggaran_tw3' => str_replace(['.', ','], '', $request->anggaran_tw3 ?? 0),
            'anggaran_tw4' => str_replace(['.', ','], '', $request->anggaran_tw4 ?? 0),
            'status_anggaran' => $request->status_anggaran,
            'status_anggaran_kas' => $request->status_anggaran_kas,
        ];

        // Mengonversi nilai rek menjadi angka atau default ke 0 jika null
        for ($i = 1; $i <= 12; $i++) {
            $rekKey = 'rek' . $i;
            $formattedData[$rekKey] = $request->filled($rekKey) ? (int) str_replace(['.', ','], '', $request->input($rekKey)) : 0;
        }

        // Update data
        DB::table('ms_anggaran')->where('id', $id)->update($formattedData);

        return redirect()->route('kelola_data.msanggaran.index')->with('message', 'Data msanggaran berhasil diubah.');
    }


    // Hapus produk
    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            DB::table('ms_anggaran')->where('id', $decryptedId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'msanggaran berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus msanggaran.'
            ], 500);
        }
    }


}
