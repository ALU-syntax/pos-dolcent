<?php

namespace Modul\Kasir\Models;

use CodeIgniter\Model;

use CodeIgniter\Database\ConnectionInterface;

use CodeIgniter\Validation\ValidationInterface;

class Model_penjualan extends Model
{
    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
    }

    protected $table              = 'penjualan';
    protected $primaryKey         = 'id';
    protected $useAutoIncrement   = true;
    protected $returnType         = 'array';

    protected $allowedFields      = [
        'id_toko',
        'id_user',
        'id_pelanggan',
        'id_tipe_bayar',
        'id_discount',
        'pelanggan',
        'subtotal',
        'ppn',
        'biaya_layanan',
        'total',
        'laba',
        'tgl',
        'discount',
        'buktibayar',
        'tipe_pesanan',
        'tanggal_preorder',
        'status_preorder',
        'id_list_barang'
    ];
}
