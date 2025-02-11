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
            $id        = $this->session->get('id_toko');
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

                if ($id) {
                    $foto = $this->db->table('toko')->select('logo')->where('id', $id)->get()->getRow();
                    $path = 'assets/img/logo/';
                    $unlink = @unlink($path . $foto->foto);
                }

                $data['logo'] = $namafile;
                $ses_data['logo'] = $namafile;
            }

            $this->session->set($ses_data);

            $save = $this->toko->save($data);

            if ($save) {
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
