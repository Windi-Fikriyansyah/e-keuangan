@extends('template.app')
@section('title', 'Laporan BPKB')
@section('content')
    <div class="page-heading">
        <h3>Laporan BPKB</h3>
    </div>
    <div class="page-content">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-info collapsed-card card-outline" id="rekap_bpkb">
                    <div class="card-body">
                        {{ 'Rekap Data BPKB' }}
                        <a class="card-block stretched-link" href="#">

                        </a>
                        <i class="fa fa-chevron-right float-end mt-2"></i>

                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-info collapsed-card card-outline" id="rekap_peminjaman">
                    <div class="card-body">
                        {{ 'Rekap Peminjaman BPKB' }}
                        <a class="card-block stretched-link" href="#">

                        </a>
                        <i class="fa fa-chevron-right float-end mt-2"></i>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalRekapBpkb" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><label for="labelcetak">Rekap BPKB</label></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Pilihan --}}
                    <div class="mb-3 row" id="row-hidden">
                        <div class="col-md-6">
                            <label for="kd_skpd" class="form-label">Pilih</label><br>
                            <div class=" form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="pilihan_keseluruhanrekapBpkb" value="1">
                                <label class="form-check-label" for="pilihan">Keseluruhan</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="pilihan_skpdrekapBpkb" value="2">
                                <label class="form-check-label" for="pilihan">SKPD</label>
                            </div>
                        </div>
                    </div>
                    {{-- SKPD --}}
                    <div class="mb-3 row" id="pilih_skpdrekapBpkb">
                        <div class="col-md-12">
                            <label for="kd_skpd" class="form-label">SKPD</label>
                            <select class="form-control select2-rekapBpkb" style=" width: 100%;" id="kd_skpdrekapBpkb">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_skpd as $skpd)
                                    <option value="{{ $skpd->kodeSkpd }}">
                                        {{ $skpd->kodeSkpd }} | {{ $skpd->namaSkpd }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Tahun</label>
                            <select class="form-control" style=" width: 100%;" id="tahunrekapBpkb">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Jenis</label>
                            <select class="form-control" style=" width: 100%;" id="jenisrekapBpkb">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Merk</label>
                            <select class="form-control" style=" width: 100%;" id="merkrekapBpkb">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Tanggal TTD</label>
                            <input type="date" id="tanggalTtdrekapBpkb" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Tanda Tangan</label>
                            <select class="form-control select2-rekapBpkb" style=" width: 100%;" id="ttdrekapBpkb">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftarTtd as $ttd)
                                    <option value="{{ $ttd->nip }}">{{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Pilihan Cetak --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-danger btn-md cetakrekapBpkb" data-jenis="pdf">
                                PDF</button>
                            <button type="button" class="btn btn-dark btn-md cetakrekapBpkb"
                                data-jenis="layar">Layar</button>
                            <button type="button" class="btn btn-dark btn-md cetakrekapBpkb"
                                data-jenis="excel">Excel</button>
                            <button type="button" class="btn btn-md btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalRekapPeminjaman" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><label for="labelcetak">Rekap Peminjaman</label></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Pilihan --}}
                    <div class="mb-3 row" id="row-hidden">
                        <div class="col-md-6">
                            <label for="kd_skpd" class="form-label">Pilih</label><br>
                            <div class=" form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="pilihan_keseluruhanrekapPeminjaman" value="1">
                                <label class="form-check-label" for="pilihan">Keseluruhan</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="pilihan_skpdrekapPeminjaman" value="2">
                                <label class="form-check-label" for="pilihan">SKPD</label>
                            </div>
                        </div>
                    </div>
                    {{-- SKPD --}}
                    <div class="mb-3 row" id="pilih_skpdrekapPeminjaman">
                        <div class="col-md-12">
                            <label for="kd_skpd" class="form-label">SKPD</label>
                            <select class="form-control select2-rekapPeminjaman" style=" width: 100%;"
                                id="kd_skpdrekapPeminjaman">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_skpd as $skpd)
                                    <option value="{{ $skpd->kodeSkpd }}">
                                        {{ $skpd->kodeSkpd }} | {{ $skpd->namaSkpd }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Jenis</label>
                            <select class="form-control" style=" width: 100%;" id="jenisrekapPeminjaman">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Merk</label>
                            <select class="form-control" style=" width: 100%;" id="merkrekapPeminjaman">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Tanggal TTD</label>
                            <input type="date" id="tanggalTtdrekapPeminjaman" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Tanda Tangan</label>
                            <select class="form-control select2-rekapPeminjaman" style=" width: 100%;"
                                id="ttdrekapPeminjaman">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftarTtd as $ttd)
                                    <option value="{{ $ttd->nip }}">{{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Pilihan Cetak --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-danger btn-md cetakrekapPeminjaman" data-jenis="pdf">
                                PDF</button>
                            <button type="button" class="btn btn-dark btn-md cetakrekapPeminjaman"
                                data-jenis="layar">Layar</button>
                            <button type="button" class="btn btn-dark btn-md cetakrekapPeminjaman"
                                data-jenis="excel">Excel</button>
                            <button type="button" class="btn btn-md btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // CETAKAN REKAP BPKB
            $('#tahunrekapBpkb').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih...",
                ajax: {
                    url: "{{ route('laporan.bpkb.tahun') }}",
                    dataType: 'json',
                    type: "POST",
                    data: function(params) {
                        let query = {
                            q: $.trim(params.term)
                        }

                        let kodeSkpd = $('#kd_skpdrekapBpkb').val();

                        if (kodeSkpd) query.kodeSkpd = kodeSkpd;

                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: data.map((tahun) => {
                                return {
                                    text: tahun.tahun,
                                    id: tahun.tahun,
                                };
                            }),
                            pagination: {
                                more: data.current_page < data.last_page,
                            },
                        };
                    },
                    cache: true
                }
            });

            $('#jenisrekapBpkb').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih...",
                ajax: {
                    url: "{{ route('laporan.bpkb.jenis') }}",
                    dataType: 'json',
                    type: "POST",
                    data: function(params) {
                        let query = {
                            q: $.trim(params.term)
                        }

                        let kodeSkpd = $('#kd_skpdrekapBpkb').val();

                        if (kodeSkpd) query.kodeSkpd = kodeSkpd;

                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: data.map((jenis) => {
                                return {
                                    text: jenis.jenis,
                                    id: jenis.jenis,
                                };
                            }),
                            pagination: {
                                more: data.current_page < data.last_page,
                            },
                        };
                    },
                    cache: true
                }
            });

            $('#merkrekapBpkb').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih...",
                ajax: {
                    url: "{{ route('laporan.bpkb.merk') }}",
                    dataType: 'json',
                    type: "POST",
                    data: function(params) {
                        let query = {
                            q: $.trim(params.term)
                        }

                        let kodeSkpd = $('#kd_skpdrekapBpkb').val();
                        let jenis = $('#jenisrekapBpkb').val();

                        if (kodeSkpd) query.kodeSkpd = kodeSkpd;
                        if (jenis) query.jenis = jenis;

                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: data.map((merk) => {
                                return {
                                    text: merk.merk,
                                    id: merk.merk,
                                };
                            }),
                            pagination: {
                                more: data.current_page < data.last_page,
                            },
                        };
                    },
                    cache: true
                }
            });

            $('#kd_skpdrekapBpkb').on('select2:select', function() {
                $('#tahunrekapBpkb').val(null).trigger('change').trigger('select2:select');
                $('#jenisrekapBpkb').val(null).trigger('change').trigger('select2:select');
                $('#merkrekapBpkb').val(null).trigger('change').trigger('select2:select');
            }).trigger('select2:select');

            $('#tahunrekapBpkb').on('select2:select', function() {
                $('#jenisrekapBpkb').val(null).trigger('change').trigger('select2:select');
                $('#merkrekapBpkb').val(null).trigger('change').trigger('select2:select');
            }).trigger('select2:select');

            $('#jenisrekapBpkb').on('select2:select', function() {
                $('#merkrekapBpkb').val(null).trigger('change').trigger('select2:select');
            }).trigger('select2:select');

            $('.select2-rekapBpkb').select2({
                dropdownParent: $('#modalRekapBpkb .modal-content'),
                theme: 'bootstrap-5'
            });

            $('#rekap_bpkb').on('click', function() {
                $('#modalRekapBpkb').modal('show')
            });

            $('#pilih_skpdrekapBpkb').hide();

            $('#pilihan_keseluruhanrekapBpkb').on('click', function() {
                $('#kd_skpdrekapBpkb').val(null).change();
                $('#nm_skpdrekapBpkb').val(null);

                $('#pilih_skpdrekapBpkb').hide();

                $('#tahunrekapBpkb').val(null).trigger('change').trigger('select2:select');
                $('#jenisrekapBpkb').val(null).trigger('change').trigger('select2:select');
                $('#merkrekapBpkb').val(null).trigger('change').trigger('select2:select');
            });

            $('#pilihan_skpdrekapBpkb').on('click', function() {
                $('#kd_skpdrekapBpkb').val(null).change();
                $('#nm_skpdrekapBpkb').val(null);

                $('#pilih_skpdrekapBpkb').show();

                $('#tahunrekapBpkb').val(null).trigger('change').trigger('select2:select');
                $('#jenisrekapBpkb').val(null).trigger('change').trigger('select2:select');
                $('#merkrekapBpkb').val(null).trigger('change').trigger('select2:select');
            });

            $('.cetakrekapBpkb').on('click', function() {
                let keseluruhan = $('#pilihan_keseluruhanrekapBpkb').prop('checked');
                let skpd = $('#pilihan_skpdrekapBpkb').prop('checked');

                if (keseluruhan == false && skpd == false) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Keseluruhan, SKPD atau Unit!",
                        icon: "warning"
                    });
                    return;
                }

                let kd_skpd = $('#kd_skpdrekapBpkb').val();
                let tahun = $('#tahunrekapBpkb').val();
                let jenis = $('#jenisrekapBpkb').val();
                let merk = $('#merkrekapBpkb').val();
                let ttd = $('#ttdrekapBpkb').val();
                let tanggalTtd = $('#tanggalTtdrekapBpkb').val();
                let jenis_print = $(this).data("jenis");

                if (skpd && !kd_skpd) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih SKPD!",
                        icon: "warning"
                    });
                    return;
                }

                if (!tahun) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Tahun!",
                        icon: "warning"
                    });
                    return;
                }

                if (!jenis) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Jenis!",
                        icon: "warning"
                    });
                    return;
                }

                if (!merk) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Merk!",
                        icon: "warning"
                    });
                    return;
                }

                if (!tanggalTtd) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Tanggal Tanda Tangan!",
                        icon: "warning"
                    });
                    return;
                }

                if (!ttd) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Tanda Tangan!",
                        icon: "warning"
                    });
                    return;
                }

                let pilihan = '';

                if (keseluruhan) {
                    pilihan = '1';
                } else if (skpd) {
                    pilihan = '2';
                }

                let url = new URL("{{ route('laporan.bpkb.cetakRekapBpkb') }}");
                let searchParams = url.searchParams;
                searchParams.append("pilihan", pilihan);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tahun", tahun);
                searchParams.append("jenis", jenis);
                searchParams.append("merk", merk);
                searchParams.append("ttd", ttd);
                searchParams.append("tanggalTtd", tanggalTtd);
                searchParams.append("jenis_print", jenis_print);
                window.open(url.toString(), "_blank");
            });



            // CETAKAN REKAP PEMINJAMAN BPKB

            $('#jenisrekapPeminjaman').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih...",
                ajax: {
                    url: "{{ route('laporan.bpkb.jenis') }}",
                    dataType: 'json',
                    type: "POST",
                    data: function(params) {
                        let query = {
                            q: $.trim(params.term)
                        }

                        let kodeSkpd = $('#kd_skpdrekapPeminjaman').val();

                        if (kodeSkpd) query.kodeSkpd = kodeSkpd;

                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: data.map((jenis) => {
                                return {
                                    text: jenis.jenis,
                                    id: jenis.jenis,
                                };
                            }),
                            pagination: {
                                more: data.current_page < data.last_page,
                            },
                        };
                    },
                    cache: true
                }
            });

            $('#merkrekapPeminjaman').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih...",
                ajax: {
                    url: "{{ route('laporan.bpkb.merk') }}",
                    dataType: 'json',
                    type: "POST",
                    data: function(params) {
                        let query = {
                            q: $.trim(params.term)
                        }

                        let kodeSkpd = $('#kd_skpdrekapPeminjaman').val();
                        let jenis = $('#jenisrekapPeminjaman').val();

                        if (kodeSkpd) query.kodeSkpd = kodeSkpd;
                        if (jenis) query.jenis = jenis;

                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: data.map((merk) => {
                                return {
                                    text: merk.merk,
                                    id: merk.merk,
                                };
                            }),
                            pagination: {
                                more: data.current_page < data.last_page,
                            },
                        };
                    },
                    cache: true
                }
            });

            $('#kd_skpdrekapPeminjaman').on('select2:select', function() {
                $('#jenisrekapPeminjaman').val(null).trigger('change').trigger('select2:select');
                $('#merkrekapPeminjaman').val(null).trigger('change').trigger('select2:select');
            }).trigger('select2:select');

            $('#jenisrekapPeminjaman').on('select2:select', function() {
                $('#merkrekapPeminjaman').val(null).trigger('change').trigger('select2:select');
            }).trigger('select2:select');

            $('.select2-rekapPeminjaman').select2({
                dropdownParent: $('#modalRekapPeminjaman .modal-content'),
                theme: 'bootstrap-5'
            });

            $('#rekap_peminjaman').on('click', function() {
                $('#modalRekapPeminjaman').modal('show')
            });

            $('#pilih_skpdrekapPeminjaman').hide();

            $('#pilihan_keseluruhanrekapPeminjaman').on('click', function() {
                $('#kd_skpdrekapPeminjaman').val(null).change();
                $('#nm_skpdrekapPeminjaman').val(null);

                $('#pilih_skpdrekapPeminjaman').hide();

                $('#tahunrekapPeminjaman').val(null).trigger('change').trigger('select2:select');
                $('#jenisrekapPeminjaman').val(null).trigger('change').trigger('select2:select');
                $('#merkrekapPeminjaman').val(null).trigger('change').trigger('select2:select');
            });

            $('#pilihan_skpdrekapPeminjaman').on('click', function() {
                $('#kd_skpdrekapPeminjaman').val(null).change();
                $('#nm_skpdrekapPeminjaman').val(null);

                $('#pilih_skpdrekapPeminjaman').show();

                $('#tahunrekapPeminjaman').val(null).trigger('change').trigger('select2:select');
                $('#jenisrekapPeminjaman').val(null).trigger('change').trigger('select2:select');
                $('#merkrekapPeminjaman').val(null).trigger('change').trigger('select2:select');
            });

            $('.cetakrekapPeminjaman').on('click', function() {
                let keseluruhan = $('#pilihan_keseluruhanrekapPeminjaman').prop('checked');
                let skpd = $('#pilihan_skpdrekapPeminjaman').prop('checked');

                if (keseluruhan == false && skpd == false) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Keseluruhan, SKPD atau Unit!",
                        icon: "warning"
                    });
                    return;
                }

                let kd_skpd = $('#kd_skpdrekapPeminjaman').val();
                let jenis = $('#jenisrekapPeminjaman').val();
                let merk = $('#merkrekapPeminjaman').val();
                let ttd = $('#ttdrekapPeminjaman').val();
                let tanggalTtd = $('#tanggalTtdrekapPeminjaman').val();
                let jenis_print = $(this).data("jenis");

                if (skpd && !kd_skpd) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih SKPD!",
                        icon: "warning"
                    });
                    return;
                }

                if (!jenis) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Jenis!",
                        icon: "warning"
                    });
                    return;
                }

                if (!merk) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Merk!",
                        icon: "warning"
                    });
                    return;
                }

                if (!tanggalTtd) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Tanggal Tanda Tangan!",
                        icon: "warning"
                    });
                    return;
                }

                if (!ttd) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Tanda Tangan!",
                        icon: "warning"
                    });
                    return;
                }

                let pilihan = '';

                if (keseluruhan) {
                    pilihan = '1';
                } else if (skpd) {
                    pilihan = '2';
                }

                let url = new URL("{{ route('laporan.bpkb.cetakRekapPeminjaman') }}");
                let searchParams = url.searchParams;
                searchParams.append("pilihan", pilihan);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("jenis", jenis);
                searchParams.append("merk", merk);
                searchParams.append("ttd", ttd);
                searchParams.append("tanggalTtd", tanggalTtd);
                searchParams.append("jenis_print", jenis_print);
                window.open(url.toString(), "_blank");
            });
        });
    </script>
@endpush
