<?= $this->extend('layout/template'); ?>
<?= $this->section('css') ?>
<link href="/assets/extensions/select2/dist/css/select2.min.css" rel="stylesheet" />
<link href="/assets/extensions/select2-bootstrap-5-theme-1.3.0/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<link href="/assets/extensions/select2-bootstrap-5-theme-1.3.0/select2-bootstrap-5-theme.rtl.min.css" rel="stylesheet" />
<style>
    .invsel {
        color: red;
        margin-top: 2px;
        font-size: small;
    }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div class="row">
        <div class="col-6">
            <h3>Tambah Kredit Barang</h3>
        </div>
        <div class="col-6 text-end">
            <a class="btn btn-secondary" href="/kredit"><i class="fa fa-arrow-left"></i>&nbsp; Kembali</a>
        </div>
    </div>
</div>

<form id="form">
    <div class="card p-4">
        <div class="row">
            <div class="col-12 mb-4">
                <h6 class="fw-bold"><i class="fas fa-info-circle me-2"></i>Informasi Kredit</h6>
            </div>
            <div class="col-md-4 mb-4">
                <label for="pelanggan" class="form-label">Pelanggan</label>
                <select class="form-control" id="pelanggan" name="pelanggan">
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-4 mb-4">
                <label for="periode" class="form-label">Periode</label>
                <select class="form-control" id="periode" name="periode">
                    <option disabled selected>Pilih periode (Bulan)</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-4 mb-4">
                <label for="tgl" class="form-label">Tanggal Kredit</label>
                <input type="date" class="form-control" id="tgl" name="tgl">
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>

    <div class="card p-4">
        <div class="row">
            <div class="col-12 mb-4">
                <h6 class="fw-bold"><i class="fas fa-box me-2"></i>Barang</h6>
            </div>
        </div>
        <div id="detail">
            <div class="row detail" id="detail1">
                <div class="col-md-4 mb-4">
                    <label for="barang" class="form-label">Barang</label>
                    <select class="form-control barang detil" id="barang" name="barang[]">
                    </select>
                    <div class="invsel"></div>
                </div>
                <div class="col-md-4 mb-4">
                    <label for="harga" class="form-label">Harga</label>
                    <input type="text" class="form-control detil" id="harga" readonly placeholder="Pilih barang terleih dahulu">
                    <div class="invalid-feedback"></div>
                    <input type="hidden" class="harga" id="hargaa" name="harga[]" readonly>
                </div>
                <div class="col-md-4 mb-4">
                    <label class="form-label">Qty</label>
                    <input type="number" class="form-control qty detil" name="qty[]" value="1">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-12 mb-4">
                    <hr>
                </div>
            </div>
        </div>
        <div class="col-12 mb-4 text-center">
            <button class="btn btn-secondary me-2 d-none" type="button" title="Tambah barang" id="btn-min"><i class="fas fa-minus"></i></button>
            <button class="btn btn-primary" type="button" title="Tambah barang" id="btn-add"><i class="fas fa-plus"></i></button>
        </div>
    </div>

    <div class="card p-4">
        <div class="row">
            <div class="col-12 mb-4">
                <h6 class="fw-bold"><i class="fas fa-money-bill me-2"></i>Nominal Kredit</h6>
            </div>
            <div class="col-md-4 mb-4">
                <p class="form-label">Subtotal Produk</p>
                <input type="text" class="form-control" id="subtotal" name="subtotal" placeholder="Auto Count" readonly>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-4 mb-4">
                <label for="total" class="form-label">Total Kredit</label>
                <input type="text" class="form-control rupiah" id="total" name="total" placeholder="Masukkan total nominal kredit (termasuk biaya / bunga)">
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-4 mb-4">
                <label for="dp" class="form-label">Uang Muka</label>
                <input type="text" class="form-control rupiah" id="dp" name="dp" placeholder="Masukkan uang muka (DP)">
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>
    <div class="text-end">
        <button type="submit" class="btn btn-primary" id="btnsave"><i class="fas fa-save me-2"></i>Simpan</button>
    </div>
</form>

<?= $this->endSection() ?>
<?= $this->section('js') ?>
<script src="/assets/extensions/select2/dist/js/select2.min.js"></script>
<script src="/assets/extensions/select2/dist/js/select2.full.min.js"></script>

