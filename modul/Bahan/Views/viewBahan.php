<?= $this->extend('layout/template'); ?>
<?= $this->section('css') ?>
<link href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="/assets/extensions/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" />
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div class="row">
        <div class="col-6">
            <h3>Daftar Bahan Baku</h3>
        </div>
        <div class="col-6 text-end">
            <button class="btn btn-primary" onclick="tambah()"><i class="fa fa-plus"></i>&nbsp; Tambah Bahan</button>
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
                                        <th>Nama Bahan</th>
                                        <th>Satuan</th>
                                        <th>Biaya</th>
                                        <th>Harga</th>
                                        <th>Stok Tersedia</th>
                                        <th>Stok Minimum</th>
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

<!-- Modal -->
<div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Bahan</label>
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama bahan baku">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="satuan" class="form-label">Satuan</label>
                        <select class="form-select" name="satuan" id="satuan">
                            <option value="" disabled selected>Pilih satuan</option>
                            <?php foreach ($satuan as $key) : ?>
                                <option value="<?php echo $key->id; ?>"><?php echo $key->nama_satuan; ?></option>
                            <?php endforeach ?>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="biaya" class="form-label">Biaya</label>
                        <input type="text" class="form-control harga" id="biaya" name="biaya" placeholder="Masukkan harga biaya bahan">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="text" class="form-control harga" id="harga" name="harga" placeholder="Masukkan harga bahan">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" placeholder="Masukkan stok yang tersedia">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="stokmin" class="form-label">Stok Minimum</label>
                        <input type="number" class="form-control" id="stokmin" name="stokmin" placeholder="Masukkan stok minimum (peringatan kuantitas)">
                        <div class="invalid-feedback"></div>
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

<!-- Modal delete -->
<div class="modal fade" id="modald" tabindex="-1" aria-labelledby="modaldLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaldLabel">Hapus data bahan baku "<span><strong id="bahan"></strong></span>"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Yakin ingin menghapus data tersebut?. Data yang telah dihapus tidak dapat dikembalikan lagi.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-delete">Ya, hapus</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal stok -->
<div class="modal fade" id="modals" tabindex="-1" aria-labelledby="modaldLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaldLabel">Kelola stok bahan baku "<span><strong class="bahan"></strong></span>"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0"><strong>Stok sekarang : <span id="stok-sekarang"></span></strong></p>
                <small class="text-muted">Stok min : <span id="stok-min"></span></small>
                <div id="list-stok">
                </div>
                <div id="aturstok" class="collapse">
                    <hr>
                    <div class="row">
                        <div class="col-8">
                            <select class="form-select" id="tipe" name="tipe">
                                <option value="1">Penambahan Stok</option>
                                <option value="2">Pengurangan Stok</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-4">
                            <input class="form-control nomor" type="number" id="jumlah" name="jumlah" placeholder="Qty">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <button class="btn btn-success mt-3 w-100" id="btnkonfirmasi">Konfirmasi</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary collapsed" data-bs-toggle="collapse" data-bs-target="#aturstok" aria-expanded="false" aria-controls="collapseExample"><i class="fas fa-sliders-h"></i>&nbsp; Atur Stok</button>
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

