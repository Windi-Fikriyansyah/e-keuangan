@extends('template.app')
@section('title', 'Tambah Program')
@section('content')
    <div class="page-heading">
        <h2>{{ isset($pajak) ? 'Edit Program' : 'Tambah Program' }}</h2>
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
                <form id="productForm" action="{{ isset($pajak) ? route('kelola_data.program.update', $pajak->id) : route('kelola_data.program.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($pajak))
                        @method('POST')
                    @endif

                    <div class="form-group">
                        <label for="barcode">Kode Program</label>
                        <input type="text" name="kd_program" id="kd_program" class="form-control" value="{{ old('kd_program', $pajak->kd_program ?? '') }}" placeholder="Input Kode Program">
                    </div>

                    <div class="form-group">
                        <label for="name">Nama Program</label>
                        <input type="text" name="nm_program" id="nm_program" class="form-control" value="{{ old('nm_program', $pajak->nm_program ?? '') }}" placeholder="Nama Program">
                    </div>
                    <button type="submit" class="btn btn-primary">{{ isset($pajak) ? 'Update' : 'Tambah' }}</button>
                    <a href="{{ route('kelola_data.program.index') }}" class="btn btn-warning">Kembali</a>
                </form>
            </div>
        </div>
    </div>
@endsection
