<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Rekap Pajak Bulanan</title>
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

        /* Header styles */
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

        /* Table styles */
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
            max-width: 150px;
            vertical-align: top;
        }

        .main-table th {
            background-color: #4a90e2;
            color: white;
            font-weight: 600;
            text-align: center;
            padding: 6px;
            white-space: nowrap;
        }

        .main-table td {
            text-align: center;
        }

        .main-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Utility classes */
        .numbers {
            text-align: right;
            font-family: 'Consolas', monospace;
            font-size: 11px;
            white-space: nowrap;
        }

        /* Footer and signature styles */
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
            border-bottom: 1px solid #333;
            padding-bottom: 0.4rem;
            font-weight: 600;
            margin: 0.8rem auto;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .signature-title {
            margin-top: 0.4rem;
            font-size: 10px;
            color: #666;
        }

        .notes {
            margin-top: 1rem;
            font-size: 11px;
        }

        /* Print styles */
        @media print {
            body {
                padding: 0.5rem;
            }

            .main-table {
                font-size: 9px;
            }

            .header h1 {
                font-size: 12px;
            }

            .numbers {
                font-size: 8px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>
            REKAPITULASI PAJAK BULANAN<br>
            BENDAHARA PENGELUARAN PEMBANTU<br>
            PERIODE {{ \Carbon\Carbon::parse($tanggalawal)->translatedFormat('j F Y') }} -
            {{ \Carbon\Carbon::parse($tanggalakhir)->translatedFormat('j F Y') }}
        </h1>
    </div>

    <div class="table-container">
        <table class="main-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Bulan</th>
                    <th>PPh Psl 21</th>
                    <th>PPh Psl 22</th>
                    <th>PPh Psl 23</th>
                    <th>PPh Psl 4</th>
                    <th>PPN</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($monthlySummary as $item)
                    <tr>
                        <td>{{ $item['no'] ? $item['no'] : '' }}</td>
                        <td>{{ $item['bulan'] }}</td>
                        <td class="numbers">{{ number_format($item['pph21'], 0, ',', '.') }}</td>
                        <td class="numbers">{{ number_format($item['pph22'], 0, ',', '.') }}</td>
                        <td class="numbers">{{ number_format($item['pph23'], 0, ',', '.') }}</td>
                        <td class="numbers">{{ number_format($item['pph4'], 0, ',', '.') }}</td>
                        <td class="numbers">{{ number_format($item['ppn'], 0, ',', '.') }}</td>
                        <td class="numbers">{{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="notes">
        <p><strong>Catatan:</strong></p>
        <p>- NTPN yang belum terinput: {{ $ntpnData->belum_terinput }} nomor</p>
        <p>- NTPN yang sudah terinput: {{ $ntpnData->sudah_terinput }} nomor</p>
        <p>- SPT sudah dibuat s/d Bulan: {{ $months[$ntpnData->bulan_terakhir] ?? '-' }}</p>
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
