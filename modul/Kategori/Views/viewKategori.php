<?= $this->extend('layout/template'); ?>
<?= $this->section('css') ?>
<link href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="/assets/extensions/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" />
<link href="/assets/extensions/select2/dist/css/select2.min.css" rel="stylesheet" />
<link href="/assets/extensions/select2-bootstrap-5-theme-1.3.0/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<link href="/assets/extensions/select2-bootstrap-5-theme-1.3.0/select2-bootstrap-5-theme.rtl.min.css" rel="stylesheet" />

<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div class="row">
        <div class="col-6">
            <h3>Daftar Kategori Produk</h3>
        </div>
        <div class="col-6 text-end">
            <button class="btn btn-primary" onclick="tambah()"><i class="fa fa-plus"></i>&nbsp; Tambah Kategori</button>
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
                                        <th>Nama Kategori</th>
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
                        <label for="nama" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama kategori produk">
                        <div class="invalid-feedback"></div>
                    </div>

                    <label for="iconSelect2" class="form-label">Icon</label>
                    <div class="mb-3 form-group d-flex">
                        <select class="form-select w-100" name="icon" id="iconSelect2" required>
                            <option value="fa-mug-hot"><i class="fa-solid fa-mug-hot"></i> Mug Hot</option>
                            <option value="fa-lemon"><i class="fa-solid fa-lemon"></i> Lemon</option>
                            <option value="fa-flask"><i class="fa-solid fa-flask"></i> Flask</option>
                            <option value="fa-fish"><i class="fa-solid fa-fish"></i> Fish</option>
                            <option value="fa-mug-saucer"><i class="fa-solid fa-mug-saucer"></i> Mug Saucer</option>
                            <option value="fa-seedling"><i class="fa-solid fa-seedling"> Seedling</i></option>
                            <option value="fa-wine-bottle"><i class="fa-solid fa-wine-bottle"></i> Wine Bottle</option>
                            <option value="fa-wine-glass-empty"><i class="fa-solid fa-wine-glass-empty"></i> Wine Glass Empty</option>
                            <option value="fa-wine-glass"><i class="fa-solid fa-wine-glass"></i> Wine Glass</option>
                            <option value="fa-whiskey-glass"><i class="fa-solid fa-whiskey-glass"></i> Whiskey Glass</option>
                            <option value="fa-wheat-awn"><i class="fa-solid fa-wheat-awn"></i> Wheat Awn</option>
                            <option value="fa-stroopwafel"><i class="fa-solid fa-stroopwafel"></i> Stroopwafel</option>
                            <option value="fa-shrimp"><i class="fa-solid fa-shrimp"></i> Shrimp</option>
                            <option value="fa-plate-wheat"><i class="fa-solid fa-plate-wheat"></i> Plate Wheat</option>
                            <option value="fa-pizza-slice"><i class="fa-solid fa-pizza-slice"></i> Pizza Slice</option>
                            <option value="fa-pepper-hot"><i class="fa-solid fa-pepper-hot"></i> Pepper Hot</option>
                            <option value="fa-martini-glass-empty"><i class="fa-solid fa-martini-glass-empty"></i> Martini Glass Empty</option>
                            <option value="fa-martini-glass-citrus"><i class="fa-solid fa-martini-glass-citrus"></i> Martini Glass Citrus</option>
                            <option value="fa-martini-glass"><i class="fa-solid fa-martini-glass"></i> Martini Glass</option>
                            <option value="fa-jar-wheat"><i class="fa-solid fa-jar-wheat"></i> Jar Wheat</option>
                            <option value="fa-jar"><i class="fa-solid fa-jar"></i> Jar</option>
                            <option value="fa-ice-cream"><i class="fa-solid fa-ice-cream"></i> Ice Cream</option>
                            <option value="fa-hotdog"><i class="fa-solid fa-hotdog"></i> Hot Dog</option>
                            <option value="fa-glass-water-droplet"><i class="fa-solid fa-glass-water-droplet"></i> Glass Water Droplet</option>
                            <option value="fa-fish-fins"><i class="fa-solid fa-fish-fins"></i> Fish Fins</option>
                            <option value="fa-egg"><i class="fa-solid fa-egg"></i> Egg</option>
                            <option value="fa-drumstick-bite"><i class="fa-solid fa-drumstick-bite"></i> Drumstick Bite</option>
                            <option value="fa-cubes-stacked"><i class="fa-solid fa-cubes-stacked"></i> Cubes Stacked</option>
                            <option value="fa-cookie"><i class="fa-solid fa-cookie"></i> Cookie</option>
                            <option value="fa-cloud-meatball"><i class="fa-solid fa-cloud-meatball"></i> Cloud Meatball</option>
                            <option value="fa-cheese"><i class="fa-solid fa-cheese"></i> Cheese</option>
                            <option value="fa-champagne-glasses"><i class="fa-solid fa-champagne-glasses"></i> Champagne Glasses</option>
                            <option value="fa-carrot"><i class="fa-solid fa-carrot"></i> Carrot</option>
                            <option value="fa-candy-cane"><i class="fa-solid fa-candy-cane"></i> Candy Cane</option>
                            <option value="fa-cake-candles"><i class="fa-solid fa-cake-candles"></i> Cake Candles</option>
                            <option value="fa-burger"><i class="fa-solid fa-burger"></i> Burger</option>
                            <option value="fa-bread-slice"><i class="fa-solid fa-bread-slice"></i> Bread Slice</option>
                            <option value="fa-bowl-rice"><i class="fa-solid fa-bowl-rice"></i> Bowl Rice</option>
                            <option value="fa-bowl-food"><i class="fa-solid fa-bowl-food"></i> Bowl Food</option>
                            <option value="fa-bottle-water"><i class="fa-solid fa-bottle-water"></i> Bottle Water</option>
                            <option value="fa-bottle-droplet"><i class="fa-solid fa-bottle-droplet"></i> Bottle Droplet</option>
                            <option value="fa-bone"><i class="fa-solid fa-bone"></i> Bone</option>
                            <option value="fa-blender"><i class="fa-solid fa-blender"></i> Blender</option>
                            <option value="fa-beer-mug-empty"><i class="fa-solid fa-beer-mug-empty"></i> Beer Mug Empty</option>
                            <option value="fa-bacon"><i class="fa-solid fa-bacon"></i> Bacon</option>
                            <option value="fa-apple-whole"><i class="fa-solid fa-apple-whole"></i> Apple Whole</option>
                        </select>
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
                <h5 class="modal-title" id="modaldLabel">Hapus data kategori "<span><strong id="kategori"></strong></span>"</h5>
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
<script src="/assets/extensions/select2/dist/js/select2.min.js"></script>
<script src="/assets/extensions/select2/dist/js/select2.full.min.js"></script>

