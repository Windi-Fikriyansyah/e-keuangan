@extends('template.app')
@section('title', 'Tambah Peminjaman Sertifikat')
@section('content')
    <div class="page-heading">
        <h3>Tambah Peminjaman Sertifikat</h3>
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
        <div class="card">
            <div class="card-body">
                <form method="POST"action="{{ route('peminjaman.sertifikat.store') }}" id="formSertifikat" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Register</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('nomorRegister') is-invalid @enderror select_option"
                                name="nomorRegister" id="nomorRegister" data-placeholder="Silahkan Pilih" autofocus>
                                <option value="" selected>Silahkan Pilih</option>
                            </select>
                            @error('nomorRegister')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Surat</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorSurat') is-invalid @enderror readonlyInput"
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="nomorSurat"
                                value="{{ $nomorSurat }}" readonly>
                            @error('nomorSurat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Tanggal Pinjam</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tanggalPinjam') is-invalid @enderror" type="date"
                                placeholder="Tidak perlu diisi, otomatis" name="tanggalPinjam"
                                value="{{ old('tanggalPinjam') ? old('tanggalPinjam') : date('Y-m-d') }}">
                            @error('tanggalPinjam')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">

                        <label class="col-sm-2 col-form-label">Nama Ksbtgn</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('namaKsbtgn') is-invalid @enderror "
                                type="text" placeholder="Isi nama ksbtgn" name="namaKsbtgn"
                                value="{{ old('namaKsbtgn') }}">
                            @error('namaKsbtgn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="col-sm-2 col-form-label">Nip Ksbtgn</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nipKsbtgn') is-invalid @enderror"
                                type="text" placeholder="Isi NIP Ksbtgn" name="nipKsbtgn"
                                value="{{ old('nipKsbtgn') }}">
                            @error('nipKsbtgn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="row mb-3">

                        <label class="col-sm-2 col-form-label">Peruntukan</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('peruntukan') is-invalid @enderror"
                                type="text" placeholder="Isi Peruntukan" name="peruntukan"
                                value="{{ old('peruntukan') }}">
                            @error('peruntukan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <label class="col-sm-2 col-form-label">No Telepon Ksbtgn</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('noTelpKsbtgn') is-invalid @enderror "
                                type="text" placeholder="Isi No Telepon Ksbtgn" name="noTelpKsbtgn"
                                value="{{ old('noTelpKsbtgn') }}">
                            @error('noTelpKsbtgn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                    </div>
                    <div class="row mb-3">


                        <label class="col-sm-2 col-form-label">Nomor Sertifikat</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorSertifikat') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="nomorSertifikat"
                                value="{{ old('nomorSertifikat') }}">
                            @error('nomorSertifikat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>




                    </div>
                    <div class="row mb-3">

                        <label class="col-sm-2 col-form-label">Pemegang Hak</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('pemegangHak') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="pemegangHak"
                                value="{{ old('pemegangHak') }}">
                            @error('pemegangHak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="col-sm-2 col-form-label">Tanggal Terbit</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tanggal') is-invalid @enderror readonlyInput" readonly type="date"
                                placeholder="Tidak perlu diisi, otomatis" name="tanggal"
                                value="{{ old('tanggal') }}">
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Sertifikat Asli</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('sertifikatAsli') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="sertifikatAsli"
                                value="{{ old('sertifikatAsli') }}">
                            @error('sertifikatAsli')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">NIB</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nib') is-invalid @enderror readonlyInput" readonly type="text"
                                placeholder="Tidak perlu diisi, otomatis" name="nib" value="{{ old('nib') }}">
                            @error('nib')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div  class="row mb-3">
                        <label class="col-sm-2 col-form-label">Balik Nama</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('balikNama') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="balikNama"
                                value="{{ old('balikNama') }}">
                            @error('balikNama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Luas</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('luas') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="luas"
                                value="{{ old('luas') }}">
                            @error('luas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Alamat</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('alamat') is-invalid @enderror readonlyInput" readonly type="text"
                                placeholder="Tidak perlu diisi, otomatis" name="alamat">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('keterangan') is-invalid @enderror readonlyInput" readonly type="text"
                                placeholder="Tidak perlu diisi, otomatis" name="keterangan">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a href="{{ route('peminjaman.sertifikat.index') }}" class="btn btn-warning">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <style>
        .readonlyInput {
            background-color: #e9ecef
        }
    </style>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            reset();

            $('#nomorRegister').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih...",
                minimumInputLength: 0,
                ajax: {
                    url: "{{ route('peminjaman.sertifikat.load_sertifikat') }}",
                    dataType: 'json',
                    type: "POST",
                    data: function(params) {
                        return {
                            q: $.trim(params.term)
                        };
                    },
                    processResults: function(data) {
                        console.log(data)
                        return {
                            results: data.map((sertifikat) => {
                                return {
                                    text: sertifikat.nomorRegister + ' | ' + sertifikat.nib +
                                        ' | ' + sertifikat.nomorSertifikat + ' | ' + sertifikat.kodeSkpd,
                                    id: sertifikat.nomorRegister,
                                    nomorSertifikat: sertifikat.nomorSertifikat,
                                    nib: sertifikat.nib,
                                    tanggal: sertifikat.tanggalSertifikat,
                                    luas: sertifikat.luas,
                                    hak: sertifikat.hak,
                                    pemegangHak: sertifikat.pemegangHak,
                                    asalUsul: sertifikat.asalUsul,
                                    alamat: sertifikat.alamat,
                                    sertifikatAsli: sertifikat.sertifikatAsli,
                                    balikNama: sertifikat.balikNama,
                                    penggunaan: sertifikat.penggunaan,
                                    keterangan: sertifikat.keterangan,

                                };
                            }),
                            pagination: {
                                more: data.current_page < data.last_page,
                            },
                        };
                    },
                    cache: true
                }
            }).on("select2:select", function(e) {
                var selected = e.params.data;
                console.log(selected)
                if (typeof selected !== "undefined") {
                    $("[name='nomorSertifikat']").val(selected.nomorSertifikat);
                    $("[name='nib']").val(selected.nib);
                    $("[name='tanggal']").val(selected.tanggal);
                    $("[name='luas']").val(selected.luas);
                    $("[name='hak']").val(selected.hak);
                    $("[name='pemegangHak']").val(selected.pemegangHak);
                    $("[name='asalUsul']").val(selected.asalUsul);
                    $("[name='alamat']").val(selected.alamat);
                    $("[name='sertifikatAsli']").val(selected.sertifikatAsli);
                    $("[name='balikNama']").val(selected.balikNama);
                    $("[name='penggunaan']").val(selected.penggunaan);
                    $("[name='keterangan']").val(selected.keterangan);
                }
            }).on("select2:unselecting", function(e) {
                $("form").each(function() {
                    this.reset()
                });
                ("#allocationsDiv").hide();
                $("[name='creditor_id']").val("");
            }).val("{{ old('nomorRegister') }}").trigger('change');

            function reset() {
                $("[name='nomorSertifikat']").val(null);
                    $("[name='nib']").val(null);
                    $("[name='tanggal']").val(null);
                    $("[name='luas']").val(null);
                    $("[name='hak']").val(null);
                    $("[name='pemegangHak']").val(null);
                    $("[name='asalUsul']").val(null);
                    $("[name='alamat']").val(null);
                    $("[name='sertifikatAsli']").val(null);
                    $("[name='balikNama']").val(null);
                    $("[name='penggunaan']").val(null);
                    $("[name='keterangan']").val(null);
            }
        });
    </script>
@endpush
