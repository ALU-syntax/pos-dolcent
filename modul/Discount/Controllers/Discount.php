<?php

namespace Modul\Discount\Controllers;

use App\Controllers\BaseController;
use Hermawan\DataTables\DataTable;
use Modul\Discount\Models\Model_discount;

class Discount extends BaseController
{
    public function __construct()
    {
        $this->discount = new Model_discount();
    }

    public function index()
    {

        $data_page = [
            'menu'    => 'biaya_layanan',
            'submenu' => 'discount',
            'title'   => 'Discount'
        ];

        return view('Modul\Discount\Views\viewDiscount', $data_page);
    }

    public function datatable()
    {
        $id_toko = $this->session->get('id_toko');

        $builder = $this->db->table('discount')->where('id_toko', $id_toko)->orderBy('id', 'DESC');

        return DataTable::of($builder)
            ->addNumbering('no')
            ->setSearchableColumns(['LOWER(nama_discount)'])
            ->add('action', function ($row) {
                return '<button type="button" class="btn btn-light" title="Edit Data" onclick="edit(\'' . $row->id . '\')"><i class="fa fa-edit"></i></button>
                <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->nama_discount . '\')"><i class="fa fa-trash"></i></button>';
            })->add('is_active', function ($row) {
                return '<div class="form-switch">
                            <input type="checkbox" class="form-check-input"  onclick="changeStatus(\'' . $row->id . '\');" id="set_active' . $row->id . '" ' . isChecked($row->status) . '>
                            <label class="form-check-label" for="set_active' . $row->id . '">' . isLabelChecked($row->status) . '</label>
                        </div>';
            })->add('tipe', function ($row) {
                if ($row->tipe == 1) {
                    return 'Persentase';
                } else {
                    return 'Nominal';
                }
            })->add('jumlah', function ($row) {
                if ($row->tipe == 1) {
                    return '%' . $row->jumlah;
                } else {
                    return 'Rp. ' . number_format($row->jumlah);
                }
            })
            ->toJson(true);
    }

    public function setStatus()
    {
        $builder = $this->db->table('discount');

        $getData = $builder->where('id', $this->request->getPost('id'))
            ->get()
            ->getRowArray();

        if (!$getData) {
            $response = [
                'status' => false,
                'errors' => 'Data Tidak Ditemukan.'
            ];
        } else {
            $this->discount->update($this->request->getPost('id'), ['status' => ($getData['status']) ? "0" : "1"]);
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
                'label'  => 'Nama discount',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'tipe' => [
                'label'  => 'Tipe discount',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'jumlah' => [
                'label'  => 'Jumlah discount',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
        ]);

        if (!$rules) {
            $errors = [
                'nama'      => $this->validation->getError('nama'),
                'tipe'      => $this->validation->getError('tipe'),
                'jumlah'    => $this->validation->getError('jumlah'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id        = $this->request->getPost('id');
            $id_toko   = $this->session->get('id_toko');
            $nama      = $this->request->getPost('nama');
            $tipe      = $this->request->getPost('tipe');
            $jumlah    = $this->request->getPost('jumlah');

            $data = [
                'id'              => $id,
                'id_toko'         => $id_toko,
                'nama_discount'   => $nama,
                'tipe'            => $tipe,
                'jumlah'          => getAmount($jumlah),
                'status'          => 1
            ];

            $save = $this->discount->save($data);

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

        $data = $this->db->table('discount')
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
            $this->discount->delete($id);
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
