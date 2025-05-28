<?= $this->extend('layout/template'); ?>
<?= $this->section('css') ?>
<link href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="/assets/extensions/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" />
<style>
    .check-disabled {
        opacity: 0.5;
        pointer-events: none;
    }

    #table tbody tr:hover {
        cursor: pointer;
    }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div class="row">
        <div class="col-6">
            <h3>Daftar Shift</h3>
        </div>
        <!-- <div class="col-6 text-end">
            <button class="btn btn-primary" onclick="tambah()"><i class="fa fa-plus"></i>&nbsp; Tambah Satuan</button>
        </div> -->
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
                            <table class="table table-bordered table-hover dataTable no-footer" id="table"
                                aria-describedby="table1_info">
                                <thead>
                                    <tr>
                                        <th>Open</th>
                                        <th>Close</th>
                                        <th>Cash Awal</th>
                                        <th>Cash Akhir</th>
                                        <!-- <th>Action</th> -->
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

<!-- Modal Detail -->
<div class="modal modal-xl fade" id="modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="btnCancelClosePattyCash" class="btn btn-secondary btn-lg"
                    data-bs-dismiss="modal">Close</button>
                <h5 class="modal-title mx-auto text-center" style="padding-right: 75px;">
                    <strong>Shift History</strong><br>
                </h5>
            </div>
            <div class="modal-body" style="padding-top: 0;">
                <!-- Payment Options -->
                <div id="end-current-shift-section">
                    <div class="row">
                        <div class="col-12">
                            <div class="card" style="overflow-y: auto; height: calc(100vh - 380px);">
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
                                                        <div class="col-6">Open Shift</div>
                                                        <div class="col-6" id="txt-name-open-shift">
                                                            Ardian</div>
                                                    </div>
                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-6">Outlet</div>
                                                        <div class="col-6" id="txt-outlet-shift">
                                                            Outlet 1</div>
                                                    </div>
                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-6">Starting Shift
                                                        </div>
                                                        <div class="col-6" id="txt-start-shift">
                                                            Thursday,
                                                            blabla</div>
                                                    </div>
                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-6">Close Shift
                                                        </div>
                                                        <div class="col-6" id="txt-close-shift">
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
                                                        <div class="col-6" id="txt-starting-cash-shift">
                                                            Rp. 50.000
                                                        </div>
                                                    </div>
                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-6">Cash Sales
                                                        </div>
                                                        <div class="col-6" id="txt-sales-shift">
                                                            Rp. 70.000
                                                        </div>
                                                    </div>
                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-6">Expected Ending
                                                            Cash</div>
                                                        <div class="col-6" id="txt-expected-ending-shift">
                                                            Rp. 121.000
                                                        </div>
                                                    </div>
                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-6">Real Ending
                                                            Cash</div>
                                                        <div class="col-6" id="txt-real-ending-shift">
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


<?= $this->endSection() ?>
<?= $this->section('js') ?>
<script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/extensions/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="/assets/extensions/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>

