<?php

namespace App\Http\Controllers\VerifikasiAdmin;

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

        return view('verifikasi_admin.sertifikat.index');
    }

    public function load(Request $request)
    {

        $pinjamanSertifikat = DB::table('pinjamanSertifikat as a')
        ->select('a.nomorSurat','a.statusTolak', 'a.nomorRegister','a.statusVerifikasiOperator','a.statusVerifAdmin', 'a.nomorSertifikat', 'a.statusPengajuan', 'a.NIB', 'a.file', 'a.kodeSkpd', 'b.namaSkpd')
        ->leftJoin('masterSkpd as b', 'a.kodeSkpd', '=', 'b.kodeSkpd')
        ->where('a.statusVerifikasiOperator', 1);


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
        ->where('statusVerifikasiOperator', 1)
        ->count();


    return DataTables::of($pinjamanSertifikat)
        ->addColumn('aksi', function ($row) {

            if ($row->statusVerifAdmin == '1' && $row->statusTolak !== "1") {
                $btn = '<a onclick="verif(\'' . $row->nomorSurat . '\')" class="btn btn-md btn-success"><span class="fa-fw select-all fas"></span></a>';
            } else if($row->statusTolak == "1"){
                $btn = '<a onclick="verif(\'' . $row->nomorSurat . '\')" class="btn btn-md btn-danger"><span class="fa-fw select-all fas"></span></a>';

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
            ->select('a.tanggalPinjam','a.statusTolak','a.statusVerifikasiOperator','a.statusVerifAdmin','a.statusVerifPenyelia', 'a.nomorSurat', 'a.nomorRegister', 'a.nomorSertifikat', 'a.NIB', 'a.tanggal', 'a.pemegangHak', 'a.luas', 'a.peruntukan', 'a.namaKsbtgn', 'a.nipKsbtgn','a.tanggalVerifAdmin','a.tanggalVerifikasiOperator','kodeSkpd','a.file', 'a.noTelpKsbtgn')
            ->where('a.nomorSurat', $nomorSurat)
            ->first();


        if ($pinjamanSertifikat) {
            return response()->json($pinjamanSertifikat);
        } else {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
    }

    public function verifikasi_admin(Request $request)
    {
        $validated = $request->validate([
            'nomorSurat' => 'required|string',
            'tanggalVerifAdmin' => 'required|date',
        ]);

        DB::table('pinjamanSertifikat')
            ->where('nomorSurat', $validated['nomorSurat'])
            ->update([
                'statusVerifAdmin' => 1,
                'userVerifAdmin' => Auth::user()->name,
                'tanggalVerifAdmin' => $validated['tanggalVerifAdmin']
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
        ->update(['statusVerifAdmin' => 0]);

        return response()->json(['success' => true]);
    }

    public function tolak(Request $request)
{
    $validated = $request->validate([
        'nomorSurat' => 'required|string',
        'nomorRegister' => 'required|string',
        'kodeSkpd' => 'required|string',
    ]);

    try {
        DB::transaction(function () use ($validated) {
            DB::table('pinjamanSertifikat')
                ->where('nomorSurat', $validated['nomorSurat'])
                ->update([
                    'statusVerifAdmin' => 0,
                    'statusTolak' => 1,
                    'statusPinjamLagi' => '0'
                ]);

            DB::table('masterSertifikat')
                ->where([
                    'nomorRegister' => $validated['nomorRegister'],
                    'kodeSkpd' => $validated['kodeSkpd'],
                ])
                ->update([
                    'statusPinjam' => 0,
                ]);
        });

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}


}
