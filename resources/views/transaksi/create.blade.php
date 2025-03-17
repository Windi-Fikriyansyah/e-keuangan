@extends('template.app')
@section('title', 'Tambah Transaksi')
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
        <h5>Transaksi Pemindahbukuan Bank</h5>
        <div class="card">
            <div class="card-body">
                <form method="POST"action="{{ route('transaksi.store') }}" id="formBpkb">
                    @csrf
                    <input type="hidden" name="details" id="hiddenDetails">

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Kode OPD/UNIT</label>
                        <div class="col-sm-10">
                            <input type="text" disabled class="form-control" value=" {{ $kd_skpd }} || {{ $nm_skpd }}" id="jenis" placeholder="Silahkan Pilih" autofocus>
                        </div>
                    </div>


                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Bukti</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('no_bukti') is-invalid @enderror readonlyInput"
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="no_bukti"
                                value="{{ $newNoBukti }}" readonly>
                        </div>
                        <label class="col-sm-2 col-form-label">Tanggal Transaksi</label>
                        <div class="col-sm-4">
                            <input id="tgl_bukti" class="form-control @error('tgl_bukti') is-invalid @enderror" type="date"
                                placeholder="Tidak perlu diisi, otomatis" name="tgl_bukti"
                                value="{{ old('tgl_bukti') ? old('tgl_bukti') : date('Y-m-d') }}">
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
                                <option value="UP" {{ old('jenis_beban') == 'UP' ? 'selected' : '' }}>UP</option>
                                <option value="GU" {{ old('jenis_beban') == 'GU' ? 'selected' : '' }}>GU</option>
                                <option value="TU" {{ old('jenis_beban') == 'TU' ? 'selected' : '' }}>TU</option>
                                <option value="GAJI" {{ old('jenis_beban') == 'GAJI' ? 'selected' : '' }}>GAJI</option>
                                <option value="Barang & Jasa" {{ old('jenis_beban') == 'Barang & Jasa' ? 'selected' : '' }}>Barang & Jasa</option>
                            </select>
                            @error('jenis_beban')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Rekening Bank Bend</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('rek_pengeluaran') is-invalid @enderror" type="text"
                                 name="rek_pengeluaran" disabled value="{{ $rek_pengeluaran }}">
                            @error('rek_pengeluaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('ket') is-invalid @enderror" type="text" name="ket"
                                placeholder="Keterangan">{{ old('ket') }}</textarea>
                            @error('ket')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3 sp2d-checkbox" style="display: none;">
                        <label class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <input type="checkbox" id="sp2d_langsung" name="sp2d_langsung" value="1" class='form-check-input'>
                            <label for="sp2d_langsung">Tarik Otomatis Terima Sp2d</label>
                        </div>
                    </div>
                    <div class="row mb-3" id="no_transaksi_wrapper" style="display: none;">
                        <label class="col-sm-2 col-form-label">No Transaksi</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('no_transaksi') is-invalid @enderror"
                                name="no_transaksi" id="no_transaksi_input" style="width: 100%" disabled>
                            </select>
                        </div>
                    </div>


                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <input type="checkbox" id="terima_sp2d" name="jenis_terima_sp2d" value="1" class='form-check-input'>
                                    <label for="terima_sp2d">Terima SP2D</label>
                        </div>

                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <input type="checkbox" id="perlimpahan" name="jenis_perlimpahan" value="1" class='form-check-input'>
                                    <label for="perlimpahan">Perlimpahan</label>
                        </div>

                    </div>


                    <div class="mb-3 text-end">
                        <input type="hidden" name="total_belanja" id="hiddenTotalBelanja">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a href="{{ route('transaksi.index') }}" class="btn btn-warning">Kembali</a>
                    </div>
                </form>
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
        <div class="card shadow-sm p-3 rounded" style="background-color: #f8f9fa; border-radius: 50px 50px; margin-bottom: -1rem;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-bold text-primary">Rekening Belanja</h5>

                <div class="d-flex gap-2">

                    <button class="btn btn-success d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#inputKegiatanModal">
                        Tambah
                    </button>

                </div>
            </div>
        </div>

        <div class="card">

            <div class="card-body">
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
                            </tbody>
                        </table>
                    </div>
                    <p id="totalBelanja" name="totalBelanja" style="text-align: right; margin-top: 10px; font-size: 16px; font-weight: bold;">
                        <strong>Total belanja:</strong> Rp 0
                    </p>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="inputKegiatanModal" tabindex="-1" aria-labelledby="inputKegiatanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inputKegiatanModalLabel">Input Rincian Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formInputKegiatan">
                        <div class="mb-3 d-flex align-items-center">
                            <label for="kd_sub_kegiatan" class="form-label me-5" style="min-width: 120px;">Sub Kegiatan</label>
                            <select
                                id="kd_sub_kegiatan"
                                name="kd_sub_kegiatan"
                                class="form-select me-2 custom-border select2 @error('kd_sub_kegiatan') is-invalid @enderror"
                                style="width: 50%;"
                                data-placeholder="Pilih Sub Kegiatan"
                                required
                            >
                                <option></option> <!-- Agar placeholder muncul -->
                                <!-- Options will be populated dynamically -->
                            </select>
                            <input
                                type="text"
                                disabled
                                class="form-control custom-border"
                                id="nm_sub_kegiatan"
                                placeholder="Nama Sub Kegiatan"
                                style="width: 50%; margin-left: 15px;"
                            >
                        </div>


                        <div class="mb-3 d-flex align-items-center">
                            <label for="no_sp2d" class="form-label me-5" style="min-width: 120px;">Nomor SP2D</label>
                            <input type="text" class="form-control" id="no_sp2d" name="no_sp2d" required>

                        </div>

                        <div class="mb-3 d-flex align-items-center">
                            <label for="kd_rek" class="form-label me-5" style="min-width: 120px;">Rekening</label>
                            <select
                                id="kd_rek"
                                name="kd_rek"
                                class="form-select me-2 custom-border select2 @error('kd_rek') is-invalid @enderror"
                                style="width: 50%;"
                                data-placeholder="Pilih Rekening"
                                required
                            >
                                <option></option> <!-- Agar placeholder muncul -->
                                <!-- Options will be populated dynamically -->
                            </select>
                            <input
                                type="text"
                                disabled
                                class="form-control custom-border"
                                id="nm_rek"
                                style="width: 50%; margin-left: 15px;"
                            >
                            <input
                                type="hidden"
                                class="form-control custom-border"
                                id="id_sumberdana"
                                style="width: 50%; margin-left: 15px;"
                            >
                        </div>


                        <div class="mb-3 d-flex align-items-center">
                            <label for="kd_dana" class="form-label me-5" style="min-width: 120px;">SumberDana</label>
                            <select
                                id="kd_dana"
                                name="kd_dana"
                                class="form-select me-2 custom-border select2 @error('kd_dana') is-invalid @enderror"
                                style="width: 50%;"
                                data-placeholder="Pilih Sumber Dana"
                                required
                            >
                                <option></option> <!-- Agar placeholder muncul -->
                                <!-- Options will be populated dynamically -->
                            </select>
                            <input
                                type="text"
                                disabled
                                class="form-control custom-border"
                                id="nm_dana"
                                style="width: 50%; margin-left: 15px;"
                            >
                        </div>

                        <div class="row mb-3">
                            <div class="col-4 d-flex align-items-center">
                                <label for="totalSPD" class="form-label mb-0 w-100">Total SPD</label>
                                <input type="text" class="form-control" id="totalSPD" name="totalSPD" oninput="formatRupiah(this); hitungSisa('totalSPD', 'realisasiSPD', 'sisaSPD');" disabled>
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="realisasiSPD" class="form-label mb-0 w-100">Realisasi</label>
                                <input type="text" disabled class="form-control" id="realisasiSPD" name="realisasiSPD" oninput="formatRupiah(this); hitungSisa('totalSPD', 'realisasiSPD', 'sisaSPD');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="sisaSPD" class="form-label mb-0 w-100">Sisa</label>
                                <input type="text" class="form-control" id="sisaSPD" name="sisaSPD" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-4 d-flex align-items-center">
                                <label for="totalAnggaranKas" class="form-label mb-0 w-100">Total Anggaran Kas</label>
                                <input type="text" disabled class="form-control" id="totalAnggaranKas" name="totalAnggaranKas" oninput="formatRupiah(this); hitungSisa('totalAnggaranKas', 'realisasiAnggaranKas', 'sisaAnggaranKas');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="realisasiAnggaranKas" class="form-label mb-0 w-100">Realisasi</label>
                                <input type="text" disabled class="form-control" id="realisasiAnggaranKas" name="realisasiAnggaranKas" oninput="formatRupiah(this); hitungSisa('totalAnggaranKas', 'realisasiAnggaranKas', 'sisaAnggaranKas');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="sisaAnggaranKas" class="form-label mb-0 w-100">Sisa</label>
                                <input type="text" class="form-control" id="sisaAnggaranKas" name="sisaAnggaranKas" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-4 d-flex align-items-center">
                                <label for="anggaran" class="form-label mb-0 w-100">Anggaran</label>
                                <input type="text" disabled class="form-control" id="anggaran" name="anggaran" oninput="formatRupiah(this); hitungSisa('anggaran', 'realisasiAnggaran', 'sisaAnggaran');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="realisasiAnggaran" class="form-label mb-0 w-100">Realisasi</label>
                                <input type="text" disabled class="form-control" id="realisasiAnggaran" name="realisasiAnggaran" oninput="formatRupiah(this); hitungSisa('anggaran', 'realisasiAnggaran', 'sisaAnggaran');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="sisaAnggaran" class="form-label mb-0 w-100">Sisa</label>
                                <input type="text" class="form-control" id="sisaAnggaran" name="sisaAnggaran" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-4 d-flex align-items-center">

                                <input type="hidden" class="form-control" id="rencanaPergeseranAnggaran" name="rencanaPergeseranAnggaran" oninput="formatRupiah(this); hitungSisa('rencanaPergeseranAnggaran', 'realisasiPergeseranAnggaran', 'sisaPergeseranAnggaran');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <input type="hidden" class="form-control" id="realisasiPergeseranAnggaran" name="realisasiPergeseranAnggaran" oninput="formatRupiah(this); hitungSisa('rencanaPergeseranAnggaran', 'realisasiPergeseranAnggaran', 'sisaPergeseranAnggaran');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <input type="hidden" class="form-control" id="sisaPergeseranAnggaran" name="sisaPergeseranAnggaran" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-4 d-flex align-items-center">
                                <label for="nilaisumberdana" class="form-label mb-0 w-100">Nilai Sumber Dana</label>
                                <input type="text" disabled class="form-control" id="nilaisumberdana" name="nilaisumberdana" oninput="formatRupiah(this); hitungSisa('nilaisumberdana', 'realisasinilaisumberdana', 'sisanilaisumberdana');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="realisasinilaisumberdana" class="form-label mb-0 w-100">Realisasi</label>
                                <input type="text" disabled class="form-control" id="realisasinilaisumberdana" name="realisasinilaisumberdana" oninput="formatRupiah(this); hitungSisa('nilaisumberdana', 'realisasinilaisumberdana', 'sisanilaisumberdana');">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="sisanilaisumberdana" class="form-label mb-0 w-100">Sisa</label>
                                <input type="text" class="form-control" id="sisanilaisumberdana" name="sisanilaisumberdana" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-4 d-flex align-items-center">
                                <label for="statusAnggaran" class="form-label mb-0 w-100">Status Anggaran</label>
                                <input type="text" disabled class="form-control" id="statusAnggaran" name="statusAnggaran">
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="statusAnggaranKas" class="form-label mb-0 w-100">Status Anggaran Kas</label>
                                <input type="text" disabled class="form-control" id="statusAnggaranKas" name="statusAnggaranKas" >
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="sisaKasBank" class="form-label mb-0 w-100">Sisa Kas Bank</label>
                                <input type="text" disabled class="form-control" id="sisaKasBank" name="sisaKasBank" value="{{ number_format($saldo_awal, 0, ',', '.') }}"  oninput="formatRupiah(this);" readonly>
                            </div>
                        </div>

                        <input type="hidden" class="form-control" id="potonganLS" name="potonganLS" oninput="formatRupiah(this);">


                                <input type="hidden" class="form-control" id="totalSisa" name="totalSisa" oninput="formatRupiah(this);">


                                <input type="hidden" class="form-control" id="volume" name="volume">

                                <input type="hidden" class="form-control" id="satuan" name="satuan">

                        <div class="row mb-3">
                            <div class="col-4 d-flex align-items-center">

                            </div>
                            <div class="col-4 d-flex align-items-center">

                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <label for="nilai" class="form-label mb-0 w-100">Nilai</label>
                                <input type="text" class="form-control" id="nilai" name="nilai" oninput="formatRupiah(this);" required>
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
document.addEventListener("DOMContentLoaded", function () {
    const jenisBeban = document.querySelector("select[name='jenis_beban']");
    const terimaSp2d = document.getElementById("terima_sp2d");
    const sp2dCheckboxContainer = document.querySelector(".sp2d-checkbox");

    // Fungsi untuk menampilkan atau menyembunyikan checkbox SP2D langsung
    function toggleSp2dCheckbox() {
        const selectedBeban = jenisBeban.value;
        const isTerimaSp2dChecked = terimaSp2d.checked;

        // Tampilkan checkbox SP2D langsung jika jenis beban adalah GAJI atau Barang & Jasa
        // DAN checkbox "Terima SP2D" tidak dicentang
        if ((selectedBeban === "GAJI" || selectedBeban === "Barang & Jasa") && !isTerimaSp2dChecked) {
            sp2dCheckboxContainer.style.display = "flex"; // Tampilkan
        } else {
            sp2dCheckboxContainer.style.display = "none"; // Sembunyikan
        }
    }

    // Jalankan fungsi saat jenis beban atau checkbox "Terima SP2D" berubah
    jenisBeban.addEventListener("change", toggleSp2dCheckbox);
    terimaSp2d.addEventListener("change", toggleSp2dCheckbox);


        toggleSp2dCheckbox();

});

document.addEventListener("DOMContentLoaded", function () {
    const sp2dLangsungCheckbox = document.getElementById("sp2d_langsung");
    const noTransaksiWrapper = document.getElementById("no_transaksi_wrapper");
    const noTransaksiInput = document.getElementById("no_transaksi_input");
    const aksiColumn = document.getElementById("aksiColumn");

    // Selecting the button with the specific selectors
    const tambahButton = document.querySelector("button[data-bs-target='#inputKegiatanModal']");

    // Make sure we have a consistent function to toggle the button visibility
    function updateButtonVisibility() {
        if (sp2dLangsungCheckbox.checked) {
            if (tambahButton) {
                tambahButton.style.display = "none";
                // Add !important to the style to override any CSS rules
                tambahButton.setAttribute("style", "display: none !important");
                aksiColumn.style.display = "none";
            }
        } else {
            if (tambahButton) {
                tambahButton.style.display = "flex";
                tambahButton.setAttribute("style", "display: flex !important");
                aksiColumn.style.display = "table-cell";
            }
        }
    }

    // Function to handle checkbox change
    function toggleNoTransaksi() {
        if (sp2dLangsungCheckbox.checked) {
            noTransaksiInput.disabled = false;
            noTransaksiWrapper.style.display = "flex";
        } else {
            noTransaksiInput.disabled = true;
            noTransaksiWrapper.style.display = "none";
        }

        // Update button visibility
        updateButtonVisibility();
    }

    // Add event listener for checkbox change
    sp2dLangsungCheckbox.addEventListener("change", toggleNoTransaksi);

    // Initial setup
    toggleNoTransaksi();

    // Add an additional event listener for when the page is fully loaded
    window.addEventListener('load', function() {
        // Wait a bit to ensure all scripts have run
        setTimeout(updateButtonVisibility, 100);
    });

    // Check periodically if the button visibility matches the checkbox state
    setInterval(updateButtonVisibility, 500);
});



document.addEventListener("DOMContentLoaded", function () {
    const jenisBeban = document.querySelector("select[name='jenis_beban']");
    const terimaSp2d = document.getElementById("terima_sp2d");

    const subKegiatan = document.getElementById("kd_sub_kegiatan");
    const rekening = document.getElementById("kd_rek");
    const sumberDana = document.getElementById("kd_dana");

    function toggleFields() {
        const selectedBeban = jenisBeban.value;
        const isChecked = terimaSp2d.checked;

        // Jika jenis beban adalah UP atau GU dan "Terima SP2D" dicentang, maka disable fields
        const shouldDisable = (selectedBeban === "UP" || selectedBeban === "GU") && isChecked;

        subKegiatan.disabled = shouldDisable;
        rekening.disabled = shouldDisable;
        sumberDana.disabled = shouldDisable;
    }

    // Event listener untuk perubahan pada jenis beban dan checkbox
    jenisBeban.addEventListener("change", toggleFields);
    terimaSp2d.addEventListener("change", toggleFields);

    // Panggil fungsi pertama kali untuk memastikan kondisi awal
    toggleFields();
});


document.addEventListener("DOMContentLoaded", function () {
    const jenisBeban = document.querySelector("select[name='jenis_beban']");
    const perlimpahan = document.getElementById("perlimpahan");

    const subKegiatan = document.getElementById("kd_sub_kegiatan");
    const rekening = document.getElementById("kd_rek");
    const sumberDana = document.getElementById("kd_dana");

    function toggleFields() {
        const selectedBeban = jenisBeban.value;
        const isChecked = perlimpahan.checked;

        // Jika jenis beban adalah UP atau GU dan "Terima SP2D" dicentang, maka disable fields
        const shouldDisable = (selectedBeban === "UP" || selectedBeban === "GU") && isChecked;

        subKegiatan.disabled = shouldDisable;
        rekening.disabled = shouldDisable;
        sumberDana.disabled = shouldDisable;
    }

    // Event listener untuk perubahan pada jenis beban dan checkbox
    jenisBeban.addEventListener("change", toggleFields);
    perlimpahan.addEventListener("change", toggleFields);

    // Panggil fungsi pertama kali untuk memastikan kondisi awal
    toggleFields();
});


function hitungTotalBelanja() {
    let totalBelanja = 0;

    // Loop through the dataSementara array to sum the "nilai" field
    dataSementara.forEach(item => {
        totalBelanja += parseFloat(item.nilai.replace(/\D/g, "")) || 0;
    });

    // Format the total as currency (Rupiah)
    let formattedTotal = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0
    }).format(totalBelanja);

    // Update the "Total Belanja" field
    document.getElementById("totalBelanja").innerHTML = `<strong>Total belanja:</strong> ${formattedTotal}`;
    document.getElementById("hiddenTotalBelanja").value = totalBelanja;

}