<script>
    var table;
    var modal = $('#modal');
    var modald = $('#modald');

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
                url: '/shift/datatable',
                method: 'POST',
            },
            columns: [{
                    data: 'open',
                    orderable: false,
                    width: 60
                },
                {
                    data: 'close',
                    orderable: false,
                    width: 60
                },
                {
                    data: 'amount_awal',
                    orderable: false,
                    className: 'text-md-center',
                    width: 60
                },
                {
                    data: 'amount_akhir',
                    orderable: false,
                    className: 'text-md-center',
                    width: 100
                },
            ],
            language: {
                url: '/assets/extensions/bahasa/id.json',
            },
            scrollX: true, // opsional, jika tabel terlalu lebar
        });

        $('#table tbody').on('click', 'tr', function () {
            // let id = $(this).data('id');
            var data = table.row(this).data();
            console.log(data);
            if (data) {
                $.ajax({
                    type: "POST",
                    url: "/shift/getShift",
                    data: {
                        id: data.id
                    },
                    dataType: "JSON",
                    beforeSend: function () {
                        showblockUI();
                    },
                    complete: function () {
                        hideblockUI();
                    },
                    success: function (response) {
                        console.log(response);
                        if (response.status) {
                            let totalSalesCash = 0;
                            let expectedEnding = parseInt(response.data.amount_awal);
                            response.penjualan.forEach(function (item) {
                                totalSalesCash += parseInt(item.total);
                                expectedEnding += parseInt(item.total);
                            });

                            $('#txt-name-open-shift').text(response.userOpen.nama);
                            $('#txt-outlet-shift').text(response.toko.nama_toko);
                            $('#txt-start-shift').text(response.data.open);
                            $('#txt-close-shift').text(response.data.close);
                            $('#txt-starting-cash-shift').text(formatRupiah(response.data
                                .amount_awal.toString(), "Rp. "));
                            $('#txt-sales-shift').text(formatRupiah(totalSalesCash
                            .toString(), "Rp. "));
                            $('#txt-expected-ending-shift').text(formatRupiah(expectedEnding
                                .toString(), "Rp. "));
                            
                            let amountAkhir = response.data.amount_akhir ? formatRupiah(response.data
                                .amount_akhir.toString(), "Rp. ") : "-";
                            $('#txt-real-ending-shift').text(amountAkhir);


                            modal.modal('show');
                        } else {
                            toastr.error(response.message);
                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus);
                    },
                });

            }
        });
    });

    function changeStatus(id) {
        var isChecked = $('#set_active' + id);
        $.ajax({
            type: "POST",
            url: "/satuan/setStatus",
            data: {
                id: id
            },
            dataType: "JSON",
            beforeSend: function () {
                showblockUI();
            },
            complete: function () {
                hideblockUI();
            },
            success: function (response) {
                if (response.status) {
                    isChecked.next().text($(isChecked).is(':checked') ? 'Aktif' : 'Nonaktif');
                    toastr.success('Data Berhasil Diperbaharui');
                } else {
                    isChecked.prop('checked', isChecked.is(':checked') ? null : 'checked');
                    toastr.error('Data gagal Diperbaharui');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                isChecked.prop('checked', isChecked.is(':checked') ? null : 'checked');

            },
        });
    }

    function tambah() {
        $('#id').val('');

        $('#form')[0].reset();
        var form = $('#form input');
        form.removeClass('is-invalid is-valid');

        $('#title').text('Tambah Satuan');
        modal.modal('show');
    }

    function edit(id) {
        $.ajax({
            type: "POST",
            url: "/satuan/getdata",
            data: {
                id: id
            },
            dataType: "JSON",
            beforeSend: function () {
                showblockUI();
            },
            complete: function () {
                hideblockUI();
            },
            success: function (response) {
                if (response.status) {
                    var form = $('#form input');
                    form.removeClass('is-invalid is-valid');
                    $('#id').val(response.data.id);
                    $('#nama').val(response.data.nama_satuan);
                    $('#title').text('Edit Satuan');
                    modal.modal('show');
                }
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

    function hapus(id, nama) {
        $('#satuan').text(nama);
        $('#btn-delete').attr('onclick', 'remove(' + id + ')');
        modald.modal('show');
    }

    function remove(id) {
        $.ajax({
            url: "/satuan/hapus",
            type: "POST",
            dataType: "JSON",
            data: {
                id: id
            },
            beforeSend: function () {
                showblockUI();
            },
            complete: function () {
                hideblockUI();
            },
            success: function (response) {
                if (response.status) {
                    toastr.success('Data Berhasil dihapus');
                    modald.modal('hide');
                    table.ajax.reload();
                } else {
                    toastr.warning(
                        'Maaf, anda tidak dapat menghapus data tersebut karna telah berelasi dengan data lain.'
                    );
                    modald.modal('hide');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
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

    $('#form').submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/satuan/simpan",
            data: $(this).serialize(),
            dataType: "JSON",
            beforeSend: function () {
                showblockUI();
            },
            complete: function () {
                hideblockUI();
            },
            success: function (response) {
                if (response.status) {
                    $('#form')[0].reset();
                    table.ajax.reload();
                    toastr.success(response.notif);
                    modal.modal('hide');
                } else {
                    $.each(response.errors, function (key, value) {
                        $('[name="' + key + '"]').addClass('is-invalid');
                        $('[name="' + key + '"]').next().text(value);
                        if (value == "") {
                            $('[name="' + key + '"]').removeClass('is-invalid');
                            $('[name="' + key + '"]').addClass('is-valid');
                        }
                    });
                }
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
    });
</script>
<?= $this->endSection() ?>