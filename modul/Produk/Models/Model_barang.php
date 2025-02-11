<?php

namespace Modul\Produk\Models;

use CodeIgniter\Model;

use CodeIgniter\Database\ConnectionInterface;

use CodeIgniter\Validation\ValidationInterface;

class Model_barang extends Model
{
    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
    }

    protected $table              = 'barang';
    protected $primaryKey         = 'id';
    protected $useAutoIncrement   = true;
    protected $returnType         = 'array';

    protected $allowedFields      = [
        'id_toko',
        'id_kategori',
        'nama_barang',
        'harga_jual',
        'harga_modal',
        'status',
        'kelola_stok',
        'stok',
        'stok_min',
        'foto',
        'deskripsi',
    ];
}
