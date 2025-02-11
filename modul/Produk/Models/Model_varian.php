<?php

namespace Modul\Produk\Models;

use CodeIgniter\Model;

use CodeIgniter\Database\ConnectionInterface;

use CodeIgniter\Validation\ValidationInterface;

class Model_varian extends Model
{
    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
    }

    protected $table              = 'varian';
    protected $primaryKey         = 'id';
    protected $useAutoIncrement   = true;
    protected $returnType         = 'array';
    protected $useTimestamps      = true;
    protected $createdField       = 'created_at';
    protected $updatedField       = 'updated_at';

    protected $allowedFields      = [
        'id_barang',
        'id_satuan',
        'nama_varian',
        'harga_jual',
        'harga_modal',
        'keterangan',
        'kelola_stok',
        'stok',
        'stok_min',
        'status',
    ];
}