function validateNilai() {
    // Cek apakah checkbox "Terima SP2D" dicentang
    let isChecked = document.getElementById('terima_sp2d').checked;
    if (isChecked) {
        return true; // Abaikan validasi jika checkbox dicentang
    }

    // Fungsi untuk membersihkan angka dari format yang tidak diinginkan
    function cleanNumber(value) {
        if (!value) return 0;
        return parseFloat(value.replace(/[^\d,-]/g, '').replace(/\./g, '').replace(',', '.')) || 0;
    }

    // Ambil nilai input dan bersihkan dari karakter yang tidak diinginkan
    let sisaSPD = cleanNumber($("#sisaSPD").val());
    let sisaAnggaranKas = cleanNumber($("#sisaAnggaranKas").val());
    let sisaAnggaran = cleanNumber($("#sisaAnggaran").val());
    let sisaPergeseranAnggaran = cleanNumber($("#sisaPergeseranAnggaran").val());
    let sisanilaisumberdana = cleanNumber($("#sisanilaisumberdana").val());

    // Validasi nilai tidak boleh negatif
    if (sisaSPD < 0 || sisaAnggaranKas < 0 || sisaAnggaran < 0 || sisaPergeseranAnggaran < 0 || sisanilaisumberdana < 0) {
        Swal.fire({
            icon: 'error',
            title: 'Data Tidak Valid!',
            text: 'Nilai Sisa tidak boleh negatif.',
        });
        return false;
    }

    // Ambil nilai input tambahan
    let nilai = cleanNumber($("#nilai").val());
    let sisaKasBank = cleanNumber($("#sisaKasBank").val());

    // Cek jika nilai lebih besar dari sisa kas bank
    if (nilai > sisaKasBank) {
        Swal.fire({
            icon: 'error',
            title: 'Kesalahan',
            text: 'Nilai tidak boleh lebih besar dari Sisa Kas Bank!',
        });
        return false;
    }

    return true;
}


