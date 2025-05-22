<?= $this->extend('layout/template'); ?>
<?= $this->section('css') ?>
<link href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="/assets/extensions/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" />

<link href="/assets/extensions/fancybox/fancybox.css" rel="stylesheet" />
<link href="/assets/extensions/select2/dist/css/select2.min.css" rel="stylesheet" />
<link href="/assets/extensions/select2-bootstrap-5-theme-1.3.0/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<link href="/assets/extensions/select2-bootstrap-5-theme-1.3.0/select2-bootstrap-5-theme.rtl.min.css" rel="stylesheet" />

<style>
    .img-preview {
        max-width: 100%;
        max-height: 100px;
    }
    .vertical-line {
        width: 50px;
        height: 2px;
        background-color: black;
        transform: rotate(90deg);
        margin: 0 auto; /* Centang garis di tengah */
    }
    
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div class="row">
        <div class="col-12">
            <h3>Daftar Biaya Produksi</h3>
        </div>
    </div>
    <div class="row mt-2 mb-2">
        <div class="col-4">
            <div class="btn " style="background-color: #435ebe; cursor:default; color:white;" ><b>Balance:</b> &nbsp; <span id="balance"></span></div>
        </div>
        <div class="col-8 d-flex align-content-end justify-content-end">
            <form action="<?= base_url('biaya-produksi/export-excel') ?>" method="POST" >
                <input type="text" name="startDateBiayaProduksi" id="startDateBiayaProduksi" hidden>
                <input type="text" name="endDateBiayaProduksi" id="endDateBiayaProduksi" hidden>
                <button class="btn btn-primary me-2" type="submit">Export Excel</button>
            </form>
            <hr class="vertical-line">
            <button class="btn btn-primary" onclick="tambah()"><i class="fa fa-plus"></i>&nbsp; Tambah Biaya Produksi</button>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <div class="row">
                <div class="col-md-6">
                    <label for="dari" class="form-label">Dari</label>
                    <input type="date" class="form-control " id="dari" name="dari">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6">
                    <label for="sampai" class="form-label">Sampai</label>
                    <input type="date" class="form-control " id="sampai" name="sampai">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="row mx-1">
                <button id="filterDate" class="btn btn-primary mt-4">Filter</button>
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
                                        <th>Bahan Baku</th>
                                        <th>Nominal</th>
                                        <th>Quantity</th>
                                        <th>Deskripsi</th>
                                        <th>Foto</th>
                                        <th>Tanggal</th>
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
            <div class="modal-body">
                <form id="form" autocomplete="off">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="id_stok_bahan_baku" id="id_stok_bahan_baku">

                    <label for="bahanSelect2" class="form-label">Bahan Baku</label>
                    <div class="mb-3 form-group d-flex">
                        <select class="form-select w-100" name="id_bahan" id="bahanSelect2" required>
                            <?php foreach ($bahan_baku as $key) : ?>
                                <option value="<?php echo $key->id; ?>"><?php echo $key->nama_bahan; ?></option>
                            <?php endforeach ?>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="nominal" class="form-label">Nominal</label>
                        <input type="text" class="form-control harga" id="nominal" name="nominal" placeholder="Masukkan nominal biaya produksi" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="biaya_pengiriman" class="form-label">Biaya Pengiriman</label>
                        <input type="text" class="form-control harga" id="biaya_pengiriman" name="biaya_pengiriman" placeholder="Masukkan nominal biaya pengiriman">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" required class="form-control" id="quantity" name="quantity" placeholder="Masukkan quantity pembelian" min="1">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="biaya_lain" class="form-label">Biaya Lainnya</label>
                        <input type="text" required class="form-control harga" id="biaya_lain" name="biaya_lain" placeholder="Masukkan biaya lainnya jika ada">
                        <div class="invalid-feedback"></div>
                    </div>

                    <hr>
                    <!-- <p class="text-center">Informasi Opsional</p> -->
                    <div class="mb-3">
                        <label for="foto" class="form-label">Upload Foto</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                        <div class="invalid-feedback"></div>
                        <p class="text-muted mt-1" style="font-size: x-small;">Silakan upload foto yang berkaitan dengan transaksi ini.</p>
                        <div id="preview"></div>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="deskripsi" placeholder="Masukkan deskripsi biaya produksi"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal">
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
                <h5 class="modal-title" id="modaldLabel">Hapus data pengeluaran</h5>
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
<script src="/assets/extensions/select2/dist/js/select2.min.js"></script>
<script src="/assets/extensions/select2/dist/js/select2.full.min.js"></script>

