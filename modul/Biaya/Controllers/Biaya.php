<?php

namespace Modul\Biaya\Controllers;

use App\Controllers\BaseController;
use DateTime;
use Hermawan\DataTables\DataTable;
use Modul\Setting\Models\Model_toko;

class Biaya extends BaseController
{
    public function __construct()
    {
        $this->toko = new Model_toko();
    }

    public function index()
    {
        $id_toko     = $this->session->get('id_toko');
        $toko        = $this->db->query("SELECT ppn, biaya_layanan FROM toko WHERE id = '$id_toko'")->getRow();

        $data = [
            'title'       => 'Reward',
            'menu'        => 'biaya_layanan',
            'submenu'     => 'biaya',
            'toko'        => $toko,
            'biaya'       => 'Rp. ' . number_format($toko->biaya_layanan, 0, ',', '.'),

        ];

        return view('Modul\Biaya\Views\viewBiaya', $data);
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
            'ppn' => [
                'label'  => 'PPN',
                'rules'  => 'required|integer',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                    'integer'    => '{field} harus berupa angka!',
                ]
            ],
            'biaya' => [
                'label'  => 'Biaya lainnya',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
        ]);

        if (!$rules) {
            $errors = [
                'ppn'    => $this->validation->getError('ppn'),
                'biaya'  => $this->validation->getError('biaya'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id_toko   = $this->session->get('id_toko');
            $ppn       = $this->request->getPost('ppn');
            $biaya     = $this->request->getPost('biaya');

            $data = [
                'id'              => $id_toko,
                'ppn'             => $ppn,
                'biaya_layanan'   => getAmount($biaya),
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
}
