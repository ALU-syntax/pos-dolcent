<?php $routes->group('kredit', ['namespace' => 'Modul\Kredit\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Kredit::index');
    $routes->get('add', 'Kredit::add');
    $routes->get('edit/(:any)', 'Kredit::edit/$1');
    $routes->post('datatable', 'Kredit::datatable');
    $routes->post('getPelanggan', 'Kredit::getPelanggan');
    $routes->post('getBarang', 'Kredit::getBarang');
    $routes->post('getHarga', 'Kredit::getHarga');
    $routes->post('simpan', 'Kredit::simpan');
    $routes->post('hapus', 'Kredit::hapus');

    // Routes Bayar
    $routes->post('datatable_jadwal', 'Kredit::datatable_jadwal');
    $routes->post('datatable_byr', 'Kredit::datatable_byr');
    $routes->post('getPeriode', 'Kredit::getPeriode');
    $routes->post('bayar', 'Kredit::bayar');
    $routes->post('batal_bayar', 'Kredit::batal_bayar');
});
