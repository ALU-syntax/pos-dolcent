<?php

use Config\Database;
use Config\Services;
use Modul\Bahan\Controllers\Bahan;

$this->db         = Database::connect();
$this->session    = Services::session();

$id_toko = $this->session->get('id_toko');

?>

<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <title>CIL space POS</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="/assets/img/logo/<?php echo $this->session->get('logo'); ?>" type="image/x-icon" />
    <link rel="stylesheet" href="/assets/pos/css/struk.css">
    <!--<link rel="icon" href="/assets/img/your-logo.png">-->
    <!--<link rel="stylesheet" href="/assets/extensions/%40fortawesome/fontawesome-free/css/all.min.css" />-->

    <link href="/assets/pos/css/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/assets/pos/css/style.bundle.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link href="/assets/extensions/toastr/toastr.min.css" rel="stylesheet" />

    <link href="/assets/extensions/select2/dist/css/select2.min.css" rel="stylesheet" />
    <link href="/assets/extensions/select2-bootstrap-5-theme-1.3.0/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link href="/assets/extensions/select2-bootstrap-5-theme-1.3.0/select2-bootstrap-5-theme.rtl.min.css" rel="stylesheet" />

    <link href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="/assets/extensions/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" />
    
    <!--<link rel="stylesheet" href="/assets/pos/css/struk.css">-->


    <style>
        .prod-icon {
            background-color: #dbdbdb;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;

            font-size: 70px;
        }

        .disabled-div {
            pointer-events: none;
            opacity: 0.5;
        }

        .cat {
            margin: 4px;
            background-color: green;
            border-radius: 10px;
            overflow: hidden;
            float: left;
        }

        .cat label {
            float: left;
            line-height: 2.5em;
            width: 8.0em;
            height: 3.0em;
            cursor: pointer !important;
        }

        .cat label span {
            text-align: center;
            padding: 3px 0;
            display: block;
        }

        .cat label input {
            position: absolute;
            display: none;
            color: #fff !important;
        }

        .cat label input+span {
            color: #fff;
        }

        .cat input:checked+span {
            color: #ffffff;
            text-shadow: 0 0 6px rgba(0, 0, 0, 0.8);
        }

        .action input:checked+span {
            background-color: red;
        }

        .comedy input:checked+span {
            background-color: #1BB8F7;
        }

        .crime input:checked+span {
            background-color: #D9D65D;
        }

        .history input:checked+span {
            background-color: #82D44E;
        }

        .reality input:checked+span {
            background-color: #F3A4CF;
        }

        .news input:checked+span {
            background-color: #8C1B1B;
        }

        .scifi input:checked+span {
            background-color: #AC9BD1;
        }

        .sports input:checked+span {
            background-color: #214A09;
        }

        .img-preview {
            max-width: 100%;
            max-height: 100px;
        }  
    </style>

</head>

