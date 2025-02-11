<?php

namespace Modul\Reward\Controllers;

use App\Controllers\BaseController;
use DateTime;
use Hermawan\DataTables\DataTable;
use Modul\Setting\Models\Model_toko;
use Modul\User\Models\Model_user;

class Reward extends BaseController
{
    public function __construct()
    {
        $this->toko = new Model_toko();
        $this->user = new Model_user();
    }

    public function index()
    {
        $id_toko     = $this->session->get('id_toko');
        $toko        = $this->db->query("SELECT reward FROM toko WHERE id = '$id_toko'")->getRow();

        $data = [
            'title'       => 'Reward',
            'menu'        => 'config',
            'submenu'     => 'reward',
            'toko'        => $toko,
            'reward'      => 'Rp. ' . number_format($toko->reward, 0, ',', '.'),

        ];

        return view('Modul\Reward\Views\viewReward', $data);
    }

    public function datatable()
    {
        $id_toko = $this->session->get('id_toko');

        $builder = $this->db->table('user')->where('id_toko', $id_toko)->orderBy('id', 'DESC');

        return DataTable::of($builder)
            ->addNumbering('no')
            ->setSearchableColumns(['LOWER(nama)', 'LOWER(email)', 'LOWER(nohp)'])
            ->add('transaksi', function ($row) {
                $trx = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_user = '$row->id' AND `delete` <> 1")->getRow()->total;
                return $trx;
            })->add('reward', function ($row) {
                return 'Rp. ' . number_format($row->reward);
            })->add('action', function ($row) {
                return '
                <button type="button" class="btn btn-light" title="Reset Reward" onclick="reset(\'' . $row->id . '\', \'' . $row->nama . '\')"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset</button>';
            })
            ->toJson(true);
    }

    public function simpan()
    {
        $rules = $this->validate([
            'reward' => [
                'label'  => 'Nominal reward',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
        ]);

        if (!$rules) {
            $errors = [
                'reward'      => $this->validation->getError('reward'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id_toko   = $this->session->get('id_toko');
            $reward    = $this->request->getPost('reward');

            $data = [
                'id'              => $id_toko,
                'reward'          => getAmount($reward),
            ];

            if ($this->toko->save($data)) {
                $respond = [
                    'status' => TRUE,
                ];
            } else {
                $respond = [
                    'status' => FALSE
                ];
            }
        }
        echo json_encode($respond);
    }

    public function reset()
    {
        $id = $this->request->getPost('id');

        $data = [
            'id'    => $id,
            'reward' => 0
        ];

        if ($this->user->save($data)) {
            $response = [
                'status'    => true
            ];
        } else {
            $response = [
                'status'    => false
            ];
        }

        echo json_encode($response);
    }
}
