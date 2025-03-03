<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Rincian CP</title>
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
            overflow-x: auto;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .table-container::-webkit-scrollbar {
            display: none;
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
            word-break: normal;
            vertical-align: middle;
            white-space: nowrap;
        }

        .main-table th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: 600;
            text-align: center;
            padding: 6px;
            white-space: nowrap;
        }

        .main-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Utility classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .numbers {
            text-align: right;
            font-family: 'Consolas', monospace;
            font-size: 9px;
            white-space: nowrap;
        }

        .total-row {
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
            REGISTER CP<br>
            PERIODE {{ \Carbon\Carbon::parse($tanggalawal)->translatedFormat('j F Y') }} -
            {{ \Carbon\Carbon::parse($tanggalakhir)->translatedFormat('j F Y') }}
        </h1>
    </div>

    <div class="table-container">
        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="3">NO</th>
                    <th rowspan="3">Tanggal CP</th>
                    <th rowspan="3">No STS</th>
                    <th rowspan="3">No SP2D</th>
                    <th rowspan="3">Uraian</th>
                    <th colspan="7">CP</th>
                    <th rowspan="3">Jumlah</th>
                </tr>
                <tr>
                    <th colspan="4">LS</th>
                    <th colspan="3">UP/GU/TU</th>
                </tr>
                <tr>

                    <th colspan="2">Gaji</th>
                    <th>Barang dan Jasa</th>
                    <th>Pihak Ketiga Lainnya</th>
                    <th>UP</th>
                    <th>GU</th>
                    <th>TU</th>
                </tr>
                <tr>

                    <th colspan="3"></th>
                    <th colspan="2"></th>
                    <th>Gaji<br>HKPG</th>
                    <th>Pot. Lain</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

                @forelse($trhtransout as $index => $data)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ isset($data->tgl_sts) ? \Carbon\Carbon::parse($data->tgl_sts)->format('d-m-Y') : '' }}</td>
                    <td>{{ $data->no_sts ?? $trhbku->no_kas }}</td>
                    <td>{{ $data->no_sp2d ?? $trhbku->no_sp2d }}</td>
                    <td>{{ $data->keterangan ?? $trhbku->nm_rek6}}</td>
                    {{-- <td>
                        @if (!empty($data->id_trhkasin_pkd))
                            {{ $data->uraian }}
                        @else
                            {{ $data->nm_rek6 }}
                        @endif
                    </td> --}}

                    <td class="text-right">{{ isset($data->gaji) && $data->gaji > 0 ? number_format($data->gaji, 2, ',', '.') : '0,00' }}</td>
            <!-- Potongan Lain -->
            <td class="text-right">{{ isset($data->pot_lain) && $data->pot_lain > 0 ? number_format($data->pot_lain, 2, ',', '.') : '0,00' }}</td>
            <!-- Barang dan Jasa -->
            <td class="text-right">{{ isset($data->barang_jasa) && $data->barang_jasa > 0 ? number_format($data->barang_jasa, 2, ',', '.') : '0,00' }}</td>
            <!-- Pihak Ketiga -->
            <td class="text-right">{{ isset($data->pihak_ketiga) && $data->pihak_ketiga > 0 ? number_format($data->pihak_ketiga, 2, ',', '.') : '0,00' }}</td>
            <!-- UP -->
            <td class="text-right">{{ isset($data->UP) && $data->UP > 0 ? number_format($data->UP, 2, ',', '.') : '0,00' }}</td>
            <!-- GU -->
            <td class="text-right">{{ isset($data->GU) && $data->GU > 0 ? number_format($data->GU, 2, ',', '.') : '0,00' }}</td>
            <!-- TU -->
            <td class="text-right">{{ isset($data->TU) && $data->TU > 0 ? number_format($data->TU, 2, ',', '.') : '0,00' }}</td>
            <!-- Total -->
            <td class="text-right">{{ isset($data->total) ? number_format($data->total, 2, ',', '.') : '0,00' }}</td>

                </tr>
                @empty
                <!-- Only show this if no data is returned -->
                <tr>
                    <td colspan="17" class="text-center">Data tidak ditemukan</td>
                </tr>
                @endforelse

                <!-- Static data rows as fallback or example -->

                <tr>
                    <td colspan="5" class="text-right">Jumlah Periode Ini</td>
                    <td class="text-right">{{ number_format($jumlahPeriodeTerpilih, 2, ',', '.') }}</td>
                    <td class="text-right">0,00</td>
                    <td class="text-right">0,00</td>
                    <td class="text-right">0,00</td>
                    <td class="text-right">0,00</td>
                    <td class="text-right">0,00</td>
                    <td class="text-right">0,00</td>
                    <td class="text-right">{{ number_format($jumlahPeriodeTerpilih, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right">Jumlah sampai Periode Ini</td>
                    <td class="text-right">{{ number_format($jumlahSampaiBulanTerpilih, 2, ',', '.') }}</td>
                    <td class="text-right">0,00</td>
                    <td class="text-right">0,00</td>
                    <td class="text-right">0,00</td>
                    <td class="text-right">0,00</td>
                    <td class="text-right">0,00</td>
                    <td class="text-right">0,00</td>
                    <td class="text-right">{{ number_format($jumlahSampaiBulanTerpilih, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
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

    <script>
        function calculateTotal() {
            // Function to calculate totals if needed
            // This can be customized based on your needs
        }

        // Calculate totals when the page loads
        window.onload = calculateTotal;
    </script>
</body>
</html>
