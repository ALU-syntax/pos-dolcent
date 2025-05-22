<?php $routes->group('bahan', ['namespace' => 'Modul\Bahan\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Bahan::index');
    $routes->post('getdata', 'Bahan::getdata');
    $routes->post('datatable', 'Bahan::datatable');
    $routes->post('setStatus', 'Bahan::setStatus');
    $routes->post('simpan', 'Bahan::simpan');
    $routes->post('hapus', 'Bahan::hapus');
    $routes->post('getStokBahan', 'Bahan::getStokBahan');
    $routes->post('updateStokBahan', 'Bahan::updateStokBahan');
});
