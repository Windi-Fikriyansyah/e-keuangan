<?php

namespace App\Http\Controllers\Pengembalian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BPKBController extends Controller
{
    public function index()
    {
        return view('pengembalian.bpkb.index');
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
        $query = DB::table('pinjamanBpkb as a')
            ->select('a.*', 'b.namaSkpd', 'c.statusPinjam')
            ->leftJoin('masterSkpd as b', 'a.kodeSkpd', '=', 'b.kodeSkpd')
            ->join('masterBpkb as c', function ($join) {
                $join->on('a.nomorRegister', '=', 'c.nomorRegister');
                $join->on('a.kodeSkpd', '=', 'c.kodeSkpd');
            })
            ->where(['a.statusBast' => '1'])
            ->get();

        // Search
        // $search = $request->search;
        // $query = $query->where(function ($query) use ($search) {
        //     $query->orWhere('nomorSurat', 'like', "%" . $search . "%");
        // });

        // $orderByName = 'nomorSurat';
        // switch ($orderColumnIndex) {
        //     case '0':
        //         $orderByName = 'nomorSurat';
        //         break;
        // }
        // $query = $query->orderBy($orderByName, $orderBy);
        // $recordsFiltered = $recordsTotal = $query->count();
        // $users = $query->skip($skip)->take($pageLength)->get();

        return DataTables::of($query)
            ->addColumn('aksi', function ($row) {
                $btn = '<a class="btn btn-md btn-primary kembali"><span class="fa-fw select-all fas"></span></a>';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function verifikasi(Request $request)
    {
        $selectedData = $request->selectedData;
        $tanggalPengembalian = $request->tanggalPengembalian;
        $tipe = $request->tipe;

        $dataPeminjaman = DB::table('pinjamanBpkb')
            ->where([
                'nomorSurat' => $selectedData['nomorSurat'],
                'nomorRegister' => $selectedData['nomorRegister'],
                'kodeSkpd' => $selectedData['kodeSkpd']
            ])
            ->first();

        // CEK TELAH DIPINJAM LAGI
        if ($dataPeminjaman->statusPinjamLagi == '1') {
            return response()->json([
                'status' => true,
                'message' => 'Batal Verifikasi tidak dapat dilakukan, data telah dibuat peminjaman lagi! Silahkan refresh!'
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
                $pinjamanBpkb
                    ->update([
                        'statusPengembalian' => '1',
                        'tanggalPengembalian' => $tanggalPengembalian,
                        'statusPinjamLagi' => '0'
                    ]);

                DB::table('masterBpkb')
                    ->where([
                        'nomorRegister' => $selectedData['nomorRegister'],
                        'kodeSkpd' => $selectedData['kodeSkpd'],
                    ])
                    ->update([
                        'statusPinjam' => '0'
                    ]);
            } else {
                $pinjamanBpkb
                    ->update([
                        'statusPengembalian' => '0',
                        'tanggalPengembalian' => '',
                    ]);

                DB::table('masterBpkb')
                    ->where([
                        'nomorRegister' => $selectedData['nomorRegister'],
                        'kodeSkpd' => $selectedData['kodeSkpd'],
                    ])
                    ->update([
                        'statusPinjam' => '1'
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
