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

        @if (session('error'))
        <div class="card bg-danger text-white border-0">
            <div class="card-body">
                {{ session('error') }}
            </div>
        </div>
    @endif

        <div class="card radius-10">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="card-title">List Transaksi Pemindahbukuan Bank</h5>
                    </div>
                    <div class="dropdown ms-auto">
                        <a href="" class="btn btn-primary" id="cetak">Cetak</a>
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
                                <th><input type="checkbox" id="select-all"></th>
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


    <div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="printModalLabel">Cetakan Rekening Tujuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="printForm" action="{{ route('transaksi.print') }}" method="POST" target="_blank">
                        @csrf
                        <div class="mb-3">
                            <label for="jenis_cetak" class="form-label">Jenis Cetakan</label>
                            <select class="form-control" name="jenis_cetak" id="jenis_cetak">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="OB">OB</option>
                                <option value="SKN">SKN</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-dark btn-md submitPrint" data-jenis="excel">Excel</button>
                    <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal">Tutup</button>
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

    // Array untuk menyimpan ID yang dicentang
    var selectedIds = [];

    var table = $('#pbk').DataTable({
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
                name: 'no_bukti',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    var isChecked = selectedIds.includes(row.no_bukti) ? 'checked' : '';
                    return `<input type="checkbox" class="select-checkbox" data-id="${row.no_bukti}" ${isChecked}>`;
                }
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
        ],
        drawCallback: function(settings) {
            // Pulihkan status ceklis setelah tabel dimuat ulang
            $('.select-checkbox').each(function() {
                var id = $(this).data('id');
                if (selectedIds.includes(id)) {
                    $(this).prop('checked', true);
                }
            });
        }
    });

    $('#select-all').on('click', function() {
        var rows = table.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);

        // Tambah atau hapus semua ID ke/dari selectedIds
        if (this.checked) {
            $('.select-checkbox', rows).each(function() {
                var id = $(this).data('id');
                if (!selectedIds.includes(id)) {
                    selectedIds.push(id);
                }
            });
        } else {
            $('.select-checkbox', rows).each(function() {
                var id = $(this).data('id');
                selectedIds = selectedIds.filter(function(item) {
                    return item !== id;
                });
            });
        }
    });

    // Handle individual checkbox click
    $('#pbk tbody').on('change', 'input[type="checkbox"]', function() {
        var id = $(this).data('id');
        if (this.checked) {
            if (!selectedIds.includes(id)) {
                selectedIds.push(id);
            }
        } else {
            selectedIds = selectedIds.filter(function(item) {
                return item !== id;
            });
        }

        if (!this.checked) {
            var el = $('#select-all').get(0);
            if (el && el.checked && ('indeterminate' in el)) {
                el.indeterminate = true;
            }
        }
    });

    $('#cetak').on('click', function(e) {
        e.preventDefault();

        if (selectedIds.length === 0) {
            Swal.fire({
                title: "Peringatan!",
                text: "Silakan pilih setidaknya satu transaksi untuk dicetak!",
                icon: "warning"
            });
            return;
        }

        // Clear previous input
        $('#printForm').find('input[name="no_bukti[]"]').remove();

        // Add selected no_bukti to form
        selectedIds.forEach(function(no_bukti) {
            $('#printForm').append('<input type="hidden" name="no_bukti[]" value="' + no_bukti + '">');
        });

        // Show modal
        $('#printModal').modal('show');
    });

    // Handle print form submission
    $('.submitPrint').on('click', function() {
        let jenis_cetak = $('#jenis_cetak').val();
        let jenis_print = $(this).data("jenis");

        if (!jenis_cetak) {
            Swal.fire({
                title: "Peringatan!",
                text: "Silakan pilih Jenis Cetakan!",
                icon: "warning"
            });
            return;
        }

        $('#printForm').append('<input type="hidden" name="jenis_print" value="' + jenis_print + '">');
        $('#printForm').submit();
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
