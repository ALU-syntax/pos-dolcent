<?php

namespace Modul\Setting\Models;

use CodeIgniter\Model;

use CodeIgniter\Database\ConnectionInterface;

use CodeIgniter\Validation\ValidationInterface;

class Model_toko extends Model
{
    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
    }

    protected $table              = 'toko';
    protected $primaryKey         = 'id';
    protected $useAutoIncrement   = true;
    protected $useSoftDeletes = true;
    protected $deletedField  = 'deleted_at'; // nama kolom soft delete, default 'deleted_at'
    protected $returnType         = 'array';

    protected $allowedFields      = [
        'nama_toko',
        'email',
        'nohp',
        'logo',
        'ppn',
        'alamat',
        'reward',
        'biaya_layanan'
    ];
}
