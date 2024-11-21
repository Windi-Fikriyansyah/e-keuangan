<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Form Peminjaman BPKB</title>
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
            <td align="center" style="font-size:18px" width="93%"><strong>PEMERINTAH
                    KABUPATEN KUBU RAYA</strong></td>
        </tr>
        <tr>
            <td align="center" style="font-size:18px;text-transform:uppercase">
                <strong>{{ $dataSkpd->namaSkpd }}</strong>
            </td>
        </tr>
        <tr>
            <td align="left" style="font-size:18px"><strong>&nbsp;</strong></td>
        </tr>
    </table>

    <hr>
    <br>

    <table style="border-collapse: collapse;font-family: 'Open Sans' sans-serif; font-size:16px;width:100%">
        <tr>
            <td style="text-align: right">Sungai Raya,
                {{ \Carbon\Carbon::parse($dataPeminjaman->tanggalPinjam)->locale('id')->isoFormat('d MMMM YYYY') }}</td>
        </tr>
    </table>

    <table style="border-collapse: collapse;font-family: 'Open Sans' sans-serif; font-size:16px;width:100%">
        <tr>
            <td style="width: 100px">Nomor</td>
            <td>:</td>
            <td>{{ $dataPeminjaman->nomorSurat }}</td>
        </tr>
        <tr>
            <td style="width: 100px">Sifat</td>
            <td>:</td>
            <td>Biasa</td>
        </tr>
        <tr>
            <td style="width: 100px">Lampiran</td>
            <td>:</td>
            <td>-</td>
        </tr>
        <tr>
            <td style="width: 100px">Hal</td>
            <td>:</td>
            <td>Peminjaman BPKB</td>
        </tr>
    </table>

    <br><br>
    <table style="border-collapse: collapse;font-family: 'Open Sans' sans-serif; font-size:16px;width:100%">
        <tr>
            <td style="width: 5%">Yth.</td>
            <td>Sekretaris Daerah Kabupaten Kubu Raya</td>
        </tr>
        <tr>
            <td></td>
            <td>Selaku Pengelola Barang</td>
        </tr>
        <tr>
            <td></td>
            <td>u.p. Kepala BPKAD Kab. Kubu Raya</td>
        </tr>
        <tr>
            <td></td>
            <td>di Sungai Raya</td>
        </tr>
    </table>

    <table style="border-collapse: collapse;font-family: 'Open Sans' sans-serif; font-size:16px;width:100%">
        <tr>
            <td colspan="2">
                <p style="text-indent: 60px">Berdasarkan Pasal 15 Peraturan Bupati Kubu Raya Nomor 102 Tahun 2021
                    tentang Tata Cara Pengamanan
                    Pedoman
                    Pengelolaan Barang Milik Daerah, bahwa BPKB dapat dipinjam oleh Pengguna Barang untuk tujuan antara
                    lain:
                </p>
            </td>
        </tr>
        <tr>
            <td>1.</td>
            <td>Perubahan data Buku Pemilik Kendaraan Bermotor dan Surat Tanda Nomor Kendaraan Bermotor; dan/atau
            </td>
        </tr>
        <tr>
            <td>2.</td>
            <td>Perpanjangan Surat Tanda Nomor Kendaraan dan penggantian plat nomor kendaraan dinas.
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <p>Oleh karena itu bersama ini Kami mengajukan permohonan peminjaman BPKB untuk keperluan seperti yang
                    tersebut pada nomor 1 atau 2 diatas, selama 7 (tujuh) hari terhitung setelah ditandatanganinya BAST
                    penyerahan BPKB dengan data sebagai berikut:</p>
            </td>
        </tr>
    </table>

    <table style="border-collapse: collapse;font-family: 'Open Sans' sans-serif; font-size:16px;width:100%"
        border="1">
        <thead>
            <tr>
                <th>No.</th>
                <th>No. Arsip Dokumen</th>
                <th>Nomor Polisi</th>
                <th>Nomor Rangka</th>
                <th>Nomor BPKB</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center">1</td>
                <td>{{ $dataPeminjaman->nomorRegister }}</td>
                <td>{{ $dataPeminjaman->nomorPolisi }}</td>
                <td>{{ $dataPeminjaman->nomorRangka }}</td>
                <td>{{ $dataPeminjaman->nomorBpkb }}</td>
            </tr>
        </tbody>
    </table>

    <br>

    <table
        style="border-collapse: collapse;font-family: 'Open Sans' sans-serif; font-size:16px;width:100%;text-align:justify">
        <tr>
            <td colspan="3">Data Pengurus Barang Pengguna :</td>
        </tr>
        <tr>
            <td style="width: 200px">1. Nama</td>
            <td style="width:10px">:</td>
            <td>{{ $dataPeminjaman->namaPbp }}</td>
        </tr>
        <tr>
            <td>2. NIP</td>
            <td>:</td>
            <td>{{ $dataPeminjaman->nipPbp }}</td>
        </tr>
        <tr>
            <td>3. Nomor HP/WA</td>
            <td>:</td>
            <td>{{ $dataPeminjaman->nomorTelpPbp }}</td>
        </tr>
        <tr>
            <td colspan="3" style="padding-top:10px;text-indent:60px">Demikian Surat Peminjaman BPKB ini, atas
                perhatian dan
                kerjasamanya diucapkan terima
                kasih. </td>
        </tr>
    </table>

    <br><br>
    <table class="table" style="width: 100%;font-size: 16px;font-family: 'Open Sans' sans-serif">
        <tr>
            <td style="width: 50%"></td>
            <td style="margin: 2px 0px;text-align: center">
                Kepala Perangkat Daerah
            </td>
        </tr>
        <tr>
            <td style="width: 50%"></td>
            <td style="padding-bottom: 50px;text-align: center">
                Selaku Pengguna Barang,
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
