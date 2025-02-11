<?php $routes->group('user', ['namespace' => 'Modul\User\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'User::index');
    $routes->post('getdata', 'User::getdata');
    $routes->post('getAksesMenu', 'User::getAksesMenu');
    $routes->post('datatable', 'User::datatable');
    $routes->post('setStatus', 'User::setStatus');
    $routes->post('simpan', 'User::simpan');
    $routes->post('simpanAkses', 'User::simpanAkses');
    $routes->post('hapus', 'User::hapus');
});
