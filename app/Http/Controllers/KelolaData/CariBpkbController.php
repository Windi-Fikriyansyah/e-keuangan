<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use App\Http\Requests\KelolaData\BPKB\EditRequest;
use App\Http\Requests\KelolaData\BPKB\TambahRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Bpkb;

class CariBpkbController extends Controller
{
    public function index()
    {
        $jenis = DB::table('masterBpkb')->select('jenis')->distinct()->get();
        return view('kelola_data.cari_bpkb.index', compact('jenis'));
    }

    public function load(Request $request)
{
    $query = DB::table('masterBpkb as a')
        ->select(
            'a.nomorRegister',
            'a.nomorBpkb',
            'a.nomorPolisi',
            'a.kodeSkpd',
            'b.namaSkpd',
            'a.statusBpkb',
            'a.statusPinjam'
        )
        ->leftJoin('masterSkpd as b', 'a.kodeSkpd', '=', 'b.kodeSkpd');

    // Apply Filters
    if ($request->filled('jenis')) {
        $query->where('a.jenis', $request->input('jenis'));
    }

    if ($request->filled('merk')) {
        $query->where('a.merk', $request->input('merk'));
    }

    if ($request->filled('nomorRegister')) {
        $query->where('a.nomorRegister', $request->input('nomorRegister'));
    }

    // DataTables Processing
    return DataTables::of($query)
        ->addColumn('aksi', function ($row) {
            $editUrl = route('kelola_data.caribpkb.edit', [
                'no_register' => Crypt::encrypt($row->nomorRegister),
                'kd_skpd' => Crypt::encrypt($row->kodeSkpd)
            ]);

            $btn = '<a href="' . $editUrl . '" class="btn btn-md btn-warning" style="margin-right:4px">
                        <span class="fa-fw select-all fas"></span>
                    </a>';



            return $btn;
        })
        ->rawColumns(['aksi'])
        ->make(true);
}


    public function getMerks(Request $request)
    {
        $merks = DB::table('masterBpkb')
        ->select('merk') // Pastikan hanya mengambil kolom yang diperlukan
        ->distinct()
        ->where('jenis', $request->jenis_id)
        ->get();

    return response()->json($merks);
    }


    public function loadBpkb(Request $request)
{
    $term = trim($request->q); // Search term
    $jenis_id = $request->jenis_id; // Filter by jenis_id
    $merk_id = $request->merk_id;  // Filter by merk_id

    $query = DB::table('masterBpkb');

    // Apply filters if provided
    if (!empty($jenis_id)) {
        $query->where('jenis', $jenis_id);
    }

    if (!empty($merk_id)) {
        $query->where('merk', $merk_id);
    }

    // Search by term
    if (!empty($term)) {
        $query->where(function ($query) use ($term) {
            $query->where('nomorRegister', 'like', "%$term%")
                ->orWhere('nomorPolisi', 'like', "%$term%")
                ->orWhere('nomorBpkb', 'like', "%$term%")
                ->orWhere('kodeSkpd', 'like', "%$term%");
        });
    }

    // Get data
    $tags = $query->get();

    // Format data for JSON response
    $formatted_tags = $tags->map(function ($tag) {
        return [
            'nomorRegister' => $tag->nomorRegister,
            'nomorPolisi' => $tag->nomorPolisi,
            'nomorBpkb' => $tag->nomorBpkb,
            'kodeSkpd' => $tag->kodeSkpd,
            'namaPemilik' => $tag->namaPemilik ?? '', // Add default value if null
            'jenis' => $tag->jenis ?? '',
            'merk' => $tag->merk ?? '',
            'tipe' => $tag->tipe ?? '',
            'model' => $tag->model ?? '',
            'tahunPembuatan' => $tag->tahunPembuatan ?? '',
            'tahunPerakitan' => $tag->tahunPerakitan ?? '',
            'isiSilinder' => $tag->isiSilinder ?? '',
            'warna' => $tag->warna ?? '',
            'alamat' => $tag->alamat ?? '',
            'nomorRangka' => $tag->nomorRangka ?? '',
            'nomorMesin' => $tag->nomorMesin ?? '',
            'keterangan' => $tag->keterangan ?? '',
            'nomorPolisiLama' => $tag->nomorPolisiLama ?? '',
            'nomorBpkbLama' => $tag->nomorBpkbLama ?? '',
        ];
    });

    return response()->json($formatted_tags);
}


