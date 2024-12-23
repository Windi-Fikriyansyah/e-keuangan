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

class HistoryBpkbController extends Controller
{
    public function index()
    {
        return view('kelola_data.history_bpkb.index');
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
        $query = DB::table('HistoryBpkb as a')
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
                $btn = '<a href="' . route("kelola_data.historybpkb.edit", ['no_register' => Crypt::encrypt($row->nomorRegister), 'kd_skpd' => Crypt::encrypt($row->kodeSkpd), 'nomorPolisi' => Crypt::encrypt($row->nomorPolisi)]) . '" class="btn btn-md btn-warning" style="margin-right:4px"><span class="fa-fw select-all fas"></span></a>';


                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }




    public function edit($nomorRegister, $kodeSkpd, $nomorPolisi)
    {
        $nomorRegister = Crypt::decrypt($nomorRegister);
        $kodeSkpd = Crypt::decrypt($kodeSkpd);
        $nomorPolisi = Crypt::decrypt($nomorPolisi);

        $daftarSkpd = DB::table('masterSkpd')
            ->get();

        $dataBpkb = DB::table('HistoryBpkb')
            ->where(['nomorRegister' => $nomorRegister, 'kodeSkpd' => $kodeSkpd, 'nomorPolisi' => $nomorPolisi])
            ->first();

        return view('kelola_data.history_bpkb.edit', compact('daftarSkpd', 'dataBpkb'));
    }





}
