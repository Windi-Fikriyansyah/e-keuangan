@extends('template.app')
@section('title', 'Verifikasi Bast Sertifikat')
@section('content')
    <div class="page-heading">
        <h3>Verifikasi Bast Sertifikat</h3>
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

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="sertifikat" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Nomor Bast</th>
                                <th>Nomor Register</th>
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

    <div class="modal fade text-left" id="modalCetak" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak Form Bast Sertifikat</h5>
                    <button type="button" class="close rounded-pill" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Nomor Bast</label>
                        <div class="col-sm-8">
                            <input class="form-control readonlyInput" id="nomorBast1" type="text" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Nomor Surat</label>
                        <div class="col-sm-8">
                            <input class="form-control readonlyInput" id="nomorSurat1" type="text" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Nomor Register</label>
                        <div class="col-sm-8">
                            <input class="form-control readonlyInput" id="nomorRegister1" type="text" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Pihak Kedua</label>
                        <div class="col-sm-8">
                            <input class="form-control readonlyInput" id="namaKsbtgn1" type="text" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Pihak Pertama</label>
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
                        <label class="col-sm-4 col-form-label">Tanggal TTD</label>
                        <div class="col-sm-8">
                            <input class="form-control" id="tanggalTtdCetak" type="date">
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


    <div class="modal fade" id="verifModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verifModalLabel">Verifikasi Peminjaman Sertifikat</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <input class="form-control readonlyInput" id="tanggalVerifPenyelia"
                            name="tanggalVerifPenyelia" type="text" placeholder="Tidak perlu diisi, otomatis" hidden>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Tanggal Bast</label>
                    <div class="col-sm-4">
                        <input class="form-control" id="tanggalBast" name="tanggalBast" type="date">
                    </div>
                    <label class="col-sm-2 col-form-label">Nomor Bast</label>
                    <div class="col-sm-4">
                        <input class="form-control readonlyInput" id="nomorBast"
                            name="nomorBast" type="text" placeholder="Tidak perlu diisi, otomatis" readonly>
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
                    <label class="col-sm-2 col-form-label">Nomor Sertifikat</label>
                    <div class="col-sm-4">
                        <input class="form-control readonlyInput" id="nomorSertifikat" name="nomorSertifikat" type="text"
                            readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">NIB</label>
                    <div class="col-sm-4">
                        <input class="form-control readonlyInput" id="NIB" name="NIB" type="text"
                            readonly>
                    </div>
                    <label class="col-sm-2 col-form-label">Nama KSBTGT</label>
                    <div class="col-sm-4">
                        <input class="form-control readonlyInput" id="namaKsbtgn" name="namaKsbtgn" type="text"
                            readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Luas</label>
                    <div class="col-sm-4">
                        <input class="form-control readonlyInput" id="luas" name="luas" type="text" readonly>
                    </div>
                    <label class="col-sm-2 col-form-label">Nip KSBTGT</label>
                    <div class="col-sm-4">
                        <input class="form-control readonlyInput" id="nipKsbtgn" name="nipKsbtgn" type="text" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">kode SKPD</label>
                    <div class="col-sm-4">
                        <input class="form-control readonlyInput" id="kodeSkpd" name="kodeSkpd" type="text"
                            readonly>
                    </div>
                    <label class="col-sm-2 col-form-label">No Telp KSBTGT</label>
                    <div class="col-sm-4">
                        <input class="form-control readonlyInput" id="noTelpKsbtgn" name="noTelpKsbtgn" type="text"
                            readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Peruntukan</label>
                    <div class="col-sm-10">
                        <textarea class="form-control readonlyInput" id="peruntukan" name="peruntukan" type="text" readonly></textarea>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-success ms-1" id="VerifBast">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Verifikasi</span>
                        </button>
                        <button type="button" class="btn btn-danger ms-1 d-none" id="BatalkanVerif">
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

            $('#sertifikat').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('bast.sertifikat.load') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = data.search.value;
                    }
                },
                pageLength: 10,
                searching: true,
                columns: [
                {
                    data: 'nomorBast',
                    name: 'nomorBast'
                },
                {
                    data: 'nomorRegister',
                    name: 'nomorRegister'
                },
                {
                    data: 'nomorSurat',
                    name: 'nomorSurat'
                },
                {
                    data: 'namaSkpd',
                    name: 'namaSkpd'
                },
                {
                    data: 'aksi',
                    width: "200",
                    className: 'text-center'
                }
            ]
            });
        });

        function verif(nomorSurat) {
            $.ajax({
                url: '{{ route("bast.sertifikat.verif") }}',
                type: 'POST',
                data: {
                    nomorSurat: nomorSurat,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#nomorBast').val(data.nomorBast);
                    $('#tanggalBast').val(data.tanggalBast);
                    $('#tanggalVerifPenyelia').val(data.tanggalVerifPenyelia);
                    $('#nomorSurat').val(data.nomorSurat);
                    $('#tanggalPinjam').val(data.tanggalPinjam);
                    $('#nomorRegister').val(data.nomorRegister);
                    $('#nomorSertifikat').val(data.nomorSertifikat);
                    $('#NIB').val(data.NIB);
                    $('#pemegangHak').val(data.pemegangHak);
                    $('#tanggal').val(data.tanggal);
                    $('#pemegangHak').val(data.pemegangHak);
                    $('#luas').val(data.luas);
                    $('#peruntukan').val(data.peruntukan);
                    $('#namaKsbtgn').val(data.namaKsbtgn);
                    $('#nipKsbtgn').val(data.nipKsbtgn);
                    $('#kodeSkpd').val(data.kodeSkpd);
                    $('#noTelpKsbtgn').val(data.noTelpKsbtgn);

                    if (data.statusPengembalian == 1) {
                        $('#VerifBast').addClass('d-none');
                        $('#BatalkanVerif').addClass('d-none');
                    } else if (data.statusBast == 1) {
                        $('#VerifBast').addClass('d-none');
                        $('#BatalkanVerif').removeClass('d-none');
                    } else {
                        $('#VerifBast').removeClass('d-none');
                        $('#BatalkanVerif').addClass('d-none');
                    }

                    const filePath = `storage/images/Peminjaman/Sertifikat/${data.kodeSkpd}/${data.file}`;
                    if (data.file) {
                        $('#filePengajuan').attr('src', `{{ asset('${filePath}') }}`);
                    } else {
                        $('#filePengajuan').attr('src', '');
                    }
                    $('#verifModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Terjadi kesalahan:', error);
                }
            });
        }

        $('#VerifBast').on('click', function() {
            var nomorSurat = $('#nomorSurat').val();
            var tanggalPinjam = $('#tanggalPinjam').val();
            var tanggalBast = $('#tanggalBast').val();
            var tanggalVerifPenyelia = $('#tanggalVerifPenyelia').val();
            if (!tanggalBast) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan pilih tanggal verifikasi!",
                        icon: "warning"
                    });
                    return;  // Hentikan eksekusi jika tanggal belum dipilih
                }
                if (tanggalBast < tanggalPinjam) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Tanggal verifikasi tidak boleh lebih kecil dari tanggal pinjam!",
                        icon: "warning"
                    });
                    return;
                }

                if (tanggalBast < tanggalVerifPenyelia) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Tanggal verifikasi tidak boleh lebih kecil dari tanggal Verif Penyelia!",
                        icon: "warning"
                    });
                    return;
                }
            $.ajax({
                url: '{{ route("bast.sertifikat.verifikasi_bast") }}',
                type: 'POST',
                data: {
                    nomorSurat: nomorSurat,
                    tanggalBast:tanggalBast,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#VerifBast').addClass('d-none');
                    $('#BatalkanVerif').removeClass('d-none');

                    Swal.fire({
                        icon: 'success',
                        title: 'Verifikasi berhasil!',
                        text: 'Nomor Surat ' + nomorSurat + ' telah diverifikasi.',
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

        $('#BatalkanVerif').on('click', function() {
            var nomorSurat = $('#nomorSurat').val();

            $.ajax({
                url: '{{ route("bast.sertifikat.batalkan") }}',
                type: 'POST',
                data: {
                    nomorSurat: nomorSurat,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#BatalkanVerif').addClass('d-none');
                    $('#VerifBast').removeClass('d-none');

                    Swal.fire({
                        icon: 'warning',
                        title: 'Verifikasi dibatalkan!',
                        text: 'Nomor Surat ' + nomorSurat + ' telah dibatalkan verifikasinya.',
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

        function cetak(nomorBast,namaKsbtgn, nomorSurat, nomorRegister,) {
            $('#nomorBast1').val(nomorBast);
            $('#nomorSurat1').val(nomorSurat);
            $('#nomorRegister1').val(nomorRegister);
            $('#namaKsbtgn1').val(namaKsbtgn);
            $('#modalCetak').modal('show');
        }

        $('.cetak').on('click', function() {
            let nomorBast = $('#nomorBast1').val();
                let nomorSurat = $('#nomorSurat1').val();
                let nomorRegister = $('#nomorRegister1').val();
                let namaKsbtgn = $('#namaKsbtgn1').val();
                let tandaTangan = $('#tandaTangan').val();
                let tanggalTtdCetak = $('#tanggalTtdCetak').val();
                let tipe = $(this).data('tipe');


                if (!nomorBast) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Nomor Bast tidak boleh kosong",
                        icon: "warning"
                    });
                    return;
                }
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
                        text: "Silahkan pilih Pihak Pertama",
                        icon: "warning"
                    });
                    return;
                }

                if (!namaKsbtgn) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan pilih Pihak Kedua",
                        icon: "warning"
                    });
                    return;
                }

                let url = new URL("{{ route('bast.sertifikat.cetak') }}");
                let searchParams = url.searchParams;
                searchParams.append("nomorBast", nomorBast);
                searchParams.append("nomorSurat", nomorSurat);
                searchParams.append("nomorRegister", nomorRegister);
                searchParams.append("namaKsbtgn", namaKsbtgn);
                searchParams.append("tandaTangan", tandaTangan);
                searchParams.append("tanggalTtdCetak", tanggalTtdCetak);
                searchParams.append("tipe", tipe);
                window.open(url.toString(), "_blank");
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
                        url: "{{ route('bast.sertifikat.hapus') }}",
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
