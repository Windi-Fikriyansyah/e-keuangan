@extends('template.app')
@section('title', 'Data Penerima')
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
                    <h5 class="card-title mb-0">Data Ms Penerima</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('kelola_data.penerima.create') }}" class="btn btn-success">Tambah</a>

                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="pajak" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Bank</th>
                                <th>NPWP</th>
                                <th>Keterangan</th>
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
                    url: "{{ route('kelola_data.penerima.load') }}",
                    type: "POST"
                },
                pageLength: 10,
                searching: true,
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'rekening', name: 'rekening' },
                    { data: 'nm_rekening', name: 'nm_rekening' },
                    { data: 'nm_bank', name: 'nm_bank' },
                    { data: 'npwp', name: 'npwp' },
                    { data: 'keterangan', name: 'keterangan' },
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
                    text: "ms Penerima ini akan dihapus!",
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
                                        'ms Penerima berhasil dihapus.',
                                        'success'
                                    );
                                    $('#pajak').DataTable().ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Gagal menghapus ms Penerima.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'Gagal menghapus ms Penerima.',
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
