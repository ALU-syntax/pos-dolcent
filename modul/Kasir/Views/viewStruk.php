<?php
$date = new DateTime($penjualan->tgl);
$date = $date->format('d F Y, H:i');

use Config\Services;

$this->session    = Services::session();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="Struk pembelian <?php echo $penjualan->nama_toko; ?>" />
    <meta property="og:description" content="Rincian pembelian anda di <?php echo $penjualan->nama_toko; ?>" />
    <meta property="og:url" content="https://bukutoko.alifalghifari.site" />
    <meta property="og:image" content="/assets/img/struk.jpg" />
    <title>Struk Penjualan - CIL SPACE</title>
    <link rel="stylesheet" href="/assets/pos/css/struk.css">
    <link rel="icon" href="/assets/img/your-logo.png">
    <link rel="stylesheet" href="/assets/extensions/%40fortawesome/fontawesome-free/css/all.min.css" />
</head>

<body>

    <div class="container" id="DivIdToPrint">
        <?php if ($this->session->get('logo')) : ?>
            <center>
                <img src="/assets/img/logo/<?php echo $this->session->get('logo'); ?>" alt="" height="70">
            </center>
        <?php endif; ?>
        <div class="receipt_header">
            <h1>Struk Penjualan <span><?php echo $penjualan->nama_toko; ?></span></h1>
            <h2 style="margin-bottom: 2px;"><span>Kasir: <?php echo $penjualan->kasir; ?></span></h2>
            <h2 style="margin-bottom: 2px;"><span><?php echo $penjualan->nohp; ?></span></h2>
            <h2><span><?php echo $penjualan->alamat; ?></span></h2>
        </div>

        <div class="receipt_body">

            <div class="date_time_con">
                <div class="date"><?php echo $date; ?></div>
            </div>

            <div class="items">
                <table>

                    <thead>
                        <th>QTY</th>
                        <th>ITEM</th>
                        <th>TOTAL</th>
                    </thead>
                    <hr>
                    <br>

                    <tbody>
                        <?php foreach ($detail as $key) : ?>
                            <tr>
                                <td><?php echo $key->qty; ?></td>
                                <td><?php echo $key->barang; ?></td>
                                <td><?php echo number_format($key->total); ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                    <tfoot>
                        <?php if ($penjualan->pelanggan) : ?>
                            <tr>
                                <td>Customer</td>
                                <td></td>
                                <td><?php echo $penjualan->pelanggan; ?></td>
                            </tr>
                        <?php endif ?>

                        <tr>
                            <td>Pembayaran</td>
                            <td></td>
                            <td><?php echo $penjualan->nama_tipe; ?></td>
                        </tr>
                    </tfoot>
                    <tfoot>
                        <tr>
                            <td style="font-weight: normal;">Harga</td>
                            <td></td>
                            <td style="font-weight: normal;">Rp. <?php echo number_format($penjualan->subtotal); ?></td>
                        </tr>
                        <tr>
                            <td>Discount</td>
                            <td></td>
                            <td>Rp. <?php echo number_format($penjualan->discount); ?></td>
                        </tr>
                        <tr>
                            <td>PPN</td>
                            <td></td>
                            <td>Rp. <?php echo number_format($penjualan->ppn); ?></td>
                        </tr>
                        <tr>
                            <td>Biaya Layanan</td>
                            <td></td>
                            <td>Rp. <?php echo number_format($penjualan->biaya_layanan); ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Total</td>
                            <td></td>
                            <td style="font-weight: bold;">Rp. <?php echo number_format($penjualan->total); ?></td>
                        </tr>
                    </tfoot>

                </table>
            </div>

        </div>

        <h3>Terimakasih!</h3>

    </div>

    <center>
        <button type="button" onclick="print_struk()" class="btnprint" style="margin-top: 20px; padding: 10px; cursor: pointer;"><i class="fas fa-print"></i> &nbsp; Print Struk</button>
    </center>

    <script src="/assets/extensions/jquery/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            window.addEventListener('beforeprint', function() {
                $('.btnprint').hide();
            });

            window.addEventListener('afterprint', function() {
                $('.btnprint').show();
            });

            window.print(); // Memulai proses pencetakan
        });

        $('.btnprint').click(function() {
            window.print();
        });
    </script>
</body>

</html>