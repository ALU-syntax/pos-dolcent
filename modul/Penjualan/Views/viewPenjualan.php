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
                <form id="form" autocomplete="off">
                    <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="pelanggan" class="form-label">Pelanggan</label>
                            <select class="form-select" name="pelanggan" id="pelanggan">
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
                                <option value="<?php echo $key->id; ?>"><?php echo $key->nama_discount; ?> (
                                    <?php echo $key->jumlah; ?>% )</option>
                                <?php } else { ?>
                                <option value="<?php echo $key->id; ?>"><?php echo $key->nama_discount; ?> (
                                    Rp.<?php echo number_format($key->jumlah); ?> )</option>
                                <?php } ?>
                                <?php endforeach ?>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tanggal" class="form-label">tanggal</label>
                            <input class="form-control" type="date" name="tanggal">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <hr>
                    <h4>Pilih Produk</h4>
                    <div class="row g-4">
                        <div class="col-md-6 pe-md-4" style="border-right: 1px solid #d9dee3;">

                            <div class="container" id="container-product"
                                style="border: 2px solid gray; border-radius: 5px;">

                                <?php foreach ($barang as $item) : ?>
                                <div class="card-custom list-product mt-3 p-3" data-id="<?php echo $item->id ?>"
                                    data-foto="<?php echo $item->foto ?>"
                                    data-hargaJual="<?php echo $item->harga_jual ?>"
                                    data-nama="<?php echo $item->nama_barang ?>">
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
                                                <p> <?php echo formatRupiah($item->harga_jual, "Rp. ") ?> </p>
                                            </div>
                                        </div>
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
                                    <div class="col-6"></div>
                                </div>
                                <div class="row">
                                    <div class="col-6">PPN(1%) : </div>
                                    <div class="col-6"></div>
                                </div>
                                <div class="row">
                                    <div class="col-6">Biaya Layanan : </div>
                                    <div class="col-6"></div>
                                </div>
                                <div class="row">
                                    <div class="col-6">Discount : </div>
                                    <div class="col-6"></div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-6"><strong>Total :</strong> </div>
                                    <div class="col-6"></div>
                                </div>

                            </div>

                        </div>

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

        let exist = listProductChoose.find(item => item.id === itemId);
        if (!exist) {
            let itemName = item.data('name');
            let data = {
                id: itemId,
                nama: "Koko",
                harga: 20000,
                qty: 1,
            }
            buildProductCard(item)
        }
    });

    function buildProductCard(itemData) {
        let fotoSrc = itemData.foto ? `/assets/img/barang/${itemData.foto}` : '/assets/img/noimage.png';
        let hargaFormatted = formatRupiah(itemData.harga_jual, "Rp. ");

        let html = `<div class="card-custom list-product-choose mt-3 p-3">
                <div class="card-custom-body">
                    <div class="row">
                        <div class="col-4">
                            <img data-fancybox
                                data-src="${fotoSrc}"
                                src="${fotoSrc}" width="80"
                                style="cursor: zoom-in; border-radius: 5px;" />
                        </div>
                        <div class="col-6">
                            <p>${itemData.nama_barang}</p>
                            <p>${hargaFormatted}</p>
                        </div>
                    </div>
                </div>
            </div>`;

        $('#container-product-terpilih').append(html);
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
</script>
<?= $this->endSection() ?>