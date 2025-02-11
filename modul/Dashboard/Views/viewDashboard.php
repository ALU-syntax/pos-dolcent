<?php

use Config\Services;

$this->session    = Services::session();

?>

<?= $this->extend('layout/template'); ?>
<?= $this->section('css') ?>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <h3>Dashboard</h3>
</div>

<div class="page-content">
    <div class="row">
        <div class="col-md-3 mt-4">
            <label for="filter" class="form-label fw-bold"><i class="bi bi-funnel me-2"></i>Filter Ringkasan</label>
            <select id="filter" class="form-control">
                <option value="0">Semua periode</option>
                <option value="1">Hari ini</option>
                <option value="2">Minggu ini</option>
                <option value="3">Bulan ini</option>
                <option value="4">Tahun ini</option>
            </select>
        </div>
        <div class="col-md-6 offset-md-3 d-flex justify-content-end pt-4 mb-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <label for="tglbyr" class="form-label">Dari</label>
                        <input type="date" class="form-control mb-4" id="dari" name="dari">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="tglbyr" class="form-label">Sampai</label>
                        <input type="date" class="form-control mb-4" id="sampai" name="sampai">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <form action="<?= base_url('dashboard/export-excel-balance') ?>" method="POST" >
                            <input type="text" name="dariBalance" id="dariBalance" hidden>
                            <input type="text" name="sampaiBalance" id="sampaiBalance" hidden>
                            <button class="btn btn-primary me-2" type="submit">Export Excel Balance</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form action="<?= base_url('dashboard/export-excel-labarugi') ?>" method="POST" >
                            <input type="text" name="dariLabarugi" id="dariLabarugi" hidden>
                            <input type="text" name="sampaiLabarugi" id="sampaiLabarugi" hidden>
                            <button class="btn btn-primary me-2" type="submit">Export Excel Laba Rugi</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-6 col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon blue mb-2">
                                        <i class="bi bi-currency-dollar" style="display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">
                                        Total Omset
                                    </h6>
                                    <h6 class="font-extrabold mb-0" id="omset">Rp. <?php echo $omset; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon green mb-2">
                                        <i class="bi bi-cash-coin" style="display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Laba</h6>
                                    <h6 class="font-extrabold mb-0" id="laba">Rp. <?php echo $laba; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon purple mb-2">
                                        <i class="bi bi-bag-check" style="display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Transaksi</h6>
                                    <h6 class="font-extrabold mb-0" id="trx"><?php echo $trx; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon black mb-2">
                                        <i class="bi bi-percent" style="display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Diskon</h6>
                                    <h6 class="font-extrabold mb-0" id="discount">Rp. <?php echo $discount; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon blue mb-2">
                                        <i class="bi bi-cash-stack" style="display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Transaksi Cash</h6>
                                    <h6 class="font-extrabold mb-0" id="cash"><?php echo $cash; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon green mb-2">
                                        <i class="bi bi-wallet2" style="display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Transaksi Non Tunai</h6>
                                    <h6 class="font-extrabold mb-0" id="noncash"><?php echo $noncash; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon purple mb-2">
                                        <i class="bi bi-cash-coin" style="display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Pemasukan</h6>
                                    <h6 class="font-extrabold mb-0" id="pemasukan">Rp. <?php echo $pemasukan; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon black mb-2">
                                        <i class="bi bi-graph-up-arrow" style="display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Pengeluaran</h6>
                                    <h6 class="font-extrabold mb-0" id="pengeluaran">Rp. <?php echo $pengeluaran; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon blue mb-2">
                                        <i class="bi bi-box-fill" style="display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Biaya Produksi</h6>
                                    <h6 class="font-extrabold mb-0" id="biaya-produksi">Rp. <?php echo $biayaProduksi; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Grafik Omset</h4>
                </div>
                <div class="card-body">
                    <div id="chart-profile-visit"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('js') ?>
<!-- Need: Apexcharts -->
<script src="/assets/extensions/apexcharts/apexcharts.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    $(document).ready(function() {
        var grafik = <?php echo $grafik; ?>;
        var dates = <?php echo $dates; ?>;

        var startDateInput = document.getElementById("dari");
        var endDateInput = document.getElementById("sampai");

        // Event listener untuk start date
        startDateInput.addEventListener("change", function() {
            var startDate = startDateInput.value;
            let dariLabarugi = document.getElementById("dariLabarugi");
            let dariBalance = document.getElementById("dariBalance");

            dariLabarugi.value = startDate;
            dariBalance.value = startDate;
            console.log("Start Date changed to:", startDate);
        });

        // Event listener untuk end date
        endDateInput.addEventListener("change", function() {
            var endDate = endDateInput.value;
            let sampaiLabarugi = document.getElementById("sampaiLabarugi");
            let sampaiBalance = document.getElementById("sampaiBalance");

            sampaiLabarugi.value = endDate;
            sampaiBalance.value = endDate;
            console.log("End Date changed to:", endDate);
        });

        var optionsProfileVisit = {
            annotations: {
                position: "back",
            },
            dataLabels: {
                enabled: false,
            },
            chart: {
                type: "bar",
                height: 300,
            },
            fill: {
                opacity: 1,
            },
            plotOptions: {},
            series: [{
                name: "Omset",
                data: grafik,
            }, ],
            colors: "#435ebe",
            xaxis: {
                categories: dates
            },
        }
        var chartProfileVisit = new ApexCharts(
            document.querySelector("#chart-profile-visit"),
            optionsProfileVisit
        )
        chartProfileVisit.render()

    });

    $('#filter').change(function() {
        $.ajax({
            url: '/dashboard/getRingkasan',
            type: 'POST',
            dataType: 'JSON',
            data: {
                filter: $(this).val()
            },
            beforeSend: function() {
                showblockUI();
            },
            complete: function() {
                hideblockUI();
            },
            success: function(response) {
                $('#omset').text(response.omset);
                $('#laba').text(response.laba);
                $('#trx').text(response.trx);
                $('#discount').text(response.discount);
                $('#cash').text(response.cash);
                $('#noncash').text(response.noncash);
                $('#pemasukan').text(response.pemasukan);
                $('#pengeluaran').text(response.pengeluaran);
                $('#biaya-produksi').text(response.biayaProduksi);
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