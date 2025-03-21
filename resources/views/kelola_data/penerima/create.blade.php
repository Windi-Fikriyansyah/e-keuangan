@extends('template.app')
@section('title', 'Tambah Penerima')
@section('content')
<div class="page-heading">
    <h2>{{ isset($pajak) ? 'Edit Penerima' : 'Tambah Penerima' }}</h2>
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

        <h5>Potongan</h5>
        <div class="card">
            <div class="card-body">
                <form id="formBpkb" action="{{ isset($pajak) ? route('kelola_data.penerima.update', $pajak->id) : route('kelola_data.penerima.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($pajak))
                        @method('POST')
                    @endif


                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">BANK</label>
                        <div class="col-sm-4">
                            <select class="form-select @error('bank') is-invalid @enderror"
                            name="bank" id="bank" style="width: 100%">
                                @if(isset($pajak) && $pajak->bank)
                                    <option value="{{ $pajak->bank }}" selected>{{ $pajak->bank }}</option>
                                @endif
                            </select>
                            @error('bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                         </div>
                        <div class="col-sm-4">
                            <input name="nm_bank" id="nm_bank" class="form-control readonlyInput" type="text"
                                value="{{ isset($pajak) ? $pajak->nm_bank : '' }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">No Rekening Bank</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('rekening') is-invalid @enderror" name="rekening"
                            placeholder="Isi dengan No Rekening" value="{{ isset($pajak) ? $pajak->rekening : old('rekening') }}">
                            @error('rekening')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nama Pemilik</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('nm_rekening') is-invalid @enderror" name="nm_rekening"
                            placeholder="Isi dengan Nama Pemilik" value="{{ isset($pajak) ? $pajak->nm_rekening : old('nm_rekening') }}">
                            @error('nm_rekening')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">NPWP</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('npwp') is-invalid @enderror" name="npwp"
                            placeholder="Isi dengan NPWP" value="{{ isset($pajak) ? $pajak->npwp : old('npwp') }}">
                            @error('npwp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nama NPWP</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('nm_wp') is-invalid @enderror" name="nm_wp"
                            placeholder="Isi dengan Nama NPWP" value="{{ isset($pajak) ? $pajak->nm_wp : old('nm_wp') }}">
                            @error('nm_wp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" name="keterangan"
                                placeholder="Isi dengan Keterangan">{{ isset($pajak) ? $pajak->keterangan : old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Rekanan/Penerima</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('nm_rekan') is-invalid @enderror" name="nm_rekan"
                                value="{{ isset($pajak) ? $pajak->nmrekan : old('nmrekan') }}">
                            @error('nm_rekan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Pimpinan</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('pimpinan') is-invalid @enderror" name="pimpinan"
                                value="{{ isset($pajak) ? $pajak->pimpinan : old('pimpinan') }}">
                            @error('pimpinan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Alamat</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('alamat') is-invalid @enderror" name="alamat"
                                value="{{ isset($pajak) ? $pajak->alamat : old('alamat') }}">
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 text-end">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a href="{{ route('kelola_data.penerima.index') }}" class="btn btn-warning">Kembali</a>
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

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#bank').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        minimumInputLength: 0,
        ajax: {
            url: "{{ route('kelola_data.penerima.getbank') }}",
            dataType: 'json',
            type: "POST",
            delay: 250,
            data: function (params) {
                return { q: $.trim(params.term) };
            },
            processResults: function (data) {
                return {
                    results: data.map(item => ({
                        id: item.id,
                        text: item.text,
                        nm_bank: item.nm_bank
                    }))
                };
            }
        }
    });

    // Load bank data if in edit mode
    @if(isset($pajak) && $pajak->bank)
    $.ajax({
        url: "{{ route('kelola_data.penerima.getbank') }}",
        type: "POST",
        dataType: "json",
        data: { q: "{{ $pajak->bank }}" },
        success: function(data) {
            if (data.length > 0) {
                const bankData = data.find(item => item.id == "{{ $pajak->bank }}") || data[0];
                const option = new Option(bankData.text, bankData.id, true, true);
                $('#bank').append(option).trigger('change');
                $('#nm_bank').val(bankData.nm_bank);
            }
        }
    });
    @endif

    $('#bank').on('select2:select', function(e) {
        var data = e.params.data;
        console.log(data);
        $('#nm_bank').val(data.nm_bank);
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
        $('#nmrekpot').val(data.nmrekpot);
    });
});

</script>
@endpush
