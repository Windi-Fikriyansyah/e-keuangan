@extends('template.app')
@section('title', 'Verifikasi Admin Sertifikat')
@section('content')
    <div class="page-heading">
        <h3>Verifikasi Admin Sertifikat</h3>
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

    <div class="modal fade" id="verifModal" tabindex="-1" role="dialog" aria-labelledby="verifModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="verifModalLabel">Verifikasi Pinjaman Sertifikat</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#verifModal').modal('hide');">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered">
                <tr>
                    <th>Nomor Surat</th>
                    <td id="nomorSurat"></td>
                  </tr>
                <tr>
                  <th>Tanggal Pinjam</th>
                  <td id="tanggalPinjam"></td>
                </tr>
                <tr>
                  <th>Nomor Register</th>
                  <td id="nomorRegister"></td>
                </tr>
                <tr>
                  <th>Nomor Sertifikat</th>
                  <td id="nomorSertifikat"></td>
                </tr>
                <tr>
                  <th>NIB</th>
                  <td id="NIB"></td>
                </tr>
                <tr>
                  <th>Tanggal</th>
                  <td id="tanggal"></td>
                </tr>
                <tr>
                  <th>Pemegang Hak</th>
                  <td id="pemegangHak"></td>
                </tr>
                <tr>
                  <th>Luas</th>
                  <td id="luas"></td>
                </tr>
                <tr>
                  <th>Peruntukan</th>
                  <td id="peruntukan"></td>
                </tr>
                <tr>
                  <th>Nama Kepala Sub Bagian Tata Guna Tanah</th>
                  <td id="namaKsbtgn"></td>
                </tr>
                <tr>
                  <th>NIP Kepala Sub Bagian Tata Guna Tanah</th>
                  <td id="nipKsbtgn"></td>
                </tr>
                <tr>
                  <th>No. Telp Kepala Sub Bagian Tata Guna Tanah</th>
                  <td id="noTelpKsbtgn"></td>
                </tr>
              </table>
            </div>
            <div class="modal-footer">
                <div class="row mb-3">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-success ms-1" id="VerifOperator">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Verifikasi</span>
                        </button>
                        <button type="button" class="btn btn-danger ms-1 d-none" id="BatalkanVerif">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Batal Verifikasi</span>
                        </button>
                        <button type="button" class="btn btn-warning" onclick="$('#verifModal').modal('hide');">Kembali</button>
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
                    url: "{{ route('verifikasi_admin.sertifikat.load') }}",
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
                url: '{{ route("verifikasi_admin.sertifikat.verif") }}',
                type: 'POST',
                data: {
                    nomorSurat: nomorSurat,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#nomorSurat').text(data.nomorSurat);
                    $('#tanggalPinjam').text(data.tanggalPinjam);
                    $('#nomorRegister').text(data.nomorRegister);
                    $('#nomorSertifikat').text(data.nomorSertifikat);
                    $('#NIB').text(data.NIB);
                    $('#tanggal').text(data.tanggal);
                    $('#pemegangHak').text(data.pemegangHak);
                    $('#luas').text(data.luas);
                    $('#peruntukan').text(data.peruntukan);
                    $('#namaKsbtgn').text(data.namaKsbtgn);
                    $('#nipKsbtgn').text(data.nipKsbtgn);
                    $('#noTelpKsbtgn').text(data.noTelpKsbtgn);

                    if (data.statusVerifPenyelia == 1) {
                        $('#VerifOperator').addClass('d-none');
                        $('#BatalkanVerif').addClass('d-none');
                    } else if (data.statusVerifAdmin == 1) {
                        $('#VerifOperator').addClass('d-none');
                        $('#BatalkanVerif').removeClass('d-none');
                    } else {
                        $('#VerifOperator').removeClass('d-none');
                        $('#BatalkanVerif').addClass('d-none');
                    }


                    $('#verifModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Terjadi kesalahan:', error);
                }
            });
        }

        $('#VerifOperator').on('click', function() {
            var nomorSurat = $('#nomorSurat').text();

            $.ajax({
                url: '{{ route("verifikasi_admin.sertifikat.verifikasi_admin") }}',
                type: 'POST',
                data: {
                    nomorSurat: nomorSurat,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#VerifOperator').addClass('d-none');
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
            var nomorSurat = $('#nomorSurat').text();

            $.ajax({
                url: '{{ route("verifikasi_admin.sertifikat.batalkan") }}',
                type: 'POST',
                data: {
                    nomorSurat: nomorSurat,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#BatalkanVerif').addClass('d-none');
                    $('#VerifOperator').removeClass('d-none');

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
