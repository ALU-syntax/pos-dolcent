<?php

namespace Modul\Shift\Controllers;

use App\Controllers\BaseController;
use Hermawan\DataTables\DataTable;
use Modul\Dashboard\Models\Model_toko;
use Modul\Kasir\Models\Model_penjualan;
use Modul\Satuan\Models\Model_satuan;
use Modul\Shift\Models\Model_petty_cashes;
use Modul\User\Controllers\User;
use Modul\User\Models\Model_user;

class Shift extends BaseController
{
    public function __construct()
    {
        $this->petty_cash = new Model_petty_cashes();   
        $this->penjualan = new Model_penjualan();
        $this->user = new Model_user();
        $this->toko = new Model_toko();
    }

    public function index()
    {
        $data_page = [
            'menu'    => 'report',
            'submenu' => 'shift',
            'title'   => 'Shift'
        ];

        return view('Modul\Shift\Views\viewShift', $data_page);
    }

    public function datatable()
    {
        $id_toko = $this->session->get('id_toko');

        $builder = $this->db->table('petty_cashes')->where('id_toko', $id_toko)->orderBy('id', 'DESC');

        return DataTable::of($builder)
            ->edit('amount_awal', function($row){
                $amountAwal = $row->amount_awal ? formatRupiah(strval($row->amount_awal), "Rp. ") : "-";
                return $amountAwal;
            })
            ->edit('amount_akhir', function($row){
                $amount_akhir = $row->amount_akhir ? formatRupiah(strval($row->amount_akhir), "Rp. ") : "-";
                return $amount_akhir;
            })
            ->edit('close', function($row){
                $close = $row->close ? $row->close : '-';
                return $close;
            })
            ->toJson(true);
    }
    
      public function getShift()
    {
        $id = $this->request->getPost('id');
        
        // Ambil data petty_cash berdasarkan id
        $pettyCashData = $this->petty_cash->find($id);

        if ($pettyCashData) {
            // Ambil data penjualan yang berelasi dengan id_petty_cash
            $penjualanData = $this->penjualan->where('id_petty_cash', $id)->where('id_tipe_bayar', 1)->findAll();
            $userOpen = $this->user->find($pettyCashData['user_id_started']);
            $toko = $this->toko->find($pettyCashData['id_toko']);

            $response = [
                'status' => true,
                'data' => $pettyCashData,
                'penjualan' => $penjualanData,
                'userOpen' => $userOpen,
                'toko' => $toko
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'Data Shift tidak ditemukan'
            ];
        }

        return $this->response->setJSON($response);
    }

}