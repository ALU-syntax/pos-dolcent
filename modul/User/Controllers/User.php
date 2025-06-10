<?php

namespace Modul\User\Controllers;

use App\Controllers\BaseController;
use Hermawan\DataTables\DataTable;
use Modul\User\Models\Model_akses_menu;
use Modul\User\Models\Model_user;

class User extends BaseController
{
    public function __construct()
    {
        $this->user = new Model_user();
        $this->akses = new Model_akses_menu();
    }

    public function index()
    {
        $app_menu = $this->db->query("SELECT id, nama_menu FROM app_menu WHERE status = 1 ORDER BY posisi ASC")->getResult();
        $toko = $this->db->query("SELECT * FROM toko WHERE deleted_at IS NULL ORDER BY nama_toko ASC")->getResult();

        $data_page = [
            'menu'     => 'user',
            'submenu'  => 'user',
            'title'    => 'Data User',
            'app_menu' => $app_menu,
            'toko'     => $toko,
        ];

        return view('Modul\User\Views\viewUser', $data_page);
    }

    public function datatable()
    {
        $id_toko = $this->session->get('id_toko');        

        $builder = $this->db->table('user')
        ->select('user.id, user.email, user.nama as user_nama, user.id_toko, user.nohp, user.status, toko.nama_toko as nama_toko')
        ->join('toko', 'toko.id = user.id_toko')
        ->where('user.email <>', 'supersuperadmin@mail.com')
        ->orderBy('user.id', 'DESC');


        if($this->session->get('email') != 'supersuperadmin@mail.com') {
            $builder->where('id_toko', $id_toko);
        }

        return DataTable::of($builder)
            ->addNumbering('no')
            ->setSearchableColumns(['LOWER(user_nama)'])
            ->add('action', function ($row) {
                return '
                <button type="button" class="btn btn-light" title="Edit Akses Menu" onclick="aksesMenu(\'' . $row->id . '\')"><i class="fas fa-tasks"></i></button>
                <button type="button" class="btn btn-light" title="Edit Data" onclick="edit(\'' . $row->id . '\')"><i class="fa fa-edit"></i></button>
                <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->user_nama . '\')"><i class="fa fa-trash"></i></button>';
            })->add('is_active', function ($row) {
                return '<div class="form-switch">
                            <input type="checkbox" class="form-check-input"  onclick="changeStatus(\'' . $row->id . '\');" id="set_active' . $row->id . '" ' . isChecked($row->status) . '>
                            <label class="form-check-label" for="set_active' . $row->id . '">' . isLabelChecked($row->status) . '</label>
                        </div>';
            })
            ->toJson(true);
    }

    public function setStatus()
    {
        $builder = $this->db->table('user');

        $getData = $builder->where('id', $this->request->getPost('id'))
            ->get()
            ->getRowArray();

        if (!$getData) {
            $response = [
                'status' => false,
                'errors' => 'Data Tidak Ditemukan.'
            ];
        } else {
            $this->user->update($this->request->getPost('id'), ['status' => ($getData['status']) ? "0" : "1"]);
            $response = [
                'status'   => TRUE,
            ];
        }

        echo json_encode($response);
    }

