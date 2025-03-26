@extends('template.app')
@section('title', 'LPJ UP/GU')
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
                    <div class="font-35 text-white"><i class='bx bxs-message-square-x'></i></div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">Error</h6>
                        <div class="text-white">{{ session('message') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <h5>LPJ UP/GU</h5>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('lpj.update', Crypt::encryptString($lpj->no_lpj)) }}" id="formBpkb">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">SKPD</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput" type="text"
                                placeholder="Tidak perlu diisi, otomatis" value="{{ $lpj->kd_skpd }}" disabled>
                            <input type="hidden"  value="{{ $lpj->kd_skpd }}">
                        </div>
                        <label class="col-sm-2 col-form-label">Nama SKPD</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="{{ $lpj->namaSkpd }}" disabled>
                            <input type="hidden" value="{{ $lpj->namaSkpd }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor LPJ</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="{{ $lpj->no_lpj }}" disabled>
<input type="hidden" name="no_lpj" value="{{ $lpj->no_lpj }}">
                        </div>
                        <label class="col-sm-2 col-form-label">Tanggal LPJ</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tgl_lpj') is-invalid @enderror" type="date"
                                name="tgl_lpj"
                                value="{{ $lpj->tgl_lpj }}">
                            @error('tgl_lpj')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" name="keterangan"
                                placeholder="Keterangan">{{ old('keterangan', $lpj->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <div class="mb-3 text-end">
                        <a href="{{ route('lpj.index') }}" class="btn btn-warning">Kembali</a>
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
                <h5 class="mb-0 fw-bold text-primary">List LPJ UP/GU</h5>
            </div>
        </div>
        <div class="card">

            <div class="card-body">
                <div class="card-body">
                    <div class="table-responsive">
                        <div class="row mb-2">
                            <div class="col-12">
                                <label class="form-label"><strong>Tanggal Transaksi</strong></label>
                            </div>
                        </div>
                        <form id="filterForm">
                            <div class="row mb-3 align-items-center">
                                <div class="col-sm-3">
                                    <input class="form-control" type="date" name="tgl_awal" id="tgl_awal" value="{{ $lpj->tgl_awal }}">
                                </div>
                                <div class="col-sm-3">
                                    <input class="form-control @error('tgl_akhir') is-invalid @enderror" type="date" name="tgl_akhir" id="tgl_akhir" value="{{ $lpj->tgl_akhir }}">
                                    @error('tgl_akhir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- <div class="col-sm-3 d-flex gap-2">
                                    <button type="button" id="btnTampilkan" class="btn btn-success d-flex align-items-center">
                                        <i class="fa-solid fa-eye me-2"></i> Tampilkan
                                    </button>
                                    <button type="button" class="btn btn-success d-flex align-items-center" onclick="kosongkanTanggal()">
                                        <i class="fa-solid fa-trash me-2"></i> Kosongkan
                                    </button>
                                </div> --}}
                            </div>
                        </form>
                        <table class="table align-middle mb-0" id="tabelPotongan" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>SKPD</th>
                                    <th>No Bukti</th>
                                    <th>Sub Kegiatan</th>
                                    <th>Rekening</th>
                                    <th>Nama Rekening</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($potonganDetails as $potongan)
                                    <tr>
                                        <td>{{ $potongan->namaSkpd }}</td>
                                        <td>{{ $potongan->no_bukti }}</td>
                                        <td>{{ $potongan->nm_sub_kegiatan }}</td>
                                        <td>{{ $potongan->kd_rek6 }}</td>
                                        <td>{{ $potongan->nm_rek6 }}</td>
                                        <td>{{ number_format($potongan->nilai, 0, ',', '.') }}</td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                    <p id="totalBelanja" name="totalBelanja" style="text-align: right; margin-top: 10px; font-size: 16px; font-weight: bold;">
                        <strong>Total :</strong> Rp 0
                    </p>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('js')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function updateTotal() {
    let tabel = document.getElementById("tabelPotongan").getElementsByTagName("tbody")[0];
    let rows = tabel.getElementsByTagName("tr");
    let total = 0;

    for (let i = 0; i < rows.length; i++) {
        let nilaiCell = rows[i].cells[5];
        let nilaiText = nilaiCell.innerHTML.replace(/\D/g, ''); // Ambil angka saja
        console.log("Nilai di baris " + (i + 1) + ": " + nilaiText); // Debugging
        total += parseFloat(nilaiText) || 0;
    }

     // Debugging

    let formattedTotal = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(total);

    document.getElementById("totalBelanja").innerHTML = `<strong>Total :</strong> ${formattedTotal}`;
}

document.addEventListener('DOMContentLoaded', function() {
    // Set initial total
    updateTotal();
});
</script>
@endpush
