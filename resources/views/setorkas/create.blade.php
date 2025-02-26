@extends('template.app')
@section('title', 'Setor Sisa Kas')
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

        <h5>Setor Sisa Kas</h5>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('setorkas.store') }}" id="formBpkb">
                    @csrf

                    <input type="hidden" name="details" id="hiddenDetails">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">SKPD</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput" type="text"
                                placeholder="Tidak perlu diisi, otomatis" value="{{ $kd_skpd }}" disabled>
                            <input type="hidden" name="kd_skpd" value="{{ $kd_skpd }}">
                        </div>
                        <label class="col-sm-2 col-form-label">Nama SKPD</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" name="nm_skpd" value="{{ $nm_skpd }}" readonly>
                            <input type="hidden" name="nm_skpd" value="{{ $nm_skpd }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Kas</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput" type="text" name="no_kas"
                                placeholder="Tidak perlu diisi, otomatis" value="{{ $newNoBukti }}" readonly>
                        </div>
                        <label class="col-sm-2 col-form-label">Tanggal Kas</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tgl_kas') is-invalid @enderror" type="date"
                            id="tgl_kas"
                            name="tgl_kas"
                                value="{{ old('tgl_kas', date('Y-m-d')) }}">
                            @error('tgl_kas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Uraian</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('Keterangan') is-invalid @enderror" name="keterangan"></textarea>
                            @error('Keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Pembayaran</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('bank') is-invalid @enderror"
                                name="bank" id="bank" style="width: 100%">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="BNK">BANK</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Jenis Transaksi</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('jns_trans') is-invalid @enderror"
                                name="jns_trans" id="jns_trans" style="width: 100%">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1">Belanja</option>
                                <option value="2">Rekening Kas</option>
                            </select>
                        </div>
                    </div>




                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">SP2D</label>
                        <div class="col-sm-4">
                            <select class="form-select @error('no_sp2d') is-invalid @enderror"
                            name="no_sp2d" id="no_sp2d" style="width: 100%">
                            </select>
                         </div>
                        <label class="col-sm-2 col-form-label">Jenis CP</label>
                        <div class="col-sm-4">
                            <select class="form-select @error('jns_cp') is-invalid @enderror"
                            name="jns_cp" id="jns_cp" style="width: 100%">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            <option value="1">UP/GU</option>
                            <option value="2">TU</option>
                            <option value="3">GAJI</option>
                            <option value="4">Barang & Jasa</option>
                            </select>
                        </div>
                    </div>


                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Kode Kegiatan</label>
                        <div class="col-sm-4">
                            <select class="form-select @error('kd_sub_kegiatan') is-invalid @enderror"
                            name="kd_sub_kegiatan" id="kd_sub_kegiatan" style="width: 100%">
                            </select>
                         </div>
                        <label class="col-sm-2 col-form-label">Nama Kegiatan</label>
                        <div class="col-sm-4">
                            <input name="nm_sub_kegiatan" placeholder="Nama Kegiatan" id="nm_sub_kegiatan" class="form-control" type="text" readonly>
                        </div>
                    </div>

                    <div class="mb-3 text-end">
                        <input type="hidden" name="total_belanja" id="hiddenTotalBelanja">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a href="{{ route('setorkas.index') }}" class="btn btn-warning">Kembali</a>
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
                <h5 class="mb-0 fw-bold text-primary">Detail STS</h5>

                <div class="d-flex gap-2">

                    <button class="btn btn-success d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#inputKegiatanModal">
                        Tambah
                    </button>

                </div>
            </div>
        </div>
        <div class="card">

            <div class="card-body">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="pajak" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Nomor Rekening</th>
                                    <th>Nama Rekening</th>
                                    <th>Rupiah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
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

    <div class="modal fade" id="inputKegiatanModal" tabindex="-1" aria-labelledby="inputKegiatanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inputKegiatanModalLabel">Input Rincian Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formInputKegiatan">
                        <div class="mb-3 d-flex align-items-center">
                            <label for="kd_rek" class="form-label me-5" style="min-width: 120px;">Kode Rekening</label>
                            <select
                                id="kd_rek"
                                name="kd_rek"
                                class="form-select me-2 custom-border select2 @error('kd_rek') is-invalid @enderror"
                                style="width: 100%;"
                                data-placeholder="Pilih Rekening"
                                required
                            >
                                <option></option> <!-- Agar placeholder muncul -->
                                <!-- Options will be populated dynamically -->
                            </select>

                        </div>


                        <div class="mb-3 d-flex align-items-center">
                            <label for="nm_rek" class="form-label me-5" style="min-width: 120px;">Nama Rekening</label>
                            <input type="text" class="form-control" id="nm_rek" name="nm_rek" required readonly>

                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <label for="sisa" class="form-label me-5" style="min-width: 120px;">Sisa Nilai</label>
                            <input type="text" class="form-control" id="sisa" name="sisa" required disabled>

                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <label for="total" class="form-label me-5" style="min-width: 120px;">Sisa Kas Bank</label>
                            <input type="text" class="form-control" id="total" name="total" required disabled>

                        </div>

                        <div class="mb-3 d-flex align-items-center">
                            <label for="nilai" class="form-label me-5" style="min-width: 120px;">Nilai</label>
                            <input type="text" class="form-control" id="nilai" name="nilai" oninput="formatRupiah(this);" required>

                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('js')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>



function hitungTotalBelanja() {
    let totalBelanja = 0;

    // Loop through the dataSementara array to sum the "nilai" field
    dataSementara.forEach(item => {
        totalBelanja += item.nilai || 0;
    });

    // Format the total as currency (Rupiah)
    let formattedTotal = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0
    }).format(totalBelanja);

    // Update the "Total Belanja" field
    document.getElementById("totalBelanja").innerHTML = `<strong>Total belanja:</strong> ${formattedTotal}`;
    document.getElementById("hiddenTotalBelanja").value = totalBelanja;

}



      let dataSementara = [];
    function updateTable() {
    let tbody = $("#pajak tbody");
    tbody.empty(); // Kosongkan isi tabel sebelum memperbarui

    dataSementara.forEach((item, index) => {
        let row = `
            <tr>
                <td>${item.kd_rek}</td>
                <td>${item.nm_rek}</td>
                <td>${item.nilai}</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="hapusData(${index})"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });

    hitungTotalBelanja();
}

function prepareSubmit() {
    // Ensure dataSementara exists and has items
    if (!dataSementara || dataSementara.length === 0) {
        alert('Harap tambahkan minimal satu kegiatan');
        return false;
    }



    // Convert dataSementara to a format suitable for Laravel
    let details = dataSementara.map(item => ({
        kd_rek: item.kd_rek,
        nm_rek: item.nm_rek,
        nilai: item.nilai,
    }));

    // Set the hidden input value
    document.getElementById('hiddenDetails').value = JSON.stringify(details);

    // Return true to allow form submission
    return true;
}

document.getElementById('formBpkb').onsubmit = function(event) {
    return prepareSubmit();
};

function toNumber(value) {
    return parseFloat(value.replace(/[^\d,-]/g, '').replace(',', '.')) || 0;
}

$("#formInputKegiatan").submit(function (e) {
        e.preventDefault(); // Mencegah form submit secara default
        console.log("Jalan");



        let kd_rek = $("#kd_rek").val();
        let nm_rek = $("#nm_rek").val();
        let nilai = toNumber($("#nilai").val());
        let sisa = toNumber($("#sisa").val());
        let sisaKasBank = toNumber($("#total").val());
        let no_sp2d = $("#no_sp2d").val();

        console.log("Nilai Input:", nilai);
    console.log("Sisa:", sisa);
    console.log("Sisa Kas Bank:", sisaKasBank);
    console.log("Nomor SP2D:", no_sp2d);

    // Pastikan no_sp2d tidak null atau undefined sebelum validasi
    if (no_sp2d.includes("LS")) {
        console.log("SP2D mengandung LS");
        if (nilai > sisa) {
            console.log("Gagal: Nilai melebihi Sisa");
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Nilai tidak boleh lebih dari Sisa Nilai untuk SP2D LS!'
            });
            return;
        }
    }

    if (no_sp2d.includes("GU") || no_sp2d.includes("UP") || no_sp2d.includes("TU")) {
        console.log("SP2D mengandung GU, UP, atau TU");
        if (nilai > sisaKasBank) {
            console.log("Gagal: Nilai melebihi Sisa Kas Bank");
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Nilai tidak boleh lebih dari Sisa Kas Bank untuk SP2D GU, UP, atau TU!'
            });
            return;
        }
    }

        // Simpan data ke array sementara
        dataSementara.push({
            kd_rek,
            nm_rek,
            nilai,

        });

        try {
            updateTable();
        } catch (error) {
            console.error("Error in updateTable:", error);
        }


        // Tutup modal setelah simpan
        console.log("Closing modal");
        $("#inputKegiatanModal").modal("hide");

        // Reset form setelah simpan
        $("#formInputKegiatan")[0].reset();
    });



function getTableData() {
    let tableData = [];
    let table = document.getElementById("tabelPotongan").getElementsByTagName("tbody")[0];
    let rows = table.getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
        let cells = rows[i].getElementsByTagName("td");
        tableData.push({
            kd_rek6: cells[0].innerHTML,
            kdrekpot: cells[1].innerHTML,
            nmrekpot: cells[2].innerHTML,
            nmrekan: cells[3].innerHTML,
            npwp: cells[4].innerHTML,
            ebilling: cells[5].innerHTML,
            nilai: cells[6].innerHTML
        });
    }
    return tableData;
}



    function updateTotal() {
    let tabel = document.getElementById("tabelPotongan").getElementsByTagName("tbody")[0];
    let rows = tabel.getElementsByTagName("tr");
    let total = 0;

    for (let i = 0; i < rows.length; i++) {
        let nilaiCell = rows[i].cells[6]; // Index 6 adalah kolom Nilai
        let nilaiText = nilaiCell.innerHTML.replace(/\D/g, ''); // Hapus semua karakter non-digit
        total += parseFloat(nilaiText) || 0;
    }

    // Format total ke dalam format rupiah
    let formattedTotal = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(total);

    // Update text total belanja
    document.getElementById("totalBelanja").innerHTML = `<strong>Total :</strong> ${formattedTotal}`;
}

function hapusData(index) {
    dataSementara.splice(index, 1);
    updateTable();
}
function formatRupiah(input) {
        let angka = input.value.replace(/\D/g, ""); // Hanya angka
        let rupiah = new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0
        }).format(angka);
        input.value = rupiah.replace("Rp", "").trim(); // Menghilangkan "Rp" agar mudah diproses
    }



$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#inputKegiatanModal').on('show.bs.modal', function (e) {

    let tglBukti = $('input[name="tgl_kas"]').val();
    let jenisBeban = $('select[name="jns_trans"]').val();
    let nosp2d = $('select[name="no_sp2d"]').val();
    let jnscp = $('select[name="jns_cp"]').val();

    let errors = [];

    if (!tglBukti) errors.push('Tanggal Kas harus diisi.');
    if (!jenisBeban) errors.push('Jenis Transaksi harus dipilih.');
    if (!nosp2d) errors.push('Nomor SP2D harus dipilih.');
    if (!jnscp) errors.push('Jenis CP harus dipilih.');

    if (errors.length > 0) {
        e.preventDefault(); // Cegah modal terbuka
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            html: errors.join('<br>'), // Menampilkan semua error dalam satu alert
        });
    }
    });

    $('#kd_rek').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        minimumInputLength: 0,
        dropdownParent: $('#inputKegiatanModal'),
        ajax: {
            url: "{{ route('setorkas.getrekening') }}",
            dataType: 'json',
            type: "POST",
            delay: 250,
            data: function(params) {
                return {
                    q: $.trim(params.term),
                    no_sp2d: $('#no_sp2d').val(),
                    tgl_kas: $('#tgl_kas').val(),
                    kd_sub_kegiatan: $('#kd_sub_kegiatan').val(),
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        },

    });

function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}


$('#kd_rek').on('select2:select', function(e) {
    var data = e.params.data;
    var nm_rek = data.nm_rek;
    var sisa = data.sisa;
    var saldoFormatted = formatRupiah(data.saldoawal);
    var sisa = formatRupiah(data.sisa); // Format saldoawal ke Rupiah
    $('#total').val(saldoFormatted);
    $('#nm_rek').val(nm_rek);
    $('#sisa').val(sisa); // Isi input total dengan saldo dalam format Rupiah
});

    $('#id_transout').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        minimumInputLength: 0,
        ajax: {
            url: "{{ route('trmpot.getTransactions') }}",
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
                            tgl_bukti: item.tgl_bukti
                        }))
                     };
            }
        }
    });

    // $('#no_sp2d').select2({
    //     theme: "bootstrap-5",
    //     width: "100%",
    //     placeholder: "Silahkan Pilih...",
    //     minimumInputLength: 0,
    //     ajax: {
    //         url: "{{ route('trmpot.get-no_sp2d') }}",
    //         dataType: 'json',
    //         type: "POST",
    //         delay: 250, // Menambahkan delay untuk mengurangi beban server
    //         data: function(params) {
    //             return { q: $.trim(params.term) };
    //         },
    //         processResults: function(data) {
    //             return { results: data.map(item => ({
    //                         id: item.id,
    //                         text: item.text,
    //                     }))
    //                  };
    //         }
    //     }
    // });

    $('#kd_sub_kegiatan').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        minimumInputLength: 0,
        allowClear: true,
        ajax: {
            url: "{{ route('setorkas.getsubkegiatan') }}",
            dataType: 'json',
            type: "POST",
            delay: 250, // Menambahkan delay untuk mengurangi beban server
            data: function(params) {
                let no_sp2d = $('#no_sp2d').val();
                // Hanya kirim data jika no_sp2d dipilih
                if (!no_sp2d) {
                    return false; // Kirim objek kosong jika no_sp2d belum dipilih
                }
                return {
                    q: $.trim(params.term),
                    no_sp2d: no_sp2d
                };

            },
            processResults: function(data) {
                return { results: data.map(item => ({
                            id: item.id,
                            text: item.text,
                            nm_sub_kegiatan: item.nm_sub_kegiatan
                        }))
                     };
            }
        }
    });



    $('#no_sp2d').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        minimumInputLength: 0,
        allowClear: true,
        ajax: {
            url: "{{ route('setorkas.getnosp2d') }}",
            dataType: 'json',
            type: "POST",
            delay: 250, // Menambahkan delay untuk mengurangi beban server
            data: function(params) {
                let jns_trans = $('#jns_trans').val();
                // Hanya kirim data jika jns_trans dipilih
                if (!jns_trans) {
                    return false; // Kirim objek kosong jika jns_trans belum dipilih
                }
                return {
                    q: $.trim(params.term),
                    jns_trans: jns_trans
                };
            },
            processResults: function(data) {
                return { results: data.map(item => ({
                            id: item.id,
                            text: item.text,
                        }))
                     };
            }
        }
    });

    $('#jns_trans').change(function() {
        $('#no_sp2d').val(null).trigger('change'); // Kosongkan SP2D
    });

    $('#no_sp2d').change(function() {
        $('#kd_sub_kegiatan').val(null).trigger('change'); // Kosongkan SP2D
    });

    $('#no_sp2d').prop('disabled', true);
    $('#kd_sub_kegiatan').prop('disabled', true);

// Enable/disable no_sp2d based on jns_trans selection
$('#jns_trans').change(function() {
    let jns_trans = $(this).val();
    $('#no_sp2d').prop('disabled', !jns_trans);
    $('#no_sp2d').val(null).trigger('change');
});

$('#no_sp2d').change(function() {
    let no_sp2d = $(this).val();
    $('#kd_sub_kegiatan').prop('disabled', !no_sp2d);
    $('#kd_sub_kegiatan').val(null).trigger('change');
});


    $('#id_transout').on('select2:select', function(e) {
            var data = e.params.data;
            $('#tgl_transaksi').val(data.tgl_bukti); // Set input tanggal transaksi
        });

    $('#kd_sub_kegiatan').on('select2:select', function(e) {
            var data = e.params.data;
            console.log(data);
            $('#nm_sub_kegiatan').val(data.nm_sub_kegiatan);
        });



        $('#nmrekan').on('select2:select', function(e) {
            var data = e.params.data;
            $('#pimpinan').val(data.pimpinan);
            $('#npwp').val(data.npwp);
            $('#alamat').val(data.alamat);
        });

        $('#kdrekpot').on('select2:select', function(e) {
            var data = e.params.data;
            $('#nmrekpot').val(data.nmrekpot); // Set input tanggal transaksi
        });


});

</script>
@endpush
