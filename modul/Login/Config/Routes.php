<?php $routes->group('login', ['namespace' => 'Modul\Login\Controllers'], function ($routes) {
    $routes->get('/', 'Login::index');
    $routes->post('doLogin', 'Login::doLogin');
    $routes->add('logout', 'Login::logout');
});
