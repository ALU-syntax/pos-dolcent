<?php $routes->group('laporan-penjualan-asli', ['namespace' => 'Modul\Laporan_penjualan_asli\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Laporan_penjualan_asli::index');
    $routes->post('datatable', 'Laporan_penjualan_asli::datatable');
    $routes->post('filter', 'Laporan_penjualan_asli::filter');
});
