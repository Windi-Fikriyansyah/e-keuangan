<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterSertifikat;
use App\Models\Skpd;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Http\Requests\KelolaData\Sertifikat\EditRequest;
use App\Http\Requests\KelolaData\Sertifikat\TambahRequest;
use App\Models\AsalUsul;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class SertifikatController extends Controller
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
        return view('kelola_data.sertifikat.index');
    }

    public function create()
    {
        $lastNumber = DB::table('masterSertifikat')->max('nomorRegister');
        $newNumber = $lastNumber ? intval($lastNumber) + 1 : 1;
        $formattedNumber = str_pad($newNumber, 6, '0', STR_PAD_LEFT);
        $daftarSkpd = Skpd::all();
        $daftarasalUsul = asalUsul::all();
        return view('kelola_data.sertifikat.create', compact('daftarSkpd','formattedNumber','daftarasalUsul'));
    }
    public function load(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterSertifikat::select('id','nomorRegister','statusSertifikat','statusPinjam','kodeSkpd', 'nib','nomorSertifikat','tanggalSertifikat','luas','hak', 'createdDate','createdUsername','updatedDate','updatedUsername');
            $search = $request->search;

            if ($search) {
                $data = $data->where(function ($query) use ($search) {
                    $query->where('nomorRegister', 'like', "%" . $search . "%")
                          ->orWhere('kodeSkpd', 'like', "%" . $search . "%")
                          ->orWhere('nomorSertifikat', 'like', "%" . $search . "%");
                });
            }

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {


                $btn = '<a href="' . route("kelola_data.sertifikat.edit", ['no_register' => Crypt::encrypt($row->nomorRegister), 'kd_skpd' => Crypt::encrypt($row->kodeSkpd)]) . '" class="btn btn-md btn-warning" style="margin-right:4px"><span class="fa-fw select-all fas"></span></a>';

                if ($row->statusSertifikat == '0' && $row->statusPinjam == '0') {
                    $btn .= '<a onclick="hapus(\'' . $row->nomorRegister . '\',\'' . $row->kodeSkpd . '\')" class="btn btn-md btn-danger"><span class="fa-fw select-all fas"></span></a>';
                }
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
            }


    }

    public function store(TambahRequest $request)
    {
        $validatedData = $request->validated();

        DB::beginTransaction();
        try {

            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                // Check if the file is a PDF
                if ($request->file('file')->getClientOriginalExtension() !== 'pdf') {
                    return redirect()
                        ->route('kelola_data.sertifikat.create')
                        ->withInput()
                        ->with('message', 'Only PDF files are allowed.');
                }

                // Generate the file name based on nomorRegister
                $filename = $validatedData['nomorRegister'] . '.pdf';

                // Store the file in the 'public/uploads' directory
               $request->file('file')->storeAs('public/uploads/sertifikat', $filename);

                // Save the file path in the validated data to insert into the database
                $validatedData['file'] = $filename;
            }

            DB::table('masterSertifikat')->lockForUpdate()->get();



            DB::table('masterSertifikat')
                ->insert([
                    'kodeSkpd' => $validatedData['kodeSkpd'],
                    'nomorRegister' => $validatedData['nomorRegister'],
                    'nib' => $validatedData['nib'],
                    'nomorSertifikat' => $validatedData['nomorSertifikat'],
                    'tanggalSertifikat' => $validatedData['tanggalSertifikat'],
                    'luas' => $validatedData['luas'],
                    'hak' => $validatedData['hak'],
                    'pemegangHak' => $validatedData['pemegangHak'],
                    'asalUsul' => $validatedData['asalUsul'],
                    'alamat' => $validatedData['alamat'],
                    'sertifikatAsli' => $validatedData['sertifikatAsli'],
                    'balikNama' => $validatedData['balikNama'],
                    'penggunaan' => $validatedData['penggunaan'],
                    'keterangan' => $validatedData['keterangan'],
                    'createdDate' => date('Y-m-d H:i:s'),
                    'createdUsername' => Auth::user()->name,
                    'updatedDate' => date('Y-m-d H:i:s'),
                    'updatedUsername' => Auth::user()->name,
                    'statusSertifikat' => '0',
                    'statusPinjam' => '0',
                    'Nibbar' => $validatedData['Nibbar'],
                    'file' => $validatedData['file'],
                ]);

            DB::commit();
            return redirect()
                ->route('kelola_data.sertifikat.index')
                ->with('message', 'Data Sertifikat berhasil disimpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('kelola_data.sertifikat.create')
                ->withInput()
                ->with('message', 'Data Sertifikat gagal disimpan!' . $e->getMessage());
        }
    }



    public function edit($nomorRegister, $kodeSkpd)
    {
        $nomorRegister = Crypt::decrypt($nomorRegister);
        $kodeSkpd = Crypt::decrypt($kodeSkpd);

        $daftarSkpd = DB::table('masterSkpd')
            ->get();

        $dataSertifikat = DB::table('masterSertifikat')
            ->where(['nomorRegister' => $nomorRegister, 'kodeSkpd' => $kodeSkpd])
            ->first();
        $daftarasalUsul = asalUsul::all();
        return view('kelola_data.sertifikat.edit', compact('daftarSkpd', 'dataSertifikat','daftarasalUsul'));
    }

    public function update(EditRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            DB::table('masterSertifikat')
                ->where([
                    'id' => $id,
                    'nomorRegister' => $request['nomorRegister'],
                    'kodeSkpd' => $request['kodeSkpd']
                ])
                ->lockForUpdate()
                ->first();

            DB::table('masterSertifikat')
                ->where([
                    'id' => $id,
                    'nomorRegister' => $request['nomorRegister'],
                    'kodeSkpd' => $request['kodeSkpd']
                ])
                ->update([
                    'nib' => $request['nib'],
                    'nomorSertifikat' => $request['nomorSertifikat'],
                    'tanggalSertifikat' => $request['tanggalSertifikat'],
                    'luas' => $request['luas'],
                    'hak' => $request['hak'],
                    'pemegangHak' => $request['pemegangHak'],
                    'asalUsul' => $request['asalUsul'],
                    'alamat' => $request['alamat'],
                    'sertifikatAsli' => $request['sertifikatAsli'],
                    'balikNama' => $request['balikNama'],
                    'penggunaan' => $request['penggunaan'],
                    'keterangan' => $request['keterangan'],
                    'updatedDate' => date('Y-m-d H:i:s'),
                    'updatedUsername' => Auth::user()->name,
                    'Nibbar' => $request['Nibbar'],
                ]);

            DB::commit();
            return redirect()
                ->route('kelola_data.sertifikat.index')
                ->with('message', 'Data Sertifikat berhasil diupdate!');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()
                ->route(
                    'kelola_data.sertifikat.edit',
                    [
                        'no_register' => Crypt::encrypt($request['nomorRegister']),
                        'kd_skpd' => Crypt::encrypt($request['kodeSkpd']),
                    ]
                )
                ->withInput()
                ->with('message', 'Data Sertifikat gagal diupdate!');
        }
    }

   

