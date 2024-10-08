@extends('template.app')
@section('title', 'Pengembalian Sertifikat')
@section('content')
    <div class="page-heading">
        <h3>Pengembalian Sertifikat</h3>
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
                                <th>Nomor Surat</th>
                                <th>Nomor Register</th>
                                <th>Nomor Sertifikat</th>
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
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Tanggal Verifikasi</label>
                    <div class="col-sm-4">
                        <input class="form-control" id="tanggalPengembalian" name="tanggalPengembalian" type="date">
                    </div>
                    <label class="col-sm-2 col-form-label">Tanggal Bast</label>
                    <div class="col-sm-4">
                        <input class="form-control readonlyInput" id="tanggalBast"
                            name="tanggalBast" type="text" readonly>
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
                        <button type="button" class="btn btn-success ms-1" id="VerifPenyelia">
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
                    url: "{{ route('pengembalian.sertifikat.load') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = data.search.value;
                    }
                },
                pageLength: 10,
                searching: true,
                columns: [
                {
                    data: 'nomorSurat',
                    name: 'nomorSurat'
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
                    data: 'namaSkpd',
                    name: 'namaSkpd'
                },
                {
                    data: 'aksi',
                    className: 'text-center'
                }
            ]
            });
        });

        function verif(nomorSurat) {
            $.ajax({
                url: '{{ route("pengembalian.sertifikat.verif") }}',
                type: 'POST',
                data: {
                    nomorSurat: nomorSurat,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#tanggalPengembalian').val(data.tanggalPengembalian);
                    $('#tanggalBast').val(data.tanggalBast);
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


                    if (data.statusPengembalian == 1 && data.statusPinjamLagi == 1) {
                        $('#VerifPenyelia').addClass('d-none');
                        $('#BatalkanVerif').addClass('d-none');
                    } else if (data.statusPengembalian == 1 && data.statusPinjamLagi !== 1) {
                        $('#VerifPenyelia').addClass('d-none');
                        $('#BatalkanVerif').removeClass('d-none');
                    } else {
                        $('#VerifPenyelia').removeClass('d-none');
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

        $('#VerifPenyelia').on('click', function() {
            var nomorSurat = $('#nomorSurat').val();
            var kodeSkpd = $('#kodeSkpd').val();
            var nomorRegister = $('#nomorRegister').val();
            var tanggalPengembalian = $('#tanggalPengembalian').val();
            var tanggalBast = $('#tanggalBast').val();
            if (!tanggalPengembalian) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan pilih tanggal verifikasi!",
                        icon: "warning"
                    });
                    return;  // Hentikan eksekusi jika tanggal belum dipilih
                }
                if (tanggalPengembalian < tanggalBast) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Tanggal verifikasi tidak boleh lebih kecil dari tanggal Bast!",
                        icon: "warning"
                    });
                    return;
                }
            $.ajax({
                url: '{{ route("pengembalian.sertifikat.pengembalian") }}',
                type: 'POST',
                data: {
                    nomorSurat: nomorSurat,
                    kodeSkpd: kodeSkpd,
                    nomorRegister: nomorRegister,
                    tanggalPengembalian: tanggalPengembalian,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#VerifPenyelia').addClass('d-none');
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
            var kodeSkpd = $('#kodeSkpd').val();
            var nomorRegister = $('#nomorRegister').val();
            $.ajax({
                url: '{{ route("pengembalian.sertifikat.batalkan") }}',
                type: 'POST',
                data: {
                    nomorSurat: nomorSurat,
                    kodeSkpd: kodeSkpd,
                    nomorRegister: nomorRegister,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#BatalkanVerif').addClass('d-none');
                    $('#VerifPenyelia').removeClass('d-none');

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
    </script>
@endpush
