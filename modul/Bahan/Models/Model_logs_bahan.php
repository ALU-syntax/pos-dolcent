<?php

namespace Modul\Bahan\Models;

use CodeIgniter\Model;

use CodeIgniter\Database\ConnectionInterface;

use CodeIgniter\Validation\ValidationInterface;

class Model_logs_bahan extends Model
{
    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
    }

    protected $table              = 'logs_bahan';
    protected $primaryKey         = 'id';
    protected $useAutoIncrement   = true;
    protected $returnType         = 'array';

    protected $allowedFields      = [
        'id_barang',
        'id_user',
        'tgl',
        'keterangan'
    ];
}
