<?= $this->extend('layout/template'); ?>
<?= $this->section('css') ?>
<link href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="/assets/extensions/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
            <h3>Daftar Kredit Barang</h3>
        </div>
        <div class="col-6 text-end">
            <a class="btn btn-primary" href="/kredit/add"><i class="fa fa-plus"></i>&nbsp; Tambah Kredit</a>
        </div>
    </div>
</div>

<div class="card p-4">
    <div class="row">
        <div class="col-md-3 mb-4 mb-md-0">
            <select class="form-control" id="pelanggan">
                <option disabled selected>Filter by Pelanggan</option>
                <?php foreach ($pelanggan as $key) : ?>
                    <option value="<?php echo $key->id; ?>"><?php echo $key->nama; ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-3 mb-4 mb-md-0">
            <select class="form-control" id="status">
                <option disabled selected>Filter by Status</option>
                <option value="0">Belum Lunas</option>
                <option value="1">Lunas</option>
            </select>
        </div>
        <div class="col-md-3 mb-4 mb-md-0">
            <input class="form-control" type="date" id="tgl">
        </div>
        <div class="col-md-3">
            <div class="d-flex justify-content-end">
                <button class="btn btn-secondary" id="reset">Reset Filter</button>
            </div>
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
                                        <th>Subtotal Barang</th>
                                        <th>Total</th>
                                        <th>Uang Muka</th>
                                        <th>Tanggal Kredit</th>
                                        <th>Periode</th>
                                        <th>Status</th>
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

<!-- Modal delete -->
<div class="modal fade" id="modald" tabindex="-1" aria-labelledby="modaldLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaldLabel">Hapus data kredit</h5>
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

<!-- Modal Jadwal Bayar-->
<div class="modal fade" id="modalj" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Daftar Jadwal Bayar Kredit "<span class="pelanggan"></span>"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4">
                                Status : &nbsp;<span class="statusutang"></span>
                            </div>
                            <div class="col-4 text-center">
                                Sudah Bayar : &nbsp;<span class="sudahbayar" style="text-decoration: underline;"></span>
                            </div>
                            <div class="col-4 text-end">
                                Sisa Kredit : &nbsp;<span class="sisa" style="text-decoration: underline;"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="id_utang" id="id_utang">
                <div class="card-body mt-4">
                    <div class="table-responsive">
                        <div id="table1_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                            <div class="row dt-row">
                                <div class="col-sm-12">
                                    <table class="table dataTable no-footer" id="table2" aria-describedby="table1_info">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Jatuh Tempo</th>
                                                <th>Status</th>
                                                <th>Total Tagihan</th>
                                                <th>Sudah Bayar</th>
                                                <th>Sisa Tagihan</th>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Bayar-->
<div class="modal fade" id="modalb" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Daftar Bayar Kredit "<span class="pelanggan"></span>"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4">
                                Status : &nbsp;<span class="statusutang"></span>
                            </div>
                            <div class="col-4 text-center">
                                Sudah Bayar : &nbsp;<span class="sudahbayar" style="text-decoration: underline;"></span>
                            </div>
                            <div class="col-4 text-end">
                                Sisa Kredit : &nbsp;<span class="sisa" style="text-decoration: underline;"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body mt-4">
                    <div class="table-responsive">
                        <div id="table1_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                            <div class="row dt-row">
                                <div class="col-sm-12">
                                    <table class="table dataTable no-footer" id="table3">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Periode Pembayaran</th>
                                                <th>Tanggal Bayar</th>
                                                <th>Nominal</th>
                                                <th>Foto Bukti</th>
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
                <hr>
                <form action="javascript:void(0)" id="form">
                    <div class="row mt-4">
                        <input type="hidden" id="id_kredit" name="id_kredit">
                        <h5 class="fw-bold mb-3">Form bayar kredit</h5>
                        <div class="col-md-4 mb-3">
                            <label for="periode" class="form-label">Periode</label>
                            <br>
                            <select name="periode" id="periode" class="form-control" required></select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="nominal" class="form-label">Nominal</label>
                            <input type="text" class="form-control harga" name="nominal" id="nominal" placeholder="Rp. 0" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="foto" class="form-label">Unggah Foto</label>
                            <input type="file" class="form-control" name="foto" id="foto" accept="image/*">
                            <div class="invalid-feedback"></div>
                            <div id="preview" class="mt-3"></div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Bayar Kredit</button>
            </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('js') ?>
