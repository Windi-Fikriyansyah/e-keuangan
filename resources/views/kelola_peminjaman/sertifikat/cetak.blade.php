<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Form Peminjaman Sertifikat</title>
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
                {{ \Carbon\Carbon::parse($dataPeminjaman->tanggalPinjam)->locale('id')->isoFormat('D MMMM YYYY') }}</td>
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
            <td>Peminjaman Sertifikat</td>
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
                <p style="text-indent: 60px">Berdasarkan Pasal 6 Peraturan Bupati Kubu Raya Nomor 102 Tahun 2021 tentang Tata Cara Pengamanan Pedoman Pengelolaan Barang Milik Daerah, bahwa bukti kepemilikan tanah hanya dapat dipinjam untuk tujuan proses balik nama sertipikat menjadi hak pakai atas nama Pemerintah Daerah atau alih media dari Sertipikat Hak Pakai Analog ke Sertipikat Hak Pakai Elektronik. Oleh karena itu bersama ini Kami mengajukan permohonan peminjaman Sertipikat Hak Pakai/Milik/Guna Bangunan untuk keperluan............, selama 30 (tiga puluh) hari terhitung setelah ditandatanganinya BAST penyerahan SHP/SHM/SHGB dengan data sebagai berikut:
                </p>
            </td>
        </tr>

    </table>

    <table style="border-collapse: collapse;font-family: 'Open Sans' sans-serif; font-size:16px;width:100%"
        border="1">
        <thead>
            <tr>
                <th>No.</th>
                <th>No. Arsip Dokumen</th>
                <th>Peruntukan</th>
                <th>Pemegang Hak</th>
                <th>Nomor Sertifikat</th>
                <th>NIB</th>
                <th>Luas (M2)</th>
                <th>Tanggal Terbit / Diperiksa</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center">1</td>
                <td>{{ $dataPeminjaman->nomorRegister }}</td>
                <td>{{ $dataPeminjaman->peruntukan }}</td>
                <td>{{ $dataPeminjaman->pemegangHak }}</td>
                <td>{{ $dataPeminjaman->nomorSertifikat }}</td>
                <td>{{ $dataPeminjaman->NIB }}</td>
                <td>{{ $dataPeminjaman->luas }}</td>
                <td>{{ $dataPeminjaman->tanggal }}</td>
            </tr>
        </tbody>
    </table>

    <br>

    <table
        style="border-collapse: collapse;font-family: 'Open Sans' sans-serif; font-size:16px;width:100%;text-align:justify">
        <tr>
            <td colspan="3">Data Kepala Sub Bagian Tata Guna Tanah :</td>
        </tr>
        <tr>
            <td style="width: 200px">1. Nama</td>
            <td style="width:10px">:</td>
            <td>{{ $dataPeminjaman->namaKsbtgn }}</td>
        </tr>
        <tr>
            <td>2. NIP</td>
            <td>:</td>
            <td>{{ $dataPeminjaman->nipKsbtgn }}</td>
        </tr>
        <tr>
            <td>3. Nomor HP/WA</td>
            <td>:</td>
            <td>{{ $dataPeminjaman->noTelpKsbtgn }}</td>
        </tr>
        <tr>
            <td colspan="3" style="padding-top:10px;text-indent:60px">Demikian Surat Peminjaman Sertifikat ini, atas
                perhatian dan kerjasamanya diucapkan terimakasih.
            </td>
        </tr>
    </table>

    <br><br>
    <table class="table" style="width: 100%;font-size: 16px;font-family: 'Open Sans' sans-serif">
        <tr>
            <td style="width: 50%"></td>
            <td style="margin: 2px 0px;text-align: center">
                Asisten Pemerintahan dan Kesejahteraan Rakyat
            </td>
        </tr>
        <tr>
            <td style="width: 50%"></td>
            <td style="padding-bottom: 50px;text-align: center">
                Setda Kabupaten Kubu Raya,
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
