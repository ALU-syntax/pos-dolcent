<?php $routes->group('setting', ['namespace' => 'Modul\Setting\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Setting::index');
    $routes->post('hapus', 'Setting::hapus');
    $routes->post('getData', 'Setting::getData');
    $routes->post('simpan', 'Setting::simpan');
    $routes->post('datatable', 'Setting::datatable');
});
