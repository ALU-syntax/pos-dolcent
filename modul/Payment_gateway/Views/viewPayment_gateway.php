<?= $this->extend('layout/template'); ?>
<?= $this->section('css') ?>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <h3>Payment Gateway</h3>
</div>

<div class="page-content">
    <div class="card p-4">
        <form id="form_midtrans">
            <div class="row">
                <div class="col-12">
                    <img src="/assets/img/pg/midtrans.png" alt="" height="75">
                </div>
                <div class="col-md-6">
                    <label for="client_key" class="form-label">Client Key</label>
                    <input type="text" class="form-control" id="client_key" name="client_key" placeholder="Masukkan client_key midtrans anda" value="<?php echo $midtrans->client_key; ?>">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6">
                    <label for="server_key" class="form-label">Server Key</label>
                    <input type="text" class="form-control" id="server_key" name="server_key" placeholder="Masukkan server_key midtrans anda" value="<?php echo $midtrans->server_key; ?>">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </div>
        </form>

        <form id="form_smartpayment">
            <div class="row">
                <div class="col-12">
                    <h5 class="mb-3">SMARTPAYMENT</h5>
                </div>
                <div class="col-6">
                    <label class="form-label">HOST</label>
                    <input type="text" class="form-control" name="host" placeholder="Masukkan host smartpayment anda" value="<?php echo $smartpayment->host; ?>">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">TOKEN</label>
                    <input type="text" class="form-control" name="token" placeholder="Masukkan token smartpayment anda" value="<?php echo $smartpayment->token; ?>">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </div>
        </form>

        <form id="form_npay">
            <div class="row">
                <div class="col-12">
                    <h5 class="mb-3">NPAY</h5>
                </div>
                <div class="col-6">
                    <label class="form-label">HOST</label>
                    <input type="text" class="form-control" name="host" placeholder="Masukkan host npay anda" value="<?php echo $npay->host; ?>">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-6">
                <label class="form-label">API KEY</label>
                    <input type="text" class="form-control" name="api_key" placeholder="Masukkan API Key npay anda" value="<?php echo $npay->api_key; ?>">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('js') ?>

<script>
    $('#form_midtrans').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/payment-gateway/simpanMidtrans",
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
                    toastr.success("Payment gateway midtrans berhasil diperbaharui");
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
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
    $('#form_smartpayment').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/payment-gateway/simpanSmartpayment",
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
                    toastr.success("Payment gateway SmartPayment berhasil diperbaharui");
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
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
    $('#form_npay').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/payment-gateway/simpanNpay",
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
                    toastr.success("Payment gateway NPAY berhasil diperbaharui");
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
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