function prepareSubmit() {
    // Ensure dataSementara exists and has items
    if (!dataSementara || dataSementara.length === 0) {
        alert('Harap tambahkan minimal satu kegiatan');
        return false;
    }



    // Convert dataSementara to a format suitable for Laravel
    let details = dataSementara.map(item => ({
        kd_sub_kegiatan: item.kd_sub_kegiatan,
        nm_sub_kegiatan: item.nm_sub_kegiatan,
        no_sp2d: item.no_sp2d,
        kd_rek: item.kd_rek,
        nm_rek: item.nm_rek,
        kd_dana: item.kd_dana,
        nm_dana: item.nm_dana,
        nilai: item.nilai,
        volume: item.volume || null,
        satuan: item.satuan || null,
        total: item.total || null,
        totalSPD : item.totalSPD,
            realisasiSPD : item.realisasiSPD,
            sisaSPD : item.sisaSPD,
            totalAnggaranKas : item.totalAnggaranKas,
            realisasiAnggaranKas : item.realisasiAnggaranKas,
            sisaAnggaranKas : item.sisaAnggaranKas,
            anggaran : item.anggaran,
            realisasiAnggaran : item.realisasiAnggaran,
            sisaAnggaran : item.sisaAnggaran,
            rencanaPergeseranAnggaran : item.rencanaPergeseranAnggaran,
            realisasiPergeseranAnggaran : item.realisasiPergeseranAnggaran,
            sisaPergeseranAnggaran : item.sisaPergeseranAnggaran,
            nilaisumberdana : item.nilaisumberdana,
            realisasinilaisumberdana : item.realisasinilaisumberdana,
            sisanilaisumberdana : item.sisanilaisumberdana
    }));

    // Set the hidden input value
    document.getElementById('hiddenDetails').value = JSON.stringify(details);

    // Return true to allow form submission
    return true;
}