    // public function create()
    // {
    //     $daftarSkpd = DB::table('masterSkpd')
    //         ->get();

    //     return view('kelola_data.cari_bpkb.create', compact('daftarSkpd'));
    // }

    // public function store(TambahRequest $request)
    // {
    //     $validatedData = $request->validated();

    //     DB::beginTransaction();
    //     try {
    //         DB::table('masterBpkb')->lockForUpdate()->get();

    //         // Process filesuratpenunjukan
    //     if ($request->hasFile('filesuratpenunjukan') && $request->file('filesuratpenunjukan')->isValid()) {
    //         if ($request->file('filesuratpenunjukan')->getClientOriginalExtension() !== 'pdf') {
    //             return redirect()
    //                 ->route('kelola_data.bpkp.create')
    //                 ->withInput()
    //                 ->with('message', 'Only PDF files are allowed for filesuratpenunjukan.');
    //         }
    //         $filename = Auth::user()->kd_skpd . '_' . $validatedData['nomorBpkb'] . '_' . 'file_surat_penunjukan.pdf';
    //         $request->file('filesuratpenunjukan')->storeAs('public/uploads/bpkb/file_surat_penunjukan', $filename);
    //         $validatedData['filesuratpenunjukan'] = $filename;
    //     }

    //     // Process fileba
    //     if ($request->hasFile('fileba') && $request->file('fileba')->isValid()) {
    //         if ($request->file('fileba')->getClientOriginalExtension() !== 'pdf') {
    //             return redirect()
    //                 ->route('kelola_data.bpkp.create')
    //                 ->withInput()
    //                 ->with('message', 'Only PDF files are allowed for fileba.');
    //         }
    //         $filename = Auth::user()->kd_skpd . '_' . $validatedData['nomorBpkb'] . '_' . 'file_ba.pdf';
    //         $request->file('fileba')->storeAs('public/uploads/bpkb/file_ba', $filename);
    //         $validatedData['fileba'] = $filename;
    //     }

    //     // Process filepaktaintegritas
    //     if ($request->hasFile('filepaktaintegritas') && $request->file('filepaktaintegritas')->isValid()) {
    //         if ($request->file('filepaktaintegritas')->getClientOriginalExtension() !== 'pdf') {
    //             return redirect()
    //                 ->route('kelola_data.bpkp.create')
    //                 ->withInput()
    //                 ->with('message', 'Only PDF files are allowed for filepaktaintegritas.');
    //         }
    //         $filename = Auth::user()->kd_skpd . '_' . $validatedData['nomorBpkb'] . '_' . 'file_pakta_integritas.pdf';
    //         $request->file('filepaktaintegritas')->storeAs('public/uploads/bpkb/file_pakta_integritas', $filename);
    //         $validatedData['filepaktaintegritas'] = $filename;
    //     }

    //     if ($request->hasFile('filebpkb') && $request->file('filebpkb')->isValid()) {
    //         if ($request->file('filebpkb')->getClientOriginalExtension() !== 'pdf') {
    //             return redirect()
    //                 ->route('kelola_data.bpkp.create')
    //                 ->withInput()
    //                 ->with('message', 'Only PDF files are allowed for filebpkb.');
    //         }
    //         $filename = Auth::user()->kd_skpd . '_' . $validatedData['nomorBpkb'] . '_' . 'file_bpkb.pdf';
    //         $request->file('filebpkb')->storeAs('public/uploads/bpkb/file_bpkb', $filename);
    //         $validatedData['filebpkb'] = $filename;
    //     }

    //         $nomorBaru = DB::table('masterBpkb')
    //             ->selectRaw("ISNULL(MAX(nomorRegister),0)+1 as nomor")
    //             ->first();

