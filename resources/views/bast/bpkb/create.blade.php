@extends('template.app')
@section('title', 'Tambah Peminjaman BPKB')
@section('content')
    <div class="page-heading">
        <h3>Tambah Peminjaman BPKB</h3>
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
                <form method="POST"action="{{ route('peminjaman.bpkb.store') }}" id="formBpkb">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Arsip Dokumen</label>
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
                                value="{{ old('nomorSurat') }}" readonly>
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
                        <label class="col-sm-2 col-form-label">Nama PBP</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('namaPbp') is-invalid @enderror" type="text"
                                placeholder="Isi nama pengurus barang pengguna" name="namaPbp" value="{{ old('namaPbp') }}">
                            @error('namaPbp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">NIP PBP</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nipPbp') is-invalid @enderror" type="text"
                                placeholder="Isi nip pengurus barang pengguna" name="nipPbp" value="{{ old('nipPbp') }}">
                            @error('nipPbp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor BPKB</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorBpkb') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="nomorBpkb"
                                value="{{ old('nomorBpkb') }}">
                            @error('nomorBpkb')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Nomor Telp PBP</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorTelpPbp') is-invalid @enderror" type="text"
                                placeholder="Isi nomor telepon pengurus barang pengguna" name="nomorTelpPbp"
                                value="{{ old('nomorTelpPbp') }}">
                            @error('nomorTelpPbp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Polisi</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorPolisi') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="nomorPolisi"
                                value="{{ old('nomorPolisi') }}">
                            @error('nomorPolisi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Nama Pemilik</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('namaPemilik') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="namaPemilik"
                                value="{{ old('namaPemilik') }}">
                            @error('namaPemilik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Jenis</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('jenis') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="jenis"
                                value="{{ old('jenis') }}">
                            @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Merk</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('merk') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="merk"
                                value="{{ old('merk') }}">
                            @error('merk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tipe</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tipe') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="tipe"
                                value="{{ old('tipe') }}">
                            @error('tipe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Model</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('model') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="model"
                                value="{{ old('model') }}">
                            @error('model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tahun Pembuatan</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tahunPembuatan') is-invalid @enderror readonlyInput"
                                readonly type="text" placeholder="Tidak perlu diisi, otomatis" name="tahunPembuatan"
                                value="{{ old('tahunPembuatan') }}">
                            @error('tahunPembuatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Tahun Perakitan</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tahunPerakitan') is-invalid @enderror readonlyInput"
                                readonly type="text" placeholder="Tidak perlu diisi, otomatis" name="tahunPerakitan"
                                value="{{ old('tahunPerakitan') }}">
                            @error('tahunPerakitan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Isi Silinder</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('isiSilinder') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="isiSilinder"
                                value="{{ old('isiSilinder') }}">
                            @error('isiSilinder')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Warna</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('warna') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="warna"
                                value="{{ old('warna') }}">
                            @error('warna')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Rangka</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorRangka') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="nomorRangka"
                                value="{{ old('nomorRangka') }}">
                            @error('nomorRangka')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Nomor Mesin</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorMesin') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="nomorMesin"
                                value="{{ old('nomorMesin') }}">
                            @error('nomorMesin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Polisi Lama</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorPolisiLama') is-invalid @enderror readonlyInput"
                                readonly type="text" placeholder="Tidak perlu diisi, otomatis" name="nomorPolisiLama"
                                value="{{ old('nomorPolisiLama') }}">
                            @error('nomorPolisiLama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Nomor Bpkb Lama</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorBpkbLama') is-invalid @enderror readonlyInput"
                                readonly type="text" placeholder="Tidak perlu diisi, otomatis" name="nomorBpkbLama"
                                value="{{ old('nomorBpkbLama') }}">
                            @error('nomorBpkbLama')
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
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keperluan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('keperluan') is-invalid @enderror" type="text" name="keperluan"
                                placeholder="Isi keperluan peminjaman BPKB">{{ old('keperluan') }}</textarea>
                            @error('keperluan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a href="{{ route('peminjaman.bpkb.index') }}" class="btn btn-warning">Kembali</a>
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
                    url: "{{ route('peminjaman.bpkb.load_bpkb') }}",
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
                            results: data.map((bpkb) => {
                                return {
                                    text: bpkb.nomorRegister + ' | ' + bpkb.nomorPolisi +
                                        ' | ' + bpkb.nomorBpkb + ' | ' + bpkb.kodeSkpd,
                                    id: bpkb.nomorRegister,
                                    nomorBpkb: bpkb.nomorBpkb,
                                    nomorPolisi: bpkb.nomorPolisi,
                                    namaPemilik: bpkb.namaPemilik,
                                    jenis: bpkb.jenis,
                                    merk: bpkb.merk,
                                    tipe: bpkb.tipe,
                                    model: bpkb.model,
                                    tahunPembuatan: bpkb.tahunPembuatan,
                                    tahunPerakitan: bpkb.tahunPerakitan,
                                    isiSilinder: bpkb.isiSilinder,
                                    warna: bpkb.warna,
                                    alamat: bpkb.alamat,
                                    nomorRangka: bpkb.nomorRangka,
                                    nomorMesin: bpkb.nomorMesin,
                                    keterangan: bpkb.keterangan,
                                    nomorPolisiLama: bpkb.nomorPolisiLama,
                                    nomorBpkbLama: bpkb.nomorBpkbLama,
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
                    $("[name='nomorBpkb']").val(selected.nomorBpkb);
                    $("[name='nomorPolisi']").val(selected.nomorPolisi);
                    $("[name='namaPemilik']").val(selected.namaPemilik);
                    $("[name='jenis']").val(selected.jenis);
                    $("[name='merk']").val(selected.merk);
                    $("[name='tipe']").val(selected.tipe);
                    $("[name='model']").val(selected.model);
                    $("[name='tahunPembuatan']").val(selected.tahunPembuatan);
                    $("[name='tahunPerakitan']").val(selected.tahunPerakitan);
                    $("[name='isiSilinder']").val(selected.isiSilinder);
                    $("[name='warna']").val(selected.warna);
                    $("[name='alamat']").val(selected.alamat);
                    $("[name='nomorRangka']").val(selected.nomorRangka);
                    $("[name='nomorMesin']").val(selected.nomorMesin);
                    $("[name='keterangan']").val(selected.keterangan);
                    $("[name='nomorPolisiLama']").val(selected.nomorPolisiLama);
                    $("[name='nomorBpkbLama']").val(selected.nomorBpkbLama);
                }
            }).on("select2:unselecting", function(e) {
                $("form").each(function() {
                    this.reset()
                });
                ("#allocationsDiv").hide();
                $("[name='creditor_id']").val("");
            }).val("{{ old('nomorRegister') }}").trigger('change');

            function reset() {
                $("[name='nomorBpkb']").val(null);
                $("[name='nomorPolisi']").val(null);
                $("[name='namaPemilik']").val(null);
                $("[name='jenis']").val(null);
                $("[name='merk']").val(null);
                $("[name='tipe']").val(null);
                $("[name='model']").val(null);
                $("[name='tahunPembuatan']").val(null);
                $("[name='tahunPerakitan']").val(null);
                $("[name='isiSilinder']").val(null);
                $("[name='warna']").val(null);
                $("[name='alamat']").val(null);
                $("[name='nomorRangka']").val(null);
                $("[name='nomorMesin']").val(null);
                $("[name='keterangan']").val(null);
                $("[name='nomorPolisiLama']").val(null);
                $("[name='nomorBpkbLama']").val(null);
            }
        });
    </script>
@endpush