<script>
    $('#btn-add').click(function() {
        var totalb = <?php echo $totalb; ?>;
        var no = $('.detail').length + 1;
        var html = `<div class="row detail" id="detail` + no + `">
            <div class="col-md-4 mb-4">
                <label for="barang" class="form-label">Barang</label>
                <select class="form-control barang detil" id="barang` + no + `" name="barang[]">
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-4 mb-4">
                <label for="harga" class="form-label">Harga</label>
                <input type="text" class="form-control detil" id="harga` + no + `" readonly placeholder="Pilih barang terleih dahulu">
                <div class="invalid-feedback"></div>
                <input type="hidden" class="harga" id="hargaa` + no + `" name="harga[]" readonly>
            </div>
            <div class="col-md-4 mb-4">
                <label class="form-label">Qty</label>
                <input type="number" class="form-control qty detil" name="qty[]" value="1">
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-12 mb-4">
                <hr>
            </div>
        </div>`;

        if (no <= totalb) {
            $('#detail').append(html);
            $('#barang' + no).select2({
                placeholder: 'Pilih Barang',
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
                    url: "/kredit/getBarang",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            b: getAllBarang()
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
            $('#barang' + no).change(function() {
                $.ajax({
                    url: '/kredit/getHarga',
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id: $(this).val(),
                        no: no
                    },
                    success: function(response) {
                        $('#harga' + response.no).val(response.hargarp);
                        $('#hargaa' + response.no).val(response.harga);
                        var total = 0;
                        $('.harga').each(function(index) {
                            var harga = parseFloat($(this).val()) || 0;
                            var qty = parseFloat($('.qty').eq(index).val()) || 1;
                            var nilai = harga * qty;
                            total += nilai;
                        });
                        $('#subtotal').val(formatRupiah(total));
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
            $('.qty').on('input', function() {
                var total = 0;
                $('.harga').each(function(index) {
                    var harga = parseFloat($(this).val()) || 0;
                    var qty = parseFloat($('.qty').eq(index).val()) || 1;
                    var nilai = harga * qty;
                    total += nilai;
                });
                $('#subtotal').val(formatRupiah(total));
            });
            $('#btn-min').removeClass('d-none');
        } else {
            toastr.warning("Melebihi total barang!");
        }
    });

    $('#btn-min').click(function() {
        if ($('.detail').length > 1) {
            $('.detail').last().remove();
            if ($('.detail').length <= 1) {
                $(this).addClass('d-none');
            }
        }
    });

    $(".rupiah").keyup(function(e) {
        $(this).val(formatRupiah2($(this).val(), "Rp. "));
    });

    $('.qty').keypress(function(e) {
        var charCode = (e.which) ? e.which : event.keyCode
        if (String.fromCharCode(charCode).match(/[^0-9]/g))
            return false;
    });

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
            url: "/kredit/getPelanggan",
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

    $('#barang').select2({
        placeholder: 'Pilih Barang',
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
            url: "/kredit/getBarang",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    b: getAllBarang()
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

    $('#barang').change(function() {
        $.ajax({
            url: '/kredit/getHarga',
            type: "POST",
            dataType: "JSON",
            data: {
                id: $(this).val()
            },
            success: function(response) {
                $('#harga').val(response.hargarp);
                $('#hargaa').val(response.harga);
                var total = 0;
                $('.harga').each(function(index) {
                    var harga = parseFloat($(this).val()) || 0;
                    var qty = parseFloat($('.qty').eq(index).val()) || 1;
                    var nilai = harga * qty;
                    total += nilai;
                });
                $('#subtotal').val(formatRupiah(total));
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

    $('.qty').on('input', function() {
        var total = 0;
        $('.harga').each(function(index) {
            var harga = parseFloat($(this).val()) || 0;
            var qty = parseFloat($('.qty').eq(index).val()) || 1;
            var nilai = harga * qty;
            total += nilai;
        });
        $('#subtotal').val(formatRupiah(total));
    });

    $('#form').submit(function(e) {
        e.preventDefault();
        $(".detil").each(function() {
            var cek_val = $(this).val();
            var name = $(this).attr('name');
            if (cek_val == '' || cek_val == null) {
                $(this).addClass('is-invalid invdetil');
                if (name == 'barang[]') {
                    $(this).next().next('.invsel').first().text('Harus diisi');
                } else {
                    $(this).next().text('Harus diisi');
                }
            } else {
                $(this).removeClass('is-invalid invdetil');
                $(this).addClass('is-valid');
                $(this).next().next('.invsel').first().text('');
            }
        });
        validasi = $("#detail .is-invalid").length;

        var form = $('#form')[0];
        var formData = new FormData(form);
        formData.append('validasi', validasi);

        $.ajax({
            type: "POST",
            url: "/kredit/simpan",
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
                    toastr.success('Data berhasil di simpan');
                    $('#btnsave').addClass('disabled');
                    setTimeout(function() {
                        window.location.href = '/kredit';
                    }, 1500);
                } else {
                    $.each(response.errors, function(key, value) {
                        $('[name="' + key + '"]').addClass('is-invalid');
                        if (key == 'pelanggan') {
                            $('[name="' + key + '"]').next().next().text(value);
                        } else {
                            $('[name="' + key + '"]').next().text(value);
                        }
                        if (value == "") {
                            $('[name="' + key + '"]').removeClass('is-invalid');
                            $('[name="' + key + '"]').addClass('is-valid');
                            if (key == 'file') {
                                $('#invalid-foto').empty();
                            }
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

    function getAllBarang() {
        var valuesArray = [];
        $('.barang').each(function() {
            valuesArray.push($(this).val());
        });
        return valuesArray;
    }

    function formatRupiah(angka) {
        var number_string = angka.toString();
        var split = number_string.split(',');
        var sisa = split[0].length % 3;
        var rupiah = split[0].substr(0, sisa);
        var ribuan = split[0].substr(sisa).match(/\d{1,3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;

        return 'Rp ' + rupiah;
    }

    function formatRupiah2(angka, prefix) {
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
</script>
<?= $this->endSection() ?>