<?php $routes->group('produk', ['namespace' => 'Modul\Produk\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Produk::index');
    $routes->post('getdata', 'Produk::getdata');
    $routes->post('getSatuan', 'Produk::getSatuan');
    $routes->post('getDataBahan', 'Produk::getDataBahan');
    $routes->post('getBahan', 'Produk::getBahan');
    $routes->post('datatable', 'Produk::datatable');
    $routes->post('datatable_logs', 'Produk::datatable_logs');
    $routes->post('setStatus', 'Produk::setStatus');
    $routes->post('setStok', 'Produk::setStok');
    $routes->post('setStokBarang', 'Produk::setStokBarang');
    $routes->post('getStok', 'Produk::getStok');
    $routes->post('getStokBarang', 'Produk::getStokBarang');
    $routes->post('updateStok', 'Produk::updateStok');
    $routes->post('updateStokBarang', 'Produk::updateStokBarang');
    $routes->post('simpan', 'Produk::simpan');
    $routes->post('hapus', 'Produk::hapus');
    $routes->post('hapusBahan', 'Produk::hapusBahan');

    // Varian
    $routes->get('varian/(:any)', 'Produk::varian/$1');
    $routes->post('getdataVarian', 'Produk::getdataVarian');
    $routes->post('datatableVarian', 'Produk::datatableVarian');
    $routes->post('simpanVarian', 'Produk::simpanVarian');
    $routes->post('setStatusVarian', 'Produk::setStatusVarian');
    $routes->post('hapusVarian', 'Produk::hapusVarian');
});
