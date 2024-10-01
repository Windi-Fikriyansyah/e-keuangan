@extends('template.app')
@section('title', 'Pengembalian BPKB')
@section('content')
    <div class="page-heading">
        <h3>Pengembalian BPKB</h3>
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
                                <th>Tanggal Kembali</th>
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

    <div class="modal fade" id="modalVerifikasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Pengembalian Peminjaman BPKB</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tanggal Pengembalian</label>
                        <div class="col-sm-4">
                            <input class="form-control" id="tanggalPengembalian" name="tanggalPengembalian" type="date">
                        </div>
                        <label class="col-sm-2 col-form-label">Tanggal BAST</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput" id="tanggalBast" name="tanggalBast" type="text"
                                readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Surat</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput" id="nomorSurat" name="nomorSurat" type="text"
                                readonly>
                        </div>
                        <label class="col-sm-2 col-form-label">Nomor BAST</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput" id="nomorBast" name="nomorBast" type="text"
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
                            <input class="form-control readonlyInput" id="nomorTelpPbp" name="nomorTelpPbp"
                                type="text" readonly>
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
                    url: "{{ route('pengembalian.bpkb.load') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                    }
                },
                createdRow: function(row, data, index) {
                    if (data.statusPengembalian == "1" && data.statusPinjamLagi != "1") {
                        $(row).css("background-color", "#90EE90");
                    } else if (data.statusPengembalian == "1" && data.statusPinjamLagi ==
                        "1") {
                        $(row).css("background-color", "#ADD8E6");
                    }
                },
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'tanggalPengembalian',
                    }, {
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

            $('#bpkb tbody').on('click', '.kembali', function() {
                selectedData = bpkb.row($(this).parents('tr')).data();

                $('#tanggalPengembalian').val(selectedData.tanggalPengembalian);
                $('#nomorBast').val(selectedData.nomorBast);
                $('#tanggalBast').val(tanggalIndonesia(selectedData.tanggalBast));
                $('#nomorSurat').val(selectedData.nomorSurat);
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
                console.log(selectedData)
                if (selectedData.statusPengembalian != '1') {
                    $('#setujuVerifikasi').prop('hidden', false);
                } else if (selectedData.statusPengembalian == '1' && selectedData.statusPinjamLagi !=
                    '1') {
                    $('#batalVerifikasi').prop('hidden', false);
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

                let tanggalPengembalian = $('#tanggalPengembalian').val();

                if (!tanggalPengembalian) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan pilih tanggal verifikasi!",
                        icon: "warning"
                    });
                    return;
                }


                if (tanggalPengembalian < selectedData.tanggalBast) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Tanggal pengembalian tidak boleh lebih kecil dari tanggal BAST!",
                        icon: "warning"
                    });
                    return;
                }

                $.ajax({
                    url: "{{ route('pengembalian.bpkb.verifikasi') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        selectedData: selectedData,
                        tanggalPengembalian: tanggalPengembalian,
                        tipe: 'setuju',
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Berhasil!",
                            text: "Pengembalian berhasil diverifikasi",
                            icon: "success"
                        });

                        $('#tanggalPengembalian').val(null);
                        $('#nomorBast').val(null);
                        $('#tanggalBast').val(null);
                        $('#nomorSurat').val(null);
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
                    url: "{{ route('pengembalian.bpkb.verifikasi') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        selectedData: selectedData,
                        tipe: 'batal',
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Berhasil!",
                            text: "Pengembalian berhasil batal verifikasi",
                            icon: "success"
                        });

                        $('#tanggalPengembalian').val(null);
                        $('#nomorBast').val(null);
                        $('#tanggalBast').val(null);
                        $('#nomorSurat').val(null);
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
                });
            });
        });
    </script>
@endpush