public function getFiles(Request $request)
    {
        try {
            // Ambil data file dari database (misalnya berdasarkan ID atau kriteria lainnya)
            // Pastikan model dan kolomnya sesuai dengan struktur database kamu
            $file = DB::table('masterSertifikat')->where('id', $request->id) // Atau sesuaikan dengan kriteria lainnya
                ->first();

            if (!$file) {
                return response()->json(['message' => 'File not found'], 404);
            }

            // Return data file dalam bentuk JSON
            return response()->json([
                'file' => $file->file,
            ]);
        } catch (\Exception $e) {
            // Tangani kesalahan jika terjadi
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function updateFile(Request $request)
{

    $request->validate([
        'file' => 'required|mimes:pdf|max:2048',
        'fileType' => 'required|in:file',
        'bpkbId' => 'required|exists:masterSertifikat,id',
        'nomorRegister' => 'required'
    ]);

    $bpkb = MasterSertifikat::findOrFail($request->bpkbId);

    $fileFieldMap = [
        'file' => 'file',
    ];

    $field = $fileFieldMap[$request->fileType];
    $path = "uploads/sertifikat";

    // Delete old file if exists
    if ($bpkb->$field) {
        Storage::delete("public/$path/" . $bpkb->$field);
    }

    // Store new file
    $fileName = $request->nomorRegister . '.pdf';
    $request->file('file')->storeAs("public/$path", $fileName);

    $bpkb->update([$field => $fileName]);

    return response()->json(['message' => 'File berhasil diperbarui']);
}


public function destroy(Request $request)
{
    DB::beginTransaction(); // Mulai transaksi
    try {
       
        $item = DB::table('masterSertifikat')
            ->where([
                'nomorRegister' => $request->nomorRegister,
                'kodeSkpd' => $request->kodeSkpd
            ])
            ->lockForUpdate()
            ->first();

        if (!$item) {
            throw new \Exception('Data tidak ditemukan.');
        }

        // Tentukan path file yang akan dihapus
        $filePaths = [
            'public/uploads/sertifikat/' . $item->file,
        ];

        // Hapus semua file yang ada
        foreach ($filePaths as $filePath) {
            if (Storage::exists($filePath)) {
                if (!Storage::delete($filePath)) {
                    throw new \Exception('Gagal menghapus file: ' . $filePath);
                }
            }
        }

        // Hapus data dari database
        DB::table('masterSertifikat')
            ->where([
                'nomorRegister' => $request->nomorRegister,
                'kodeSkpd' => $request->kodeSkpd
            ])
            ->delete();

        // Komit transaksi
        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil dihapus!'
        ], 200);
    } catch (\Exception $e) {
        DB::rollBack(); // Batalkan transaksi jika terjadi error

        return response()->json([
            'status' => false,
            'message' => 'Data gagal dihapus: ' . $e->getMessage()
        ], 500);
    }
}


public function getFiles(Request $request)
    {
        try {
            // Ambil data file dari database (misalnya berdasarkan ID atau kriteria lainnya)
            // Pastikan model dan kolomnya sesuai dengan struktur database kamu
            $file = DB::table('masterSertifikat')->where('id', $request->id) // Atau sesuaikan dengan kriteria lainnya
                ->first();

            if (!$file) {
                return response()->json(['message' => 'File not found'], 404);
            }

            // Return data file dalam bentuk JSON
            return response()->json([
                'file' => $file->file,
            ]);
        } catch (\Exception $e) {
            // Tangani kesalahan jika terjadi
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function updateFile(Request $request)
{

    $request->validate([
        'file' => 'required|mimes:pdf|max:2048',
        'fileType' => 'required|in:file',
        'bpkbId' => 'required|exists:masterSertifikat,id',
        'nomorRegister' => 'required'
    ]);

    $bpkb = MasterSertifikat::findOrFail($request->bpkbId);

    $fileFieldMap = [
        'file' => 'file',
    ];

    $field = $fileFieldMap[$request->fileType];
    $path = "uploads/sertifikat";

    // Delete old file if exists
    if ($bpkb->$field) {
        Storage::delete("public/$path/" . $bpkb->$field);
    }

    // Store new file
    $fileName = $request->nomorRegister . '.pdf';
    $request->file('file')->storeAs("public/$path", $fileName);

    $bpkb->update([$field => $fileName]);

    return response()->json(['message' => 'File berhasil diperbarui']);
}


}
