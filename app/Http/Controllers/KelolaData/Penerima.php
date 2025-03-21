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

class Penerima extends Controller
{
    // Tampilkan daftar produk
    public function index()
    {
        return view('kelola_data.penerima.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('ms_rekening_bank_online')
            ->select(['id','id','rekening', 'nm_rekening','bank','nm_bank','kd_skpd','npwp','nm_wp','keterangan','bic','nmrekan','pimpinan','alamat'])
            ->where('kd_skpd',Auth::user()->kd_skpd);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('rekening', 'like', "%{$search}%")
                  ->orWhere('nm_rekening', 'like', "%{$search}%")
                  ->orWhere('nm_bank', 'like', "%{$search}%")
                  ->orWhere('npwp', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");

            });
        }

        return DataTables::of($query)
            ->addIndexColumn() // Menambahkan kolom nomor
            ->addColumn('aksi', function ($row) {
                $editUrl = route('kelola_data.penerima.edit', Crypt::encrypt($row->id));
            $deleteUrl = route('kelola_data.penerima.destroy', Crypt::encrypt($row->id));

            $editButton = '<a href="' . $editUrl . '" class="btn btn-warning btn-sm d-inline-block me-1">
                            <i class="fas fa-edit"></i>
                           </a>';
            $deleteButton = '<button class="btn btn-sm btn-danger delete-btn d-inline-block" data-url="' . $deleteUrl . '" data-id="' . $row->id . '">
                              <i class="fas fa-trash-alt"></i>
                             </button>';

            return '<div class="d-flex">' . $editButton . $deleteButton . '</div>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
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
    $filePath = public_path('template/format_Penerima.xlsx'); // Pastikan file template ada di folder public/template/

    if (!file_exists($filePath)) {
        return redirect()->back()->with('message', 'File format tidak ditemukan!');
    }

    return Response::download($filePath, 'Format_Penerima.xlsx');
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


        return view('kelola_data.penerima.create');
    }


    public function getbank(Request $request)
    {

        $search = $request->q;

        $sertifikat = DB::table('ms_bank')
            ->select('kode','nama')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('kode', 'LIKE', "%{$search}%")
                      ->orWhere('nama', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $sertifikat->map(function ($item) {
            return [
                'id' => $item->kode,
                'text' => implode(' | ', [
                    $item->kode,
                    $item->nama,

                ]),
                'nm_bank' => $item->nama
            ];
        });

        return response()->json($data);

    }


public function store(Request $request)
{
    // Validasi input
    $validatedData = $request->validate([
        'bank' => 'required',
        'nm_bank' => 'required|string|max:255',
        'rekening' => 'required',
        'nm_rekening' => 'required|string|max:255',
        'npwp' => 'required',
        'nm_wp' => 'required',
        'keterangan' => 'nullable|string|max:255',
        'nm_rekan' => 'nullable',
        'pimpinan' => 'nullable',
        'alamat' => 'nullable',
    ], [
        'bank.required' => 'Bank harus diisi.',
        'rekening.required' => 'rekening harus diisi.',
    ]);

    // Format nilai anggaran menjadi integer
    $data = [
        'rekening' => $validatedData['rekening'],
        'nm_rekening' => $validatedData['nm_rekening'],
        'bank' => $validatedData['bank'],
        'nm_bank' => $validatedData['nm_bank'],
        'kd_skpd' => Auth::user()->kd_skpd,
        'npwp' => $validatedData['npwp'],
        'nm_wp' => $validatedData['nm_wp'],
        'keterangan' => $validatedData['keterangan'],
        'nmrekan' => $validatedData['nm_rekan'],
        'pimpinan' => $validatedData['pimpinan'],
        'alamat' => $validatedData['alamat'],
    ];

    // Insert data ke dalam database
    DB::table('ms_rekening_bank_online')->insert($data);

    // Redirect dengan pesan sukses
    return redirect()->route('kelola_data.penerima.index')->with('message', 'Data Penerima berhasil ditambahkan.');
}


    public function edit($id)
    {
        // Dekripsi ID yang terenkripsi
        $decryptedId = Crypt::decrypt($id);

        // Ambil data pajak berdasarkan ID menggunakan Query Builder
        $pajak = DB::table('ms_rekening_bank_online')->where('id', $decryptedId)->first();

        // Cek apakah data ditemukan
        if (!$pajak) {
            return redirect()->route('kelola_data.penerima.index')->with('message', 'Data penerima tidak ditemukan.');
        }

        // Tampilkan view untuk mengedit data
        return view('kelola_data.penerima.create', compact('pajak'));
    }


    // Update produk
    public function update(Request $request, $id)
    {
        $pajak = DB::table('ms_rekening_bank_online')->where('id', $id)->first();

        if (!$pajak) {
            return redirect()->route('kelola_data.penerima.index')->with('message', 'penerima tidak ditemukan.');
        }

        // Validasi input
        $validatedData = $request->validate([
        'bank' => 'required',
        'nm_bank' => 'required|string|max:255',
        'rekening' => 'required',
        'nm_rekening' => 'required|string|max:255',
        'npwp' => 'required',
        'nm_wp' => 'required',
        'keterangan' => 'nullable|string|max:255',
        'nm_rekan' => 'nullable',
        'pimpinan' => 'nullable',
        'alamat' => 'nullable',
        ], [
            'bank.required' => 'Bank harus diisi.',
        'rekening.required' => 'rekening harus diisi.',
        ]);

        // Format angka (menghapus titik dan koma) sebelum disimpan
        $formattedData = [
            'rekening' => $validatedData['rekening'],
            'nm_rekening' => $validatedData['nm_rekening'],
            'bank' => $validatedData['bank'],
            'nm_bank' => $validatedData['nm_bank'],
            'kd_skpd' => Auth::user()->kd_skpd,
            'npwp' => $validatedData['npwp'],
            'nm_wp' => $validatedData['nm_wp'],
            'keterangan' => $validatedData['keterangan'],
            'nmrekan' => $validatedData['nm_rekan'],
            'pimpinan' => $validatedData['pimpinan'],
            'alamat' => $validatedData['alamat'],
        ];



        // Update data
        DB::table('ms_rekening_bank_online')->where('id', $id)->update($formattedData);

        return redirect()->route('kelola_data.penerima.index')->with('message', 'Data penerima berhasil diubah.');
    }


    // Hapus produk
    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            DB::table('ms_rekening_bank_online')->where('id', $decryptedId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Penerima berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Penerima.'
            ], 500);
        }
    }


}
