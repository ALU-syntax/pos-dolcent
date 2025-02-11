<?php $routes->group('kategori', ['namespace' => 'Modul\Kategori\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Kategori::index');
    $routes->post('getdata', 'Kategori::getdata');
    $routes->post('datatable', 'Kategori::datatable');
    $routes->post('setStatus', 'Kategori::setStatus');
    $routes->post('simpan', 'Kategori::simpan');
    $routes->post('hapus', 'Kategori::hapus');
});
