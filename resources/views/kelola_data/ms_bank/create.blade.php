@extends('template.app')
@section('title', 'Tambah Bank')
@section('content')
<div class="page-heading">
    <h2>{{ isset($pajak) ? 'Edit Bank' : 'Tambah Bank' }}</h2>
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

        <h5>Potongan</h5>
        <div class="card">
            <div class="card-body">
                <form id="formBpkb" action="{{ isset($pajak) ? route('kelola_data.ms_bank.update', $pajak->kode) : route('kelola_data.ms_bank.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($pajak))
                        @method('POST')
                    @endif




                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Kode</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('kode') is-invalid @enderror" name="kode"
                            placeholder="Isi dengan Kode Bank" value="{{ isset($pajak) ? $pajak->kode : old('kode') }}">
                            @error('kode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nama Bank</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('nama') is-invalid @enderror" name="nama"
                            placeholder="Isi dengan Nama Pemilik" value="{{ isset($pajak) ? $pajak->nama : old('nama') }}">
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">BIC</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('bic') is-invalid @enderror" name="bic"
                            placeholder="Isi dengan bic" value="{{ isset($pajak) ? $pajak->bic : old('bic') }}">
                            @error('bic')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <div class="mb-3 text-end">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a href="{{ route('kelola_data.ms_bank.index') }}" class="btn btn-warning">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endpush
