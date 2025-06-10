<?php

use Config\Services;

$this->session    = Services::session();

?>
<?= $this->extend('layout/template'); ?>
<?= $this->section('css') ?>

<link href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="/assets/extensions/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" />
<style>
    .avatar-upload {
        position: relative;
        max-width: 205px;
        margin: 50px auto;
    }

    .avatar-upload .avatar-edit {
        position: absolute;
        right: 12px;
        z-index: 1;
        top: 10px;
    }

    .avatar-upload .avatar-edit input {
        display: none;
    }

    .avatar-upload .avatar-edit input+label {
        display: inline-block;
        width: 34px;
        height: 34px;
        margin-bottom: 0;
        border-radius: 100%;
        background: #FFFFFF;
        border: 1px solid transparent;
        box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
        cursor: pointer;
        font-weight: normal;
        transition: all 0.2s ease-in-out;
    }

    .avatar-upload .avatar-edit input+label:hover {
        background: #f1f1f1;
        border-color: #d6d6d6;
    }

    .avatar-upload .avatar-edit input+label:after {
        content: "\f044";
        font-family: 'Font Awesome\ 5 Free';
        color: #757575;
        position: absolute;
        top: 10px;
        left: 0;
        right: 0;
        text-align: center;
        margin: auto;
    }

    .avatar-upload .avatar-preview {
        width: 192px;
        height: 192px;
        position: relative;
        border-radius: 100%;
        border: 6px solid #F8F8F8;
        box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
    }

    .avatar-upload .avatar-preview>div {
        width: 100%;
        height: 100%;
        border-radius: 100%;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div class="row">
        <div class="col-6">
            <h3>Toko</h3>
        </div>
        <div class="col-6 text-end">
            <button class="btn btn-primary" onclick="tambah()"><i class="fa fa-plus"></i>&nbsp; Tambah Toko</button>
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
                                        <th>Nama Toko</th>
                                        <th>Email</th>
                                        <th>Nomor Telfon</th>
                                        <th>Alamat</th>
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
<div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="form">
                    <input type="hidden" name="id" id="id">
                    <section class="section">
                        <div class="card p-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="avatar-upload">
                                        <div class="avatar-edit">
                                            <input type='file' id="imageUpload" accept=".png, .jpg, .jpeg"
                                                name="logo" />
                                            <label for="imageUpload"></label>
                                        </div>
                                        <div class="avatar-preview">
                                            <div id="imagePreview"
                                                style="background-image: url('/assets/compiled/jpg/1.jpg');">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama" class="form-label">Nama Toko</label>
                                        <input type="text" class="form-control" name="nama" id="nama">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="nohp" class="form-label">Nomor HP</label>
                                        <input type="text" class="form-control" name="nohp" id="nohp">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" id="email">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="ppn" class="form-label">PPN (%)</label>
                                        <input type="number" class="form-control" name="ppn" id="ppn">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-1">
                                        <label for="alamat" class="form-label">Alamat</label>
                                        <textarea name="alamat" id="alamat" placeholder="Masukkan alamat lengkap toko"
                                            class="form-control"></textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan</button>
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
                <h5 class="modal-title" id="modaldLabel">Hapus data toko "<span><strong id="toko"></strong></span>"</h5>
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

    document.addEventListener("DOMContentLoaded", function () {
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
                url: '/setting/datatable',
                method: 'POST',
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    width: 10
                },
                {
                    data: 'nama_toko',
                    orderable: false,
                    width: 150
                },
                {
                    data: 'email',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'nohp',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'alamat',
                    orderable: false,
                    width: 200
                },
                {
                    data: 'action',
                    orderable: false,
                    className: 'text-md-center',
                    width: 50
                },
            ],
            language: {
                url: '/assets/extensions/bahasa/id.json',
            },

        });

    });

    function tambah() {
        $('#id').val('');

        $('#form')[0].reset();
        var form = $('#form input');
        form.removeClass('is-invalid is-valid');
        $('#imagePreview').css('background-image', 'url(/assets/compiled/jpg/1.jpg)');

        $('#title').text('Tambah data toko');
        modal.modal('show');
    }

    $('#form').submit(function (e) {
        e.preventDefault();
        var form = $('#form')[0];
        var formData = new FormData(form);
        $.ajax({
            type: "POST",
            url: "/setting/simpan",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "JSON",
            beforeSend: function () {
                showblockUI();
            },
            complete: function () {
                hideblockUI();
            },
            success: function (response) {
                if (response.status) {
                    $('#form')[0].reset();
                    table.ajax.reload();
                    toastr.success(response.notif);
                    modal.modal('hide');
                } else {
                    $.each(response.errors, function (key, value) {
                        $('[name="' + key + '"]').addClass('is-invalid');
                        $('[name="' + key + '"]').next().text(value);
                        if (value == "") {
                            $('[name="' + key + '"]').removeClass('is-invalid');
                            $('[name="' + key + '"]').addClass('is-valid');
                        }
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown, exception) {
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

    function edit(id){
        $.ajax({
            type: "POST",
            url: "/setting/getData",
            data: { id: id },
            dataType: "JSON",
            success: function (response) {
                console.log(response);
                if (response.status) {
                    $('#id').val(response.data.id);
                    $('#title').text('Edit Data Toko');
                    $('#nama').val(response.data.nama_toko);
                    $('#nohp').val(response.data.nohp);
                    $('#email').val(response.data.email);
                    $('#alamat').val(response.data.alamat);
                    $('#ppn').val(response.data.ppn);
                    if (response.data.logo) {
                        $('#imagePreview').css('background-image', 'url(/assets/img/logo/' + response.data.logo + ')');
                    } else {
                        $('#imagePreview').css('background-image', 'url(/assets/compiled/jpg/1.jpg)');
                    }
                    modal.modal('show');
                } else {
                    toastr.error(response.errors);
                }
            },
            error: function (jqXHR, textStatus, errorThrown, exception) {
                alert('Error retrieving data');
            }
        });
    }

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                $('#imagePreview').hide();
                $('#imagePreview').fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#imageUpload").change(function () {
        readURL(this);
    });

    function hapus(id, nama) {
        $('#toko').text(nama);
        $('#btn-delete').attr('onclick', 'remove(' + id + ')');
        modald.modal('show');
    }

    function remove(id) {
        $.ajax({
            url: "/setting/hapus",
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
</script>
<?= $this->endSection() ?>