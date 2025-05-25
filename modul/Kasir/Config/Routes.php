<?php $routes->group('kasir', ['namespace' => 'Modul\Kasir\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Kasir::index');
    $routes->post('datatable', 'Kasir::datatable');
    $routes->post('getProduct', 'Kasir::getProduct');
    $routes->post('getPelanggan', 'Kasir::getPelanggan');
    $routes->post('getDiscount', 'Kasir::getDiscount');
    $routes->post('getVarian', 'Kasir::getVarian');
    $routes->post('simpan', 'Kasir::simpan');
    $routes->post('getToken', 'Kasir::getToken');
    $routes->post('smartpayment', 'Kasir::smartpayment');
    $routes->get('whatsapp/(:any)', 'Kasir::whatsapp/$1');
    $routes->get('whatsapp-image/(:any)/(:any)', 'Kasir::whatsappImage/$1/$2');
    $routes->post('getTransactionByPettyCash', 'Kasir::getTransactionByPettyCash');
    
    $routes->post('storePettyCash', 'Kasir::submitPettyCash');
    $routes->post('npay/qr', 'Kasir::npayQr');
    $routes->post('npay/check', 'Kasir::npayCheck');
    $routes->post('npay/pay', 'Kasir::npayPay');
    $routes->post('closePattyCash', 'Kasir::closePattyCash');
});

$routes->group('kasir', ['namespace' => 'Modul\Kasir\Controllers'], function ($routes) {
    $routes->get('struk/(:any)', 'Kasir::struk/$1');
    $routes->get('api-struk/(:any)', 'Kasir::apiStruk/$1');
    $routes->get('api-notif', 'Kasir::notification');
    $routes->post('save-image/(:any)', 'Kasir::SaveImageStruk/$1');
});
