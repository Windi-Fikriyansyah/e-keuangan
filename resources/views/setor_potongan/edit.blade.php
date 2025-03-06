@extends('template.app')
@section('title', 'Setor Potongan Pajak')
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

        <h5>Setor Potongan Pajak</h5>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('strpot.store') }}" id="formBpkb">
                    @csrf



                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Bukti</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput" type="text" name="no_bukti"
                                placeholder="Tidak perlu diisi, otomatis" value="{{ $strpot->no_bukti }}" readonly>
                        </div>
                        <label class="col-sm-2 col-form-label">Tanggal</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tgl_bukti') is-invalid @enderror" type="date"
                                name="tgl_bukti"
                                value="{{ $strpot->tgl_bukti }}" disabled>
                            @error('tgl_bukti')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>



                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">No Terima</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('no_terima') is-invalid @enderror"
                            name="no_terima" id="no_terima" style="width: 100%" disabled>
                            <option value="">Silahkan Pilih</option>
                            @if(isset($strpot))
                                    <option value="{{ $strpot->no_terima }}" selected>
                                        {{ $strpot->no_terima }}
                                    </option>
                            @endif
                            </select>
                         </div>

                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">No Sp2d</label>
                        <div class="col-sm-4">
                            <input name="no_sp2d" id="no_sp2d" class="form-control" value="{{ $strpot->no_sp2d }}" type="text" disabled>
                         </div>
                        <label class="col-sm-2 col-form-label">Pembayaran</label>
                        <div class="col-sm-4">
                            <input name="pay" id="pay" class="form-control" type="text" value="{{ $strpot->pay }}" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Kode Kegiatan</label>
                        <div class="col-sm-4">
                            <input name="kd_sub_kegiatan" id="kd_sub_kegiatan" value="{{ $strpot->kd_sub_kegiatan }}" class="form-control" type="text" disabled>
                         </div>
                        <label class="col-sm-2 col-form-label">Nama Kegiatan</label>
                        <div class="col-sm-4">
                            <input name="nm_sub_kegiatan" id="nm_sub_kegiatan" value="{{ $strpot->nm_sub_kegiatan }}" class="form-control" type="text" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Kode Rekening</label>
                        <div class="col-sm-4">
                            <input name="kd_rek6" id="kd_rekening" class="form-control" value="{{ $strpot->kd_rek6 }}" type="text" disabled>
                         </div>
                        <label class="col-sm-2 col-form-label">Nama Rekening</label>
                        <div class="col-sm-4">
                            <input name="nm_rek6" id="nm_rekening" class="form-control" value="{{ $strpot->nm_rek6 }}" type="text" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Rekanan</label>
                        <div class="col-sm-4">
                            <input name="nmrekan" id="nmrekan" class="form-control" value="{{ $strpot->nmrekan }}" type="text" disabled>
                         </div>
                        <label class="col-sm-2 col-form-label">Pimpinan</label>
                        <div class="col-sm-4">
                            <input name="pimpinan" id="pimpinan" class="form-control" type="text" value="{{ $strpot->pimpinan }}" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Beban</label>
                        <div class="col-sm-4">
                            <input name="beban" id="beban" class="form-control" type="text" value="{{ $strpot->beban }}" disabled>
                         </div>
                        <label class="col-sm-2 col-form-label">NPWP</label>
                        <div class="col-sm-4">
                            <input name="npwp" id="npwp" class="form-control" value="{{ $strpot->npwp }}" type="text" disabled>
                        </div>
                    </div>
                    <input name="id_trhtransout" id="id_trhtransout" class="form-control" type="hidden" disabled>


                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Alamat</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('alamat') is-invalid @enderror" name="alamat" id="alamat"
                                placeholder="Alamat" disabled >{{ old('alamat', $strpot->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('ket') is-invalid @enderror" name="ket" id="ket"
                                placeholder="Keterangan" disabled >{{ old('ket', $strpot->ket) }}</textarea>
                            @error('ket')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>



                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">SKPD</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput" type="text"
                                placeholder="Tidak perlu diisi, otomatis" value="{{ $strpot->kd_skpd }}" disabled>

                        </div>
                        <label class="col-sm-2 col-form-label">Nama SKPD</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" name="nm_skpd" value="{{ $strpot->nm_skpd }}" disabled>

                        </div>
                    </div>



                    <div class="mb-3 text-end">
                        <a href="{{ route('strpot.index') }}" class="btn btn-warning">Kembali</a>
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
                <h5 class="mb-0 fw-bold text-primary">List Potongan</h5>
            </div>
        </div>
        <div class="card">

            <div class="card-body">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="tabelPotongan" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Rekening</th>
                                    <th>Nama Rekening</th>
                                    <th>Rekanan</th>
                                    <th>NPWP</th>
                                    <th>Nilai</th>
                                    <th>NTPN</th>
                                    <th>No Billing</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($potonganDetails as $index => $potongan)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $potongan->kd_rek6 }}</td>
                                        <td>{{ $potongan->nm_rek6 }}</td>
                                        <td>{{ $potongan->rekanan }}</td>
                                        <td>{{ $potongan->npwp }}</td>
                                        <td>{{ number_format($potongan->nilai, 0, ',', '.') }}</td>
                                        <td>{{ $potongan->ntpn }}</td>
                                        <td>{{ $potongan->ebilling }}</td>

                                    </tr>
                                @endforeach
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

// Inside your $(document).ready function:
// Modify the form submit handler to properly format numeric values

document.addEventListener("DOMContentLoaded", function () {
    function hitungTotalBelanja() {
        let total = 0;
        document.querySelectorAll("#tabelPotongan tbody tr").forEach(row => {
            let nilaiText = row.cells[5].innerText.replace(/\./g, '').replace(',', '.'); // Hapus format ribuan
            let nilai = parseFloat(nilaiText) || 0;
            total += nilai;
        });

        // Format hasil ke Rupiah
        let formattedTotal = new Intl.NumberFormat("id-ID").format(total);

        // Tampilkan hasil di elemen totalBelanja
        document.getElementById("totalBelanja").innerHTML = `<strong>Total :</strong> Rp ${formattedTotal}`;
    }

    hitungTotalBelanja(); // Panggil saat halaman dimuat
});

$('#formBpkb').on('submit', function(e) {
    e.preventDefault();

    // Function to clean numeric values
    function cleanNumericValue(value) {
        // Hapus semua karakter kecuali angka
        return value.replace(/[^0-9]/g, '');
    }

    let potonganData = [];
    $('#tabelPotongan tbody tr').each(function() {
        let row = $(this);
        let nilai = row.find('td:eq(5)').text(); // Nilai column

        potonganData.push({
            kdrekpot: row.find('td:eq(1)').text().trim(),  // Remove any whitespace
            nmrekpot: row.find('td:eq(2)').text().trim(),
            nmrekan: row.find('td:eq(3)').text().trim(),
            npwp: row.find('td:eq(4)').text().trim(),
            nilai: cleanNumericValue(nilai),  // Clean the numeric value
            ntpn: row.find('td:eq(6)').text().trim(),
            ebilling: row.find('td:eq(7)').text().trim(),
            id_trdtrmpot: row.data('id-trdtrmpot')
        });
    });

    // Add the potongan data to the form data
    let formData = new FormData(this);
    formData.append('potongan_data', JSON.stringify(potonganData));

    // Submit via AJAX
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message
                }).then((result) => {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.message
                });
            }
        },
        error: function(xhr) {
            let message = 'Terjadi kesalahan saat menyimpan data.';
            if (xhr.responseJSON) {
                message = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: message
            });
        }
    });
});


