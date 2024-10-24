@extends('template.app')
@section('title', 'Master Sertifikat')
@section('content')
    <div class="page-heading">
        <h3>Master Sertifikat</h3>
    </div>
    <div class="page-content">
        <section class="section">
            @if (session('message'))
                <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0">Master Sertifikat</h5>
                        <div class="dropdown ms-auto">
                            <a href="{{ route('kelola_data.sertifikat.create') }}" class="btn btn-success">Tambah</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="sertifikat">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor Register</th>
                                <th>Nomor Sertifikat</th>
                                <th>Tanggal</th>
                                <th>Luas (m2)</th>
                                <th>Kode Skpd</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </section>
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

            $('#sertifikat').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kelola_data.sertifikat.load') }}",
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
            searchable: false
        },
        {
            data: 'nomorRegister',
            name: 'nomorRegister'
        },
        {
            data: 'nomorSertifikat',
            name: 'nomorSertifikat'
        },
        {
            data: 'tanggalSertifikat',
            name: 'tanggalSertifikat'
        },
        {
            data: 'luas',
            name: 'luas'
        },
        {
            data: 'kodeSkpd',
            name: 'kodeSkpd'
        },
        {
            data: 'aksi',
            className: 'text-center'
        }
    ]
            });
        });

        function hapus(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/kelola_data/sertifikat/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Deleted!',
                            response.message,
                            'success'
                        );
                        $('#sertifikat').DataTable().ajax.reload();
                    } else {
                        Swal.fire(
                            'Failed!',
                            response.message,
                            'error'
                        );
                    }
                },
                error: function(xhr) {
                    Swal.fire(
                        'Error!',
                        'An error occurred while deleting the item.',
                        'error'
                    );
                }
            });
        }
    });
}
    </script>

@endpush
