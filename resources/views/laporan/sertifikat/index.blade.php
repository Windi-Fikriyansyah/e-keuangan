@extends('template.app')
@section('title', 'Laporan Sertifikat')
@section('content')
    <div class="page-heading">
        <h3>Laporan Sertifikat</h3>
    </div>
    <div class="page-content">
        <div class="row">
            @if (Auth::user()->tipe == 'admin')
                <div class="col-md-6">
                    <div class="card card-info collapsed-card card-outline" id="rekap_sertifikat">
                        <div class="card-body">
                            {{ 'Rekap Data Sertifikat' }}
                            <a class="card-block stretched-link" href="#">

                            </a>
                            <i class="fa fa-chevron-right float-end mt-2"></i>

                        </div>
                    </div>
                </div>
            @endif
            <div class="col-md-6">
                <div class="card card-info collapsed-card card-outline" id="rekap_peminjaman">
                    <div class="card-body">
                        {{ 'Rekap Peminjaman Sertifikat' }}
                        <a class="card-block stretched-link" href="#">

                        </a>
                        <i class="fa fa-chevron-right float-end mt-2"></i>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalRekapSertifikat" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><label for="labelcetak">Rekap Sertifikat</label></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Pilihan --}}
                    <div class="mb-3 row" id="row-hidden">
                        <div class="col-md-6">
                            <label for="kd_skpd" class="form-label">Pilih</label><br>
                            <div class=" form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="pilihan_keseluruhanrekapSertifikat" value="1">
                                <label class="form-check-label" for="pilihan">Keseluruhan</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="pilihan_skpdrekapSertifikat" value="2">
                                <label class="form-check-label" for="pilihan">SKPD</label>
                            </div>
                        </div>
                    </div>
                    {{-- SKPD --}}
                    <div class="mb-3 row" id="pilih_skpdrekapSertifikat">
                        <div class="col-md-12">
                            <label for="kd_skpd" class="form-label">SKPD</label>
                            <select class="form-control select2-rekapSertifikat" style=" width: 100%;" id="kd_skpdrekapSertifikat">
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
                            <label class="form-label">Balik Nama</label>
                            <select class="form-control" style=" width: 100%;" id="balikNamarekapSertifikat">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Hak</label>
                            <select class="form-control" style=" width: 100%;" id="hakrekapSertifikat">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Asal-Usul</label>
                            <select class="form-control" style=" width: 100%;" id="asalUsulrekapSertifikat">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Tanggal TTD</label>
                            <input type="date" id="tanggalTtdrekapSertifikat" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Tanda Tangan</label>
                            <select class="form-control select2-rekapSertifikat" style=" width: 100%;" id="ttdrekapSertifikat">
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
                            <button type="button" class="btn btn-danger btn-md cetakrekapSertifikat" data-jenis="pdf">
                                PDF</button>
                            <button type="button" class="btn btn-dark btn-md cetakrekapSertifikat"
                                data-jenis="layar">Layar</button>
                            <button type="button" class="btn btn-dark btn-md cetakrekapSertifikat"
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
                        <div class="col-md-6">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" id="tanggal_awal" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" id="tanggal_akhir" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Balik Nama</label>
                            <select class="form-control" style=" width: 100%;" id="balikNamaPeminjaman">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Pengembalian</label>
                            <select class="form-control" style=" width: 100%;" id="PengembalianPeminjaman">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Hak</label>
                            <select class="form-control" style=" width: 100%;" id="hakPeminjaman">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Asal Usul</label>
                            <select class="form-control" style=" width: 100%;" id="asalUsulrekapPeminjaman">
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
                            <select class="form-control" style=" width: 100%;" id="ttdrekapPeminjaman">
                                <option value="" disabled selected>Silahkan Pilih</option>
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
            $('#balikNamarekapSertifikat').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih...",
                ajax: {
                    url: "{{ route('laporan.sertifikat.balikNama') }}",
                    dataType: 'json',
                    type: "POST",
                    data: function(params) {
                        let query = {
                            q: $.trim(params.term)
                        }

                        let kodeSkpd = $('#kd_skpdrekapSertifikat').val();

                        if (kodeSkpd) query.kodeSkpd = kodeSkpd;

                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: data.map((balikNama) => {
                                return {
                                    text: balikNama.balikNama === 'Keseluruhan'
                              ? "Keseluruhan"
                              : (balikNama.balikNama == 0 ? "Belum" : "Sudah"),
                                    id: balikNama.balikNama,
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

            $('#hakrekapSertifikat').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih...",
                ajax: {
                    url: "{{ route('laporan.sertifikat.hak') }}",
                    dataType: 'json',
                    type: "POST",
                    data: function(params) {
                        let query = {
                            q: $.trim(params.term)
                        }

                        let kodeSkpd = $('#kd_skpdrekapSertifikat').val();

                        if (kodeSkpd) query.kodeSkpd = kodeSkpd;

                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: data.map((Hak) => {
                                return {
                                    text: Hak.Hak,
                                    id: Hak.Hak,
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

            $('#asalUsulrekapSertifikat').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih...",
                ajax: {
                    url: "{{ route('laporan.sertifikat.asalUsul') }}",
                    dataType: 'json',
                    type: "POST",
                    data: function(params) {
                        let query = {
                            q: $.trim(params.term)
                        }

                        let kodeSkpd = $('#kd_skpdrekapSertifikat').val();
                        let Hak = $('#hakrekapSertifikat').val();

                        if (kodeSkpd) query.kodeSkpd = kodeSkpd;
                        if (Hak) query.Hak = Hak;

                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: data.map((asalUsul) => {
                                return {
                                    text: asalUsul.asalUsul,
                                    id: asalUsul.asalUsul,
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

            $('#kd_skpdrekapSertifikat').on('select2:select', function() {
                $('#balikNamarekapSertifikat').val(null).trigger('change').trigger('select2:select');
                $('#hakrekapSertifikat').val(null).trigger('change').trigger('select2:select');
                $('#asalUsulrekapSertifikat').val(null).trigger('change').trigger('select2:select');
            }).trigger('select2:select');

            $('#balikNamarekapSertifikat').on('select2:select', function() {
                $('#hakrekapSertifikat').val(null).trigger('change').trigger('select2:select');
                $('#asalUsulrekapSertifikat').val(null).trigger('change').trigger('select2:select');
            }).trigger('select2:select');

            $('#hakrekapSertifikat').on('select2:select', function() {
                $('#asalUsulrekapSertifikat').val(null).trigger('change').trigger('select2:select');
            }).trigger('select2:select');

            $('.select2-rekapSertifikat').select2({
                dropdownParent: $('#modalRekapSertifikat .modal-content'),
                theme: 'bootstrap-5'
            });

            $('#rekap_sertifikat').on('click', function() {
                $('#modalRekapSertifikat').modal('show')
            });

            $('#pilih_skpdrekapSertifikat').hide();

            $('#pilihan_keseluruhanrekapSertifikat').on('click', function() {
                $('#kd_skpdrekapSertifikat').val(null).change();
                $('#nm_skpdrekapSertifikat').val(null);

                $('#pilih_skpdrekapSertifikat').hide();

                $('#balikNamarekapSertifikat').val(null).trigger('change').trigger('select2:select');
                $('#hakrekapSertifikat').val(null).trigger('change').trigger('select2:select');
                $('#asalUsulrekapSertifikat').val(null).trigger('change').trigger('select2:select');
            });

            $('#pilihan_skpdrekapSertifikat').on('click', function() {
                $('#kd_skpdrekapSertifikat').val(null).change();
                $('#nm_skpdrekapSertifikat').val(null);

                $('#pilih_skpdrekapSertifikat').show();

                $('#balikNamarekapSertifikat').val(null).trigger('change').trigger('select2:select');
                $('#hakrekapSertifikat').val(null).trigger('change').trigger('select2:select');
                $('#asalUsulrekapSertifikat').val(null).trigger('change').trigger('select2:select');
            });

            $('.cetakrekapSertifikat').on('click', function() {
                let keseluruhan = $('#pilihan_keseluruhanrekapSertifikat').prop('checked');
                let skpd = $('#pilihan_skpdrekapSertifikat').prop('checked');

                if (keseluruhan == false && skpd == false) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Keseluruhan, SKPD atau Unit!",
                        icon: "warning"
                    });
                    return;
                }

                let kd_skpd = $('#kd_skpdrekapSertifikat').val();
                let balikNama = $('#balikNamarekapSertifikat').val();
                let Hak = $('#hakrekapSertifikat').val();
                let asalUsul = $('#asalUsulrekapSertifikat').val();
                let ttd = $('#ttdrekapSertifikat').val();
                let tanggalTtd = $('#tanggalTtdrekapSertifikat').val();
                let jenis_print = $(this).data("jenis");

                if (skpd && !kd_skpd) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih SKPD!",
                        icon: "warning"
                    });
                    return;
                }

                if (!balikNama) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Balik Nama!",
                        icon: "warning"
                    });
                    return;
                }

                if (!Hak) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Hak!",
                        icon: "warning"
                    });
                    return;
                }

                if (!asalUsul) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Asal Usul!",
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

                let url = new URL("{{ route('laporan.sertifikat.cetakRekapSertifikat') }}");
                let searchParams = url.searchParams;
                searchParams.append("pilihan", pilihan);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("balikNama", balikNama);
                searchParams.append("Hak", Hak);
                searchParams.append("asalUsul", asalUsul);
                searchParams.append("ttd", ttd);
                searchParams.append("tanggalTtd", tanggalTtd);
                searchParams.append("jenis_print", jenis_print);
                window.open(url.toString(), "_blank");
            });



            // CETAKAN REKAP PEMINJAMAN Sertifikat

            $('#hakPeminjaman').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih...",
                ajax: {
                    url: "{{ route('laporan.sertifikat.hak') }}",
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
                            results: data.map((Hak) => {
                                return {
                                    text: Hak.Hak,
                                    id: Hak.Hak,
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

            $('#balikNamaPeminjaman').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih...",
                ajax: {
                    url: "{{ route('laporan.sertifikat.balikNama') }}",
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
                            results: data.map((balikNama) => {
                                return {
                                    text: balikNama.balikNama === 'Keseluruhan'
                              ? "Keseluruhan"
                              : (balikNama.balikNama == 0 ? "Belum" : "Sudah"),
                                    id: balikNama.balikNama,
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

    $('#PengembalianPeminjaman').select2({
    theme: "bootstrap-5",
    width: "100%",
    placeholder: "Silahkan Pilih...",
    ajax: {
        url: "{{ route('laporan.sertifikat.Pengembalian') }}",
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
                results: data.map((item) => {
                    let text;
                    if (item.statusPengembalian === 'Keseluruhan') {
                        text = "Keseluruhan";
                    } else if (item.statusPengembalian == 0) {
                        text = "Belum";
                    } else if (item.statusPengembalian == 1) {
                        text = "Sudah";
                    } else {
                        text = item.statusPengembalian;
                    }
                    return {
                        text: text,
                        id: item.statusPengembalian,
                    };
                }),
                pagination: {
                    more: false,
                },
            };
        },
        cache: true
    }
});

            $('#asalUsulrekapPeminjaman').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih...",
                ajax: {
                    url: "{{ route('laporan.sertifikat.asalUsul') }}",
                    dataType: 'json',
                    type: "POST",
                    data: function(params) {
                        let query = {
                            q: $.trim(params.term)
                        }

                        let kodeSkpd = $('#kd_skpdrekapPeminjaman').val();
                        let Hak = $('#hakPeminjaman').val();

                        if (kodeSkpd) query.kodeSkpd = kodeSkpd;
                        if (Hak) query.Hak = Hak;

                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: data.map((asalUsul) => {
                                return {
                                    text: asalUsul.asalUsul,
                                    id: asalUsul.asalUsul,
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

            $('#ttdrekapPeminjaman').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih...",
                ajax: {
                    url: "{{ route('laporan.sertifikat.tandaTangan') }}",
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
                            results: data.map((ttd) => {
                                return {
                                    text: ttd.nama,
                                    id: ttd.nip,
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
                $('#hakPeminjaman').val(null).trigger('change').trigger('select2:select');
                $('#asalUsulrekapPeminjaman').val(null).trigger('change').trigger('select2:select');
                $('#ttdrekapPeminjaman').val(null).trigger('change').trigger('select2:select');
            }).trigger('select2:select');

            $('#hakPeminjaman').on('select2:select', function() {
                $('#asalUsulrekapPeminjaman').val(null).trigger('change').trigger('select2:select');
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
                $('#hakPeminjaman').val(null).trigger('change').trigger('select2:select');
                $('#asalUsulrekapPeminjaman').val(null).trigger('change').trigger('select2:select');
                $('#ttdrekapPeminjaman').val(null).trigger('change').trigger('select2:select');
            });

            $('#pilihan_skpdrekapPeminjaman').on('click', function() {
                $('#kd_skpdrekapPeminjaman').val(null).change();
                $('#nm_skpdrekapPeminjaman').val(null);

                $('#pilih_skpdrekapPeminjaman').show();

                $('#tahunrekapPeminjaman').val(null).trigger('change').trigger('select2:select');
                $('#hakPeminjaman').val(null).trigger('change').trigger('select2:select');
                $('#asalUsulrekapPeminjaman').val(null).trigger('change').trigger('select2:select');
                $('#ttdrekapPeminjaman').val(null).trigger('change').trigger('select2:select');
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
                let balikNama = $('#balikNamaPeminjaman').val();
                let statusPengembalian = $('#PengembalianPeminjaman').val();
                let Hak = $('#hakPeminjaman').val();
                let asalUsul = $('#asalUsulrekapPeminjaman').val();
                let ttd = $('#ttdrekapPeminjaman').val();
                let tanggalTtd = $('#tanggalTtdrekapPeminjaman').val();
                let jenis_print = $(this).data("jenis");
                let tanggal_awal = $('#tanggal_awal').val();
                let tanggal_akhir = $('#tanggal_akhir').val();
                if (skpd && !kd_skpd) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih SKPD!",
                        icon: "warning"
                    });
                    return;
                }

                if (!Hak) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Hak!",
                        icon: "warning"
                    });
                    return;
                }

                if (!statusPengembalian) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Pengembalian!",
                        icon: "warning"
                    });
                    return;
                }

                if (!asalUsul) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan Pilih Asal Usul!",
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

                if (!tanggal_awal || !tanggal_akhir) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Silahkan pilih periode tanggal!",
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

                let url = new URL("{{ route('laporan.sertifikat.cetakRekapPeminjaman') }}");
                let searchParams = url.searchParams;
                searchParams.append("pilihan", pilihan);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("balikNama", balikNama);
                searchParams.append("statusPengembalian", statusPengembalian);
                searchParams.append("tanggal_awal", tanggal_awal);
                searchParams.append("tanggal_akhir", tanggal_akhir);
                searchParams.append("Hak", Hak);
                searchParams.append("asalUsul", asalUsul);
                searchParams.append("ttd", ttd);
                searchParams.append("tanggalTtd", tanggalTtd);
                searchParams.append("jenis_print", jenis_print);
                window.open(url.toString(), "_blank");
            });
        });
    </script>
@endpush
