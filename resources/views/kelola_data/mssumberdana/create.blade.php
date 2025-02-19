@extends('template.app')
@section('title', 'Tambah Sumber Dana')
@section('content')
    <div class="page-heading">
        <h2>{{ isset($pajak) ? 'Edit Sumber Dana' : 'Tambah Sumber Dana' }}</h2>
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
                    <div class="font-35 text-white"><i class='bx bxs-message-square-x'></i></div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">Error</h6>
                        <div class="text-white">{{ session('message') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                <form id="productForm" action="{{ isset($pajak) ? route('kelola_data.mssumberdana.update', $pajak->id) : route('kelola_data.mssumberdana.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($pajak))
                        @method('POST')
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kd_dana">Kode Rekening</label>
                                <input type="text" name="kd_dana" id="kd_dana" class="form-control" value="{{ old('kd_dana', $pajak->kd_dana ?? '') }}" placeholder="Input Kode Rekening">
                            </div>
                            <div class="form-group">
                                <label for="nm_dana">Nama Rekening</label>
                                <input type="text" name="nm_dana" id="nm_dana" class="form-control" value="{{ old('nm_dana', $pajak->nm_dana ?? '') }}" placeholder="Nama Rekening">
                            </div>
                            <div class="form-group">
                                <label for="anggaran_tahun">Anggaran Pertahun</label>
                                <input type="text" name="anggaran_tahun" oninput="formatRupiah(this);" id="anggaran_tahun" class="form-control" value="{{ old('anggaran_tahun', $pajak->anggaran_tahun ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="anggaran_tw1">Anggaran TW1</label>
                                <input type="text" name="anggaran_tw1" oninput="formatRupiah(this);" id="anggaran_tw1" class="form-control" value="{{ old('anggaran_tw1', $pajak->anggaran_tw1 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="anggaran_tw2">Anggaran TW2</label>
                                <input type="text" name="anggaran_tw2" oninput="formatRupiah(this);" id="anggaran_tw2" class="form-control" value="{{ old('anggaran_tw2', $pajak->anggaran_tw2 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="anggaran_tw3">Anggaran TW3</label>
                                <input type="text" name="anggaran_tw3" oninput="formatRupiah(this);" id="anggaran_tw3" class="form-control" value="{{ old('anggaran_tw3', $pajak->anggaran_tw3 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="anggaran_tw4">Anggaran TW4</label>
                                <input type="text" name="anggaran_tw4" oninput="formatRupiah(this);" id="anggaran_tw4" class="form-control" value="{{ old('anggaran_tw4', $pajak->anggaran_tw4 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek9">Rek 9</label>
                                <input type="text" name="rek9" oninput="formatRupiah(this);" id="rek9" class="form-control" value="{{ old('rek9', $pajak->rek9 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek10">Rek 10</label>
                                <input type="text" name="rek10" oninput="formatRupiah(this);" id="rek10" class="form-control" value="{{ old('rek10', $pajak->rek10 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek12">Rek 12</label>
                                <input type="text" name="rek12" oninput="formatRupiah(this);" id="rek12" class="form-control" value="{{ old('rek12', $pajak->rek12 ?? '') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rek1">Rek 1</label>
                                <input type="text" name="rek1" oninput="formatRupiah(this);" id="rek1" class="form-control" value="{{ old('rek1', $pajak->rek1 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek2">Rek 2</label>
                                <input type="text" name="rek2" oninput="formatRupiah(this);" id="rek2" class="form-control" value="{{ old('rek2', $pajak->rek2 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek3">Rek 3</label>
                                <input type="text" name="rek3" oninput="formatRupiah(this);" id="rek3" class="form-control" value="{{ old('rek3', $pajak->rek3 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek4">Rek 4</label>
                                <input type="text" name="rek4" oninput="formatRupiah(this);" id="rek4" class="form-control" value="{{ old('rek4', $pajak->rek4 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek5">Rek 5</label>
                                <input type="text" name="rek5" oninput="formatRupiah(this);" id="rek5" class="form-control" value="{{ old('rek5', $pajak->rek5 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek6">Rek 6</label>
                                <input type="text" name="rek6" oninput="formatRupiah(this);" id="rek6" class="form-control" value="{{ old('rek6', $pajak->rek6 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek7">Rek 7</label>
                                <input type="text" name="rek7" oninput="formatRupiah(this);" id="rek7" class="form-control" value="{{ old('rek7', $pajak->rek7 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek8">Rek 8</label>
                                <input type="text" name="rek8" oninput="formatRupiah(this);" id="rek8" class="form-control" value="{{ old('rek8', $pajak->rek8 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek11">Rek 11</label>
                                <input type="text" name="rek11" oninput="formatRupiah(this);" id="rek11" class="form-control" value="{{ old('rek11', $pajak->rek11 ?? '') }}">
                            </div>

                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary">{{ isset($pajak) ? 'Update' : 'Tambah' }}</button>
                        <a href="{{ route('kelola_data.mssumberdana.index') }}" class="btn btn-warning">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
function formatRupiah(input) {
    let angka = input.value.replace(/\D/g, ""); // Hanya angka
    let rupiah = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0
    }).format(angka);
    input.value = rupiah.replace("Rp", "").trim(); // Menghilangkan "Rp" agar mudah diproses
}
</script>
@endpush
