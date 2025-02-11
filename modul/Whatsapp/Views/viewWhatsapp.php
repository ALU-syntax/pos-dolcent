<?= $this->extend('layout/template'); ?>
<?= $this->section('css') ?>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <h3>Whatsapp</h3>
</div>

<div class="page-content">
    <div class="card p-4">
        <form id="form_onesender">
            <div class="row">
                <div class="col-12">
                    <h5 class="mb-3">ONESENDER</h5>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Host</label>
                    <input type="text" class="form-control" name="host" placeholder="Masukkan host onesender anda" value="<?php echo $onesender->host; ?>">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Key</label>
                    <input type="text" class="form-control" name="key" placeholder="Masukkan key onesender anda" value="<?php echo $onesender->key; ?>">
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
    $('#form_onesender').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/whatsapp/simpan",
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
                    toastr.success("Pengaturan One Sender berhasil diperbaharui");
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