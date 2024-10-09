@extends('template.app')
@section('title', 'Edit Peminjaman Sertifikat')
@section('content')
    <div class="page-heading">
        <h3>Edit Peminjaman Sertifikat</h3>
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
                <form method="POST"action="{{ route('peminjaman.sertifikat.update', $dataPinjam->id) }}" id="formSertifikat" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Register</label>
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
                                value="{{ $dataPinjam->tanggalPinjam }}" >
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
                                value="{{ $dataPinjam->namaKsbtgn }}">
                            @error('namaKsbtgn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="col-sm-2 col-form-label">Nip Ksbtgn</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nipKsbtgn') is-invalid @enderror"
                                type="text" placeholder="Isi NIP Ksbtgn" name="nipKsbtgn"
                                value="{{ $dataPinjam->nipKsbtgn }}">
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
                                value="{{ $dataPinjam->peruntukan }}">
                            @error('peruntukan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <label class="col-sm-2 col-form-label">No Telepon Ksbtgn</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('noTelpKsbtgn') is-invalid @enderror "
                                type="text" placeholder="Isi No Telepon Ksbtgn" name="noTelpKsbtgn"
                                value="{{ $dataPinjam->noTelpKsbtgn }}">
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
                                value="{{ $dataSertifikat->pemegangHak }}">
                            @error('nomorSertifikat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="col-sm-2 col-form-label">Pemegang Hak</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('pemegangHak') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="pemegangHak"
                                value="{{ $dataSertifikat->pemegangHak }}">
                            @error('pemegangHak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="row mb-3">

                        <label class="col-sm-2 col-form-label">NIB</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nib') is-invalid @enderror readonlyInput" readonly type="text"
                                placeholder="Tidak perlu diisi, otomatis" name="nib" value="{{ $dataSertifikat->nib }}">
                            @error('nib')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="col-sm-2 col-form-label">Tanggal Terbit</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tanggal') is-invalid @enderror readonlyInput" readonly type="date"
                                placeholder="Tidak perlu diisi, otomatis" name="tanggal"
                                value="{{ $dataSertifikat->tanggalSertifikat }}">
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
                                value="{{ $dataSertifikat->sertifikatAsli == 1 ? 'Ya' : 'Tidak' }}">
                            @error('sertifikatAsli')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Luas</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('luas') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="luas"
                                value="{{ $dataPinjam->luas }}">
                            @error('luas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                    </div>

                    <div  class="row mb-3">
                        <label class="col-sm-2 col-form-label">Balik Nama</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('balikNama') is-invalid @enderror readonlyInput" readonly
                                type="text" placeholder="Tidak perlu diisi, otomatis" name="balikNama"
                                value="{{ $dataSertifikat->balikNama == 1 ? 'Sudah' : 'Belum' }}">
                            @error('balikNama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Alamat</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('alamat') is-invalid @enderror readonlyInput" readonly type="text"
                                placeholder="Tidak perlu diisi, otomatis" name="alamat">{{ $dataSertifikat->alamat }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('keterangan') is-invalid @enderror readonlyInput" readonly type="text"
                                placeholder="Tidak perlu diisi, otomatis" name="keterangan">{{ $dataSertifikat->keterangan }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        @if ($dataPinjam->statusPengajuan == '0')
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        @endif
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
        });
    </script>
@endpush
