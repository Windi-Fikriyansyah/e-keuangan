<?php

namespace App\Http\Controllers\BAST;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class BPKBController extends Controller
{
    public function index()
    {
        $daftarTandaTangan = DB::table('masterTtd')
            ->where(['kodeSkpd' => Auth::user()->kd_skpd])
            ->get();

        $daftarTandaTanganKepala = DB::table('masterTtd')
            ->where(['kode' => 'PPKD'])
            ->get();

        return view('bast.bpkb.index', compact('daftarTandaTangan', 'daftarTandaTanganKepala'));
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
            ->select('a.*', 'b.namaSkpd')
            ->leftJoin('masterSkpd as b', 'a.kodeSkpd', '=', 'b.kodeSkpd')
            ->where(['a.statusVerifPenyelia' => '1'])
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
                if ($row->statusBast == '1' && $row->statusPengembalian != '1') {
                    $btn = '<a onclick="hapus(\'' . $row->nomorBast . '\',\'' . $row->kodeSkpd . '\')" class="btn btn-md btn-danger" style="margin-right:4px"><span class="fa-fw select-all fas"></span></a>';
                } else {
                    $btn = '';
                }
                $btn .= '<a onclick="cetak(\'' . $row->nomorBast . '\',\'' . $row->kodeSkpd . '\')" class="btn btn-md btn-dark" style="margin-right:4px"><span class="fa-fw select-all fas"></span></a>';

                if (!($row->statusBast == '1' && $row->statusPengembalian == '1')) {
                    $btn .= '<a class="btn btn-md btn-primary pengajuan"><span class="fa-fw select-all fas"></span></a>';
                }
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function simpan(Request $request)
    {
        $selectedData = $request->selectedData;
        $tanggalBast = $request->tanggalBast;

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

            $statusBast = $pinjamanBpkb
                ->first()
                ->statusBast;

            $nomor = collect(DB::select("SELECT case when max(nomor+1) is null then 1 else max(nomor+1) end as nomor from (
                select nomorUrutBast nomor, 'Urut BAST BPKB' ket from pinjamanBpkb
                union all
                select nomorUrutBast nomor, 'Urut BAST Sertifikat' ket from pinjamanSertifikat
                )
                z"))
                ->first();

            $nomorBast = '000.2.3.2/' . $nomor->nomor . '/BAST/BPKAD-Aset';

            if ($statusBast == '1') {
                $pinjamanBpkb
                    ->update([
                        'tanggalBast' => $tanggalBast,
                    ]);
            } else {
                $pinjamanBpkb
                    ->update([
                        'nomorBast' => $nomorBast,
                        'tanggalBast' => $tanggalBast,
                        'statusBast' => '1',
                        'nomorUrutBast' => $nomor->nomor
                    ]);
            }


            DB::commit();
            return response()->json([
                'message' => $statusBast == '1' ? 'BAST berhasil diubah' : 'BAST berhasil ditambahkan'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $statusBast == '1' ? 'Error, BAST tidak berhasil diubah' : "Error, BAST tidak berhasil ditambahkan",
            ], 500);
        }
    }

    public function hapus(Request $request)
    {
        $nomorBast = $request->nomorBast;
        $kodeSkpd = $request->kodeSkpd;

        // CEK BAST SUDAH PENGEMBALIAN
        $pinjamanBpkb = DB::table('pinjamanBpkb')
            ->where([
                'nomorBast' => $nomorBast,
                'kodeSkpd' => $kodeSkpd,
            ])
            ->first();

        if ($pinjamanBpkb->statusPengembalian == '1') {
            return response()->json([
                'message' => 'Hapus BAST tidak dapat dilakukan, BAST telah dikembalikan!'
            ], 500);
        }

        DB::beginTransaction();
        try {
            DB::table('pinjamanBpkb')
                ->where([
                    'nomorBast' => $nomorBast,
                    'kodeSkpd' => $kodeSkpd,
                ])
                ->lockForUpdate()
                ->first();

            DB::table('pinjamanBpkb')
                ->where([
                    'nomorBast' => $nomorBast,
                    'kodeSkpd' => $kodeSkpd,
                ])
                ->update([
                    'nomorBast' => '',
                    'tanggalBast' => '',
                    'statusBast' => '',
                    'nomorUrutBast' => ''
                ]);

            DB::commit();
            return response()->json([
                'message' => 'BAST berhasil dihapus'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'BAST tidak berhasil dihapus'
            ], 500);
        }
    }

    public function cetak(Request $request)
    {
        $nomorBast = $request->nomorBast;
        $tandaTangan = $request->tandaTangan;
        $tandaTangan2 = $request->tandaTangan2;
        $tipe = $request->tipe;
        $tanggalTtd = $request->tanggalTtd;
        $kodeSkpd = Auth::user()->kd_skpd;

        $data = [
            'dataSkpd' => DB::table('masterSkpd')
                ->select('namaSkpd')
                ->where(['kodeSkpd' => $kodeSkpd])
                ->first(),
            'dataPeminjaman' => DB::table('pinjamanBpkb')
                ->where([
                    'nomorBast' => $nomorBast,
                    'kodeSkpd' => $kodeSkpd
                ])
                ->first(),
            'tandaTangan' => DB::table('masterTtd')
                ->where(['nip' => $tandaTangan])
                ->first(),
            'tandaTangan2' => DB::table('masterTtd')
                ->where([
                    'nip' => $tandaTangan2,
                    'kode' => 'PPKD'
                ])
                ->first(),
            'tipe' => $tipe,
            'tanggalTtd' => $tanggalTtd,
        ];

        $view = view('bast.bpkb.cetak')->with($data);

        if ($tipe == 'layar') {
            return $view;
        } else if ($tipe == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOrientation('portrait')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('FormPenyerahanBPKB.pdf');
        }
    }
}
