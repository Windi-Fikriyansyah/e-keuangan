@extends('template.app')
@section('title', 'LPJ TU')
@section('content')
    <div class="page-content">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('message'))
            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-white"><i class='bx bxs-message-square-x'></i></div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">Error</h6>
                        <div class="text-white">{{ session('message') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <h5>LPJ TU</h5>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('lpj_tu.store') }}" id="formBpkb">
                    @csrf

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">SKPD</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput" type="text"
                                placeholder="Tidak perlu diisi, otomatis" value="{{ $kd_skpd }}" disabled>
                            <input type="hidden" name="kd_skpd" value="{{ $kd_skpd }}">
                        </div>
                        <label class="col-sm-2 col-form-label">Nama SKPD</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput" type="text" name="nm_skpd" value="{{ $nm_skpd }}" readonly>
                            <input type="hidden" name="nm_skpd" value="{{ $nm_skpd }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor LPJ</label>
                        <div class="col-sm-4">
                            <input class="form-control " type="text" name="no_lpj"
                                placeholder="Nomor LPJ" value="{{ old('no_lpj') }}">
                        </div>
                        <label class="col-sm-2 col-form-label">Tanggal LPJ</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tgl_lpj') is-invalid @enderror" type="date"
                                name="tgl_lpj"
                                value="{{ old('tgl_lpj') }}">
                            @error('tgl_lpj')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">No. SP2D</label>
                        <div class="col-sm-4">
                            <select class="form-select @error('no_sp2d') is-invalid @enderror"
                            name="no_sp2d" id="no_sp2d" style="width: 100%">
                            </select>
                         </div>
                        <label class="col-sm-2 col-form-label">Tanggal SP2D</label>
                        <div class="col-sm-4">
                            <input name="tgl_sp2d" id="tgl_sp2d" class="form-control readonlyInput" type="date" readonly>
                        </div>
                    </div>


                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" name="keterangan"
                                placeholder="Keterangan">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 text-end">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a href="{{ route('lpj_tu.index') }}" class="btn btn-warning">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="page-content">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('message'))
            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-white"><i class='bx bxs-message-square-x'></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">Error</h6>
                        <div class="text-white">{{ session('message') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card shadow-sm p-3 rounded" style="background-color: #f8f9fa; border-radius: 50px 50px; margin-bottom: -1rem;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-bold text-primary">Detail LPJ</h5>
            </div>
        </div>
        <div class="card">

            <div class="card-body">
                <div class="card-body">
                    <div class="table-responsive">

                        <table class="table align-middle mb-0" id="tabelPotongan" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>SKPD</th>
                                    <th>No Bukti</th>
                                    <th>Sub Kegiatan</th>
                                    <th>Rekening</th>
                                    <th>Nama Rekening</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody id="tabelPotonganBody">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>

                    </div>
                    <p id="totalBelanja" name="totalBelanja" style="text-align: right; margin-top: 10px; font-size: 16px; font-weight: bold;">
                        <strong>Total :</strong> Rp 0
                    </p>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('js')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function getTableData() {
    let tableData = [];
    let rows = document.querySelectorAll("#tabelPotongan tbody tr");

    rows.forEach(row => {
        let cells = row.querySelectorAll("td");
        let jenis_beban = row.getAttribute('data-jenis-beban');
        let no_sp2d = row.getAttribute('data-no-sp2d');
        tableData.push({
            kd_skpd: cells[0].textContent,
            no_bukti: cells[1].textContent,
            kd_sub_kegiatan: cells[2].textContent,
            kd_rek6: cells[3].textContent,
            nm_rek6: cells[4].textContent,
            nilai: cells[5].textContent,
            jenis_beban: jenis_beban,
            no_sp2d: no_sp2d
        });
    });

    return tableData;
}

document.getElementById("formBpkb").addEventListener("submit", function(e) {
    e.preventDefault();

    // Get table data
    let potonganData = getTableData();
    console.log("Potongan Data:", potonganData);
    if (potonganData.length === 0) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Minimal harus ada satu data!"
        });
        return;
    }

    // Create FormData object
    let formData = new FormData(this);
    formData.append('potongan_data', JSON.stringify(potonganData));

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Submit form with fetch
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(json => Promise.reject(json));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: data.message
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = data.redirect;
                }
            });
        } else {
            throw new Error(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: error.message || 'Terjadi kesalahan pada server'
        });
    });
});


    $(document).ready(function() {

        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

        $('#no_sp2d').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        minimumInputLength: 0,
        ajax: {
            url: "{{ route('lpj_tu.get_nosp2d') }}",
            dataType: 'json',
            type: "POST",
            delay: 250, // Menambahkan delay untuk mengurangi beban server
            data: function(params) {
                return { q: $.trim(params.term) };
            },
            processResults: function(data) {
                return { results: data.map(item => ({
                            id: item.id,
                            text: item.text,
                            tgl_bukti: item.tgl_bukti,
                            no_bukti: item.no_bukti
                        }))
                     };
            }
        }
        });

        $('#no_sp2d').on('select2:select', function(e) {
            var data = e.params.data;
            $('#tgl_sp2d').val(data.tgl_bukti);

            loadDetailData(data.no_bukti);
        });


        function loadDetailData(no_bukti) {
        // Show loading indicator
        $('#tabelPotonganBody').html('<tr><td colspan="6" class="text-center">Loading data...</td></tr>');

        // Fetch data from server
        $.ajax({
            url: '{{ route("lpj_tu.getDataByNoBukti") }}',  // You'll need to create this route
            type: 'GET',
            data: {
                no_bukti: no_bukti
            },
            dataType: 'json',
            success: function(response) {
                // Clear table first
                $('#tabelPotonganBody').empty();

                if (response.data && response.data.length > 0) {
                    var totalNilai = 0;

                    // Populate table with data
                    $.each(response.data, function(index, item) {
                        let nilaiValue = parseFloat(item.nilai || 0);
                        totalNilai += nilaiValue;

                        $('#tabelPotonganBody').append(`
                            <tr data-jenis-beban="${item.jenis_beban || ''}" data-no-sp2d="${item.no_sp2d || ''}">
                                <td>${item.kd_skpd || ''}</td>
                                <td>${item.no_bukti || ''}</td>
                                <td>${item.kd_sub_kegiatan || ''}</td>
                                <td>${item.kd_rek6 || ''}</td>
                                <td>${item.nm_rek6 || ''}</td>
                                <td class="text-end">${formatRupiah(nilaiValue)}</td>
                            </tr>
                        `);
                    });

                    // Update total
                    $('#totalBelanja').html('<strong>Total :</strong> ' + formatRupiah(totalNilai));
                } else {
                    $('#tabelPotonganBody').html('<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>');
                    $('#totalBelanja').html('<strong>Total :</strong> Rp 0');
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                $('#tabelPotonganBody').html('<tr><td colspan="6" class="text-center">Error loading data</td></tr>');

                Swal.fire({
                    title: 'Error',
                    text: 'Terjadi kesalahan saat memuat data',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    // Function to format currency
    function formatRupiah(angka) {
        // Remove any non-numeric characters
        angka = String(angka).replace(/[^,\d]/g, '');

        // Handle decimal part if it exists
        let split = angka.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        // Add decimal part back if it exists
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;

        // Add 'Rp ' prefix
        return 'Rp ' + rupiah;
    }




    });

    // Function to clear date inputs
    function kosongkanTanggal() {
        $('#tgl_awal').val('');
        $('#tgl_akhir').val('');
        $('#tabelPotonganBody').empty();
        $('#totalBelanja').html('<strong>Total :</strong> Rp 0');
    }
</script>
@endpush
