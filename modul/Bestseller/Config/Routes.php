<?php $routes->group('bestseller', ['namespace' => 'Modul\Bestseller\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Bestseller::index');
    $routes->post('filter', 'Bestseller::filter');
});