<script>
    var table;
    var modal = $('#modal');
    var modald = $('#modald');

    var balance = <?php echo $balance ?>;
    var nominalBiayaProduksi = <?php echo $nominal_biaya_produksi ?>;
    var balanceResult = balance - nominalBiayaProduksi;

    var globalBalance = balanceResult;

    $('#balance').html(formatRupiahFromNumber(balanceResult));
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
                url: '/biaya-produksi/datatable',
                method: 'POST',
                data: function(d) {
                    d.dari = $('#dari').val();
                    d.sampai = $('#sampai').val();
                },
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    width: 10
                },
                {
                    data: 'nama_bahan',
                    orderable: false,
                    width: 150
                },
                {
                    data: 'nominal',
                    orderable: false,
                    width: 200
                },
                {
                    data: 'quantity',
                    orderable: false,
                    width: 200
                },
                {
                    data: 'deskripsi',
                    orderable: false,
                    width: 200
                },
                {
                    data: 'foto',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'tanggal',
                    orderable: false,
                    width: 200
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

        $('#filterDate').click(function() {
            table.draw();
            changeBalance();
        });

        $('#bahanSelect2').select2({
            dropdownParent: modal,
            placeholder: "Pilih Bahan",
            allowClear: true // Untuk mengizinkan penghapusan opsi
        });

        $('.select2-container').addClass('w-100');
    });

    $(document).ready(function() {
        $(".harga").keyup(function(e) {
            $(this).val(formatRupiah($(this).val(), "Rp. "));
        });
    });

    function formatRupiahFromNumber(angka) {
        var rupiah = '';
        var angkarev = angka.toString().split('').reverse().join('');
        for (var i = 0; i < angkarev.length; i++) {
            if (i % 3 == 0) {
                rupiah += angkarev.substr(i, 3) + '.';
            }
        }
        return 'Rp. ' + rupiah.split('', rupiah.length - 1).reverse().join('');
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

    function tambah() {
        $('#id').val('');
        $('#preview').empty();

        var today = new Date().toISOString().slice(0, 10);
        $('#tanggal').attr("value", today);

        $('#form')[0].reset();
        var form = $('#form input, #form select');
        form.removeClass('is-invalid is-valid');

        $('#title').text('Tambah data biaya produksi');
        modal.modal('show');
    }

    function edit(id) {
        $.ajax({
            type: "POST",
            url: "/biaya-produksi/getdata",
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
                console.log(response)
                if (response.status) {
                    var form = $('#form input, #form select');
                    form.removeClass('is-invalid is-valid');

                    $('#foto').val(null);
                    $('#preview').html('<img src="/assets/img/biaya-produksi/' + response.data.foto + '" alt="Preview Gambar" class="img-preview rounded">');

                    $('#id').val(response.data.id);
                    $('#id_stok_bahan_baku').val(response.data.id_stok_bahan_baku);
                    $('#deskripsi').val(response.data.deskripsi);
                    $('#nominal').val(formatRupiah(response.data.nominal.toString(), "Rp. "));
                     $('#bahanSelect2').val(response.data.id_bahan).trigger('change');
                    $('#biaya_pengiriman').val(formatRupiah(response.data.biaya_pengiriman.toString(), "Rp. "));
                    $('#quantity').val(response.data.quantity);
                    $('#biaya_lain').val(formatRupiah(response.data.biaya_lain.toString(), "Rp. "));

                    let serverDate = response.data.tanggal;
                    let date = serverDate.split(' ')[0];
                    console.log(serverDate);
                    console.log(date);
                    $('#tanggal').val(date);
                    $('#title').text('Edit data biaya produksi');
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

    function hapus(id, foto) {
        $('#btn-delete').attr('onclick', 'remove(' + id + ', \'' + foto + '\')');
        modald.modal('show');
    }

    function remove(id, foto) {
        $.ajax({
            url: "/biaya-produksi/hapus",
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
                const tanggal = new Date(data.data.tanggal);
                const inputStartDate = document.getElementById('dari').value;
                const inputEndDate = document.getElementById('sampai').value;

                const startDate = new Date(inputStartDate);
                const endDate = new Date(inputEndDate);

                let tambahUpdateNominal = data.data.nominal
                if(inputStartDate && inputEndDate){
                    if (tanggal >= startDate && tanggal <= endDate) {
                        globalBalance += parseInt(tambahUpdateNominal);
                        $('#balance').html(formatRupiahFromNumber(globalBalance));
                    }
                }else if(inputStartDate){
                    if (tanggal >= startDate) {
                        globalBalance += parseInt(tambahUpdateNominal);
                        $('#balance').html(formatRupiahFromNumber(globalBalance));
                    }
                }else if(inputEndDate){
                    if (tanggal <= endDate) {
                        globalBalance += parseInt(tambahUpdateNominal);
                        $('#balance').html(formatRupiahFromNumber(globalBalance));
                    }
                }else if(!inputStartDate && !inputEndDate){
                    globalBalance += parseInt(tambahUpdateNominal);
                    $('#balance').html(formatRupiahFromNumber(globalBalance));
                }
                
                toastr.success('Data Berhasil dihapus');
                modald.modal('hide');
                table.ajax.reload();
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

    $('#form').submit(function(e) {
        e.preventDefault();
        var form = $('#form')[0];
        var formData = new FormData(form);
        
        $.ajax({
            type: "POST",
            url: "/biaya-produksi/simpan",
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
                console.log(response)
                if (response.status) {
                    console.log(response)
                    console.log(globalBalance)

                    const tanggal = new Date(document.getElementById('tanggal').value);
                    const inputStartDate = document.getElementById('dari').value;
                    const inputEndDate = document.getElementById('sampai').value;

                    const startDate = new Date(inputStartDate);
                    const endDate = new Date(inputEndDate);

                    let tambahUpdateNominal = $('#nominal').val();

                    //update data
                    if(response.dataLama){
                        if(tambahUpdateNominal != response.dataLama.nominal){
                            if(inputStartDate && inputEndDate){
                                if (tanggal >= startDate && tanggal <= endDate) {
                                    globalBalance += response.dataLama.nominal;
                                    globalBalance -= tambahUpdateNominal.split(' ')[1];
                                    $('#balance').val(formatRupiahFromNumber(globalBalance));
                                }
                            }else if(inputStartDate){
                                if (tanggal >= startDate) {
                                    globalBalance += response.dataLama.nominal;
                                    globalBalance -= tambahUpdateNominal.split(' ')[1];
                                    $('#balance').val(formatRupiahFromNumber(globalBalance));
                                }
                            }else if(inputEndDate){
                                if (tanggal <= endDate) {
                                    globalBalance += response.dataLama.nominal;
                                    globalBalance -= tambahUpdateNominal.split(' ')[1];
                                    $('#balance').val(formatRupiahFromNumber(globalBalance));
                                }
                            }
                                
                        }    
                    }else{
                        if(inputStartDate && inputEndDate){
                            if (tanggal >= startDate && tanggal <= endDate) {
                                globalBalance -= getAmount(tambahUpdateNominal);
                                console.log("masok 1 " + globalBalance)
                                $('#balance').html(formatRupiahFromNumber(globalBalance));
                            }
                        }else if(inputStartDate){
                            if (tanggal >= startDate) {
                                globalBalance -= getAmount(tambahUpdateNominal);
                                console.log(tanggal)
                                console.log()
                                console.log("masok 2 " + globalBalance)
                                $('#balance').html(formatRupiahFromNumber(globalBalance));
                            }
                        }else if(inputEndDate){
                            if (tanggal <= endDate) {
                                globalBalance -= getAmount(tambahUpdateNominal);
                                console.log("masok 3 " + globalBalance)
                                $('#balance').html(formatRupiahFromNumber(globalBalance));
                            }1
                        }else if(!inputStartDate && !inputEndDate){
                            globalBalance -= getAmount(tambahUpdateNominal);
                            console.log("masok 4 " + globalBalance)
                            $('#balance').html(formatRupiahFromNumber(globalBalance));
                        }

                    }
        
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

    function changeBalance(){
        let tglDari = $('#dari').val();
        let tglSampai = $('#sampai').val();

        console.log(tglDari);
        console.log(tglSampai);

        $.ajax({
            type: "POST",
            url: "/biaya-produksi/fetch-balance",
            data: {
                dari: tglDari,
                sampai: tglSampai,
            },
            dataType: "JSON",
            beforeSend: function() {
                showblockUI();
            },
            complete: function() {
                hideblockUI();
            },
            success: function(response) {
                if(response.status){
                    let balance = response.balance;
                    let nominalBiayaProduksi = response.nominalBiayaProduksi;

                    let result = balance -nominalBiayaProduksi;
                    globalBalance = result;
                    $('#balance').html(formatRupiahFromNumber(result));    
                }else{
                    let result = formatRupiahFromNumber(0);

                    $('#balance').html(result);    
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

    function getAmount(money) {
        // Menghapus semua karakter kecuali angka, titik, dan koma
        let cleanString = money.replace(/([^0-9\.,])/g, '');
        // Menghapus semua karakter kecuali angka
        let onlyNumbersString = money.replace(/([^0-9])/g, '');

        let separatorsCountToBeErased = cleanString.length - onlyNumbersString.length - 1;

        // Menghapus titik dan koma berdasarkan jumlah separator yang harus dihapus
        let stringWithCommaOrDot = cleanString;
        for (let i = 0; i < separatorsCountToBeErased; i++) {
            stringWithCommaOrDot = stringWithCommaOrDot.replace(/([,\.])/, '');
        }

        // Menghapus pemisah ribuan, titik atau koma, yang diikuti oleh tiga atau lebih angka
        let removedThousandSeparator = stringWithCommaOrDot.replace(/(\.|,)(?=[0-9]{3,}$)/, '');

        // Mengganti koma dengan titik dan mengembalikan nilai sebagai float
        return parseFloat(removedThousandSeparator.replace(',', '.'));
    }

</script>
<?= $this->endSection() ?>