document.getElementById('formBpkb').onsubmit = function(event) {
    return prepareSubmit();
};

      let dataSementara = [];
      function updateTable() {
    let tbody = $("#pajak tbody");
    tbody.empty(); // Kosongkan isi tabel sebelum memperbarui

    dataSementara.forEach((item, index) => {
        let row = `
            <tr>
                <td>${index + 1}</td>
                <td>${item.nm_sub_kegiatan}</td>
                <td>${item.kd_rek}</td>
                <td>${item.nm_rek}</td>
                <td>${item.nilai}</td>
                <td>${item.nm_dana}</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="hapusData(${index})"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });

    hitungTotalBelanja();
}

// Fungsi untuk menghapus data dari array dan memperbarui tabel
function hapusData(index) {
    dataSementara.splice(index, 1);
    updateTable();
}
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

    function formatRupiah5(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(number);
    }
    $('#no_transaksi_input').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...", // Pastikan placeholder ada di luar ajax
        allowClear: true, // Tambahkan agar placeholder tetap terlihat
        ajax: {
            url: "{{ route('transaksi.getno_transaksi') }}",
            dataType: 'json',
            type: "POST",
            delay: 250,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Tambahkan CSRF token
            },
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    })
                };
            },
            cache: true
        }
    });

    // Event ketika no_transaksi dipilih
    // Event ketika no_transaksi dipilih
$('#no_transaksi_input').change(function() {
    let noTransaksi = $(this).val();

    if (noTransaksi) {
        $.ajax({
            url: "{{ route('transaksi.getpotongandata') }}",
            type: "POST",
            data: { no_transaksi: noTransaksi },
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Clear the existing dataSementara array
                    dataSementara = [];

                    $.each(response.trdtrmpot, function(index, row) {
                        // Add each row to dataSementara with the required structure
                        dataSementara.push({
                            kd_sub_kegiatan: row.kd_sub_kegiatan || '',
                            nm_sub_kegiatan: row.nm_sub_kegiatan || '',
                            no_sp2d: row.no_sp2d || '',
                            kd_rek: row.kd_rek6 || '',
                            nm_rek: row.nm_rek6 || '',
                            kd_dana: row.id_dana || '',
                            nm_dana: row.nm_dana || '',
                            nilai: formatRupiah5(row.nilai).replace('Rp', '').trim(),
                            volume: row.volume || null,
                            satuan: row.satuan || null,
                            total: row.total || null,
                            totalSPD: row.totalSPD || 0,
                            realisasiSPD: row.realisasiSPD || 0,
                            sisaSPD: row.sisaSPD || 0,
                            totalAnggaranKas: row.totalAnggaranKas || 0,
                            realisasiAnggaranKas: row.realisasiAnggaranKas || 0,
                            sisaAnggaranKas: row.sisaAnggaranKas || 0,
                            anggaran: row.anggaran || 0,
                            realisasiAnggaran: row.realisasiAnggaran || 0,
                            sisaAnggaran: row.sisaAnggaran || 0,
                            rencanaPergeseranAnggaran: row.rencanaPergeseranAnggaran || 0,
                            realisasiPergeseranAnggaran: row.realisasiPergeseranAnggaran || 0,
                            sisaPergeseranAnggaran: row.sisaPergeseranAnggaran || 0,
                            nilaisumberdana: row.nilaisumberdana || 0,
                            realisasinilaisumberdana: row.realisasinilaisumberdana || 0,
                            sisanilaisumberdana: row.sisanilaisumberdana || 0
                        });
                    });

                    // Update the table with new data
                    updateTable();
                } else {
                    alert('Data tidak ditemukan!');
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Terjadi kesalahan saat mengambil data.');
            }
        });
    }
});


    $('#inputKegiatanModal').on('show.bs.modal', function (e) {

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



    $("#formInputKegiatan").submit(function (e) {
        e.preventDefault(); // Mencegah form submit secara default


        if (!validateNilai()) {
            return false; // Jika tidak valid, jangan lanjutkan submit
        }
        // Ambil data dari input modal
        let kd_sub_kegiatan = $("#kd_sub_kegiatan").val();
        let nm_sub_kegiatan = $("#nm_sub_kegiatan").val();
        let no_sp2d = $("#no_sp2d").val();
        let kd_rek = $("#kd_rek").val();
        let nm_rek = $("#nm_rek").val();
        let kd_dana = $("#kd_dana").val();
        let nm_dana = $("#nm_dana").val();
        let nilai = $("#nilai").val();
        let volume = $("#volume").val();
        let satuan = $("#satuan").val();
        let total = $("#total").val();

        let totalSPD = $("#totalSPD").val();
        let realisasiSPD = $("#realisasiSPD").val();
        let sisaSPD = $("#sisaSPD").val();
        let totalAnggaranKas = $("#totalAnggaranKas").val();
        let realisasiAnggaranKas = $("#realisasiAnggaranKas").val();
        let sisaAnggaranKas = $("#sisaAnggaranKas").val();
        let anggaran = $("#anggaran").val();
        let realisasiAnggaran = $("#realisasiAnggaran").val();
        let sisaAnggaran = $("#sisaAnggaran").val();
        let rencanaPergeseranAnggaran = $("#rencanaPergeseranAnggaran").val();
        let realisasiPergeseranAnggaran = $("#realisasiPergeseranAnggaran").val();
        let sisaPergeseranAnggaran = $("#sisaPergeseranAnggaran").val();
        let nilaisumberdana = $("#nilaisumberdana").val();
        let realisasinilaisumberdana = $("#realisasinilaisumberdana").val();
        let sisanilaisumberdana = $("#sisanilaisumberdana").val();


        // Simpan data ke array sementara
        dataSementara.push({
            kd_sub_kegiatan,
            nm_sub_kegiatan,
            no_sp2d,
            kd_rek,
            nm_rek,
            kd_dana,
            nm_dana,
            nilai,
            volume,
            satuan,
            total,
            totalSPD,
            realisasiSPD,
            sisaSPD,
            totalAnggaranKas,
            realisasiAnggaranKas,
            sisaAnggaranKas,
            anggaran,
            realisasiAnggaran,
            sisaAnggaran,
            rencanaPergeseranAnggaran,
            realisasiPergeseranAnggaran,
            sisaPergeseranAnggaran,
            nilaisumberdana,
            realisasinilaisumberdana,
            sisanilaisumberdana

        });

        // Perbarui tabel dengan data terbaru
        updateTable();

        // Tutup modal setelah simpan
        $("#inputKegiatanModal").modal("hide");

        // Reset form setelah simpan
        $("#formInputKegiatan")[0].reset();
    });



    $('#inputKegiatanModal').on('shown.bs.modal', function () {
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

        // Ambil data Sub Kegiatan via AJAX
        $.ajax({
            url: "{{ route('transaksi.get-sub-kegiatan') }}",
            type: 'GET',
            success: function(response) {
                $('#kd_sub_kegiatan').empty().append('<option value="">Pilih Sub Kegiatan</option>');
                $.each(response, function(index, item) {
                    $('#kd_sub_kegiatan').append(
                        '<option value="' + item.kd_sub_kegiatan + '" data-nm_sub_kegiatan="' + item.nm_sub_kegiatan + '">' +
                        item.kd_sub_kegiatan + ' || ' + item.nm_sub_kegiatan +
                        '</option>'
                    );
                });
                $('#kd_sub_kegiatan').trigger('change');
            },
            error: function(xhr, status, error) {
                console.error("Error fetching kd_sub_kegiatan: ", error);
            }
        });

        // Inisialisasi Select2 untuk Nomor SP2D
        // $('#no_sp2d').select2({
        //     dropdownParent: $('#inputKegiatanModal .modal-content'),
        //     placeholder: 'Pilih Nomor SP2D',
        //     width: 'resolve',
        //     theme: 'bootstrap-5',
        // });

        // // Ambil data Nomor SP2D via AJAX
        // $.ajax({
        //     url: "{{ route('transaksi.get-no_sp2d') }}",
        //     type: 'GET',
        //     success: function(response) {
        //         $('#no_sp2d').empty().append('<option value="">Pilih Nomor SP2D</option>');
        //         $.each(response, function(index, item) {
        //             $('#no_sp2d').append(
        //                 '<option value="' + item.no_sp2d + '">' +
        //                 item.no_sp2d + ' || ' + item.tgl_sp2d +
        //                 '</option>'
        //             );
        //         });
        //         $('#no_sp2d').trigger('change');
        //     },
        //     error: function(xhr, status, error) {
        //         console.error("Error fetching no_sp2d: ", error);
        //     }
        // });

        // $('#kd_dana').select2({
        //     dropdownParent: $('#inputKegiatanModal .modal-content'),
        //     placeholder: 'Pilih Sub Kegiatan',
        //     width: 'resolve',
        //     theme: 'bootstrap-5',
        // });

        // // Ambil data Sub Kegiatan via AJAX
        // $.ajax({
        //     url: "{{ route('transaksi.get-sumberdana') }}",
        //     type: 'GET',
        //     success: function(response) {
        //         $('#kd_dana').empty().append('<option value="">Pilih Sub Kegiatan</option>');
        //         $.each(response, function(index, item) {
        //             $('#kd_dana').append(`
        //             <option value="${item.kd_dana}"
        //                     data-id_sumberdana="${item.id}"
        //                     data-nm_dana="${item.nm_dana}"
        //                     data-anggaran_tahun="${item.anggaran_tahun}">
        //                 ${item.kd_dana} || ${item.nm_dana}
        //             </option>
        //         `);
        //         });
        //         $('#kd_dana').trigger('change');
        //     },
        //     error: function(xhr, status, error) {
        //         console.error("Error fetching kd_dana: ", error);
        //     }
        // });

        $('#kd_rek').change(function () {
        var kd_rek = $(this).val();
        var kd_sub_kegiatan = $('#kd_sub_kegiatan').val(); // Ambil kode rekening yang dipilih
        var csrfToken = $('meta[name="csrf-token"]').attr('content'); // Ambil CSRF token

        if (kd_rek) {
            $.ajax({
                url: "{{ route('transaksi.getrealisasi') }}", // Pastikan route ini sesuai
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken // Tambahkan CSRF token
                },
                data: { kd_rek: kd_rek, kd_sub_kegiatan: kd_sub_kegiatan },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#realisasiSPD').val(formatRupiah2(response.realisasiSPD ?? 0));
                        $('#realisasiAnggaranKas').val(formatRupiah2(response.realisasiAnggaranKas ?? 0));
                        $('#realisasiAnggaran').val(formatRupiah2(response.realisasiAnggaran ?? 0));
                        $('#realisasinilaisumberdana').val(formatRupiah2(response.realisasiSumberDana ?? 0));

                        hitungSisa('totalSPD', 'realisasiSPD', 'sisaSPD');
                        hitungSisa('totalAnggaranKas', 'realisasiAnggaranKas', 'sisaAnggaranKas');
                        hitungSisa('anggaran', 'realisasiAnggaran', 'sisaAnggaran');
                        hitungSisa('nilaisumberdana', 'realisasinilaisumberdana', 'sisanilaisumberdana');
                    } else {
                        alert("Data realisasi tidak ditemukan!");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
                }
            });
        }
    });

    function formatRupiah2(angka) {
        let number = parseInt(angka); // Konversi ke integer untuk menghilangkan desimal
        let number_string = number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Pisahkan ribuan dengan titik
        return 'Rp. ' + number_string; // Tambahkan prefix Rp.
    }

    $('#kd_sub_kegiatan').on('change', function() {
        var kd_sub_kegiatan = $(this).val();
        var nm_sub_kegiatan = $(this).find('option:selected').data('nm_sub_kegiatan') || '';

        // Set nama sub kegiatan ke input yang disabled
        $('#nm_sub_kegiatan').val(nm_sub_kegiatan);

        // Reset rekening dropdown jika tidak ada sub kegiatan yang dipilih
        if (!kd_sub_kegiatan) {
            $('#kd_rek').empty().append('<option value="">Pilih Rekening</option>').trigger('change');
            return;
        }

        // Ambil data rekening berdasarkan kd_sub_kegiatan yang dipilih
        $.ajax({
            url: "{{ route('transaksi.get-rekening') }}",
            type: 'GET',
            data: { kd_sub_kegiatan: kd_sub_kegiatan },
            success: function(response) {
                // Reset rekening dropdown
                $('#kd_rek').empty().append('<option value="">Pilih Rekening</option>');

                // Populate rekening options
                $.each(response, function(index, item) {
                    $('#kd_rek').append(
                        `<option value="${item.kd_rek}"
                            data-nm_rek="${item.nm_rek}"
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
                            ${item.kd_rek} || ${item.nm_rek}
                        </option>`
                    );
                });

                // Refresh Select2 to show the new options
                $('#kd_rek').trigger('change');
            },
            error: function(xhr, status, error) {
                console.error("Error fetching kd_rek: ", error);
                $('#kd_rek').empty().append('<option value="">Error loading data</option>').trigger('change');
            }
        });
    });


$('#kd_dana').select2({
        dropdownParent: $('#inputKegiatanModal .modal-content'),
        placeholder: 'Pilih Sumber Dana',
        width: 'resolve',
        theme: 'bootstrap-5',
    });

    // Ketika kd_rek dipilih, ambil daftar kd_dana berdasarkan id_sumberdana
    $('#kd_rek').on('change', function() {
        var idSumberDana = $('#kd_rek option:selected').data('id_sumberdana');

        if (idSumberDana) {
            $.ajax({
                url: "{{ route('transaksi.get-sumberdana') }}",
                type: 'GET',
                data: { id_sumberdana: idSumberDana }, // Kirim id_sumberdana ke controller
                success: function(response) {
                    $('#kd_dana').empty().append('<option value="">Pilih Sumber Dana</option>');
                    $.each(response, function(index, item) {
                        $('#kd_dana').append(`
                            <option value="${item.id}"
                             data-id_sumberdana="${item.id}"
                             data-nm_dana="${item.nm_dana}"
                             data-anggaran_tahun="${item.anggaran_tahun}">
                         ${item.kd_dana} || ${item.nm_dana}
                     </option>
                 `);


                    });
                    $('#kd_dana').trigger('change');
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching kd_dana: ", error);
                }
            });
        } else {
            $('#kd_dana').empty().append('<option value="">Pilih Sumber Dana</option>').trigger('change');
        }
    });

    });


    $(document).on('change', '#kd_sub_kegiatan', function() {
        var selectedOption = $(this).find('option:selected');
        var nmSubKegiatan = selectedOption.data('nm_sub_kegiatan') || '';
        $('#nm_sub_kegiatan').val(nmSubKegiatan);
    });

    $(document).on('change', '#kd_dana', function() {
        var selectedOption = $(this).find('option:selected');
        var anggaranTahun = selectedOption.data('anggaran_tahun') || '';
        var nmSumberdana = selectedOption.data('nm_dana') || '';
        $('#nm_dana').val(nmSumberdana);
        $('#nilaisumberdana').val(formatRupiah1(anggaranTahun));
        hitungSisa("nilaisumberdana", "realisasinilaisumberdana", "sisanilaisumberdana");
    });

    $(document).on('change', '[name="tgl_bukti"]', function () {
        var tglInput = $(this).val();
        if (tglInput) {
            var selectedMonth = new Date(tglInput).getMonth() + 1;
            var selectedTriwulan = Math.ceil(selectedMonth / 3); // Ambil bulan (1-12)
            $('#kd_rek').data('selected-month', selectedMonth);
            $('#kd_rek').data('selected-triwulan', selectedTriwulan);
            console.log("Bulan yang dipilih:", selectedMonth);
        }
    });

    // Tangkap perubahan pada dropdown rekening
    // Tangkap perubahan pada dropdown rekening
    $(document).on('change', '#kd_rek', function () {
    let selectedOption = $(this).find('option:selected');
    let nmrek = selectedOption.data('nm_rek') || '';
    let idsumberdana = selectedOption.data('id_sumberdana') || '';
    let anggaranTahun = parseFloat(selectedOption.data('anggaran')) || 0;
    let status_anggaran = selectedOption.data('status_anggaran') || '';
    let status_anggaran_kas = selectedOption.data('status_anggaran_kas') || '';

    // Pastikan selectedMonth & selectedTriwulan sudah di-set
    let selectedMonth = $('#kd_rek').data('selected-month') || new Date().getMonth() + 1;
    let selectedTriwulan = $('#kd_rek').data('selected-triwulan') || Math.ceil(selectedMonth / 3);

    console.log("Bulan saat ini:", selectedMonth);
    console.log("Triwulan saat ini:", selectedTriwulan);

    // Pilih anggaran triwulan yang sesuai
    let totalSPD = parseFloat(selectedOption.data(`anggaran-tw${selectedTriwulan}`)) || 0;

    // Ambil anggaran sesuai bulan yang dipilih
    let anggaranBulan = parseFloat(selectedOption.data(`rek${selectedMonth}`)) || 0;

    let totalAnggaranKas = 0;
    for (let i = 1; i <= selectedMonth; i++) {
        totalAnggaranKas += parseFloat(selectedOption.data(`rek${i}`)) || 0;
    }

    let totalAnggaranSebelumnya = 0;
    for (let i = 1; i < selectedMonth; i++) {
        totalAnggaranSebelumnya += parseFloat(selectedOption.data(`rek${i}`)) || 0;
    }

    let totalspdsebelumnya = 0;
    for (let i = 1; i < selectedTriwulan; i++) {
        totalspdsebelumnya += parseFloat(selectedOption.data(`anggaran-tw${i}`)) || 0;
    }
    console.log("Total anggaran sebelumnya:", totalspdsebelumnya);

    let kd_rek = $(this).val();

    $.post("{{ route('transaksi.get-total-nilai') }}", {
        _token: "{{ csrf_token() }}",
        kd_rek: kd_rek
    }).done(function(response) {
        let totalNilai = parseFloat(response.total_nilai) || 0;
        let totalSPDFinal = (selectedTriwulan === 1) ? totalSPD : totalSPD + (totalspdsebelumnya - totalNilai);

        $('#nm_rek').val(nmrek);
        $('#id_sumberdana').val(idsumberdana);
        $('#statusAnggaran').val(status_anggaran);
        $('#statusAnggaranKas').val(status_anggaran_kas);
        $('#totalSPD').val(formatRupiah1(totalSPDFinal));
        $('#anggaran').val(formatRupiah1(anggaranTahun));
        $('#totalAnggaranKas').val(formatRupiah1(totalAnggaranKas));

        console.table({
            "Total SPD": totalSPDFinal,
            "Total Anggaran Kas": totalAnggaranKas,
            "Total Nilai": totalNilai
        });

        hitungSisa("totalSPD", "realisasiSPD", "sisaSPD");
        hitungSisa("anggaran", "realisasiAnggaran", "sisaAnggaran");
        hitungSisa("totalAnggaranKas", "realisasiAnggaranKas", "sisaAnggaranKas");
    }).fail(function(xhr, status, error) {
        console.error("Error fetching total nilai:", error);
    });
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
    $('#inputKegiatanModal').on('hidden.bs.modal', function () {
        $('#kd_sub_kegiatan').select2('destroy');
        $('#nm_sub_kegiatan').val('');
        $('#kd_dana').select2('destroy');
        $('#nm_dana').val('');
    });
});



// Panggil fungsi hitungSisa() secara otomatis setelah halaman selesai dimuat
window.onload = function () {
    hitungSisa("totalSPD", "realisasiSPD", "sisaSPD");
    hitungSisa("totalAnggaranKas", "realisasiAnggaranKas", "sisaAnggaranKas");
    hitungSisa("anggaran", "realisasiAnggaran", "sisaAnggaran");
    hitungSisa("rencanaPergeseranAnggaran", "realisasiPergeseranAnggaran", "sisaPergeseranAnggaran");
    hitungSisa("nilaisumberdana", "realisasinilaisumberdana", "sisanilaisumberdana");
};

</script>
@endpush
