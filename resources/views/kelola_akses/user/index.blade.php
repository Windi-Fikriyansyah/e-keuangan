@extends('template.app')
@section('title', 'Pengguna')
@section('content')
    <div class="page-heading">
        <h3>Kelola Akses</h3>
    </div>
    <div class="page-content">
        @if (session('message'))
            <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card radius-10">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="card-title">PENGGUNA</h5>
                    </div>
                    <div class="dropdown ms-auto">
                        <a href="{{ route('user.create') }}" class="btn btn-success">Tambah</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="user" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <style>
        .right-gap {
            margin-right: 10px
        }
    </style>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#user').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('user.load') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                    }
                },
                pageLength: 10,
                searching: true,
                aoColumns: [{
                        data: 'name',
                    },
                    {
                        data: 'aksi',
                        className: 'text-center'
                    }
                ]
            });
        });

        function hapus(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success right-gap",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, hapus",
                cancelButtonText: "Tidak, kembali!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/user/' + id,
                        type: "DELETE",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status == true) {
                                swalWithBootstrapButtons.fire({
                                    title: "Terhapus!",
                                    text: "Data berhasil dihapus!",
                                    icon: "success"
                                });
                                let tabel = $('#user').DataTable();
                                tabel.ajax.reload();
                            } else {
                                swalWithBootstrapButtons.fire({
                                    title: "Gagal!",
                                    text: response.message,
                                    icon: "danger"
                                });
                            }
                        },
                        error: function(e) {
                            console.log(e);
                        },
                    });

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: "Batal",
                        text: "Data tidak dihapus!",
                        icon: "error"
                    });
                }
            });
        }
    </script>
@endpush
