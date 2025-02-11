<?= $this->extend('layout/template'); ?>
<?= $this->section('css') ?>
<link href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="/assets/extensions/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" />

<link href="/assets/extensions/fancybox/fancybox.css" rel="stylesheet" />

<style>
    .img-preview {
        max-width: 100%;
        max-height: 100px;
    }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div class="row">
        <div class="col-6">
            <h3>Daftar Piutang</h3>
        </div>
        <div class="col-6 text-end">
            <button class="btn btn-primary" onclick="tambah()"><i class="fa fa-plus"></i>&nbsp; Tambah Piutang</button>
        </div>
    </div>
</div>

<section class="section">
    <div class="card">
        <div class="card-header">
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <div id="table1_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                    <div class="row dt-row">
                        <div class="col-sm-12">
                            <table class="table dataTable no-footer" id="table" aria-describedby="table1_info">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Pelanggan</th>
                                        <th>Jumlah</th>
                                        <th>Catatan</th>
                                        <th>Tanggal</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Foto</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form" autocomplete="off">
                    <input type="hidden" name="id" id="id">
                    <div class="mb-3">
                        <label for="pelanggan" class="form-label">Memberikan ke :</label>
                        <select class="form-select" name="pelanggan" id="pelanggan">
                            <option disabled selected>Pilih pelanggan</option>
                            <?php foreach ($pelanggan as $key) : ?>
                                <option value="<?php echo $key->id; ?>"><?php echo $key->nama; ?></option>
                            <?php endforeach ?>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Memberikan sejumlah</label>
                        <input type="text" class="form-control harga" id="jumlah" name="jumlah" placeholder="Masukkan jumlah piutang">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="foto" class="form-label">Upload Foto</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                        <div class="invalid-feedback"></div>
                        <p class="text-muted mt-1" style="font-size: x-small;">Silakan upload foto yang berkaitan dengan piutang ini.</p>
                        <div id="preview"></div>
                    </div>
                    <div class="mb-3">
                        <label for="foto" class="form-label">Catatan</label>
                        <input type="text" class="form-control" name="catatan" id="catatan" placeholder="Cth: Piutang pembelian pulsa"></input>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="tgl" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tgl" name="tgl">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="jt" class="form-label">Jatuh Tempo</label>
                        <input type="date" class="form-control" id="jt" name="jt">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input belum" type="radio" name="status" value="0">
                                    <label class="form-check-label">
                                        Belum lunas
                                    </label>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-check">
                                    <input class="form-check-input lunas" type="radio" name="status" value="1">
                                    <label class="form-check-label">
                                        Lunas
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Bayar-->
<div class="modal fade" id="modalb" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Daftar Bayar Piutang "<span class="pelanggan"></span>"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4">
                                Status : &nbsp;<span id="statuspiutang"></span>
                            </div>
                            <div class="col-4">
                                Sudah Bayar : &nbsp;<span id="sudahbayar" style="text-decoration: underline;"></span>
                            </div>
                            <div class="col-4">
                                Sisa Piutang : &nbsp;<span id="sisa" style="text-decoration: underline;"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body mt-4">
                    <div class="table-responsive">
                        <div id="table1_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                            <div class="row dt-row">
                                <div class="col-sm-12">
                                    <table class="table dataTable no-footer" id="table2" aria-describedby="table1_info">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal bayar</th>
                                                <th>Jumlah</th>
                                                <th>Foto</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="byr" class="collapse">
                    <hr>
                    <p class="text-center"><strong>Form Pembayaran Utang</strong></p>
                    <form action="javascript:void(0)" id="formbyr">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="jmlbyr" class="form-label">Jumlah Bayar</label>
                                    <input type="text" class="form-control harga" id="jmlbyr" name="jmlbyr" placeholder="Jumlah bayar">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="tglbyr" class="form-label">Tanggal Bayar</label>
                                    <input type="date" class="form-control" id="tglbyr" name="tglbyr">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="fotobyr" class="form-label">Upload Foto</label>
                                    <input type="file" class="form-control" id="fotobyr" name="fotobyr" accept="image/*">
                                    <div class="invalid-feedback"></div>
                                    <p class="text-muted mt-1" style="font-size: x-small;">Silakan upload foto yang berkaitan dengan pembayaran ini.</p>
                                    <div id="previewbyr"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <button class="btn btn-success mt-3 w-100" id="btnkonfirmasi">Konfirmasi Pembayaran</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary collapsed" data-bs-toggle="collapse" data-bs-target="#byr" aria-expanded="false" aria-controls="collapseExample" id="btnbayar"><i class="fas fa-money-bill-wave-alt"></i>&nbsp;&nbsp; Bayar</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal delete -->
