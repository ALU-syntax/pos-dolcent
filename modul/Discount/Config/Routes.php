<?php $routes->group('discount', ['namespace' => 'Modul\Discount\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Discount::index');
    $routes->post('getdata', 'Discount::getdata');
    $routes->post('datatable', 'Discount::datatable');
    $routes->post('setStatus', 'Discount::setStatus');
    $routes->post('simpan', 'Discount::simpan');
    $routes->post('hapus', 'Discount::hapus');
});
