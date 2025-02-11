<?php

use Config\Services;

$this->session    = Services::session();

?>

<?php echo $this->extend('layout/template'); ?>
<?php echo $this->section('css') ?>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<style>
    .zoom {
        transition: transform .2s;
    }

    .zoom:hover {
        transform: scale(0.93);
        border: 1px solid black;
    }

    * {
        font-size: 62, 5%;
        box-sizing: border-box;
        margin: 0;
    }

    main {
        width: 40rem;
        height: 43rem;
        background-color: #ffffff;
        -webkit-box-shadow: 0px 5px 15px 8px #e4e7fb;
        box-shadow: 0px 5px 15px 8px #e4e7fb;
        display: flex;
        flex-direction: column;
        align-items: center;
        border-radius: 0.5rem;
    }

    #header {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 2.5rem 2rem;
    }

    h1 {
        font-family: "Rubik", sans-serif;
        font-size: 1.7rem;
        color: #141a39;
        text-transform: uppercase;
        cursor: default;
    }

    #leaderboard {
        width: 100%;
        position: relative;
    }

    #table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        color: #141a39;
        cursor: default;
    }

    #table tr {
        transition: all 0.2s ease-in-out;
        border-radius: 0.2rem;
    }

    #table tr:not(:first-child):hover {
        background-color: #fff;
        transform: scale(1.1);
        -webkit-box-shadow: 0px 5px 15px 8px #e4e7fb;
        box-shadow: 0px 5px 15px 8px #e4e7fb;
    }

    #table tr:nth-child(odd) {
        background-color: #f9f9f9;
    }

    #table tr:nth-child(1) {
        color: #fff;
    }

    #table td {
        height: 5rem;
        font-family: "Rubik", sans-serif;
        font-size: 1.4rem;
        padding: 1rem 2rem;
        position: relative;
    }

    .number {
        width: 1rem;
        font-size: 2.2rem;
        font-weight: bold;
        text-align: left;
    }

    .name {
        text-align: left;
        font-size: 1.2rem;
    }

    .points {
        font-weight: bold;
        font-size: 1.3rem;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .points:first-child {
        width: 10rem;
    }

    .gold-medal {
        height: 3rem;
        margin-left: 1.5rem;
    }

    .ribbon {
        width: 42rem;
        height: 5.5rem;
        top: -0.5rem;
        background-color: #5c5be5;
        position: absolute;
        left: -1rem;
        -webkit-box-shadow: 0px 15px 11px -6px #7a7a7d;
        box-shadow: 0px 15px 11px -6px #7a7a7d;
    }

    .ribbon::before {
        content: "";
        height: 1.5rem;
        width: 1.5rem;
        bottom: -0.8rem;
        left: 0.35rem;
        transform: rotate(45deg);
        background-color: #5c5be5;
        position: absolute;
        z-index: -1;
    }

    .ribbon::after {
        content: "";
        height: 1.5rem;
        width: 1.5rem;
        bottom: -0.8rem;
        right: 0.35rem;
        transform: rotate(45deg);
        background-color: #5c5be5;
        position: absolute;
        z-index: -1;
    }

    #buttons {
        width: 100%;
        margin-top: 3rem;
        display: flex;
        justify-content: center;
        gap: 2rem;
    }

    .exit {
        width: 11rem;
        height: 3rem;
        font-family: "Rubik", sans-serif;
        font-size: 1.3rem;
        text-transform: uppercase;
        color: #7e7f86;
        border: 0;
        background-color: #fff;
        border-radius: 2rem;
        cursor: pointer;
    }

    .exit:hover {
        border: 0.1rem solid #5c5be5;
    }

    .continue {
        width: 11rem;
        height: 3rem;
        font-family: "Rubik", sans-serif;
        font-size: 1.3rem;
        color: #fff;
        text-transform: uppercase;
        background-color: #5c5be5;
        border: 0;
        border-bottom: 0.2rem solid #3838b8;
        border-radius: 2rem;
        cursor: pointer;
    }

    .continue:active {
        border-bottom: 0;
    }

    @media (max-width: 740px) {
        * {
            font-size: 70%;
        }
    }

    @media (max-width: 500px) {
        * {
            font-size: 55%;
        }
    }

    @media (max-width: 390px) {
        * {
            font-size: 45%;
        }
    }
</style>
<?php echo $this->endSection(); ?>
<?php echo $this->section('content'); ?>

<center>
    <label for="daterange" class="me-2">Filter Periode :</label>
    <input type="text" id="daterange" name="daterange" class="mb-4" />
    <button class="btn btn-primary ms-2" onclick="filter()"><i class="fas fa-filter me-2"></i>Filter</button>

    <main>
        <div id="header">
            <h1>Produk Best Seller</h1>
            <?php if ($this->session->get('logo')) { ?>
                <img src="/assets/img/logo/<?php echo $this->session->get('logo') ?>" height="60">
            <?php } else { ?>
                <img src="/assets/compiled/jpg/1.jpg" height="60">
            <?php } ?>
        </div>
        <div id="leaderboard">
            <div class="ribbon"></div>
            <table id="table">
                <?php $no = 0;
                foreach ($best as $key) :
                    $no++ ?>
                    <?php if ($no == 1) { ?>
                        <tr>
                            <td class="number">1</td>
                            <td class="name"><?php echo $key->nama_barang; ?> - <?php echo $key->nama_satuan; ?></td>
                            <td class="points">
                                <?php echo $key->total; ?> <img class="gold-medal" src="https://github.com/malunaridev/Challenges-iCodeThis/blob/master/4-leaderboard/assets/gold-medal.png?raw=true" alt="gold medal" />
                            </td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td class="number"><?php echo $no; ?></td>
                            <td class="name"><?php echo $key->nama_barang; ?> - <?php echo $key->nama_satuan; ?></td>
                            <td class="points"><?php echo $key->total; ?></td>
                        </tr>
                    <?php } ?>
                <?php endforeach; ?>
            </table>
            <p class="fw-bold d-none" id="nodata"></p>
        </div>
    </main>
</center>

<?php echo $this->endSection() ?>
<?php echo $this->section('js') ?>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    $(function() {
        $('input[name="daterange"]').daterangepicker({
            opens: 'left',
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
    });

    function filter() {
        var periode = $('#daterange').val();
        var dateArray = periode.split(" - ");
        var startDate = new Date(dateArray[0]);
        var endDate = new Date(dateArray[1]);

        $.ajax({
            url: "/bestseller/filter",
            type: "POST",
            dataType: "JSON",
            data: {
                start: dateArray[0],
                end: dateArray[1]
            },
            beforeSend: function() {
                showblockUI();
            },
            complete: function() {
                hideblockUI();
            },
            success: function(response) {
                if (response.status) {
                    $('#nodata').addClass('d-none');
                    $('.ribbon, #table').removeClass('d-none');
                    $('#table').html(response.html);
                } else {
                    $('.ribbon, #table').addClass('d-none');
                    $('#nodata').text('Tidak ada data penjualan pada periode ' + response.start + ' - ' + response.end).removeClass('d-none');
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