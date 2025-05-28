<?php $routes->group('shift', ['namespace' => 'Modul\Shift\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Shift::index');
    $routes->post('getShift', 'Shift::getShift');
    $routes->post('datatable', 'Shift::datatable');
    // $routes->post('setStatus', 'Shift::setStatus');
    // $routes->post('simpan', 'Shift::simpan');
    // $routes->post('hapus', 'Shift::hapus');
});
