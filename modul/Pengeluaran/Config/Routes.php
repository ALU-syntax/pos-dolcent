<?php $routes->group('pengeluaran', ['namespace' => 'Modul\Pengeluaran\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Pengeluaran::index');
    $routes->post('getdata', 'Pengeluaran::getdata');
    $routes->post('datatable', 'Pengeluaran::datatable');
    $routes->post('simpan', 'Pengeluaran::simpan');
    $routes->post('hapus', 'Pengeluaran::hapus');
});

$routes->group('pengeluaran', ['namespace' => 'Modul\Pengeluaran\Controllers'], function ($routes) {
    $routes->post('export-excel', 'Pengeluaran::exportExcel');
});
