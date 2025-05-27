@extends('template.app')
@section('title', 'Edit Transaksi')
@section('content')
    <div class="page-content">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('message'))
            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-white"><i class='bx bxs-message-square-x'></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">Error</h6>
                        <div class="text-white">{{ session('message') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <h5>Edit Transaksi Pemindahbukuan Bank</h5>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('transaksi.update', Crypt::encrypt($transaksi->no_bukti)) }}"
                    id="formBpkb">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="details" id="hiddenDetails">
                    <input type="hidden" name="details_tujuan" id="hiddenDetailsTujuan">

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Kode OPD/UNIT</label>
                        <div class="col-sm-10">
                            <input type="text" disabled class="form-control"
                                value="{{ $transaksi->kd_skpd }} || {{ $transaksi->nm_skpd }}" id="jenis"
                                placeholder="Silahkan Pilih" autofocus>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Bukti</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('no_bukti') is-invalid @enderror readonlyInput" type="text"
                                placeholder="Tidak perlu diisi, otomatis" name="no_bukti" value="{{ $transaksi->no_bukti }}"
                                readonly>
                        </div>
                        <label class="col-sm-2 col-form-label">Tanggal Transaksi</label>
                        <div class="col-sm-4">
                            <input id="tgl_bukti" class="form-control @error('tgl_bukti') is-invalid @enderror"
                                type="date" placeholder="Tidak perlu diisi, otomatis" name="tgl_bukti"
                                value="{{ old('tgl_bukti', $transaksi->tgl_bukti) }}">
                            @error('tgl_bukti')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Jenis Beban</label>
                        <div class="col-sm-4">
                            <select class="form-select @error('jenis_beban') is-invalid @enderror" name="jenis_beban">
                                <option value="" disabled selected>Pilih Jenis Beban</option>
                                <option value="UP"
                                    {{ old('jenis_beban', $transaksi->jenis_beban) == 'UP' ? 'selected' : '' }}>UP</option>
                                <option value="GU"
                                    {{ old('jenis_beban', $transaksi->jenis_beban) == 'GU' ? 'selected' : '' }}>GU</option>
                                <option value="TU"
                                    {{ old('jenis_beban', $transaksi->jenis_beban) == 'TU' ? 'selected' : '' }}>TU</option>
                                <option value="GAJI"
                                    {{ old('jenis_beban', $transaksi->jenis_beban) == 'GAJI' ? 'selected' : '' }}>GAJI
                                </option>
                                <option value="Barang & Jasa"
                                    {{ old('jenis_beban', $transaksi->jenis_beban) == 'Barang & Jasa' ? 'selected' : '' }}>
                                    Barang & Jasa</option>
                            </select>
                            @error('jenis_beban')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Rekening Bank Bend</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput @error('rek_pengeluaran') is-invalid @enderror"
                                type="text" name="rek_pengeluaran" readonly value="{{ $rek_pengeluaran }}">
                            @error('rek_pengeluaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('ket') is-invalid @enderror" type="text" name="ket"
                                placeholder="Keterangan">{{ old('ket', $transaksi->ket) }}</textarea>
                            @error('ket')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3 sp2d-checkbox" style="display: none;">
                        <label class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <input type="checkbox" id="sp2d_langsung" name="sp2d_langsung" value="1"
                                class='form-check-input'>
                            <label for="sp2d_langsung">Tarik Otomatis Terima Sp2d</label>
                        </div>
                    </div>
                    <div class="row mb-3" id="no_transaksi_wrapper" style="display: none;">
                        <label class="col-sm-2 col-form-label">No Transaksi</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('no_transaksi') is-invalid @enderror" name="no_transaksi"
                                id="no_transaksi_input" style="width: 100%" disabled>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <input type="checkbox" id="terima_sp2d" name="jenis_terima_sp2d" value="1"
                                class='form-check-input'
                                {{ old('jenis_terima_sp2d', $transaksi->jenis_terima_sp2d) ? 'checked' : '' }}>
                            <label for="terima_sp2d">Terima SP2D</label>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <input type="checkbox" id="perlimpahan" name="jenis_perlimpahan" value="1"
                                class='form-check-input'
                                {{ old('jenis_perlimpahan', $transaksi->perlimpahan) ? 'checked' : '' }}>
                            <label for="perlimpahan">Perlimpahan</label>
                        </div>
                    </div>

                    <div class="mb-3 text-end">
                        <input type="hidden" name="total_belanja" id="hiddenTotalBelanja">
                        <input type="hidden" name="totalTransfer" id="hiddenTotalTransfer">
                        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                        <a href="{{ route('transaksi.index') }}" class="btn btn-warning">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="card shadow-sm p-3 rounded"
            style="background-color: #f8f9fa; border-radius: 50px 50px; margin-bottom: -1rem;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-bold text-primary">Rekening Belanja</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-success d-flex align-items-center" data-bs-toggle="modal"
                        data-bs-target="#inputKegiatanModal">
                        Tambah
                    </button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="pajak" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kegiatan</th>
                                <th>Kode Rek</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                                <th>Sumber</th>
                                <th id="aksiColumn">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($potonganDetails as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $detail->nm_sub_kegiatan }}</td>
                                    <td>{{ $detail->kd_rek6 }}</td>
                                    <td>{{ $detail->nm_rek6 }}</td>
                                    <td>{{ number_format($detail->nilai, 0, ',', '.') }}</td>
                                    <td>{{ $detail->nm_dana }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger delete-item" data-id="{{ $detail->id }}">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p id="totalBelanja" name="totalBelanja"
                    style="text-align: right; margin-top: 10px; font-size: 16px; font-weight: bold;">
                    <strong>Total belanja:</strong> Rp {{ number_format($transaksi->total, 0, ',', '.') }}
                </p>
                <input type="hidden" id="hiddenTotalBelanja" name="hiddenTotalBelanja"
                    value="{{ $transaksi->total }}">
                <p id="totalPotongan" name="totalPotongan"
                    style="text-align: right; margin-top: 10px; font-size: 16px; font-weight: bold;">
                    <strong>Total Potongan:</strong> Rp
                </p>
            </div>
        </div>
    </div>


    <div class="page-content">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('message'))
            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-white"><i class='bx bxs-message-square-x'></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">Error</h6>
                        <div class="text-white">{{ session('message') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card shadow-sm p-3 rounded"
            style="background-color: #f8f9fa; border-radius: 50px 50px; margin-bottom: -1rem;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-bold text-primary">Rekening Tujuan</h5>

                <div class="d-flex gap-2">

                    <button class="btn btn-success d-flex align-items-center" data-bs-toggle="modal"
                        data-bs-target="#inputKegiatanModal1">
                        Tambah
                    </button>

                </div>
            </div>
        </div>

        <div class="card">

            <div class="card-body">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="tabeltujuan" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Rek.Tujuan</th>
                                    <th>Nilai</th>
                                    <th id="aksiColumn">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <p id="totalTransfer" name="totalTransfer"
                        style="text-align: right; margin-top: 10px; font-size: 16px; font-weight: bold;">
                        <strong>Total Transfer:</strong> Rp 0
                    </p>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal for adding new items -->
    <div class="modal fade" id="inputKegiatanModal" tabindex="-1" aria-labelledby="inputKegiatanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inputKegiatanModalLabel">Input Rincian Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formInputKegiatan">
                        <div class="mb-3 d-flex align-items-center">
                            <div class="col-sm-12 d-flex flex-wrap align-items-center">
                                <input type="checkbox" id="pergeseran1" name="jenis_pergeseran[]" value="1"
                                    class="form-check-input me-2">
                                <label for="pergeseran1" class="me-3">Pergeseran I</label>

                                <input type="checkbox" id="pergeseran2" name="jenis_pergeseran[]" value="2"
                                    class="form-check-input me-2">
                                <label for="pergeseran2" class="me-3">Pergeseran II</label>

                                <input type="checkbox" id="pergeseran3" name="jenis_pergeseran[]" value="3"
                                    class="form-check-input me-2">
                                <label for="pergeseran3">Pergeseran III</label>
                            </div>
                        </div>

                        <div class="mb-3 d-flex align-items-center">
                            <label for="kd_sub_kegiatan" class="form-label me-5" style="min-width: 120px;">Sub
                                Kegiatan</label>
                            <select id="kd_sub_kegiatan" name="kd_sub_kegiatan"
                                class="form-select me-2 custom-border select2 @error('kd_sub_kegiatan') is-invalid @enderror"
                                style="width: 50%;" data-placeholder="Pilih Sub Kegiatan" required>
                                <option></option> <!-- Agar placeholder muncul -->
                                <!-- Options will be populated dynamically -->
                            </select>
                            <input type="text" disabled class="form-control custom-border" id="nm_sub_kegiatan"
                                placeholder="Nama Sub Kegiatan" style="width: 50%; margin-left: 15px;">
                        </div>


                        <div class="mb-3 d-flex align-items-center">
                            <label for="no_sp2d" class="form-label me-5" style="min-width: 120px;">Nomor SP2D</label>
                            <input type="text" class="form-control" id="no_sp2d" name="no_sp2d" required>

                        </div>

                        <div class="mb-3 d-flex align-items-center">
                            <label for="kd_rek" class="form-label me-5" style="min-width: 120px;">Rekening</label>
                            <select id="kd_rek" name="kd_rek"
                                class="form-select me-2 custom-border select2 @error('kd_rek') is-invalid @enderror"
                                style="width: 50%;" data-placeholder="Pilih Rekening" required>
                                <option></option> <!-- Agar placeholder muncul -->
                                <!-- Options will be populated dynamically -->
                            </select>
                            <input type="text" disabled class="form-control custom-border" id="nm_rek"
                                style="width: 50%; margin-left: 15px;">
                            <input type="hidden" class="form-control custom-border" id="id_sumberdana"
                                style="width: 50%; margin-left: 15px;">
                        </div>


                        <div class="mb-3 d-flex align-items-center">
                            <label for="kd_dana" class="form-label me-5" style="min-width: 120px;">SumberDana</label>
                            <select id="kd_dana" name="kd_dana"
                                class="form-select me-2 custom-border select2 @error('kd_dana') is-invalid @enderror"
                                style="width: 50%;" data-placeholder="Pilih Sumber Dana" required>
                                <option></option> <!-- Agar placeholder muncul -->
                                <!-- Options will be populated dynamically -->
                            </select>
                            <input type="text" disabled class="form-control custom-border" id="nm_dana"
                                style="width: 50%; margin-left: 15px;">
                        </div>

                        <div class="row mb-3">
                            <div class="col-4 d-flex align-items-center">
                                <label for="totalSPD" class="form-label mb-0 w-100">Total SPD</label>
                                <input type="text" class="form-control" id="totalSPD" name="totalSPD"
                                    oninput="formatRupiah(this); hitungSisa('totalSPD', 'realisasiSPD', 'sisaSPD');"
                                    disabled>
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="realisasiSPD" class="form-label mb-0 w-100">Realisasi</label>
                                <input type="text" disabled class="form-control" id="realisasiSPD"
                                    name="realisasiSPD"
                                    oninput="formatRupiah(this); hitungSisa('totalSPD', 'realisasiSPD', 'sisaSPD');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="sisaSPD" class="form-label mb-0 w-100">Sisa</label>
                                <input type="text" class="form-control" id="sisaSPD" name="sisaSPD" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-4 d-flex align-items-center">
                                <label for="totalAnggaranKas" class="form-label mb-0 w-100">Total Anggaran Kas</label>
                                <input type="text" disabled class="form-control" id="totalAnggaranKas"
                                    name="totalAnggaranKas"
                                    oninput="formatRupiah(this); hitungSisa('totalAnggaranKas', 'realisasiAnggaranKas', 'sisaAnggaranKas');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="realisasiAnggaranKas" class="form-label mb-0 w-100">Realisasi</label>
                                <input type="text" disabled class="form-control" id="realisasiAnggaranKas"
                                    name="realisasiAnggaranKas"
                                    oninput="formatRupiah(this); hitungSisa('totalAnggaranKas', 'realisasiAnggaranKas', 'sisaAnggaranKas');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="sisaAnggaranKas" class="form-label mb-0 w-100">Sisa</label>
                                <input type="text" class="form-control" id="sisaAnggaranKas" name="sisaAnggaranKas"
                                    disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-4 d-flex align-items-center">
                                <label for="anggaran" class="form-label mb-0 w-100">Anggaran</label>
                                <input type="text" disabled class="form-control" id="anggaran" name="anggaran"
                                    oninput="formatRupiah(this); hitungSisa('anggaran', 'realisasiAnggaran', 'sisaAnggaran');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="realisasiAnggaran" class="form-label mb-0 w-100">Realisasi</label>
                                <input type="text" disabled class="form-control" id="realisasiAnggaran"
                                    name="realisasiAnggaran"
                                    oninput="formatRupiah(this); hitungSisa('anggaran', 'realisasiAnggaran', 'sisaAnggaran');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="sisaAnggaran" class="form-label mb-0 w-100">Sisa</label>
                                <input type="text" class="form-control" id="sisaAnggaran" name="sisaAnggaran"
                                    disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-4 d-flex align-items-center">

                                <input type="hidden" class="form-control" id="rencanaPergeseranAnggaran"
                                    name="rencanaPergeseranAnggaran"
                                    oninput="formatRupiah(this); hitungSisa('rencanaPergeseranAnggaran', 'realisasiPergeseranAnggaran', 'sisaPergeseranAnggaran');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <input type="hidden" class="form-control" id="realisasiPergeseranAnggaran"
                                    name="realisasiPergeseranAnggaran"
                                    oninput="formatRupiah(this); hitungSisa('rencanaPergeseranAnggaran', 'realisasiPergeseranAnggaran', 'sisaPergeseranAnggaran');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <input type="hidden" class="form-control" id="sisaPergeseranAnggaran"
                                    name="sisaPergeseranAnggaran" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-4 d-flex align-items-center">
                                <label for="nilaisumberdana" class="form-label mb-0 w-100">Nilai Sumber Dana</label>
                                <input type="text" disabled class="form-control" id="nilaisumberdana"
                                    name="nilaisumberdana"
                                    oninput="formatRupiah(this); hitungSisa('nilaisumberdana', 'realisasinilaisumberdana', 'sisanilaisumberdana');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="realisasinilaisumberdana" class="form-label mb-0 w-100">Realisasi</label>
                                <input type="text" disabled class="form-control" id="realisasinilaisumberdana"
                                    name="realisasinilaisumberdana"
                                    oninput="formatRupiah(this); hitungSisa('nilaisumberdana', 'realisasinilaisumberdana', 'sisanilaisumberdana');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="sisanilaisumberdana" class="form-label mb-0 w-100">Sisa</label>
                                <input type="text" class="form-control" id="sisanilaisumberdana"
                                    name="sisanilaisumberdana" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-4 d-flex align-items-center">
                                <label for="statusAnggaran" class="form-label mb-0 w-100">Status Anggaran</label>
                                <input type="text" disabled class="form-control" id="statusAnggaran"
                                    name="statusAnggaran">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="statusAnggaranKas" class="form-label mb-0 w-100">Status Anggaran Kas</label>
                                <input type="text" disabled class="form-control" id="statusAnggaranKas"
                                    name="statusAnggaranKas">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="sisaKasBank" class="form-label mb-0 w-100">Sisa Kas Bank</label>
                                <input type="text" disabled class="form-control" id="sisaKasBank" name="sisaKasBank"
                                    value="{{ number_format($saldo_awal, 0, ',', '.') }}" oninput="formatRupiah(this);"
                                    readonly>
                            </div>
                        </div>

                        <input type="hidden" class="form-control" id="potonganLS" name="potonganLS"
                            oninput="formatRupiah(this);">


                        <input type="hidden" class="form-control" id="totalSisa" name="totalSisa"
                            oninput="formatRupiah(this);">


                        <input type="hidden" class="form-control" id="volume" name="volume">

                        <input type="hidden" class="form-control" id="satuan" name="satuan">

                        <div class="row mb-3">
                            <div class="col-4 d-flex align-items-center">

                            </div>
                            <div class="col-4 d-flex align-items-center">

                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="nilai" class="form-label mb-0 w-100">Nilai</label>
                                <input type="text" class="form-control" id="nilai" name="nilai"
                                    oninput="formatRupiah(this);" required>
                            </div>
                        </div>




                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="inputKegiatanModal1" tabindex="-1" aria-labelledby="inputKegiatanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inputKegiatanModalLabel">Input Rekening Tujuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formInputTujuan">
                        <div class="mb-3 d-flex align-items-center">
                            <label for="nilai_potongan" class="form-label me-5" style="min-width: 120px;">Nilai
                                Potongan</label>
                            <input type="text" class="form-control" id="nilai_potongan" name="nilai_potongan"
                                oninput="formatRupiah(this);" required>

                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <label for="kd_rek" class="form-label me-5" style="min-width: 120px;">Rekening
                                Tujuan</label>
                            <select id="rekeningtujuan" name="rekeningtujuan"
                                class="form-select me-2 custom-border select2 @error('rekeningtujuan') is-invalid @enderror"
                                style="width: 100%;" data-placeholder="Pilih Rekening" required>
                                <option></option> <!-- Agar placeholder muncul -->
                                <!-- Options will be populated dynamically -->
                            </select>

                        </div>


                        <div class="mb-3 d-flex align-items-center">
                            <label for="nm_rekening" class="form-label me-5" style="min-width: 120px;">A.N.
                                Rekening</label>
                            <input type="text" class="form-control readonlyInput" id="nm_rekening" name="nm_rekening"
                                required readonly>

                        </div>

                        <div class="mb-3 d-flex align-items-center">
                            <label for="nm_bank" class="form-label me-5" style="min-width: 120px;">Bank</label>
                            <input type="text" class="form-control readonlyInput" id="nm_bank" name="nm_bank"
                                required readonly>
                            <input type="hidden" class="form-control readonlyInput" id="bank" name="bank"
                                required readonly>

                        </div>


                        <div class="mb-3 d-flex align-items-center">
                            <label for="nilai_transfer" class="form-label me-5" style="min-width: 120px;">Nilai
                                Transfer</label>
                            <input type="text" class="form-control" id="nilai_transfer" name="nilai_transfer"
                                oninput="formatRupiah(this);" required>

                        </div>

                        <div class="mb-3 d-flex align-items-center">
                            <label for="ket_tpp" class="form-label me-5" style="min-width: 120px;">Keterangan
                                TPP</label>
                            <input type="text" class="form-control" id="ket_tpp" name="ket_tpp" required>

                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



@endsection
@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .readonlyInput {
            background-color: #e9ecef;
        }

        .custom-border {
            border: 1px solid #333;
            box-shadow: none;
        }

        .custom-border:focus {
            border-color: #000;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        }

        /* Fixed width styles for Select2 */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 5px;
            height: 38px;
            width: 100%;
        }

        .select2-dropdown {
            width: auto !important;
            min-width: 100% !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #495057;
            line-height: 28px;
            padding-left: 10px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
            right: 10px;
        }
    </style>

    <script>
        let dataSementara = [];
        let dataSementara1 = [];

        // Fungsi untuk menginisialisasi data yang sudah ada
        function initializeExistingData() {
            // Inisialisasi data rekening belanja yang sudah ada
            @if (isset($potonganDetails) && count($potonganDetails) > 0)
                @foreach ($potonganDetails as $detail)
                    dataSementara.push({
                        id: {{ $detail->id ?? 'null' }},
                        kd_sub_kegiatan: "{{ $detail->kd_sub_kegiatan ?? '' }}",
                        nm_sub_kegiatan: "{{ $detail->nm_sub_kegiatan ?? '' }}",
                        kd_rek: "{{ $detail->kd_rek6 ?? '' }}",
                        nm_rek: "{{ $detail->nm_rek6 ?? '' }}",
                        kd_dana: "{{ $detail->kd_dana ?? '' }}",
                        nm_dana: "{{ $detail->nm_dana ?? '' }}",
                        nilai: "{{ number_format($detail->nilai ?? 0, 0, ',', '.') }}",
                        no_sp2d: "{{ $detail->no_sp2d ?? '' }}",
                        // Tambahkan field lain sesuai kebutuhan
                        isExisting: true // Flag untuk membedakan data lama dan baru
                    });
                @endforeach
            @endif

            // Inisialisasi data rekening tujuan yang sudah ada (jika ada)
            // Sesuaikan dengan struktur data rekening tujuan yang ada
            @if (isset($rekeningTujuanDetails) && count($rekeningTujuanDetails) > 0)
                @foreach ($rekeningTujuanDetails as $detail)
                    dataSementara1.push({
                        id: {{ $detail->id ?? 'null' }},
                        nilai_potongan: "{{ number_format($detail->nilai_potongan ?? 0, 0, ',', '.') }}",
                        rekeningtujuan: "{{ $detail->rekening_tujuan ?? '' }}",
                        nm_rekening: "{{ $detail->nm_rekening ?? '' }}",
                        nm_bank: "{{ $detail->nm_bank ?? '' }}",
                        bank: "{{ $detail->bank ?? '' }}",
                        nilai_transfer: "{{ number_format($detail->nilai_transfer ?? 0, 0, ',', '.') }}",
                        ket_tpp: "{{ $detail->ket_tpp ?? '' }}",
                        isExisting: true
                    });
                @endforeach
            @endif
        }

        // Fungsi untuk update tabel rekening belanja
        function updateTable() {
            const tableBody = document.querySelector('#pajak tbody');
            if (!tableBody) return;

            // Kosongkan tbody kecuali baris yang sudah ada dari server
            const existingRows = tableBody.querySelectorAll('tr[data-existing="true"]');
            tableBody.innerHTML = '';

            // Kembalikan baris yang sudah ada
            existingRows.forEach(row => {
                tableBody.appendChild(row);
            });

            // Tambahkan baris baru dari dataSementara
            dataSementara.forEach((item, index) => {
                if (!item.isExisting) { // Hanya tambahkan data baru
                    const row = document.createElement('tr');
                    row.setAttribute('data-new', 'true');

                    // Hitung nomor urut berdasarkan total baris
                    const totalExistingRows = existingRows.length;
                    const rowNumber = totalExistingRows + index + 1;

                    row.innerHTML = `
                <td>${rowNumber}</td>
                <td>${item.nm_sub_kegiatan || ''}</td>
                <td>${item.kd_rek || ''}</td>
                <td>${item.nm_rek || ''}</td>
                <td>${item.nilai || '0'}</td>
                <td>${item.nm_dana || ''}</td>
                <td>
                    <button class="btn btn-sm btn-danger delete-new-item" data-index="${index}">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            `;
                    tableBody.appendChild(row);
                }
            });

            // Update total belanja
            updateTotalBelanja();
        }

        // Fungsi untuk update tabel rekening tujuan
        function updateTable1() {
            const tableBody = document.querySelector('#tabeltujuan tbody');
            if (!tableBody) return;

            // Kosongkan tbody kecuali baris yang sudah ada dari server
            const existingRows = tableBody.querySelectorAll('tr[data-existing="true"]');
            tableBody.innerHTML = '';

            // Kembalikan baris yang sudah ada
            existingRows.forEach(row => {
                tableBody.appendChild(row);
            });

            // Tambahkan baris baru dari dataSementara1
            dataSementara1.forEach((item, index) => {
                if (!item.isExisting) { // Hanya tambahkan data baru
                    const row = document.createElement('tr');
                    row.setAttribute('data-new', 'true');

                    // Hitung nomor urut berdasarkan total baris
                    const totalExistingRows = existingRows.length;
                    const rowNumber = totalExistingRows + index + 1;

                    row.innerHTML = `
                <td>${rowNumber}</td>
                <td>${item.nm_rekening || ''}</td>
                <td>${item.rekeningtujuan || ''}</td>
                <td>${item.nilai_transfer || '0'}</td>
                <td>
                    <button class="btn btn-sm btn-danger delete-new-tujuan-item" data-index="${index}">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            `;
                    tableBody.appendChild(row);
                }
            });

            // Update total transfer
            updateTotalTransfer();
        }

        // Fungsi untuk update total belanja
        function updateTotalBelanja() {
            const hiddenTotal = document.getElementById('hiddenTotalBelanja');
            const totalBelanjaElement = document.getElementById('totalBelanja');

            if (!hiddenTotal || !totalBelanjaElement) return;

            // Ambil total yang sudah ada dari server
            let totalExisting = parseFloat(hiddenTotal.value) || 0;

            // Tambahkan nilai dari data baru
            let totalNew = 0;
            dataSementara.forEach(item => {
                if (!item.isExisting) {
                    const nilai = parseFloat(item.nilai.replace(/\D/g, '')) || 0;
                    totalNew += nilai;
                }
            });

            const grandTotal = totalExisting + totalNew;
            totalBelanjaElement.innerHTML =
                `<strong>Total belanja:</strong> Rp ${new Intl.NumberFormat('id-ID').format(grandTotal)}`;
        }

        // Fungsi untuk update total transfer
        function updateTotalTransfer() {
            const totalTransferElement = document.getElementById('totalTransfer');
            if (!totalTransferElement) return;

            let total = 0;
            dataSementara1.forEach(item => {
                const nilai = parseFloat(item.nilai_transfer.replace(/\D/g, '')) || 0;
                total += nilai;
            });

            totalTransferElement.innerHTML =
                `<strong>Total Transfer:</strong> Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
        }

        // Fungsi untuk update total potongan
        function updateTotalPotongan() {
            const totalPotonganElement = document.getElementById('totalPotongan');
            if (!totalPotonganElement) return;

            let total = 0;
            dataSementara1.forEach(item => {
                const nilai = parseFloat(item.nilai_potongan.replace(/\D/g, '')) || 0;
                total += nilai;
            });

            totalPotonganElement.innerHTML =
                `<strong>Total Potongan:</strong> Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
        }

        // Fungsi validasi nilai
        function validateNilai() {
            const nilai = document.getElementById('nilai');
            const nilaiTransfer = document.getElementById('nilai_transfer');

            if (nilai && nilai.value) {
                const nilaiAngka = parseFloat(nilai.value.replace(/\D/g, '')) || 0;
                if (nilaiAngka <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Nilai harus lebih besar dari 0!'
                    });
                    return false;
                }
            }

            if (nilaiTransfer && nilaiTransfer.value) {
                const nilaiTransferAngka = parseFloat(nilaiTransfer.value.replace(/\D/g, '')) || 0;
                if (nilaiTransferAngka <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Nilai transfer harus lebih besar dari 0!'
                    });
                    return false;
                }
            }

            return true;
        }

        // Event handlers untuk delete item baru
        $(document).on('click', '.delete-new-item', function() {
            const index = parseInt($(this).data('index'));

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus item ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Hapus dari array
                    dataSementara.splice(index, 1);
                    // Update tabel
                    updateTable();

                    Swal.fire({
                        title: 'Berhasil',
                        text: 'Item berhasil dihapus',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        $(document).on('click', '.delete-new-tujuan-item', function() {
            const index = parseInt($(this).data('index'));

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus item ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Hapus dari array
                    dataSementara1.splice(index, 1);
                    // Update tabel
                    updateTable1();

                    // Enable kembali input nilai_potongan jika tidak ada data
                    if (dataSementara1.filter(item => !item.isExisting).length === 0) {
                        $("#nilai_potongan").prop("disabled", false);
                    }

                    updateTotalPotongan();

                    Swal.fire({
                        title: 'Berhasil',
                        text: 'Item berhasil dihapus',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        function formatRupiah(input) {
            let angka = input.value.replace(/\D/g, ""); // Hanya angka
            let rupiah = new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0
            }).format(angka);
            input.value = rupiah.replace("Rp", "").trim(); // Menghilangkan "Rp" agar mudah diproses
        }

        function hitungSisa(totalId, realisasiId, sisaId) {
            let total = parseFloat(document.getElementById(totalId).value.replace(/\D/g, "")) || 0;
            let realisasi = parseFloat(document.getElementById(realisasiId).value.replace(/\D/g, "")) || 0;
            let sisa = total - realisasi;

            document.getElementById(sisaId).value = new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0
            }).format(sisa).replace("Rp", "").trim();
        }
        $(document).ready(function() {




            $('#inputKegiatanModal').on('show.bs.modal', function(e) {

                let tglBukti = $('input[name="tgl_bukti"]').val();
                let jenisBeban = $('select[name="jenis_beban"]').val();

                if (!tglBukti || !jenisBeban) {

                    e.preventDefault(); // Cegah modal dari Bootstrap
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Harap isi Tanggal Transaksi dan Jenis Beban sebelum melanjutkan!',
                    });
                }
            });

            $('#inputKegiatanModal1').on('show.bs.modal', function(e) {

                if (dataSementara1.length === 0) {
                    // Aktifkan kembali input nilai_potongan
                    $("#nilai_potongan").prop("disabled", false);
                }
                let tglBukti = $('input[name="tgl_bukti"]').val();
                let jenisBeban = $('select[name="jenis_beban"]').val();

                if (!tglBukti || !jenisBeban) {

                    e.preventDefault(); // Cegah modal dari Bootstrap
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Harap isi Tanggal Transaksi dan Jenis Beban sebelum melanjutkan!',
                    });
                }
            });



            $("#formInputKegiatan").submit(function(e) {
                e.preventDefault();

                if (!validateNilai()) {
                    return false;
                }

                let jenisPergeseran = [];
                $('input[name="jenis_pergeseran[]"]:checked').each(function() {
                    jenisPergeseran.push($(this).val());
                });

                // Ambil data dari input modal
                let newItem = {
                    jenis_pergeseran: jenisPergeseran,
                    kd_sub_kegiatan: $("#kd_sub_kegiatan").val(),
                    nm_sub_kegiatan: $("#nm_sub_kegiatan").val(),
                    no_sp2d: $("#no_sp2d").val(),
                    kd_rek: $("#kd_rek").val(),
                    nm_rek: $("#nm_rek").val(),
                    kd_dana: $("#kd_dana").val(),
                    nm_dana: $("#nm_dana").val(),
                    nilai: $("#nilai").val(),
                    volume: $("#volume").val(),
                    satuan: $("#satuan").val(),
                    totalSPD: $("#totalSPD").val(),
                    realisasiSPD: $("#realisasiSPD").val(),
                    sisaSPD: $("#sisaSPD").val(),
                    totalAnggaranKas: $("#totalAnggaranKas").val(),
                    realisasiAnggaranKas: $("#realisasiAnggaranKas").val(),
                    sisaAnggaranKas: $("#sisaAnggaranKas").val(),
                    anggaran: $("#anggaran").val(),
                    realisasiAnggaran: $("#realisasiAnggaran").val(),
                    sisaAnggaran: $("#sisaAnggaran").val(),
                    rencanaPergeseranAnggaran: $("#rencanaPergeseranAnggaran").val(),
                    realisasiPergeseranAnggaran: $("#realisasiPergeseranAnggaran").val(),
                    sisaPergeseranAnggaran: $("#sisaPergeseranAnggaran").val(),
                    nilaisumberdana: $("#nilaisumberdana").val(),
                    realisasinilaisumberdana: $("#realisasinilaisumberdana").val(),
                    sisanilaisumberdana: $("#sisanilaisumberdana").val(),
                    isExisting: false // Tandai sebagai data baru
                };

                // Tambahkan ke array
                dataSementara.push(newItem);

                // Update tabel
                updateTable();

                // Tutup modal dan reset form
                $("#inputKegiatanModal").modal("hide");
                $("#formInputKegiatan")[0].reset();

                Swal.fire({
                    title: 'Berhasil',
                    text: 'Item berhasil ditambahkan',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            });


            $("#formInputTujuan").submit(function(e) {
                e.preventDefault(); // Mencegah form submit secara default


                if (!validateNilai()) {
                    return false; // Jika tidak valid, jangan lanjutkan submit
                }
                // Ambil data dari input modal
                let nilai_potongan = $("#nilai_potongan").val();
                let rekeningtujuan = $("#rekeningtujuan").val();
                let nm_rekening = $("#nm_rekening").val();
                let nm_bank = $("#nm_bank").val();
                let bank = $("#bank").val();
                let nilai_transfer = $("#nilai_transfer").val();
                let ket_tpp = $("#ket_tpp").val();

                // Simpan data ke array sementara
                dataSementara1.push({
                    nilai_potongan,
                    rekeningtujuan,
                    nm_rekening,
                    nm_bank,
                    bank,
                    nilai_transfer,
                    ket_tpp

                });

                // Perbarui tabel dengan data terbaru
                updateTable1();

                if (dataSementara1.length > 0) {
                    $("#nilai_potongan").prop("disabled", true);
                }
                // Tutup modal setelah simpan
                $("#inputKegiatanModal1").modal("hide");

                // Reset form setelah simpan
                $("#formInputTujuan")[0].reset();

                updateTotalPotongan();
            });




            $('#rekeningtujuan').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih...",
                minimumInputLength: 0,
                dropdownParent: $('#inputKegiatanModal1'),
                ajax: {
                    url: "{{ route('transaksi.getrekeningtujuan') }}",
                    dataType: 'json',
                    type: "POST",
                    delay: 250,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(params) {
                        return {
                            q: $.trim(params.term),
                            nm_rekening: $('#nm_rekening').val(),
                            nm_bank: $('#nm_bank').val(),
                            bank: $('#bank').val(),
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                },
            });

            $('#rekeningtujuan').on('select2:select', function(e) {
                var data = e.params.data;
                var nm_rekening = data.nm_rekening;
                var nm_bank = data.nm_bank;
                var bank = data.bank;
                $('#nm_rekening').val(nm_rekening);
                $('#nm_bank').val(nm_bank);
                $('#bank').val(bank); // Isi input total dengan saldo dalam format Rupiah
            });


            $('#inputKegiatanModal').on('shown.bs.modal', function() {
                // Inisialisasi Select2 untuk Sub Kegiatan
                $('#kd_sub_kegiatan').select2({
                    dropdownParent: $('#inputKegiatanModal .modal-content'),
                    placeholder: 'Pilih Sub Kegiatan',
                    width: 'resolve',
                    theme: 'bootstrap-5',
                });

                $('#kd_rek').select2({
                    dropdownParent: $('#inputKegiatanModal .modal-content'),
                    placeholder: 'Pilih Rekening',
                    width: 'resolve',
                    theme: 'bootstrap-5',
                    allowClear: true
                });

                $('#kd_dana').select2({
                    dropdownParent: $('#inputKegiatanModal .modal-content'),
                    placeholder: 'Pilih Sumber Dana',
                    width: 'resolve',
                    theme: 'bootstrap-5',
                    allowClear: true
                });

                // Ambil data Sub Kegiatan via AJAX
                $.ajax({
                    url: "{{ route('transaksi.get-sub-kegiatan') }}",
                    type: 'GET',
                    success: function(response) {
                        $('#kd_sub_kegiatan').empty().append(
                            '<option value="">Pilih Sub Kegiatan</option>');
                        $.each(response, function(index, item) {
                            $('#kd_sub_kegiatan').append(
                                '<option value="' + item.kd_sub_kegiatan +
                                '" data-nm_sub_kegiatan="' + item.nm_sub_kegiatan +
                                '">' +
                                item.kd_sub_kegiatan + ' || ' + item
                                .nm_sub_kegiatan +
                                '</option>'
                            );
                        });
                        $('#kd_sub_kegiatan').trigger('change');
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching kd_sub_kegiatan: ", error);
                    }
                });

                // Event handler untuk jenis pergeseran checkboxes
                $('input[name="jenis_pergeseran[]"]').on('change', function() {
                    // Jika sub kegiatan sudah dipilih, refresh data
                    var selectedSubKegiatan = $('#kd_sub_kegiatan').val();
                    if (selectedSubKegiatan) {
                        $('#kd_sub_kegiatan').trigger('change');
                    }
                });

                // Event handler untuk perubahan sub kegiatan
                $('#kd_sub_kegiatan').on('change', function() {
                    var kd_sub_kegiatan = $(this).val();
                    var nm_sub_kegiatan = $(this).find('option:selected').data('nm_sub_kegiatan') ||
                        '';

                    // Kumpulkan jenis pergeseran yang dipilih
                    var checkedPergeseran = [];
                    $('input[name="jenis_pergeseran[]"]:checked').each(function() {
                        checkedPergeseran.push($(this).val());
                    });

                    // Set nama sub kegiatan ke input yang disabled
                    $('#nm_sub_kegiatan').val(nm_sub_kegiatan);

                    // Reset rekening dropdown jika tidak ada sub kegiatan yang dipilih
                    if (!kd_sub_kegiatan) {
                        $('#kd_rek').empty().append('<option value="">Pilih Rekening</option>')
                            .trigger('change');
                        return;
                    }

                    // Ambil data rekening berdasarkan kd_sub_kegiatan dan jenis_pergeseran yang dipilih
                    $.ajax({
                        url: "{{ route('transaksi.get-rekening') }}",
                        type: 'GET',
                        data: {
                            kd_sub_kegiatan: kd_sub_kegiatan,
                            jenis_pergeseran: checkedPergeseran
                        },
                        success: function(response) {
                            // Reset rekening dropdown
                            $('#kd_rek').empty().append(
                                '<option value="">Pilih Rekening</option>');

                            // Populate rekening options
                            $.each(response, function(index, item) {
                                $('#kd_rek').append(
                                    `<option value="${item.kd_rek}" data-nm_rek="${item.nm_rek}">
                            ${item.kd_rek} || ${item.nm_rek}
                        </option>`
                                );
                            });

                            // Refresh Select2 to show the new options
                            $('#kd_rek').trigger('change');
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching kd_rek: ", error);
                            $('#kd_rek').empty().append(
                                    '<option value="">Error loading data</option>')
                                .trigger('change');
                        }
                    });
                });

                // Event handler untuk perubahan rekening
                $('#kd_rek').on('change', function() {
                    var kd_rek = $(this).val();
                    var kd_sub_kegiatan = $('#kd_sub_kegiatan').val();
                    var nm_rek = $(this).find('option:selected').data('nm_rek') || '';

                    // Set nama rekening
                    $('#nm_rek').val(nm_rek);

                    // Kumpulkan jenis pergeseran yang dipilih
                    var checkedPergeseran = [];
                    $('input[name="jenis_pergeseran[]"]:checked').each(function() {
                        checkedPergeseran.push($(this).val());
                    });

                    // Reset sumber dana dropdown jika tidak ada rekening yang dipilih
                    if (!kd_rek) {
                        $('#kd_dana').empty().append('<option value="">Pilih Sumber Dana</option>')
                            .trigger('change');
                        // Reset nilai dalam form
                        $('#totalSPD').val('');
                        $('#totalAnggaranKas').val('');
                        $('#anggaran').val('');
                        $('#realisasiSPD').val('');
                        $('#realisasiAnggaranKas').val('');
                        $('#realisasiAnggaran').val('');
                        $('#realisasinilaisumberdana').val('');
                        $('#sisaSPD').val('');
                        $('#sisaAnggaranKas').val('');
                        $('#sisaAnggaran').val('');
                        $('#sisanilaisumberdana').val('');
                        return;
                    }

                    // Ambil data sumber dana berdasarkan kd_sub_kegiatan, kd_rek, dan jenis_pergeseran
                    $.ajax({
                        url: "{{ route('transaksi.get-sumberdana') }}",
                        type: 'GET',
                        data: {
                            kd_sub_kegiatan: kd_sub_kegiatan,
                            kd_rek: kd_rek,
                            jenis_pergeseran: checkedPergeseran
                        },
                        success: function(response) {
                            // Reset sumber dana dropdown
                            $('#kd_dana').empty().append(
                                '<option value="">Pilih Sumber Dana</option>');

                            // Populate sumber dana options
                            $.each(response, function(index, item) {
                                $('#kd_dana').append(
                                    `<option value="${item.kd_dana}"
                            data-nm_dana="${item.nm_dana}"
                            data-id_sumberdana="${item.id_sumberdana}"
                            data-anggaran-tw1="${item.anggaran_tw1}"
                            data-anggaran-tw2="${item.anggaran_tw2}"
                            data-anggaran-tw3="${item.anggaran_tw3}"
                            data-anggaran-tw4="${item.anggaran_tw4}"
                            data-anggaran="${item.anggaran_tahun}"
                            data-status_anggaran="${item.status_anggaran}"
                            data-status_anggaran_kas="${item.status_anggaran_kas}"
                            ${[...Array(12)].map((_, i) => `data-rek${i+1}="${item[`rek${i+1}`] || 0}"`).join(' ')}
                        >
                            ${item.kd_dana} || ${item.nm_dana}
                        </option>`
                                );
                            });

                            // Refresh Select2 to show the new options
                            $('#kd_dana').trigger('change');
                            if (response.length > 0) {
                                var firstKdDana = response[0].kd_dana;
                                getRealisasiData(kd_rek, kd_sub_kegiatan,
                                    checkedPergeseran, firstKdDana);
                            } else {
                                getRealisasiData(kd_rek, kd_sub_kegiatan,
                                    checkedPergeseran, '');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching kd_dana: ", error);
                            $('#kd_dana').empty().append(
                                    '<option value="">Error loading data</option>')
                                .trigger('change');
                        }
                    });


                });

                // Event handler untuk perubahan sumber dana
                $('#kd_dana').on('change', function() {
                    let selectedOption = $(this).find('option:selected');
                    let kd_rek = $('#kd_rek').val();
                    let kd_sub_kegiatan = $('#kd_sub_kegiatan').val();
                    let kd_dana = $(this).val();

                    // Jika tidak ada sumber dana yang dipilih, kosongkan informasi anggaran
                    if (!kd_dana) {
                        $('#totalSPD').val('');
                        $('#anggaran').val('');
                        $('#totalAnggaranKas').val('');
                        $('#nilaisumberdana').val('');
                        $('#id_sumberdana').val('');
                        $('#statusAnggaran').val('');
                        $('#statusAnggaranKas').val('');
                        $('#nm_dana').val('');
                        return;
                    }

                    // Ambil data dari atribut data sumber dana yang dipilih
                    let nmDana = selectedOption.data('nm_dana') || '';
                    let idSumberDana = selectedOption.data('id_sumberdana') || '';
                    let anggaranTahun = parseFloat(selectedOption.data('anggaran')) || 0;
                    let statusAnggaran = selectedOption.data('status_anggaran') || '';
                    let statusAnggaranKas = selectedOption.data('status_anggaran_kas') || '';

                    $('#nm_dana').val(nmDana);
                    // Pastikan selectedMonth & selectedTriwulan sudah di-set
                    let selectedMonth = $('#kd_rek').data('selected-month') || new Date()
                        .getMonth() + 1;
                    let selectedTriwulan = $('#kd_rek').data('selected-triwulan') || Math.ceil(
                        selectedMonth / 3);

                    // Pilih anggaran triwulan yang sesuai
                    let totalSPD = parseFloat(selectedOption.data(
                        `anggaran-tw${selectedTriwulan}`)) || 0;

                    // Ambil anggaran sesuai bulan yang dipilih
                    let anggaranBulan = parseFloat(selectedOption.data(`rek${selectedMonth}`)) || 0;

                    // Hitung total anggaran kas sampai bulan yang dipilih
                    let totalAnggaranKas = 0;
                    for (let i = 1; i <= selectedMonth; i++) {
                        totalAnggaranKas += parseFloat(selectedOption.data(`rek${i}`)) || 0;
                    }

                    // Hitung total anggaran sebelumnya
                    let totalAnggaranSebelumnya = 0;
                    for (let i = 1; i < selectedMonth; i++) {
                        totalAnggaranSebelumnya += parseFloat(selectedOption.data(`rek${i}`)) || 0;
                    }

                    // Hitung total SPD sebelumnya
                    let totalspdsebelumnya = 0;
                    for (let i = 1; i < selectedTriwulan; i++) {
                        totalspdsebelumnya += parseFloat(selectedOption.data(`anggaran-tw${i}`)) ||
                            0;
                    }

                    // Kumpulkan jenis pergeseran yang dipilih
                    var checkedPergeseran = [];
                    $('input[name="jenis_pergeseran[]"]:checked').each(function() {
                        checkedPergeseran.push($(this).val());
                    });

                    getRealisasiData(kd_rek, kd_sub_kegiatan, checkedPergeseran, kd_dana);
                    // Ambil total nilai realisasi
                    $.post("{{ route('transaksi.get-total-nilai') }}", {
                        _token: "{{ csrf_token() }}",
                        kd_rek: kd_rek,
                        kd_sub_kegiatan: kd_sub_kegiatan,
                        kd_dana: kd_dana,
                        jenis_pergeseran: checkedPergeseran
                    }).done(function(response) {
                        let totalNilai = parseFloat(response.total_nilai) || 0;
                        let totalSPDFinal = (selectedTriwulan === 1) ? totalSPD : totalSPD +
                            totalspdsebelumnya;

                        // Set nilai ke dalam form
                        $('#id_sumberdana').val(idSumberDana);
                        $('#statusAnggaran').val(statusAnggaran);
                        $('#statusAnggaranKas').val(statusAnggaranKas);
                        $('#totalSPD').val(formatRupiah1(totalSPDFinal));
                        $('#anggaran').val(formatRupiah1(anggaranTahun));
                        $('#totalAnggaranKas').val(formatRupiah1(totalAnggaranKas));
                        $('#nilaisumberdana').val(formatRupiah1(
                            anggaranTahun)); // Tambahkan ini untuk nilaisumberdana

                        // Hitung sisa-sisa
                        hitungSisa("totalSPD", "realisasiSPD", "sisaSPD");
                        hitungSisa("anggaran", "realisasiAnggaran", "sisaAnggaran");
                        hitungSisa("totalAnggaranKas", "realisasiAnggaranKas",
                            "sisaAnggaranKas");
                        hitungSisa("nilaisumberdana", "realisasinilaisumberdana",
                            "sisanilaisumberdana");
                    }).fail(function(xhr, status, error) {
                        console.error("Error fetching total nilai:", error);
                    });
                });

                function getRealisasiData(kd_rek, kd_sub_kegiatan, jenis_pergeseran, kd_dana) {
                    if (kd_rek && kd_sub_kegiatan) {
                        var csrfToken = $('meta[name="csrf-token"]').attr('content');

                        $.ajax({
                            url: "{{ route('transaksi.getrealisasi') }}",
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            data: {
                                kd_rek: kd_rek,
                                kd_sub_kegiatan: kd_sub_kegiatan,
                                jenis_pergeseran: jenis_pergeseran,
                                kd_dana: kd_dana
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    // Format nilai realisasi sebagai Rupiah sebelum ditampilkan
                                    $('#realisasiSPD').val(formatRupiah8(parseFloat(response
                                        .realisasiSPD) || 0));
                                    $('#realisasiAnggaranKas').val(formatRupiah8(parseFloat(
                                        response.realisasiAnggaranKas) || 0));
                                    $('#realisasiAnggaran').val(formatRupiah8(parseFloat(
                                        response.realisasiAnggaran) || 0));
                                    $('#realisasinilaisumberdana').val(formatRupiah8(parseFloat(
                                        response.realisasiSumberDana) || 0));

                                    // Hitung sisa-sisa setelah mendapatkan realisasi
                                    hitungSisa("totalSPD", "realisasiSPD", "sisaSPD");
                                    hitungSisa("anggaran", "realisasiAnggaran", "sisaAnggaran");
                                    hitungSisa("totalAnggaranKas", "realisasiAnggaranKas",
                                        "sisaAnggaranKas");
                                    hitungSisa("nilaisumberdana", "realisasinilaisumberdana",
                                        "sisanilaisumberdana");
                                } else {
                                    // Jika tidak ada data, set ke 0
                                    $('#realisasiSPD').val(formatRupiah8(0));
                                    $('#realisasiAnggaranKas').val(formatRupiah8(0));
                                    $('#realisasiAnggaran').val(formatRupiah8(0));
                                    $('#realisasinilaisumberdana').val(formatRupiah8(0));

                                    // Hitung sisa-sisa
                                    hitungSisa("totalSPD", "realisasiSPD", "sisaSPD");
                                    hitungSisa("anggaran", "realisasiAnggaran", "sisaAnggaran");
                                    hitungSisa("totalAnggaranKas", "realisasiAnggaranKas",
                                        "sisaAnggaranKas");
                                    hitungSisa("nilaisumberdana", "realisasinilaisumberdana",
                                        "sisanilaisumberdana");
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("AJAX Error: ", status, error);
                                // Tetap set ke 0 jika terjadi error
                                $('#realisasiSPD').val(formatRupiah8(0));
                                $('#realisasiAnggaranKas').val(formatRupiah8(0));
                                $('#realisasiAnggaran').val(formatRupiah8(0));
                                $('#realisasinilaisumberdana').val(formatRupiah8(0));

                                // Hitung sisa-sisa
                                hitungSisa("totalSPD", "realisasiSPD", "sisaSPD");
                                hitungSisa("anggaran", "realisasiAnggaran", "sisaAnggaran");
                                hitungSisa("totalAnggaranKas", "realisasiAnggaranKas",
                                    "sisaAnggaranKas");
                                hitungSisa("nilaisumberdana", "realisasinilaisumberdana",
                                    "sisanilaisumberdana");
                            }
                        });
                    }
                }

                function formatRupiah8(angka) {
                    if (isNaN(angka)) angka = 0;
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(angka).replace('Rp', '').trim();
                }
            });

            $(document).on('change', '#kd_sub_kegiatan', function() {
                var selectedOption = $(this).find('option:selected');
                var nmSubKegiatan = selectedOption.data('nm_sub_kegiatan') || '';
                $('#nm_sub_kegiatan').val(nmSubKegiatan);
            });

            $(document).on('change', '[name="tgl_bukti"]', function() {
                var tglInput = $(this).val();
                if (tglInput) {
                    var selectedMonth = new Date(tglInput).getMonth() + 1;
                    var selectedTriwulan = Math.ceil(selectedMonth / 3); // Ambil bulan (1-12)
                    $('#kd_rek').data('selected-month', selectedMonth);
                    $('#kd_rek').data('selected-triwulan', selectedTriwulan);
                    console.log("Bulan yang dipilih:", selectedMonth);

                    // Jika rekening sudah dipilih, refresh nilainya
                    if ($('#kd_dana').val()) {
                        $('#kd_dana').trigger('change');
                    }
                }
            });


            function formatRupiah1(angka) {
                let number_string = angka.toString().replace(/[^,\d]/g, ''),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                return 'Rp ' + rupiah + (split[1] !== undefined ? ',' + split[1] : '');
            }

            // Reset Select2 saat modal ditutup
            $('#inputKegiatanModal').on('hidden.bs.modal', function() {
                $('#kd_sub_kegiatan').select2('destroy');
                $('#nm_sub_kegiatan').val('');
                $('#kd_dana').select2('destroy');
                $('#nm_dana').val('');
            });
        });



        // Panggil fungsi hitungSisa() secara otomatis setelah halaman selesai dimuat
        window.onload = function() {
            hitungSisa("totalSPD", "realisasiSPD", "sisaSPD");
            hitungSisa("totalAnggaranKas", "realisasiAnggaranKas", "sisaAnggaranKas");
            hitungSisa("anggaran", "realisasiAnggaran", "sisaAnggaran");
            hitungSisa("rencanaPergeseranAnggaran", "realisasiPergeseranAnggaran", "sisaPergeseranAnggaran");
            hitungSisa("nilaisumberdana", "realisasinilaisumberdana", "sisanilaisumberdana");
        };
    </script>
@endpush
