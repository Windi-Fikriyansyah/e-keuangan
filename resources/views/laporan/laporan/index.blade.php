@extends('template.app')

@section('title', 'Laporan')

@section('content')
    <div class="page-heading">
        <h3>Laporan</h3>
    </div>
    <div class="page-content">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-info collapsed-card card-outline" id="BKU" data-bs-toggle="modal" data-bs-target="#modalBku">
                    <div class="card-body">
                        <span>Laporan BKU</span>
                        <a class="stretched-link" href="#"></a>
                        <i class="fa fa-chevron-right float-end mt-2"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-info collapsed-card card-outline" id="BPP" data-bs-toggle="modal" data-bs-target="#modalBPPajak">
                    <div class="card-body">
                        <span>Buku Pembantu Pajak</span>
                        <a class="stretched-link" href="#"></a>
                        <i class="fa fa-chevron-right float-end mt-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-info collapsed-card card-outline" id="BPBANK" data-bs-toggle="modal" data-bs-target="#modalBpbank">
                    <div class="card-body">
                        <span>Buku Pembantu Bank</span>
                        <a class="stretched-link" href="#"></a>
                        <i class="fa fa-chevron-right float-end mt-2"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-info collapsed-card card-outline" id="DTH" data-bs-toggle="modal" data-bs-target="#modalDTH">
                    <div class="card-body">
                        <span>DTH</span>
                        <a class="stretched-link" href="#"></a>
                        <i class="fa fa-chevron-right float-end mt-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-info collapsed-card card-outline" id="realisasifisik" data-bs-toggle="modal" data-bs-target="#modalrealisasifisik">
                    <div class="card-body">
                        <span>Realisasi Fisik</span>
                        <a class="stretched-link" href="#"></a>
                        <i class="fa fa-chevron-right float-end mt-2"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-info collapsed-card card-outline" id="rincianobjek" data-bs-toggle="modal" data-bs-target="#modalrincianobjek">
                    <div class="card-body">
                        <span>Rincian Objek</span>
                        <a class="stretched-link" href="#"></a>
                        <i class="fa fa-chevron-right float-end mt-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="row">
            {{-- <div class="col-md-6">
                <div class="card card-info collapsed-card card-outline" id="spj" data-bs-toggle="modal" data-bs-target="#modalspj">
                    <div class="card-body">
                        <span>SPJ Fungsional</span>
                        <a class="stretched-link" href="#"></a>
                        <i class="fa fa-chevron-right float-end mt-2"></i>
                    </div>
                </div>
            </div> --}}

            <div class="col-md-6">
                <div class="card card-info collapsed-card card-outline" id="rinciancp" data-bs-toggle="modal" data-bs-target="#modalrinciancp">
                    <div class="card-body">
                        <span>Rincian CP</span>
                        <a class="stretched-link" href="#"></a>
                        <i class="fa fa-chevron-right float-end mt-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal BKU -->
    <div id="modalBku" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Laporan BKU</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="kd_skpdrekapbku" class="form-label">Kode SKPD</label>
                            <input type="text"
                                   value="{{ ($daftar_skpd->kd_skpd ?? '') }}"
                                   id="kd_skpdrekapbku"
                                   name="kd_skpd"
                                   class="form-control"
                                   readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="name_skpdrekapbku" class="form-label">Nama SKPD</label>
                            <input type="text"
                                   value="{{ ($daftar_skpd->name ?? '') }}"
                                   id="name_skpdrekapbku"
                                   name="name_skpd"
                                   class="form-control"
                                   readonly>
                        </div>
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Tanggal Awal</label>
                        <input type="date" id="tanggalawal" name="tanggalawal" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" id="tanggalakhir" name="tanggalakhir" class="form-control">
                    </div>


                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal TTD</label>
                            <input type="date" id="tanggalTtdrekapbku" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bendahara</label>
                            <select class="form-control" id="ttdbendaharabku">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">PA/KPA</label>
                            <select class="form-control" id="ttdbku">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>


                    <div class="mb-3 text-center">
                        <button type="button" class="btn btn-danger btn-md cetakbku" data-jenis="pdf">PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetakbku" data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-dark btn-md cetakbku" data-jenis="excel">Excel</button>
                        <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- BP Pajak -->
    <div id="modalBPPajak" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Laporan BP Pajak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="kd_skpdrekapbpp" class="form-label">Kode SKPD</label>
                            <input type="text"
                                   value="{{ ($daftar_skpd->kd_skpd ?? '') }}"
                                   id="kd_skpdrekapbpp"
                                   name="kd_skpd"
                                   class="form-control"
                                   readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="name_skpdrekapbpp" class="form-label">Nama SKPD</label>
                            <input type="text"
                                   value="{{ ($daftar_skpd->name ?? '') }}"
                                   id="name_skpdrekapbpp"
                                   name="name_skpd"
                                   class="form-control"
                                   readonly>
                        </div>
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Tanggal Awal</label>
                        <input type="date" id="tanggalawal1" name="tanggalawal1" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" id="tanggalakhir1" name="tanggalakhir1" class="form-control">
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal TTD</label>
                            <input type="date" id="tanggalTtdrekapbpp" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bendahara</label>
                            <select class="form-control" id="ttdbendaharabpp">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">PA/KPA</label>
                            <select class="form-control" id="ttdbpp">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>


                    <div class="mb-3 text-center">
                        <button type="button" class="btn btn-danger btn-md cetakbpp" data-jenis="pdf">PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetakbpp" data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-dark btn-md cetakbpp" data-jenis="excel">Excel</button>
                        <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalBpbank" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Laporan BP Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="kd_skpdrekapbpbank" class="form-label">Kode SKPD</label>
                            <input type="text" value="{{ ($daftar_skpd->kd_skpd ?? '') }}" id="kd_skpdrekapbpbank" name="kd_skpd" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="name_skpdrekapbpbank" class="form-label">Nama SKPD</label>
                            <input type="text" value="{{ ($daftar_skpd->name ?? '') }}" id="name_skpdrekapbpbank" name="name_skpd" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" id="tanggalawalbpbank" name="tanggalawal" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" id="tanggalakhirbpbank" name="tanggalakhir" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal TTD</label>
                            <input type="date" id="tanggalTtdrekapbpbank" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bendahara</label>
                            <select class="form-control" id="ttdbendaharabpbank">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">PA/KPA</label>
                            <select class="form-control" id="ttdbpbank">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 text-center">
                        <button type="button" class="btn btn-danger btn-md cetakbpbank" data-jenis="pdf">PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetakbpbank" data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-dark btn-md cetakbpbank" data-jenis="excel">Excel</button>
                        <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="modalDTH" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Laporan BP Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="kd_skpdrekapdth" class="form-label">Kode SKPD</label>
                            <input type="text" value="{{ ($daftar_skpd->kd_skpd ?? '') }}" id="kd_skpdrekapdth" name="kd_skpd" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="name_skpdrekapdth" class="form-label">Nama SKPD</label>
                            <input type="text" value="{{ ($daftar_skpd->name ?? '') }}" id="name_skpdrekapdth" name="name_skpd" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" id="tanggalawaldth" name="tanggalawal" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" id="tanggalakhirdth" name="tanggalakhir" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal TTD</label>
                            <input type="date" id="tanggalTtdrekapdth" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bendahara</label>
                            <select class="form-control" id="ttdbendaharadth">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">PA/KPA</label>
                            <select class="form-control" id="ttdpa_kpa">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 text-center">
                        <button type="button" class="btn btn-danger btn-md cetakdth" data-jenis="pdf">PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetakdth" data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-dark btn-md cetakdth" data-jenis="excel">Excel</button>
                        <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalrealisasifisik" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Laporan Realisasi Fisik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="kd_skpdrekaprealisasi" class="form-label">Kode SKPD</label>
                            <input type="text" value="{{ ($daftar_skpd->kd_skpd ?? '') }}" id="kd_skpdrekaprealisasi" name="kd_skpd" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="name_skpdrekaprealisasi" class="form-label">Nama SKPD</label>
                            <input type="text" value="{{ ($daftar_skpd->name ?? '') }}" id="name_skpdrekaprealisasi" name="name_skpd" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" id="tanggalawalrealisasi" name="tanggalawal" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" id="tanggalakhirrealisasi" name="tanggalakhir" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal TTD</label>
                            <input type="date" id="tanggalTtdrekaprealisasi" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bendahara</label>
                            <select class="form-control" id="ttdbendahararealisasi">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">PA/KPA</label>
                            <select class="form-control" id="ttdrealisasi">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 text-center">
                        <button type="button" class="btn btn-danger btn-md cetakrealisasi" data-jenis="pdf">PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetakrealisasi" data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-dark btn-md cetakrealisasi" data-jenis="excel">Excel</button>
                        <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalrincianobjek" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Laporan Rincian Objek</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="kd_skpdrekapobjek" class="form-label">Kode SKPD</label>
                            <input type="text" value="{{ ($daftar_skpd->kd_skpd ?? '') }}" id="kd_skpdrekapobjek" name="kd_skpd" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="name_skpdrekapobjek" class="form-label">Nama SKPD</label>
                            <input type="text" value="{{ ($daftar_skpd->name ?? '') }}" id="name_skpdrekapobjek" name="name_skpd" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Jenis Anggaran</label>
                            <select class="form-control" id="jenis_anggaran">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="Penetapan">M|Penetapan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sub Kegiatan</label>
                            <select class="form-control" id="sub_kegiatan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Jenis</label>
                            <select class="form-control" id="jenis_objek">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1">Cek Pemakaian Anggaran Akun Belanja</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Akun Belanja</label>
                            <select class="form-control" id="akun_belanja">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>


                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" id="tanggalawalobjek" name="tanggalawal" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" id="tanggalakhirobjek" name="tanggalakhir" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal TTD</label>
                            <input type="date" id="tanggalTtdrekapobjek" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bendahara</label>
                            <select class="form-control" id="ttdbendaharaobjek">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">PA/KPA</label>
                            <select class="form-control" id="ttdobjek">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 text-center">
                        <button type="button" class="btn btn-danger btn-md cetakobjek" data-jenis="pdf">PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetakobjek" data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-dark btn-md cetakobjek" data-jenis="excel">Excel</button>
                        <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalspj" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Laporan SPJ Fungsional</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="kd_skpdrekapspj" class="form-label">Kode SKPD</label>
                            <input type="text" value="{{ ($daftar_skpd->kd_skpd ?? '') }}" id="kd_skpdrekapspj" name="kd_skpd" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="name_skpdrekapspj" class="form-label">Nama SKPD</label>
                            <input type="text" value="{{ ($daftar_skpd->name ?? '') }}" id="name_skpdrekapspj" name="name_skpd" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" id="tanggalawalspj" name="tanggalawal" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" id="tanggalakhirspj" name="tanggalakhir" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal TTD</label>
                            <input type="date" id="tanggalTtdrekapspj" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bendahara</label>
                            <select class="form-control" id="ttdbendaharaspj">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">PA/KPA</label>
                            <select class="form-control" id="ttdspj">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 text-center">
                        <button type="button" class="btn btn-danger btn-md cetakspj" data-jenis="pdf">PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetakspj" data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-dark btn-md cetakspj" data-jenis="excel">Excel</button>
                        <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalrinciancp" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Laporan Rincian CP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="kd_skpdrekaprinciancp" class="form-label">Kode SKPD</label>
                            <input type="text" value="{{ ($daftar_skpd->kd_skpd ?? '') }}" id="kd_skpdrekaprinciancp" name="kd_skpd" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="name_skpdrekaprinciancp" class="form-label">Nama SKPD</label>
                            <input type="text" value="{{ ($daftar_skpd->name ?? '') }}" id="name_skpdrekaprinciancp" name="name_skpd" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" id="tanggalawalrinciancp" name="tanggalawal" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" id="tanggalakhirrinciancp" name="tanggalakhir" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal TTD</label>
                            <input type="date" id="tanggalTtdrekaprinciancp" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bendahara</label>
                            <select class="form-control" id="ttdbendahararinciancp">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">PA/KPA</label>
                            <select class="form-control" id="ttdrinciancp">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 text-center">
                        <button type="button" class="btn btn-danger btn-md cetakrinciancp" data-jenis="pdf">PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetakrinciancp" data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-dark btn-md cetakrinciancp" data-jenis="excel">Excel</button>
                        <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
