@extends('template.app')
@section('title', 'Setor Sisa Kas')
@section('content')
    <div class="page-heading">
        <h3>List STS</h3>
    </div>
    <div class="page-content">
        @if (session('success'))
            <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card radius-10">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="card-title">List STS</h5>
                    </div>
                    <div class="dropdown ms-auto">
                        <a href="{{ route('setorkas.create') }}" class="btn btn-success">Tambah</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="pbk" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor STS</th>
                                <th>Tanggal STS</th>
                                <th>SKPD</th>
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
@endsection

@push('js')
<style>
    .right-gap {
        margin-right: 10px;
    }
    /* Mencegah tombol bertumpuk ke bawah */
    .aksi-container {
        display: flex;
        gap: 5px;
        justify-content: center;
    }
    .aksi-container button {
        white-space: nowrap;
    }
</style>

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#pbk').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('setorkas.load') }}",
                type: "POST",
                data: function(data) {
                    data.search = data.search.value;
                }
            },
            pageLength: 10,
            searching: true,
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
                {
                    data: 'no_sts',
                    name: 'no_sts'
                },
                {
                    data: 'tgl_sts',
                    name: 'tgl_sts'
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd'
                },
                {
                    data: 'keterangan',
                    name: 'keterangan'
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `<div class="aksi-container">${data}</div>`;
                    }
                }
            ]
        });

    });

    $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var deleteUrl = $(this).data('url');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Terima Potongan Pajak ini akan dihapus!",
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
                                        'Terima Potongan Pajak berhasil dihapus.',
                                        'success'
                                    );
                                    $('#pbk').DataTable().ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Gagal menghapus Terima Potongan Pajak.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'Gagal menghapus Terima Potongan Pajak.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
    </script>
@endpush
