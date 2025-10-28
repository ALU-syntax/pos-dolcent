<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DOLCENT - Login</title>

    <link rel="icon" href="/assets/img/logo-dolcent.jpg" type="image/x-icon" />
    <script async type="module" crossorigin src="/assets/compiled/js/auth-forgot-password.html"></script>
    <link rel="stylesheet" href="/assets/compiled/css/auth-forgot-password.css" />
    <link rel="stylesheet" href="/assets/compiled/css/app.css" />
    <link rel="stylesheet" href="/assets/compiled/css/app-dark.css" />
    <link href="/assets/extensions/toastr/toastr.min.css" rel="stylesheet" />

    <style>
        .bg-right {
            background-image: url('/assets/img/logo-dolcent.jpg') !important;
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
                    <div class="auth-logo">
                    </div>
                    <h1 class="auth-title">Log in.</h1>
                    <p class="auth-subtitle mb-5">Masuk dengan data user anda untuk masuk ke halaman admin dan kasir.</p>

                    <form action="javascript:void(0)" id="form" method="POST">
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="email" class="form-control form-control-xl" placeholder="E-mail" name="email" required />
                            <div class="form-control-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-0">
                            <input type="password" class="form-control form-control-xl" placeholder="Password" name="password" id="password" required />
                            <div class="form-control-icon">
                                <i class="bi bi-eye-slash" style="cursor: pointer;" id="pwtoggle"></i>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg mt-5">Masuk</button>
                    </form>
                    <!-- <div class="text-center mt-5 text-lg fs-4">
                        <p class="text-gray-600">Belum mempunyai akun?<a href="/register" class="font-bold"> Daftar Sekarang</a>.</p>
                    </div> -->
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

        $("#pwtoggle").click(function() {
            if ($("#password").attr('type') == 'password') {
                $("#password").attr('type', 'text');
                $(this).removeAttr('class').attr('class', 'bi bi-eye');
            } else {
                $("#password").attr('type', 'password');
                $(this).removeAttr('class').attr('class', 'bi bi-eye-slash');
            }
        });

        $('#form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "/login/doLogin",
                data: $(this).serialize(),
                dataType: "JSON",
                beforeSend: function() {
                    showblockUI();
                },
                complete: function() {
                    hideblockUI();
                },
                success: function(response) {
                    if (response.status == true) {
                        window.location.href = response.link;
                        toastr.success('Berhasil masuk');
                    } else if (response.status_form == false) {
                        $.each(response.errors, function(key, value) {
                            $('[name="' + key + '"]').addClass('is-invalid');
                            $('[name="' + key + '"]').next().next().text(value);
                            if (value == "") {
                                $('[name="' + key + '"]').removeClass('is-invalid');
                                $('[name="' + key + '"]').addClass('is-valid');
                            }
                        });
                    } else if (response.status == false) {
                        toastr.error(response.notif);
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