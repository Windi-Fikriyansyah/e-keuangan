<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan SPJ Fungsional</title>
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
            overflow-x: auto;
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
            overflow-x: auto;
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
            border: 1px solid #888;
            padding: 4px 6px;
            word-break: normal;
            vertical-align: middle;
            text-align: center;
        }

        .main-table th {
            background-color: #eee;
            color: #333;
            font-weight: 600;
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

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
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

        .spj-gaji,
        .spj-barang-jasa,
        .spj-up-gu-tu {
            text-align: center;
        }

        .main-table .column-header {
            border-bottom: none;
        }

        .main-table .subheader {
            border-top: none;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>SPJ<br>
            PERIODE {{ \Carbon\Carbon::parse($tanggalawal)->translatedFormat('j F Y') }} -
            {{ \Carbon\Carbon::parse($tanggalakhir)->translatedFormat('j F Y') }}
        </h1>
    </div>

    <div class="table-container">
        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="2">Kode Rekening</th>
                    <th rowspan="2">Uraian</th>
                    <th rowspan="2">Jumlah Anggaran</th>
                    <th colspan="3" class="column-header">SPJ-LS Gaji</th>
                    <th colspan="3" class="column-header">SPJ-LS Barang & Jasa</th>
                    <th colspan="3" class="column-header">SPJ UP/GU/TU</th>
                    <th rowspan="2">Jumlah SPJ (LS+UP/GU/TU)</th>
                    <th rowspan="2">Sisa Pagu Anggaran</th>
                </tr>
                <tr class="subheader">
                    <th>s.d Bulan lalu</th>
                    <th>Bulan Ini</th>
                    <th>s.d Bulan Ini</th>
                    <th>s.d Bulan lalu</th>
                    <th>Bulan Ini</th>
                    <th>s.d Bulan Ini</th>
                    <th>s.d Bulan lalu</th>
                    <th>Bulan Ini</th>
                    <th>s.d Bulan Ini</th>
                </tr>
                <tr>
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>6</th>
                    <th>7</th>
                    <th>8</th>
                    <th>9</th>
                    <th>10</th>
                    <th>11</th>
                    <th>12</th>
                    <th>13</th>
                    <th>14</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedData = $trhtransout->groupBy('kd_sub_kegiatan')->sortKeys();
                    $totalAnggaranAll = 0;
                    $totalSpjLsGajiSdBulanLaluAll = 0;
                    $totalSpjLsGajiBulanIniAll = 0;
                    $totalSpjLsGajiSdBulanIniAll = 0;
                    $totalSpjBarangSdBulanLaluAll = 0;
                    $totalSpjBarangBulanIniAll = 0;
                    $totalSpjBarangSdBulanIniAll = 0;
                    $totalSpjUpSdBulanLaluAll = 0;
                    $totalSpjUpBulanIniAll = 0;
                    $totalSpjUpSdBulanIniAll = 0;
                    $totalSpjAll = 0;
                    $totalSisaPaguAll = 0;
                @endphp

                @foreach ($groupedData as $kd_sub_kegiatan => $items)
                    @php
                        $firstItem = $items->first();

                        // Calculate totals
                        $totalAnggaran = $items->sum('anggaran_tahun');

                        // SPJ-LS Gaji
                        $spjLsGajiSdBulanLalu = $items->sum('spj_ls_gaji_sd_bulan_lalu');
                        $spjLsGajiBulanIni = $items->sum('spj_ls_gaji_bulan_ini');
                        $spjLsGajiSdBulanIni = $spjLsGajiSdBulanLalu + $spjLsGajiBulanIni;

                        // SPJ-LS Barang & Jasa
                        $spjBarangSdBulanLalu = $items->sum('spj_ls_barang_sd_bulan_lalu');
                        $spjBarangBulanIni = $items->sum('spj_ls_barang_bulan_ini');
                        $spjBarangSdBulanIni = $spjBarangSdBulanLalu + $spjBarangBulanIni;

                        // SPJ UP/GU/TU
                        $spjUpSdBulanLalu = $items->sum('spj_up_gu_tu_sd_bulan_lalu');
                        $spjUpBulanIni = $items->sum('spj_up_gu_tu_bulan_ini');
                        $spjUpSdBulanIni = $spjUpSdBulanLalu + $spjUpBulanIni;

                        // Calculate totals
                        $totalSpj = $spjLsGajiSdBulanIni + $spjBarangSdBulanIni + $spjUpSdBulanIni;
                        $sisaPagu = $totalAnggaran - $totalSpj;

                        $totalAnggaranAll += $totalAnggaran;
                        $totalSpjLsGajiSdBulanLaluAll += $spjLsGajiSdBulanLalu;
                        $totalSpjLsGajiBulanIniAll += $spjLsGajiBulanIni;
                        $totalSpjLsGajiSdBulanIniAll += $spjLsGajiSdBulanIni;
                        $totalSpjBarangSdBulanLaluAll += $spjBarangSdBulanLalu;
                        $totalSpjBarangBulanIniAll += $spjBarangBulanIni;
                        $totalSpjBarangSdBulanIniAll += $spjBarangSdBulanIni;
                        $totalSpjUpSdBulanLaluAll += $spjUpSdBulanLalu;
                        $totalSpjUpBulanIniAll += $spjUpBulanIni;
                        $totalSpjUpSdBulanIniAll += $spjUpSdBulanIni;
                        $totalSpjAll += $totalSpj;
                        $totalSisaPaguAll += $sisaPagu;
                    @endphp

                    <!-- Row untuk Parent -->
                    <tr class="parent-row">
                        <td class="text-center">{{ $firstItem->kd_sub_kegiatan }}</td>
                        <td class="text-left"><strong>{{ $firstItem->nm_sub_kegiatan }}</strong></td>
                        <td class="numbers">{{ number_format($totalAnggaran, 2, ',', '.') }}</td>
                        <td class="numbers">{{ number_format($spjLsGajiSdBulanLalu, 2, ',', '.') }}</td>
                        <td class="numbers">{{ number_format($spjLsGajiBulanIni, 2, ',', '.') }}</td>
                        <td class="numbers">{{ number_format($spjLsGajiSdBulanIni, 2, ',', '.') }}</td>
                        <td class="numbers">{{ number_format($spjBarangSdBulanLalu, 2, ',', '.') }}</td>
                        <td class="numbers">{{ number_format($spjBarangBulanIni, 2, ',', '.') }}</td>
                        <td class="numbers">{{ number_format($spjBarangSdBulanIni, 2, ',', '.') }}</td>
                        <td class="numbers">{{ number_format($spjUpSdBulanLalu, 2, ',', '.') }}</td>
                        <td class="numbers">{{ number_format($spjUpBulanIni, 2, ',', '.') }}</td>
                        <td class="numbers">{{ number_format($spjUpSdBulanIni, 2, ',', '.') }}</td>
                        <td class="numbers">{{ number_format($totalSpj, 2, ',', '.') }}</td>
                        <td class="numbers">{{ number_format($sisaPagu, 2, ',', '.') }}</td>
                    </tr>

                    <!-- Row untuk Sub-Item -->
                    @foreach ($items->sortBy('kd_rek') as $sub)
                        @continue(empty($sub->kd_rek)) <!-- Pastikan nilainya tidak kosong -->
                        @php
                            // Calculate values for sub items
                            $subAnggaran = $sub->anggaran_tahun ?? 0;

                            // SPJ-LS Gaji - sub
                            $subSpjLsGajiSdBulanLalu = $sub->spj_ls_gaji_sd_bulan_lalu ?? 0;
                            $subSpjLsGajiBulanIni = $sub->spj_ls_gaji_bulan_ini ?? 0;
                            $subSpjLsGajiSdBulanIni = $subSpjLsGajiSdBulanLalu + $subSpjLsGajiBulanIni;

                            // SPJ-LS Barang & Jasa - sub
                            $subSpjBarangSdBulanLalu = $sub->spj_ls_barang_sd_bulan_lalu ?? 0;
                            $subSpjBarangBulanIni = $sub->spj_ls_barang_bulan_ini ?? 0;
                            $subSpjBarangSdBulanIni = $subSpjBarangSdBulanLalu + $subSpjBarangBulanIni;

                            // SPJ UP/GU/TU - sub
                            $subSpjUpSdBulanLalu = $sub->spj_up_gu_tu_sd_bulan_lalu ?? 0;
                            $subSpjUpBulanIni = $sub->spj_up_gu_tu_bulan_ini ?? 0;
                            $subSpjUpSdBulanIni = $subSpjUpSdBulanLalu + $subSpjUpBulanIni;

                            // Calculate totals for sub
                            $subTotalSpj = $subSpjLsGajiSdBulanIni + $subSpjBarangSdBulanIni + $subSpjUpSdBulanIni;
                            $subSisaPagu = $subAnggaran - $subTotalSpj;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $sub->kd_sub_kegiatan }}.{{ $sub->kd_rek }}</td>
                            <td class="text-left" style="padding-left: 20px;">└ {{ $sub->nm_rek ?? 'Tidak Ada Nama' }}
                            </td>
                            <td class="numbers">{{ number_format($subAnggaran, 2, ',', '.') }}</td>
                            <td class="numbers">{{ number_format($subSpjLsGajiSdBulanLalu, 2, ',', '.') }}</td>
                            <td class="numbers">{{ number_format($subSpjLsGajiBulanIni, 2, ',', '.') }}</td>
                            <td class="numbers">{{ number_format($subSpjLsGajiSdBulanIni, 2, ',', '.') }}</td>
                            <td class="numbers">{{ number_format($subSpjBarangSdBulanLalu, 2, ',', '.') }}</td>
                            <td class="numbers">{{ number_format($subSpjBarangBulanIni, 2, ',', '.') }}</td>
                            <td class="numbers">{{ number_format($subSpjBarangSdBulanIni, 2, ',', '.') }}</td>
                            <td class="numbers">{{ number_format($subSpjUpSdBulanLalu, 2, ',', '.') }}</td>
                            <td class="numbers">{{ number_format($subSpjUpBulanIni, 2, ',', '.') }}</td>
                            <td class="numbers">{{ number_format($subSpjUpSdBulanIni, 2, ',', '.') }}</td>
                            <td class="numbers">{{ number_format($subTotalSpj, 2, ',', '.') }}</td>
                            <td class="numbers">{{ number_format($subSisaPagu, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endforeach

                <tr class="total-row">
                    <td colspan="2" class="text-center"><strong>TOTAL</strong></td>
                    <td class="numbers">{{ number_format($totalAnggaranAll, 2, ',', '.') }}</td>
                    <td class="numbers">{{ number_format($totalSpjLsGajiSdBulanLaluAll, 2, ',', '.') }}</td>
                    <td class="numbers">{{ number_format($totalSpjLsGajiBulanIniAll, 2, ',', '.') }}</td>
                    <td class="numbers">{{ number_format($totalSpjLsGajiSdBulanIniAll, 2, ',', '.') }}</td>
                    <td class="numbers">{{ number_format($totalSpjBarangSdBulanLaluAll, 2, ',', '.') }}</td>
                    <td class="numbers">{{ number_format($totalSpjBarangBulanIniAll, 2, ',', '.') }}</td>
                    <td class="numbers">{{ number_format($totalSpjBarangSdBulanIniAll, 2, ',', '.') }}</td>
                    <td class="numbers">{{ number_format($totalSpjUpSdBulanLaluAll, 2, ',', '.') }}</td>
                    <td class="numbers">{{ number_format($totalSpjUpBulanIniAll, 2, ',', '.') }}</td>
                    <td class="numbers">{{ number_format($totalSpjUpSdBulanIniAll, 2, ',', '.') }}</td>
                    <td class="numbers">{{ number_format($totalSpjAll, 2, ',', '.') }}</td>
                    <td class="numbers">{{ number_format($totalSisaPaguAll, 2, ',', '.') }}</td>
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
