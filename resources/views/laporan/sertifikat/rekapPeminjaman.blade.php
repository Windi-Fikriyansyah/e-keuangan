<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Rekap Peminjaman Sertifikat</title>
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
                    REKAP Sertifikat <br>
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
                <th>No Registrasi</th>
                <th>Luas</th>
                <th>Alamat</th>
                <th>Hak</th>
                <th>Tanggal</th>
                <th>Nomor</th>
                <th>Penggunaan</th>
                <th>Asal Usul</th>
                <th>Keterangan</th>
                <th>Balik Nama</th>
                <th>Pengembalian</th>
                <th>Nama SKPD</th>
                {{-- <th>Tanggal Penyerahan</th>
                <th>Tanggal Pengembalian</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($dataSertifikat as $item)
                <tr>
                    <td>{{ $item->nomorRegister }}</td>
                    <td>{{ $item->luas }}</td>
                    <td>{{ $item->alamat }}</td>
                    <td>{{ $item->hak }}</td>
                    <td>{{ $item->tanggalSertifikat }}</td>
                    <td>{{ $item->nomorSertifikat }}</td>
                    <td>{{ $item->penggunaan }}</td>
                    <td>{{ $item->asalUsul }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td>{{ $item->balikNama == 0 ? 'Belum' : 'Sudah' }}</td>
                    <td>{{ $item->statusPengembalian == 0 ? 'Belum' : 'Sudah' }}</td>
                    <td>{{ $item->namaSkpd }}</td>
                    {{-- <td>{{ $item->tanggalPinjam }}</td>
                    <td>{{ $item->tanggalPeminjaman }}</td> --}}
                    <td></td>
                    <td></td>
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
