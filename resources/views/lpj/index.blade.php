@extends('template.app')
@section('title', 'LPJ')
@section('content')
    <div class="page-heading">
        <h3>LPJ UP/GU</h3>
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
                        <h5 class="card-title">LPJ UP/GU</h5>
                    </div>
                    <div class="dropdown ms-auto">
                        <a href="{{ route('lpj.create') }}" class="btn btn-success">Tambah</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="pbk" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor LPJ</th>
                                <th>Tanggal LPJ</th>
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


    <!-- Print Modal -->
<div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printModalLabel">Print LPJ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="printForm" action="{{ route('lpj.print') }}" method="POST" target="_blank">
                    @csrf
                    <div class="mb-3">
                        <label for="no_lpj_modal" class="form-label">Nomor LPJ</label>
                        <input type="text" class="form-control readonlyInput" id="no_lpj_modal" name="no_lpj" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="no_sp2d_modal" class="form-label">Nomor SP2D</label>
                        <input type="text" class="form-control readonlyInput" id="no_sp2d_modal" name="no_sp2d" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="tgl_ttd" class="form-label">Tanggal TTD</label>
                        <input type="date" class="form-control" id="tgl_ttd" name="tgl_ttd" required>
                    </div>

                    <div class="mb-3">
                        <label for="ttdbendaharabku" class="form-label">Bendahara Pengeluaran</label>
                        <select class="form-control" name ="ttdbendaharabku" id="ttdbendaharabku">
                            <option value="" disabled selected>Silahkan Pilih</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="ttdpaka" class="form-label">PA/KPA</label>
                        <select class="form-control" name ="ttdpaka" id="ttdpaka">
                            <option value="" disabled selected>Silahkan Pilih</option>
                        </select>
                    </div>


                    <div class="mb-3">
                        <label class="form-label d-block">Pilihan Cetak</label>
                        <div class="text-center">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="print_type" id="print_lpj_tu" value="lpj_tu" checked>
                                <label class="form-check-label" for="print_lpj_tu">Cetak Rincian</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="print_type" id="print_sptb" value="sptb">
                                <label class="form-check-label" for="print_sptb">Cetak SPTB</label>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" id="submitPrint">Layar</button>
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

        $('#ttdbendaharabku').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('lpj.tandaTangan') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term)
                };
            },
            processResults: function(data) {
                return {
                    results: data.map((ttd) => {
                        return {
                            text: ttd.nama,
                            id: ttd.nip,
                        };
                    }),
                    pagination: {
                        more: data.current_page < data.last_page,
                    },
                };
            },
            cache: true
        }
    });


    $('#ttdpaka').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('lpj.tandaTanganPa') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term)
                };
            },
            processResults: function(data) {
                return {
                    results: data.map((ttd) => {
                        return {
                            text: ttd.nama,
                            id: ttd.nip,
                        };
                    }),
                    pagination: {
                        more: data.current_page < data.last_page,
                    },
                };
            },
            cache: true
        }
    });

        $('#pbk').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('lpj.load') }}",
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
                    data: 'no_lpj',
                    name: 'no_lpj'
                },
                {
                    data: 'tgl_lpj',
                    name: 'tgl_lpj'
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
                    text: "LPJ UP/GU ini akan dihapus!",
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
                                        'LPJ UP/GU berhasil dihapus.',
                                        'success'
                                    );
                                    $('#pbk').DataTable().ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Gagal menghapus LPJ UP/GU.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'Gagal menghapus LPJ UP/GU.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            // Add this to your existing JavaScript section
$(document).on('click', '.print-btn', function() {
    var noLpj = $(this).data('no-lpj');

    // Fetch SP2D number and other details
    $.ajax({
        url: "{{ route('lpj.get-sp2d') }}",
        type: "POST",
        data: {
            no_lpj: noLpj
        },
        success: function(response) {
            if (response.success) {
                $('#no_lpj_modal').val(noLpj);
                $('#no_sp2d_modal').val(response.no_sp2d);

                // Set default date to current date
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
                var yyyy = today.getFullYear();
                today = yyyy + '-' + mm + '-' + dd;
                $('#tgl_ttd').val(today);

                // Show the modal
                $('#printModal').modal('show');
            } else {
                Swal.fire(
                    'Error!',
                    'Gagal mendapatkan data SP2D.',
                    'error'
                );
            }
        },
        error: function() {
            Swal.fire(
                'Error!',
                'Gagal mendapatkan data SP2D.',
                'error'
            );
        }
    });
});

// Handle print form submission
$(document).on('click', '#submitPrint', function() {
    if ($('#tgl_ttd').val() === '') {
        Swal.fire(
            'Error!',
            'Tanggal tanda tangan harus diisi.',
            'error'
        );
        return;
    }

    // Submit the form
    $('#printForm').submit();
});
    </script>
@endpush
