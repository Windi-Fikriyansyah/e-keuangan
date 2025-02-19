@extends('template.app')
@section('title', 'Ubah Pengguna')
@section('content')
    <div class="page-heading">
        <h3>Ubah Pengguna</h3>
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
        <div class="card">
            <div class="card-body">
                <form action="{{ route('user.update', $data->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nama</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('name') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nama" name="name" id="name" value="{{ $data->name }}"
                                autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Kode Skpd</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('kd_skpd') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nama" name="kd_skpd" id="kd_skpd" value="{{ $data->kd_skpd }}"
                                autofocus>
                            @error('kd_skpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Username</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('username') is-invalid @enderror" type="text"
                                placeholder="Isi dengan username" name="username" id="username"
                                value="{{ $data->username }}">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('status_aktif') is-invalid @enderror select_option"
                                name="status_aktif" id="status_aktif" data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                <option value="0" {{ $data->status_aktif == 0 ? 'selected' : '' }}>
                                    Tidak Aktif</option>
                                <option value="1" {{ $data->status_aktif == 1 ? 'selected' : '' }}>
                                    Aktif</option>
                            </select>
                            @error('status_aktif')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tipe</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('tipe') is-invalid @enderror select_option" name="tipe"
                                id="tipe" data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                <option value="owner" {{ $data->tipe == 'owner' ? 'selected' : '' }}>
                                    Owner</option>
                                <option value="kasir" {{ $data->tipe == 'kasir' ? 'selected' : '' }}>
                                    Kasir</option>
                            </select>
                            @error('tipe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Peran</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('role') is-invalid @enderror select_option" name="role"
                                id="role" data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                @foreach ($daftar_peran as $peran)
                                    <option value="{{ $peran->id }}" {{ $data->role == $peran->id ? 'selected' : '' }}>
                                        {{ $peran->name }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Jabatan</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('jabatan') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nama" name="jabatan" id="jabatan" value="{{ $data->jabatan }}">
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Rekening Pengeluaran</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('rek_pengeluaran') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nama" name="rek_pengeluaran" id="rek_pengeluaran" value="{{ $data->rek_pengeluaran }}">
                            @error('rek_pengeluaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a href="{{ route('user.index') }}" class="btn btn-warning">Kembali</a>
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

            $("#show_hide_password_lama a").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_password_lama input').attr("type") == "text") {
                    $('#show_hide_password_lama input').attr('type', 'password');
                    $('#show_hide_password_lama i').addClass("bx-hide");
                    $('#show_hide_password_lama i').removeClass("bx-show");
                } else if ($('#show_hide_password_lama input').attr("type") == "password") {
                    $('#show_hide_password_lama input').attr('type', 'text');
                    $('#show_hide_password_lama i').removeClass("bx-hide");
                    $('#show_hide_password_lama i').addClass("bx-show");
                }
            });


            $("#show_hide_password a").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_password input').attr("type") == "text") {
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass("bx-hide");
                    $('#show_hide_password i').removeClass("bx-show");
                } else if ($('#show_hide_password input').attr("type") == "password") {
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass("bx-hide");
                    $('#show_hide_password i').addClass("bx-show");
                }
            });

            $("#show_hide_confirmation_password a").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_confirmation_password input').attr("type") == "text") {
                    $('#show_hide_confirmation_password input').attr('type', 'password');
                    $('#show_hide_confirmation_password i').addClass("bx-hide");
                    $('#show_hide_confirmation_password i').removeClass("bx-show");
                } else if ($('#show_hide_confirmation_password input').attr("type") == "password") {
                    $('#show_hide_confirmation_password input').attr('type', 'text');
                    $('#show_hide_confirmation_password i').removeClass("bx-hide");
                    $('#show_hide_confirmation_password i').addClass("bx-show");
                }
            });
        });
    </script>
@endpush
