@extends('template.app')
@section('title', 'Tambah SubKegiatan')
@section('content')
    <div class="page-heading">
        <h2>{{ isset($pajak) ? 'Edit SubKegiatan' : 'Tambah SubKegiatan' }}</h2>
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
        <div class="card">
            <div class="card-body">
                <form id="productForm" action="{{ isset($pajak) ? route('kelola_data.subkegiatan.update', $pajak->id) : route('kelola_data.subkegiatan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($pajak))
                        @method('POST')
                    @endif

                    <div class="form-group">
                        <label for="barcode">Kode Program</label>
                        <select class="form-select @error('kd_program') is-invalid @enderror"
                            name="kd_program" id="kd_program" style="width: 100%">
                            <option value="">Silahkan Pilih...</option>
                                @if(isset($pajak))
                                    <option value="{{ $pajak->kd_program }}" selected>
                                        {{ $pajak->kd_program }} || {{ $pajak->nm_program }}
                                    </option>
                                @endif
                            </select>
                    </div>

                    <div class="form-group">
                        <label for="barcode">Nama Program</label>
                        <input type="text" name="nm_program" id="nm_program" class="form-control" value="{{ old('nm_program', $pajak->nm_program ?? '') }}" placeholder="Input Kode Program" readonly>
                    </div>

                    <div class="form-group">
                        <label for="barcode">Kode Subkegiatan</label>
                        <input type="text" name="kd_sub_kegiatan" id="kd_sub_kegiatan" class="form-control" value="{{ old('kd_sub_kegiatan', $pajak->kd_sub_kegiatan ?? '') }}" placeholder="Input Kode Subkegiatan">
                    </div>

                    <div class="form-group">
                        <label for="name">Nama Subkegiatan</label>
                        <input type="text" name="nm_sub_kegiatan" id="nm_sub_kegiatan" class="form-control" value="{{ old('nm_sub_kegiatan', $pajak->nm_sub_kegiatan ?? '') }}" placeholder="Nama Subkegiatan">
                    </div>
                    <button type="submit" class="btn btn-primary">{{ isset($pajak) ? 'Update' : 'Tambah' }}</button>
                    <a href="{{ route('kelola_data.subkegiatan.index') }}" class="btn btn-warning">Kembali</a>
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
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $('#kd_program').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        minimumInputLength: 0,
        ajax: {
            url: "{{ route('kelola_data.subkegiatan.getprogram') }}",
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
                            nm_program: item.nm_program
                        }))
                     };
            }
        }
    });





    $('#kd_program').on('select2:select', function(e) {
            var data = e.params.data;
            console.log(data);
            $('#nm_program').val(data.nm_program);
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
