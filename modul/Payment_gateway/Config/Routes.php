<?php $routes->group('payment-gateway', ['namespace' => 'Modul\Payment_gateway\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Payment_gateway::index');
    $routes->post('simpanMidtrans', 'Payment_gateway::simpanMidtrans');
    $routes->post('simpanSmartpayment', 'Payment_gateway::simpanSmartpayment');
    $routes->post('simpanNpay', 'Payment_gateway::simpanNpay');
});