<script>
    $(document).ready(function () {

        $('#ttdbendaharadth').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('laporan.laporan.tandaTangan') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term),
                    kodeSkpd: $('#kd_skpdrekapdth').val()
                };
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


    $('#ttdpa_kpa').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('laporan.laporan.tandaTanganPa') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term),
                    kodeSkpd: $('#kd_skpdrekapdth').val()
                };
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

    $('#ttdbendahararinciancp').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('laporan.laporan.tandaTangan') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term),
                    kodeSkpd: $('#kd_skpdrekaprinciancp').val()
                };
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


    $('#ttdrinciancp').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('laporan.laporan.tandaTanganPa') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term),
                    kodeSkpd: $('#kd_skpdrekaprinciancp').val()
                };
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


    $('#ttdbendaharaspj').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('laporan.laporan.tandaTangan') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term),
                    kodeSkpd: $('#kd_skpdrekapspj').val()
                };
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


    $('#ttdspj').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('laporan.laporan.tandaTanganPa') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term),
                    kodeSkpd: $('#kd_skpdrekapspj').val()
                };
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

    $('#ttdbendaharabku').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('laporan.laporan.tandaTangan') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term),
                    kodeSkpd: $('#kd_skpdrekapbku').val()
                };
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


    $('#ttdbku').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('laporan.laporan.tandaTanganPa') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term),
                    kodeSkpd: $('#kd_skpdrekapbku').val()
                };
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

    $('#ttdbendaharabpp').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('laporan.laporan.tandaTangan') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term),
                    kodeSkpd: $('#kd_skpdrekapbpp').val()
                };
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


    $('#ttdbpp').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('laporan.laporan.tandaTanganPa') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term),
                    kodeSkpd: $('#kd_skpdrekapbpp').val()
                };
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

    $('#ttdbendaharabpbank').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('laporan.laporan.tandaTangan') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term),
                    kodeSkpd: $('#kd_skpdrekapbpbank').val()
                };
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


    $('#ttdbpbank').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('laporan.laporan.tandaTanganPa') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term),
                    kodeSkpd: $('#kd_skpdrekapbpbank').val()
                };
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

    $('#ttdbendahararealisasi').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('laporan.laporan.tandaTangan') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term),
                    kodeSkpd: $('#kd_skpdrekaprealisasi').val()
                };
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


    $('#ttdrealisasi').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('laporan.laporan.tandaTanganPa') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term),
                    kodeSkpd: $('#kd_skpdrekaprealisasi').val()
                };
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

    $('#ttdbendaharaobjek').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('laporan.laporan.tandaTangan') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term),
                    kodeSkpd: $('#kd_skpdrekapobjek').val()
                };
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


    $('#ttdobjek').select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: "Silahkan Pilih...",
        ajax: {
            url: "{{ route('laporan.laporan.tandaTanganPa') }}",
            dataType: 'json',
            type: "POST",
            data: function(params) {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Tambahkan CSRF token
                    q: $.trim(params.term),
                    kodeSkpd: $('#kd_skpdrekapobjek').val()
                };
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

    $('#sub_kegiatan').select2({
    dropdownParent: $('#modalrincianobjek'),
    theme: "bootstrap-5",
    width: "100%",
    placeholder: "Silahkan Pilih...",
    ajax: {
        url: "{{ route('laporan.laporan.getsubkegiatan') }}",
        dataType: 'json',
        type: "POST",
        data: function(params) {
            return {
                _token: $('meta[name="csrf-token"]').attr('content'),
                q: $.trim(params.term),
                kodeSkpd: $('#kd_skpdrekapobjek').val()
            };
        },
        processResults: function(data) {
            // Memeriksa apakah data adalah objek tunggal atau array
            if (!Array.isArray(data)) {
                // Jika objek tunggal, ubah menjadi array dengan satu item
                if (data && data.kd_sub_kegiatan) {
                    return {
                        results: [{
                            id: data.kd_sub_kegiatan,
                            text: data.kd_sub_kegiatan + ' - ' + data.nm_sub_kegiatan
                        }]
                    };
                }
                return { results: [] };
            }

            // Jika sudah array, proses seperti biasa
            return {
                results: data.map(function(item) {
                    return {
                        id: item.kd_sub_kegiatan,
                        text: item.kd_sub_kegiatan + ' - ' + item.nm_sub_kegiatan
                    };
                }),
                pagination: {
                    more: data.current_page && data.last_page ?
                          data.current_page < data.last_page : false
                }
            };
        },
        cache: true
    }
});


$('#akun_belanja').select2({
    dropdownParent: $('#modalrincianobjek'),
    theme: "bootstrap-5",
    width: "100%",
    placeholder: "Silahkan Pilih...",
    ajax: {
        url: "{{ route('laporan.laporan.getakunbelanja') }}",
        dataType: 'json',
        type: "POST",
        data: function(params) {
            return {
                _token: $('meta[name="csrf-token"]').attr('content'),
                q: $.trim(params.term),
                kodeSkpd: $('#kd_skpdrekapobjek').val()
            };
        },
        processResults: function(data) {
            // Memeriksa apakah data adalah objek tunggal atau array
            if (!Array.isArray(data)) {
                // Jika objek tunggal, ubah menjadi array dengan satu item
                if (data && data.kd_rek6) {
                    return {
                        results: [{
                            id: data.kd_rek6,
                            text: data.kd_rek6 + ' - ' + data.nm_rek6
                        }]
                    };
                }
                return { results: [] };
            }

            // Jika sudah array, proses seperti biasa
            return {
                results: data.map(function(item) {
                    return {
                        id: item.kd_rek6,
                        text: item.kd_rek6 + ' - ' + item.nm_rek6
                    };
                }),
                pagination: {
                    more: data.current_page && data.last_page ?
                          data.current_page < data.last_page : false
                }
            };
        },
        cache: true
    }
});
        $('.cetakbku').on('click', function () {
            let kd_skpd = $('#kd_skpdrekapbku').val();
            let tanggalawal = $('#tanggalawal').val();
            let tanggalakhir = $('#tanggalakhir').val();
            let tanggalTtd = $('#tanggalTtdrekapbku').val();
            let ttdbendahara = $('#ttdbendaharabku').val(); // Pastikan ID ini sesuai
            let ttdpa_kpa = $('#ttdbku').val();
            let jenis_print = $(this).data("jenis");

            if (!kd_skpd) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih SKPD!",
                    icon: "warning"
                });
                return;
            }

            if (!tanggalawal || !tanggalakhir) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih rentang tanggal!",
                    icon: "warning"
                });
                return;
            }

            if (!tanggalTtd) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih tanggal tanda tangan!",
                    icon: "warning"
                });
                return;
            }

            let url = new URL("{{ route('laporan.laporan.cetakbku') }}", window.location.origin);
            url.searchParams.append("kd_skpd", kd_skpd);
            url.searchParams.append("tanggalawal", tanggalawal);
            url.searchParams.append("tanggalakhir", tanggalakhir);
            url.searchParams.append("tanggalTtd", tanggalTtd);
            url.searchParams.append("ttdbendaharadth", ttdbendahara); // Pastikan cocok dengan request controller
            url.searchParams.append("ttdpa_kpa", ttdpa_kpa);
            url.searchParams.append("jenis_print", jenis_print);

            window.open(url.toString(), "_blank");
        });

        $('.cetakbpbank').on('click', function () {
            let kd_skpd = $('#kd_skpdrekapbpbank').val();
            let tanggalawal = $('#tanggalawalbpbank').val();
            let tanggalakhir = $('#tanggalakhirbpbank').val();
            let tanggalTtd = $('#tanggalTtdrekapbpbank').val();
            let ttdbendahara = $('#ttdbendaharabpbank').val(); // Pastikan ID ini sesuai
            let ttdpa_kpa = $('#ttdbpbank').val();
            let jenis_print = $(this).data("jenis");

            if (!ttdbendahara) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih bendahara!",
                    icon: "warning"
                });
                return;
            }

            if (!ttdpa_kpa) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih PA/KPA!",
                    icon: "warning"
                });
                return;
            }

            if (!kd_skpd) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih SKPD!",
                    icon: "warning"
                });
                return;
            }

            if (!tanggalawal || !tanggalakhir) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih rentang tanggal!",
                    icon: "warning"
                });
                return;
            }

            if (!tanggalTtd) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih tanggal tanda tangan!",
                    icon: "warning"
                });
                return;
            }

            let url = new URL("{{ route('laporan.laporan.cetakbpbank') }}", window.location.origin);
            url.searchParams.append("kd_skpd", kd_skpd);
            url.searchParams.append("tanggalawal", tanggalawal);
            url.searchParams.append("tanggalakhir", tanggalakhir);
            url.searchParams.append("tanggalTtd", tanggalTtd);
            url.searchParams.append("ttdbendaharadth", ttdbendahara); // Pastikan cocok dengan request controller
            url.searchParams.append("ttdpa_kpa", ttdpa_kpa);
            url.searchParams.append("jenis_print", jenis_print);

            window.open(url.toString(), "_blank");
        });


        $('.cetakbpp').on('click', function () {
            let kd_skpd = $('#kd_skpdrekapbpp').val();
            let tanggalawal = $('#tanggalawal1').val();
            let tanggalakhir = $('#tanggalakhir1').val();
            let tanggalTtd = $('#tanggalTtdrekapbpp').val();
            let ttdbendahara = $('#ttdbendaharabpp').val(); // Pastikan ID ini sesuai
            let ttdpa_kpa = $('#ttdbpp').val();
            let jenis_print = $(this).data("jenis");

            if (!kd_skpd) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih SKPD!",
                    icon: "warning"
                });
                return;
            }

            if (!tanggalawal || !tanggalakhir) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih rentang tanggal!",
                    icon: "warning"
                });
                return;
            }

            if (!tanggalTtd) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih tanggal tanda tangan!",
                    icon: "warning"
                });
                return;
            }

            let url = new URL("{{ route('laporan.laporan.cetakbpp') }}", window.location.origin);
            url.searchParams.append("kd_skpd", kd_skpd);
            url.searchParams.append("tanggalawal", tanggalawal);
            url.searchParams.append("tanggalakhir", tanggalakhir);
            url.searchParams.append("tanggalTtd", tanggalTtd);
            url.searchParams.append("ttdbendaharadth", ttdbendahara); // Pastikan cocok dengan request controller
            url.searchParams.append("ttdpa_kpa", ttdpa_kpa);
            url.searchParams.append("jenis_print", jenis_print);

            window.open(url.toString(), "_blank");
        });

        $('.cetakdth').on('click', function () {
            let kd_skpd = $('#kd_skpdrekapdth').val();
            let tanggalawal = $('#tanggalawaldth').val();
            let tanggalakhir = $('#tanggalakhirdth').val();
            let tanggalTtd = $('#tanggalTtdrekapdth').val();
            let ttdbendahara = $('#ttdbendaharadth').val(); // Pastikan ID ini sesuai
            let ttdpa_kpa = $('#ttdpa_kpa').val();
            let jenis_print = $(this).data("jenis");

            if (!kd_skpd) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih SKPD!",
                    icon: "warning"
                });
                return;
            }

            if (!tanggalawal || !tanggalakhir) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih rentang tanggal!",
                    icon: "warning"
                });
                return;
            }

            if (!tanggalTtd) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih tanggal tanda tangan!",
                    icon: "warning"
                });
                return;
            }

            if (!ttdbendahara) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih bendahara!",
                    icon: "warning"
                });
                return;
            }

            if (!ttdpa_kpa) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih PA/KPA!",
                    icon: "warning"
                });
                return;
            }

            let url = new URL("{{ route('laporan.laporan.cetakdth') }}", window.location.origin);
            url.searchParams.append("kd_skpd", kd_skpd);
            url.searchParams.append("tanggalawal", tanggalawal);
            url.searchParams.append("tanggalakhir", tanggalakhir);
            url.searchParams.append("tanggalTtd", tanggalTtd);
            url.searchParams.append("ttdbendaharadth", ttdbendahara); // Pastikan cocok dengan request controller
            url.searchParams.append("ttdpa_kpa", ttdpa_kpa);
            url.searchParams.append("jenis_print", jenis_print);

            window.open(url.toString(), "_blank");
        });

        $('.cetakrealisasi').on('click', function () {
            let kd_skpd = $('#kd_skpdrekaprealisasi').val();
            let tanggalawal = $('#tanggalawalrealisasi').val();
            let tanggalakhir = $('#tanggalakhirrealisasi').val();
            let tanggalTtd = $('#tanggalTtdrekaprealisasi').val();
            let ttdbendahara = $('#ttdbendahararealisasi').val(); // Pastikan ID ini sesuai
            let ttdpa_kpa = $('#ttdrealisasi').val();
            let jenis_print = $(this).data("jenis");

            if (!kd_skpd) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih SKPD!",
                    icon: "warning"
                });
                return;
            }

            if (!tanggalawal || !tanggalakhir) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih rentang tanggal!",
                    icon: "warning"
                });
                return;
            }

            if (!tanggalTtd) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih tanggal tanda tangan!",
                    icon: "warning"
                });
                return;
            }

            if (!ttdbendahara) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih bendahara!",
                    icon: "warning"
                });
                return;
            }

            if (!ttdpa_kpa) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih PA/KPA!",
                    icon: "warning"
                });
                return;
            }

            let url = new URL("{{ route('laporan.laporan.cetakrealisasi') }}", window.location.origin);
            url.searchParams.append("kd_skpd", kd_skpd);
            url.searchParams.append("tanggalawal", tanggalawal);
            url.searchParams.append("tanggalakhir", tanggalakhir);
            url.searchParams.append("tanggalTtd", tanggalTtd);
            url.searchParams.append("ttdbendaharadth", ttdbendahara); // Pastikan cocok dengan request controller
            url.searchParams.append("ttdpa_kpa", ttdpa_kpa);
            url.searchParams.append("jenis_print", jenis_print);

            window.open(url.toString(), "_blank");
        });

        $('.cetakobjek').on('click', function () {
            let kd_skpd = $('#kd_skpdrekapobjek').val();
            let tanggalawal = $('#tanggalawalobjek').val();
            let tanggalakhir = $('#tanggalakhirobjek').val();
            let tanggalTtd = $('#tanggalTtdrekapobjek').val();
            let ttdbendahara = $('#ttdbendaharaobjek').val(); // Pastikan ID ini sesuai
            let ttdpa_kpa = $('#ttdobjek').val();
            let jenis_anggaran = $('#jenis_anggaran').val();
            let sub_kegiatan = $('#sub_kegiatan').val(); // Pastikan ID ini sesuai
            let jenis = $('#jenis_objek').val();
            let akun_belanja = $('#akun_belanja').val();
            let jenis_print = $(this).data("jenis");

            if (!kd_skpd) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih SKPD!",
                    icon: "warning"
                });
                return;
            }

            if (!jenis_anggaran) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih Jenis Anggaran!",
                    icon: "warning"
                });
                return;
            }

            if (!sub_kegiatan) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih Sub Kegiatan!",
                    icon: "warning"
                });
                return;
            }

            if (!jenis) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih Jenis!",
                    icon: "warning"
                });
                return;
            }

            if (!akun_belanja) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih Akun Belanja!",
                    icon: "warning"
                });
                return;
            }

            if (!tanggalawal || !tanggalakhir) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih rentang tanggal!",
                    icon: "warning"
                });
                return;
            }

            if (!tanggalTtd) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih tanggal tanda tangan!",
                    icon: "warning"
                });
                return;
            }

            if (!ttdbendahara) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih bendahara!",
                    icon: "warning"
                });
                return;
            }

            if (!ttdpa_kpa) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih PA/KPA!",
                    icon: "warning"
                });
                return;
            }

            let url = new URL("{{ route('laporan.laporan.cetakobjek') }}", window.location.origin);
            url.searchParams.append("kd_skpd", kd_skpd);
            url.searchParams.append("tanggalawal", tanggalawal);
            url.searchParams.append("tanggalakhir", tanggalakhir);
            url.searchParams.append("tanggalTtd", tanggalTtd);
            url.searchParams.append("ttdbendaharadth", ttdbendahara); // Pastikan cocok dengan request controller
            url.searchParams.append("ttdpa_kpa", ttdpa_kpa);
            url.searchParams.append("jenis_anggaran", jenis_anggaran); // Pastikan cocok dengan request controller
            url.searchParams.append("sub_kegiatan", sub_kegiatan);
            url.searchParams.append("jenis", jenis); // Pastikan cocok dengan request controller
            url.searchParams.append("akun_belanja", akun_belanja);
            url.searchParams.append("jenis_print", jenis_print);

            window.open(url.toString(), "_blank");
        });


        $('.cetakspj').on('click', function () {
            let kd_skpd = $('#kd_skpdrekapspj').val();
            let tanggalawal = $('#tanggalawalspj').val();
            let tanggalakhir = $('#tanggalakhirspj').val();
            let tanggalTtd = $('#tanggalTtdrekapspj').val();
            let ttdbendahara = $('#ttdbendaharaspj').val(); // Pastikan ID ini sesuai
            let ttdpa_kpa = $('#ttdspj').val();
            let jenis_print = $(this).data("jenis");

            if (!kd_skpd) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih SKPD!",
                    icon: "warning"
                });
                return;
            }

            if (!tanggalawal || !tanggalakhir) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih rentang tanggal!",
                    icon: "warning"
                });
                return;
            }

            if (!tanggalTtd) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih tanggal tanda tangan!",
                    icon: "warning"
                });
                return;
            }

            if (!ttdbendahara) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih bendahara!",
                    icon: "warning"
                });
                return;
            }

            if (!ttdpa_kpa) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih PA/KPA!",
                    icon: "warning"
                });
                return;
            }

            let url = new URL("{{ route('laporan.laporan.cetakspj') }}", window.location.origin);
            url.searchParams.append("kd_skpd", kd_skpd);
            url.searchParams.append("tanggalawal", tanggalawal);
            url.searchParams.append("tanggalakhir", tanggalakhir);
            url.searchParams.append("tanggalTtd", tanggalTtd);
            url.searchParams.append("ttdbendaharadth", ttdbendahara); // Pastikan cocok dengan request controller
            url.searchParams.append("ttdpa_kpa", ttdpa_kpa);
            url.searchParams.append("jenis_print", jenis_print);

            window.open(url.toString(), "_blank");
        });


        $('.cetakrinciancp').on('click', function () {
            let kd_skpd = $('#kd_skpdrekaprinciancp').val();
            let tanggalawal = $('#tanggalawalrinciancp').val();
            let tanggalakhir = $('#tanggalakhirrinciancp').val();
            let tanggalTtd = $('#tanggalTtdrekaprinciancp').val();
            let ttdbendahara = $('#ttdbendahararinciancp').val(); // Pastikan ID ini sesuai
            let ttdpa_kpa = $('#ttdrinciancp').val();
            let jenis_print = $(this).data("jenis");

            if (!ttdbendahara) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih bendahara!",
                    icon: "warning"
                });
                return;
            }

            if (!ttdpa_kpa) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih PA/KPA!",
                    icon: "warning"
                });
                return;
            }

            if (!kd_skpd) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih SKPD!",
                    icon: "warning"
                });
                return;
            }

            if (!tanggalawal || !tanggalakhir) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih rentang tanggal!",
                    icon: "warning"
                });
                return;
            }

            if (!tanggalTtd) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silakan pilih tanggal tanda tangan!",
                    icon: "warning"
                });
                return;
            }

            let url = new URL("{{ route('laporan.laporan.cetakrinciancp') }}", window.location.origin);
            url.searchParams.append("kd_skpd", kd_skpd);
            url.searchParams.append("tanggalawal", tanggalawal);
            url.searchParams.append("tanggalakhir", tanggalakhir);
            url.searchParams.append("tanggalTtd", tanggalTtd);
            url.searchParams.append("ttdbendaharadth", ttdbendahara); // Pastikan cocok dengan request controller
            url.searchParams.append("ttdpa_kpa", ttdpa_kpa);
            url.searchParams.append("jenis_print", jenis_print);

            window.open(url.toString(), "_blank");
        });

    });
</script>
@endpush
