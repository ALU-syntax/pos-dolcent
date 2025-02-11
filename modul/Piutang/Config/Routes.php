<?php $routes->group('piutang', ['namespace' => 'Modul\Piutang\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Piutang::index');
    $routes->post('getdata', 'Piutang::getdata');
    $routes->post('datatable', 'Piutang::datatable');
    $routes->post('simpan', 'Piutang::simpan');
    $routes->post('hapus', 'Piutang::hapus');

    // Bayar Piutang
    $routes->post('datatable_byr', 'Piutang::datatable_byr');
    $routes->post('updateBayar', 'Piutang::updateBayar');
    $routes->post('hapusBayar', 'Piutang::hapusBayar');
});
