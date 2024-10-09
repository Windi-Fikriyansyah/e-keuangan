<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Serah Terima Sertifikat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            line-height: 1.5;
        }
        .header {
            display: flex; /* Use flexbox to align items */
            align-items: center; /* Center items vertically */
            justify-content: center; /* Center content horizontally */
            margin-bottom: 20px;
            position: relative; /* To allow absolute positioning of the logo */
        }
        .logo {
            width: 60px;
            height: auto;
            position: absolute; /* Position logo absolutely */
            left: 20px; /* Adjust as needed */
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }
        .subtitle {
            text-align: center;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
        }
        .no-border {
            border: none;
        }
        .signature {
        display: flex;
        justify-content: space-around; /* Mengatur jarak antar div */
        text-align: center; /* Memusatkan teks dalam setiap div */
        margin-top: 50px; /* Jarak atas jika perlu */
    }
    .signature div {
        margin: 20px; /* Jarak antara dua kolom */
    }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ $tipe == 'pdf' ? public_path('image/logo.png') : asset('image/logo.png') }}" alt="Logo" class="logo">
        <div>
            <p class="title">PEMERINTAH KABUPATEN KUBU RAYA</p>
            <p class="title">BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH</p>
            <p class="subtitle">Jalan Supadio (Kantor Bupati Kubu Raya), e-mail bpkadkuburayakab.go.id</p>
        </div>
    </div>

    <hr>

    <h2 style="text-align: center;">BERITA ACARA SERAH TERIMA SERTIFIKAT</h2>
    <p style="text-align: center;">Nomor: {{ $dataPeminjaman->nomorBast }}</p>

    <p>Pada hari ini .............. tanggal .............. Bulan .......... tahun Dua Ribu Dua Puluh ............ yang bertanda tangan di bawah ini:</p>

    <ol>
        <li>
            <table style="border-collapse: collapse; width: 100%;">
                <tr>
                    <td style="width: 150px; border: none;">Nama</td>
                    <td style="border: none;">: {{ $tandaTangan->nama }}</td>
                </tr>
                <tr>
                    <td style="border: none;">NIP</td>
                    <td style="border: none;">: {{ $tandaTangan->nip }}</td>
                </tr>
                <tr>
                    <td style="border: none;">Jabatan</td>
                    <td style="border: none;">: {{ $tandaTangan->jabatan }}</td>
                </tr>
                <tr>
                    <td style="border: none;">Alamat</td>
                    <td style="border: none;">: {{ $tandaTangan->alamat }}</td>
                </tr>
            </table>
            &nbsp;Yang selanjutnya disebut PIHAK PERTAMA
        </li>
        <br>
        <li>
            <table style="border-collapse: collapse; width: 100%;">
                <tr>
                    <td style="width: 150px; border: none;">Nama</td>
                    <td style="border: none;">: {{ $dataPeminjaman->namaKsbtgn }}</td>
                </tr>
                <tr>
                    <td style="border: none;">NIP</td>
                    <td style="border: none;">: {{ $dataPeminjaman->nipKsbtgn }}</td>
                </tr>
                <tr>
                    <td style="border: none;">Jabatan</td>
                    <td style="border: none;">: Asisten Pemerintahan dan Kesejahteraan Rakyat</td>
                </tr>
                <tr>
                    <td style="border: none;"></td>
                    <td style="border: none;">&nbsp;&nbsp;Sekretariat Daerah Kabupaten Kubu Raya</td>
                </tr>
                <tr>
                    <td style="border: none;">Alamat</td>
                    <td style="border: none;">: Jalan Supadio</td>
                </tr>
            </table>
            &nbsp;Yang selanjutnya disebut PIHAK KEDUA
        </li>
    </ol>



    <p>PIHAK PERTAMA telah menyerahkan kepada PIHAK KEDUA Sertifikat Hak Pakai/Milik/Guna Bangunan untuk keperluan .............. selama 30 (tiga puluh) hari berdasarkan Surat dari Asisten Pemerintahan dan Kesejahteraan Rakyat Sekretariat Daerah Kabupaten Kubu Raya Nomor: .............. tanggal .............. hal Peminjaman Sertifikat Hak Pakai/Milik/Guna Bangunan, dengan data sebagai berikut:</p>

    <table>
        <tr>
            <th>No.</th>
            <th>No. Register</th>
            <th>Peruntukan</th>
            <th>Pemegang Hak</th>
            <th>Nomor Sertifikat</th>
            <th>NIB</th>
            <th>Luas (m2)</th>
            <th>Tanggal Terbit / Diperiksa</th>
        </tr>
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
    </table>

    <p>Semua dokumen tersebut telah diterima dalam keadaan baik dan lengkap.</p>

    <p>Demikian Berita Acara Serah Terima Sertifikat ini dibuat dalam rangkap 2 (dua) untuk dipergunakan sebagaimana mestinya.</p>

    <div class="signature">
        <div>
            <p>PIHAK KEDUA<br>Yang Menerima,</p>
            <br><br><br>
            <p>{{ $dataPeminjaman->namaKsbtgn }}<br>NIP. {{ $dataPeminjaman->nipKsbtgn }}</p>
        </div>
        <div>
            <p>PIHAK PERTAMA<br>Yang Menyerahkan,</p>
            <br><br><br>
            <p>{{ $tandaTangan->nama }}<br>NIP. {{ $tandaTangan->nip }}</p>
        </div>
    </div>
</body>
</html>