<script>
    var table;
    var modal = $('#modal');
    var modald = $('#modald');
    var modals = $('#modals');

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
                url: '/bahan/datatable',
                method: 'POST',
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    width: 10
                },
                {
                    data: 'nama_bahan',
                    orderable: false,
                    width: 250
                },
                {
                    data: 'nama_satuan',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'biaya',
                    orderable: false,
                    className: 'text-end',
                    width: 100
                },
                {
                    data: 'harga',
                    orderable: false,
                    className: 'text-end',
                    width: 200
                },
                {
                    data: 'stok_penjualan',
                    orderable: false,
                    className: 'text-end',
                    width: 200
                },
                {
                    data: 'stokmin',
                    orderable: false,
                    className: 'text-end',
                    width: 200
                },
                {
                    data: 'is_active',
                    orderable: false,
                },
                {
                    data: 'action',
                    orderable: false,
                    className: 'text-md-center',
                    width: 100
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

    function changeStatus(id) {
        var isChecked = $('#set_active' + id);
        $.ajax({
            type: "POST",
            url: "/bahan/setStatus",
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
                    isChecked.next().text($(isChecked).is(':checked') ? 'Aktif' : 'Nonaktif');
                    toastr.success('Data Berhasil Diperbaharui');
                } else {
                    isChecked.prop('checked', isChecked.is(':checked') ? null : 'checked');
                    toastr.error('Data gagal Diperbaharui');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                isChecked.prop('checked', isChecked.is(':checked') ? null : 'checked');

            },
        });
    }

    function tambah() {
        $('#id').val('');

        $('#form')[0].reset();
        var form = $('#form input, #form select');
        form.removeClass('is-invalid is-valid');

        $('#title').text('Tambah Bahan Baku');
        modal.modal('show');
    }

    function edit(id) {
        $.ajax({
            type: "POST",
            url: "/bahan/getdata",
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
                    $('#id').val(response.data.id);
                    $('#nama').val(response.data.nama_bahan);
                    $("#satuan").val(response.data.id_satuan).attr("selected", "selected");
                    $('#biaya').val(response.biaya);
                    $('#harga').val(response.harga);
                    $('#stok').val(response.data.stok);
                    $('#stokmin').val(response.data.stokmin);
                    $('#title').text('Edit Bahan Baku');
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

    function hapus(id, nama) {
        $('#bahan').text(nama);
        $('#btn-delete').attr('onclick', 'remove(' + id + ')');
        modald.modal('show');
    }

    function remove(id) {
        $.ajax({
            url: "/bahan/hapus",
            type: "POST",
            dataType: "JSON",
            data: {
                id: id
            },
            beforeSend: function() {
                showblockUI();
            },
            complete: function() {
                hideblockUI();
            },
            success: function(response) {
                if (response.status) {
                    toastr.success('Data Berhasil dihapus');
                    modald.modal('hide');
                    table.ajax.reload();
                } else {
                    toastr.warning('Maaf, anda tidak dapat menghapus data tersebut karna telah berelasi dengan data lain.');
                    modald.modal('hide');
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

    $('#form').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/bahan/simpan",
            data: $(this).serialize(),
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

    function stok(id) {
        $.ajax({
            type: "POST",
            url: "/bahan/getStokBahan",
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
                console.log(response)
                $('.bahan').text(response.data.nama_bahan);
                $('#stok-sekarang').text(response.data.stok_penjualan);
                $('#stok-min').text(response.data.stokmin);

                $('#list-stok').html(response.html);

                $('#btnkonfirmasi').attr('onclick', 'updateStok(' + response.data.id + ')');
                $('#jumlah').val('');
                $('.collapse').removeClass('show');
                $('#tipe, #jumlah').removeClass('is-invalid is-valid');
                modals.modal('show');
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

    function updateStok(id_bahan) {
        $.ajax({
            type: "POST",
            url: "/bahan/updateStokBahan",
            data: {
                id_bahan: id_bahan,
                tipe: $('#tipe').val(),
                jumlah: $('#jumlah').val()
            },
            dataType: "JSON",
            beforeSend: function() {
                showblockUI();
            },
            complete: function() {
                hideblockUI();
            },
            success: function(response) {
                console.log(response.data.jumlah);
                if (response.status) {
                    if (response.data.tipe == 1) {
                        var html = `<div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
                            <div class="card-body">
                                Penambahan Stok &nbsp;<span class="text-success">+` + response.jumlah + `</span>
                                <br>
                                <small style="font-size: x-small;">` + response.date + `</small>
                            </div>
                         </div>`;
                    } else {
                        var html = `<div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
                            <div class="card-body">
                                Pengurangan Stok &nbsp;<span class="text-danger">-` + response.jumlah + `</span>
                                <br>
                                <small style="font-size: x-small;">` + response.date + `</small>
                            </div>
                         </div>`;
                    }
                    toastr.success('Berhasil update stok');
                    $('#stok-sekarang').text(response.stok);
                    $('#nostok').hide();
                    $('#list-stok').append(html);
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
</script>
<?= $this->endSection() ?>