<?php

namespace Modul\Bahan\Controllers;

use App\Controllers\BaseController;
use Hermawan\DataTables\DataTable;
use Modul\Bahan\Models\Model_bahan;

class Bahan extends BaseController
{
    public function __construct()
    {
        $this->bahan = new Model_bahan();
    }

    public function index()
    {
        $id_toko = $this->session->get("id_toko");
        $satuan = $this->db->query("SELECT * FROM satuan WHERE id_toko = '$id_toko' AND status = 1 ORDER BY nama_satuan ASC")->getResult();

        $data_page = [
            'menu'    => 'master',
            'submenu' => 'bahan',
            'title'   => 'Bahan Baku',
            'satuan'  => $satuan
        ];

        return view('Modul\Bahan\Views\viewBahan', $data_page);
    }

    public function datatable()
    {
        $id_toko = $this->session->get('id_toko');

        $builder = $this->db->table('bahan_baku as a')
            ->select("a.id as id, a.nama_bahan as nama_bahan, a.biaya as biaya, a.harga as harga, a.stok as stok, a.stokmin as stokmin, a.status as status, b.nama_satuan as nama_satuan")
            ->join("satuan as b", "b.id = a.id_satuan")
            ->where('a.id_toko', $id_toko)->orderBy('a.id', 'DESC');

        return DataTable::of($builder)
            ->addNumbering('no')
            ->setSearchableColumns(['LOWER(nama_bahan)'])
            ->add('action', function ($row) {
                return '<button type="button" class="btn btn-light" title="Edit Data" onclick="edit(\'' . $row->id . '\')"><i class="fa fa-edit"></i></button>
                <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->nama_bahan . '\')"><i class="fa fa-trash"></i></button>';
            })->add('is_active', function ($row) {
                return '<div class="form-switch">
                            <input type="checkbox" class="form-check-input"  onclick="changeStatus(\'' . $row->id . '\');" id="set_active' . $row->id . '" ' . isChecked($row->status) . '>
                            <label class="form-check-label" for="set_active' . $row->id . '">' . isLabelChecked($row->status) . '</label>
                        </div>';
            })->add('biaya', function ($row) {
                return 'Rp ' . number_format($row->biaya);
            })->add('harga', function ($row) {
                return 'Rp ' . number_format($row->harga);
            })->add("nama_bahan", function ($row) {
                if ($row->stok <= $row->stokmin) {
                    return $row->nama_bahan . '<br><span class="badge rounded-pill bg-warning text-dark" style="font-size: x-small;">Stok mencapai batas minimum</span>';
                } else {
                    return $row->nama_bahan;
                }
            })
            ->toJson(true);
    }

    public function setStatus()
    {
        $builder = $this->db->table('bahan_baku');

        $getData = $builder->where('id', $this->request->getPost('id'))
            ->get()
            ->getRowArray();

        if (!$getData) {
            $response = [
                'status' => false,
                'errors' => 'Data Tidak Ditemukan.'
            ];
        } else {
            $this->bahan->update($this->request->getPost('id'), ['status' => ($getData['status']) ? "0" : "1"]);
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
                'label'  => 'Nama bahan',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'satuan' => [
                'label'  => 'Satuan',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'biaya' => [
                'label'  => 'Biaya',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'harga' => [
                'label'  => 'Harga',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'stok' => [
                'label'  => 'Stok',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'stokmin' => [
                'label'  => 'Stok minimum',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
        ]);

        if (!$rules) {
            $errors = [
                'nama'      => $this->validation->getError('nama'),
                'satuan'    => $this->validation->getError('satuan'),
                'biaya'     => $this->validation->getError('biaya'),
                'harga'     => $this->validation->getError('harga'),
                'stok'      => $this->validation->getError('stok'),
                'stokmin'   => $this->validation->getError('stokmin'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id        = $this->request->getPost('id');
            $id_toko   = $this->session->get('id_toko');
            $nama      = $this->request->getPost('nama');
            $satuan    = $this->request->getPost('satuan');
            $biaya     = $this->request->getPost('biaya');
            $harga     = $this->request->getPost('harga');
            $stok      = $this->request->getPost('stok');
            $stokmin   = $this->request->getPost('stokmin');

            $data = [
                'id'              => $id,
                'id_satuan'       => $satuan,
                'id_toko'         => $id_toko,
                'nama_bahan'      => $nama,
                'biaya'           => getAmount($biaya),
                'harga'           => getAmount($harga),
                'stok'            => $stok,
                'stokmin'         => $stokmin,
            ];

            if (!$id) {
                $data['status'] = 1;
            }

            $save = $this->bahan->save($data);

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

        $data = $this->db->table('bahan_baku')
            ->where('id', $id)
            ->get()->getRow();

        if ($data) {
            $response = [
                'status' => TRUE,
                'data'   => $data,
                'harga'  => 'Rp. ' . number_format($data->harga, 0, ',', '.'),
                'biaya'  => 'Rp. ' . number_format($data->biaya, 0, ',', '.'),
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
            $this->bahan->delete($id);
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
