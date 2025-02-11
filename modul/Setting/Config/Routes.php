<?php $routes->group('setting', ['namespace' => 'Modul\Setting\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Setting::index');
    $routes->post('simpan', 'Setting::simpan');
});
