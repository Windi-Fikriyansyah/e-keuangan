<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan BKU</title>
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
            font-size: 10px;
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
        .col-no { width: 5%; }
        .col-date { width: 10%; }
        .col-number { width: 12%; }
        .col-desc { width: 33%; }
        .col-amount { width: 13%; }
        .col-balance { width: 14%; }

        /* Utility classes */
        .numbers {
            text-align: right;
            font-family: 'Consolas', monospace;
            font-size: 9px;
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
            width: 250px; /* Sesuaikan panjang garis */
            border-bottom: 1px solid #333;
            padding-bottom: 0.4rem;
            font-weight: 600;
            margin: 0.8rem auto; /* Menengahkan */
            white-space: nowrap; /* Mencegah teks turun ke baris baru */
            overflow: hidden; /* Opsional, mencegah teks keluar */
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
            BUKU KAS UMUM PENGELUARAN<br>
            PERIODE {{ \Carbon\Carbon::parse($tanggalawal)->translatedFormat('j F Y') }} -
            {{ \Carbon\Carbon::parse($tanggalakhir)->translatedFormat('j F Y') }}
        </h1>
    </div>

    <div class="table-container">
        <table class="main-table">
            <thead>
                <tr>
                    <th class="col-no">NO</th>
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
                    $rowNumber = 1;
                    $totalTerima = 0;
                    $totalKeluar = 0;
                    $utangBelanjaItems = [];
                @endphp
                <tr class="saldo-lalu">
                    <td colspan="3"></td>
                    <td>Saldo Lalu</td>
                    <td></td>
                    <td></td>
                    <td class="numbers">Rp {{ number_format($saldo, 2, ',', '.') }}</td>
                </tr>
                @foreach ($trhtransout as $item)
                    <tr>
                        <td>{{ $rowNumber++ }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tgl_kas)->translatedFormat('j F Y') }}</td>
                        <td>{{ $item->no_sp2d }}</td>
                        <td><strong>{{ $item->uraian }}</strong></td>
                        <td class="numbers"></td>
                        <td class="numbers"></td>
                        <td class="numbers"></td> {{-- Tidak menampilkan saldo di sini --}}
                    </tr>
                    @php
                        $key = $item->no_kas . '-' .
                        ($item->id_trhkasin_pkd ?? '0') . '-' .
                        ($item->id_trhtransout ?? '0') . '-' .
                        ($item->id_trmpot ?? '0') . '-' .
                        ($item->id_strpot ?? '0');

                        $hasUtangBelanja = false;
                        $utangBelanjaDetails = [];
                    @endphp
                    @if (isset($detailGrouped[$key]))
                        @foreach ($detailGrouped[$key] as $detail)
                            @php
                                $saldo += $detail->terima - $detail->keluar;
                                $totalTerima += $detail->terima;
                                $totalKeluar += $detail->keluar;

                                // Check if nm_rek6 contains "utang belanja"
                                if (stripos($detail->nm_rek6, 'utang belanja') !== false) {
                                    $hasUtangBelanja = true;
                                    $utangBelanjaDetails[] = $detail;
                                }
                            @endphp
                            <tr class="child-row">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td> - {{ $detail->nm_rek6 }}</td>
                                <td class="numbers">Rp {{ number_format($detail->terima, 2, ',', '.') }}</td>
                                <td class="numbers">Rp {{ number_format($detail->keluar, 2, ',', '.') }}</td>
                                <td class="numbers">Rp {{ number_format($saldo, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach

                        @if ($hasUtangBelanja)
                            @php
                                // Store the parent item and its utang belanja details for later rendering
                                $utangBelanjaItems[] = [
                                    'parent' => $item,
                                    'details' => $utangBelanjaDetails,
                                    'key' => $key
                                ];
                            @endphp
                        @endif
                    @elseif($item->terima != 0 || $item->keluar != 0)
                        @php
                            $saldo += $item->terima - $item->keluar;
                            $totalTerima += $item->terima;
                            $totalKeluar += $item->keluar;
                        @endphp
                        <tr class="child-row">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td> - {{ $item->uraian }}</td>
                            <td class="numbers">Rp {{ number_format($item->terima, 2, ',', '.') }}</td>
                            <td class="numbers">Rp {{ number_format($item->keluar, 2, ',', '.') }}</td>
                            <td class="numbers">Rp {{ number_format($saldo, 2, ',', '.') }}</td>
                        </tr>
                    @endif
                @endforeach

                {{-- Render duplicated utang belanja items --}}
                @foreach ($utangBelanjaItems as $utangItem)
                    <tr class="utang-belanja-parent">
                        <td>{{ $rowNumber++ }}</td>
                        <td>{{ \Carbon\Carbon::parse($utangItem['parent']->tgl_kas)->translatedFormat('j F Y') }}</td>
                        <td>{{ $utangItem['parent']->no_sp2d }}</td>
                        <td><strong>{{ $utangItem['parent']->uraian }} (Transaksi CP)</strong></td>
                        <td class="numbers"></td>
                        <td class="numbers"></td>
                        <td class="numbers"></td>
                    </tr>

                    @foreach ($utangItem['details'] as $detail)
                        @php
                            // Continue calculating the saldo from where the main section left off
                            $saldo += $detail->terima - $detail->keluar;
                            // Also add to the totals
                            $totalTerima += $detail->terima;
                            $totalKeluar += $detail->keluar;
                        @endphp
                        <tr class="utang-belanja-child-row">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td> - {{ $detail->nm_rek6 }}</td>
                            <td class="numbers">Rp {{ number_format($detail->terima, 2, ',', '.') }}</td>
                            <td class="numbers">Rp {{ number_format($detail->keluar, 2, ',', '.') }}</td>
                            <td class="numbers">Rp {{ number_format($saldo, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endforeach

                <tr class="total-row">
                    <td colspan="4"><strong>Saldo Kas di Bendahara Pengeluaran/Bendahara Pengeluaran Pembantu Periode ini</strong></td>
                    <td class="numbers"><strong>Rp {{ number_format($totalTerima, 2, ',', '.') }}</strong></td>
                    <td class="numbers"><strong>Rp {{ number_format($totalKeluar, 2, ',', '.') }}</strong></td>
                    <td class="numbers"><strong>Rp {{ number_format($saldo, 2, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <br>

    <div class="saldo-summary">
        <p><strong>Terdiri dari:</strong></p>
    <table style="width: 30%; border-collapse: collapse;">
        <tr>
            <td>1. Saldo Tunai</td>
            <td style="text-align: right;">Rp {{ number_format(0, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td>2. Saldo Bank</td>
            <td style="text-align: right;">Rp {{ number_format($saldo, 2, ',', '.') }}</td>
        </tr>

        <tr>
            <td>3. Surat Berharga</td>
            <td style="text-align: right;">Rp {{ number_format(0, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td>4. Saldo Pajak</td>
            <td style="text-align: right;">Rp {{ number_format(0, 2, ',', '.') }}</td>
        </tr>
    </table>
    </div>
    </div>

    <div class="footer" style="display: flex; justify-content: space-between; gap: 20px;">
        <div class="signature" style="flex: 1; text-align: center;">
            <div>Pontianak, {{ \Carbon\Carbon::parse($tanggalTtd)->translatedFormat('j F Y') }}</div>
            <div>{{ $pa_kpa->jabatan}}</div>
            <div class="signature-content">
                <div></div>
                <br>
                <div class="signature-line">{{$pa_kpa->nama}}</div>
                <div class="signature-title">{{$pa_kpa->pangkat}}</div>
                <div class="signature-title">NIP. {{$pa_kpa->nip}}</div>
            </div>
        </div>
        <div class="signature" style="flex: 1; text-align: center;">
            <div>Pontianak, {{ \Carbon\Carbon::parse($tanggalTtd)->translatedFormat('j F Y') }}</div>
            <div>{{ $bendahara->jabatan}}</div>
            <div class="signature-content">
                <div></div>
                <br>
                <div class="signature-line">{{$bendahara->nama}}</div>
                <div class="signature-title">{{$bendahara->pangkat}}</div>
                <div class="signature-title">NIP. {{$bendahara->nip}}</div>
            </div>
        </div>
    </div>
</body>
</html>
