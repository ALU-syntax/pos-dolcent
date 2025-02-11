<?php

namespace Modul\Kredit\Models;

use CodeIgniter\Model;

use CodeIgniter\Database\ConnectionInterface;

use CodeIgniter\Validation\ValidationInterface;

class Model_kredit extends Model
{
    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
    }

    protected $table              = 'kredit';
    protected $primaryKey         = 'id';
    protected $useAutoIncrement   = true;
    protected $returnType         = 'array';

    protected $allowedFields      = [
        'id_toko',
        'id_pelanggan',
        'tgl_kredit',
        'subtotal_barang',
        'total_kredit',
        'dp',
        'periode',
        'status'
    ];
}
