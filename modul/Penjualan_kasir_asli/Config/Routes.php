<?php $routes->group('penjualan-kasir-asli', ['namespace' => 'Modul\Penjualan_kasir_asli\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Penjualan_kasir_asli::index');
    $routes->post('datatable', 'Penjualan_kasir_asli::datatable');
    $routes->post('hapus', 'Penjualan_kasir_asli::hapus');
    
});

$routes->group('penjualan-kasir-asli', ['namespace' => 'Modul\Penjualan_kasir_asli\Controllers'], function ($routes) {
    $routes->post('export-excel', 'Penjualan_kasir_asli::exportExcel');
});

