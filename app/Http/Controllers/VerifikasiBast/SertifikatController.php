<?php

namespace App\Http\Controllers\VerifikasiBast;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Crypt;

class SertifikatController extends Controller
{
    public function index()
    {
        return view('verifikasi_bast.sertifikat.index');
    }

    public function load(Request $request)
    {

        $pinjamanSertifikat = DB::table('pinjamanSertifikat as a')
        ->select('a.nomorSurat', 'a.nomorRegister','a.statusVerifikasiOperator','a.statusVerifAdmin','a.statusVerifPenyelia','a.statusBast', 'a.nomorSertifikat', 'a.statusPengajuan', 'a.NIB', 'a.file', 'a.kodeSkpd', 'b.namaSkpd')
        ->leftJoin('masterSkpd as b', 'a.kodeSkpd', '=', 'b.kodeSkpd')
        ->where('a.statusVerifPenyelia', 1);


        $search = $request->search;
        $pinjamanSertifikat = $pinjamanSertifikat->where(function ($pinjamanSertifikat) use ($search) {
            $pinjamanSertifikat->orWhere('nomorSurat', 'like', "%" . $search . "%")
            ->orWhere('nomorRegister', 'like', "%" . $search . "%")
            ->orWhere('nomorSertifikat', 'like', "%" . $search . "%")
            ->orWhere('b.namaSkpd', 'like', "%" . $search . "%");
        });


    $totalFiltered = $pinjamanSertifikat->count();


    $orderColumnIndex = $request->input('order.0.column');
    $orderColumnName = $request->input('columns')[$orderColumnIndex]['data'];
    $orderDirection = $request->input('order.0.dir');

    $pinjamanSertifikat = $pinjamanSertifikat
        ->orderBy($orderColumnName, $orderDirection)
        ->skip($request->input('start'))
        ->take($request->input('length'))
        ->get();
    $totalData = DB::table('pinjamanSertifikat')
        ->where('statusVerifPenyelia', 1)
        ->count();


    return DataTables::of($pinjamanSertifikat)
        ->addColumn('aksi', function ($row) {

            if ($row->statusBast == '1') {
                $btn = '<a onclick="verif(\'' . $row->nomorSurat . '\')" class="btn btn-md btn-success"><span class="fa-fw select-all fas"></span></a>';
            } else {
                $btn = '<a onclick="verif(\'' . $row->nomorSurat . '\')" class="btn btn-md btn-primary"><span class="fa-fw select-all fas"></span></a>';
            }
            return $btn;
        })
        ->with([
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered
        ])
        ->rawColumns(['aksi'])
        ->make(true);
    }

    public function verif(Request $request)
    {
        $nomorSurat = $request->input('nomorSurat');

        $pinjamanSertifikat = DB::table('pinjamanSertifikat as a')
            ->select('a.tanggalPinjam','a.statusVerifikasiOperator','a.statusVerifAdmin','a.statusVerifPenyelia','a.statusBast', 'a.nomorSurat', 'a.nomorRegister', 'a.nomorSertifikat', 'a.NIB', 'a.tanggal', 'a.pemegangHak', 'a.luas', 'a.peruntukan', 'a.namaKsbtgn', 'a.nipKsbtgn', 'a.noTelpKsbtgn')
            ->where('a.nomorSurat', $nomorSurat)
            ->first();


        if ($pinjamanSertifikat) {
            return response()->json($pinjamanSertifikat);
        } else {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
    }

    protected function generateNomorUrut()
    {

        DB::beginTransaction();
        try {
            $lastRecord = DB::table('pinjamanSertifikat')->orderBy('nomorUrutBast', 'desc')->first();
            $lastNomorUrut = $lastRecord ? intval($lastRecord->nomorUrutBast) : 0;
            $newNomorUrut = str_pad($lastNomorUrut + 1, 6, '0', STR_PAD_LEFT);
            DB::commit();

            return $newNomorUrut;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function verifikasi_bast(Request $request)
    {

        $nomorUrutBast = $this->generateNomorUrut();
        $nomorBast = '000.2.3.2/' . $nomorUrutBast . '/BAST/BPKAD-Aset';
        $validated = $request->validate([
            'nomorSurat' => 'required|string',

        ]);

        DB::table('pinjamanSertifikat')
            ->where('nomorSurat', $validated['nomorSurat'])
            ->update([
                'statusBast' => 1,
                'nomorBast' => $nomorBast,
                'nomorUrutBast' => $nomorUrutBast,
                'tanggalBast' => now()->setTimezone('Asia/Jakarta')
            ]);

        return response()->json(['success' => true]);
    }

    public function batalkan(Request $request)
    {
        $validated = $request->validate([
            'nomorSurat' => 'required|string',
        ]);

        DB::table('pinjamanSertifikat')
        ->where('nomorSurat', $validated['nomorSurat'])
        ->update(['statusBast' => 0]);

        return response()->json(['success' => true]);
    }

}
