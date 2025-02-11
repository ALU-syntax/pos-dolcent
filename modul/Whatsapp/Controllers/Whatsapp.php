<?php

namespace Modul\Whatsapp\Controllers;

use App\Controllers\BaseController;
use Modul\Penjualan\Libraries\OneSender;

class Whatsapp extends BaseController
{
    public function index()
    {
        $id_toko     = $this->session->get('id_toko');
        $onesender    = $this->db->query("SELECT * FROM onesender WHERE id_toko = '$id_toko'")->getRow();

        $data = [
            'title'       => 'Whatsapp',
            'menu'        => 'config',
            'submenu'     => 'whatsapp',
            'onesender'    => $onesender,
        ];

        return view('Modul\Whatsapp\Views\viewWhatsapp', $data);
    }

    public function simpan()
    {
        $rules = $this->validate([
            'host' => [
                'label'  => 'Host',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'key' => [
                'label'  => 'Key',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
        ]);

        if (!$rules) {
            $errors = [
                'host'  => $this->validation->getError('host'),
                'key'  => $this->validation->getError('key'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id_toko = $this->session->get('id_toko');
            $host = $this->request->getPost('host');
            $key = $this->request->getPost('key');

            $data = [
                'host'  => $host,
                'key'  => $key,
            ];

            $builder = $this->db->table("onesender")->where("id_toko", $id_toko);

            if ($builder->update($data)) {
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

    public function kirim() {
        $rules = $this->validate([
            'pesan' => [
                'label'  => 'Pesan',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'nomor' => [
                'label'  => 'Nomor',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
        ]);

        if (!$rules) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => [
                    'host'  => $this->validation->getError('host'),
                    'key'  => $this->validation->getError('key'),
                ]
            ]);
        }

        $id_toko = $this->session->get('id_toko');
        $setting = $this->db->query("SELECT * FROM onesender WHERE id_toko = '$id_toko'")->getRow();

        $nomor = $this->getPost('nomor');
        $pesan = $this->getPost('pesan');

        $onesender = new OneSender($setting->host, $setting->key);
        $res = $onesender->sendText($nomor, $pesan);

        return $this->response->setJSON([
            'status' => $res
        ]);
    }
}
