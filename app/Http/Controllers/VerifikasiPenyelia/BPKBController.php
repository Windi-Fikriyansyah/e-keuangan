<?php

namespace App\Http\Controllers\VerifikasiPenyelia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BPKBController extends Controller
{
    public function index()
    {
        return view('verifikasi_penyelia.bpkb.index');
    }

    public function load(Request $request)
    {
        // Page Length
        $pageNumber = ($request->start / $request->length) + 1;
        $pageLength = $request->length;
        $skip       = ($pageNumber - 1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        // get data from products table
        $query = DB::table('pinjamanBpkb as a')
            ->select('a.*', 'b.namaSkpd')
            ->leftJoin('masterSkpd as b', 'a.kodeSkpd', '=', 'b.kodeSkpd')
            ->where([
                'a.statusPengajuan' => '1',
                'a.statusVerifikasiOperator' => '1',
                'a.statusVerifAdmin' => '1'
            ]);

        // Search
        $search = $request->search;
        $query = $query->where(function ($query) use ($search) {
            $query->orWhere('nomorSurat', 'like', "%" . $search . "%");
        });

        $orderByName = 'nomorSurat';
        switch ($orderColumnIndex) {
            case '0':
                $orderByName = 'nomorSurat';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $users = $query->skip($skip)->take($pageLength)->get();

        return DataTables::of($users)
            ->addColumn('aksi', function ($row) {
                $btn = '<a class="btn btn-md btn-primary verifikasi"><span class="fa-fw select-all fas"></span></a>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function verifikasi(Request $request)
    {
        $selectedData = $request->selectedData;
        $tanggalVerifikasi = $request->tanggalVerifikasi;
        $tipe = $request->tipe;

        $dataPeminjaman = DB::table('pinjamanBpkb')
            ->where([
                'nomorSurat' => $selectedData['nomorSurat'],
                'nomorRegister' => $selectedData['nomorRegister'],
                'kodeSkpd' => $selectedData['kodeSkpd']
            ])
            ->first();

        // CEK TELAH BAST
        if ($dataPeminjaman->statusBast == '1') {
            return response()->json([
                'status' => true,
                'message' => 'Batal Verifikasi tidak dapat dilakukan, data telah dibuat BAST! Silahkan refresh!'
            ], 500);
        }

        DB::beginTransaction();
        try {
            DB::table('pinjamanBpkb')
                ->where([
                    'id' => $selectedData['id'],
                    'kodeSkpd' => $selectedData['kodeSkpd'],
                    'nomorSurat' => $selectedData['nomorSurat'],
                    'nomorRegister' => $selectedData['nomorRegister'],
                ])
                ->lockForUpdate()
                ->first();

            $pinjamanBpkb = DB::table('pinjamanBpkb')
                ->where([
                    'id' => $selectedData['id'],
                    'kodeSkpd' => $selectedData['kodeSkpd'],
                    'nomorSurat' => $selectedData['nomorSurat'],
                    'nomorRegister' => $selectedData['nomorRegister'],
                ]);

            if ($tipe === 'setuju') {
                $pinjamanBpkb->update([
                    'statusVerifPenyelia' => '1',
                    'userVerifPenyelia' => Auth::user()->name,
                    'tanggalVerifPenyelia' => $tanggalVerifikasi,
                ]);
            } else {
                $pinjamanBpkb->update([
                    'statusVerifPenyelia' => '0',
                    'userVerifPenyelia' => '',
                    'tanggalVerifPenyelia' => '',
                ]);
            }


            DB::commit();
            return response()->json([
                'message' => $tipe === 'setuju' ? 'Pengajuan berhasil di verifikasi' : 'Pengajuan berhasil batal verifikasi'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $tipe === 'setuju' ? 'Error, Pengajuan tidak berhasil di verifikasi' : "Error, Pengajuan tidak berhasil dibatalkan verifikasi",
            ], 500);
        }
    }
}
