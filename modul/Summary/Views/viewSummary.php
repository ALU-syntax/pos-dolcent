<?php

use Config\Services;
use Config\Database;

$this->db         = Database::connect();
$this->session    = Services::session();

$id_toko = $this->session->get('id_toko');
$tgl = date('Y-m-d');
?>

<?php echo $this->extend('layout/template'); ?>
<?php echo $this->section('css') ?>
<link href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="/assets/extensions/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" />
<?php echo $this->endSection(); ?>
<?php echo $this->section('content'); ?>

<div class="page-heading">
    <h3>Ringkasan Penjualan</h3>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <label for="tgl" class="form-label">Filter By Tanggal</label>
        <input type="date" id="tgl" class="form-control">
    </div>
</div>

<div class="card p-4" id="summary">
    <h6 class="fw-bold">Ringkasan Hari Ini</h6>
    <div class="row mt-4">
        <div class="col-md-3 col-6">
            <p class="mb-0 fw-bold"><i class="fas fa-receipt me-2"></i>Total Transaksi</p>
            <p><?php echo $total; ?></p>
        </div>
        <div class="col-md-3 col-6">
            <p class="mb-0 fw-bold"><i class="fas fa-money-bill me-2"></i>Total Omset</p>
            <p>Rp. <?php echo number_format($omset); ?></p>
        </div>
        <div class="col-md-3 col-6">
            <p class="mb-0 fw-bold"><i class="fas fa-percentage me-2"></i>Total Discount</p>
            <p>Rp. <?php echo number_format($discount); ?></p>
        </div>
        <div class="col-md-3 col-6">
            <p class="mb-0 fw-bold"><i class="fas fa-wallet me-2"></i>Total Laba</p>
            <p>Rp. <?php echo number_format($laba); ?></p>
        </div>
        <div class="col-12">
            <hr>
        </div>
        <?php foreach ($tipe as $key) : ?>
            <?php $omsett    = $this->db->query("SELECT SUM(total) as total FROM penjualan WHERE id_toko = '$id_toko' AND DATE(tgl) = '$tgl' AND id_tipe_bayar = '$key->id'")->getRow()->total; ?>
            <div class="col-md-3 col-6">
                <p class="mb-0 fw-bold"><i class="<?php echo $key->icon; ?> me-2"></i><?php echo $key->nama_tipe; ?></p>
                <p>Rp. <?php echo number_format($omsett); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<section class="section">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Penjualan Terbaru</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive datatable-minimal">
                <div id="table2_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                    <div class="row dt-row">
                        <div class="col-sm-12">
                            <table class="table dataTable no-footer" id="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Pelanggan</th>
                                        <th>Metode Bayar</th>
                                        <th>Total</th>
                                        <th>Laba</th>
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

<?php echo $this->endSection() ?>
<?php echo $this->section('js') ?>
<script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/extensions/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="/assets/extensions/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>

<script>
    var table;

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
                url: '/summary/datatable',
                method: 'POST',
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    width: 10
                },
                {
                    data: 'tgl',
                    orderable: false,
                    width: 150
                },
                {
                    data: 'pelanggan',
                    orderable: false,
                    width: 150
                },
                {
                    data: 'metode',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'total',
                    orderable: false,
                    className: 'text-end',
                    width: 200
                },
                {
                    data: 'laba',
                    orderable: false,
                    className: 'text-end',
                    width: 200
                },
            ],
            language: {
                url: '/assets/extensions/bahasa/id.json',
            },

        });
    });

    $('#tgl').on('input', function() {
        var tgl = $(this).val();

        $.ajax({
            url: "/summary/filter",
            type: "POST",
            dataType: "JSON",
            data: {
                tgl: tgl,
            },
            beforeSend: function() {
                showblockUI();
            },
            complete: function() {
                hideblockUI();
            },
            success: function(response) {
                if (response.status) {
                    $('#summary').html(response.html);
                } else {
                    toastr.warning('Gagal mendapatkan data');
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
</script>
<?= $this->endSection() ?>