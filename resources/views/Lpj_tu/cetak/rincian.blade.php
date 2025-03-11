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
                <td><b>LAPORAN PERTANGGUNG JAWABAN TAMBAHAN UANG (TU)</b></td>
            </tr>
            <tr>
                <td><b>{{ Str::upper($bendahara->jabatan) }}</b></td>
            </tr>
        </table>
    </div>

    <div style="text-align: left;padding-top:20px">
        <table style="width: 100%">
            <tr>
                <td>Sub Kegiatan</td>
                <td>:</td>
                <td>{{ $program->kd_sub_kegiatan }} - {{ $program->nm_sub_kegiatan }}</td>
            </tr>
            <tr>
                <td>No SP2D</td>
                <td>:</td>
                <td>{{ $noSp2d }}</td>
            </tr>
        </table>
    </div>
    <table style="border-collapse:collapse;width:100%" id="header" border="1">
        <thead>
            <tr>
                <th>KODE REKENING</th>
                <th>URAIAN</th>
                <th>JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data_lpj_tu as $data)
            <tr>
                <td>{{ Str::substr($data->kd_rek6, -2) == '.1' ? '' : $data->kd_rek6 }}</td>
                <td>{{ $data->nm_rek6 }}</td>
                <td style="text-align: right">{{ number_format($data->nilai, 2, ',', '.') }}</td>
            </tr>
            @endforeach

            <tr>
                <td></td>
                <td><b>Total</b></td>
                <td style="text-align: right"><b>{{ number_format($total, 2, ',', '.') }}</b></td>
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
