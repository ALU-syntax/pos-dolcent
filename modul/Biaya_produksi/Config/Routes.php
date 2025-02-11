<?php 
$routes->group('biaya-produksi', ['namespace' => 'Modul\Biaya_produksi\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'BiayaProduksi::index');
    $routes->post('datatable', 'BiayaProduksi::datatable');
    $routes->post('simpan', 'BiayaProduksi::simpan');
    $routes->post('getdata', 'BiayaProduksi::getdata');
    $routes->post('hapus', 'BiayaProduksi::hapus');
    $routes->post('fetch-balance', 'BiayaProduksi::fetchBalance');    
});

$routes->group('biaya-produksi', ['namespace' => 'Modul\Biaya_produksi\Controllers'], function ($routes) {
    $routes->post('export-excel', 'BiayaProduksi::exportExcel');
});
