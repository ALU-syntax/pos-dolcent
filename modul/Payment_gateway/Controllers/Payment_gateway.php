<?php

namespace Modul\Payment_gateway\Controllers;

use App\Controllers\BaseController;
use DateTime;
use Hermawan\DataTables\DataTable;
use Modul\Payment_gateway\Models\Model_midtrans;

class Payment_gateway extends BaseController
{
    public function __construct()
    {
        $this->midtrans = new Model_midtrans();
    }

    public function index()
    {
        $id_toko     = $this->session->get('id_toko');
        $midtrans    = $this->db->query("SELECT * FROM midtrans WHERE id = '$id_toko'")->getRow();
        $smartpayment        = $this->db->query("SELECT * FROM smartpayment WHERE id_toko = '$id_toko'")->getRow();
        $npay        = $this->db->query("SELECT * FROM npay WHERE id_toko = '$id_toko'")->getRow();

        $data = [
            'title'       => 'Payment Gateway',
            'menu'        => 'config',
            'submenu'     => 'payment-gateway',
            'midtrans'    => $midtrans,
            'smartpayment' => $smartpayment,
            'npay'        => $npay
        ];

        return view('Modul\Payment_gateway\Views\viewPayment_gateway', $data);
    }

    public function simpanMidtrans()
    {
        $rules = $this->validate([
            'client_key' => [
                'label'  => 'Client key',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'server_key' => [
                'label'  => 'Server key',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
        ]);

        if (!$rules) {
            $errors = [
                'client_key'  => $this->validation->getError('client_key'),
                'server_key'  => $this->validation->getError('server_key'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id_toko      = $this->session->get('id_toko');
            $client_key   = $this->request->getPost('client_key');
            $server_key   = $this->request->getPost('server_key');

            $data = [
                'client_key'  => $client_key,
                'server_key'  => $server_key,
            ];

            $builder = $this->db->table("midtrans")->where("id_toko", $id_toko);

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

    public function simpanSmartpayment()
    {
        $rules = $this->validate([
            'host' => [
                'label'  => 'Host',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'token' => [
                'label'  => 'Token',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
        ]);

        if (!$rules) {
            $errors = [
                'host'  => $this->validation->getError('host'),
                'token'  => $this->validation->getError('token'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id_toko    = $this->session->get('id_toko');
            $host       = $this->request->getPost('host');
            $token      = $this->request->getPost('token');

            $data = [
                'host' => $host,
                'token'  => $token,
            ];

            $builder = $this->db->table("smartpayment")->where("id_toko", $id_toko);

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

    public function simpanNpay()
    {
        $rules = $this->validate([
            'host' => [
                'label'  => 'Host',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'api_key' => [
                'label'  => 'Api Key',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
        ]);

        if (!$rules) {
            $errors = [
                'host'  => $this->validation->getError('host'),
                'api_key'  => $this->validation->getError('api_key'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id_toko    = $this->session->get('id_toko');
            $host       = $this->request->getPost('host');
            $api_key    = $this->request->getPost('api_key');

            $data = [
                'host' => $host,
                'api_key'  => $api_key,
            ];

            $builder = $this->db->table("npay")->where("id_toko", $id_toko);

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
}
