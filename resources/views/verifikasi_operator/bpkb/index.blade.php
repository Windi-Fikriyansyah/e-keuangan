@extends('template.app')
@section('title', 'Verifikasi Operator')
@section('content')
    <div class="page-heading">
        <h3>Verifikasi Operator</h3>
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

    <div class="modal fade" id="modalVerifikasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Verifikasi Peminjaman BPKB</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tanggal Verifikasi</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="tanggalVerifikasi" name="tanggalVerifikasi" type="date">
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
                            <input class="form-control readonlyInput" id="tanggalPinjam" name="tanggalPinjam" type="text"
                                readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Register</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput" id="nomorRegister" name="nomorRegister" type="text"
                                readonly>
                        </div>
                        <label class="col-sm-2 col-form-label">Nomor Polisi</label>
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
                        </div>
                        <label class="col-sm-2 col-form-label">Nomor Bpkb</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput" id="nomorBpkb" name="nomorBpkb" type="text"
                                readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nama PBP</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput" id="namaPbp" name="namaPbp" type="text" readonly>
                        </div>
                        <label class="col-sm-2 col-form-label">NIP Bpkb</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput" id="nipPbp" name="nipPbp" type="text" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Telp. PBP</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput" id="nomorTelpPbp" name="nomorTelpPbp" type="text"
                                readonly>
                        </div>
                        <label class="col-sm-2 col-form-label">Kode SKPD</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput" id="kodeSkpd" name="kodeSkpd" type="text"
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
                            <button type="button" class="btn btn-success ms-1" id="setujuVerifikasi" hidden>
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Verifikasi</span>
                            </button>
                            <button type="button" class="btn btn-danger ms-1" id="TolakVerif">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Tolak Peminjaman</span>
                            </button>
                            <button type="button" class="btn btn-danger ms-1" id="batalVerifikasi" hidden>
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Batal Verifikasi</span>
                            </button>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-12">
                            <iframe style="width: 100%;height:100vh" id="filePengajuan"></iframe>
                        </div>
                    </div>
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
                    url: "{{ route('verifikasi_operator.bpkb.load') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                    }
                },
                createdRow: function(row, data, index) {
                    if (data.statusVerifikasiOperator == "1" && data.statusVerifAdmin != "1" && data.statusTolak !== "1") {
                        $(row).css("background-color", "#90EE90");
                    } else if (data.statusVerifikasiOperator == "1" && data.statusVerifAdmin ==
                        "1") {
                        $(row).css("background-color", "#ADD8E6");
                    } else if (data.statusTolak == "1") {
                        $(row).css("background-color", "#ff0e0e");
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

            let selectedData;

            $('#bpkb tbody').on('click', '.verifikasi', function() {
                selectedData = bpkb.row($(this).parents('tr')).data();

                $('#tanggalVerifikasi').val(selectedData.tanggalVerifikasiOperator);
                $('#nomorSurat').val(selectedData.nomorSurat);
                $('#tanggalPinjam').val(tanggalIndonesia(selectedData.tanggalPinjam));
                $('#nomorRegister').val(selectedData.nomorRegister);
                $('#nomorPolisi').val(selectedData.nomorPolisi);
                $('#nomorRangka').val(selectedData.nomorRangka);
                $('#nomorBpkb').val(selectedData.nomorBpkb);
                $('#namaPbp').val(selectedData.namaPbp);
                $('#nipPbp').val(selectedData.nipPbp);
                $('#nomorTelpPbp').val(selectedData.nomorTelpPbp);
                $('#keperluan').val(selectedData.keperluan);
                $('#kodeSkpd').val(selectedData.kodeSkpd);

                $('#setujuVerifikasi').prop('hidden', true);
                $('#batalVerifikasi').prop('hidden', true);


                if(selectedData.statusTolak == 1){
                    $('#setujuVerifikasi').prop('hidden', true);
                    $('#batalVerifikasi').prop('hidden', true);
                    $('#TolakVerif').prop('hidden', true);
                    }
                else if (selectedData.statusVerifikasiOperator != '1') {
                    $('#setujuVerifikasi').prop('hidden', false);
                    $('#TolakVerif').prop('hidden', false);
                } else if (selectedData.statusVerifikasiOperator == '1' && selectedData.statusVerifAdmin !=
                    '1') {
                    $('#batalVerifikasi').prop('hidden', false);
                    $('#TolakVerif').prop('hidden', true);
                }

                const formatFile =
                    `storage/images/Peminjaman/BPKB/${selectedData.kodeSkpd}/${selectedData.file}`;

                selectedData.file ? document.getElementById('filePengajuan').src =
                    `{{ asset('${formatFile}') }}` : '';

                $('#modalVerifikasi').modal('show')
            });

            $('#setujuVerifikasi').on('click', function() {
                if (!selectedData) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan refresh!Data yang diverifikasi tidak ada!",
                        icon: "warning"
                    });
                    return;
                }

                let tanggalVerifikasi = $('#tanggalVerifikasi').val();

                if (!tanggalVerifikasi) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan pilih tanggal verifikasi!",
                        icon: "warning"
                    });
                    return;
                }


                if (tanggalVerifikasi < selectedData.tanggalPinjam) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Tanggal verifikasi tidak boleh lebih kecil dari tanggal pinjam!",
                        icon: "warning"
                    });
                    return;
                }

                $.ajax({
                    url: "{{ route('verifikasi_operator.bpkb.verifikasi') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        selectedData: selectedData,
                        tanggalVerifikasi: tanggalVerifikasi,
                        tipe: 'setuju',
                    },
                    beforeSend: function() {
                        $("#overlay").fadeIn(100);
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Berhasil!",
                            text: "Pengajuan berhasil diverifikasi",
                            icon: "success"
                        });

                        $('#tanggalVerifikasi').val(null);
                        $('#nomorSurat').val(null);
                        $('#tanggalPinjam').val(null);
                        $('#nomorRegister').val(null);
                        $('#nomorPolisi').val(null);
                        $('#nomorRangka').val(null);
                        $('#nomorBpkb').val(null);
                        $('#namaPbp').val(null);
                        $('#nipPbp').val(null);
                        $('#nomorTelpPbp').val(null);
                        $('#keperluan').val(null);
                        $('#kodeSkpd').val(null);

                        $('#setujuVerifikasi').prop('hidden', true);
                        $('#batalVerifikasi').prop('hidden', true);

                        document.getElementById('filePengajuan').src = ''

                        $('#modalVerifikasi').modal('hide')

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


            $('#TolakVerif').on('click', function() {
            var nomorSurat = $('#nomorSurat').val();
            var kodeSkpd = $('#kodeSkpd').val();
            var nomorRegister = $('#nomorRegister').val();
            $.ajax({
                url: '{{ route("verifikasi_operator.bpkb.tolak") }}',
                type: 'POST',
                data: {
                    nomorSurat: nomorSurat,
                    kodeSkpd: kodeSkpd,
                    nomorRegister: nomorRegister,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#setujuVerifikasi').prop('hidden', true);
                    $('#batalVerifikasi').prop('hidden', true);
                    $('#TolakVerif').prop('hidden', true);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peminjaman Ditolak!',
                        text: 'Nomor Surat ' + nomorSurat + ' Peminjaman Telah Ditolak',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Terjadi kesalahan:', error);
                }
            });
        });

            $('#batalVerifikasi').on('click', function() {
                if (!selectedData) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan refresh!Data yang diverifikasi tidak ada!",
                        icon: "warning"
                    });
                    return;
                }

                $.ajax({
                    url: "{{ route('verifikasi_operator.bpkb.verifikasi') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        selectedData: selectedData,
                        tipe: 'batal',
                    },
                    beforeSend: function() {
                        $("#overlay").fadeIn(100);
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Berhasil!",
                            text: "Pengajuan berhasil batal verifikasi",
                            icon: "success"
                        });

                        $('#tanggalVerifikasi').val(null);
                        $('#nomorSurat').val(null);
                        $('#tanggalPinjam').val(null);
                        $('#nomorRegister').val(null);
                        $('#nomorPolisi').val(null);
                        $('#nomorRangka').val(null);
                        $('#nomorBpkb').val(null);
                        $('#namaPbp').val(null);
                        $('#nipPbp').val(null);
                        $('#nomorTelpPbp').val(null);
                        $('#keperluan').val(null);
                        $('#kodeSkpd').val(null);

                        $('#setujuVerifikasi').prop('hidden', true);
                        $('#batalVerifikasi').prop('hidden', true);

                        document.getElementById('filePengajuan').src = ''

                        $('#modalVerifikasi').modal('hide')

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
        });
    </script>
@endpush
