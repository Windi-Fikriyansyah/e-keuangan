@extends('template.app')
@section('title', 'Edit Peminjaman BPKB')
@section('content')
    <div class="page-heading">
        <h3>Edit Peminjaman BPKB</h3>
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
                <form method="POST"action="{{ route('peminjaman.bpkb.update', $dataPinjam->id) }}">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Jenis Kendaraan</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('jenis') is-invalid @enderror readonlyInput"
                                type="text" placeholder="Tidak perlu diisi, otomatis"
                                value="{{ $dataBpkb->jenis }}" readonly>
                            @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Merek</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('merk') is-invalid @enderror readonlyInput"
                                type="text" placeholder="Tidak perlu diisi, otomatis"
                                value="{{ $dataBpkb->merk }}" readonly>
                            @error('merk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Arsip Dokumen</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('nomorRegister') is-invalid @enderror readonlyInput"
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="nomorRegister"
                                value="{{ $dataPinjam->nomorRegister }}" readonly>
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
                                value="{{ $dataPinjam->nomorSurat }}" readonly>
                            @error('nomorSurat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Tanggal Pinjam</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tanggalPinjam') is-invalid @enderror" type="date"
                                placeholder="Tidak perlu diisi, otomatis" name="tanggalPinjam"
                                value="{{ $dataPinjam->tanggalPinjam }}">
                            @error('tanggalPinjam')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nama PBP</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('namaPbp') is-invalid @enderror" type="text"
                                placeholder="Isi nama pengurus barang pengguna" name="namaPbp"
                                value="{{ $dataPinjam->namaPbp }}">
                            @error('namaPbp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">NIP PBP</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nipPbp') is-invalid @enderror" type="text"
                                placeholder="Isi nip pengurus barang pengguna" name="nipPbp"
                                value="{{ $dataPinjam->nipPbp }}">
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
                                value="{{ $dataBpkb->nomorBpkb }}">
                            @error('nomorBpkb')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Nomor Telp PBP</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorTelpPbp') is-invalid @enderror" type="text"
                                placeholder="Isi nomor telepon pengurus barang pengguna" name="nomorTelpPbp"
                                value="{{ $dataPinjam->nomorTelpPbp }}">
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
                                value="{{ $dataBpkb->nomorPolisi }}">
                            @error('nomorPolisi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Nama Pemilik</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('namaPemilik') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="namaPemilik"
                                value="{{ $dataBpkb->namaPemilik }}">
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
                                value="{{ $dataBpkb->jenis }}">
                            @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Merk</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('merk') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="merk"
                                value="{{ $dataBpkb->merk }}">
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
                                value="{{ $dataBpkb->tipe }}">
                            @error('tipe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Model</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('model') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="model"
                                value="{{ $dataBpkb->model }}">
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
                                value="{{ $dataBpkb->tahunPembuatan }}">
                            @error('tahunPembuatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Tahun Perakitan</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tahunPerakitan') is-invalid @enderror readonlyInput"
                                readonly type="text" placeholder="Tidak perlu diisi, otomatis" name="tahunPerakitan"
                                value="{{ $dataBpkb->tahunPerakitan }}">
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
                                value="{{ $dataBpkb->isiSilinder }}">
                            @error('isiSilinder')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Warna</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('warna') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="warna"
                                value="{{ $dataBpkb->warna }}">
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
                                value="{{ $dataBpkb->nomorRangka }}">
                            @error('nomorRangka')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Nomor Mesin</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorMesin') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="nomorMesin"
                                value="{{ $dataBpkb->nomorMesin }}">
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
                                value="{{ $dataBpkb->nomorPolisiLama }}">
                            @error('nomorPolisiLama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Nomor Bpkb Lama</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorBpkbLama') is-invalid @enderror readonlyInput"
                                readonly type="text" placeholder="Tidak perlu diisi, otomatis" name="nomorBpkbLama"
                                value="{{ $dataBpkb->nomorBpkbLama }}">
                            @error('nomorBpkbLama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Alamat</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('alamat') is-invalid @enderror readonlyInput" readonly type="text"
                                placeholder="Tidak perlu diisi, otomatis" name="alamat">{{ $dataBpkb->alamat }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('keterangan') is-invalid @enderror readonlyInput" readonly type="text"
                                placeholder="Tidak perlu diisi, otomatis" name="keterangan">{{ $dataBpkb->keterangan }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keperluan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('keperluan') is-invalid @enderror" type="text" name="keperluan"
                                placeholder="Isi keperluan peminjaman BPKB">{{ $dataPinjam->keperluan }}</textarea>
                            @error('keperluan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        @if ($dataPinjam->statusPengajuan == '0')
                            <button class="btn btn-primary" type="submit">Simpan</button>
                        @endif
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
        });
    </script>
@endpush
