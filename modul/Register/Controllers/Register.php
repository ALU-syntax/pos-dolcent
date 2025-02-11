<?php

namespace Modul\Register\Controllers;

use App\Controllers\BaseController;
use Modul\Register\Models\Model_register;
use Modul\User\Models\Model_akses_menu;
use Modul\User\Models\Model_user;

class Register extends BaseController
{
    function __construct()
    {
        $this->register = new Model_register();
        $this->user = new Model_user();
        $this->akses = new Model_akses_menu();
    }

    public function index()
    {
        return view('Modul\Register\Views\viewRegister');
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
            'email' => [
                'label'  => 'E-mail',
                'rules'  => 'required|is_unique[toko.email]|valid_email',
                'errors' => [
                    'required'     => '{field} harus diisi',
                    'is_unique'    => '{field} telah terdaftar',
                    'valid_email'  => '{field} tidak valid',
                ]
            ],
            'nohp' => [
                'label'  => 'No Hp',
                'rules'  => 'required|integer|is_unique[toko.email]',
                'errors' => [
                    'required'   => '{field} harus diisi',
                    'integer'    => '{field} harus berupa angka',
                    'is_unique'  => '{field} telah terdaftar',
                ]
            ],
            'namau' => [
                'label'  => 'Nama user',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi',
                ]
            ],
            'emailu' => [
                'label'  => 'E-mail user',
                'rules'  => 'required|is_unique[user.email]|valid_email',
                'errors' => [
                    'required'     => '{field} harus diisi',
                    'is_unique'    => '{field} telah terdaftar',
                    'valid_email'  => '{field} tidak valid',
                ]
            ],
            'password' => [
                'label'  => 'Password',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi',
                ]
            ]
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
                'namau'     => $this->validation->getError('namau'),
                'emailu'    => $this->validation->getError('emailu'),
                'password'  => $this->validation->getError('password'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $nama      = $this->request->getPost('nama');
            $email     = $this->request->getPost('email');
            $nohp      = $this->request->getPost('nohp');

            $namau     = $this->request->getPost('namau');
            $emailu    = $this->request->getPost('emailu');
            $password  = $this->request->getPost('password');

            $toko = [
                'nama_toko'       => $nama,
                'email'           => $email,
                'nohp'            => $nohp,
            ];

            $save = $this->register->save($toko);
            $id_toko = $this->register->getInsertID();

            $app_menu = [];
            $menu = $this->db->query("SELECT id FROM app_menu WHERE status = 1")->getResult();
            foreach ($menu as $key => $value) {
                array_push($app_menu, $value->id);
            }
            $child_menu = [];
            $child = $this->db->query("SELECT id FROM app_child_menu WHERE status = 1")->getResult();
            foreach ($child as $key => $value) {
                array_push($child_menu, $value->id);
            }

            $user = [
                'id_toko'         => $id_toko,
                'nama'            => $namau,
                'email'           => $emailu,
                'password'        => md5(md5($password)),
                'status'          => 1,
                'reward'          => 0
            ];

            $save = $this->user->save($user);
            $id_user = $this->user->getInsertID();

            $data = [
                'id_user'   => $id_user,
                'menu'      => implode(',', $app_menu),
                'child'     => implode(',', $child_menu)
            ];

            $save = $this->akses->save($data);

            if ($save) {
                $respond = [
                    'status'   => TRUE,
                    'toko'     => $toko,
                    'user'     => $user,
                    'password' => $password
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
