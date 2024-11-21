<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Form Penyerahan BAST BPKB</title>
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
            <td align="center" style="font-size:18px;padding:-100px" width="93%"><strong>PEMERINTAH
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

    <table
        style="border-collapse: collapse;font-family: 'Open Sans' sans-serif; font-size:16px;width:100%;text-align:center">
        <tr>
            <td>
                <b><u>BERITA ACARA SERAH TERIMA BPKB</u></b>
            </td>
        </tr>
        <tr>
            <td>Nomor : {{ $dataPeminjaman->nomorBast }}</td>
        </tr>
    </table>

    <br>

    <table style="border-collapse: collapse;font-family: 'Open Sans' sans-serif; font-size:16px;width:100%">
        <tr>
            <td>
                Pada hari ini {{ \Carbon\Carbon::parse($tanggalTtd)->locale('id')->dayName }} tanggal
                {{ \Carbon\Carbon::parse($tanggalTtd)->locale('id')->isoformat('D') }} bulan
                {{ \Carbon\Carbon::parse($tanggalTtd)->locale('id')->isoformat('MMMM') }} tahun
                {{ \Carbon\Carbon::parse($tanggalTtd)->locale('id')->isoformat('Y') }}, yang
                bertanda tangan di bawah ini :
            </td>
        </tr>
    </table>

    <br>

    <table style="border-collapse: collapse;font-family: 'Open Sans' sans-serif; font-size:16px;width:100%">
        <tr>
            <td style="width: 2%">1.</td>
            <td style="width: 10%">Nama</td>
            <td style="width: 1%">:</td>
            <td>
                {{ $tandaTangan->nama }}
            </td>
        </tr>
        <tr>
            <td style="width: 2%"></td>
            <td style="width: 10%">NIP</td>
            <td>:</td>
            <td>
                {{ $tandaTangan->nip }}
            </td>
        </tr>
        <tr>
            <td style="width: 2%"></td>
            <td style="width: 10%">Jabatan</td>
            <td>:</td>
            <td>
                {{ $tandaTangan->jabatan }}
            </td>
        </tr>
        <tr>
            <td style="width: 2%"></td>
            <td style="width: 10%">Alamat</td>
            <td>:</td>
            <td>
                {{ $tandaTangan->alamat }}
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3">Yang selanjutnya disebut PIHAK PERTAMA</td>
        </tr>

        <tr>
            <td colspan="4" style="padding-top: 10px"></td>
        </tr>

        <tr>
            <td style="width: 2%">2.</td>
            <td style="width: 10%">Nama</td>
            <td>:</td>
            <td>
                {{ $dataPeminjaman->namaPbp }}
            </td>
        </tr>
        <tr>
            <td style="width: 2%"></td>
            <td style="width: 10%">NIP</td>
            <td>:</td>
            <td>
                {{ $dataPeminjaman->nipPbp }}
            </td>
        </tr>
        <tr>
            <td style="width: 2%"></td>
            <td style="width: 10%">Jabatan</td>
            <td>:</td>
            <td>Pengurus Barang Pengguna</td>
        </tr>
        <tr>
            <td style="width: 2%"></td>
            <td style="width: 10%">Alamat</td>
            <td>:</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3">Yang selanjutnya disebut PIHAK KEDUA</td>
        </tr>
    </table>

    <br>

    <table style="border-collapse: collapse;font-family: 'Open Sans' sans-serif; font-size:16px;width:100%">
        <tr>
            <td>PIHAK PERTAMA telah menyerahkan kepada PIHAK KEDUA Buku Pemilik Kendaraan Bermotor (BPKB) untuk
                keperluan {{ $dataPeminjaman->keperluan }} selama 7 (tujuh) hari berdasarkan Surat dari Pengguna Barang
                Nomor:
                {{ $dataPeminjaman->nomorBast }} tanggal
                {{ \Carbon\Carbon::parse($dataPeminjaman->tanggalBast)->locale('id')->isoFormat('D MMMM Y') }} hal
                Peminjaman BPKB, dengan
                data sebagai berikut:
            </td>
        </tr>
    </table>

    <br>

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
            <td>Semua dokumen tersebut telah diterima dalam keadaan baik dan lengkap.</td>
        </tr>
        <tr>
            <td style="padding-top:10px">Demikian Berita Acara Serah Terima BPKB ini dibuat dalam rangkap 2 (dua) untuk
                dipergunakan sebagaimana
                mestinya.</td>
        </tr>
    </table>

    <br><br>
    <table class="table" style="width: 100%;font-size: 16px;font-family: 'Open Sans' sans-serif">
        <tr>
            <td style="width: 50%;text-align:center">
                PIHAK KEDUA
            </td>
            <td style="margin: 2px 0px;text-align: center">
                PIHAK PERTAMA
            </td>
        </tr>
        <tr>
            <td style="padding-bottom: 50px;text-align: center">
                Yang Menerima,
            </td>
            <td style="padding-bottom: 50px;text-align: center">
                Yang Menyerahkan,
            </td>
        </tr>
        <tr>
            <td style="width: 50%;text-align: center">
                <b><u>{{ $dataPeminjaman->namaPbp }}</u></b> <br>
                NIP. {{ $dataPeminjaman->nipPbp }}
            </td>
            <td style="text-align: center">
                <b><u>{{ $tandaTangan->nama }}</u></b> <br>
                NIP. {{ $tandaTangan->nip }}
            </td>
        </tr>

        <tr>
            <td style="text-align: center;padding-top:20px" colspan="2">
                Mengetahui,
            </td>
        </tr>
        <tr>
            <td style="padding-bottom: 50px;text-align: center" colspan="2">
                Kepala Bidang Pengelolaan Aset Daerah <br>
                BPKAD Kabupaten Kubu Raya,
            </td>
        </tr>
        <tr>
            <td style="text-align: center" colspan="2">
                <b><u>{{ $tandaTangan2->nama }}</u></b> <br>
                NIP. {{ $tandaTangan2->nip }}
            </td>
        </tr>
    </table>
</body>

</html>
