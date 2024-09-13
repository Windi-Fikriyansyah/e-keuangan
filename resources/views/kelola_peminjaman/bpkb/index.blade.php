@extends('template.app')
@section('title', 'Kelola Peminjaman BPKB')
@section('content')
    <div class="page-heading">
        <h3>Kelola Peminjaman</h3>
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
                        <h5 class="card-title">BPKB</h5>
                    </div>
                    <div class="dropdown ms-auto">
                        <a href="{{ route('peminjaman.bpkb.create') }}" class="btn btn-success">Tambah</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="bpkb" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Nomor Surat</th>
                                <th>Nomor Register</th>
                                <th>Nomor BPKB</th>
                                <th>Nomor Polisi</th>
                                <th>SKPD</th>
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

    <div class="modal fade text-left" id="modalCetak" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak Form Peminjaman BPKB</h5>
                    <button type="button" class="close rounded-pill" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Nomor Surat</label>
                        <div class="col-sm-8">
                            <input class="form-control readonlyInput" id="nomorSurat" type="text" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Nomor Register</label>
                        <div class="col-sm-8">
                            <input class="form-control readonlyInput" id="nomorRegister" type="text" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Tanda Tangan</label>
                        <div class="col-sm-8">
                            <select class="form-select select_option" id="tandaTangan" data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                @foreach ($daftarTandaTangan as $item)
                                    <option value="{{ $item->nip }}">{{ $item->nip }} | {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="flex: 1;align-items:center;justify-content:center">
                    <button type="button" class="btn btn-dark ms-1 cetak" data-tipe="layar">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Layar</span>
                    </button>
                    <button type="button" class="btn btn-danger ms-1 cetak" data-tipe="pdf">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">PDF</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPengajuan" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Pengajuan Peminjaman BPKB</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Nomor Surat</label>
                        <div class="col-sm-8">
                            <input class="form-control readonlyInput" id="nomorSuratPengajuan" type="text" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Nomor Register</label>
                        <div class="col-sm-8">
                            <input class="form-control readonlyInput" id="nomorRegisterPengajuan" type="text" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">File Pengajuan</label>
                        <div class="col-sm-8">
                            <input class="form-control" id="filePengajuan" type="file">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="display: flex;justify-content:center;align-items:center">

                    <button type="button" class="btn btn-success ms-1" data-bs-dismiss="modal">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Ajukan</span>
                    </button><button type="button" class="btn btn-danger ms-1" data-bs-dismiss="modal">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Batal Ajukan</span>
                    </button>
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

            $('#bpkb').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('peminjaman.bpkb.load') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                    }
                },
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'nomorSurat',
                    }, {
                        data: 'nomorRegister',
                    }, {
                        data: 'nomorBpkb',
                    }, {
                        data: 'nomorPolisi',
                    }, {
                        data: 'namaSkpd',
                    },
                    {
                        data: 'aksi',
                        width: "200",
                        className: 'text-center'
                    }
                ],
                columnDefs: [
                    // Center align both header and body content of columns 1, 2 & 3
                    {
                        className: "dt-head-center",
                        targets: ['_all']
                    },
                    {
                        className: "dt-body-center",
                        targets: [0, 1, 2, 4]
                    }
                ]
            });

            $('.cetak').on('click', function() {
                let nomorSurat = $('#nomorSurat').val();
                let nomorRegister = $('#nomorRegister').val();
                let tandaTangan = $('#tandaTangan').val();
                let tipe = $(this).data('tipe');

                if (!nomorSurat) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Nomor surat tidak boleh kosong",
                        icon: "warning"
                    });
                }

                if (!nomorRegister) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Nomor register tidak boleh kosong",
                        icon: "warning"
                    });
                }

                if (!tandaTangan) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan pilih tanda tangan",
                        icon: "warning"
                    });
                }

                let url = new URL("{{ route('peminjaman.bpkb.cetak') }}");
                let searchParams = url.searchParams;
                searchParams.append("nomorSurat", nomorSurat);
                searchParams.append("nomorRegister", nomorRegister);
                searchParams.append("tandaTangan", tandaTangan);
                searchParams.append("tipe", tipe);
                window.open(url.toString(), "_blank");
            });
        });

        function hapus(nomorSurat, nomorRegister, kodeSkpd) {
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
                        url: "{{ route('peminjaman.bpkb.delete') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            nomorSurat: nomorSurat,
                            nomorRegister: nomorRegister,
                            kodeSkpd: kodeSkpd
                        },
                        success: function(response) {
                            swalWithBootstrapButtons.fire({
                                title: "Terhapus!",
                                text: response.message,
                                icon: "success"
                            });

                            let tabel = $('#bpkb').DataTable();

                            tabel.ajax.reload();
                        },
                        error: function(e) {
                            swalWithBootstrapButtons.fire({
                                title: "Gagal!",
                                text: e.responseJSON.message,
                                icon: "error"
                            });
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

        function cetak(nomorSurat, nomorRegister, kodeSkpd) {
            $('#nomorSurat').val(nomorSurat);
            $('#nomorRegister').val(nomorRegister);
            $('#modalCetak').modal('show');
        }

        function pengajuan(nomorSurat, nomorRegister, kodeSkpd) {
            $('#nomorSuratPengajuan').val(nomorSurat);
            $('#nomorRegisterPengajuan').val(nomorRegister);
            $('#modalPengajuan').modal('show');
        }
    </script>
@endpush