$(document).ready(function() {

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(number);
    }
    // Inisialisasi Select2 untuk pemilihan nomor terima
    $('#no_terima').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...", // Tambahkan agar placeholder tetap terlihat
        ajax: {
            url: "{{ route('strpot.getnoterima') }}",
            dataType: 'json',
            type: "POST",
            delay: 250,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Tambahkan CSRF token
            },
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    })
                };
            },
            cache: true
        }
    });

    // Event ketika no_terima dipilih
    $('#no_terima').change(function() {
        let noTerima = $(this).val();

        if (noTerima) {
            $.ajax({
                url: "{{ route('strpot.getpotongandata') }}",
                type: "POST",
                data: { no_terima: noTerima },  // Perbaiki agar sesuai dengan Controller
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        let data = response.data;

                        // Isi input berdasarkan data yang diterima
                        $('#id_trhtransout').val(data.id_trhtransout || '');
                        $('#ntpn').val(data.ntpn || '');
                        $('#no_sp2d').val(data.no_sp2d || '');
                        $('#pay').val(data.pay || '');
                        $('#kd_sub_kegiatan').val(data.kd_sub_kegiatan || '');
                        $('#nm_sub_kegiatan').val(data.nm_sub_kegiatan || '');
                        $('#kd_rekening').val(data.kd_rekening || '');
                        $('#nm_rekening').val(data.nm_rekening || '');
                        $('#nmrekan').val(data.nmrekan || '');
                        $('#pimpinan').val(data.pimpinan || '');
                        $('#beban').val(data.beban || '');
                        $('#npwp').val(data.npwp || '');
                        $('#alamat').val(data.alamat || '');

                        // Hapus data lama dalam tabel
                        $('#tabelPotongan tbody').empty();
                        let total = 0;
                        // Tambahkan data ke tabel dari trdtrmpot berdasarkan no_bukti
                        $.each(response.trdtrmpot, function(index, row) {
                            total += parseFloat(row.nilai || 0);

                            $('#tabelPotongan tbody').append(`
                                <tr data-id-trdtrmpot="${row.id}">
                                    <td>${index + 1}</td>
                                    <td>${row.kd_rek6}</td>
                                    <td>${row.nm_rek6}</td>
                                    <td>${row.rekanan}</td>
                                    <td>${row.npwp}</td>
                                   <td style="text-align: right;">${formatRupiah(row.nilai)}</td>
                                    <td>${row.ntpn}</td>
                                    <td>${row.ebilling}</td>
                                </tr>
                            `);
                        });
                        $('#totalBelanja').html(`<strong>Total : </strong>${formatRupiah(total)}`);
                    } else {
                        alert('Data tidak ditemukan!');
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('Terjadi kesalahan saat mengambil data.');
                }
            });
        }
    });
});

</script>
@endpush
