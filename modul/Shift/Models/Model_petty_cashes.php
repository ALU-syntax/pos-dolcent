<?php

namespace Modul\Shift\Models;

use CodeIgniter\Model;

use CodeIgniter\Database\ConnectionInterface;

use CodeIgniter\Validation\ValidationInterface;

class Model_petty_cashes extends Model
{
    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
    }

    protected $table              = 'petty_cashes';
    protected $primaryKey         = 'id';
    protected $useAutoIncrement   = true;
    protected $returnType         = 'array';
    protected $useTimestamps = true;
    
    public function getUserStarted($id)
    {
        return $this->db->table('users')
                        ->where('id', $id)
                        ->get()
                        ->getRow();
    }

    protected $allowedFields      = [
        'id_toko',
        'amount_awal',
        'amount_akhir',
        'user_id_started',
        'user_id_ended',
        'open',
        'close',
        'created_at',
        'updated_at'
    ];
}
