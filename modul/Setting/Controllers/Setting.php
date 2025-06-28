<?php

namespace Modul\Setting\Controllers;

use App\Controllers\BaseController;
use Hermawan\DataTables\DataTable;
use Modul\Setting\Models\Model_toko;

class Setting extends BaseController
{
    public function __construct()
    {
        $this->toko = new Model_toko();
    }

    public function index()
    {
        $id_toko = $this->session->get('id_toko');
        $data = $this->db->query("SELECT * FROM toko WHERE id = '$id_toko'")->getRow();

        $data_page = [
            'menu'    => 'config',
            'submenu' => 'setting',
            'title'   => 'Setting Toko',
            'data'    => $data
        ];

        return view('Modul\Setting\Views\viewSetting', $data_page);
    }

    private function validation()
    {
        $rules = $this->validate([
            'nama' => [
                'label'  => 'Nama toko',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi',
                ]
            ],
            'nohp' => [
                'label'  => 'Nomor HP',
                'rules'  => 'required',
                'errors' => [
                    'required'     => '{field} harus diisi',
                ]
            ],
            'email' => [
                'label'  => 'E-mail',
                'rules'  => 'required|valid_email',
                'errors' => [
                    'required'     => '{field} harus diisi',
                    'valid_email'  => '{field} tidak valid',
                    'is_unique'    => '{field} telah terdaftar',
                ]
            ],
        ]);

        return $rules;
    }

    public function simpan()
    {
        if (!$this->validation()) {
            $errors = [
                'nama'      => $this->validation->getError('nama'),
                'email'     => $this->validation->getError('email'),
                'nohp'      => $this->validation->getError('nohp'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            // $id        = $this->session->get('id_toko');
            $id        = $this->request->getPost('id');
            $nama      = $this->request->getPost('nama');
            $email     = $this->request->getPost('email');
            $nohp      = $this->request->getPost('nohp');
            $ppn       = $this->request->getPost('ppn');
            $alamat    = $this->request->getPost('alamat');

            $foto      = $this->request->getFile('logo');

            $data = [
                'id'              => $id,
                'nama_toko'       => $nama,
                'email'           => $email,
                'nohp'            => $nohp,
                'ppn'             => $ppn,
                'alamat'          => $alamat
            ];

            $ses_data = [
                'nama_toko'      => $nama,
                'nohp_toko'      => $nohp,
                'email_toko'     => $email,
            ];


            if ($foto->isValid() && !$foto->hasMoved()) {
                $namafile = $foto->getRandomName();
                $foto->move(ROOTPATH . 'public/assets/img/logo/', $namafile);
                // $foto->move(APPPATH . 'public/assets/img/logo/', $namafile);
                // Path penyimpanan gambar harus sesuai dengan root public domain cilspace.neidra.my.id
                // $foto->move('/home/uuytuuac/cilspace.neidra.my.id/assets/img/logo/', $namafile);

                if ($id) {
                    $foto = $this->db->table('toko')->select('logo')->where('id', $id)->get()->getRow();
                    $path = 'assets/img/logo/';
                    $unlink = @unlink($path . $foto->foto);
                }

                $data['logo'] = $namafile;
                $ses_data['logo'] = $namafile;
            }

            // $this->session->set($ses_data);

            $save = $this->toko->save($data);

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

    public function datatable(){
        $builder = $this->db->table('toko');
        $builder->select('id, nama_toko, email, nohp, alamat, logo');
        $builder->where('deleted_at', null);
        $builder->orderBy('id', 'DESC');

        return DataTable::of($builder)
            ->addNumbering('no')
            ->add('action', function ($row) {
                return '<button type="button" class="btn btn-light" title="Edit Data" onclick="edit(\'' . $row->id . '\')"><i class="fa fa-edit"></i></button>
                <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->nama_toko . '\')"><i class="fa fa-trash"></i></button>';
            })
            ->toJson(true);
    }

    public function getData(){
        $id = $this->request->getPost('id');

        $data = $this->toko->find($id);
        
        if ($data) {
            $respond = [
                'status' => TRUE,
                'data'   => $data
            ];
        } else {
            $respond = [
                'status' => FALSE,
                'errors' => 'Data tidak ditemukan'
            ];
        }
        echo json_encode($respond);
    }

    public function hapus()
    {
        $id = $this->request->getPost('id');

        try {
            $this->toko->delete($id);
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
