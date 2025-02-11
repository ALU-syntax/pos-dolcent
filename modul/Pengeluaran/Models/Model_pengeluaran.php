<?php

namespace Modul\Pengeluaran\Models;

use CodeIgniter\Model;

use CodeIgniter\Database\ConnectionInterface;

use CodeIgniter\Validation\ValidationInterface;

class Model_pengeluaran extends Model
{
    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
    }

    protected $table              = 'pengeluaran';
    protected $primaryKey         = 'id';
    protected $useAutoIncrement   = true;
    protected $returnType         = 'array';

    protected $allowedFields      = [
        'id_toko',
        'id_kategori_pengeluaran',
        'id_pelanggan',
        'pelanggan',
        'jumlah',
        'foto',
        'tgl',
        'catatan'
    ];
}
