<?php $routes->group('tipebayar', ['namespace' => 'Modul\Tipebayar\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Tipebayar::index');
    $routes->post('getdata', 'Tipebayar::getdata');
    $routes->post('datatable', 'Tipebayar::datatable');
    $routes->post('setStatus', 'Tipebayar::setStatus');
    $routes->post('simpan', 'Tipebayar::simpan');
    $routes->post('hapus', 'Tipebayar::hapus');
});