<div class="modal fade" id="modald" tabindex="-1" aria-labelledby="modaldLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaldLabel">Hapus data piutang "<span id="piutanghapus"></span>"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Yakin ingin menghapus data tersebut?. Data yang sudah dihapus tidak dapat kembalikan lagi.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-delete">Ya, hapus</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('js') ?>
<script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/extensions/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="/assets/extensions/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>

<script src="/assets/extensions/fancybox/fancybox.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    var table;
    var table2;
    var modal = $('#modal');
    var modalb = $('#modalb');
    var modald = $('#modald');

    document.addEventListener("DOMContentLoaded", function() {
        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            info: true,
            paging: true,
            searching: true,
            stateSave: true,
            bDestroy: true,
            order: [],
            ajax: {
                url: '/piutang/datatable',
                method: 'POST',
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    width: 10
                },
                {
                    data: 'pelanggan',
                    orderable: false,
                    width: 200
                },
                {
                    data: 'jumlah',
                    orderable: false,
                    className: 'text-end',
                    width: 200
                },
                {
                    data: 'catatan',
                    orderable: false,
                    width: 400
                },
                {
                    data: 'tgl',
                    orderable: false,
                },
                {
                    data: 'jt',
                    orderable: false,
                },
                {
                    data: 'foto',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'action',
                    orderable: false,
                    className: 'text-center',
                    width: 150
                },
            ],
            language: {
                url: '/assets/extensions/bahasa/id.json',
            },

        });
    });

    $(document).ready(function() {
        $(".harga").keyup(function(e) {
            $(this).val(formatRupiah($(this).val(), "Rp. "));
        });
    });

    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, "").toString(),
            split = number_string.split(","),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }

        rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
    }

    function tambah() {
        $('#id').val('');
        $('#preview').empty();
        $('.lunas').removeAttr('checked', 'checked');
        $('.belum').attr('checked', 'checked');

        var today = new Date().toISOString().slice(0, 10);
        $('#tgl').attr("value", today);

        $('#form')[0].reset();
        var form = $('#form input, #form select');
        form.removeClass('is-invalid is-valid');

        $('#title').text('Tambah data piutang');
        modal.modal('show');
    }

    function edit(id) {
        $.ajax({
            type: "POST",
            url: "/piutang/getdata",
            data: {
                id: id
            },
            dataType: "JSON",
            beforeSend: function() {
                showblockUI();
            },
            complete: function() {
                hideblockUI();
            },
            success: function(response) {
                if (response.status) {
                    var form = $('#form input, #form select');
                    form.removeClass('is-invalid is-valid');

                    $('#foto').val(null);
                    $('#preview').html('<img src="/assets/img/piutang/' + response.data.foto + '" alt="Preview Gambar" class="img-preview rounded">');

                    $('#id').val(response.data.id);
                    $('#pelanggan').val(response.data.id_pelanggan).attr('selected', 'selected');
                    $('#jumlah').val('Rp. ' + response.data.jumlah);
                    $('#catatan').val(response.data.catatan);
                    $('#tgl').val(response.data.tgl);
                    $('#jt').val(response.data.jt);
                    if (response.data.status == 0) {
                        $('.belum').attr('checked', 'checked');
                    } else {
                        $('.lunas').attr('checked', 'checked');
                    }
                    $('#title').text('Edit data piutang');
                    modal.modal('show');
                }
            },
            error: function(jqXHR, textStatus, errorThrown, exception) {
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            }
        });
    }

    function hapus(id, foto, catatan) {
        $('#btn-delete').attr('onclick', 'remove(' + id + ', \'' + foto + '\')');
        $('#piutanghapus').text(catatan);
        modald.modal('show');
    }

    function remove(id, foto) {
        $.ajax({
            url: "/piutang/hapus",
            type: "POST",
            dataType: "JSON",
            data: {
                id: id,
                foto: foto
            },
            beforeSend: function() {
                showblockUI();
            },
            complete: function() {
                hideblockUI();
            },
            success: function(data) {
                toastr.success('Data Berhasil dihapus');
                modald.modal('hide');
                table.ajax.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            }
        });
    }

    $('#foto').on('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(event) {
                var imgSrc = event.target.result;
                $('#preview').html('<img src="' + imgSrc + '" alt="Preview Gambar" class="img-preview rounded">');
            };
            reader.readAsDataURL(file);
        }
    });

    $('#fotobyr').on('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(event) {
                var imgSrc = event.target.result;
                $('#previewbyr').html('<img src="' + imgSrc + '" alt="Preview Gambar" class="img-preview rounded">');
            };
            reader.readAsDataURL(file);
        }
    });

    $('#form').submit(function(e) {
        e.preventDefault();
        var form = $('#form')[0];
        var formData = new FormData(form);
        $.ajax({
            type: "POST",
            url: "/piutang/simpan",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "JSON",
            beforeSend: function() {
                showblockUI();
            },
            complete: function() {
                hideblockUI();
            },
            success: function(response) {
                if (response.status) {
                    $('#form')[0].reset();
                    table.ajax.reload();
                    toastr.success(response.notif);
                    modal.modal('hide');
                } else {
                    $.each(response.errors, function(key, value) {
                        $('[name="' + key + '"]').addClass('is-invalid');
                        $('[name="' + key + '"]').next().text(value);
                        if (value == "") {
                            $('[name="' + key + '"]').removeClass('is-invalid');
                            $('[name="' + key + '"]').addClass('is-valid');
                        }
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown, exception) {
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            }
        });
    });

    // Function Bayar
    function bayar(id, pelanggan, status, bayar, sisa) {
        // Data Piutang
        if (status == 0) {
            $('#statuspiutang').html('<span class="badge bg-light-warning">Belum lunas</span>');
        } else {
            $('#statuspiutang').html('<span class="badge bg-light-success">Lunas</span>');
        }

        $('#sudahbayar').text(bayar);
        $('#sisa').text(sisa);

        // Setting Form & Modal
        $('.pelanggan').text(pelanggan);

        $('#jmlbyr').val('');
        $('#fotobyr').val(null);
        var today = new Date().toISOString().slice(0, 10);
        $('#tglbyr').attr("value", today);
        $('#previewbyr').empty();

        $('.collapse').removeClass('show');
        $('#tglbyr, #jmlbyr, #fotobyr').removeClass('is-invalid is-valid');
        modalb.modal('show');

        // Datatable
        tablebyr = $('#table2').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            info: true,
            paging: true,
            searching: false,
            stateSave: true,
            bDestroy: true,
            order: [],
            ajax: {
                url: '/piutang/datatable_byr',
                method: 'POST',
                data: function(d) {
                    d.piutang = id;
                }
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    width: 10
                },
                {
                    data: 'tgl_bayar',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'jumlah',
                    orderable: false,
                    className: 'text-end',
                    width: 150
                },
                {
                    data: 'foto',
                    orderable: false,
                    width: 200
                },
                {
                    data: 'action',
                    orderable: false,
                    className: 'text-center',
                    width: 50
                },
            ],
            language: {
                url: '/assets/extensions/bahasa/id.json',
            },
        });

        // Form Bayar
        $('#btnkonfirmasi').attr('onclick', 'updateBayar(' + id + ')');
        if (status == 1) {
            $('#btnbayar').attr('disabled', 'disabled');
        } else {
            $('#btnbayar').removeAttr('disabled', 'disabled');
        }
    }

    function updateBayar(id_utang) {
        var form = $('#formbyr')[0];
        var formData = new FormData(form);
        formData.append('id_piutang', id_utang);
        $.ajax({
            type: "POST",
            url: "/piutang/updateBayar",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "JSON",
            beforeSend: function() {
                showblockUI();
            },
            complete: function() {
                hideblockUI();
            },
            success: function(response) {
                if (response.status_jml == false) {
                    toastr.warning('Jumlah bayar melebihi sisa!')
                }
                if (response.status) {
                    toastr.success('Berhasil update pembayaran');
                    // Setting Form & Modal
                    $('#jmlbyr').val('');
                    $('#fotobyr').val(null);
                    var today = new Date().toISOString().slice(0, 10);
                    $('#tglbyr').attr("value", today);
                    $('#previewbyr').empty();
                    $('#formbyr')[0].reset();
                    $('.collapse').removeClass('show');

                    $('#sudahbayar').text(response.sudahbyr);
                    $('#sisa').text(response.sisa);

                    if (response.lunas) {
                        $('#btnbayar').attr('disabled', 'disabled').addClass('disable');
                        $('#statuspiutang').html('<span class="badge bg-light-success">Lunas</span>');
                    }

                    tablebyr.ajax.reload();
                    table.ajax.reload();
                } else {
                    $.each(response.errors, function(key, value) {
                        $('[name="' + key + '"]').addClass('is-invalid');
                        $('[name="' + key + '"]').next().text(value);
                        if (value == "") {
                            $('[name="' + key + '"]').removeClass('is-invalid');
                            $('[name="' + key + '"]').addClass('is-valid');
                        }
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown, exception) {
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            }
        });
    }

    function hapusBayar(id, jumlah, foto, id_piutang) {
        Swal.fire({
            title: 'Hapus Data Ini?',
            text: "Tindakan ini akan berpengaruh pada status piutang",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/piutang/hapusBayar",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id: id,
                        jumlah: jumlah,
                        id_piutang: id_piutang,
                        foto: foto
                    },
                    beforeSend: function() {
                        showblockUI();
                    },
                    complete: function() {
                        hideblockUI();
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Data berhasil dihapus',
                                showConfirmButton: false,
                                timer: 1000
                            })
                            tablebyr.ajax.reload();
                            table.ajax.reload();

                            $('#statuspiutang').html('<span class="badge bg-light-warning">Belum lunas</span>');
                            $('#sudahbayar').text(response.sudahbyr);
                            $('#sisa').text(response.sisa);
                            $('#btnbayar').removeAttr('disabled', 'disabled');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        var msg = '';
                        if (jqXHR.status === 0) {
                            msg = 'Not connect.\n Verify Network.';
                        } else if (jqXHR.status == 404) {
                            msg = 'Requested page not found. [404]';
                        } else if (jqXHR.status == 500) {
                            msg = 'Internal Server Error [500].';
                        } else if (exception === 'parsererror') {
                            msg = 'Requested JSON parse failed.';
                        } else if (exception === 'timeout') {
                            msg = 'Time out error.';
                        } else if (exception === 'abort') {
                            msg = 'Ajax request aborted.';
                        } else {
                            msg = 'Uncaught Error.\n' + jqXHR.responseText;
                        }
                        alert(msg);
                    }
                });
            }
        })
    }
</script>
<?= $this->endSection() ?>