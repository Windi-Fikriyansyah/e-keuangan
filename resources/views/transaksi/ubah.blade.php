@extends('template.app')
@section('title', 'Lihat Transaksi')
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
                            <input type="text" disabled class="form-control" value=" {{ $transaksi->kd_skpd }} || {{ $transaksi->nm_skpd }}" id="jenis" placeholder="Silahkan Pilih" autofocus>
                        </div>
                    </div>


                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Bukti</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('no_bukti') is-invalid @enderror readonlyInput"
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="no_bukti"
                                value="{{ $transaksi->no_bukti }}" disabled>
                        </div>
                        <label class="col-sm-2 col-form-label">Tanggal Transaksi</label>
                        <div class="col-sm-4">
                            <input id="tgl_bukti" class="form-control @error('tgl_bukti') is-invalid @enderror" type="date"
                                placeholder="Tidak perlu diisi, otomatis" name="tgl_bukti"
                                value="{{ $transaksi->tgl_bukti }}">
                            @error('tgl_bukti')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Jenis Beban</label>
                        <div class="col-sm-4">
                            <select class="form-select @error('jenis_beban') is-invalid @enderror" name="jenis_beban">
                                <option value="{{ $transaksi->jenis_beban }}" disabled selected>{{ $transaksi->jenis_beban }}</option>
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
                                placeholder="Keterangan">{{ old('ket', $transaksi->ket) }}</textarea>
                            @error('ket')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <input type="checkbox" id="terima_sp2d" name="jenis_terima_sp2d" value="1" class="form-check-input"
                                {{ old('jenis_terima_sp2d', $transaksi->jenis_terima_sp2d) == 1 ? 'checked' : '' }}>
                            <label for="terima_sp2d">Terima SP2D</label>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <input type="checkbox" id="perlimpahan" name="jenis_perlimpahan" value="1" class='form-check-input'  {{ old('perlimpahan', $transaksi->perlimpahan) == 1 ? 'checked' : '' }}>
                                    <label for="perlimpahan">Perlimpahan</label>
                        </div>

                    </div>

                    <div class="mb-3 text-end">
                        <input type="hidden" name="total_belanja" id="hiddenTotalBelanja">
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($potonganDetails as $index => $potongan)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $potongan->nm_sub_kegiatan }}</td>
                                        <td>{{ $potongan->kd_rek6 }}</td>
                                        <td>{{ $potongan->nm_rek6 }}</td>
                                        <td>{{ number_format($potongan->nilai, 0, ',', '.') }}</td>
                                        <td>{{ $potongan->nm_dana }}</td>

                                    </tr>
                                @endforeach
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

$(document).ready(function () {
    function hitungTotalBelanja() {
        let total = 0;
        $("#pajak tbody tr").each(function () {
            let nilai = $(this).find("td:eq(4)").text().replace(/\./g, "").trim(); // Ambil nilai dan hilangkan format ribuan
            if (!isNaN(nilai) && nilai !== "") {
                total += parseInt(nilai);
            }
        });
        $("#totalBelanja").html(`<strong>Total belanja:</strong> Rp ${total.toLocaleString('id-ID')}`);
        $("#hiddenTotalBelanja").val(total);
    }

    // Panggil fungsi saat halaman dimuat
    hitungTotalBelanja();
});

</script>
@endpush
