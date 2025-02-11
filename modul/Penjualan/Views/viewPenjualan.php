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
    <h3>Daftar Penjualan</h3>
</div>


<div class="card p-4">
    <div class="row">
        <div class="col-md-5 mb-4 mb-md-0">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <select class="form-control" id="pelanggan">
                            <option disabled selected>Filter by Pelanggan</option>
                            <?php foreach ($pelanggan as $key) : ?>
                                <option value="<?php echo $key->id; ?>"><?php echo $key->nama; ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <select class="form-control" id="metode">
                            <option disabled selected>Filter by Metode</option>
                            <?php foreach ($metode as $key) : ?>
                                <option value="<?php echo $key->id; ?>"><?php echo $key->nama_tipe; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-12">
                        <input class="form-control" type="date" id="tgl">
                    </div>

                    <div class="col-md-12 mt-2">
                        <button class="btn btn-primary w-100" id="reset">Reset Filter</button>
                    </div>
                </div>
            </div>
            
        </div>
    
        <div class="col-md-5 offset-md-2">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <label for="tglbyr" class="form-label">Dari</label>
                        <input type="date" class="form-control mb-3" id="dari" name="dari">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="tglbyr" class="form-label">Sampai</label>
                        <input type="date" class="form-control mb-3" id="sampai" name="sampai">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <form action="<?= base_url('penjualan/export-excel') ?>" method="POST" >
                            <input type="text" name="dariPenjualan" id="dariPenjualan" hidden>
                            <input type="text" name="sampaiPenjualan" id="sampaiPenjualan" hidden>
                            <button class="btn btn-primary me-2 w-100" type="submit">Export Excel</button>
                        </form>
                    </div>
                </div>
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
                                        <th>Tanggal</th>
                                        <th>Metode Bayar</th>
                                        <th>Subtotal</th>
                                        <th>PPN</th>
                                        <th>Discount</th>
                                        <th>Total</th>
                                        <th>Laba</th>
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
                <h5 class="modal-title" id="modaldLabel">Hapus data penjualan</h5>
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

<script>
    var table;
    var modal = $('#modal');
    var modald = $('#modald');

    var startDateInput = document.getElementById("dari");
    var endDateInput = document.getElementById("sampai");

    // Event listener untuk start date
    startDateInput.addEventListener("change", function() {
        var startDate = startDateInput.value;
        let dariLabarugi = document.getElementById("dariPenjualan");

        dariLabarugi.value = startDate;
        console.log("Start Date changed to:", startDate);
    });

    // Event listener untuk end date
    endDateInput.addEventListener("change", function() {
        var endDate = endDateInput.value;
        let sampaiLabarugi = document.getElementById("sampaiPenjualan");

        sampaiLabarugi.value = endDate;
        console.log("End Date changed to:", endDate);
    });

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
                url: '/penjualan/datatable',
                method: 'POST',
                data: function(d) {
                    d.pelanggan = $('#pelanggan').val();
                    d.kategori = $('#kategori').val();
                    d.metode = $('#metode').val();
                    d.tgl = $('#tgl').val();
                }
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    width: 10
                },
                {
                    data: 'tgl',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'metode',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'subtotal',
                    orderable: false,
                    className: 'text-end',
                    width: 100
                },
                {
                    data: 'ppn',
                    orderable: false,
                    className: 'text-end',
                    width: 100
                },
                {
                    data: 'discount',
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
                    data: 'laba',
                    orderable: false,
                    className: 'text-end',
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
    });

    function hapus(id, foto) {
        $('#btn-delete').attr('onclick', 'remove(' + id + ', "' + foto + '")');
        modald.modal('show');
    }

    function remove(id, foto) {
        $.ajax({
            url: "/penjualan/hapus",
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

    $('#pelanggan').on('change', function() {
        table.ajax.reload();
    });

    $('#metode').on('change', function() {
        table.ajax.reload();
    });

    $('#tgl').on('change', function() {
        table.ajax.reload();
    });

    $('#reset').on('click', function() {
        $('#pelanggan').prop('selectedIndex', 0);
        $('#metode').prop('selectedIndex', 0);
        $('#tgl').val('');

        table.ajax.reload();
    });
</script>
<?= $this->endSection() ?>