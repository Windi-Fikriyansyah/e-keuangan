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

class BPKBController extends Controller
{
    public function index()
    {
        return view('kelola_data.bpkb.index');
    }

    public function load(Request $request)
    {
        // Page Length
        // $pageNumber = ($request->start / $request->length) + 1;
        // $pageLength = $request->length;
        // $skip       = ($pageNumber - 1) * $pageLength;

        // // Page Order
        // $orderColumnIndex = $request->order[0]['column'] ?? '0';
        // $orderBy = $request->order[0]['dir'] ?? 'desc';

        // get data from products table
        $query = DB::table('masterBpkb as a')
            ->select('a.nomorRegister', 'a.nomorBpkb', 'a.nomorPolisi', 'a.kodeSkpd', 'b.namaSkpd', 'a.statusBpkb', 'a.statusPinjam')
            ->leftJoin('masterSkpd as b', 'a.kodeSkpd', '=', 'b.kodeSkpd')
            ->get();

        // Search
        // $search = $request->search;
        // $query = $query->where(function ($query) use ($search) {
        //     $query->orWhere('nomorRegister', 'like', "%" . $search . "%");
        // });

        // $orderByName = 'nomorRegister';
        // switch ($orderColumnIndex) {
        //     case '0':
        //         $orderByName = 'nomorRegister';
        //         break;
        // }
        // $query = $query->orderBy($orderByName, $orderBy);
        // $recordsFiltered = $recordsTotal = $query->count();
        // $users = $query->skip($skip)->take($pageLength)->get();

        return DataTables::of($query)
            ->addColumn('aksi', function ($row) {
                $btn = '<a href="' . route("kelola_data.bpkb.edit", ['no_register' => Crypt::encrypt($row->nomorRegister), 'kd_skpd' => Crypt::encrypt($row->kodeSkpd)]) . '" class="btn btn-md btn-warning" style="margin-right:4px"><span class="fa-fw select-all fas"></span></a>';

                if ($row->statusBpkb == '0' && $row->statusPinjam == '0') {
                    $btn .= '<a onclick="hapus(\'' . $row->nomorRegister . '\',\'' . $row->kodeSkpd . '\')" class="btn btn-md btn-danger"><span class="fa-fw select-all fas"></span></a>';
                }
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $daftarSkpd = DB::table('masterSkpd')
            ->get();

        return view('kelola_data.bpkb.create', compact('daftarSkpd'));
    }

    public function store(TambahRequest $request)
    {
        $validatedData = $request->validated();

        DB::beginTransaction();
        try {
            DB::table('masterBpkb')->lockForUpdate()->get();

            // Process filesuratpenunjukan
        if ($request->hasFile('filesuratpenunjukan') && $request->file('filesuratpenunjukan')->isValid()) {
            if ($request->file('filesuratpenunjukan')->getClientOriginalExtension() !== 'pdf') {
                return redirect()
                    ->route('kelola_data.bpkp.create')
                    ->withInput()
                    ->with('message', 'Only PDF files are allowed for filesuratpenunjukan.');
            }
            $filename = Auth::user()->kd_skpd . '_' . $validatedData['nomorBpkb'] . '_' . 'file_surat_penunjukan.pdf';
            $request->file('filesuratpenunjukan')->storeAs('public/uploads/bpkb/file_surat_penunjukan', $filename);
            $validatedData['filesuratpenunjukan'] = $filename;
        }

        // Process fileba
        if ($request->hasFile('fileba') && $request->file('fileba')->isValid()) {
            if ($request->file('fileba')->getClientOriginalExtension() !== 'pdf') {
                return redirect()
                    ->route('kelola_data.bpkp.create')
                    ->withInput()
                    ->with('message', 'Only PDF files are allowed for fileba.');
            }
            $filename = Auth::user()->kd_skpd . '_' . $validatedData['nomorBpkb'] . '_' . 'file_ba.pdf';
            $request->file('fileba')->storeAs('public/uploads/bpkb/file_ba', $filename);
            $validatedData['fileba'] = $filename;
        }

        // Process filepaktaintegritas
        if ($request->hasFile('filepaktaintegritas') && $request->file('filepaktaintegritas')->isValid()) {
            if ($request->file('filepaktaintegritas')->getClientOriginalExtension() !== 'pdf') {
                return redirect()
                    ->route('kelola_data.bpkp.create')
                    ->withInput()
                    ->with('message', 'Only PDF files are allowed for filepaktaintegritas.');
            }
            $filename = Auth::user()->kd_skpd . '_' . $validatedData['nomorBpkb'] . '_' . 'file_pakta_integritas.pdf';
            $request->file('filepaktaintegritas')->storeAs('public/uploads/bpkb/file_pakta_integritas', $filename);
            $validatedData['filepaktaintegritas'] = $filename;
        }
            $nomorBaru = DB::table('masterBpkb')
                ->selectRaw("ISNULL(MAX(nomorRegister),0)+1 as nomor")
                ->first();

            if (Str::length($nomorBaru->nomor) == '1') {
                $nomor = '00000' . $nomorBaru->nomor;
            } else if (Str::length($nomorBaru->nomor) == '2') {
                $nomor = '0000' . $nomorBaru->nomor;
            } else if (Str::length($nomorBaru->nomor) == '3') {
                $nomor = '000' . $nomorBaru->nomor;
            } else if (Str::length($nomorBaru->nomor) == '4') {
                $nomor = '00' . $nomorBaru->nomor;
            } else if (Str::length($nomorBaru->nomor) == '5') {
                $nomor = '0' . $nomorBaru->nomor;
            } else if (Str::length($nomorBaru->nomor) == '6') {
                $nomor = $nomorBaru->nomor;
            }

            DB::table('masterBpkb')
                ->insert([
                    'kodeSkpd' => $validatedData['kodeSkpd'],
                    'nomorRegister' => $nomor,
                    'nomorBpkb' => $validatedData['nomorBpkb'],
                    'nomorPolisi' => $validatedData['nomorPolisi'],
                    'namaPemilik' => $validatedData['namaPemilik'],
                    'jenis' => $validatedData['jenis'],
                    'merk' => $validatedData['merk'],
                    'tipe' => $validatedData['tipe'],
                    'model' => $validatedData['model'],
                    'tahunPembuatan' => $validatedData['tahunPembuatan'],
                    'tahunPerakitan' => $validatedData['tahunPerakitan'],
                    'isiSilinder' => $validatedData['isiSilinder'],
                    'warna' => $validatedData['warna'],
                    'alamat' => $validatedData['alamat'],
                    'nomorRangka' => $validatedData['nomorRangka'],
                    'nomorMesin' => $validatedData['nomorMesin'],
                    'keterangan' => $validatedData['keterangan'],
                    'nomorPolisiLama' => $validatedData['nomorPolisiLama'],
                    'nomorBpkbLama' => $validatedData['nomorBpkbLama'],
                    'Nibbar' => $validatedData['Nibbar'],
                    'namapenerimakendaraan' => $validatedData['namapenerimakendaraan'],
                    'filesuratpenunjukan' => $validatedData['filesuratpenunjukan'],
                    'fileba' => $validatedData['fileba'],
                    'filepaktaintegritas' => $validatedData['filepaktaintegritas'],
                    'createdDate' => date('Y-m-d H:i:s'),
                    'createdUsername' => Auth::user()->name,
                    'updatedDate' => date('Y-m-d H:i:s'),
                    'updatedUsername' => Auth::user()->name,
                    'statusBpkb' => '0',
                    'statusPinjam' => '0',
                ]);

            DB::commit();
            return redirect()
                ->route('kelola_data.bpkb.index')
                ->with('message', 'Data BPKB berhasil disimpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('kelola_data.bpkb.create')
                ->withInput()
                ->with('message', 'Data BPKB gagal disimpan!' . $e->getMessage());
        }
    }

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
                'filepaktaintegritas' => $file->filepaktaintegritas // Nama kolom yang menyimpan nama file BA
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
        'fileType' => 'required|in:suratPenunjukan,ba,paktaIntegritas',
        'bpkbId' => 'required|exists:masterBpkb,id'
    ]);

    $bpkb = Bpkb::findOrFail($request->bpkbId);

    $fileFieldMap = [
        'suratPenunjukan' => 'filesuratpenunjukan',
        'ba' => 'fileba',
        'paktaIntegritas' => 'filepaktaintegritas'
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

        return view('kelola_data.bpkb.edit', compact('daftarSkpd', 'dataBpkb'));
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
                ->route('kelola_data.bpkb.index')
                ->with('message', 'Data BPKB berhasil diupdate!');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()
                ->route(
                    'kelola_data.bpkb.edit',
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