    private function validation()
    {
        $id        = $this->request->getPost('id');
        if ($id) {
            $data = $this->db->query("SELECT email, nohp FROM user WHERE id = '$id'")->getRow();
            $nohp = $data->nohp;
            $email = $data->email;
            $rules = $this->validate([
                'nama' => [
                    'label'  => 'Nama',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'email' => [
                    'label'  => 'E-mail',
                    'rules'  => 'required|valid_email|is_unique[user.email,email,' . $email . ']',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                        'valid_email'  => '{field} tidak valid',
                        'is_unique'    => '{field} telah terdaftar',
                    ]
                ],
                'nohp' => [
                    'label'  => 'No hp',
                    'rules'  => 'required|is_unique[user.nohp,nohp,' . $nohp . ']',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                        'is_unique'    => '{field} telah terdaftar',
                    ]
                ],
                'id_toko' => [
                    'label'  => 'Toko',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
            ]);
        } else {
            $rules = $this->validate([
                'nama' => [
                    'label'  => 'Nama',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'email' => [
                    'label'  => 'E-mail',
                    'rules'  => 'required|valid_email|is_unique[user.email]',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                        'valid_email'  => '{field} tidak valid',
                        'is_unique'    => '{field} telah terdaftar',
                    ]
                ],
                'nohp' => [
                    'label'  => 'No hp',
                    'rules'  => 'required|is_unique[user.nohp,nohp]',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                        'is_unique'    => '{field} telah terdaftar',
                    ]
                ],
                'id_toko' => [
                    'label'  => 'Toko',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'pw' => [
                    'label'  => 'Password',
                    'rules'  => 'required|min_length[6]',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                        'min_length'   => '{field} minimal mengandung 6 karakter',
                    ]
                ],
            ]);
        }

        return $rules;
    }

    public function simpan()
    {
        if (!$this->validation()) {
            $errors = [
                'nama'      => $this->validation->getError('nama'),
                'email'     => $this->validation->getError('email'),
                'nohp'      => $this->validation->getError('nohp'),
                'pw'        => $this->validation->getError('pw'),
                'id_toko'   => $this->validation->getError('id_toko'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id        = $this->request->getPost('id');
            // $id_toko   = $this->session->get('id_toko');
            $id_toko   = $this->request->getPost('id_toko');
            $pw        = $this->request->getPost('pw');

            $data = [
                'id'              => $id,
                'id_toko'         => $id_toko,
                'nama'            => $this->request->getPost('nama'),
                'email'           => $this->request->getPost('email'),
                'nohp'            => $this->request->getPost('nohp'),
                'level'           => $this->request->getPost('level'),
                'status'          => 1
            ];

            if ($pw) {
                $data['password']  = md5(md5($pw));
            }

            $save = $this->user->save($data);

            if (!$id) {
                $data = [
                    'id_user'   => $this->user->getInsertID(),
                    'menu'      => 7
                ];

                $save = $this->akses->save($data);
            }

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

    public function simpanAkses()
    {
        $id_akses  = $this->request->getPost('id_akses');
        $menu      = $this->request->getPost('menu[]');
        $child     = $this->request->getPost('child[]');
        if ($child) {
            $child = implode(',', $child);
        } else {
            $child = null;
        }

        $data = [
            'id'        => $id_akses,
            'menu'      => implode(',', $menu),
            'child'     => $child,
        ];

        $save = $this->akses->save($data);

        if ($save) {
            $respond = [
                'status' => TRUE,
            ];
        } else {
            $respond = [
                'status' => FALSE
            ];
        }

        echo json_encode($respond);
    }

    public function getdata()
    {
        $id = $this->request->getPost('id');

        $data = $this->db->table('user')
            ->where('id', $id)
            ->get()->getRow();

        $menu = $this->db->query("SELECT id, menu, child FROM akses_menu WHERE id_user = '$id'")->getRow();

        if ($data) {
            $response = [
                'status' => TRUE,
                'data'   => $data,
                'menu'   => explode(',', $menu->menu),
                'child'  => explode(',', $menu->child),
                'akses'  => $menu->id
            ];
        } else {
            $response = [
                'status' => false,
            ];
        }

        echo json_encode($response);
    }

    public function getAksesMenu()
    {
        $id = $this->request->getPost('id');

        $menu = $this->db->query("SELECT id, menu, child FROM akses_menu WHERE id_user = '$id'")->getRow();

        if ($menu) {
            $response = [
                'status' => TRUE,
                'menu'   => explode(',', $menu->menu),
                'child'  => explode(',', $menu->child),
                'akses'  => $menu->id
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

        if ($this->user->delete($id)) {
            $response = [
                'status' => true,
            ];
        } else {
            $response = [
                'status' => false,
            ];
        }

        echo json_encode($response);
    }
}