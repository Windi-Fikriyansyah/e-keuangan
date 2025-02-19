@extends('template.app')
@section('title', 'Transaksi')
@section('content')
    <div class="page-heading">
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
                        <h5 class="card-title">List Transaksi Pemindahbukuan Bank</h5>
                    </div>
                    <div class="dropdown ms-auto">
                        <a href="{{ route('transaksi.create') }}" class="btn btn-success">Tambah</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="pbk" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor Bukti</th>
                                <th>Tanggal Bukti</th>
                                <th>Skpd</th>
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

    /* Pastikan tabel responsif */
    .dataTables_wrapper {
        width: 100%;
        overflow-x: auto;
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
                url: "{{ route('transaksi.load') }}",
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
                    data: 'no_bukti',
                    name: 'no_bukti'
                },
                {
                    data: 'tgl_bukti',
                    name: 'tgl_bukti'
                },
                {
                    data: 'nm_skpd',
                    name: 'nm_skpd'
                },
                {
                    data: 'ket',
                    name: 'ket'
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
                    text: "Transaksi ini akan dihapus!",
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
                                        'Transaksi berhasil dihapus.',
                                        'success'
                                    );
                                    $('#pbk').DataTable().ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Gagal menghapus Transaksi.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'Gagal menghapus Transaksi.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

    </script>
@endpush