<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed aside-fixed aside-secondary-disabled">
    <script>
        var defaultThemeMode = "light";
        var themeMode;

        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }

            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }

            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>
    <div id="session-data" data-logo="<?php echo $this->session->get('logo'); ?>"></div>
    <div class="d-flex flex-column flex-root">
        <div class="page d-flex flex-row flex-column-fluid">
            <div id="kt_aside" class="aside " data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="auto" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_toggle">
                <div class="aside-logo flex-column-auto pt-10 pt-lg-20" id="kt_aside_logo">
                    <a href="javascript:void(0)">
                        <img alt="Logo" src="/assets/img/logo/<?php echo $this->session->get('logo'); ?>" height="30" />
                    </a>
                </div>
                <div class="aside-menu flex-column-fluid pt-0 pb-7 py-lg-10" id="kt_aside_menu">
                </div>
                <div class="aside-footer flex-column-auto pb-5 pb-lg-10" id="kt_aside_footer">
                    <div class="d-flex flex-center w-100 scroll-px">
                        <a href="javascript:void(0);" class="btn btn-custom" onclick="showPettyCashModal()" id="pattyCashBtn"><i class="fas fa-cash-register"></i></a>
                    </div>
                </div>
                <div class="aside-footer flex-column-auto pb-5 pb-lg-10" id="kt_aside_footer">
                    <div class="d-flex flex-center w-100 scroll-px">
                        <a href="javascript:void(0);" class="btn btn-custom" id="fullScreenBtn"><i class="fas fa-tv"></i></a>
                    </div>
                </div>
                <div class="aside-footer flex-column-auto pb-5 pb-lg-10" id="kt_aside_footer">
                    <div class="d-flex flex-center w-100 scroll-px">
                        <a href="javascript:void(0);" class="btn btn-custom" onclick="datatable()" title="Histori Transaksi"><i class="fas fa-history"></i></a>
                    </div>
                </div>
                <div class="aside-footer flex-column-auto pb-5 pb-lg-10" id="kt_aside_footer">
                    <div class="d-flex flex-center w-100 scroll-px">
                        <a href="<?php echo $redirect; ?>" class="btn btn-custom"><i class="fas fa-power-off"></i></a>
                    </div>
                </div>
            </div>

            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                <div class="header-mobile py-3">
                    <div class="container d-flex flex-stack">
                        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                            <img alt="Logo" src="/assets/img/logo/<?php echo $this->session->get('logo'); ?>" class="h-35px" />
                        </div>
                        <button class="btn btn-icon btn-active-color-primary me-n4" id="kt_aside_toggle">
                            <i class="fas fa-stream fs-2x"><span class="path1"></span><span class="path2"></span></i> </button>
                    </div>
                </div>

                <div id="kt_header" class="header  py-6 py-lg-0" data-kt-sticky="true" data-kt-sticky-name="header" data-kt-sticky-offset="{lg: '300px'}">
                    <div class="header-container  container-xxl ">
                        <div class="page-title d-flex flex-column align-items-start justify-content-center flex-wrap me-lg-20 py-3 py-lg-0 me-3">
                            <h1 class="d-flex flex-column text-dark fw-bold my-1">
                                <span class="text-white fs-1">
                                    Kasir <?php echo $this->session->get('nama_toko'); ?> </span>
                            </h1>
                        </div>
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="dropdown" >
                                <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class="">
                                    <div class="user-menu d-flex">
                                        <i class="fas fa-bell fs-2 me-4" style="z-index: 999; color: #ffffff;">
                                            <span class="path1" id="notif">
                                            <span class="path2" id="notif-2"></span>
                                        </i>
                                    </div>
                                </a>
                                <ul id="list-notif" class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton" style="min-width: 11rem; max-height: 400px; overflow-y: auto;">
                                    <li>
                                        <a href="#">
                                            <div class="notif-content" style="width: 300px; padding: 20px;">
                                                <b>Tidak Ada Pesanan</b>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        
                            <div class="header-search py-3 py-lg-0 me-3">
                                <div id="kt_header_search" class="header-search d-flex align-items-center w-lg-250px" data-kt-search-keypress="true" data-kt-search-min-length="2" data-kt-search-enter="enter" data-kt-search-layout="menu" data-kt-search-responsive="false" data-kt-menu-trigger="auto" data-kt-menu-permanent="true" data-kt-menu-placement="bottom-end">
                                    <form data-kt-search-element="form" class="w-100 position-relative" autocomplete="off">
                                        <input type="hidden" />
                                        <i class="fas fa-search fs-2 search-icon position-absolute top-50 translate-middle-y ms-4"><span class="path1"></span><span class="path2"></span></i>
                                        <input type="text" class="form-control custom-form-control ps-13" name="search" value="" placeholder="Temukan barang" id="search" />
                                        </span>
                                        <span class="btn btn-flush btn-active-color-primary position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-4">
                                            <i class="ki-duotone ki-cross fs-2 fs-lg-1 me-0"><span class="path1"></span><span class="path2"></span></i> </span>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="header-offset"></div>
                </div>

                <!--begin::Content-->
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class=" container-xxl " id="kt_content_container">
                        <div class="d-flex flex-column flex-xl-row">
                            <div class="d-flex flex-row-fluid me-xl-9 mb-10 mb-xl-0">
                                <div class="card card-flush card-p-0 bg-transparent border-0 ">
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <select name="kategori" id="kategori" class="form-control" style="margin-bottom: 6%;">
                                                <option value="">Filter Produk By Kategori</option>
                                                <?php foreach ($kategori as $key) :
                                                    $jmlbarang = $this->db->query("SELECT COUNT(id) as total FROM barang WHERE id_kategori = '$key->id' AND id_toko = '$id_toko' AND status = 1")->getRow()->total; ?>
                                                    <option value="<?php echo $key->id; ?>"><?php echo $key->nama_kategori; ?> (<?php echo $jmlbarang; ?>)</option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="tab-pane fade show active" id="kt_pos_food_content_1">
                                                <div class="d-flex flex-wrap d-grid gap-5 gap-xxl-9" id="product">
                                                    <?php foreach ($barang as $key) :
                                                        $varian = $this->db->query("SELECT id FROM varian WHERE id_barang = '$key->id' AND status = 1")->getRow();
                                                        $totalv = $this->db->query("SELECT COUNT(id) as total FROM varian WHERE id_barang = '$key->id' AND status = 1")->getRow()->total;
                                                        $bahan = $this->db->query("SELECT SUM(b.harga * a.qty) as harga, SUM(b.biaya * a.qty) as biaya FROM bahan_barang a JOIN bahan_baku b ON a.id_bahan_baku = b.id WHERE a.id_barang = '$key->id'")->getRow();
                                                        if ($bahan) {
                                                            // $harga_jual = $key->harga_jual + $bahan->harga; saat ini harga jual tetap harga jual tanpa akumulasi dari penambahan harga bahan
                                                            $harga_jual = $key->harga_jual;
                                                            // $harga_modal = $key->harga_modal + $bahan->biaya; saat ini harga modal tetap harga modal tanpa akumulasi dari penambahan harga bahan
                                                            $harga_modal = $key->harga_modal;
                                                        } else {
                                                            $harga_jual = $key->harga_jual;
                                                            $harga_modal = $key->harga_modal;
                                                        } ?>
                                                        <div class="card card-flush flex-row-fluid p-6 pb-5 mw-100 <?php echo $key->nama_barang; ?> barang" style="width: 180px; cursor: pointer;" data-nama="<?php echo $key->nama_barang; ?>">
                                                            <?php if ($totalv >= 1) { ?>
                                                                <div class="card-body text-center" onclick="varian(<?= $key->id; ?>, '<?= $key->nama_barang; ?>')">
                                                                <?php } else { ?>
                                                                    <div class="card-body text-center" onclick="add_barang(<?= $key->id; ?>, '<?= $key->nama_barang; ?>', '<?= $harga_jual; ?>', '<?= $harga_modal; ?>')">
                                                                    <?php } ?>
                                                                    <?php if ($key->foto) { ?>
                                                                        <img src="/assets/img/barang/<?php echo $key->foto; ?>" alt="Foto Barang" class="rounded mb-4" style="height: 120px;">
                                                                    <?php } else { ?>
                                                                        <h1 class="prod-icon"><?php echo substr($key->nama_barang, 0, 1) ?></h1>
                                                                    <?php } ?>
                                                                    <div class="mb-2">
                                                                        <div class="text-center">
                                                                            <?php if ($totalv >= 1) { ?> <span class="fw-bold text-gray-800 cursor-pointer text-hover-primary fs-3 fs-xl-1" onclick="varian(<?= $key->id; ?>, '<?= $key->nama_barang; ?>')"><?php echo $key->nama_barang; ?></span>
                                                                            <?php } else { ?>
                                                                                <span class="fw-bold text-gray-800 cursor-pointer text-hover-primary fs-3 fs-xl-1" onclick="add_barang(<?= $key->id; ?>, '<?= $key->nama_barang; ?>', '<?= $harga_jual; ?>', '<?= $harga_modal; ?>')"><?php echo $key->nama_barang; ?></span>
                                                                            <?php } ?>

                                                                            <span class="text-gray-400 fw-semibold d-block fs-6 mt-n1"><?php echo $totalv; ?> Varian</span>
                                                                        </div>
                                                                    </div>

                                                                    <span class="text-success text-end fw-bold fs-1"></span>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach ?>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Content-->

                                <!--begin::Sidebar-->
                                <div class="flex-row-auto w-xl-450px">
                                    <form action="javascript:void(0)" id="form">
                                        <div class="card card-flush bg-body " id="kt_pos_form">
                                            <div class="card-header pt-5">
                                                <h3 class="card-title fw-bold text-gray-800 fs-2qx">Pesanan Saat Ini</h3>

                                                <div class="card-toolbar">
                                                    <button type="button" class="btn btn-light-primary fs-4 fw-bold py-4" onclick="clear_all()">Clear All</button>
                                                </div>

                                                <select class="form-control" id="pelanggan" name="pelanggan">
                                                </select>

                                                <button class="btn btn-primary btn-sm mt-4 w-100" type="button" onclick="tambah()" style="margin-bottom: 20px !important;"><i class="fas fa-user-plus"></i> Tambah Pelanggan</button>
                                            </div>

                                            <!--begin::Body-->
                                            <div class="card-body pt-0">
                                                <div class="table-responsive mb-8">
                                                    <table class="table align-middle gs-0 gy-4 my-0">
                                                        <thead>
                                                            <tr>
                                                                <th class="min-w-175px"></th>
                                                                <th class="w-125px"></th>
                                                                <th class="w-60px"></th>
                                                            </tr>
                                                        </thead>

                                                        <tbody id="pesanan">
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="d-flex flex-stack rounded-3 p-6 mb-11" style="background-color: #3b527f;">
                                                    <div class="fs-6 fw-bold text-white">
                                                        <span class="d-block mb-2">Subtotal</span>
                                                        <span class="d-block mb-2">PPN(<?php echo $toko->ppn; ?>%)</span>
                                                        <span class="d-block mb-2">Biaya Layanan</span>
                                                        <span class="d-block mb-2">Discount</span>
                                                        <br>
                                                        <span class="d-block fs-2qx lh-1">Total</span>
                                                    </div>

                                                    <div class="fs-6 fw-bold text-white text-end">
                                                        <span class="d-block mb-2" id="subtotal">Rp 0</span>
                                                        <span class="d-block mb-2" id="ppn">Rp 0</span>
                                                        <span class="d-block mb-2" id="biaya">Rp <?php echo number_format($toko->biaya_layanan, 0, ',', '.'); ?></span>
                                                        <span class="d-block mb-2" id="discountval">Rp 0</span>
                                                        <br>
                                                        <span class="d-block fs-2qx lh-1" id="grant-total">Rp 0</span>
                                                        <input type="hidden" name="granttotal" id="grant-total2" value="0">
                                                        <input type="hidden" name="subtotal" id="subtotal2" value="0">
                                                        <input type="hidden" name="discount2" id="discount2" value="0">
                                                        <input type="hidden" name="ppn" id="ppn2" value="0">
                                                        <input type="hidden" id="tipedc" value="0">
                                                    </div>
                                                </div>


                                                <select class="form-select mb-4 mt-4" name="discount" id="discount" disabled>
                                                    <option value="">Pilih Discount</option>
                                                    <?php foreach ($discount as $key) : ?>
                                                        <?php if ($key->tipe == 1) { ?>
                                                            <option value="<?php echo $key->id; ?>"><?php echo $key->nama_discount; ?> ( <?php echo $key->jumlah; ?>% )</option>
                                                        <?php } else { ?>
                                                            <option value="<?php echo $key->id; ?>"><?php echo $key->nama_discount; ?> ( Rp.<?php echo number_format($key->jumlah); ?> )</option>
                                                        <?php } ?>
                                                    <?php endforeach ?>
                                                </select>
                                                <br>

                                                <!--begin::Payment Method-->
                                                <div class="m-0">
                                                    <h1 class="fw-bold text-gray-800 mb-5">Metode Pembayaran</h1>

                                                    <div class="row mb-12" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                                        <?php $no = 0;
                                                        foreach ($bayar as $key) :
                                                            $no++; ?>
                                                            <div class="col-md-4 col-6 mb-4">
                                                                <label class="btn bg-light btn-color-gray-600 btn-active-text-gray-800 border border-3 border-gray-100 border-active-primary btn-active-light-primary w-100 px-4 <?php if ($no == 1) : ?> active<?php endif; ?>" data-kt-button="true" id="cash">
                                                                    <input class="btn-check" type="radio" name="method" value="<?php echo $key->id; ?>" <?php if ($no == 1) : ?> checked <?php endif; ?> />
                                                                    <i class="<?php echo $key->icon; ?> fs-2hx mb-2 pe-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                                                    <span class="fs-7 fw-bold d-block"><?php echo $key->nama_tipe; ?></span>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                        <!-- <?php // if ($midtrans->client_key != null) : ?>
                                                            <div class="col-md-4 col-6 mb-4">
                                                                <label class="btn bg-light btn-color-gray-600 btn-active-text-gray-800 border border-3 border-gray-100 border-active-primary btn-active-light-primary w-100 px-4" data-kt-button="true">
                                                                    <input class="btn-check" type="radio" name="method" value="midtrans" id="midtrans" />
                                                                    <i class="fas fa-wallet fs-2hx mb-2 pe-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                                                    <span class="fs-7 fw-bold d-block">Midtrans</span>
                                                                </label>
                                                            </div>
                                                        <?php // endif; ?>
                                                        <?php // if ($npay->api_key != null) : ?>
                                                            <div class="col-md-4 col-6 mb-4">
                                                                <label class="btn bg-light btn-color-gray-600 btn-active-text-gray-800 border border-3 border-gray-100 border-active-primary btn-active-light-primary w-100 px-4" data-kt-button="true">
                                                                    <input class="btn-check" type="radio" name="method" value="npay" id="npay" />
                                                                    <i class="fas fa-wallet fs-2hx mb-2 pe-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                                                    <span class="fs-7 fw-bold d-block">NPAY</span>
                                                                </label>
                                                            </div>
                                                        <?php // endif; ?>
                                                        <?php // if ($smartpayment->host != null) : ?>
                                                            <div class="col-md-4 col-6 mb-4">
                                                                <label class="btn bg-light btn-color-gray-600 btn-active-text-gray-800 border border-3 border-gray-100 border-active-primary btn-active-light-primary w-100 px-4" data-kt-button="true">
                                                                    <input class="btn-check" type="radio" name="method" value="smartpayment" id="smartpayment" />
                                                                    <i class="fas fa-wallet fs-2hx mb-2 pe-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                                                    <span class="fs-9 fw-bold d-block">SMARTPAYMENT</span>
                                                                </label>
                                                            </div>
                                                        <?php // endif; ?> -->

                                                        <!-- <label for="tipe_pesanan" class="form-label mt-4">Tipe Pembayaran</label>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="tipe_pesanan" id="tipe_pesanan_langsung" value="1" onchange="handleRadioChange(this)" checked>
                                                            <label class="form-check-label" for="tipe_pesanan_langsung">
                                                                Order Langsung
                                                            </label>
                                                        </div>
                                                        <div class="form-check mt-2">
                                                            <input class="form-check-input" type="radio" name="tipe_pesanan" id="tipe_pesanan_preorder" value="2" onchange="handleRadioChange(this)">
                                                            <label class="form-check-label" for="tipe_pesanan_preorder">
                                                                Pre Order
                                                            </label>
                                                        </div> -->

                                                        <label for="tanggal_preorder" class="form-label mt-4" id="label_tanggal_preorder" hidden>Pilih Tanggal</label>
                                                        <input type="datetime-local" class="form-control" id="tanggal_preorder" name="tanggal_preorder" hidden/>

                                                        <label for="foto" class="form-label mt-4">Bukti Pembayaran <small class="text-muted">*opsional</small></label>
                                                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                                        <div id="preview" class="mt-3"></div>
                                                    </div>

                                                    <button type="submit" class="btn  fs-1 w-100 py-4" style="background-color: #3b527f; color: white;" disabled id="btn-submit">Submit Order</button>
                                                </div>
                                                <!--end::Payment Method-->
                                            </div>
                                            <!--end: Card Body-->
                                        </div>
                                    </form>
                                    <!--end::Pos order-->
                                </div>
                                <!--end::Sidebar-->
                            </div>
                            <!--end::Layout-->
                        </div>
                        <!--end::Container-->
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Page-->
        </div>
        <!--end::Root-->
        <!--end::Main-->

        <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
            <i class="fas fa-up-long"><span class="path1"></span><span class="path2"></span></i>
        </div>

        <!-- Modal Varian -->
        <div class="modal modal-lg fade" id="modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pilih varian produk <span id="namaproduk" style="text-decoration: underline;"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalbody">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btnvarian">Tambah</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Datatable -->
        <div class="modal modal-xl fade" id="modald" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Histori transaksi hari ini</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalbody">
                        <div class="table-responsive">
                            <div id="table1_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                                <div class="row dt-row">
                                    <div class="col-sm-12">
                                        <table class="table dataTable no-footer" id="table" aria-describedby="table1_info">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Tanggal</th>
                                                    <th>Pelanggan</th>
                                                    <th>Subtotal</th>
                                                    <th>Metode Bayar</th>
                                                    <th>Kasir</th>
                                                    <th>Aksi</th>
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Pelanggan -->
        <div class="modal fade" id="modalp" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah pelanggan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formp" autocomplete="off">
                            <input type="hidden" name="id" id="id">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama pelanggan">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <label for="nohp" class="form-label">Nomor HP</label>
                                <input type="number" class="form-control" id="nohp" name="nohp" placeholder="Masukkan nomor hp (Opsional)">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Masukkan Alamat (Opsional)">
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

        <!-- Modal Success -->
        <div class="modal fade" id="modals" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-body">
                        <center>
                            <img src="https://i.gifer.com/7efs.gif" alt="Transaction Successfully" class="img-fluid">
                            <h1>Transaksi Sukses!</h1>
                            <p>Total : <span id="totaltrx"></span></p>
                            <span class="badge rounded-pill bg-primary text-white mb-4">Metode Pembayaran : <span id="metodetrx"></span></span>
                            <div class="d-flex justify-content-center mt-4">
                                <a href="javascript:void(0);" class="btn btn-secondary me-4" target="_blank" id="btninvoice"><i class="fab fa-whatsapp me-1"></i>Kirim Invoice</a>
                                <a href="javascript:void(0);" class="btn btn-secondary me-4" target="_blank" id="btnstruk"><i class="fas fa-receipt me-1"></i>Cetak Struk</a>
                                <a href="javascript:void(0);" class="btn btn-secondary" target="_blank" id="btnSettingDevice"><i class="fas fa-gear me-1"></i>Setting Device</a>
                            </div>
                        </center>
                    </div>
                    <div class="d-flex justify-content-center mb-4">
                        <a type="submit" class="btn btn-primary w-50" href="/kasir">Buat Pesanan Baru</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Smartpayment -->
        <div class="modal fade" id="modal-smartpayment" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">SMARTPAYMENT</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="form-smartpayment" autocomplete="off">
                        <div class="modal-body">
                                <input type="hidden" name="id" id="id">
                                <div class="mb-3">
                                    <label class="form-label">Nomor Kartu</label>
                                    <input type="text" class="form-control" name="nomor_kartu" placeholder="Nomor Kartu" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">PIN</label>
                                    <input type="password" class="form-control" name="pin" placeholder="PIN" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Bayar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Smartpayment -->
        <div class="modal modal-md fade" id="modal-npay" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">NPAY</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="form-npay" autocomplete="off">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-7">
                                    <input type="hidden" name="id" id="id">
                                    <div class="mb-3">
                                        <label class="form-label">Nomor Kartu</label>
                                        <input type="text" class="form-control" name="nomor_kartu" placeholder="Nomor Kartu" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-5">
                                        <label class="form-label">PIN</label>
                                        <input type="password" class="form-control" name="pin" placeholder="PIN" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="mb-3 text-center">
                                        <h5 class="mb-0">Scan Disini</h5>
                                        <img src="" id="npay-qrcode"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Bayar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal PattyCash -->
         <div class="modal modal-xl fade" id="modalpc" tabindex="-1">
             <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" id="btnClosePattyCash" class="btn btn-secondary btn-lg"
                            data-bs-dismiss="modal">Batal</button>
                        <h5 class="modal-title mx-auto text-center" style="padding-right: 75px;" id="productModalLabel">
                            <strong id="namaProduct">Mulai Shift</strong><br>
                        </h5>
                    </div>
                    <div class="modal-body">
                        <!-- Payment Options -->
                        <div class="mt-0">
                            <div class="row d-flex justify-content-center">
                                <img style="width: 20%;" src="/assets/img/cashier-machine.png" alt="">
                            </div>
                        </div>
                        <hr>
    
                        <form id="formOpenPettyCash"  method="POST"
                            enctype="multipart/form-data">
    
                            <div class=" mt-2">
                                <div class="row">
                                    <div class="form-group w-100">
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text" id="inputGroup-sizing-lg">Saldo Tunai</span>
                                            <input type="text" name="saldo_awal" id="saldo_awal" class="form-control"
                                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="row">
                                <button type="submit" id="btnSubmitPattyCash"
                                class="btn btn-primary btn-lg mt-4 ms-3" style="width: 96%">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
         </div>

         <!-- Modal Close PattyCash -->
          <div class="modal modal-xl fade" id="modalcpc" tabindex="-1">
             <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" id="btnCancelClosePattyCash" class="btn btn-secondary btn-lg"
                            data-bs-dismiss="modal">Batal</button>
                        <h5 class="modal-title mx-auto text-center" style="padding-right: 75px;" >
                            <strong >Shift Aktif</strong><br>
                        </h5>
                    </div>
                    <div class="modal-body" style="padding-top: 0;">
                        <!-- Payment Options -->
                        <div  id="end-current-shift-section">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <h5>Actual Ending Cash</h5>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control"
                                                            id="endingCash"
                                                            placeholder="Ending cash">
                                                    </div>
                                                    <div class="row mt-2 d-none"
                                                        id="container-difference">
                                                        <hr>
                                                        <div class="col-6">
                                                            <h5 class="text-muted ms-4">Difference
                                                            </h5>
                                                        </div>
                                                        <div class="col-6 me-auto">
                                                            <div id="difference"></div>
                                                        </div>
                                                        <hr>
                                                    </div>

                                                    <button
                                                        class="btn btn-danger w-100 btn-lg mt-2"
                                                        id="btnEndCurrentShift">End
                                                        Current Shift</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card"
                                        style="overflow-y: auto; height: calc(100vh - 380px);">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="row">
                                                                <strong>SHIFT DETAILS</strong>
                                                            </div>
                                                            <hr>

                                                            <div class="row">
                                                                <div class="col-6">Name</div>
                                                                <div class="col-6"
                                                                    id="txt-name-end-current-shift">
                                                                    Ardian</div>
                                                            </div>
                                                            <hr>

                                                            <div class="row">
                                                                <div class="col-6">Outlet</div>
                                                                <div class="col-6"
                                                                    id="txt-outlet-end-current-shift">
                                                                    Outlet 1</div>
                                                            </div>
                                                            <hr>

                                                            <div class="row">
                                                                <div class="col-6">Starting Shift
                                                                </div>
                                                                <div class="col-6"
                                                                    id="txt-start-end-current-shift">
                                                                    Thursday,
                                                                    blabla</div>
                                                            </div>
                                                            <hr>

                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="row">
                                                                <strong>CASH</strong>
                                                            </div>
                                                            <hr>

                                                            <div class="row">
                                                                <div class="col-6">Starting Cash
                                                                    In Drawer</div>
                                                                <div class="col-6"
                                                                    id="txt-starting-cash-end-current-shift">
                                                                    Rp. 50.000
                                                                </div>
                                                            </div>
                                                            <hr>

                                                            <div class="row">
                                                                <div class="col-6">Cash Sales
                                                                </div>
                                                                <div class="col-6"
                                                                    id="txt-sales-end-current-shift">
                                                                    Rp. 70.000
                                                                </div>
                                                            </div>
                                                            <hr>

                                                            <div class="row">
                                                                <div class="col-6">Expected Ending
                                                                    Cash</div>
                                                                <div class="col-6"
                                                                    id="txt-expected-ending-end-current-shift">
                                                                    Rp. 121.000
                                                                </div>
                                                            </div>
                                                            <hr>

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
         </div>

        <script src="/assets/pos/js/plugins.bundle.js"></script>
        <script src="/assets/pos/js/scripts.bundle.js"></script>

        <script src="/assets/pos/js/pos.js"></script>
        <script src="/assets/extensions/toastr/toastr.min.js"></script>
        <script src="/assets/extensions/blockui/jquery.blockui.min.js"></script>

        <script src="/assets/extensions/select2/dist/js/select2.min.js"></script>
        <script src="/assets/extensions/select2/dist/js/select2.full.min.js"></script>

        <script src="/assets/extensions/bootbox/bootbox.min.js"></script>

        <script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
        <script src="/assets/extensions/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="/assets/extensions/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
        <!-- <script src="https://app.midtrans.com/snap/snap.js" data-client-key="<?php // echo $midtrans->client_key; ?>"></script> -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"></script>
        <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image-more/2.8.0/dom-to-image-more.min.js"></script>-->

        <script>
            var table;
            var modal = $('#modal');
            var modald = $('#modald');
            var modalp = $('#modalp');
            var modals = $('#modals');
            var modalpc = $('#modalpc');
            var modalcpc = $('#modalcpc');
            var modalSmartpayment = $('#modal-smartpayment');
            var modalNpay = $('#modal-npay');
            let npayInterval = null;
            let npayData = null;
            var sessionDataDiv = document.getElementById('session-data');
            var logo = sessionDataDiv.getAttribute('data-logo');
            var pettyCash = <?= json_encode($pettyCash) ?>;
            var pettyCashId = pettyCash ? pettyCash.id : null;
            var finalExpectedEndingCash = 0;

            $(document).ready(function() {
                var endingCashInput = document.getElementById('endingCash');

                if (endingCashInput) {
                    endingCashInput.addEventListener("keyup", function(e) {
                        this.value = formatRupiah(this.value, "Rp. ");
                        if (this.value == '' || this.value == 'Rp. ') {
                            $('#container-difference').addClass('d-none');
                            $('#difference').text(formatRupiah("0", "Rp. "));
                        } else {
                            $('#container-difference').removeClass('d-none');

                            let hargaInput = this.value.match(/\d+/g).join('');
                            let different = hargaInput - finalExpectedEndingCash;
                            if (different > 0) {
                                $('#difference').text(formatRupiah(different.toString(), "Rp. "));
                            } else {
                                $('#difference').text("-" + formatRupiah(different.toString(), "Rp. "));
                            }
                        }
                    })
                }

                $('#nohp').on('input', function() {
                    var input = $(this).val();
                    input = input.replace(/\D/g, '');
                    input = '62' + input.substr(2);
                    $(this).val(input);
                });

                $('#fullScreenBtn').on('click', function() {
                    $(this).attr("id", "exitFullScreenBtn");
                    const element = document.documentElement;

                    if (element.requestFullscreen) {
                        element.requestFullscreen();
                    } else if (element.mozRequestFullScreen) {
                        element.mozRequestFullScreen();
                    } else if (element.webkitRequestFullscreen) {
                        element.webkitRequestFullscreen();
                    } else if (element.msRequestFullscreen) {
                        element.msRequestFullscreen();
                    }

                    $('#exitFullScreenBtn').on('click', function() {
                        $(this).attr("id", "fullScreenBtn");
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                        } else if (document.mozCancelFullScreen) {
                            document.mozCancelFullScreen();
                        } else if (document.webkitExitFullscreen) {
                            document.webkitExitFullscreen();
                        } else if (document.msExitFullscreen) {
                            document.msExitFullscreen();
                        }
                    });
                });

                $('#exitFullScreenBtn').on('click', function() {
                    $(this).attr("id", "fullScreenBtn");
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.mozCancelFullScreen) {
                        document.mozCancelFullScreen();
                    } else if (document.webkitExitFullscreen) {
                        document.webkitExitFullscreen();
                    } else if (document.msExitFullscreen) {
                        document.msExitFullscreen();
                    }
                });

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

                $('#btnEndCurrentShift').off().on('click', function(a){
                    if($('#endingCash').val() == ''){
                        toastr.warning("Input Ending Cash tidak boleh kosong");
                        return
                    }
                    let hasilEndingCash = $('#endingCash').val();
                    $.ajax({
                        url: '/kasir/closePattyCash',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            ending_cash: hasilEndingCash
                        },
                        beforeSend: function() {
                            showblockUI();
                            $('#btnEndCurrentShift').addClass("btn-load").attr("disabled", true).html(
                                    `<span class="d-flex align-items-center">
                            <span class="spinner-border flex-shrink-0"></span><span class="flex-grow-1 ms-2"> Loading...  </span></span>`
                                );
                        },
                        complete: function() {
                            hideblockUI();
                            $('#btnEndCurrentShift').removeClass("btn-load").removeAttr("disabled").text("End Current Shift");
                        },
                        success: function(response) {
                            console.log(response);
                            if(response.status){
                                toastr.success(response.notif);
                                modalcpc.modal('hide');
                                pettyCashId = null;
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
                            $('#btnEndCurrentShift').removeClass("btn-load").removeAttr("disabled").text("End Current Shift");
                        }
                    }); 
                });
            });

            function showblockUI() {
                jQuery.blockUI({
                    message: 'Sedang Proses...',
                    baseZ: 2000,
                    css: {
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .5,
                        color: '#fff'
                    }
                });
            }

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

            function showPettyCashModal(){
                if(pettyCashId){
                    $.ajax({
                        url: '/kasir/getTransactionByPettyCash',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            id_petty_cash: pettyCashId
                        },
                        beforeSend: function() {
                            showblockUI();
                        },
                        complete: function() {
                            hideblockUI();
                        },
                        success: function(response) {
                            console.log(response);
                            if (response.status) {
                                $('#endingCash').val('');
                                pettyCash = response.pettyCash;
                                let expectedEndingCash = parseInt(pettyCash.amount_awal);
                                let sales = 0;
                                
                                response.dataTransaction.forEach(function(item){
                                    sales += parseInt(item.total);
                                    expectedEndingCash += parseInt(item.total);
                                });

                                finalExpectedEndingCash = expectedEndingCash;

                                $('#txt-name-end-current-shift').text(pettyCash.nama_user_pembuka);
                                $('#txt-outlet-end-current-shift').text(pettyCash.nama_toko);
                                $('#txt-start-end-current-shift').text(pettyCash.open);
                                $('#txt-starting-cash-end-current-shift').text(formatRupiah(pettyCash.amount_awal.toString(), "Rp. "))
                                $('#txt-sales-end-current-shift').text(formatRupiah(sales.toString(), "Rp. "));
                                $('#txt-expected-ending-end-current-shift').text(formatRupiah(expectedEndingCash.toString(), "Rp. "));
                                modalcpc.modal('show');
                            } else {
                                toastr.warning('Maaf, gagal mendapatkan data patty cash');
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
                }else{
                    $('#saldo_awal').val('');

                    let saldoAwalInput = document.getElementById('saldo_awal');

                    saldoAwalInput.addEventListener("keyup", function(e) {
                        this.value = formatRupiah(this.value, "Rp. ");
                    })

                    modalpc.modal('show');
                }
            }

            function hideblockUI() {
                $.unblockUI();
            }

            function datatable() {
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
                        url: '/kasir/datatable',
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
                            width: 100
                        },
                        {
                            data: 'pelanggan',
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
                            data: 'metode',
                            orderable: false,
                            width: 100
                        },
                        {
                            data: 'kasir',
                            orderable: false,
                            width: 100
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

                modald.modal('show');
            }

            function tambah() {
                $('#id').val('');

                $('#formp')[0].reset();
                var form = $('#formp input');
                form.removeClass('is-invalid is-valid');

                modalp.modal('show');
            }

            function varian(id, nama) {
                var varianValues = $('.varian').map(function() {
                    return $(this).val(); // Mengambil nilai dari setiap elemen input
                }).get();
                $.ajax({
                    url: '/kasir/getVarian',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        id: id,
                        id_varian: varianValues
                    },
                    beforeSend: function() {
                        showblockUI();
                    },
                    complete: function() {
                        hideblockUI();
                    },
                    success: function(response) {
                        if (response.status) {
                            $('#namaproduk').text(nama);
                            $('#modalbody').html(response.html);

                            var id_varian = [];
                            var barang = [];
                            var harga = [];
                            var modal = [];
                            var satuan = [];
                            var id_barang = [];

                            // Ceheckbox varian
                            $('input[type="checkbox"]').click(function() {
                                var id = $(this).val();
                                if ($(this).prop("checked")) {
                                    id_varian.push(id);
                                    var inputs = $('#varian' + id).find('input');

                                    barang.push(inputs.eq(0).val());
                                    harga.push(inputs.eq(1).val());
                                    modal.push(inputs.eq(2).val());
                                    satuan.push(inputs.eq(3).val());
                                    id_barang.push(inputs.eq(4).val());
                                } else {
                                    var index = $.inArray(id, id_varian);

                                    id_varian.splice(index, 1);
                                    barang.splice(index, 1);
                                    harga.splice(index, 1);
                                    modal.splice(index, 1);
                                    satuan.splice(index, 1);
                                    id_barang.splice(index, 1);
                                }
                                var arrayParameter = JSON.stringify([id_barang, barang, harga, modal, satuan, id_varian]);
                                $('#btnvarian').attr("onclick", "add_barang_varian(" + arrayParameter + ")");
                            });

                            $('#modal').modal('show');
                        } else {
                            toastr.warning('Maaf, gagal mendapatkan data varian');
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

            function add_barang_varian(data) {
                var barang = data[1];
                var harga = data[2];
                var modal = data[3];
                var satuan = data[4];
                var id_varian = data[5];

                $.each(data[0], function(index, id_barang) {
                    $('#pesanan' + id_varian[index]).remove();
                    var html = `<tr class="pesanan" id="pesanan` + id_varian[index] + `">
                            <input type="hidden" name="id_barang[]" value="` + id_barang + `" />
                            <input type="hidden" class="varian" name="id_varian[]" value="` + id_varian[index] + `" />
                            <input type="hidden" name="barang[]" value="` + barang[index] + ` - ` + satuan[index] + `" />
                            <td class="pe-0">
                                <div class="d-flex align-items-center">
                                <i class="fas fa-trash me-4 fs-3" style="cursor: pointer;" title="Hapus barang" onclick="remove_barang(` + id_varian[index] + `)"></i>
                                    <span class="fw-bold text-gray-800 cursor-pointer text-hover-primary fs-6 me-1">` + barang[index] + ` - ` + satuan[index] + `</span>
                                </div>
                            </td>
                            <td class="pe-0">
                                <div class="position-relative d-flex align-items-center">
                                    <button type="button" class="btn btn-icon btn-sm btn-light btn-icon-gray-400" onclick="decrease(` + id_varian[index] + `, ` + harga[index] + `)"><i class="fas fa-minus"></i></button>
                                    <input type="text" class="form-control border-0 text-center px-0 fs-3 fw-bold text-gray-800 w-30px" placeholder="Amount" name="qty[]" id="qty` + id_varian[index] + `" readonly value="1" />
                                    <button type="button" class="btn btn-icon btn-sm btn-light btn-icon-gray-400" onclick="increase(` + id_varian[index] + `, ` + harga[index] + `)"><i class="fas fa-plus"></i></button>
                                </div>
                            </td>
                            <td class="text-end">
                            <input type="hidden" class="harga" name="harga[]" id="harga` + id_varian[index] + `" value="` + harga[index] + `"/>
                            <input type="hidden" name="modal[]" id="modal` + id_varian[index] + `" value="` + modal[index] + `"/>
                                <span class="fw-bold text-primary fs-2" id="item-total` + id_varian[index] + `">` + rupiah(Number(harga[index])) + `</span>
                            </td>
                        </tr>`;

                    $('#btn-submit').removeAttr('disabled', 'disabled');
                    $('#discount').removeAttr('disabled', 'disabled');

                    // if ($('#pesanan' + val).length == 0) {
                    $('#modal').modal('hide');
                    $('#pesanan').append(html);

                    var subTotal = 0;
                    $('.harga').each(function() {
                        subTotal += parseFloat($(this).val());
                    });
                    $('#subtotal').text(rupiah(subTotal));
                    $('#subtotal').val(subTotal);

                    // Total harga
                    var jumlah = parseInt($('#discount2').val());
                    var totalHarga = 0;
                    $('.harga').each(function() {
                        totalHarga += parseFloat($(this).val());
                    });

                    console.log(<?php echo $toko->biaya_layanan ? $toko->biaya_layanan : 0; ?>)
                    var ppn = totalHarga * <?php echo $toko->ppn; ?> / 100;
                    totalHarga = totalHarga + ppn + <?php echo $toko->biaya_layanan ? $toko->biaya_layanan : 0; ?>;
                    

                    if ($('#tipedc').val() == 1) {
                        var discount = totalHarga * jumlah / 100;
                        totalHarga = totalHarga - discount;
                        $('#discountval').text(rupiah(discount));
                    } else {
                        totalHarga = totalHarga - jumlah;
                        $('#discountval').text(rupiah(jumlah));
                    }

                    $('#ppn').text(rupiah(ppn));
                    $('#ppn2').val(ppn);

                    $('#grant-total').text(rupiah(totalHarga));
                    $('#grant-total2').val(totalHarga);
                    // } else {
                    // toastr.warning('Barang telah ditambahkan');
                    // }
                });
            }

            function add_barang(id, nama, harga, modal) {
                console.log(id);
                console.log(nama);
                console.log(harga);
                console.log(modal);
                var html = `<tr class="pesanan" id="pesanan` + id + `">
                            <input type="hidden" name="id_barang[]" value="` + id + `" />
                            <input type="hidden" name="id_varian[]" value="" />
                            <input type="hidden" name="barang[]" value="` + nama + `" />
                            <td class="pe-0">
                                <div class="d-flex align-items-center">
                                <i class="fas fa-trash me-4 fs-3" style="cursor: pointer;" title="Hapus barang" onclick="remove_barang(` + id + `)"></i>
                                    <span class="fw-bold text-gray-800 cursor-pointer text-hover-primary fs-6 me-1">` + nama + `</span>
                                </div>
                            </td>
                            <td class="pe-0">
                                <div class="position-relative d-flex align-items-center">
                                    <button type="button" class="btn btn-icon btn-sm btn-light btn-icon-gray-400" onclick="decrease(` + id + `, ` + harga + `)"><i class="fas fa-minus"></i></button>
                                    <input type="text" class="form-control border-0 text-center px-0 fs-3 fw-bold text-gray-800 w-30px" placeholder="Amount" name="qty[]" id="qty` + id + `" readonly value="1" />
                                    <button type="button" class="btn btn-icon btn-sm btn-light btn-icon-gray-400" onclick="increase(` + id + `, ` + harga + `)"><i class="fas fa-plus"></i></button>
                                </div>
                            </td>
                            <td class="text-end">
                            <input type="hidden" class="harga" name="harga[]" id="harga` + id + `" value="` + harga + `"/>
                            <input type="hidden" name="modal[]" id="modal` + id + `" value="` + modal + `"/>
                                <span class="fw-bold text-primary fs-2" id="item-total` + id + `">` + rupiah(Number(harga)) + `</span>
                            </td>
                        </tr>`;

                $('#btn-submit').removeAttr('disabled', 'disabled');
                $('#discount').removeAttr('disabled', 'disabled');

                if ($('#pesanan' + id).length == 0) {
                    $('#pesanan').append(html);

                    var subTotal = 0;
                    $('.harga').each(function() {
                        subTotal += parseFloat($(this).val());
                    });
                    $('#subtotal').text(rupiah(subTotal));
                    $('#subtotal').val(subTotal);

                    // Total harga
                    var jumlah = parseInt($('#discount2').val());
                    var totalHarga = 0;
                    $('.harga').each(function() {
                        totalHarga += parseFloat($(this).val());
                    });

                    console.log(<?php echo $toko->biaya_layanan ? $toko->biaya_layanan : 0; ?>)
                    var ppn = totalHarga * <?php echo $toko->ppn; ?> / 100;
                    totalHarga = totalHarga + ppn;

                    console.log(ppn);

                    if ($('#tipedc').val() == 1) {
                        var discount = totalHarga * jumlah / 100;
                        totalHarga = totalHarga - discount;
                        $('#discountval').text(rupiah(discount));
                    } else {
                        totalHarga = totalHarga - jumlah;
                        $('#discountval').text(rupiah(jumlah));
                    }

                    $('#ppn').text(rupiah(ppn));
                    $('#ppn2').val(ppn);

                    $('#grant-total').text(rupiah(totalHarga));
                    $('#grant-total2').val(totalHarga);
                } else {
                    // toastr.warning('Barang telah ditambahkan');
                }
            }

            function remove_barang(id) {
                $('#pesanan' + id).remove();
                if ($('.pesanan').length < 1) {
                    $('#btn-submit').attr('disabled', 'disabled');
                    $('#discount').attr('disabled', 'disabled');
                    $('#discount').prop('selectedIndex', 0);
                    $('#discount2').val(0);
                    $('#tipedc').val(0);
                    $('#discountval').text(rupiah(0));
                }

                // Total harga
                var subTotal = 0;
                $('.harga').each(function() {
                    subTotal += parseFloat($(this).val());
                });
                $('#subtotal').text(rupiah(subTotal));
                $('#subtotal').val(subTotal);

                // Total harga
                var jumlah = parseInt($('#discount2').val());
                var totalHarga = 0;
                $('.harga').each(function() {
                    totalHarga += parseFloat($(this).val());
                });

                var ppn = totalHarga * <?php echo $toko->ppn; ?> / 100;
                totalHarga = totalHarga + ppn + <?php echo $toko->biaya_layanan ? $toko->biaya_layanan : 0; ?>;

                if ($('#tipedc').val() == 1) {
                    var discount = totalHarga * jumlah / 100;
                    totalHarga = totalHarga - discount;
                    $('#discountval').text(rupiah(discount));
                } else {
                    totalHarga = totalHarga - jumlah;
                    $('#discountval').text(rupiah(jumlah));
                }

                $('#ppn').text(rupiah(ppn));
                $('#ppn2').val(ppn);

                $('#grant-total').text(rupiah(totalHarga));
                $('#grant-total2').val(totalHarga);
            }

            function increase(id, harga) {
                var qty = parseInt($('#qty' + id).val()) + 1;
                var itemTotal = harga * qty;

                // harga & qty item
                $('#qty' + id).val(qty);
                $('#harga' + id).val(itemTotal);
                $('#item-total' + id).text(rupiah(itemTotal));

                // Total harga
                var subTotal = 0;
                $('.harga').each(function() {
                    subTotal += parseFloat($(this).val());
                });
                $('#subtotal').text(rupiah(subTotal));
                $('#subtotal').val(subTotal);

                // Total harga
                var jumlah = parseInt($('#discount2').val());
                var totalHarga = 0;
                $('.harga').each(function() {
                    totalHarga += parseFloat($(this).val());
                });

                var ppn = totalHarga * <?php echo $toko->ppn; ?> / 100;
                totalHarga = totalHarga + ppn + <?php echo $toko->biaya_layanan ? $toko->biaya_layanan : 0; ?>;

                if ($('#tipedc').val() == 1) {
                    var discount = totalHarga * jumlah / 100;
                    totalHarga = totalHarga - discount;
                    $('#discountval').text(rupiah(discount));
                } else {
                    totalHarga = totalHarga - jumlah;
                    $('#discountval').text(rupiah(jumlah));
                }

                $('#ppn').text(rupiah(ppn));
                $('#ppn2').val(ppn);

                $('#grant-total').text(rupiah(totalHarga));
                $('#grant-total2').val(totalHarga);
            }

            function decrease(id, harga) {
                var qty = parseInt($('#qty' + id).val()) - 1;
                var itemTotal = harga * qty;

                if (qty >= 1) {
                    $('#qty' + id).val(qty);
                    $('#harga' + id).val(itemTotal);
                    $('#item-total' + id).text(rupiah(itemTotal));

                    // Total harga
                    var subTotal = 0;
                    $('.harga').each(function() {
                        subTotal += parseFloat($(this).val());
                    });
                    $('#subtotal').text(rupiah(subTotal));
                    $('#subtotal').val(subTotal);

                    // Total harga
                    var jumlah = parseInt($('#discount2').val());
                    var totalHarga = 0;
                    $('.harga').each(function() {
                        totalHarga += parseFloat($(this).val());
                    });

                    var ppn = totalHarga * <?php echo $toko->ppn; ?> / 100;
                    totalHarga = totalHarga + ppn + <?php echo $toko->biaya_layanan ? $toko->biaya_layanan : 0; ?>;

                    if ($('#tipedc').val() == 1) {
                        var discount = totalHarga * jumlah / 100;
                        totalHarga = totalHarga - discount;
                        $('#discountval').text(rupiah(discount));
                    } else {
                        totalHarga = totalHarga - jumlah;
                        $('#discountval').text(rupiah(jumlah));
                    }

                    $('#ppn').text(rupiah(ppn));
                    $('#ppn2').val(ppn);

                    $('#grant-total').text(rupiah(totalHarga));
                    $('#grant-total2').val(totalHarga);
                }
            }

            function clear_all() {
                $('#pesanan').empty();
                $('#btn-submit').attr('disabled', 'disabled');

                $('#discount').attr('disabled', 'disabled');
                $('#discount').prop('selectedIndex', 0);
                $('#discount2').val(0);
                $('#tipedc').val(0);
                $('#discountval').text(rupiah(0));

                // Total harga
                var subTotal = 0;
                $('.harga').each(function() {
                    subTotal += parseFloat($(this).val());
                });
                $('#subtotal').text(rupiah(subTotal));
                $('#subtotal').val(subTotal);

                // Total harga
                var jumlah = parseInt($('#discount2').val());
                var totalHarga = 0;
                $('.harga').each(function() {
                    totalHarga += parseFloat($(this).val());
                });

                console.log(<?php echo $toko->biaya_layanan ? $toko->biaya_layanan : 0; ?>)
                var ppn = totalHarga * <?php echo $toko->ppn; ?> / 100;
                totalHarga = totalHarga + ppn;

                console.log(ppn);

                if ($('#tipedc').val() == 1) {
                    var discount = totalHarga * jumlah / 100;
                    totalHarga = totalHarga - discount;
                    $('#discountval').text(rupiah(discount));
                } else {
                    totalHarga = totalHarga - jumlah;
                    $('#discountval').text(rupiah(jumlah));
                }

                $('#ppn').text(rupiah(ppn));
                $('#ppn2').val(ppn);

                $('#grant-total').text(rupiah(totalHarga));
                $('#grant-total2').val(totalHarga);
            }

            function rupiah(angka) {
                var formatMataUang = angka.toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                });
                formatMataUang = formatMataUang.replace('IDR', 'Rp.');

                formatMataUang = formatMataUang.replace(/,00$/, '');

                return formatMataUang;
            }

            function smartpayment() {
                const form = $('#form-smartpayment');

                form[0].reset()
                form.removeClass('is-invalid is-valid');
                modalSmartpayment.modal('show')
            }

            function npay() {
                var form = $('#form')[0];
                var formData = new FormData(form);
                $.ajax({
                    type: "POST",
                    url: '/kasir/npay/qr',
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
                            const qrcode = $('#npay-qrcode');
                            qrcode.attr('src', response.qr);

                            npayData = response.data

                            const form = $('#form-npay');
                            form[0].reset()
                            form.removeClass('is-invalid is-valid');
                            form.find('#npay-qrcode').attr('src', form.qr)

                            modalNpay.modal('show')
                        } else {
                            toastr.warning('Gagal membuat qr npay');
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

            function simpanTransaksi() {
                var form = $('#form')[0];
                var formData = new FormData(form);
                formData.append('id_petty_cash', pettyCashId);
                console.log(formData);

                if(pettyCashId){
                    var url = "/kasir/simpan";
                    formData.append('id_petty_cash', pettyCashId);

                    $.ajax({
                        type: "POST",
                        url: url,
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
                                toastr.success('Transaksi berhasil');
                                $('#totaltrx').text(response.total);
                                $('#metodetrx').text(response.metode);
                                // $('#btnstruk').attr('href', '/kasir/struk/' + response.id);
                                $('#btnstruk').attr('href', 'intent://cetak-struk?id=' + response.id);
                                $('#btnSettingDevice').attr('href', 'intent://list-bluetooth-device');
                                console.log("intent://cetak-struk?id=" + response.id)
                                
    
                                if(response.pelanggan) {
                                    $('#btninvoice').data('id', response.id)
                                    $('#btninvoice').data('nohp', response.pelanggan.nohp)
                                } else {
                                    $('#btninvoice').attr('href', response.waLink);
                                }
    
                                modals.modal('show');
                            } else {
                                toastr.warning('Transaksi gagal');
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
                }else{
                    showPettyCashModal();
                }
            }

            $('#pelanggan').select2({
                placeholder: 'Pilih Pelanggan',
                allowClear: false,
                theme: 'bootstrap-5',
                width: '100%',
                language: {
                    noResults: function() {
                        return "Data tidak ditemukan";
                    }
                },
                ajax: {
                    type: "POST",
                    url: "/kasir/getPelanggan",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
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

            $('#discount').change(function() {
                $.ajax({
                    type: "POST",
                    url: "/kasir/getDiscount",
                    data: {
                        id: $(this).val()
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
                            // Subtotal
                            var subTotal = 0;
                            $('.harga').each(function() {
                                subTotal += parseFloat($(this).val());
                            });
                            $('#subtotal').text(rupiah(subTotal));
                            $('#subtotal').val(subTotal);

                            // Total harga
                            var jumlah = parseInt(response.data.jumlah);
                            var totalHarga = 0;
                            $('.harga').each(function() {
                                totalHarga += parseFloat($(this).val());
                            });

                            console.log(<?php echo $toko->biaya_layanan ? $toko->biaya_layanan : 0; ?>)
                            var ppn = totalHarga * <?php echo $toko->ppn; ?> / 100;
                            totalHarga = totalHarga + ppn;

                            if (response.data.tipe == 1) {
                                var discount = totalHarga * jumlah / 100;
                                totalHarga = totalHarga - discount;
                                $('#discountval').text(rupiah(discount));
                            } else {
                                totalHarga = totalHarga - jumlah;
                                $('#discountval').text(rupiah(jumlah));
                            }

                            $('#ppn').text(rupiah(ppn));
                            $('#ppn2').val(ppn);

                            $('#grant-total').text(rupiah(totalHarga));
                            $('#grant-total2').val(totalHarga);

                            // End

                            $('#discount2').val(jumlah);
                            $('#tipedc').val(response.data.tipe);
                        } else {
                            $('#discount2').val(0);
                            $('#tipedc').val(0);
                            $('#discountval').text(rupiah(0));
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

            $('#kategori').change(function() {
                $.ajax({
                    type: "POST",
                    url: "/kasir/getProduct",
                    data: {
                        id_kategori: $(this).val()
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
                            $('#product').html(response.html);
                        } else {
                            toastr.warning('Tidak ada produk pada kategori ' + response.kategori);
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

            $('#search').on('input', function() {
                var inputValue = $(this).val().toLowerCase();

                $('.barang').each(function() {
                    var namaBarang = $(this).data('nama').toLowerCase();

                    if (namaBarang.includes(inputValue)) {
                        $(this).removeClass('d-none');
                    } else {
                        $(this).addClass('d-none');
                    }
                });
            });

            $('#formp').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "/pelanggan/simpan",
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
                            $('#formp')[0].reset();
                            toastr.success(response.notif);
                            modalp.modal('hide');
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

            $('#form').submit(function(e) {
                e.preventDefault();

                if($('#smartpayment').is(':checked')) {
                    smartpayment()
                    return
                }

                if($('#npay').is(':checked')) {
                    npay()
                    return
                }

                var form = $('#form')[0];
                var formData = new FormData(form);

                console.log(formData);
                if ($("#midtrans").is(":checked")) {
                    $.ajax({
                        type: "POST",
                        url: "/kasir/getToken",
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
                            snap.pay(response.token, {
                                onSuccess: function(result) {
                                    toastr.success("Pembayaran berhasil!");
                                    simpanTransaksi()
                                },
                                onPending: function(result) {
                                    alert("Waiting for your payment!");
                                    console.log(result);
                                },
                                onError: function(result) {
                                    alert("Payment failed!");
                                    console.log(result);
                                },
                                onClose: function() {
                                    alert('You closed the popup without finishing the payment');
                                }
                            });
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
                } else {
                    simpanTransaksi()
                }
            });
        
            $('#form-smartpayment').submit(function(e) {
                e.preventDefault()
                const thisForm = $('#form-smartpayment')

                var form = $('#form')[0];
                var formData = new FormData(form);
                const nomor_kartu = $(this).find('input[name="nomor_kartu"]')
                const pin = $(this).find('input[name="pin"]')
                formData.append('nomor_kartu', nomor_kartu.val())
                formData.append('pin', pin.val())

                $.ajax({
                    type: "POST",
                    url: "/kasir/smartpayment",
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
                        if(response.status) {
                            toastr.success("Pembayaran berhasil!");
                            modalSmartpayment.modal('hide');
                            simpanTransaksi()
                        } else {
                            if(response.message) {
                                alert(response.message);
                                thisForm[0].reset();
                                nomor_kartu.focus();
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
            })

            $('#form-npay').submit(function(e) {
                e.preventDefault()
                const thisForm = $('#form-npay')

                var form = $('#form')[0];
                var formData = new FormData(form);
                const nomor_kartu = $(this).find('input[name="nomor_kartu"]')
                const pin = $(this).find('input[name="pin"]')
                formData.append('nomor_kartu', nomor_kartu.val())
                formData.append('pin', pin.val())

                $.ajax({
                    type: "POST",
                    url: "/kasir/npay/pay",
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
                        if(response.status) {
                            toastr.success("Pembayaran berhasil!");
                            modalNpay.modal('hide');
                            simpanTransaksi()
                        } else {
                            if(response.message) {
                                alert(response.message);
                                thisForm[0].reset();
                                nomor_kartu.focus();
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
            })

            $('#formOpenPettyCash').submit(function(e){
                e.preventDefault()

                var form = $('#formOpenPettyCash')[0];
                var formData = new FormData(form);

                console.log(formData);

                $.ajax({
                    type: "POST",
                    url: "/kasir/storePettyCash",
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
                        console.log(response);
                        if(response.status) {
                            pettyCashId = response.id_petty_cash;
                            toastr.success(response.notif);
                            modalpc.modal('hide');
                        }else{
                            toastr.warning("Saldo awal tidak boleh kosong");
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

            // $('#btninvoice').click(function(e) {
            //     const el = $(this);
            //     const id = el.data('id')
            //     let nohp = el.data('nohp')

            //     if(id) {
            //         e.preventDefault();
            //         $.ajax({
            //             type: "GET",
            //             url: `/kasir/whatsapp/${id}/${nohp}`,
            //             processData: false,
            //             contentType: false,
            //             dataType: "JSON",
            //             beforeSend: function() {
            //                 showblockUI();
            //             },
            //             complete: function() {
            //                 hideblockUI();
            //             },
            //             success: function(response) {
            //                 if (response.status) {
            //                     toastr.success('Berhasil mengirim invoice');
            //                 } else {
            //                     toastr.error('Berhasil mengirim invoice');
            //                 }
            //             },
            //             error: function(jqXHR, textStatus, errorThrown, exception) {
            //                 var msg = '';
            //                 if (jqXHR.status === 0) {
            //                     msg = 'Not connect.\n Verify Network.';
            //                 } else if (jqXHR.status == 404) {
            //                     msg = 'Requested page not found. [404]';
            //                 } else if (jqXHR.status == 500) {
            //                     msg = 'Internal Server Error [500].';
            //                 } else if (exception === 'parsererror') {
            //                     msg = 'Requested JSON parse failed.';
            //                 } else if (exception === 'timeout') {
            //                     msg = 'Time out error.';
            //                 } else if (exception === 'abort') {
            //                     msg = 'Ajax request aborted.';
            //                 } else {
            //                     msg = 'Uncaught Error.\n' + jqXHR.responseText;
            //                 }
            //                 alert(msg);
            //             }
            //         });
            //     }
            // })
            
            function sendImageWhatsapp(id, linkImage){
                console.log(linkImage)
                $.ajax({
                        type: "GET",
                        url: `/kasir/whatsapp-image/${id}/${linkImage}`,
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
                                toastr.success('Berhasil mengirim invoice');
                            } else {
                                toastr.error('Gagal mengirim invoice');
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
            
            $('#btninvoice').click(function(e) {
                const el = $(this);
                const id = el.data('id')
                let nohp = el.data('nohp')

                if(id) {
                    e.preventDefault();
                    $.ajax({
                        type: "GET",
                        url: `/kasir/api-struk/${id}`,
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
                            console.log(response)
                            var penjualan = response.penjualan;
                            var detail = response.detail
                            // Membuat objek Date baru untuk tanggal hari ini
                            var today = new Date();
                    
                            // Mendapatkan komponen tanggal
                            var day = today.getDate(); // Tanggal (1-31)
                            var month = today.getMonth() + 1; // Bulan (0-11), ditambahkan 1 agar sesuai (1-12)
                            var year = today.getFullYear(); // Tahun (4 digit)
                            // Menyusun tanggal dalam format "DD-MM-YYYY"
                            var formattedDate = day + '-' + month + '-' + year;

                            var formatter = new Intl.NumberFormat('id-ID', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                            
                            // Membuat elemen container utama
                            var container = document.createElement('div');
                            container.className = 'container m-0';
                            container.id = 'DivIdToPrint';
                            container.style.backgroundColor = '#FFFFFF'
                            container.style.zIndex = 999
                            
                            // Menambahkan logo jika tersedia
                            if(logo){
                                var logoCenter = document.createElement('center');
                                var logoImg = document.createElement('img');
                                logoImg.src = '/assets/img/logo/' + logo;
                                logoImg.alt = '';
                                logoImg.height = 70;
                                logoCenter.appendChild(logoImg);
                                container.appendChild(logoCenter);
                            }
                            
                            // Membuat header struk
                            var receiptHeader = document.createElement('div');
                            receiptHeader.className = 'receipt_header';
                            
                            var h1 = document.createElement('h1');
                            h1.innerHTML = `Struk Penjualan <span>${penjualan.nama_toko}</span>`;
                            receiptHeader.appendChild(h1);
                            
                            var h2_1 = document.createElement('h2');
                            h2_1.style.marginBottom = '2px';
                            h2_1.innerHTML = `<span>Kasir: ${penjualan.kasir}</span>`;
                            receiptHeader.appendChild(h2_1);
                            
                            var h2_2 = document.createElement('h2');
                            h2_2.style.marginBottom = '2px';
                            h2_2.innerHTML = `<span>${penjualan.nohp}</span>`;
                            receiptHeader.appendChild(h2_2);
                            
                            var h2_3 = document.createElement('h2');
                            h2_3.innerHTML = `<span>${penjualan.alamat}</span>`;
                            receiptHeader.appendChild(h2_3);
                            
                            container.appendChild(receiptHeader);
                            
                            // Membuat body struk
                            var receiptBody = document.createElement('div');
                            receiptBody.className = 'receipt_body';
                            
                            // Menambahkan tanggal dan waktu
                            var dateTimeCon = document.createElement('div');
                            dateTimeCon.className = 'date_time_con';
                            var dateDiv = document.createElement('div');
                            dateDiv.className = 'date';
                            dateDiv.textContent = formattedDate;
                            dateTimeCon.appendChild(dateDiv);
                            receiptBody.appendChild(dateTimeCon);
                            
                            // Membuat tabel items
                            var itemsDiv = document.createElement('div');
                            itemsDiv.className = 'items';
                            var table = document.createElement('table');
                            
                            // Membuat thead
                            var thead = document.createElement('thead');
                            thead.innerHTML = '<tr><th>QTY</th><th>ITEM</th><th>TOTAL</th></tr>';
                            table.appendChild(thead);
                            
                            // Membuat tbody
                            var tbody = document.createElement('tbody');
                            detail.forEach(function(item) {
                                var tr = document.createElement('tr');
                    
                                var td1 = document.createElement('td');
                                td1.textContent = item.qty;
                    
                                var td2 = document.createElement('td');
                                td2.textContent = item.barang;
                    
                                var td3 = document.createElement('td');
                                td3.textContent = new Intl.NumberFormat().format(item.total);
                    
                                tr.appendChild(td1);
                                tr.appendChild(td2);
                                tr.appendChild(td3);
                    
                                tbody.appendChild(tr);
                            });
                            
                            table.appendChild(tbody);
                            
                            // Membuat tfoot untuk pelanggan dan pembayaran
                            var tfoot1 = document.createElement('tfoot');
                            if(penjualan.pelanggan){
                                var tr1 = document.createElement('tr');
                                var td1_1 = document.createElement('td');
                                td1_1.textContent = 'Customer';
                                var td1_2 = document.createElement('td');
                                var td1_3 = document.createElement('td');
                                td1_3.textContent = penjualan.pelanggan;
                                tr1.appendChild(td1_1);
                                tr1.appendChild(td1_2);
                                tr1.appendChild(td1_3);
                                tfoot1.appendChild(tr1);
                            }
                            var tr2 = document.createElement('tr');
                            var td2_1 = document.createElement('td');
                            td2_1.textContent = 'Pembayaran';
                            var td2_2 = document.createElement('td');
                            var td2_3 = document.createElement('td');
                            td2_3.textContent = penjualan.nama_tipe;
                            tr2.appendChild(td2_1);
                            tr2.appendChild(td2_2);
                            tr2.appendChild(td2_3);
                            tfoot1.appendChild(tr2);
                            table.appendChild(tfoot1);
                            
                            // Membuat tfoot untuk total harga
                            var tfoot2 = document.createElement('tfoot');
                            
                            var subtotal = formatter.format(penjualan.subtotal); // "1.234.568"
                            var rows = [
                                { label: 'Harga', value: subtotal, bold: false },
                                { label: 'Discount', value: `Rp. ${penjualan.discount}`, bold: false },
                                { label: 'PPN', value: `Rp. ${penjualan.ppn}`, bold: false },
                                { label: 'Biaya Layanan', value: `Rp. ${penjualan.biaya_layanan}`, bold: false },
                                { label: 'Total', value: `Rp. ${penjualan.total}`, bold: true }
                            ];
                            
                            rows.forEach(function(row) {
                                var tr = document.createElement('tr');
                                var td1 = document.createElement('td');
                                td1.textContent = row.label;
                                if (row.bold) td1.style.fontWeight = 'bold';
                                var td2 = document.createElement('td');
                                var td3 = document.createElement('td');
                                td3.textContent = row.value;
                                if (row.bold) td3.style.fontWeight = 'bold';
                                tr.appendChild(td1);
                                tr.appendChild(td2);
                                tr.appendChild(td3);
                                tfoot2.appendChild(tr);
                            });
                            
                            table.appendChild(tfoot2);
                            itemsDiv.appendChild(table);
                            receiptBody.appendChild(itemsDiv);
                            container.appendChild(receiptBody);
                            
                            // Menambahkan pesan terima kasih
                            var thankYou = document.createElement('h3');
                            thankYou.textContent = 'Terimakasih!';
                            container.appendChild(thankYou);
                            
                            // Menambahkan elemen ke body atau elemen lain di DOM
                            document.body.appendChild(container);
                            
                            var node = document.getElementById('DivIdToPrint');
                            console.log(node);
                            
                            domtoimage.toBlob(node)
                            .then(function (blob) {
                                console.log(blob)
                                var formData = new FormData();
                                formData.append('image', blob, 'captured-image.png');   
                                
                                $.ajax({
                                    url: '/kasir/save-image/'+id,
                                    type: 'POST',
                                    processData: false,
                                    contentType: false,
                                    data: formData,
                                    success: function(response) {
                                        console.log('Image saved successfully');
                                    },
                                    error: function(error) {
                                        console.error('oops, something went wrong!', error);
                                        sendImageWhatsapp(id, id)
                                    }
                                });
                            });
                
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
            })
        
            modalNpay.on('shown.bs.modal', function() {
                npayInterval = setInterval(function() {
                    const formData = new FormData();
                    formData.append('invoiceId', npayData.invoiceId)
                    $.ajax({
                        type: "POST",
                        url: '/kasir/npay/check',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: "JSON",
                        success: function(response) {
                            if (response.data) {
                                clearInterval(npayInterval)
                                toastr.success("Pembayaran berhasil!");
                                modalNpay.modal('hide');
                                simpanTransaksi()
                            }
                        },
                    });
                }, 1000)
            })
            
            modalNpay.on('hidden.bs.modal', function() {
                clearInterval(npayInterval)
            })

            function handleRadioChange(radio) {
                let value = radio.value;
                let inputTanggalan = document.getElementById('tanggal_preorder');
                let labelPilihTanggal = document.getElementById('label_tanggal_preorder');

                console.log('Selected value: ' + radio.value);
                if(value == 1){
                    inputTanggalan.hidden = true;
                    labelPilihTanggal.hidden = true;
                }else{
                    inputTanggalan.hidden = false;
                    labelPilihTanggal.hidden = false;
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Mendapatkan elemen input tanggal
                var datePicker = document.getElementById('tanggal_preorder');
              
                 // Mendapatkan tanggal dan waktu hari ini
                var today = new Date();
                var day = today.getDate();
                var month = today.getMonth() + 1; // Januari adalah 0
                var year = today.getFullYear();
                var hours = today.getHours();
                var minutes = today.getMinutes();

                // Format tanggal ke YYYY-MM-DDTHH:MM
                if(day < 10) {
                    day = '0' + day;
                }
                if(month < 10) {
                    month = '0' + month;
                }
                if(hours < 10) {
                    hours = '0' + hours;
                }
                if(minutes < 10) {
                    minutes = '0' + minutes;
                }
                var todayDateTime = year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;

                // Set nilai minimum pada input datetime-local
                var dateTimePicker = document.getElementById('tanggal_preorder');
                dateTimePicker.min = todayDateTime;
            });

            function getNotif(){
                $.ajax({
                        type: "GET",
                        url: `kasir/api-notif`,
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
                            var listNotif = document.getElementById('list-notif');
                            listNotif.innerHTML = '';
                            if(response.data.length > 0){
                                let countNotif = document.getElementById('notif-2');
                                countNotif.textContent = response.data.length
                                response.data.forEach(data => {
                                    console.log(data)
                                    let div = document.createElement('div');
                                    div.style.width = '300px';
                                    div.style.padding = '20px';

                                    let nama = document.createElement('b');
                                    nama.textContent = data.data_notif.pelanggan

                                    div.appendChild(nama);

                                    
                                    data.detail_penjualan.forEach(item => {
                                        let makanan = document.createElement('p');
                                        makanan.textContent = item.barang

                                        let qty = document.createElement('small');
                                        qty.textContent = ' qty: ' + item.qty;

                                        makanan.appendChild(qty)
                                        div.appendChild(makanan);
                                    });
                                
                                    listNotif.appendChild(div);
                                });
                                
                            }else{
                                let div = document.createElement('div');
                                div.style.width = '300px';
                                div.style.padding = '20px';

                                let kosong = document.createElement('b');
                                kosong.textContent = "Tidak Ada Pesanan";

                                div.appendChild(kosong);
                                listNotif.appendChild(div);
                            }
                        

                        },
                        error: function(jqXHR, textStatus, errorThrown, exception){

                        }
                    });
            }

            setInterval(getNotif, 10800000);
            // setInterval(getNotif, 3000);
        </script>
</body>

</html>