<script>
    var table;
    var modal = $('#modal');
    var modald = $('#modald');

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
                url: '/kategori/datatable',
                method: 'POST',
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    width: 10
                },
                {
                    data: 'nama_kategori',
                    orderable: false,
                    width: 300
                },
                {
                    data: 'is_active',
                    orderable: false,
                    width: 100
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

        $('#iconSelect2').select2({
            dropdownParent: modal,
            templateResult: formatIcon,
            templateSelection: formatIcon,
            placeholder: "Pilih Icon",
            allowClear: true // Untuk mengizinkan penghapusan opsi
        });

        $('.select2-container').addClass('w-100');

    });

    function formatIcon(icon) {
        if (!icon.id) {
            return icon.text;
        }
        var $icon = $(
            `<span><i class="fas ${icon.id}"></i> ${icon.text}</span>`
        );
        return $icon;
    }

    function changeStatus(id) {
        var isChecked = $('#set_active' + id);
        $.ajax({
            type: "POST",
            url: "/kategori/setStatus",
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
        var form = $('#form input');
        form.removeClass('is-invalid is-valid');

        $('#title').text('Tambah Kategori Produk');
        modal.modal('show');
    }

    function edit(id) {
        $.ajax({
            type: "POST",
            url: "/kategori/getdata",
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
                    var form = $('#form input');
                    form.removeClass('is-invalid is-valid');
                    $('#id').val(response.data.id);
                    $('#nama').val(response.data.nama_kategori);
                    // Set value di select2
                    $('#iconSelect2').val(response.data.icon).trigger('change');
                    $('#title').text('Edit Kategori Produk');
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
        $('#kategori').text(nama);
        $('#btn-delete').attr('onclick', 'remove(' + id + ')');
        modald.modal('show');
    }

    function remove(id) {
        $.ajax({
            url: "/kategori/hapus",
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
            url: "/kategori/simpan",
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
</script>
<?= $this->endSection() ?>