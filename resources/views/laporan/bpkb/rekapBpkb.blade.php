<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Rekap BPKB</title>
</head>

<body>
    <table style="border-collapse:collapse;font-family: 'Open Sans' sans-serif; font-size:18px" width="100%"
        align="center" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td rowspan="4" align="left" width="7%">
                <img src="{{ $tipe == 'pdf' ? public_path('image/logo.png') : asset('image/logo.png') }}" width="75"
                    height="100" />
            </td>
            <td align="left" style="font-size:18px" width="93%">&nbsp;</td>
        </tr>
        <tr>
            <td align="left" style="font-size:18px" width="93%"><strong>PEMERINTAH
                    KABUPATEN KUBU RAYA</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:18px;text-transform:uppercase">
                <strong>{{ $dataSkpd->namaSkpd }}</strong>
            </td>
        </tr>
        <tr>
            <td align="left" style="font-size:18px"><strong>&nbsp;</strong></td>
        </tr>
    </table>

    <hr>
    <br>

    <table style="border-collapse: collapse;font-family: 'Open Sans' sans-serif; font-size:17px;width:100%">
        <tr>
            <td style="text-align: center">
                <b>
                    REKAP BPKB <br>
                    {{ $pilihan == '0' ? '' : $dataSkpd->namaSkpd }}
                </b>
            </td>
        </tr>
    </table>

    <br><br>
    <table style="border-collapse: collapse;font-family: 'Open Sans' sans-serif; font-size:16px;width:100%"
        border="1">
        <thead>
            <tr>
                <th>NO REGISTER</th>
                <th>SKPD</th>
                <th>NO POLISI</th>
                <th>MERK</th>
                <th>TYPE</th>
                <th>JENIS</th>
                <th>MODEL</th>
                <th>TAHUN <br> PEMBUATAN</th>
                <th>TAHUN <br> PERAKITAN</th>
                <th>ISI</th>
                <th>WARNA</th>
                <th>NO RANGKA</th>
                <th>NO MESIN</th>
                <th>NO BPKB</th>
                <th>KETERANGAN</th>
                <th>TANGGAL PENYERAHAN</th>
                <th>KEBERADAAN</th>
                <th>NAMA PEMAKAI KENDARAAN</th>
                <th>NO PLAT YANG LAMA</th>
                <th>NO BPKB YANG LAMA</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataBpkb as $item)
                <tr>
                    <td>{{ $item->nomorRegister }}</td>
                    <td>{{ $item->namaSkpd }}</td>
                    <td>{{ $item->nomorPolisi }}</td>
                    <td>{{ $item->merk }}</td>
                    <td>{{ $item->tipe }}</td>
                    <td>{{ $item->jenis }}</td>
                    <td>{{ $item->model }}</td>
                    <td>{{ $item->tahunPembuatan }}</td>
                    <td>{{ $item->tahunPerakitan }}</td>
                    <td>{{ $item->isiSilinder }}</td>
                    <td>{{ $item->warna }}</td>
                    <td>{{ $item->nomorRangka }}</td>
                    <td>{{ $item->nomorMesin }}</td>
                    <td>{{ $item->nomorBpkb }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td></td>
                    <td>{{ $item->statusPinjam == '0' ? 'Di Aset' : 'Di Pinjam' }}</td>
                    <td>{{ $item->namaPemakai }}</td>
                    <td>{{ $item->nomorPolisiLama }}</td>
                    <td>{{ $item->nomorBpkbLama }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br><br>
    <table class="table" style="width: 100%;font-size: 16px;font-family: 'Open Sans' sans-serif">
        <tr>
            <td style="width: 50%"></td>
            <td style="margin: 2px 0px;text-align: center">
                Kuburaya, {{ \Carbon\Carbon::parse($tanggalTtd)->locale('id')->isoFormat('DD MMMM YYYY') }}
            </td>
        </tr>
        <tr>
            <td style="width: 50%"></td>
            <td style="padding-bottom: 50px;text-align: center">
                {{ $tandaTangan->jabatan }}
            </td>
        </tr>
        <tr>
            <td style="width: 50%"></td>
            <td style="text-align: center">
                <strong><u>{{ $tandaTangan->nama }}</u></strong> <br>
                {{ $tandaTangan->pangkat }} <br>
                NIP. {{ $tandaTangan->nip }}
            </td>
        </tr>
    </table>
</body>

</html>