    //         if (Str::length($nomorBaru->nomor) == '1') {
    //             $nomor = '00000' . $nomorBaru->nomor;
    //         } else if (Str::length($nomorBaru->nomor) == '2') {
    //             $nomor = '0000' . $nomorBaru->nomor;
    //         } else if (Str::length($nomorBaru->nomor) == '3') {
    //             $nomor = '000' . $nomorBaru->nomor;
    //         } else if (Str::length($nomorBaru->nomor) == '4') {
    //             $nomor = '00' . $nomorBaru->nomor;
    //         } else if (Str::length($nomorBaru->nomor) == '5') {
    //             $nomor = '0' . $nomorBaru->nomor;
    //         } else if (Str::length($nomorBaru->nomor) == '6') {
    //             $nomor = $nomorBaru->nomor;
    //         }

    //         DB::table('masterBpkb')
    //             ->insert([
    //                 'kodeSkpd' => $validatedData['kodeSkpd'],
    //                 'nomorRegister' => $nomor,
    //                 'nomorBpkb' => $validatedData['nomorBpkb'],
    //                 'nomorPolisi' => $validatedData['nomorPolisi'],
    //                 'namaPemilik' => $validatedData['namaPemilik'],
    //                 'jenis' => $validatedData['jenis'],
    //                 'merk' => $validatedData['merk'],
    //                 'tipe' => $validatedData['tipe'],
    //                 'model' => $validatedData['model'],
    //                 'tahunPembuatan' => $validatedData['tahunPembuatan'],
    //                 'tahunPerakitan' => $validatedData['tahunPerakitan'],
    //                 'isiSilinder' => $validatedData['isiSilinder'],
    //                 'warna' => $validatedData['warna'],
    //                 'alamat' => $validatedData['alamat'],
    //                 'nomorRangka' => $validatedData['nomorRangka'],
    //                 'nomorMesin' => $validatedData['nomorMesin'],
    //                 'keterangan' => $validatedData['keterangan'],
    //                 'nomorPolisiLama' => $validatedData['nomorPolisiLama'],
    //                 'nomorBpkbLama' => $validatedData['nomorBpkbLama'],
    //                 'Nibbar' => $validatedData['Nibbar'],
    //                 'namapenerimakendaraan' => $validatedData['namapenerimakendaraan'],
    //                 'filesuratpenunjukan' => $validatedData['filesuratpenunjukan'],
    //                 'fileba' => $validatedData['fileba'],
    //                 'filepaktaintegritas' => $validatedData['filepaktaintegritas'],
    //                 'filebpkb' => $validatedData['filebpkb'],
    //                 'createdDate' => date('Y-m-d H:i:s'),
    //                 'createdUsername' => Auth::user()->name,
    //                 'updatedDate' => date('Y-m-d H:i:s'),
    //                 'updatedUsername' => Auth::user()->name,
    //                 'statusBpkb' => '0',
    //                 'statusPinjam' => '0',
    //             ]);

    //         DB::commit();
    //         return redirect()
    //             ->route('kelola_data.cari_bpkb.index')
    //             ->with('message', 'Data BPKB berhasil disimpan!');
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         return redirect()
    //             ->route('kelola_data.cari_bpkb.create')
    //             ->withInput()
    //             ->with('message', 'Data BPKB gagal disimpan!' . $e->getMessage());
    //     }
    // }

