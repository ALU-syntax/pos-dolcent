<?php $routes->group('whatsapp', ['namespace' => 'Modul\Whatsapp\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Whatsapp::index');
    $routes->post('simpan', 'Whatsapp::simpan');
    $routes->post('kirim', 'Whatsapp::kirim');
});
