@extends('template.app')
@section('title', 'Kelola Peminjaman Sertifikat')
@section('content')
    <div class="page-heading">
        <h3>Kelola Peminjaman Sertifikat</h3>
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
                        <h5 class="card-title">Sertifikat</h5>
                    </div>
                    <div class="dropdown ms-auto">
                        <a href="{{ route('peminjaman.sertifikat.create') }}" class="btn btn-success">Tambah</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="sertifikat" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Nomor Surat</th>
                                <th>Nomor Register</th>
                                <th>Nomor Sertifikat</th>
                                <th>NIB</th>
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

    <div class="modal fade text-left" id="modalCetak" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel1"aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak Form Peminjaman Sertifikat</h5>
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
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Pengajuan Peminjaman Sertifikat</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" enctype="multipart/form-data" id="formPengajuan" method="POST">
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">Nomor Surat</label>
                            <div class="col-sm-8">
                                <input class="form-control readonlyInput" id="nomorSuratPengajuan"
                                    name="nomorSuratPengajuan" type="text" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">Nomor Register</label>
                            <div class="col-sm-8">
                                <input class="form-control readonlyInput" id="nomorRegisterPengajuan"
                                    name="nomorRegisterPengajuan" type="text" readonly>
                                <input class="form-control readonlyInput" id="statusPengajuan" name="statusPengajuan"
                                    type="text" hidden>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-success ms-1" data-tipe="ajukan"
                                    id="AjukanPengajuan">
                                    <i class="bx bx-check d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Ajukan</span>
                                </button>
                                <button type="submit" class="btn btn-danger ms-1" data-tipe="batalkan"
                                    id="BatalkanPengajuan">
                                    <i class="bx bx-check d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Batal Ajukan</span>
                                </button>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">File Pengajuan</label>
                            <div class="col-sm-8">
                                <input class="form-control" id="filePengajuan" name="filePengajuan" type="file"
                                    onchange="fileValidation()">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-12">
                                <iframe style="width: 100%;height:100vh" id="tampilanFilePengajuan"></iframe>
                            </div>
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
                    url: "{{ route('peminjaman.sertifikat.load') }}",
                    type: "POST",
                    // data: function(data) {
                    //     data.search = $('input[type="search"]').val();
                    // }
                },
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'nomorSurat',
                    }, {
                        data: 'nomorRegister',
                    }, {
                        data: 'nomorSertifikat',
                    }, {
                        data: 'NIB',
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
        });

        $('#formPengajuan').on('submit', function(e) {
            e.preventDefault();

            let form = $('#formPengajuan')[0];
            let formData = new FormData(form);

            let nomorSuratPengajuan = $("#nomorSuratPengajuan").val();
            let nomorRegisterPengajuan = $("#nomorRegisterPengajuan").val();
            let statusPengajuan = $("#statusPengajuan").val();
            let filePengajuan = $("#filePengajuan").val();

            if (!nomorSuratPengajuan) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Nomor surat tidak boleh kosong",
                    icon: "warning"
                });
                return;
            }

            if (!nomorRegisterPengajuan) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Nomor register tidak boleh kosong",
                    icon: "warning"
                });
                return;
            }

            if (!filePengajuan && statusPengajuan == '0') {
                Swal.fire({
                    title: "Peringatan!",
                    text: "File Pengajuan tidak boleh kosong",
                    icon: "warning"
                });
                return;
            }

            formData.append('nomorSuratPengajuan', $('input[name=nomorSuratPengajuan]').val())
            formData.append('nomorRegisterPengajuan', $('input[name=nomorRegisterPengajuan]').val())

            if (statusPengajuan == '0') {
                formData.append("filePengajuan", $('input[name=filePengajuan]')[0].files[0]);
            }

            $.ajax({
                url: "{{ route('peminjaman.sertifikat.pengajuan') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        title: "Berhasil!",
                        text: response.message,
                        icon: "success"
                    });

                    $('#sertifikat').DataTable().ajax.reload();

                    document.getElementById('filePengajuan').value = '';
                    document.getElementById('tampilanFilePengajuan').src = '';

                    $('#modalPengajuan').modal('hide');
                },
                error: function(e) {
                    Swal.fire({
                        title: "Gagal!",
                        text: e.responseJSON.message,
                        icon: "error"
                    });
                },
            });
        })

        function fileValidation() {
            const fileInput = document.getElementById('filePengajuan');
            const fileSize = (fileInput.files[0].size / 1024 / 1024).toFixed(2);
            if (fileSize > 5) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "File pengajuan tidak boleh lebih dari 5MB",
                    icon: "warning"
                });
                fileInput.value = '';
                return false;
            }
        }

        function pengajuan(nomorSurat, nomorRegister, kodeSkpd, file, statusPengajuan) {
            $('#nomorSuratPengajuan').val(nomorSurat);
            $('#nomorRegisterPengajuan').val(nomorRegister);
            $('#statusPengajuan').val(statusPengajuan);

            $('#AjukanPengajuan').prop('hidden', true);
            $('#BatalkanPengajuan').prop('hidden', true)

            const formatFile = `storage/images/Peminjaman/Sertifikat/${kodeSkpd}/${file}`;

            statusPengajuan == '0' ? $('#AjukanPengajuan').prop('hidden', false) : $('#BatalkanPengajuan').prop('hidden',
                false);

            console.log(file)
            console.log(formatFile)

            file ? document.getElementById('tampilanFilePengajuan').src = `{{ asset('${formatFile}') }}` : '';
            $('#modalPengajuan').modal('show');
        }

        function cetak(nomorSurat, nomorRegister, kodeSkpd) {
            $('#nomorSurat').val(nomorSurat);
            $('#nomorRegister').val(nomorRegister);
            $('#modalCetak').modal('show');
        }

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
                return;
            }

            if (!nomorRegister) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Nomor register tidak boleh kosong",
                    icon: "warning"
                });
                return;
            }

            if (!tandaTangan) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silahkan pilih tanda tangan",
                    icon: "warning"
                });
                return;
            }

            let url = new URL("{{ route('peminjaman.sertifikat.cetak') }}");
            let searchParams = url.searchParams;
            searchParams.append("nomorSurat", nomorSurat);
            searchParams.append("nomorRegister", nomorRegister);
            searchParams.append("tandaTangan", tandaTangan);
            searchParams.append("tipe", tipe);
            window.open(url.toString(), "_blank");
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
                        url: "{{ route('peminjaman.sertifikat.delete') }}",
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

                            let tabel = $('#sertifikat').DataTable();

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
    </script>
@endpush
