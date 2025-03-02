@extends('template.app')
@section('title', 'Terima Potongan Pajak')
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

        <h5>Potongan</h5>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('trmpot.store') }}" id="formBpkb">
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
                            <input class="form-control" type="text" name="nm_skpd" value="{{ $nm_skpd }}" readonly>
                            <input type="hidden" name="nm_skpd" value="{{ $nm_skpd }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Bukti Terima</label>
                        <div class="col-sm-4">
                            <input class="form-control readonlyInput" type="text" name="no_bukti"
                                placeholder="Tidak perlu diisi, otomatis" value="{{ $newNoBukti }}" readonly>
                        </div>
                        <label class="col-sm-2 col-form-label">Tanggal Bukti</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tgl_bukti') is-invalid @enderror" type="date"
                                name="tgl_bukti"
                                value="{{ old('tgl_bukti', date('Y-m-d')) }}">
                            @error('tgl_bukti')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">No Transaksi</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('id_transout') is-invalid @enderror"
                                name="id_trhtransout" id="id_transout" style="width: 100%">
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tanggal Transaksi</label>
                        <div class="col-sm-4">
                            <input name="tgl_transaksi" placeholder="YYYY/MM/DD" id="tgl_transaksi" class="form-control" type="text" disabled>
                        </div>

                        <div class="col-sm-4">

                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">No SP2D</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="no_sp2d" name="no_sp2d" readonly>
                         </div>
                        <label class="col-sm-2 col-form-label">Pembayaran</label>
                        <div class="col-sm-4">
                            <select class="form-select @error('pay') is-invalid @enderror"
                                name="pay" id="pay" style="width: 100%">
                                <option value="">Silahkan Pilih...</option>
                                <option value="BANK">BANK</option>
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

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Kode Rekening</label>
                        <div class="col-sm-4">
                            <select class="form-select @error('kd_rek6') is-invalid @enderror"
                            name="kd_rek6" id="kd_rek6" style="width: 100%">
                            </select>
                         </div>
                        <label class="col-sm-2 col-form-label">Nama Rekening</label>
                        <div class="col-sm-4">
                            <input name="nm_rek6" placeholder="Nama Rekening" id="nm_rek6" class="form-control" type="text" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Rekanan</label>
                        <div class="col-sm-4">
                            <select class="form-select @error('nmrekan') is-invalid @enderror"
                            name="nmrekan" id="nmrekan" style="width: 100%">
                            </select>
                         </div>
                        <label class="col-sm-2 col-form-label">Pimpinan</label>
                        <div class="col-sm-4">
                            <input name="pimpinan" placeholder="Nama Pimpinan" id="pimpinan" class="form-control" type="text" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Beban</label>
                        <div class="col-sm-4">
                            <select class="form-select @error('beban') is-invalid @enderror"
                            name="beban" id="beban" style="width: 100%">
                            <option value="">Silahkan Pilih</option>
                            <option value="UP">UP</option>
                            <option value="GU">GU</option>
                            <option value="TU">TU</option>
                            <option value="LS GAJI">LS GAJI</option>
                            <option value="LS PPKD">LS PPKD</option>
                            <option value="LS Barang & Jasa">LS Barang & Jasa</option>
                            </select>
                         </div>
                        <label class="col-sm-2 col-form-label">NPWP</label>
                        <div class="col-sm-4">
                            <input name="npwp" placeholder="Nama NPWP" id="npwp" class="form-control" type="text" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Alamat</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat"
                                placeholder="Alamat" readonly>{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('ket') is-invalid @enderror" name="ket"
                                placeholder="Keterangan">{{ old('ket') }}</textarea>
                            @error('ket')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Rekening Potongan</label>
                        <div class="col-sm-4">
                            <select class="form-select @error('kdrekpot') is-invalid @enderror"
                            name="kdrekpot" id="kdrekpot" style="width: 100%">
                            </select>
                         </div>
                        <label class="col-sm-2 col-form-label">Nama Rekening</label>
                        <div class="col-sm-4">
                            <input name="nmrekpot" placeholder="Nama Rekening" id="nmrekpot" class="form-control" type="text" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">No Billing</label>
                        <div class="col-sm-4">
                            <input name="ebilling" placeholder="No Billing" id="ebilling" class="form-control" type="text" >
                         </div>
                        <label class="col-sm-2 col-form-label">Nilai</label>
                        <div class="col-sm-4">
                            <input id="nilai" name="nilai" oninput="formatRupiah(this);" class="form-control" type="text">
                        </div>
                    </div>
                    <button class="btn btn-primary" type="button" id="btnTambahPotongan">Tambah Potongan</button>


                    <div class="mb-3 text-end">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a href="{{ route('trmpot.index') }}" class="btn btn-warning">Kembali</a>
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
                                    <th>Rek Trans</th>
                                    <th>Rekening</th>
                                    <th>Nama Rekening</th>
                                    <th>Rekanan</th>
                                    <th>NPWP</th>
                                    <th>No Billing</th>
                                    <th>Nilai</th>
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

@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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

// Modify the form submission
document.getElementById("formBpkb").addEventListener("submit", function(e) {
    e.preventDefault();

    // Get table data
    let potonganData = getTableData();

    if (potonganData.length === 0) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Minimal harus ada satu potongan!"
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

document.getElementById("btnTambahPotongan").addEventListener("click", function() {
        let kd_rek6 = document.getElementById("kd_rek6").value;
        let kdrekpot = document.getElementById("kdrekpot").value;
        let nmrekpot = document.getElementById("nmrekpot").value;
        let nmrekan = document.getElementById("nmrekan").value;
        let npwp = document.getElementById("npwp").value;
        let ebilling = document.getElementById("ebilling").value;
        let nilai = document.getElementById("nilai").value;

        // Validasi input
        if (!kd_rek6 || !kdrekpot || !nmrekpot || !nmrekan || !npwp || !nilai) {
            Swal.fire({
                icon: "warning",
                title: "Oops...",
                text: "Harap isi semua kolom sebelum menambahkan potongan!",
            });
            return;
        }

        // Tambahkan data ke tabel
        let tabel = document.getElementById("tabelPotongan").getElementsByTagName("tbody")[0];
        let row = tabel.insertRow();
        let cellrektrans = row.insertCell(0);
        let cellRekening = row.insertCell(1);
        let cellNamaRekening = row.insertCell(2);
        let cellRekanan = row.insertCell(3);
        let cellNpwp = row.insertCell(4);
        let cellBilling = row.insertCell(5);
        let cellNilai = row.insertCell(6);
        let cellAksi = row.insertCell(7);

        let rowCount = tabel.rows.length;
        cellrektrans.innerHTML = kd_rek6;
        cellRekening.innerHTML = kdrekpot;
        cellNamaRekening.innerHTML = nmrekpot;
        cellRekanan.innerHTML = nmrekan;
        cellNpwp.innerHTML = npwp;
        cellBilling.innerHTML = ebilling;
        cellNilai.innerHTML = nilai;
        cellAksi.innerHTML = `<button class="btn btn-danger btn-sm" onclick="hapusRow(this)">Hapus</button>`;


        document.getElementById("ebilling").value = "";
        document.getElementById("nilai").value = "";

        updateTotal();
        Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: "Potongan berhasil ditambahkan.",
        });
    });

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

    function hapusRow(btn) {
        let row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
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

    function hitungSisa(totalId, realisasiId, sisaId) {
        let total = parseFloat(document.getElementById(totalId).value.replace(/\D/g, "")) || 0;
        let realisasi = parseFloat(document.getElementById(realisasiId).value.replace(/\D/g, "")) || 0;
        let sisa = total - realisasi;

        document.getElementById(sisaId).value = new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0
        }).format(sisa).replace("Rp", "").trim();
    }
    $(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
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
                            tgl_bukti: item.tgl_bukti,
                            no_sp2d : item.no_sp2d
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
        ajax: {
            url: "{{ route('trmpot.getsubkegiatan') }}",
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
                            nm_sub_kegiatan: item.nm_sub_kegiatan
                        }))
                     };
            }
        }
    });

    $('#kd_rek6').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        minimumInputLength: 0,
        ajax: {
            url: "{{ route('trmpot.get-rekening') }}",
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
                            nm_rek6: item.nm_rek6
                        }))
                     };
            }
        }
    });

    $('#nmrekan').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        minimumInputLength: 0,
        ajax: {
            url: "{{ route('trmpot.getrekan') }}",
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
                            pimpinan: item.pimpinan,
                            npwp: item.npwp,
                            alamat: item.alamat
                        }))
                     };
            }
        }
    });

    $('#kdrekpot').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        minimumInputLength: 0,
        ajax: {
            url: "{{ route('trmpot.getmspot') }}",
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
                            nmrekpot: item.nmrekpot,
                        }))
                     };
            }
        }
    });

    $('#id_transout').on('select2:select', function(e) {
            var data = e.params.data;
            $('#tgl_transaksi').val(data.tgl_bukti);
            $('#no_sp2d').val(data.no_sp2d); // Set input tanggal transaksi
        });

    $('#kd_sub_kegiatan').on('select2:select', function(e) {
            var data = e.params.data;
            console.log(data);
            $('#nm_sub_kegiatan').val(data.nm_sub_kegiatan);
        });

    $('#kd_rek6').on('select2:select', function(e) {
            var data = e.params.data;
            $('#nm_rek6').val(data.nm_rek6);
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
