<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CETAK SPTB</title>
</head>

<body>



    <br>
    <br>
    <div style="text-align: center">
        <table style="width: 100%">
            <tr>
                <td><u><b>SURAT PERNYATAAN TANGGUNG JAWABAN BELANJA (SPTB)</b></u></td>
            </tr>
        </table>
    </div>

    <div style="text-align: left;padding-top:20px">
        <table style="width: 100%">
            <tr>
                <td>1. OPD</td>
                <td>:</td>
                <td>{{ $kd_skpd }} - {{ $nm_skpd }}</td>
            </tr>
            <tr>
                <td>2. Satuan Kerja</td>
                <td>:</td>
                <td>{{ $kd_skpd }} - {{ $nm_skpd }}</td>
            </tr>
            <tr>
                <td>3. Tanggal/NO. DPA</td>
                <td>:</td>
                <td>-</td>
            </tr>
            <tr>
                <td>4. Tahun Anggaran</td>
                <td>:</td>
                <td>2025</td>
            </tr>
            <tr>
                <td>5. Jumlah Belanja</td>
                <td>:</td>
                <td>Rp. {{ number_format($jumlah_belanja, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <br>

    <table style="font-family: Open Sans; font-size:16px">
        <tr>
            <td>Yang bertanda tangan dibawah ini adalah Pengguna Anggaran Satuan Kerja {{ $nm_skpd }}
                Menyatakan bahwa
                saya bertanggung jawab penuh atas segala pengeluaran yang telah dibayar lunas oleh Bendahara Pengeluaran
                kepada yang berhak menerima, sebagimana tertera dalam Laporan Pertanggung jawaban Tambah Uang
                disampaikan oleh Bendahara Pengeluaran</td>
        </tr>
        <tr>
            <td style="height: 10px"></td>
        </tr>
        <tr>
            <td>Bukti-bukti belanja tertera dalam Laporan Pertanggung Jawaban Uang Disimpan sesuai ketentuan yang
                berlaku pada Satuan Kerja {{ $nm_skpd }}
                Untuk kelengkapan administrasi dan keperluan pemeriksaan aparat pengawasan Fungsional.</td>
        </tr>
        <tr>
            <td style="height: 10px"></td>
        </tr>
        <tr>
            <td>Demikian Surat Pernyataan ini dibuat dengan sebenarnya.</td>
        </tr>
    </table>

    <br>
    <br>
    <div style="padding-top:20px">
        <table class="table" style="width: 100%">
            <tr>
                <td style="width: 50%"></td>
                <td style="margin: 2px 0px;text-align: center">
                    Pontianak, {{ \Carbon\Carbon::parse($tanggal_ttd)->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td style="width: 50%"></td>
                <td style="padding-bottom: 50px;text-align: center">
                    {{ $bendahara->jabatan }}
                </td>
            </tr>
            <tr>
                <td style="width: 50%"></td>
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
