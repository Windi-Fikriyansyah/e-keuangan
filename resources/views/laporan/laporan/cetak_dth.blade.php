<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan DTH</title>
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
            font-size: 12px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #ddd;
            padding: 4px 6px;
            word-break: normal;
            vertical-align: top;
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

        /* Column widths */
        .col-no { width: 40px; text-align: center; }
        .col-spm { width: 250px; }
        .col-nilai { width: 150px; }
        .col-sp2d { width: 150px; }
        .col-nilai-belanja { width: 150px; }
        .col-akun-belanja { width: 250px; }
        .col-akun-potongan { width: 150px; }
        .col-jenis { width: 60px; }
        .col-jumlah { width: 120px; }
        .col-npwp { width: 150px; }
        .col-rekanan { width: 200px; }
        .col-ntpn { width: 150px; }
        .col-no-billi { width: 150px; }
        .col-no-invoice { width: 150px; }
        .col-ket { width: 150px; }

        /* Utility classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .numbers {
            text-align: right;
            font-family: 'Consolas', monospace;
            font-size: 11px;
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
            DAFTAR TRANSAKSI HARIAN (DTH)<br>
            PERIODE {{ \Carbon\Carbon::parse($tanggalawal)->translatedFormat('j F Y') }} -
            {{ \Carbon\Carbon::parse($tanggalakhir)->translatedFormat('j F Y') }}
        </h1>
    </div>

    <div class="table-container">
        <table class="main-table">
            <thead>
                <tr>
                    <th class="col-no" rowspan="2">No.</th>
                    <th class="col-spm" colspan="2">SPM/SPD</th>
                    <th class="col-sp2d" colspan="2">SP2D</th>
                    <th class="col-akun-belanja" rowspan="2">Akun Belanja</th>
                    <th class="col-akun-potongan" colspan="3">Potongan Pajak</th>
                    <th class="col-npwp" rowspan="2">NPWP</th>
                    <th class="col-rekanan" rowspan="2">Nama Rekanan</th>
                    <th class="col-ntpn" rowspan="2">NTPN</th>
                    <th class="col-no-billi" rowspan="2">No Billing</th>
                    <th class="col-no-invoice" rowspan="2">No Invoice</th>
                    <th class="col-ket" rowspan="2">Ket</th>
                </tr>
                <tr>
                    <th>No. SPM</th>
                    <th>Nilai Belanja(Rp)</th>
                    <th>No. SP2D</th>
                    <th>Nilai Belanja (Rp)</th>
                    <th>Akun Potongan</th>
                    <th>Jenis</th>
                    <th>jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dth as $index => $data)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td></td>
                    <td class="text-right"></td>
                    <td>{{ $data->no_sp2d }}</td>
                    <td class="text-right nilai-belanja" >Rp. {{ number_format($data->total, 0, ',', '.') }}</td>
                    <td>{{ $data->kd_rek6 }}</td>
                    <td>{!! $data->kd_potong ?? '-' !!}</td>
                    <td class="text-center">{!! $data->nm_potong ?? '-' !!}</td>
                    <td class="text-right nilai-potongan">{!! $data->nilai ?? '-' !!}</td>
                    <td>{{ $data->npwp }}</td>
                    <td>{{ $data->nmrekan }}</td>
                    <td>{{ $data->no_ntpn }}</td>
                    <td>{!! $data->ebilling ?? '-' !!}</td>
                    <td></td>
                    <td>{{ $data->no_bukti }}</td>
                </tr>

                @endforeach
                <tr class="total-row">
                    <td colspan="2" class="text-right">Total</td>
                    <td class="text-right"></td>
                    <td></td>
                    <td class="text-right" id="total-nilai-belanja">Rp. 0</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right" id="total-nilai-potongan">Rp. 0</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
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
            let totalNilaiBelanja = 0;
            let totalNilaiPotongan = 0;

            // Get all rows except the header and total rows
            const rows = document.querySelectorAll('tbody tr:not(.total-row)');

            rows.forEach(function(row) {
                // Calculate total nilai belanja
                const nilaiBelanja = row.querySelector('.nilai-belanja');
                if (nilaiBelanja) {
                    const nilai = nilaiBelanja.innerText
                        .replace('Rp. ', '')
                        .replace(/\./g, '')
                        .replace(',', '.');
                    if (!isNaN(nilai)) {
                        totalNilaiBelanja += parseFloat(nilai);
                    }
                }

                // Calculate total nilai potongan - handle multiple values
                const nilaiPotongan = row.querySelector('.nilai-potongan');
                if (nilaiPotongan && nilaiPotongan.innerHTML !== '-') {
                    // Split by <br><hr> to get all values
                    const values = nilaiPotongan.innerHTML.split('<br><hr>');

                    values.forEach(value => {
                        if (value !== '-') {
                            const nilai = value
                                .replace('Rp. ', '')
                                .replace(/\./g, '')
                                .replace(',', '.');
                            if (!isNaN(nilai)) {
                                totalNilaiPotongan += parseFloat(nilai);
                            }
                        }
                    });
                }
            });

            // Update totals with proper formatting
            document.getElementById('total-nilai-belanja').innerText =
                'Rp. ' + totalNilaiBelanja.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });

            document.getElementById('total-nilai-potongan').innerText =
                'Rp. ' + totalNilaiPotongan.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
        }

        // Calculate totals when the page loads
        window.onload = calculateTotal;
    </script>
</body>
</html>
