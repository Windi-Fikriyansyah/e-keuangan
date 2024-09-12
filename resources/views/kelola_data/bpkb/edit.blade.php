@extends('template.app')
@section('title', 'Edit BPKB')
@section('content')
    <div class="page-heading">
        <h3>Edit BPKB</h3>
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
                <form method="POST"action="{{ route('kelola_data.bpkb.update', $dataBpkb->id) }}">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">SKPD</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('kodeSkpd') is-invalid @enderror select_option"
                                name="kodeSkpd" data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                @foreach ($daftarSkpd as $skpd)
                                    <option value="{{ $skpd->kodeSkpd }}"
                                        {{ $dataBpkb->kodeSkpd == $skpd->kodeSkpd ? 'selected' : '' }}>
                                        {{ $skpd->namaSkpd }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kodeSkpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Register</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('nomorRegister') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor register" name="nomorRegister"
                                value="{{ $dataBpkb->nomorRegister }}" autofocus>
                            @error('nomorRegister')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor BPKB</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('nomorBpkb') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor bpkb" name="nomorBpkb" value="{{ $dataBpkb->nomorBpkb }}">
                            @error('nomorBpkb')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Polisi</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('nomorPolisi') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor polisi" name="nomorPolisi"
                                value="{{ $dataBpkb->nomorPolisi }}">
                            @error('nomorPolisi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nama Pemilik</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('namaPemilik') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nama pemilik" name="namaPemilik"
                                value="{{ $dataBpkb->namaPemilik }}">
                            @error('namaPemilik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Jenis</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('jenis') is-invalid @enderror" type="text"
                                placeholder="Isi dengan jenis" name="jenis" value="{{ $dataBpkb->jenis }}">
                            @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Merk</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('merk') is-invalid @enderror" type="text"
                                placeholder="Isi dengan merk" name="merk" value="{{ $dataBpkb->merk }}">
                            @error('merk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tipe</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tipe') is-invalid @enderror" type="text"
                                placeholder="Isi dengan tipe" name="tipe" value="{{ $dataBpkb->tipe }}">
                            @error('tipe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Model</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('model') is-invalid @enderror" type="text"
                                placeholder="Isi dengan model" name="model" value="{{ $dataBpkb->model }}">
                            @error('model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tahun Pembuatan</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tahunPembuatan') is-invalid @enderror" type="text"
                                placeholder="Isi dengan tahun pembuatan" name="tahunPembuatan"
                                value="{{ $dataBpkb->tahunPembuatan }}">
                            @error('tahunPembuatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Tahun Perakitan</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tahunPerakitan') is-invalid @enderror" type="text"
                                placeholder="Isi dengan tahun perakitan" name="tahunPerakitan"
                                value="{{ $dataBpkb->tahunPerakitan }}">
                            @error('tahunPerakitan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Isi Silinder</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('isiSilinder') is-invalid @enderror" type="text"
                                placeholder="Isi dengan isi silinder" name="isiSilinder"
                                value="{{ $dataBpkb->isiSilinder }}">
                            @error('isiSilinder')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Warna</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('warna') is-invalid @enderror" type="text"
                                placeholder="Isi dengan warna" name="warna" value="{{ $dataBpkb->warna }}">
                            @error('warna')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Rangka</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorRangka') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor rangka" name="nomorRangka"
                                value="{{ $dataBpkb->nomorRangka }}">
                            @error('nomorRangka')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Nomor Mesin</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorMesin') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor mesin" name="nomorMesin"
                                value="{{ $dataBpkb->nomorMesin }}">
                            @error('nomorMesin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Polisi Lama</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorPolisiLama') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor polisi lama" name="nomorPolisiLama"
                                value="{{ $dataBpkb->nomorPolisiLama }}">
                            @error('nomorPolisiLama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Nomor Bpkb Lama</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorBpkbLama') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor bpkb lama" name="nomorBpkbLama"
                                value="{{ $dataBpkb->nomorBpkbLama }}">
                            @error('nomorBpkbLama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Alamat</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('alamat') is-invalid @enderror" type="text" placeholder="Isi dengan alamat"
                                name="alamat">{{ $dataBpkb->alamat }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" type="text"
                                placeholder="Isi dengan keterangan" name="keterangan">{{ $dataBpkb->keterangan }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a href="{{ route('kelola_data.bpkb.index') }}" class="btn btn-warning">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
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
