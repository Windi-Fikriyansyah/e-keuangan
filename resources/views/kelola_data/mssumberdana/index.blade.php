@extends('template.app')
@section('title', 'Data Pajak')
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
                    <h5 class="card-title mb-0">Data Ms Sumber Dana</h5>
                    <div class="d-flex gap-2">
                        <!-- Tombol untuk membuka modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            Upload Excel
                        </button>
                        <a href="{{ route('kelola_data.mssumberdana.download_format') }}" class="btn btn-info">Format Excel</a>
                        <a href="{{ route('kelola_data.mssumberdana.create') }}" class="btn btn-success">Tambah</a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="pajak" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Sumber Dana</th>
                                <th>Nama Sumber Dana</th>
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


    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload File Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="file" name="file" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
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
                    url: "{{ route('kelola_data.mssumberdana.load') }}",
                    type: "POST"
                },
                pageLength: 10,
                searching: true,
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'kd_dana', name: 'kd_dana' },
                    { data: 'nm_dana', name: 'nm_dana' },
                    { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' }
                ],
                columnDefs: [
                    { className: "dt-head-center", targets: '_all' },
                    { className: "dt-body-center", targets: [0, 1, 2, 3] }
                ]
            });


            $('#uploadForm').submit(function(e) {
    e.preventDefault();
    let formData = new FormData(this);

    $.ajax({
        url: "{{ route('kelola_data.mssumberdana.upload') }}",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            Swal.fire({
                title: 'Mengupload...',
                text: 'Harap tunggu sebentar.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: response.message,
                }).then((result) => {
                    $('#uploadModal').modal('hide');
                    $('#uploadForm')[0].reset();
                    $('#pajak').DataTable().ajax.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: response.message || 'Terjadi kesalahan.',
                });
            }
        },
        error: function(xhr) {
            let errorMessage = 'Terjadi kesalahan.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: errorMessage,
            });
        }
    });
});
            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var deleteUrl = $(this).data('url');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "ms anggaran ini akan dihapus!",
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
                                        'ms anggaran berhasil dihapus.',
                                        'success'
                                    );
                                    $('#pajak').DataTable().ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Gagal menghapus ms anggaran.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'Gagal menghapus ms anggaran.',
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
