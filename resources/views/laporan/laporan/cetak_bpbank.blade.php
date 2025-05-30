<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan BP BANK</title>
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
            vertical-align: top;
        }

        .main-table th {
            background-color: #4a90e2;
            color: white;
            font-weight: 600;
            text-align: left;
            padding: 6px;
            white-space: nowrap;
        }

        .main-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Column widths */
        .col-no {
            width: 5%;
        }

        .col-date {
            width: 10%;
        }

        .col-number {
            width: 12%;
        }

        .col-desc {
            width: 33%;
        }

        .col-amount {
            width: 13%;
        }

        .col-balance {
            width: 14%;
        }

        /* Utility classes */
        .numbers {
            text-align: right;
            font-family: 'Consolas', monospace;
            font-size: 11px;
            white-space: nowrap;
        }

        .saldo-lalu {
            font-weight: 600;
            background-color: #f2f2f2;
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
            BUKU PEMBANTU BANK<br>
            PERIODE {{ \Carbon\Carbon::parse($tanggalawal)->translatedFormat('j F Y') }} -
            {{ \Carbon\Carbon::parse($tanggalakhir)->translatedFormat('j F Y') }}
        </h1>
    </div>

    <div class="table-container">
        <table class="main-table">
            <thead>
                <tr>
                    <th class="col-date">Tanggal</th>
                    <th class="col-number">No Bukti</th>
                    <th class="col-desc">Uraian</th>
                    <th class="col-amount">Penerimaan</th>
                    <th class="col-amount">Pengeluaran</th>
                    <th class="col-balance">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $saldo = $saldoLalu ?? 0;
                    $totalPenerimaan = 0;
                    $totalPengeluaran = 0;
                @endphp
                <tr class="saldo-lalu">
                    <td colspan="2"></td>
                    <td>Saldo Sebelumnya</td>
                    <td></td>
                    <td></td>
                    <td class="numbers">Rp {{ number_format($saldo, 2, ',', '.') }}</td>
                </tr>
                @foreach ($trhtransout as $item)
                    @php
                        $saldo += $item->terima - $item->keluar;
                        $totalPenerimaan += $item->terima;
                        $totalPengeluaran += $item->keluar;
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tgl_kas)->translatedFormat('j F Y') }}</td>
                        <td>{{ $item->no_sp2d }}</td>
                        <td>{{ $item->uraian }}</td>
                        <td class="numbers">Rp {{ number_format($item->terima, 2, ',', '.') }}</td>
                        <td class="numbers">Rp {{ number_format($item->keluar, 2, ',', '.') }}</td>
                        <td class="numbers">Rp {{ number_format($saldo, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL</td>
                    <td class="numbers">Rp {{ number_format($totalPenerimaan, 2, ',', '.') }}</td>
                    <td class="numbers">Rp {{ number_format($totalPengeluaran, 2, ',', '.') }}</td>
                    <td class="numbers">Rp {{ number_format($saldo, 2, ',', '.') }}</td>
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
