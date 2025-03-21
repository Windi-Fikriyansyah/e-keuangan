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
                <form method="POST" action="{{ route('strpot.update', $strpot->no_bukti) }}" id="formBpkb">
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
                        <button class="btn btn-primary" type="submit">Simpan</button>
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
                                <tr data-id="{{ $potongan->id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $potongan->kd_rek6 }}</td>
                                        <td>{{ $potongan->nm_rek6 }}</td>
                                        <td>{{ $potongan->rekanan }}</td>
                                        <td>{{ $potongan->npwp }}</td>
                                        <td>{{ number_format($potongan->nilai, 0, ',', '.') }}</td>
                                        <td>
                                            <input type="text" class="form-control ntpn-input"
                                                style="min-width: 200px; max-width: 100%; text-align: center; padding: 5px;"
                                                value="{{ $potongan->ntpn ?? '' }}">
                                        </td>
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
document.addEventListener("DOMContentLoaded", function() {
        updateTotal(); // Panggil fungsi updateTotal setelah tabel selesai dimuat
    });

const initialPotonganData = @json($potonganDetails);
function getTableData() {
    let tableData = [];
    let table = document.getElementById("tabelPotongan").getElementsByTagName("tbody")[0];
    let rows = table.getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
        let cells = rows[i].getElementsByTagName("td");
        let nilai = cells[5].innerText; // Ambil nilai langsung (tanpa format)
        let ntpn = cells[6].querySelector('input').value; // Ambil nilai NTPN dari input field
        let id = rows[i].getAttribute('data-id'); // Ambil id dari atribut data-id

        tableData.push({
            id: id, // Sertakan id dalam data
            kdrekpot: cells[1].innerText, // Kolom ke-2 (indeks 1)
            nmrekpot: cells[2].innerText, // Kolom ke-3 (indeks 2)
            nmrekan: cells[3].innerText, // Kolom ke-4 (indeks 3)
            npwp: cells[4].innerText, // Kolom ke-5 (indeks 4)
            nilai: parseFloat(nilai), // Konversi nilai ke angka (float)
            ntpn: ntpn, // NTPN dari input field
            ebilling: cells[7].innerText // Kolom ke-8 (indeks 7)
        });
    }
    return tableData;
}
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

    // Add this for debugging
    console.log("Sending data:", {
        potongan_data: JSON.stringify(potonganData)
    });

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
        console.log("Response status:", response.status);

        // Enhanced error handling to get more detailed information
        if (!response.ok) {
            return response.text().then(text => {
                console.error("Server response:", text);
                throw new Error(`Server responded with status ${response.status}: ${text}`);
            });
        }

        const contentType = response.headers.get('content-type');
        console.log("Content-Type:", contentType);

        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            return response.text().then(text => {
                console.error("Non-JSON response:", text);
                throw new Error('Response is not in JSON format');
            });
        }
    })
    .then(data => {
        console.log("Processed data:", data);

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
        console.error('Error details:', error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: error.message || 'Terjadi kesalahan pada server'
        });
    });
});


function updateTotal() {
    let tabel = document.getElementById("tabelPotongan").getElementsByTagName("tbody")[0];
    let rows = tabel.getElementsByTagName("tr");
    let total = 0;

    for (let i = 0; i < rows.length; i++) {
        let nilaiCell = rows[i].cells[5];
        let nilaiText = nilaiCell.innerHTML.replace(/\D/g, ''); // Ambil angka saja
        console.log("Nilai di baris " + (i + 1) + ": " + nilaiText); // Debugging
        total += parseFloat(nilaiText) || 0;
    }

     // Debugging

    let formattedTotal = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(total);

    document.getElementById("totalBelanja").innerHTML = `<strong>Total :</strong> ${formattedTotal}`;
}
</script>
@endpush
