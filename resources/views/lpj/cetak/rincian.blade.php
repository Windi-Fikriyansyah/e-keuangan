<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CETAK RINCIAN</title>
    <style>
        #header>thead>tr>th {
            background-color: #CCCCCC;
        }
    </style>
</head>

<body>

    <div style="text-align: center">
        <table style="width: 100%">
            <tr>
                <td><b>LAPORAN PERTANGGUNG JAWABAN UANG PERSEDIAAN</b></td>
            </tr>
            <tr>
                <td><b>{{ Str::upper($bendahara->jabatan) }}</b></td>
            </tr>
        </table>
    </div>

    <div style="text-align: left;padding-top:20px">
        <table style="width: 100%">

                <tr>
                    <td>Periode</td>
                    <td>:</td>
                    <td>{{ $lpj->tgl_awal }} s/d {{ $lpj->tgl_akhir }}</td>
                </tr>
        </table>
    </div>
    <table style="border-collapse:collapse;width:100%" id="header" border="1">
        <thead>
            <tr>
                <th>NO</th>
                <th>KODE REKENING</th>
                <th>URAIAN</th>
                <th>JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no_kegiatan = 0;
                $total = 0;
            @endphp

            @foreach ($data_lpj as $kd_sub_kegiatan => $items)
                @php $no_kegiatan++; @endphp
                <tr>
                    <td style="text-align: center"><b>{{ $no_kegiatan }}</b></td>
                    <td><b>{{ $kd_sub_kegiatan }}</b></td>
                    <td><b>{{ $items->first()->nm_sub_kegiatan }}</b></td>
                    <td></td>
                </tr>

                @php $no_rekening = 0; @endphp
                @foreach ($items as $item)
                    @php
                        $no_rekening++;
                        $total += $item->nilai;
                    @endphp
                    <tr>
                        <td></td>
                        <td>{{ $kd_sub_kegiatan }}.{{ $item->kd_rek6 }}</td>
                        <td>{{ $item->nm_rek6 }}</td>
                        <td style="text-align: right">{{ number_format($item->nilai, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach


            @php
                $uang_persediaan_akhir = $persediaan->total_up - $total;
            @endphp

            <tr>
                <td></td>
                <td></td>
                <td style="text-align: right"><b>Uang Persediaan Awal Periode</b></td>
                <td style="text-align: right"><b>{{ number_format($persediaan->total_up, 2, ',', '.') }}</b></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td style="text-align: right"><b>Total</b></td>
                <td style="text-align: right"><b>{{ number_format($total, 2, ',', '.') }}</b></td>
            </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td style="text-align: right"><b>Uang Persediaan Akhir Periode</b></td>
                    <td style="text-align: right"><b>{{ number_format($uang_persediaan_akhir, 2, ',', '.') }}</b></td>
                </tr>
        </tbody>
    </table>
    <br>
    <div style="padding-top:20px">
        <table class="table" style="width: 100%">
            <tr>
                <td style="text-align: center">MENGETAHUI :</td>
                <td style="margin: 2px 0px;text-align: center">
                    Pontianak, {{ \Carbon\Carbon::parse($lpj->tgl_lpj)->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center">
                    {{ $pa_kpa->jabatan }}
                </td>
                <td style="padding-bottom: 50px;text-align: center">
                    {{ $bendahara->jabatan }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center">
                    <b><u>{{ $pa_kpa->nama }}</u></b> <br>
                    {{ $pa_kpa->pangkat }} <br>
                    NIP. {{ $pa_kpa->nip }}
                </td>
                <td style="text-align: center">
                    <b><u>{{ $bendahara->nama }}</u></b> <br>
                    {{ $bendahara->pangkat }} <br>
                    NIP. {{ $bendahara->nip }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
