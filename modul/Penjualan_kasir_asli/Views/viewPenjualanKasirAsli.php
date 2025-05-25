<?= $this->extend('layout/template'); ?>
<?= $this->section('css') ?>

<?php 
    use Config\Database;
    use Config\Services;
    
    $this->db         = Database::connect();
    $this->session    = Services::session();
    
    $id_toko = $this->session->get('id_toko');
?>

<link href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="/assets/extensions/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" />

<link href="/assets/extensions/fancybox/fancybox.css" rel="stylesheet" />

<style>
    .img-preview {
        max-width: 100%;
        max-height: 100px;
    }

    #container-product {
        border: 2px solid gray;
        border-radius: 5px;
        max-height: 100vh;
        /* Anda bisa menyesuaikan tinggi maksimum sesuai kebutuhan */
        overflow-y: auto;
        /* Mengaktifkan scroll vertikal */
        padding: 10px;
        /* Opsional: menambahkan padding untuk memberikan ruang di sekitar konten */
    }

    #container-product-terpilih {
        border: 2px solid gray;
        border-radius: 5px;
        max-height: 80vh;
        height: 80vh;
        /* Anda bisa menyesuaikan tinggi maksimum sesuai kebutuhan */
        overflow-y: auto;
        /* Mengaktifkan scroll vertikal */
        padding: 10px;
        /* Opsional: menambahkan padding untuk memberikan ruang di sekitar konten */
    }

    #container-result {
        border: 2px solid gray;
        border-radius: 5px;
        max-height: 80vh;
        /* Anda bisa menyesuaikan tinggi maksimum sesuai kebutuhan */
        overflow-y: auto;
        /* Mengaktifkan scroll vertikal */
        padding: 10px;
        /* Opsional: menambahkan padding untuk memberikan ruang di sekitar konten */
    }

    .card-custom {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        width: 100%;
        transition: transform 0.2s;
    }

    .card-custom:hover {
        transform: translateY(-10px);
    }

    .card-custom-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .card-custom-content {
        padding: 16px;
    }

    .card-custom-title {
        font-size: 1.5em;
        margin: 0 0 10px;
    }

    .card-custom-description {
        font-size: 1em;
        color: #666;
        margin-bottom: 20px;
    }

    .card-custom-button {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 1em;
        transition: background-color 0.3s;
    }

    .card-custom-button:hover {
        background-color: #0056b3;
    }

    /* Styling the radio button container */
    .radio-container {
        align-items: center;
        margin-bottom: 15px;
        cursor: pointer;
    }

    /* Hiding the default radio button */
    .radio-container input[type="radio"] {
        display: none;
    }

    /* Styling the custom radio button */
    .radio-container label.custom-radio {
        display: flex;
        align-items: center;
        padding: 10px;
        border: 2px solid #ccc;
        border-radius: 5px;
        background-color: #fff;
        transition: border-color 0.3s, background-color 0.3s;
    }

    .radio-container input[type="radio"]:checked + label.custom-radio {
        border-color: #2196F3;
        background-color: #e7f3fe;
    }

    .radio-container label.custom-radio:hover {
        border-color: #999;
    }

    /* Styling the icon */
    .radio-container .icon {
        width: 24px;
        height: 24px;
        margin-right: 10px;
        background-color: #2196F3; /* Example color */
    }

    /* Styling the text */
    .radio-container .text {
        font-size: 16px;
        color: #333;
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
                        <form action="<?= base_url('penjualan/export-excel') ?>" method="POST">
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
            <button onclick="tambah()" class="btn btn-primary btn-round ms-auto"><i class="fa fa-plus"></i> Tambah
                Transaksi</button>
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
                <form id="form"  autocomplete="off">
                    <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="input_pelanggan" class="form-label">Pelanggan</label>
                            <select class="form-select" name="input_pelanggan" id="input_pelanggan">
                                <option value="" disabled selected>Pilih Pelanggan</option>
                                <?php foreach ($pelanggan as $key) : ?>
                                <option value="<?php echo $key->id; ?>"><?php echo $key->nama; ?></option>
                                <?php endforeach ?>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="diskon" class="form-label">Diskon</label>
                            <select class="form-select" name="diskon" id="diskon">
                                <option value="" disabled selected>Pilih Diskon</option>
                                <?php foreach ($discount as $key) : ?>
                                <?php if ($key->tipe == 1) { ?>
                                <option value="<?php echo $key->id; ?>" data-nominal="<?php echo $key->jumlah; ?>"
                                    data-satuan="<?php echo $key->tipe; ?>"><?php echo $key->nama_discount; ?> (
                                    <?php echo $key->jumlah; ?>% )</option>
                                <?php } else { ?>
                                <option value="<?php echo $key->id; ?>" data-nominal="<?php echo $key->jumlah; ?>"
                                    data-satuan="<?php echo $key->tipe; ?>"><?php echo $key->nama_discount; ?> (
                                    Rp.<?php echo number_format($key->jumlah); ?> )</option>
                                <?php } ?>
                                <?php endforeach ?>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input class="form-control" type="datetime-local" name="tanggal" id="tgl_custom" required> 
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <hr>
                    <h4>Pilih Produk</h4>
                    <div class="row g-4">
                        <div class="col-md-6 pe-md-4" style="border-right: 1px solid #d9dee3;">

                            <div class="container" id="container-product"
                                style="border: 2px solid gray; border-radius: 5px;">

                                <?php foreach ($barang as $item) : 
                                    $varian = $this->db->query("SELECT id, nama_varian, id_barang, harga_jual, harga_modal FROM varian WHERE id_barang = '$item->id' AND status = 1")->getResult(); 
                                    $totalv = $this->db->query("SELECT COUNT(id) as total FROM varian WHERE id_barang = '$item->id' AND status = 1")->getRow()->total;
                                    $bahan = $this->db->query("SELECT SUM(b.harga * a.qty) as harga, SUM(b.biaya * a.qty) as biaya FROM bahan_barang a JOIN bahan_baku b ON a.id_bahan_baku = b.id WHERE a.id_barang = '$item->id'")->getRow();
                                ?>
                                
                                <div class="card-custom list-product mt-3 p-3" data-id="<?php echo $item->id ?>"
                                    data-photo="<?php echo $item->foto ?>" data-price="<?php echo ($totalv>=1) ? $varian[0]->harga_jual : $item->harga_jual; ?>"
                                    data-name="<?php echo $item->nama_barang ?>" data-modal="<?php echo ($totalv >= 1) ? $varian[0]->harga_modal : $item->harga_modal ?>">
                                    <div class="card-custom-body">
                                        <div class="row">
                                            <div class="col-4">
                                                <?php if ($item->foto) { ?>
                                                <image data-fancybox
                                                    data-src="/assets/img/barang/<?php echo $item->foto ?>"
                                                    src="/assets/img/barang/<?php echo $item->foto ?>" width="80"
                                                    style="cursor: zoom-in; border-radius: 5px;" />
                                                <?php } else { ?>
                                                <image src="/assets/img/noimage.png" width="80"
                                                    style="cursor: zoom-in;" />';
                                                <?php } ?>
                                            </div>
                                            <div class="col-6">
                                                <p> <?php echo $item->nama_barang; ?> </p>
                                                <p class="price">  <?php if($totalv >= 1) {
                                                    echo formatRupiah($varian[0]->harga_jual, "Rp. ");
                                                }else{
                                                    echo formatRupiah($item->harga_jual, "Rp. ");
                                                }  ?> </p>
                                            </div>
                                        </div>
                                        
                                        <hr>
                                        <?php if($totalv >= 1) {
                                            foreach ($varian as $key => $dataVariant) :
                                                if($key == 0 ){ ?>
                                                    <button type="button" class="btn btn-outline-primary active list-variant product-variant-<?php echo $dataVariant->id_barang ?>"
                                                     data-namavariant="<?php echo $dataVariant->nama_varian ?>"  
                                                     data-idbarang="<?php echo $dataVariant->id_barang ?>" 
                                                     data-idvariant="<?php echo $dataVariant->id ?>" 
                                                     data-hargavariant="<?php echo $dataVariant->harga_jual ?>"
                                                     data-modal="<?php echo $dataVariant->harga_modal ?>"
                                                     onclick="handleClickVariant(this)" ><?php echo $dataVariant->nama_varian ?></button>
                                                <?php } else{ ?>
                                                    <button type="button" class="btn btn-outline-primary list-variant product-variant-<?php echo $dataVariant->id_barang ?>" 
                                                    data-namavariant="<?php echo $dataVariant->nama_varian ?>" 
                                                    data-idbarang="<?php echo $dataVariant->id_barang ?>" 
                                                    data-idvariant="<?php echo $dataVariant->id ?>" 
                                                    data-hargavariant="<?php echo $dataVariant->harga_jual ?>"
                                                    data-modal="<?php echo $dataVariant->harga_modal ?>"
                                                    onclick="handleClickVariant(this)" ><?php echo $dataVariant->nama_varian ?></button>
                                                <?php } ?>
                                        <?php endforeach ?>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php endforeach ?>

                            </div>

                        </div>
                        <div class="col-md-6 ps-md-4">
                            <div class="container" id="container-product-terpilih"
                                style="border: 2px solid gray; border-radius: 5px;">

                            </div>

                            <div class="container mt-2" id="container-result"
                                style="border: 2px solid gray; border-radius: 5px;">
                                <div class="row">
                                    <div class="col-6">Subtotal : </div>
                                    <div class="col-6" id="subtotal">Rp. 0</div>
                                </div>
                                <div class="row">
                                    <div class="col-6">Discount : </div>
                                    <div class="col-6" id="discount">Rp. 0</div>
                                </div>
                                <div class="row">
                                    <div class="col-6">Biaya Layanan : </div>
                                    <div class="col-6" id="biaya-layanan">Rp. 0</div>
                                </div>
                                <div class="row">
                                    <div class="col-6">PPN(<?php echo $toko->ppn; ?>%) : </div>
                                    <div class="col-6" id="ppn">Rp. 0</div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-6"><strong>Total :</strong> </div>
                                    <div class="col-6" id="total">Rp. 0</div>
                                </div>

                            </div>

                            <!--begin::Payment Method-->
                            <div class="m-0">
                                <h3 class="fw-bold text-gray-800 mb-2">Metode Pembayaran</h3>

                                <div class="row mb-12">
                                    <?php $no = 0; foreach ($bayar as $key) : $no++;?>
                                        <div class="col-6">
                                            <div class="radio-container">
                                                <input type="radio" id="payment_<?php echo $no; ?>" name="payment" value="<?php echo $key->id; ?>" <?php if ($no == 1) : ?> checked <?php endif; ?>>
                                                <label for="payment_<?php echo $no; ?>" class="custom-radio">
                                                <i class="<?php echo $key->icon; ?> fs-2hx mb-2 pe-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> &nbsp; &nbsp; 
                                                    <span class="text"><?php echo $key->nama_tipe ?></span>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSimpan" class="btn btn-primary" disabled>Simpan</button>
                </div>
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

<script src="/assets/extensions/fancybox/fancybox.js"></script>

<script>
    var table;
    var modal = $('#modal');
    var modald = $('#modald');
    var listProductChoose = [];
    var ppn = <?php echo $toko-> ppn; ?> ;
    var biayaLayanan = <?php echo $toko-> biaya_layanan; ?> ;
    var nominalDiskon = 0;
    var satuanDiskon = 0;
    var idDiskon = 0;

    var startDateInput = document.getElementById("dari");
    var endDateInput = document.getElementById("sampai");

    // Event listener untuk start date
    startDateInput.addEventListener("change", function () {
        var startDate = startDateInput.value;
        let dariLabarugi = document.getElementById("dariPenjualan");

        dariLabarugi.value = startDate;
        console.log("Start Date changed to:", startDate);
    });

    // Event listener untuk end date
    endDateInput.addEventListener("change", function () {
        var endDate = endDateInput.value;
        let sampaiLabarugi = document.getElementById("sampaiPenjualan");

        sampaiLabarugi.value = endDate;
        console.log("End Date changed to:", endDate);
    });

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
                url: '/penjualan/datatable',
                method: 'POST',
                data: function (d) {
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

    function tambah() {
        $('#id').val('');
        $('#form')[0].reset();
        clearBarang()
        listProductChoose = [];
        syncTotal()
        
        modal.modal('show');
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
            beforeSend: function () {
                showblockUI();
            },
            complete: function () {
                hideblockUI();
            },
            success: function (data) {
                toastr.success('Data Berhasil dihapus');
                modald.modal('hide');
                table.ajax.reload();
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
    }

    $('#pelanggan').on('change', function () {
        table.ajax.reload();
    });

    $('#metode').on('change', function () {
        table.ajax.reload();
    });

    $('#tgl').on('change', function () {
        table.ajax.reload();
    });

    $('#reset').on('click', function () {
        $('#pelanggan').prop('selectedIndex', 0);
        $('#metode').prop('selectedIndex', 0);
        $('#tgl').val('');

        table.ajax.reload();
    });

    $('.list-product').on('click', function (e) {
        let item = $(this);
        let itemId = item.data('id');
        let itemName = item.data('name');
        let itemPrice = item.data('price');
        let itemPhoto = item.data('photo');
        let itemModal = item.data('modal')

        let data = {
            id: itemId,
            name: itemName,
            price: itemPrice,
            modal: itemModal,
            qty: 1,
            photo: itemPhoto,
            variantId : null,
            variantName: null,
        }

        let idVariant = '';
        let namaVariant = '';
        let activeButton = '';
        if(item.has('.list-variant').length > 0){
            activeButton = $(`.product-variant-${itemId}`).filter('.active');
            idVariant = activeButton.data('idvariant');
            namaVariant = activeButton.data('namavariant');
        }

        let exist = listProductChoose.find(item => item.id === itemId);
        if (!exist) {
            if(activeButton.length > 0){
                data.variantId =idVariant;
                data.variantName = namaVariant;
            }
        
            listProductChoose.push(data);
            buildProductCard(data)
            syncTotal();
        }else{
            let existProductVariant = listProductChoose.find(item => item.variantId === idVariant)

            if(!existProductVariant){
                data.variantId =idVariant;
                data.variantName = namaVariant;

                listProductChoose.push(data);
                buildProductCard(data)
                syncTotal();
            }
        }

        $('#btnSimpan').removeAttr('disabled')
    });

    function buildProductCard(itemData) {
        let fotoSrc = itemData.photo ? `/assets/img/barang/${itemData.photo}` : '/assets/img/noimage.png';
        let hargaFormatted = formatRupiah(itemData.price.toString(), "Rp. ");
        let namaProduct = itemData.variantName ? `<p>${itemData.name} - ${itemData.variantName}</p>`  : `<p>${itemData.name}</p>`;
        

        let html = `<div class="card-custom list-product-choose mt-3 p-3" data-id="${itemData.id}" data-variantid="${itemData.variantId}">
                <div class="card-custom-body">
                    <div class="row">
                        <div class="col-4">
                            <img data-fancybox
                                data-src="${fotoSrc}"
                                src="${fotoSrc}" width="80"
                                style="cursor: zoom-in; border-radius: 5px;" />
                        </div>
                        <div class="col-6">
                            ${namaProduct}
                            <p>${hargaFormatted}</p>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button class="btn btn-outline-secondary decrement" type="button" data-id="${itemData.id}" data-variantid="${itemData.variantId}">-</button>
                                </div>
                                <input type="text" class="form-control text-center input-quantity" id="quantity" value="1"  readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary increment" type="button"  data-id="${itemData.id}" data-variantid="${itemData.variantId}">+</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-2">
                            <button type="button" onclick="removeItemChoose(this)" data-id="${itemData.id}" data-variantid="${itemData.variantId}" class="btn-close" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            </div>`;


        $('#container-product-terpilih').append(html);

        handleIncrementQuantity()

        handleDecrementQuantity()
    }

    function formatRupiah(angka, prefix) {
        var number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix + rupiah;
    }

    function removeItemChoose(card) {
        let button = $(card);
        let id = button.data('id');
        let variantId = button.data('variantid');
        
        // Mencari indeks dari elemen dengan id 2
        const indexToRemove = listProductChoose.findIndex(item => item.id === id);

        if (indexToRemove !== -1) {
            if(variantId){
                const variantIndex = listProductChoose.findIndex(item => item.variantId === variantId);
                if(variantIndex !== -1){
                    listProductChoose.splice(variantIndex, 1);
                    $('#container-product-terpilih').find(`.card-custom[data-variantid="${variantId}"]`).remove();        
                }
            }else{
                // Menghapus elemen pada indeks yang ditemukan
                listProductChoose.splice(indexToRemove, 1);
                $('#container-product-terpilih').find(`.card-custom[data-id="${id}"]`).remove();
            }
            syncTotal();
        }

        console.log(listProductChoose.length);
        if(listProductChoose.length == 0){
            $('#btnSimpan').attr('disabled', true);
        }
    }

    function handleIncrementQuantity(){
        // Increment button click event
        $('.increment').off().on('click', function () {
            let widgetButton = $(this);
            let itemId = widgetButton.data('id');
            let variantId = widgetButton.data('variantid');

            let data = listProductChoose.find(item => item.id === itemId);
            if (data) {
                if(variantId){
                    let productVariant = listProductChoose.find(item => item.variantId === variantId)
                    if(productVariant){
                        productVariant.qty += 1;
                    }
                }else{
                    data.qty += 1;
                }
                let input = $(this).closest('.input-group').find('.input-quantity');
                let currentValue = parseInt(input.val());
                input.val(currentValue + 1);
                syncTotal();
            }
        });
    }

    handleIncrementQuantity();


    function handleDecrementQuantity(){
        // Decrement button click event
        $('.decrement').off().on('click', function () {
            let widgetButton = $(this);
            let itemId = widgetButton.data('id');
            let variantId = widgetButton.data('variantid');

            let input = $(this).closest('.input-group').find('.input-quantity');
            let currentValue = parseInt(input.val());

            let data = listProductChoose.find(item => item.id === itemId);
            if (data) {
                if(variantId){
                    let productVariant = listProductChoose.find(item => item.variantId === variantId)
                    if(productVariant){
                        productVariant.qty -= 1;
                    }
                }else{
                    if (currentValue > 1) {
                        data.qty -= 1;
                    }    
                }

                if(currentValue > 1){
                    input.val(currentValue - 1);
                    syncTotal();
                }
                
            }
        });
    }

    handleDecrementQuantity();
    

    $('#diskon').off().change(function () {
        // Mengambil nilai dari opsi yang dipilih
        var selectedValue = $(this).val();
        // Mengambil data-nominal dan data-satuan dari opsi yang dipilih
        nominalDiskon = $(this).find('option:selected').data('nominal');
        satuanDiskon = $(this).find('option:selected').data('satuan');
        idDiskon = selectedValue;
        
        syncTotal();
    });

    function syncTotal() {
        let subTotal = 0;
        let total = 0;
        let pajak = 0;
        let discount = 0;
        listProductChoose.forEach(function (item) {
            let priceItemTotal = item.price * item.qty;
            subTotal += priceItemTotal
        });

        // kalo discount persen
        if(satuanDiskon == 1){
            discount = subTotal * nominalDiskon / 100;
        }else{
            discount = nominalDiskon;
        }

        pajak = (subTotal - discount + biayaLayanan) * ppn / 100;
        
        total = subTotal - discount + biayaLayanan + pajak;

        $('#subtotal').text(formatRupiah(subTotal.toString(), "Rp. "));
        $('#discount').text("-" + formatRupiah(discount.toString(), "Rp. "));
        $('#biaya-layanan').text(formatRupiah(biayaLayanan.toString(), "Rp. "));
        $('#ppn').text(formatRupiah(pajak.toString(), "Rp. "));

        $('#total').text(formatRupiah(total.toString(), "Rp. "));
    }

    function handleClickVariant(element){
        let widget = $(element);
        let idBarang = widget.data('idbarang');
        let hargaVariant = widget.data('hargavariant');
        let modalVariant = widget.data('modal')
        console.log(hargaVariant);

        let product = $(`.list-product[data-id="${idBarang}"]`);
        // Mengganti nilai data-price
        product.data('price', hargaVariant); // Menggunakan .data() untuk mengubah nilai
        product.data('modal', modalVariant);

        // Memperbarui elemen yang menampilkan harga
        product.find('p').last().text(formatRupiah(hargaVariant.toString(), "Rp. ")); // Menggunakan .last() untuk mendapatkan <p> terakhir

        $(`.product-variant-${idBarang}`).removeClass('active');

        widget.addClass('active');
    }

    $('#btnSimpan').off().on('click', function(e){
        e.preventDefault();
        var isValid = true;
        $('#form input[required], select[required], textarea[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid'); // Menandai input yang tidak valid
                // Tampilkan pesan kesalahan jika diperlukan
                toastr.warning('Silakan isi semua field yang diperlukan.');
            } else {
                $(this).removeClass('is-invalid'); // Menghapus tanda jika valid
            }
        });

        if (!isValid) {
            return; // Hentikan eksekusi jika ada input yang tidak valid
        }

        var formData = new FormData();
        listProductChoose.forEach(function(item){
            formData.append('id_barang[]', item.id);
            var idVariant = (item.variantId) ? item.variantId : ''; 
            formData.append('id_varian[]', idVariant);
            let namaProduct = (item.variantName != null) ? item.name + ' - ' + item.variantName : item.name;
            formData.append('barang[]', namaProduct);
            formData.append('qty[]', item.qty);
            formData.append('harga[]', item.price);
            formData.append('modal[]', item.modal);
        });

        let textTotal = $('#total').text().trim();
        let total = parseInt(textTotal.replace(/[^\d]/g, ""));

        formData.append('granttotal', total);
        formData.append('subtotal', 0);

        if(idDiskon != 0){
            formData.append('discount2', nominalDiskon)
            formData.append('discount', idDiskon);
        }else{
            formData.append('discount2', 0)
            formData.append('discount', '');
        }

        let textPpn = $('#ppn').text().trim();
        let ppn = parseInt(textPpn.replace(/[^\d]/g, ""));
        formData.append('ppn', ppn);

        var selectedMethod = $('input[name="payment"]:checked').val();
        formData.append('method', selectedMethod);
        formData.append('tanggal_preorder', '');

        var selectedTanggalCustom = $('#tgl_custom').val();
        formData.append('tgl_custom', selectedTanggalCustom);

        var selectedPelanggan = $('#input_pelanggan').val();
        if(selectedPelanggan != null){
            formData.append('pelanggan', selectedPelanggan);
        }       

        console.log(selectedTanggalCustom)
        console.log(formData)
        var url = "/kasir/simpan";
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
                    console.log(response)
                    $('#form')[0].reset();
                    toastr.success('Transaksi berhasil');
                    table.ajax.reload();
                    modal.modal('hide');
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
    })

    $(document).ready(function() {
        $('.radio-container input[type="radio"]').on('click', function() {
            // Handle click event
            $('.radio-container input[type="radio"]').not(this).prop('checked', false);
            $(this).prop('checked', true);
        });
    });

    function clearBarang(){
        $('#container-product-terpilih').empty();

        syncTotal();
    }
</script>
<?= $this->endSection() ?>