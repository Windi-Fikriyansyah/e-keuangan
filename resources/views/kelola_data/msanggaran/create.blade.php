@extends('template.app')
@section('title', 'Tambah Anggaran')
@section('content')
    <div class="page-heading">
        <h2>{{ isset($pajak) ? 'Edit Anggaran' : 'Tambah Anggaran' }}</h2>
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
                    <div class="font-35 text-white"><i class='bx bxs-message-square-x'></i></div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">Error</h6>
                        <div class="text-white">{{ session('message') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                <form id="productForm" action="{{ isset($pajak) ? route('kelola_data.msanggaran.update', $pajak->id) : route('kelola_data.msanggaran.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($pajak))
                        @method('POST')
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kd_rek">Kode Rekening</label>
                                <input type="text" name="kd_rek" id="kd_rek" class="form-control" value="{{ old('kd_rek', $pajak->kd_rek ?? '') }}" placeholder="Input Kode Rekening">
                            </div>
                            <div class="form-group">
                                <label for="nm_rek">Nama Rekening</label>
                                <input type="text" name="nm_rek" id="nm_rek" class="form-control" value="{{ old('nm_rek', $pajak->nm_rek ?? '') }}" placeholder="Nama Rekening">
                            </div>
                            <div class="form-group">
                                <label for="anggaran_tahun">Anggaran Pertahun</label>
                                <input type="text" name="anggaran_tahun" oninput="formatRupiah(this);" id="anggaran_tahun" class="form-control" value="{{ old('anggaran_tahun', $pajak->anggaran_tahun ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="anggaran_tw1">Anggaran TW1</label>
                                <input type="text" name="anggaran_tw1" oninput="formatRupiah(this);" id="anggaran_tw1" class="form-control" value="{{ old('anggaran_tw1', $pajak->anggaran_tw1 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="anggaran_tw2">Anggaran TW2</label>
                                <input type="text" name="anggaran_tw2" oninput="formatRupiah(this);" id="anggaran_tw2" class="form-control" value="{{ old('anggaran_tw2', $pajak->anggaran_tw2 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="anggaran_tw3">Anggaran TW3</label>
                                <input type="text" name="anggaran_tw3" oninput="formatRupiah(this);" id="anggaran_tw3" class="form-control" value="{{ old('anggaran_tw3', $pajak->anggaran_tw3 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="anggaran_tw4">Anggaran TW4</label>
                                <input type="text" name="anggaran_tw4" oninput="formatRupiah(this);" id="anggaran_tw4" class="form-control" value="{{ old('anggaran_tw4', $pajak->anggaran_tw4 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek9">Rek 9</label>
                                <input type="text" name="rek9" oninput="formatRupiah(this);" id="rek9" class="form-control" value="{{ old('rek9', $pajak->rek9 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek10">Rek 10</label>
                                <input type="text" name="rek10" oninput="formatRupiah(this);" id="rek10" class="form-control" value="{{ old('rek10', $pajak->rek10 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek12">Rek 12</label>
                                <input type="text" name="rek12" oninput="formatRupiah(this);" id="rek12" class="form-control" value="{{ old('rek12', $pajak->rek12 ?? '') }}">
                            </div>



                            <div class="form-group">
                                <label for="rek12">Kode Sub Kegiatan</label>
                                <select class="form-select @error('kd_sub_kegiatan') is-invalid @enderror"
                                name="kd_sub_kegiatan" id="kd_sub_kegiatan" style="width: 100%">
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="rek12">Nama Sub Kegiatan</label>
                                <input type="text" name="nm_sub_kegiatan" readonly id="nm_sub_kegiatan" class="form-control" value="{{ old('nm_sub_kegiatan', $pajak->nm_sub_kegiatan ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="rek12">Kode Sumber Dana</label>
                                <select class="form-select @error('id_sumberdana') is-invalid @enderror"
                                name="id_sumberdana" id="id_sumberdana" style="width: 100%">
                                </select>
                            </div>


                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rek1">Rek 1</label>
                                <input type="text" name="rek1" oninput="formatRupiah(this);" id="rek1" class="form-control" value="{{ old('rek1', $pajak->rek1 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek2">Rek 2</label>
                                <input type="text" name="rek2" oninput="formatRupiah(this);" id="rek2" class="form-control" value="{{ old('rek2', $pajak->rek2 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek3">Rek 3</label>
                                <input type="text" name="rek3" oninput="formatRupiah(this);" id="rek3" class="form-control" value="{{ old('rek3', $pajak->rek3 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek4">Rek 4</label>
                                <input type="text" name="rek4" oninput="formatRupiah(this);" id="rek4" class="form-control" value="{{ old('rek4', $pajak->rek4 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek5">Rek 5</label>
                                <input type="text" name="rek5" oninput="formatRupiah(this);" id="rek5" class="form-control" value="{{ old('rek5', $pajak->rek5 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek6">Rek 6</label>
                                <input type="text" name="rek6" oninput="formatRupiah(this);" id="rek6" class="form-control" value="{{ old('rek6', $pajak->rek6 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek7">Rek 7</label>
                                <input type="text" name="rek7" oninput="formatRupiah(this);" id="rek7" class="form-control" value="{{ old('rek7', $pajak->rek7 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek8">Rek 8</label>
                                <input type="text" name="rek8" oninput="formatRupiah(this);" id="rek8" class="form-control" value="{{ old('rek8', $pajak->rek8 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek11">Rek 11</label>
                                <input type="text" name="rek11" oninput="formatRupiah(this);" id="rek11" class="form-control" value="{{ old('rek11', $pajak->rek11 ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="status_anggaran">Status Anggaran</label>
                                <input type="text" name="status_anggaran" id="status_anggaran" class="form-control" value="{{ old('status_anggaran', $pajak->status_anggaran ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="status_anggaran_kas">Status Anggaran Kas</label>
                                <input type="text" name="status_anggaran_kas" id="status_anggaran_kas" class="form-control" value="{{ old('status_anggaran_kas', $pajak->status_anggaran_kas ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="rek12">Nama Program</label>
                                <input type="text" name="nm_program" id="nm_program" readonly class="form-control" value="{{ old('nm_program', $pajak->nm_program ?? '') }}">
                                <input type="hidden" name="kd_program" id="kd_program" readonly class="form-control" value="{{ old('kd_program', $pajak->kd_program ?? '') }}">
                            </div>



                            <div class="form-group">
                                <label for="rek12">Nama Sumber Dana</label>
                                <input type="text" name="nm_sumberdana" readonly id="nm_sumberdana" class="form-control" value="{{ old('nm_sumberdana', $pajak->nm_sumberdana ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary">{{ isset($pajak) ? 'Update' : 'Tambah' }}</button>
                        <a href="{{ route('kelola_data.msanggaran.index') }}" class="btn btn-warning">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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


    $('#kd_sub_kegiatan').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        minimumInputLength: 0,
        ajax: {
            url: "{{ route('kelola_data.msanggaran.getsubkegiatan') }}",
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
                            nm_sub_kegiatan: item.nm_sub_kegiatan,
                            nm_program: item.nm_program,
                            kd_program: item.kd_program
                        }))
                     };
            }
        }
    });


    $('#id_sumberdana').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        minimumInputLength: 0,
        ajax: {
            url: "{{ route('kelola_data.msanggaran.getsumberdana') }}",
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
                            nm_sumberdana: item.nm_sumberdana,
                        }))
                     };
            }
        }
    });



    $('#id_sumberdana').on('select2:select', function(e) {
            var data = e.params.data;
            console.log(data);
            $('#nm_sumberdana').val(data.nm_sumberdana);
        });

    $('#kd_sub_kegiatan').on('select2:select', function(e) {
            var data = e.params.data;
            console.log(data);
            $('#nm_sub_kegiatan').val(data.nm_sub_kegiatan);
            $('#nm_program').val(data.nm_program);
            $('#kd_program').val(data.kd_program);
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
