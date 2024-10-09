<?php

namespace App\Http\Controllers\VerifikasiOperator;

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

        return view('verifikasi_operator.sertifikat.index');
    }

    public function load(Request $request)
    {

        $pinjamanSertifikat = DB::table('pinjamanSertifikat as a')
        ->select('a.nomorSurat', 'a.nomorRegister','a.statusVerifikasiOperator', 'a.nomorSertifikat', 'a.statusPengajuan', 'a.NIB', 'a.file', 'a.kodeSkpd', 'b.namaSkpd')
        ->leftJoin('masterSkpd as b', 'a.kodeSkpd', '=', 'b.kodeSkpd')
        ->where('a.statusPengajuan', 1);


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
        ->where('statusPengajuan', 1)
        ->count();


    return DataTables::of($pinjamanSertifikat)
        ->addColumn('aksi', function ($row) {

            if ($row->statusVerifikasiOperator == '1') {
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
            ->select('a.tanggalPinjam','a.statusVerifikasiOperator','a.statusVerifAdmin', 'a.nomorSurat', 'a.nomorRegister', 'a.nomorSertifikat', 'a.NIB', 'a.tanggal', 'a.pemegangHak', 'a.luas', 'a.peruntukan','a.kodeSkpd','a.file', 'a.namaKsbtgn', 'a.nipKsbtgn','a.tanggalVerifikasiOperator', 'a.noTelpKsbtgn')
            ->where('a.nomorSurat', $nomorSurat)
            ->first();


        if ($pinjamanSertifikat) {
            return response()->json($pinjamanSertifikat);
        } else {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
    }

    public function verifikasi_operator(Request $request)
    {
        $validated = $request->validate([
            'nomorSurat' => 'required|string',
            'tanggalVerifikasiOperator' => 'required|date',
        ]);

        DB::table('pinjamanSertifikat')
            ->where('nomorSurat', $validated['nomorSurat'])
            ->update([
                'statusVerifikasiOperator' => 1,
                'userVerifikasiOperator' => Auth::user()->name,
                'tanggalVerifikasiOperator' => $validated['tanggalVerifikasiOperator']
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
        ->update(['statusVerifikasiOperator' => 0]);

        return response()->json(['success' => true]);
    }

}
