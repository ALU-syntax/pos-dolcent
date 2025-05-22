<?php
namespace Modul\Biaya_produksi\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

use CodeIgniter\Validation\ValidationInterface;

class Model_biaya_produksi extends Model{
    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null){
        parent::__construct($db, $validation);
    }

    protected $table = 'biaya_produksi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $useSoftDeletes = true;
    protected $deletedField  = 'deleted_at'; // nama kolom soft delete, default 'deleted_at'

    
    protected $allowedFields = [
        'id_toko',
        'nominal',
        'deskripsi',
        'foto',
        'tanggal',
        'quantity',
        'id_bahan',
        'biaya_lain',
        'biaya_pengiriman',
    ];
}