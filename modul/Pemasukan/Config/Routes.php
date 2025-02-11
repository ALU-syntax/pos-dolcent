<?php $routes->group('pemasukan', ['namespace' => 'Modul\Pemasukan\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Pemasukan::index');
    $routes->post('getdata', 'Pemasukan::getdata');
    $routes->post('datatable', 'Pemasukan::datatable');
    $routes->post('simpan', 'Pemasukan::simpan');
    $routes->post('hapus', 'Pemasukan::hapus');
});
