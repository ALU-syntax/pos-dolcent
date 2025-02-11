<?php $routes->group('summary', ['namespace' => 'Modul\Summary\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Summary::index');
    $routes->post('datatable', 'Summary::datatable');
    $routes->post('filter', 'Summary::filter');
});
