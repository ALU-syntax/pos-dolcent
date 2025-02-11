<?php $routes->group('satuan', ['namespace' => 'Modul\Satuan\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Satuan::index');
    $routes->post('getdata', 'Satuan::getdata');
    $routes->post('datatable', 'Satuan::datatable');
    $routes->post('setStatus', 'Satuan::setStatus');
    $routes->post('simpan', 'Satuan::simpan');
    $routes->post('hapus', 'Satuan::hapus');
});
