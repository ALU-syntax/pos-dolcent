<?php $routes->group('utang', ['namespace' => 'Modul\Utang\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Utang::index');
    $routes->post('getdata', 'Utang::getdata');
    $routes->post('datatable', 'Utang::datatable');
    $routes->post('simpan', 'Utang::simpan');
    $routes->post('hapus', 'Utang::hapus');

    // Bayar Utang
    $routes->post('datatable_byr', 'Utang::datatable_byr');
    $routes->post('updateBayar', 'Utang::updateBayar');
    $routes->post('hapusBayar', 'Utang::hapusBayar');
});
