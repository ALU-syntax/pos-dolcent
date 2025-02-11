<?php $routes->group('reward', ['namespace' => 'Modul\Reward\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Reward::index');
    $routes->post('simpan', 'Reward::simpan');
    $routes->post('datatable', 'Reward::datatable');
    $routes->post('reset', 'Reward::reset');
});
