<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Rincian Objek</title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            color: #333;
            max-width: 100%;
            margin: 0 auto;
            padding: 0.8rem;
            background-color: #fff;
            overflow-x: hidden;
            font-size: 11px;
        }

        .header {
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 0.5rem;
        }

        .header h1 {
            font-size: 14px;
            font-weight: 700;
            line-height: 1.6;
            margin-bottom: 0.3rem;
        }

        .table-container {
            width: 100%;
            overflow-x: hidden;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            font-size: 12px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #ddd;
            padding: 4px 6px;
            word-break: break-word;
            vertical-align: top;
        }

        .main-table th {
            background-color: #4a90e2;
            color: white;
            font-weight: 600;
            text-align: left;
            padding: 6px;
        }

        .main-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .numbers {
            text-align: right;
            font-family: 'Consolas', monospace;
            font-size: 11px;
            white-space: nowrap;
        }

        .footer {
            margin-top: 1.5rem;
            width: 100%;
        }

        .signature {
            float: right;
            width: 200px;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 11px;
        }

        .signature-content {
            margin-top: 0.8rem;
            padding: 0.8rem;
        }

        .signature-line {
            display: inline-block;
            width: 250px;
            /* Sesuaikan panjang garis */
            border-bottom: 1px solid #333;
            padding-bottom: 0.4rem;
            font-weight: 600;
            margin: 0.8rem auto;
            /* Menengahkan */
            white-space: nowrap;
            /* Mencegah teks turun ke baris baru */
            overflow: hidden;
            /* Opsional, mencegah teks keluar */
            text-overflow: ellipsis;
        }

        .signature-title {
            margin-top: 0.4rem;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>BUKU PEMBANTU SUB RINCIAN OBYEK BELANJA<br>
            PERIODE {{ isset($tanggalawal) ? \Carbon\Carbon::parse($tanggalawal)->translatedFormat('j F Y') : '' }} -
            {{ isset($tanggalakhir) ? \Carbon\Carbon::parse($tanggalakhir)->translatedFormat('j F Y') : '' }}
        </h1>
    </div>

    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
        <div>
            <strong>Nama Sub Kegiatan :</strong>
            {{ isset($kegiatan->kd_sub_kegiatan) ? $kegiatan->kd_sub_kegiatan : '' }} -
            {{ isset($kegiatan->nm_sub_kegiatan) ? $kegiatan->nm_sub_kegiatan : '' }}<br><br>
            <strong>Nama Rekening :</strong> {{ isset($jumlah_anggaran->kd_rek) ? $jumlah_anggaran->kd_rek : '' }} -
            {{ isset($jumlah_anggaran->nm_rek) ? $jumlah_anggaran->nm_rek : '' }}<br><br>
            <strong>Jumlah Anggaran :</strong> Rp
            {{ isset($jumlah_anggaran->anggaran_tahun) ? number_format($jumlah_anggaran->anggaran_tahun, 2, ',', '.') : '0,00' }}
        </div>
    </div>

    <div class="table-container">
        <table class="main-table">
            <thead>
                <tr>
                    <th>No SP2D/No SPP/No Tagih/No Bukti</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Sumber Dana</th>
                    <th>Nilai</th>
                </tr>
            </thead>
            <tbody>
                @php $totalInputan = 0; @endphp
                @if (isset($trhtransout) && count($trhtransout) > 0)
                    @foreach ($trhtransout as $item)
                        @php $totalInputan += isset($item->nilai) ? $item->nilai : 0; @endphp
                        <tr>
                            <td>
                                @if (isset($item->no_sp2d) && !empty($item->no_sp2d) && strpos($item->no_sp2d, 'LS') !== false)
                                    {{ $item->no_sp2d }}
                                @else
                                    {{ isset($item->no_bukti) ? $item->no_bukti : '' }}
                                @endif
                            </td>
                            <td>{{ isset($item->tgl_bukti) ? \Carbon\Carbon::parse($item->tgl_bukti)->translatedFormat('j F Y') : '' }}
                            </td>
                            <td>{{ isset($item->ket) ? $item->ket : '' }}</td>
                            <td>{{ isset($item->sumber_dana) ? $item->sumber_dana : 'Tidak Ada Sumber Dana' }}</td>
                            <td class="numbers">Rp
                                {{ isset($item->nilai) ? number_format($item->nilai, 2, ',', '.') : '0,00' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center;">Tidak ada data transaksi</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4">Jumlah Anggaran</th>
                    <th class="numbers">Rp
                        {{ isset($jumlah_anggaran->anggaran_tahun) ? number_format($jumlah_anggaran->anggaran_tahun, 2, ',', '.') : '0,00' }}
                    </th>
                </tr>
                <tr>
                    <th colspan="4">Total Inputan</th>
                    <th class="numbers">Rp {{ number_format($totalInputan, 2, ',', '.') }}</th>
                </tr>
                <tr>
                    <th colspan="4">Sisa Anggaran</th>
                    <th class="numbers">
                        Rp
                        {{ isset($jumlah_anggaran->anggaran_tahun)
                            ? number_format($jumlah_anggaran->anggaran_tahun - $totalInputan, 2, ',', '.')
                            : number_format(0 - $totalInputan, 2, ',', '.') }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer" style="display: flex; justify-content: space-between; gap: 20px;">
        <div class="signature" style="flex: 1; text-align: center;">
            <div></div>
            <div>{{ isset($pa_kpa->jabatan) ? $pa_kpa->jabatan : '' }}</div>
            <div class="signature-content">
                <div></div>
                <br>
                <div class="signature-line">{{ isset($pa_kpa->nama) ? $pa_kpa->nama : '' }}</div>
                <div class="signature-title">{{ isset($pa_kpa->pangkat) ? $pa_kpa->pangkat : '' }}</div>
                <div class="signature-title">NIP. {{ isset($pa_kpa->nip) ? $pa_kpa->nip : '' }}</div>
            </div>
        </div>
        <div class="signature" style="flex: 1; text-align: center;">
            <div>Pontianak,
                {{ isset($tanggalTtd) ? \Carbon\Carbon::parse($tanggalTtd)->translatedFormat('j F Y') : '' }}</div>
            <div>{{ isset($bendahara->jabatan) ? $bendahara->jabatan : '' }}</div>
            <div class="signature-content">
                <div></div>
                <br>
                <div class="signature-line">{{ isset($bendahara->nama) ? $bendahara->nama : '' }}</div>
                <div class="signature-title">{{ isset($bendahara->pangkat) ? $bendahara->pangkat : '' }}</div>
                <div class="signature-title">NIP. {{ isset($bendahara->nip) ? $bendahara->nip : '' }}</div>
            </div>
        </div>
    </div>
</body>

</html>
