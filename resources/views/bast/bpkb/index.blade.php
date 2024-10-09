@extends('template.app')
@section('title', 'BAST BPKB')
@section('content')
    <div class="page-heading">
        <h3>BAST</h3>
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
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="bpkb" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Nomor BAST</th>
                                <th>Tanggal BAST</th>
                                <th>Nomor Surat</th>
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

    <div class="modal fade" id="modalPengajuan" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Pengajuan BAST BPKB</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" enctype="multipart/form-data" id="formPengajuan" method="POST">
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Nomor BAST</label>
                            <div class="col-sm-4">
                                <input class="form-control readonlyInput" id="nomorBast" name="nomorBast" type="text"
                                    readonly placeholder="Tidak perlu diisi, otomatis">
                            </div>
                            <label class="col-sm-2 col-form-label">Tanggal BAST</label>
                            <div class="col-sm-4">
                                <input class="form-control" id="tanggalBast" name="tanggalBast" type="date">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Nomor Surat</label>
                            <div class="col-sm-4">
                                <input class="form-control readonlyInput" id="nomorSurat" name="nomorSurat" type="text"
                                    readonly>
                            </div>
                            <label class="col-sm-2 col-form-label">Tanggal Pinjam</label>
                            <div class="col-sm-4">
                                <input class="form-control readonlyInput" id="tanggalPinjam" name="tanggalPinjam"
                                    type="text" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Nomor Register</label>
                            <div class="col-sm-4">
                                <input class="form-control readonlyInput" id="nomorRegister" name="nomorRegister"
                                    type="text" readonly>
                            </div><label class="col-sm-2 col-form-label">Nomor Polisi</label>
                            <div class="col-sm-4">
                                <input class="form-control readonlyInput" id="nomorPolisi" name="nomorPolisi" type="text"
                                    readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Nomor Rangka</label>
                            <div class="col-sm-4">
                                <input class="form-control readonlyInput" id="nomorRangka" name="nomorRangka" type="text"
                                    readonly>
                            </div><label class="col-sm-2 col-form-label">Nomor Bpkb</label>
                            <div class="col-sm-4">
                                <input class="form-control readonlyInput" id="nomorBpkb" name="nomorBpkb" type="text"
                                    readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Nama PBP</label>
                            <div class="col-sm-4">
                                <input class="form-control readonlyInput" id="namaPbp" name="namaPbp" type="text"
                                    readonly>
                            </div><label class="col-sm-2 col-form-label">NIP PBP</label>
                            <div class="col-sm-4">
                                <input class="form-control readonlyInput" id="nipPbp" name="nipPbp" type="text"
                                    readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Nomor Telp PBP</label>
                            <div class="col-sm-4">
                                <input class="form-control readonlyInput" id="nomorTelpPbp" name="nomorTelpPbp"
                                    type="text" readonly>
                            </div><label class="col-sm-2 col-form-label">Nama SKPD</label>
                            <div class="col-sm-4">
                                <input class="form-control readonlyInput" id="namaSkpd" name="namaSkpd" type="text"
                                    readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Keperluan</label>
                            <div class="col-sm-10">
                                <textarea class="form-control readonlyInput" id="keperluan" name="keperluan" type="text" readonly></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12 text-center">
                                <button type="button" class="btn btn-success btn-lg" id="simpanBast">
                                    <i class="bx bx-check d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Simpan</span>
                                </button>
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

    <div class="modal fade text-left" id="modalCetak" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak Form Penyerahan BAST</h5>
                    <button type="button" class="close rounded-pill" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Nomor Bast</label>
                        <div class="col-sm-8">
                            <input class="form-control readonlyInput" id="nomorBastCetak" type="text" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Kode SKPD</label>
                        <div class="col-sm-8">
                            <input class="form-control readonlyInput" id="kodeSkpdCetak" type="text" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Tanggal TTD</label>
                        <div class="col-sm-8">
                            <input class="form-control" id="tanggalTtdCetak" type="date">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Tanda Tangan I</label>
                        <div class="col-sm-8">
                            <select class="form-select select_option" id="tandaTangan" data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                @foreach ($daftarTandaTangan as $item)
                                    <option value="{{ $item->nip }}">{{ $item->nip }} | {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Tanda Tangan II</label>
                        <div class="col-sm-8">
                            <select class="form-select select_option" id="tandaTangan2"
                                data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                @foreach ($daftarTandaTanganKepala as $item)
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

            let bpkb = $('#bpkb').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('bast.bpkb.load') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                    }
                },
                pageLength: 10,
                searching: true,
                createdRow: function(row, data, index) {
                    if (data.statusBast == "1" && data.statusPengembalian != "1") {
                        $(row).css("background-color", "#90EE90");
                    } else if (data.statusBast == "1" && data.statusPengembalian == "1") {
                        $(row).css("background-color", "#ADD8E6");
                    }
                },
                columns: [{
                        data: 'nomorBast',
                    }, {
                        data: 'tanggalBast',
                    }, {
                        data: 'nomorSurat',
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

            let selectedData;

            $('#bpkb tbody').on('click', '.pengajuan', function() {
                selectedData = bpkb.row($(this).parents('tr')).data();

                $('#nomorBast').val(selectedData.nomorBast);
                $('#tanggalBast').val(selectedData.tanggalBast);
                $('#nomorSurat').val(selectedData.nomorSurat);
                $('#tanggalPinjam').val(tanggalIndonesia(selectedData.tanggalPinjam));
                $('#nomorRegister').val(selectedData.nomorRegister);
                $('#nomorPolisi').val(selectedData.nomorPolisi);
                $('#nomorRangka').val(selectedData.nomorRangka);
                $('#nomorBpkb').val(selectedData.nomorBpkb);
                $('#namaPbp').val(selectedData.namaPbp);
                $('#nipPbp').val(selectedData.nipPbp);
                $('#nomorTelpPbp').val(selectedData.nomorTelpPbp);
                $('#namaSkpd').val(selectedData.namaSkpd);
                $('#keperluan').val(selectedData.keperluan);

                $('#simpanBast').prop('hidden', true);

                selectedData.statusPengembalian != '1' ? $('#simpanBast').prop('hidden', false) : $(
                    '#simpanBast').prop('hidden', true);

                const formatFile =
                    `storage/images/Peminjaman/BPKB/${selectedData.kodeSkpd}/${selectedData.file}`;

                selectedData.file ? document.getElementById('tampilanFilePengajuan').src =
                    `{{ asset('${formatFile}') }}` : '';

                $('#modalPengajuan').modal('show')
            });

            $('#simpanBast').on('click', function() {
                let tanggalBast = $('#tanggalBast').val()

                if (!selectedData) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan refresh!Data yang dibuat BAST tidak ada!",
                        icon: "warning"
                    });
                    return;
                }

                if (!tanggalBast) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan pilih tanggal BAST!",
                        icon: "warning"
                    });
                    return;
                }

                if (tanggalBast < selectedData.tanggalPinjam) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Tanggal BAST tidak boleh lebih kecil dari tanggal pinjam!",
                        icon: "warning"
                    });
                    return;
                }

                if (tanggalBast < selectedData.tanggalVerifPenyelia) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Tanggal BAST tidak boleh lebih kecil dari tanggal verifikasi penyelia! " +
                            tanggalIndonesia(selectedData.tanggalVerifPenyelia),
                        icon: "warning"
                    });
                    return;
                }

                $.ajax({
                    url: "{{ route('bast.bpkb.simpan') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        selectedData: selectedData,
                        tanggalBast: tanggalBast,
                    },
                    beforeSend: function() {
                        $("#overlay").fadeIn(100);
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Berhasil!",
                            text: response.message,
                            icon: "success"
                        });

                        $('#nomorBast').val(null);
                        $('#tanggalBast').val(null);
                        $('#nomorSurat').val(null);
                        $('#tanggalPinjam').val(null);
                        $('#nomorRegister').val(null);
                        $('#nomorPolisi').val(null);
                        $('#nomorRangka').val(null);
                        $('#nomorBpkb').val(null);
                        $('#namaPbp').val(null);
                        $('#nipPbp').val(null);
                        $('#nomorTelpPbp').val(null);
                        $('#namaSkpd').val(null);
                        $('#keperluan').val(null);

                        $('#simpanBast').prop('hidden', true);

                        document.getElementById('tampilanFilePengajuan').src = ''

                        $('#modalPengajuan').modal('hide')

                        bpkb.ajax.reload()
                    },
                    error: function(e) {
                        Swal.fire({
                            title: "Gagal!",
                            text: e.responseJSON.message,
                            icon: "error"
                        });
                    },
                    complete: function(data) {
                        $("#overlay").fadeOut(100);
                    }
                });
            });

            $('.cetak').on('click', function() {
                let nomorBast = $('#nomorBastCetak').val();
                let kodeSkpd = $('#kodeSkpdCetak').val();
                let tanggalTtd = $('#tanggalTtdCetak').val();
                let tandaTangan = $('#tandaTangan').val();
                let tandaTangan2 = $('#tandaTangan2').val();
                let tipe = $(this).data('tipe');

                if (!nomorBast) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Nomor BAST tidak boleh kosong",
                        icon: "warning"
                    });
                    return;
                }

                if (!kodeSkpd) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Kode SKPD tidak boleh kosong",
                        icon: "warning"
                    });
                    return;
                }

                if (!tanggalTtd) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan pilih tanggal TTD",
                        icon: "warning"
                    });
                    return;
                }

                if (!tandaTangan) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan pilih tanda tangan I",
                        icon: "warning"
                    });
                    return;
                }

                if (!tandaTangan2) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan pilih tanda tangan II",
                        icon: "warning"
                    });
                    return;
                }

                let url = new URL("{{ route('bast.bpkb.cetak') }}");
                let searchParams = url.searchParams;
                searchParams.append("nomorBast", nomorBast);
                searchParams.append("kodeSkpd", kodeSkpd);
                searchParams.append("nomorRegister", nomorRegister);
                searchParams.append("tanggalTtd", tanggalTtd);
                searchParams.append("tandaTangan", tandaTangan);
                searchParams.append("tandaTangan2", tandaTangan2);
                searchParams.append("tipe", tipe);
                window.open(url.toString(), "_blank");
            });
        });

        function hapus(nomorBast, kodeSkpd) {
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
                        url: "{{ route('bast.bpkb.hapus') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            nomorBast: nomorBast,
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

        function cetak(nomorBast, kodeSkpd) {
            $('#nomorBastCetak').val(nomorBast);
            $('#kodeSkpdCetak').val(kodeSkpd);
            $('#modalCetak').modal('show');
        }
    </script>
@endpush
