<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - Roda Minang</title>

    <link rel="icon" href="/assets/img/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/assets/compiled/css/auth-forgot-password.css" />
    <link rel="stylesheet" href="/assets/compiled/css/app.css" />
    <link rel="stylesheet" href="/assets/compiled/css/app-dark.css" />
    <link href="/assets/extensions/toastr/toastr.min.css" rel="stylesheet" />

    <style>
        .bg-right {
            background-image: url('/assets/img/login-bg.jpg') !important;
            background-repeat: no-repeat !important;
            background-size: cover !important;
        }
    </style>
</head>

<body>
    <script src="/assets/static/js/initTheme.js"></script>
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <h1 class="auth-title mt-4">Registrasi</h1>
                    <p class="auth-subtitle mb-5">
                        Masukkan data Anda untuk mendaftar ke website kami.
                    </p>

                    <div class="card p-4 d-none" id="success">
                        <div class="alert alert-success text-center" role="alert">
                            <i class="bi bi-check-circle me-1"></i> Registrasi Berhasil
                        </div>
                        <hr>
                        <h6 class="mb-3"><i class="bi bi-shop me-2"></i>Informasi Toko: </h6>
                        <p class="mb-2">Nama Toko: <span class="nama"></span></p>
                        <p class="mb-2">E-mail: <span class="email"></span></p>
                        <p class="mb-2">No Telpon: <span class="nohp"></span></p>
                        <hr>
                        <h6 class="mb-3"><i class="bi bi-person-badge me-2"></i>Informasi User: </h6>
                        <p class="mb-2">Nama: <span class="namau"></span></p>
                        <p class="mb-2">E-mail: <span class="emailu"></span></p>
                        <p class="mb-2">Password: <span class="password"></span></p>
                    </div>

                    <form action="javascript:void(0)" id="form" method="POST">
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" class="form-control form-control-xl" placeholder="Nama Toko" name="nama" required />
                            <div class="invalid-feedback"></div>
                            <div class="form-control-icon">
                                <i class="bi bi-shop"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="email" class="form-control form-control-xl" placeholder="Email" name="email" required />
                            <div class="invalid-feedback"></div>
                            <div class="form-control-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="number" class="form-control form-control-xl" placeholder="Nomor HP" name="nohp" required />
                            <div class="invalid-feedback"></div>
                            <div class="form-control-icon">
                                <i class="bi bi-phone"></i>
                            </div>
                        </div>
                        <div class="alert alert-info mb-4" role="alert">
                            User Toko
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" class="form-control form-control-xl" placeholder="Nama User" name="namau" required />
                            <div class="invalid-feedback"></div>
                            <div class="form-control-icon">
                                <i class="bi bi-person-badge"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="email" class="form-control form-control-xl" placeholder="Email User" name="emailu" required />
                            <div class="invalid-feedback"></div>
                            <div class="form-control-icon">
                                <i class="bi bi-envelope-at"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" class="form-control form-control-xl" placeholder="Password" name="password" required />
                            <div class="invalid-feedback"></div>
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg mt-4">
                            Daftar
                        </button>
                    </form>
                    <div class="text-center mt-5 text-lg fs-4">
                        <p class="text-gray-600">
                            Sudah memiliki akun?
                            <a href="/" class="font-bold">Masuk</a>.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div class="bg-right" id="auth-right"></div>
            </div>
        </div>
    </div>

    <script src="/assets/extensions/jquery/jquery.min.js"></script>
    <script src="/assets/extensions/blockui/jquery.blockui.min.js"></script>
    <script src="/assets/extensions/toastr/toastr.min.js"></script>

    <script>
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

        function hideblockUI() {
            $.unblockUI();
        }

        $('#form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "/register/simpan",
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
                        toastr.success('Registrasi berhasil');

                        $('#form').addClass('d-none');
                        $('#success').removeClass('d-none');
                        $('.nama').text(response.toko.nama_toko);
                        $('.email').text(response.toko.email);
                        $('.nohp').text(response.toko.nohp);

                        $('.namau').text(response.user.nama);
                        $('.emailu').text(response.user.email);
                        $('.password').text(response.password);
                    } else {
                        $.each(response.errors, function(key, value) {
                            $('[name="' + key + '"]').addClass('is-invalid');
                            if (key == 'partai') {
                                $('[name="' + key + '"]').next().next().text(value);
                            } else {
                                $('[name="' + key + '"]').next().text(value);
                            }
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
</body>

</html>