    public function getFiles(Request $request)
    {
        try {
            // Ambil data file dari database (misalnya berdasarkan ID atau kriteria lainnya)
            // Pastikan model dan kolomnya sesuai dengan struktur database kamu

            $file = DB::table('masterBpkb')->where('id', $request->id) // Atau sesuaikan dengan kriteria lainnya
                ->first();

            if (!$file) {
                return response()->json(['message' => 'File not found'], 404);
            }

            // Return data file dalam bentuk JSON
            return response()->json([
                'filesuratpenunjukan' => $file->filesuratpenunjukan, // Nama kolom yang menyimpan nama file
                'fileba' => $file->fileba,
                'filepaktaintegritas' => $file->filepaktaintegritas,
                'filebpkb' => $file->filebpkb // Nama kolom yang menyimpan nama file BA
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
        'fileType' => 'required|in:suratPenunjukan,ba,paktaIntegritas,bpkb',
        'bpkbId' => 'required|exists:masterBpkb,id'
    ]);

    $bpkb = Bpkb::findOrFail($request->bpkbId);

    $fileFieldMap = [
        'suratPenunjukan' => 'filesuratpenunjukan',
        'ba' => 'fileba',
        'paktaIntegritas' => 'filepaktaintegritas',
        'bpkb' => 'bpkb'
    ];

    $field = $fileFieldMap[$request->fileType];
    $path = "uploads/bpkb/file_" . Str::snake($request->fileType);

    // Delete old file if exists
    if ($bpkb->$field) {
        Storage::delete("public/$path/" . $bpkb->$field);
    }

    // Store new file
    $fileName = Auth::user()->kd_skpd . '_' . $request['nomorBpkb'] . '_' . 'file_' . Str::snake($request['fileType']) . '.pdf';
    $request->file('file')->storeAs("public/$path", $fileName);

    $bpkb->update([$field => $fileName]);

    return response()->json(['message' => 'File berhasil diperbarui']);
}

    public function edit($nomorRegister, $kodeSkpd)
    {
        $nomorRegister = Crypt::decrypt($nomorRegister);
        $kodeSkpd = Crypt::decrypt($kodeSkpd);

        $daftarSkpd = DB::table('masterSkpd')
            ->get();

        $dataBpkb = DB::table('masterBpkb')
            ->where(['nomorRegister' => $nomorRegister, 'kodeSkpd' => $kodeSkpd])
            ->first();

        return view('kelola_data.cari_bpkb.edit', compact('daftarSkpd', 'dataBpkb'));
    }

    public function update(EditRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            DB::table('masterBpkb')
                ->where([
                    'id' => $id,
                    'nomorRegister' => $request['nomorRegister'],
                    'kodeSkpd' => $request['kodeSkpd']
                ])
                ->lockForUpdate()
                ->first();

            DB::table('masterBpkb')
                ->where([
                    'id' => $id,
                    'nomorRegister' => $request['nomorRegister'],
                    'kodeSkpd' => $request['kodeSkpd']
                ])
                ->update([
                    'nomorBpkb' => $request['nomorBpkb'],
                    'nomorPolisi' => $request['nomorPolisi'],
                    'namaPemilik' => $request['namaPemilik'],
                    'jenis' => $request['jenis'],
                    'merk' => $request['merk'],
                    'tipe' => $request['tipe'],
                    'model' => $request['model'],
                    'tahunPembuatan' => $request['tahunPembuatan'],
                    'tahunPerakitan' => $request['tahunPerakitan'],
                    'isiSilinder' => $request['isiSilinder'],
                    'warna' => $request['warna'],
                    'alamat' => $request['alamat'],
                    'nomorRangka' => $request['nomorRangka'],
                    'nomorMesin' => $request['nomorMesin'],
                    'keterangan' => $request['keterangan'],
                    'nomorPolisiLama' => $request['nomorPolisiLama'],
                    'Nibbar' => $request['Nibbar'],
                    'namapenerimakendaraan' => $request['namapenerimakendaraan'],
                    'nomorBpkbLama' => $request['nomorBpkbLama'],
                    'updatedDate' => date('Y-m-d H:i:s'),
                    'updatedUsername' => Auth::user()->name,
                ]);

            DB::commit();
            return redirect()
                ->route('kelola_data.cari_bpkb.index')
                ->with('message', 'Data BPKB berhasil diupdate!');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()
                ->route(
                    'kelola_data.cari_bpkb.edit',
                    [
                        'no_register' => Crypt::encrypt($request['nomorRegister']),
                        'kd_skpd' => Crypt::encrypt($request['kodeSkpd']),
                    ]
                )
                ->withInput()
                ->with('message', 'Data BPKB gagal diupdate!');
        }
    }

    public function destroy(Request $request)
{
    DB::beginTransaction(); // Mulai transaksi
    try {
        // Ambil data item berdasarkan nomorRegister dan kodeSkpd
        $item = DB::table('masterBpkb')
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
            'public/uploads/bpkb/file_surat_penunjukan/' . $item->filesuratpenunjukan,
            'public/uploads/bpkb/file_ba/' . $item->fileba,
            'public/uploads/bpkb/file_pakta_integritas/' . $item->filepaktaintegritas
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
        DB::table('masterBpkb')
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

}
