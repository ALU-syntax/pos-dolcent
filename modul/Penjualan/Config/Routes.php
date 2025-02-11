<?php $routes->group('penjualan', ['namespace' => 'Modul\Penjualan\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Penjualan::index');
    $routes->post('datatable', 'Penjualan::datatable');
    $routes->post('hapus', 'Penjualan::hapus');
    
});

$routes->group('penjualan', ['namespace' => 'Modul\Penjualan\Controllers'], function ($routes) {
    $routes->post('export-excel', 'Penjualan::exportExcel');
});

