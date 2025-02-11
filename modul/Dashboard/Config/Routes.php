<?php $routes->group('/dashboard', ['namespace' => 'Modul\Dashboard\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->post('getRingkasan', 'Dashboard::getRingkasan');
    $routes->post('change_profile', 'Dashboard::change_profile');
});

$routes->group('/dashboard', ['namespace' => 'Modul\Dashboard\Controllers'], function ($routes) {
    $routes->post('export-excel-balance', 'Dashboard::exportExcelBalance');
    $routes->post('export-excel-labarugi', 'Dashboard::exportExcelLabaRugi');
});
