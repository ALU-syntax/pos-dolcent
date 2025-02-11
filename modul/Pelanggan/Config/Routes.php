<?php $routes->group('pelanggan', ['namespace' => 'Modul\Pelanggan\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Pelanggan::index');
    $routes->post('getdata', 'Pelanggan::getdata');
    $routes->post('datatable', 'Pelanggan::datatable');
    $routes->post('setStatus', 'Pelanggan::setStatus');
    $routes->post('simpan', 'Pelanggan::simpan');
    $routes->post('hapus', 'Pelanggan::hapus');
});
