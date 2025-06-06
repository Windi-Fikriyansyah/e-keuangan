<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan REALISASI FISIK</title>
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
        <h1>REALISASI FISIK<br>
            PERIODE {{ \Carbon\Carbon::parse($tanggalawal)->translatedFormat('j F Y') }} -
            {{ \Carbon\Carbon::parse($tanggalakhir)->translatedFormat('j F Y') }}
        </h1>
    </div>



    <div class="table-container">
        <table class="main-table">
            <thead>
                <tr>
                    <th>KODE REKENING</th>
                    <th>URAIAN</th>
                    <th>ANGGARAN</th>
                    <th>REALISASI</th>
                    <th>SISA ANGGARAN</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedData = $trhtransout->groupBy('kd_kegiatan')->sortKeys();
                    $grandTotalAnggaran = 0;
                    $grandTotalRealisasi = 0;
                @endphp

                @foreach ($groupedData as $kd_kegiatan => $items)
                    @php
                        $firstItem = $items->first();
                        $totalAnggaran = $items->sum('anggaran_tahun');
                        $totalRealisasi = $items->sum('nilai');
                        $sisaAnggaran = $totalAnggaran - $totalRealisasi;
                        $persentase = $totalAnggaran > 0 ? ($totalRealisasi / $totalAnggaran) * 100 : 0;
                        $grandTotalAnggaran += $totalAnggaran;
                        $grandTotalRealisasi += $totalRealisasi;
                    @endphp

                    <!-- Row untuk Parent -->
                    <tr class="parent-row">
                        <td><strong>{{ $kd_kegiatan }}</strong></td>
                        <td><strong>{{ $firstItem->nm_kegiatan ?? 'Tidak Ada Nama' }}</strong></td>
                        <td class="numbers"><strong>{{ number_format($totalAnggaran, 2, ',', '.') }}</strong></td>
                        <td class="numbers"><strong>{{ number_format($totalRealisasi, 2, ',', '.') }}</strong></td>
                        <td class="numbers"><strong>{{ number_format($sisaAnggaran, 2, ',', '.') }}</strong></td>
                        <td class="numbers"><strong>{{ number_format($persentase, 2, ',', '.') }}%</strong></td>
                    </tr>

                    <!-- Row untuk Sub-Item -->
                    @foreach ($items->sortBy('kd_rek5') as $sub)
                        @php
                            $subSisaAnggaran = ($sub->anggaran_tahun ?? 0) - ($sub->nilai ?? 0);
                            $subPersentase =
                                ($sub->anggaran_tahun ?? 0) > 0 ? (($sub->nilai ?? 0) / $sub->anggaran_tahun) * 100 : 0;
                        @endphp
                        <tr>
                            <td>{{ $kd_kegiatan }}.{{ $sub->kd_rek5 ?? '-' }}</td>
                            <td style="padding-left: 20px;"> {{ $sub->nm_rek5 ?? 'Tidak Ada Nama' }}</td>
                            <td class="numbers">{{ number_format($sub->anggaran_tahun ?? 0, 2, ',', '.') }}</td>
                            <td class="numbers">{{ number_format($sub->nilai ?? 0, 2, ',', '.') }}</td>
                            <td class="numbers">{{ number_format($subSisaAnggaran, 2, ',', '.') }}</td>
                            <td class="numbers">{{ number_format($subPersentase, 2, ',', '.') }}%</td>
                        </tr>
                    @endforeach
                @endforeach

                @php
                    $grandSisaAnggaran = $grandTotalAnggaran - $grandTotalRealisasi;
                    $grandPersentase = $grandTotalAnggaran > 0 ? ($grandTotalRealisasi / $grandTotalAnggaran) * 100 : 0;
                @endphp

                <!-- Baris Total Keseluruhan -->
                <tr class="total-row">
                    <td colspan="2" style="text-align: right;"><strong>TOTAL</strong></td>
                    <td class="numbers"><strong>{{ number_format($grandTotalAnggaran, 2, ',', '.') }}</strong></td>
                    <td class="numbers"><strong>{{ number_format($grandTotalRealisasi, 2, ',', '.') }}</strong></td>
                    <td class="numbers"><strong>{{ number_format($grandSisaAnggaran, 2, ',', '.') }}</strong></td>
                    <td class="numbers"><strong>{{ number_format($grandPersentase, 2, ',', '.') }}%</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer" style="display: flex; justify-content: space-between; gap: 20px;">
        <div class="signature" style="flex: 1; text-align: center;">
            <div></div>
            <div>{{ $pa_kpa->jabatan }}</div>
            <div class="signature-content">
                <div></div>
                <br>
                <div class="signature-line">{{ $pa_kpa->nama }}</div>
                <div class="signature-title">{{ $pa_kpa->pangkat }}</div>
                <div class="signature-title">NIP. {{ $pa_kpa->nip }}</div>
            </div>
        </div>
        <div class="signature" style="flex: 1; text-align: center;">
            <div>Pontianak, {{ \Carbon\Carbon::parse($tanggalTtd)->translatedFormat('j F Y') }}</div>
            <div>{{ $bendahara->jabatan }}</div>
            <div class="signature-content">
                <div></div>
                <br>
                <div class="signature-line">{{ $bendahara->nama }}</div>
                <div class="signature-title">{{ $bendahara->pangkat }}</div>
                <div class="signature-title">NIP. {{ $bendahara->nip }}</div>
            </div>
        </div>
    </div>
</body>

</html>
