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

        $daftarTandaTangan = DB::table('masterTtd')
        ->where(['kodeSkpd' => Auth::user()->kd_skpd])
        ->get();
        return view('verifikasi_bast.sertifikat.index', compact('daftarTandaTangan'));
    }

    public function load(Request $request)
    {

        $pinjamanSertifikat = DB::table('pinjamanSertifikat as a')
        ->select('a.nomorSurat','a.statusBast','a.nomorBast','namaKsbtgn', 'a.nomorRegister','a.statusVerifikasiOperator','a.statusVerifAdmin','a.statusVerifPenyelia','a.statusBast','a.statusPengembalian', 'a.nomorSertifikat', 'a.statusPengajuan', 'a.NIB', 'a.file', 'a.kodeSkpd','a.statusPengembalian', 'b.namaSkpd')
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
            $btn = '';

            // Add Hapus button
            if ($row->statusBast == '1' && $row->statusPengembalian != '1') {
                $btn .= '<a onclick="hapus(\'' . $row->nomorBast . '\',\'' . $row->kodeSkpd . '\')" class="btn btn-md btn-danger" style="margin-right:4px"><span class="fa-fw select-all fas"></span></a>';
            }

            // Add Cetak button
            $btn .= '<a onclick="cetak(\'' . $row->nomorBast . '\',\'' . $row->namaKsbtgn . '\',\'' . $row->nomorSurat . '\',\'' . $row->nomorRegister . '\')" class="btn btn-md btn-dark" style="margin-right:4px"><span class="fa-fw select-all fas"></span></a>';

            // Add Verif button
            if ($row->statusBast == '1') {
                $btn .= '<a onclick="verif(\'' . $row->nomorSurat . '\')" class="btn btn-md btn-success"><span class="fa-fw select-all fas"></span></a>';
            } else {
                $btn .= '<a onclick="verif(\'' . $row->nomorSurat . '\')" class="btn btn-md btn-primary"><span class="fa-fw select-all fas"></span></a>';
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
            ->select('a.tanggalPinjam','a.statusVerifikasiOperator','a.kodeSkpd','a.statusVerifAdmin','a.statusVerifPenyelia','a.statusBast','a.statusPengembalian', 'a.nomorSurat', 'a.nomorRegister', 'a.nomorSertifikat', 'a.NIB', 'a.tanggal', 'a.pemegangHak', 'a.luas', 'a.peruntukan', 'a.namaKsbtgn', 'a.nipKsbtgn','a.tanggalBast','a.tanggalVerifPenyelia','a.file', 'a.noTelpKsbtgn')
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

        $validated = $request->validate([
            'nomorSurat' => 'required|string',
            'tanggalBast' => 'required|date',
        ]);
        $nomor = collect(DB::select("SELECT case when max(nomor+1) is null then 1 else max(nomor+1) end as nomor from (
            select nomorUrutBast nomor, 'Urut BAST BPKB' ket from pinjamanBpkb
            union all
            select nomorUrutBast nomor, 'Urut BAST Sertifikat' ket from pinjamanSertifikat
            )
            z"))
            ->first();

        $nomorBast = '000.2.3.2/' . $nomor->nomor . '/BAST/BPKAD-Aset';

        DB::table('pinjamanSertifikat')
            ->where('nomorSurat', $validated['nomorSurat'])
            ->update([
                'statusBast' => 1,
                'nomorBast' => $nomorBast,
                'nomorUrutBast' => $nomor->nomor,
                'tanggalBast' => $validated['tanggalBast']
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

    public function cetakBast(Request $request)
    {
        $nomorBast = $request->nomorBast;
        $nomorSurat = $request->nomorSurat;
        $nomorRegister = $request->nomorRegister;
        $tandaTangan = $request->tandaTangan;
        $tipe = $request->tipe;
        $kodeSkpd = Auth::user()->kd_skpd;

        $data = [
            'dataSkpd' => DB::table('masterSkpd')
                ->select('namaSkpd')
                ->where(['kodeSkpd' => $kodeSkpd])
                ->first(),
            'dataPeminjaman' => DB::table('pinjamanSertifikat')
                ->where([
                    'nomorBast' => $nomorBast,
                    'nomorSurat' => $nomorSurat,
                    'nomorRegister' => $nomorRegister,
                    'kodeSkpd' => $kodeSkpd
                ])
                ->first(),
            'tandaTangan' => DB::table('masterTtd')
                ->where(['nip' => $tandaTangan])
                ->first(),
            'tipe' => $tipe
        ];

        $view = view('verifikasi_bast.sertifikat.cetak')->with($data);

        if ($tipe == 'layar') {
            return $view;
        } else if ($tipe == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOrientation('portrait')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('FormBastSertifikat.pdf');
        }
    }

    public function hapus(Request $request)
    {
        $nomorBast = $request->nomorBast;
        $kodeSkpd = $request->kodeSkpd;

        // CEK BAST SUDAH PENGEMBALIAN
        $pinjamanSertifikat = DB::table('pinjamanSertifikat')
            ->where([
                'nomorBast' => $nomorBast,
                'kodeSkpd' => $kodeSkpd,
            ])
            ->first();

        if ($pinjamanSertifikat->statusPengembalian == '1') {
            return response()->json([
                'message' => 'Hapus BAST tidak dapat dilakukan, BAST telah dikembalikan!'
            ], 500);
        }

        DB::beginTransaction();
        try {
            DB::table('pinjamanSertifikat')
                ->where([
                    'nomorBast' => $nomorBast,
                    'kodeSkpd' => $kodeSkpd,
                ])
                ->lockForUpdate()
                ->first();

            DB::table('pinjamanSertifikat')
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

}
