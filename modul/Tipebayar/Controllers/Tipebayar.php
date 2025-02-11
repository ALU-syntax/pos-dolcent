<?php

namespace Modul\Tipebayar\Controllers;

use App\Controllers\BaseController;
use Hermawan\DataTables\DataTable;
use Modul\Tipebayar\Models\Model_tipebayar;

class Tipebayar extends BaseController
{
    public function __construct()
    {
        $this->tipe = new Model_tipebayar();
    }

    public function index()
    {

        $data_page = [
            'menu'    => 'config',
            'submenu' => 'tipebayar',
            'title'   => 'Tipe Bayar'
        ];

        return view('Modul\Tipebayar\Views\viewTipebayar', $data_page);
    }

    public function datatable()
    {
        $id_toko = $this->session->get('id_toko');

        $builder = $this->db->table('tipe_bayar')->where('id_toko', $id_toko)->where('hide', 0)->orderBy('id', 'DESC');

        return DataTable::of($builder)
            ->addNumbering('no')
            ->setSearchableColumns(['LOWER(nama_tipe)'])
            ->add('action', function ($row) {
                return '<button type="button" class="btn btn-light" title="Edit Data" onclick="edit(\'' . $row->id . '\')"><i class="fa fa-edit"></i></button>
                <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->nama_tipe . '\')"><i class="fa fa-trash"></i></button>';
            })->add('is_active', function ($row) {
                return '<div class="form-switch">
                            <input type="checkbox" class="form-check-input"  onclick="changeStatus(\'' . $row->id . '\');" id="set_active' . $row->id . '" ' . isChecked($row->status) . '>
                            <label class="form-check-label" for="set_active' . $row->id . '">' . isLabelChecked($row->status) . '</label>
                        </div>';
            })->add('icon', function ($row) {
                return '<i class="' . $row->icon . ' fs-2"></i>';
            })
            ->toJson(true);
    }

    public function setStatus()
    {
        $builder = $this->db->table('tipe_bayar');

        $getData = $builder->where('id', $this->request->getPost('id'))
            ->get()
            ->getRowArray();

        if (!$getData) {
            $response = [
                'status' => false,
                'errors' => 'Data Tidak Ditemukan.'
            ];
        } else {
            $this->tipe->update($this->request->getPost('id'), ['status' => ($getData['status']) ? "0" : "1"]);
            $response = [
                'status'   => TRUE,
            ];
        }

        echo json_encode($response);
    }

    public function simpan()
    {
        $rules = $this->validate([
            'nama' => [
                'label'  => 'Nama',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'icon' => [
                'label'  => 'Icon',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
        ]);

        if (!$rules) {
            $errors = [
                'nama'      => $this->validation->getError('nama'),
                'icon'      => $this->validation->getError('icon'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id        = $this->request->getPost('id');
            $id_toko   = $this->session->get('id_toko');
            $nama      = $this->request->getPost('nama');
            $icon      = $this->request->getPost('icon');

            $data = [
                'id'              => $id,
                'id_toko'         => $id_toko,
                'nama_tipe'       => $nama,
                'icon'            => $icon,
                'status'          => 1
            ];

            $save = $this->tipe->save($data);

            if ($save) {
                if ($id) {
                    $notif = "Data berhasil diperbaharui";
                } else {
                    $notif = "Data berhasil ditambahkan";
                }
                $respond = [
                    'status' => TRUE,
                    'notif'  => $notif
                ];
            } else {
                $respond = [
                    'status' => FALSE
                ];
            }
        }
        echo json_encode($respond);
    }

    public function getdata()
    {
        $id = $this->request->getPost('id');

        $data = $this->db->table('tipe_bayar')
            ->where('id', $id)
            ->get()->getRow();

        if ($data) {
            $response = [
                'status' => TRUE,
                'data'   => $data
            ];
        } else {
            $response = [
                'status' => false,
            ];
        }

        echo json_encode($response);
    }

    public function hapus()
    {
        $id = $this->request->getPost('id');

        try {
            $this->tipe->delete($id);
            return $this->response->setJSON(['status' => true]);
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            $errorMessage = $e->getMessage();

            if (strpos($errorMessage, 'foreign key constraint') !== false) {
                return $this->response->setJSON(['status' => false]);
            } else {
                return $this->response->setJSON(['status' => false]);
            }
        }
    }
}