<script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/extensions/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="/assets/extensions/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="/assets/extensions/fancybox/fancybox.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    var table;
    var modal = $('#modal');
    var modalj = $('#modalj');
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
                url: '/kredit/datatable',
                method: 'POST',
                data: function(d) {
                    d.pelanggan = $('#pelanggan').val();
                    d.status = $('#status').val();
                    d.tgl = $('#tgl').val();
                }
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    width: 10
                },
                {
                    data: 'pelanggan',
                    orderable: false,
                    width: 50
                },
                {
                    data: 'subtotal',
                    orderable: false,
                    className: 'text-end',
                    width: 100
                },
                {
                    data: 'total',
                    orderable: false,
                    className: 'text-end',
                    width: 100
                },
                {
                    data: 'dp',
                    orderable: false,
                    className: 'text-end',
                    width: 100
                },
                {
                    data: 'tgl_kredit',
                    orderable: false,
                    width: 50
                },
                {
                    data: 'periode',
                    orderable: false,
                    width: 50
                },
                {
                    data: 'status',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'action',
                    orderable: false,
                    className: 'text-md-center',
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

    function hapus(id) {
        $('#btn-delete').attr('onclick', 'remove(' + id + ')');
        modald.modal('show');
    }

    function remove(id, foto) {
        $.ajax({
            url: "/kredit/hapus",
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

    function jadwal_bayar(id, pelanggan, status, bayar, sisa) {
        // Data Hutang
        if (status == 0) {
            $('.statusutang').html('<span class="badge bg-light-warning">Belum lunas</span>');
        } else {
            $('.statusutang').html('<span class="badge bg-light-success">Lunas</span>');
        }

        $('.sudahbayar').text(bayar);
        $('.sisa').text(sisa);

        // Setting Form & Modal
        $('.pelanggan').text(pelanggan);

        modalj.modal('show');

        // Datatable
        tablejadwal = $('#table2').DataTable({
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
                url: '/kredit/datatable_jadwal',
                method: 'POST',
                data: function(d) {
                    d.kredit = id;
                }
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    width: 10
                },
                {
                    data: 'jt',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'status',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'nominal',
                    orderable: false,
                    className: 'text-end',
                    width: 100
                },
                {
                    data: 'sudah_byr',
                    orderable: false,
                    className: 'text-end',
                    width: 100
                },
                {
                    data: 'sisa',
                    orderable: false,
                    className: 'text-end',
                    width: 100
                },
            ],
            language: {
                url: '/assets/extensions/bahasa/id.json',
            },
        });
    }

    function bayar(id, pelanggan, status, bayar, sisa) {
        if (pelanggan) {
            // Form Reset
            $('#preview').empty();
            $('#periode').val(null).trigger('change');
            $('#form')[0].reset();
            var form = $('#form input, #form select');
            form.removeClass('is-invalid is-valid');

            // Data Hutang
            if (status == 0) {
                $('.statusutang').html('<span class="badge bg-light-warning">Belum lunas</span>');
            } else {
                $('.statusutang').html('<span class="badge bg-light-success">Lunas</span>');
            }

            $('.sudahbayar').text(bayar);
            $('.sisa').text(sisa);

            // Setting Form & Modal
            $('.pelanggan').text(pelanggan);

            $("#id_kredit").val(id);

            modalb.modal('show');
        }

        // Datatable
        tablebyr = $('#table3').DataTable({
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
                url: '/kredit/datatable_byr',
                method: 'POST',
                data: function(d) {
                    d.kredit = id;
                }
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    width: 10
                },
                {
                    data: 'periode',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'tgl_bayar',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'nominal',
                    orderable: false,
                    className: 'text-end',
                    width: 100
                },
                {
                    data: 'foto',
                    orderable: false,
                    className: 'text-center',
                    width: 100
                },
                {
                    data: 'action',
                    orderable: false,
                    className: 'text-center',
                    width: 100
                },
            ],
            language: {
                url: '/assets/extensions/bahasa/id.json',
            },
        });
    }

    function batal_bayar(id, id_kredit, ipk, foto) {
        if (confirm("Yakin akan menghapus data tersebut ?") == true) {
            $.ajax({
                type: "POST",
                url: "/kredit/batal_bayar",
                data: {
                    id: id,
                    id_kredit: id_kredit,
                    ipk: ipk,
                    foto: foto
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
                        $('#preview').empty();
                        $('#periode').val(null).trigger('change');
                        $('#form')[0].reset();
                        bayar(response.data.id);
                        table.ajax.reload();
                        toastr.success("Pembayaran berhasil di hapus");

                        if (response.data.status == 0) {
                            $('.statusutang').html('<span class="badge bg-light-warning">Belum lunas</span>');
                        } else {
                            $('.statusutang').html('<span class="badge bg-light-success">Lunas</span>');
                        }

                        $('.sudahbayar').text(response.bayar);
                        $('.sisa').text(response.sisa);
                    } else {
                        toastr.warning('Maaf, gagal menghapus data');
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
        } else {}
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

    $('#periode').select2({
        placeholder: 'Pilih periode pembayaran',
        allowClear: false,
        dropdownParent: modalb,
        width: '100%',
        language: {
            noResults: function() {
                return "Data tidak ditemukan";
            }
        },
        ajax: {
            type: "POST",
            url: "/kredit/getPeriode",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    id_kredit: $('#id_kredit').val()
                }
            },
            processResults: function(data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });

    $('#form').submit(function(e) {
        e.preventDefault();
        var form = $('#form')[0];
        var formData = new FormData(form);
        $.ajax({
            type: "POST",
            url: "/kredit/bayar",
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
                    $('#preview').empty();
                    $('#periode').val(null).trigger('change');
                    $('#form')[0].reset();
                    bayar(response.data.id);
                    table.ajax.reload();
                    toastr.success("Pembayaran berhasil di update");
                    var form = $('#form input, #form select');
                    form.removeClass('is-invalid is-valid');

                    if (response.data.status == 0) {
                        $('.statusutang').html('<span class="badge bg-light-warning">Belum lunas</span>');
                    } else {
                        $('.statusutang').html('<span class="badge bg-light-success">Lunas</span>');
                    }

                    $('.sudahbayar').text(response.bayar);
                    $('.sisa').text(response.sisa);
                } else if (response.status_cek == false) {
                    $('[name="nominal"]').addClass('is-invalid');
                    $('[name="nominal"]').next().text("Nominal tidak boleh melebihi sisa wajib bayar : " + response.sisa);
                    $('[name="periode"]').removeClass('is-invalid').addClass('is-valid');

                } else {
                    $.each(response.errors, function(key, value) {
                        $('[name="' + key + '"]').addClass('is-invalid');
                        if (key == 'periode') {
                            $('[name="' + key + '"]').next().next().text(value);
                        } else {
                            $('[name="' + key + '"]').next().text(value);
                        }
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

    $('#pelanggan').on('change', function() {
        table.ajax.reload();
    });

    $('#status').on('change', function() {
        table.ajax.reload();
    });

    $('#tgl').on('change', function() {
        table.ajax.reload();
    });

    $('#reset').on('click', function() {
        $('#pelanggan').prop('selectedIndex', 0);
        $('#status').prop('selectedIndex', 0);
        $('#tgl').val('');

        table.ajax.reload();
    });
</script>
<?= $this->endSection() ?>