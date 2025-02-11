<?php

namespace Modul\Login\Controllers;

use App\Controllers\BaseController;

class Login extends BaseController
{
    public function index()
    {
        $id = $this->session->get('id');

        if ($id) {
            $akses    = $this->db->query("SELECT * FROM akses_menu WHERE id_user = '$id'")->getRow();
            $id_menu  = explode(',', $akses->menu)[0];
            if ($id_menu == 1 || $id_menu == 7) {
                $menu     = $this->db->query("SELECT link FROM app_menu WHERE id = '$id_menu'")->getRow();
                $redirect = '/' . $menu->link;
            } else {
                $id_child  = explode(',', $akses->child)[0];
                $child     = $this->db->query("SELECT link FROM app_child_menu WHERE id = '$id_child'")->getRow();
                $redirect = '/' . $child->link;
            }
            return redirect()->to($redirect);
        } else {
            return view('Modul\Login\Views\viewLogin');
        }
    }

    public function doLogin()
    {
        $rules = $this->validate([
            'email'  => [
                'label'  => 'E-mail',
                'rules'  => 'required',
                'errors' => [
                    'required'  => '{field} harus diisi',
                ]
            ],
            'password'  => [
                'label'  => 'Password',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi',
                ]
            ]
        ]);

        if (!$rules) {
            $errors = [
                'email'     => $this->validation->getError('email'),
                'password'  => $this->validation->getError('password')
            ];

            $respond = [
                'status_form' => FALSE,
                'errors' => $errors,
            ];
        } else {
            $email    = $this->request->getPost('email');
            $password = md5(md5($this->request->getPost('password')));

            $user =  $this->db->table("user as a")->where('a.email', $email)
                ->select('a.*, b.nama_toko, b.logo, b.nohp as nohp_toko, b.email as email_toko, c.menu, c.child')
                ->join('toko as b', 'b.id = a.id_toko')->join('akses_menu as c', 'c.id_user = a.id')->where('a.password', $password)->get()->getRow();

            if ($user) {
                if ($user->status == 1) {
                    $ses_data = [
                        'id'             => $user->id,
                        'id_toko'        => $user->id_toko,
                        'nama_toko'      => $user->nama_toko,
                        'nohp_toko'      => $user->nohp_toko,
                        'email_toko'     => $user->email_toko,
                        'logo'           => $user->logo,
                        'nama'           => $user->nama,
                        'email'          => $user->email,
                        'nohp'           => $user->nohp,
                        'level'          => $user->password,
                        '@#)login(#@'    => TRUE
                    ];

                    $this->session->set($ses_data);

                    if (in_array(1, explode(',', $user->menu))) {
                        $link = '/dashboard';
                    } else if (in_array(7, explode(',', $user->menu))) {
                        $link = '/kasir';
                    } else {
                        $id_child = explode(',', $user->child)[0];
                        $child    = $this->db->query("SELECT link FROM app_child_menu WHERE id = '$id_child'")->getRow();
                        $link     = '/' . $child->link;
                    }

                    $respond = [
                        'status' => TRUE,
                        'link'   => $link
                    ];
                } else {
                    $respond = [
                        'status' => FALSE,
                        'notif'  => 'Akun anda tidak aktif, hubungi admin toko'
                    ];
                }
            } else {
                $respond = [
                    'status' => FALSE,
                    'notif'  => 'Akun tidak ditemukan'
                ];
            }
        }
        echo json_encode($respond);
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to(base_url('login'));
    }
}
