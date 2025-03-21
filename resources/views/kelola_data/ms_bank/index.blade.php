@extends('template.app')
@section('title', 'MS BANK')
@section('content')
    <div class="page-heading">
        <h3>Data Master</h3>
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
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Data Ms Bank</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('kelola_data.ms_bank.create') }}" class="btn btn-success">Tambah</a>

                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="pajak" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#pajak').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kelola_data.ms_bank.load') }}",
                    type: "POST"
                },
                pageLength: 10,
                searching: true,
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'kode', name: 'kode' },
                    { data: 'nama', name: 'nama' },
                    { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center nowrap' }
                ],
                columnDefs: [
                    { className: "dt-head-center", targets: '_all' },
                    { className: "dt-body-center", targets: [0, 1, 2, 3] }
                ]
            });




            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var deleteUrl = $(this).data('url');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "ms bank ini akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Terhapus!',
                                        'ms bank berhasil dihapus.',
                                        'success'
                                    );
                                    $('#pajak').DataTable().ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Gagal menghapus ms ms_bank.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'Gagal menghapus ms bank.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });



    </script>
@endpush
