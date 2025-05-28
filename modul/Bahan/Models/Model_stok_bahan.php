<?php

namespace Modul\Bahan\Models;

use CodeIgniter\Model;

use CodeIgniter\Database\ConnectionInterface;

use CodeIgniter\Validation\ValidationInterface;

class Model_stok_bahan extends Model
{
    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
    }

    protected $table              = 'stok_bahan_baku';
    protected $primaryKey         = 'id';
    protected $useAutoIncrement   = true;
    protected $returnType         = 'array';

    protected $allowedFields      = [
        'id_bahan',
        'tanggal',
        'jumlah',
        'tipe',
        'delete',
        'id_biaya_produksi',
        'id_detail_penjualan'
    ];
}
