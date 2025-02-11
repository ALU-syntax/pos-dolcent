<?php $routes->group('biaya', ['namespace' => 'Modul\Biaya\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Biaya::index');
    $routes->post('simpan', 'Biaya::simpan');
});
