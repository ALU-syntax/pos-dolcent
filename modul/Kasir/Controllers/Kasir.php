<?php

namespace Modul\Kasir\Controllers;

use App\Controllers\BaseController;
use chillerlan\QRCode\QRCode;
use Hermawan\DataTables\DataTable;
use DateTime;
use Modul\Payment_gateway\Libraries\Npay;
use Modul\Payment_gateway\Libraries\SmartPayment;
use Modul\Kasir\Models\Model_detail_penjualan;
use Modul\Kasir\Models\Model_penjualan;
use Modul\Whatsapp\Libraries\OneSender;
use Modul\User\Models\Model_user;
use Midtrans\Config;
use Midtrans\Snap;
use Config\Services;
use Config\Database;


class Kasir extends BaseController
{
    public function __construct()
    {
        $this->penjualan = new Model_penjualan();
        $this->user      = new Model_user();
        $this->detail    = new Model_detail_penjualan();

        // $midtransConfig = config('Midtrans');
        $this->session    = Services::session();
        $this->db         = Database::connect();

        $id_toko = 1;
        $midtrans = $this->db->query("SELECT * FROM midtrans WHERE id_toko = '$id_toko'")->getRow();

        Config::$serverKey = $midtrans->server_key;
        Config::$isProduction = true;
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function index()
    {
        $id = $this->session->get('id');
        $id_toko = $this->session->get('id_toko');

        $toko = $this->db->query("SELECT ppn, biaya_layanan FROM toko WHERE id = '$id_toko'")->getRow();

        $discount = $this->db->query("SELECT * FROM discount WHERE id_toko = '$id_toko' AND status = 1")->getResult();
        $barang = $this->db->table("barang as a")->select('a.id, a.nama_barang, a.harga_jual, a.harga_modal, a.foto, b.nama_kategori')
            ->join('kategori as b', 'a.id_kategori = b.id')->where('a.id_toko', $id_toko)->where('a.status', 1)->get()->getResult();
        $kategori = $this->db->query("SELECT id, nama_kategori FROM kategori WHERE id_toko = '$id_toko' AND status = 1")->getResult();
        $bayar = $this->db->query("SELECT * FROM tipe_bayar WHERE status = 1")->getResult();

        // $lastorder = $this->db->query("SELECT id FROM penjualan WHERE id_toko = '$id_toko' ORDER BY id DESC")->getRow();
        // $lastorder = $this->db->query("detail_penjualan as a")
        //     ->join("")->get()->getResult();

        // Redirect link
        $akses    = $this->db->query("SELECT * FROM akses_menu WHERE id_user = '$id'")->getRow();
        $id_menu  = explode(',', $akses->menu)[0];
        if ($id_menu == 1) {
            $redirect = '/dashboard';
        } else if ($id_menu == 7) {
            $redirect = '/login/logout';
        } else {
            $id_child  = explode(',', $akses->child)[0];
            $child     = $this->db->query("SELECT link FROM app_child_menu WHERE id = '$id_child'")->getRow();
            $redirect = '/' . $child->link;
        }

        // Midtrans
        $midtrans = $this->db->query("SELECT * FROM midtrans WHERE id_toko = '$id_toko'")->getRow();
        // NPAY
        $npay = $this->db->query("SELECT * FROM npay WHERE id_toko = '$id_toko'")->getRow();
        // SMARTPAYMENT
        $smartpayment = $this->db->query("SELECT * FROM smartpayment WHERE id_toko = '$id_toko'")->getRow();

        $data = [
            'barang'    => $barang,
            'kategori'  => $kategori,
            'bayar'     => $bayar,
            'toko'      => $toko,
            'discount'  => $discount,
            'redirect'  => $redirect,
            'midtrans'  => $midtrans,
            'npay'      => $npay,
            'smartpayment' => $smartpayment,
        ];

        return view('Modul\Kasir\Views\viewKasir', $data);
    }

    public function datatable()
    {
        $id_toko = $this->session->get('id_toko');
        $now = date("Y-m-d");

        $builder = $this->db->table('penjualan as a')
            ->select('a.id as id, a.tgl as tgl, a.total as total, a.subtotal as subtotal, a.ppn as ppn, a.discount as discount, a.laba as laba, a.pelanggan as pelanggan, b.nama as nama_pelanggan, b.nohp as nohp, c.icon as icon, c.nama_tipe as nama_tipe, d.nama as kasir')
            ->join('pelanggan as b', 'a.id_pelanggan = b.id', 'left')
            ->join('tipe_bayar as c', 'c.id = id_tipe_bayar')
            ->join('user as d', 'a.id_user = d.id')
            ->where('a.id_toko', $id_toko)->where("DATE(a.tgl)", $now)
            ->where('a.delete <>', 1)
            ->orderBy('a.id', 'DESC');

        return DataTable::of($builder)
            ->addNumbering('no')
            ->setSearchableColumns(['LOWER(b.nama)', 'LOWER(a.pelanggan)'])
            ->add('metode', function ($row) {
                return '<i class="' . $row->icon . '"></i>&nbsp; ' . $row->nama_tipe . '';
            })->add('subtotal', function ($row) {
                return 'Rp. ' . number_format($row->subtotal);
            })->add('tgl', function ($row) {
                $tgl = new DateTime($row->tgl);
                $date = $tgl->format('d F Y, H:i');

                return $date;
            })->add('pelanggan', function ($row) {
                if ($row->nama_pelanggan) {
                    return $row->nama_pelanggan;
                } else if ($row->pelanggan) {
                    return $row->pelanggan;
                } else {
                    return '--';
                }
            })->add('action', function ($row) {
                // Data invoice
                $orderNumber = $row->id;
                $orderDate = $row->tgl;

                $products = [];
                $detail = $this->db->table("detail_penjualan as a")
                    ->select("a.qty, b.nama_barang, d.nama_satuan")
                    ->join("barang as b", "b.id = a.id_barang")
                    ->join("varian as c", "c.id = a.id_varian", "left")
                    ->join("satuan as d", "d.id = c.id_satuan", "left")
                    ->where('a.delete <>', 1)
                    ->where('a.id_penjualan', $row->id)->get()->getResult();

                foreach ($detail as $key) {
                    $prod = $key->qty . 'x ' . $key->nama_barang . ' - ' . $key->nama_satuan;
                    array_push($products, $prod);
                }

                $subtotal = "Rp " . number_format($row->subtotal);
                $total = "Rp " . number_format($row->total);
                $paymentMethod = $row->nama_tipe . ": Rp " . number_format($row->total);

                // Membuat link WhatsApp dengan pesan invoice
                $whatsappMessage = "Halo, berikut ini invoice pesanan Anda:\n\nOrder from " . $this->session->get('nama_toko') . "\n*{$orderNumber}* ({$orderDate})\n\nProduct:\n" . $this->formatProducts($products) . "\n\nSubtotal: {$subtotal}\n\nTotal: {$total}\n\nPayment:\n{$paymentMethod}\n\nThank you for shopping with us";
                $whatsappLink = "https://wa.me/$row->nohp?text=" . urlencode($whatsappMessage);

                return '<a href="/kasir/struk/' . base64_encode($row->id) . '" class="btn btn-light" title="Cetak Struk" target="_blank"><i class="fas fa-receipt"></i></a>
                <a href="' . $whatsappLink . '" class="btn btn-light" title="Direct Whatsapp" target="_blank"><i class="fab fa-whatsapp"></i></a>';
            })
            ->toJson(true);
    }

    public function getVarian()
    {
        $id_barang = $this->request->getPost('id');
        $id_varian = $this->request->getPost('id_varian');

        $varian    = $this->db->query("SELECT a.id, a.id_barang, a.harga_jual, a.harga_modal, a.keterangan, b.id as id_barang, b.nama_barang, c.nama_satuan FROM varian a JOIN barang b ON a.id_barang = b.id JOIN satuan c ON a.id_satuan = c.id WHERE a.id_barang = '$id_barang' AND a.status = 1 ORDER BY a.id ASC")->getResult();
        $html      = '';

        foreach ($varian as $key) {
            if ($id_varian) {
                if (in_array($key->id, $id_varian)) {
                    $disabled = 'disabled-div';
                } else {
                    $disabled = '';
                }
            } else {
                $disabled = '';
            }

            if ($key->keterangan) {
                $ket = '<p>' . $key->keterangan . '</p>';
            } else {
                $ket = '-';
            }

            $bahan = $this->db->query("SELECT SUM(b.harga) as harga, SUM(b.biaya) as biaya FROM bahan_barang a JOIN bahan_baku b ON a.id_bahan_baku = b.id WHERE a.id_barang = '$key->id_barang'")->getRow();
            if ($bahan) {
                $harga_jual = $key->harga_jual + $bahan->harga;
                $harga_modal = $key->harga_modal + $bahan->biaya;
            } else {
                $harga_jual = $key->harga_jual;
                $harga_modal = $key->harga_modal;
            }

            $html .= '<div class="row ' . $disabled . '" id="varian' . $key->id . '">
                        <input type="hidden" value="' . $key->nama_barang . '">
                        <input type="hidden" value="' . $harga_jual . '">
                        <input type="hidden" value="' . $harga_modal . '">
                        <input type="hidden" value="' . $key->nama_satuan . '">
                        <input type="hidden" value="' . $key->id_barang . '">
                        <div class="col-6">
                            <div class="cat action">
                                <label>
                                    <input type="checkbox" value="' . $key->id . '" name="varian" id="varian' . $key->id . '"><span>' . $key->nama_satuan . '</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-6 text-end align-middle">
                            <h5>Rp. ' . number_format($harga_jual) . '</h5>
                            ' . $ket . '
                        </div>
                        <div class="col-12">
                            <hr>
                        </div>
                    </div>';
        }

        if ($varian) {
            $response = [
                'status'    => true,
                'html'      => $html
            ];
        } else {
            $response = [
                'status'    => false
            ];
        }

        echo json_encode($response);
    }

    public function getProduct()
    {
        $id_toko = $this->session->get('id_toko');
        $id_kategori = $this->request->getPost('id_kategori');

        if ($id_kategori != null || $id_kategori != '') {
            $barang = $this->db->table("barang as a")->select('a.id, a.nama_barang, a.harga_jual, a.harga_modal, a.foto, b.nama_kategori')
                ->join('kategori as b', 'a.id_kategori = b.id')->where('a.id_toko', $id_toko)->where('a.status', 1)->where('a.id_kategori', $id_kategori)->get()->getResult();
        } else {
            $barang = $this->db->table("barang as a")->select('a.id, a.nama_barang, a.harga_jual, a.harga_modal, a.foto, b.nama_kategori')
                ->join('kategori as b', 'a.id_kategori = b.id')->where('a.id_toko', $id_toko)->where('a.status', 1)->get()->getResult();
        }

        $html = '';

        if ($barang) {
            foreach ($barang as $key) {
                $varian = $this->db->query("SELECT id FROM varian WHERE id_barang = '$key->id' AND status = 1")->getRow();
                $totalv = $this->db->query("SELECT COUNT(id) as total FROM varian WHERE id_barang = '$key->id' AND status = 1")->getRow()->total;
                if ($key->foto) {
                    $foto = '<img src="/assets/img/barang/' . $key->foto . '" alt="Foto Barang" class="rounded mb-4" style="height: 120px;">';
                } else {
                    $foto = '<h1 class="prod-icon">' . substr($key->nama_barang, 0, 1) . '</h1>';
                }

                $bahan = $this->db->query("SELECT SUM(b.harga * a.qty) as harga, SUM(b.biaya * a.qty) as biaya FROM bahan_barang a JOIN bahan_baku b ON a.id_bahan_baku = b.id WHERE a.id_barang = '$key->id'")->getRow();
                if ($bahan) {
                    $harga_jual = $key->harga_jual + $bahan->harga;
                    $harga_modal = $key->harga_modal + $bahan->biaya;
                } else {
                    $harga_jual = $key->harga_jual;
                    $harga_modal = $key->harga_modal;
                }

                if ($totalv >= 1) {
                    $html .= '<div class="card card-flush flex-row-fluid p-6 pb-5 mw-100 barang ' . $key->nama_barang . '" style="width: 180px; cursor: pointer;" data-nama="' . $key->nama_barang . '">
                                                            <div class="card-body text-center" onclick="varian(\'' . $key->id . '\', \'' . $key->nama_barang . '\')">
                                                               ' . $foto . '
                                                                <div class="mb-2">
                                                                    <div class="text-center">
                                                                        <span class="fw-bold text-gray-800 cursor-pointer text-hover-primary fs-3 fs-xl-1" onclick="varian(\'' . $key->id . '\', \'' . $key->nama_barang . '\')">' . $key->nama_barang . '</span>
                                                                        <span class="text-gray-400 fw-semibold d-block fs-6 mt-n1">' . $totalv . ' Varian</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>';
                } else {
                    $html .= '<div class="card card-flush flex-row-fluid p-6 pb-5 mw-100 barang ' . $key->nama_barang . '" style="width: 180px; cursor: pointer;" data-nama="' . $key->nama_barang . '">
                                                            <div class="card-body text-center" onclick="add_barang(\'' . $key->id . '\', \'' . $key->nama_barang . '\', \'' . $harga_jual . '\', \'' . $harga_modal . '\')">
                                                               ' . $foto . '
                                                                <div class="mb-2">
                                                                    <div class="text-center">
                                                                        <span class="fw-bold text-gray-800 cursor-pointer text-hover-primary fs-3 fs-xl-1" onclick="add_barang(\'' . $key->id . '\', \'' . $key->nama_barang . '\', \'' . $harga_jual . '\', \'' . $harga_modal . '\')">' . $key->nama_barang . '</span>
                                                                        <span class="text-gray-400 fw-semibold d-block fs-6 mt-n1">' . $totalv . ' Varian</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>';
                }
            }

            $respond = [
                'status'    => true,
                'html'      => $html
            ];
        } else {
            $kategori = $this->db->query("SELECT nama_kategori FROM kategori WHERE id = '$id_kategori'")->getRow();

            $respond = [
                'status'    => false,
                'kategori'  => $kategori->nama_kategori
            ];
        }

        echo json_encode($respond);
    }

    public function getPelanggan()
    {
        $id_toko    = $this->session->get('id_toko');
        $searchTerm = "";
        $data       = [];
        $searchTerm = strtolower($this->request->getVar('q'));
        $builder    = $this->db->table('pelanggan');
        $query      = $builder
            ->where("LOWER(nama) like '%" . $searchTerm . "%' ")
            ->where("id_toko", $id_toko)
            ->select('id as id, nama as text')
            ->orderBy('nama', 'ACS')->orderBy('nama', 'ASC')->get();
        $data = $query->getResult();

        echo json_encode($data);
    }

    public function getDiscount()
    {
        $id = $this->request->getPost('id');
        $data = $this->db->query("SELECT * FROM discount WHERE id = '$id'")->getRow();

        if ($data) {
            $respond = [
                'status'     => true,
                'data'       => $data
            ];
        } else {
            $respond = [
                'status'     => false
            ];
        }

        echo json_encode($respond);
    }

    public function getToken()
    {
        $id_pelanggan = $this->request->getPost('pelanggan');
        $pelanggan    = $this->db->query("SELECT nama, nohp FROM pelanggan WHERE id = '$id_pelanggan'")->getRow();
        $total        = $this->request->getPost('granttotal');
        if ($pelanggan) {
            $parts = explode(' ', $pelanggan->nama);
            $firstname = $parts[0];
            $lastname = implode(' ', array_slice($parts, 1));

            $params = [
                'transaction_details' => [
                    'order_id' => rand(),
                    'gross_amount' => $total,
                ],
                'customer_details' => [
                    'first_name' => "" . $firstname . "",
                    'last_name' => "" . $lastname . "",
                    'email' => "customer@gmail.com",
                    'phone' => "" . $pelanggan->nohp . "",
                ],
            ];
        } else {
            $params = [
                'transaction_details' => [
                    'order_id' => rand(),
                    'gross_amount' => $total,
                ],
                'customer_details' => [
                    'first_name' => "Unregistered",
                    'last_name' => "Customer",
                    'email' => "customer@gmail.com",
                    'phone' => "0000000",
                ],
            ];
        }

        $snapToken = Snap::getSnapToken($params);
        return $this->response->setJSON(['token' => $snapToken]);
    }

    public function simpan()
    {
        var_dump($this->session->get('id_toko'));
        $id_toko      = $this->session->get('id_toko');
        $toko         = $this->db->query("SELECT biaya_layanan FROM toko WHERE id = '$id_toko'")->getRow();
        $id_pelanggan = $this->request->getPost('pelanggan');
        $pelanggan    = $this->db->query("SELECT nama, nohp FROM pelanggan WHERE id = '$id_pelanggan'")->getRow();
        $tgl          = date('Y-m-d H:i');
        $ppn          = $this->request->getPost('ppn');
        $total        = $this->request->getPost('granttotal');
        $method       = $this->request->getPost('method');
        $id_discount  = $this->request->getPost('discount');
        $discount     = $this->request->getPost('discount2');
        $foto         = $this->request->getFile('foto');
        $tipePesanan  = $this->request->getPost('tipe_pesanan'); 
        $tanggalPreorder = $this->request->getPost('tanggal_preorder');

        $id_barang    = $this->request->getPost('id_barang[]');
        $id_varian    = $this->request->getPost('id_varian[]');
        $barang       = $this->request->getPost('barang[]');
        $qty          = $this->request->getPost('qty[]');
        $totalb       = $this->request->getPost('harga[]');
        $modal        = $this->request->getPost('modal[]');

        $totalm = 0;
        foreach ($modal as $key => $value) {
            $totalm += $value * $qty[$key];
        }

        var_dump($this->request->get());

        $laba = array_sum($totalb) - $totalm;

        if(! is_int($method)) {
            $pm = $this->db->query("SELECT id FROM tipe_bayar WHERE id='$method' OR (LOWER(nama_tipe) = '$method' AND hide=1)")->getRow();
            $method = $pm->id;
        }

        $data = [
            'id_toko'       => $id_toko,
            'id_user'       => $this->session->get('id'),
            'id_pelanggan'  => $id_pelanggan,
            'id_tipe_bayar' => $method,
            'total'         => $total,
            'tgl'           => $tgl,
            'subtotal'      => array_sum($totalb),
            'ppn'           => $ppn,
            'biaya_layanan' => $toko->biaya_layanan,
            'total'         => $total,
            'laba'          => $laba,
            'tipe_pesanan'  => $tipePesanan,
        ];

        if ($pelanggan) {
            $data['pelanggan'] = $pelanggan->nama;
        }

        if ($id_discount) {
            $data['id_discount'] = $id_discount;
            $data['discount'] = $discount;
        }

        if ($foto->isValid() && !$foto->hasMoved()) {
            $namafile = $foto->getRandomName();
            $foto->move(ROOTPATH . 'public/assets/img/buktibayar/', $namafile);
            $data['buktibayar'] = $namafile;
        }

        if($tipePesanan == 2){
            $data['tanggal_preorder'] = $tanggalPreorder;
            $data['status_preorder'] = 0;
            $data['id_list_barang'] = $this->penjualan->getInsertId();
        }

        $save = $this->penjualan->save($data);
        $id_penjualan = $this->penjualan->getInsertID();

        foreach ($barang as $key => $value) {
            $data_detail = [
                'id_penjualan'  => $id_penjualan,
                'id_barang'     => $id_barang[$key],
                'barang'        => $value,
                'qty'           => $qty[$key],
                'total'         => $totalb[$key]
            ];

            if ($id_varian[$key]) {
                $data_detail['id_varian'] = $id_varian[$key];

                $stokVarian     = $this->db->query("SELECT stok FROM varian WHERE id = '$id_varian[$key]'")->getRow();
                $new_stok       = $stokVarian->stok -  $qty[$key];
                $varian         = $this->db->table('varian')->where('id', $id_varian[$key])->update(['stok' => $new_stok]);
            }

            $bahan = $this->db->query("SELECT a.*, b.stok FROM bahan_barang a JOIN bahan_baku b ON a.id_bahan_baku = b.id WHERE a.id_barang = '$id_barang[$key]'")->getResult();
            if ($bahan) {
                foreach ($bahan as $kuy) {
                    $new_stok = $kuy->stok - $kuy->qty * $qty[$key];
                    $updateBahan = $this->db->table("bahan_baku")->where("id", $kuy->id_bahan_baku)->update(["stok" => $new_stok]);
                }
            }

            $save = $this->detail->save($data_detail);
        }

        if ($save) {
            // Save Reward
            $id_user = $this->session->get('id');
            $user = $this->db->query("SELECT reward FROM user WHERE id = '$id_user'")->getRow();
            $reward = $this->db->query("SELECT reward FROM toko WHERE id = '$id_toko'")->getRow();
            $data   = [
                'id'     => $id_user,
                'reward' => $user->reward + $reward->reward
            ];

            $this->user->save($data);

            // WA Chat API Link
            $orderNumber = $id_penjualan;
            $orderDate = $tgl;

            $products = [];
            $detail = $this->db->table("detail_penjualan as a")
                ->select("a.qty, b.nama_barang, d.nama_satuan")
                ->join("barang as b", "b.id = a.id_barang")
                ->join("varian as c", "c.id = a.id_varian", "left")
                ->join("satuan as d", "d.id = c.id_satuan", "left")
                ->where('a.id_penjualan', $id_penjualan)->get()->getResult();

            foreach ($detail as $key) {
                $prod = $key->qty . 'x ' . $key->nama_barang . ' - ' . $key->nama_satuan;
                array_push($products, $prod);
            }

            $subtotal = "Rp " . number_format(array_sum($totalb));
            $total = "Rp " . number_format((int)$total);
            $pm = $this->db->query("SELECT nama_tipe FROM tipe_bayar WHERE id = '$method'")->getRow();
            $paymentMethod = $pm->nama_tipe . ": " . $total;

            if ($pelanggan) {
                $nohp = $pelanggan->nohp;
            } else {
                $nohp = '';
            }

            // Membuat link WhatsApp dengan pesan invoice
            $whatsappMessage = "Halo, berikut ini invoice pesanan Anda:\n\nOrder from " . $this->session->get('nama_toko') . "\n*{$orderNumber}* ({$orderDate})\n\nProduct:\n" . $this->formatProducts($products) . "\n\nSubtotal: {$subtotal}\n\nTotal: {$total}\n\nPayment:\n{$paymentMethod}\n\nThank you for shopping with us";
            $whatsappLink = "https://wa.me/$nohp?text=" . urlencode($whatsappMessage);

            $respond = [
                'status'    => true,
                'id'        => base64_encode($id_penjualan),
                'waLink'    => $whatsappLink,
                'total'     => $total,
                'metode'    => $this->db->query("SELECT nama_tipe FROM tipe_bayar WHERE id = '$method'")->getRow()->nama_tipe,
                'pelanggan' => $pelanggan,
            ];
        } else {
            $respond = [
                'status'    => false
            ];
        }

        echo json_encode($respond);
    }

    function formatProducts($products)
    {
        return implode("\n", $products);
    }

    public function struk($id)
    {
        $id = base64_decode($id);
        $penjualan = $this->db->table('penjualan as a')
            ->select('a.*, c.nama_toko, c.nohp, c.alamat, d.nama_tipe, e.nama as kasir')
            ->join('toko as c', 'c.id = a.id_toko')->join('tipe_bayar as d', 'd.id = a.id_tipe_bayar')->join('user as e', 'a.id_user = e.id')
            ->where('a.id', $id)->get()->getRow();
        $detail = $this->db->table('detail_penjualan')->where('id_penjualan', $id)->get()->getResult();

        $data = [
            'penjualan' => $penjualan,
            'detail'    => $detail,
        ];

        return view('Modul\Kasir\Views\viewStruk', $data);
    }
    
    public function apiStruk($id){
        $id = base64_decode($id);
        $penjualan = $this->db->table('penjualan as a')
            ->select('a.*, c.nama_toko, c.nohp, c.alamat, d.nama_tipe, e.nama as kasir')
            ->join('toko as c', 'c.id = a.id_toko')->join('tipe_bayar as d', 'd.id = a.id_tipe_bayar')->join('user as e', 'a.id_user = e.id')
            ->where('a.id', $id)->get()->getRow();
        $detail = $this->db->table('detail_penjualan')->where('id_penjualan', $id)->get()->getResult();

        return $this->response->setJSON([
            'status' => true,
            'penjualan' => $penjualan,
            'detail' => $detail
        ]);
    }
    
    public function SaveImageStruk($name){
        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $name . '.png';
            $file->move(ROOTPATH . 'public/assets/img/invoice/', $fileName);
            
            return $this->response->setStatusCode(ResponseInterface::HTTP_OK)
                                  ->setBody('Image uploaded successfully');
        }

        return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                              ->setBody('Upload failed');
        
    }

    public function whatsapp($id, $nohp=null) {
        $id_toko = $this->session->get('id_toko');
        $id_penjualan = base64_decode($id);
        $row = $this->db->query("SELECT a.id as id, a.tgl as tgl, a.total as total, a.subtotal as subtotal, a.ppn as ppn, a.discount as discount, a.laba as laba, a.pelanggan as pelanggan, a.buktibayar as buktibayar, b.nama as nama_pelanggan, b.nohp as nohp, c.icon as icon, c.nama_tipe as nama_tipe
            FROM penjualan as a
            LEFT JOIN pelanggan as b ON a.id_pelanggan = b.id
            JOIN tipe_bayar as c ON c.id = id_tipe_bayar
            WHERE a.id_toko = $id_toko
            AND a.id = $id_penjualan
        ")->getRow();

        $orderNumber = $row->id;
        $orderDate = $row->tgl;

        $products = [];
        $detail = $this->db->table("detail_penjualan as a")
            ->select("a.qty, b.nama_barang, d.nama_satuan")
            ->join("barang as b", "b.id = a.id_barang")
            ->join("varian as c", "c.id = a.id_varian", "left")
            ->join("satuan as d", "d.id = c.id_satuan", "left")
            ->where('a.id_penjualan', $row->id)
            ->get()
            ->getResult();

        foreach ($detail as $key) {
            $prod = $key->qty . 'x ' . $key->nama_barang . ' - ' . $key->nama_satuan;
            array_push($products, $prod);
        }
        $subtotal = "Rp " . number_format($row->subtotal);
        $total = "Rp " . number_format($row->total);
        $paymentMethod = $row->nama_tipe . ": Rp " . number_format($row->total);

        $message = "Halo, berikut ini invoice pesanan Anda:\n\nOrder from " . $this->session->get('nama_toko') . "\n*{$orderNumber}* ({$orderDate})\n\nProduct:\n" . $this->formatProducts($products) . "\n\nSubtotal: {$subtotal}\n\nTotal: {$total}\n\nPayment:\n{$paymentMethod}\n\nThank you for shopping with us";

        $oneSenderSetting = $this->db->query("SELECT * FROM onesender WHERE id_toko = '$id_toko'")->getRow();
        $oneSender = new OneSender($oneSenderSetting->host, $oneSenderSetting->key);
        $send = $oneSender->sendText($row->nohp ?? $nohp, $message);

        return $this->response->setJSON([
            'status' => $send
        ]);
    }

    public function whatsappPreorder($id, $nohp=null){
        $id_toko = 1;
        $id_penjualan = $id;
        $row = $this->db->query("SELECT a.id as id, a.tgl as tgl, a.total as total, a.subtotal as subtotal, a.ppn as ppn, a.discount as discount, a.laba as laba, a.pelanggan as pelanggan, a.buktibayar as buktibayar, b.nama as nama_pelanggan, b.nohp as nohp, c.icon as icon, c.nama_tipe as nama_tipe
            FROM penjualan as a
            LEFT JOIN pelanggan as b ON a.id_pelanggan = b.id
            JOIN tipe_bayar as c ON c.id = id_tipe_bayar
            WHERE a.id_toko = $id_toko
            AND a.id = $id_penjualan
        ")->getRow();

        $orderNumber = $row->id;
        $orderDate = $row->tgl;

        $products = [];
        $detail = $this->db->table("detail_penjualan as a")
            ->select("a.qty, b.nama_barang, d.nama_satuan")
            ->join("barang as b", "b.id = a.id_barang")
            ->join("varian as c", "c.id = a.id_varian", "left")
            ->join("satuan as d", "d.id = c.id_satuan", "left")
            ->where('a.id_penjualan', $row->id)
            ->get()
            ->getResult();

        foreach ($detail as $key) {
            $prod = $key->qty . 'x ' . $key->nama_barang . ' - ' . $key->nama_satuan;
            array_push($products, $prod);
        }
        $subtotal = "Rp " . number_format($row->subtotal);
        $total = "Rp " . number_format($row->total);
        $paymentMethod = $row->nama_tipe . ": Rp " . number_format($row->total);

        $message = "Halo, berikut ini adalah pesanan Pesanan Preorder Hari ini:\n\nOrder from " . $this->session->get('nama_toko') . "\n*{$orderNumber}* ({$orderDate})\n\nProduct:\n" . $this->formatProducts($products) . "\n\nSubtotal: {$subtotal}\n\nTotal: {$total}\n\nPayment:\n{$paymentMethod}\n\nThank you for shopping with us";

        $oneSenderSetting = $this->db->query("SELECT * FROM onesender WHERE id_toko = '$id_toko'")->getRow();
        $oneSender = new OneSender($oneSenderSetting->host, $oneSenderSetting->key);
        $send = $oneSender->sendText($row->nohp ?? $nohp, $message);

        return $this->response->setJSON([
            'status' => $send
        ]);
    }
    
    public function whatsappImage($id, $rawImage, $nohp=null){
        $id_toko = $this->session->get('id_toko');
        $id_penjualan = base64_decode($id);
        $row = $this->db->query("SELECT a.id as id, a.tgl as tgl, a.total as total, a.subtotal as subtotal, a.ppn as ppn, a.discount as discount, a.laba as laba, a.pelanggan as pelanggan, a.buktibayar as buktibayar, b.nama as nama_pelanggan, b.nohp as nohp, c.icon as icon, c.nama_tipe as nama_tipe
            FROM penjualan as a
            LEFT JOIN pelanggan as b ON a.id_pelanggan = b.id
            JOIN tipe_bayar as c ON c.id = id_tipe_bayar
            WHERE a.id_toko = $id_toko
            AND a.id = $id_penjualan
        ")->getRow();

        $orderNumber = $row->id;
        $orderDate = $row->tgl;

        $products = [];
        $detail = $this->db->table("detail_penjualan as a")
            ->select("a.qty, b.nama_barang, d.nama_satuan")
            ->join("barang as b", "b.id = a.id_barang")
            ->join("varian as c", "c.id = a.id_varian", "left")
            ->join("satuan as d", "d.id = c.id_satuan", "left")
            ->where('a.id_penjualan', $row->id)
            ->get()
            ->getResult();

        foreach ($detail as $key) {
            $prod = $key->qty . 'x ' . $key->nama_barang . ' - ' . $key->nama_satuan;
            array_push($products, $prod);
        }
        $subtotal = "Rp " . number_format($row->subtotal);
        $total = "Rp " . number_format($row->total);
        $paymentMethod = $row->nama_tipe . ": Rp " . number_format($row->total);


        $oneSenderSetting = $this->db->query("SELECT * FROM onesender WHERE id_toko = '$id_toko'")->getRow();
        $oneSender = new OneSender($oneSenderSetting->host, $oneSenderSetting->key);
        $send = $oneSender->sendImage($row->nohp ?? $nohp, $rawImage);

        return $this->response->setJSON([
            'status' => $send
        ]);
        
    }

    public function smartpayment() {
        $rules = $this->validate([
            'nomor_kartu' => [
                'label'  => 'Nomor Kartu',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'pin' => [
                'label'  => 'PIN',
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
                    'nomor_kartu' => $this->validation->getError('nama'),
                    'pin' => $this->validation->getError('nohp'),
                ]
            ]);
        }

        $id_toko    = $this->session->get('id_toko');
        $nomor_kartu = $this->request->getPost('nomor_kartu');
        $pin = $this->request->getPost('pin');
        $total = $this->request->getPost('granttotal');

        try {
            $sp = $this->db->query("SELECT * FROM smartpayment WHERE id_toko = '$id_toko'")->getRow();
            if(!$sp) throw new \Exception('Pengaturan Smartpayment belum diisi');
            if(!$sp->host || !$sp->token) throw new \Exception('Pengaturan Smartpayment belum diisi');

            $toko = $this->db->query("SELECT * FROM toko WHERE id = '$id_toko'")->getRow();
            if(!$toko) throw new \Exception('Toko tidak ditemukan');

            $smartpayment = new SmartPayment($sp->host, $sp->token, $toko->nama_toko);
            $smartpayment->pay([
                'nominal' => $total, 
                'nokartu' => $nomor_kartu, 
                'pin' => $pin
            ]);

            return $this->response->setJSON([
                'status' => true,
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function npayQr() {
        $rules = $this->validate([
            'granttotal' => [
                'label'  => 'Total',
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
                    'grantotal' => $this->validation->getError('granttotal'),
                ]
            ]);
        }

        try {
            $id_toko = $this->session->get('id_toko');
            $npaySetting = $this->db->query("SELECT * FROM npay WHERE id_toko = '$id_toko'")->getRow();
            $npay = new Npay($npaySetting->host, $npaySetting->api_key);
            $merchant = $npay->getMe();

            $data = [
                'merchantId' => $merchant['id'],
                'merchantName' => $merchant['name'],
                'invoiceId' => uniqid(),
                'invoiceAmount' => doubleval($this->request->getPost('granttotal'))
            ];

            $qrcode = (new QRCode)->render(json_encode($data));

            return $this->response->setJSON([
                'status' => true,
                'qr' => $qrcode,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function npayCheck() {
        $rules = $this->validate([
            'invoiceId' => [
                'label'  => 'Invoice ID',
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
                    'invoiceId' => $this->validation->getError('invoiceId'),
                ]
            ]);
        }

        try {
            $id_toko = $this->session->get('id_toko');
            $invoiceId = $this->request->getPost('invoiceId');
            $npaySetting = $this->db->query("SELECT * FROM npay WHERE id_toko = '$id_toko'")->getRow();
            $npay = new Npay($npaySetting->host, $npaySetting->api_key);
            $trans = $npay->findTransaction($invoiceId);

            $data = null;
            if(count($trans) > 0) {
                $data = $trans[0];
            }

            return $this->response->setJSON([
                'status' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function npayPay() {
        $rules = $this->validate([
            'nomor_kartu' => [
                'label'  => 'Nomor Kartu',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'pin' => [
                'label'  => 'PIN',
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
                    'nomor_kartu' => $this->validation->getError('nama'),
                    'pin' => $this->validation->getError('nohp'),
                ]
            ]);
        }

        $id_toko = $this->session->get('id_toko');
        $nomor_kartu = $this->request->getPost('nomor_kartu');
        $pin = $this->request->getPost('pin');
        $total = $this->request->getPost('granttotal');

        try {
            $npaySetting = $this->db->query("SELECT * FROM npay WHERE id_toko = '$id_toko'")->getRow();
            if(!$npaySetting) throw new \Exception('Pengaturan NPAY belum diisi');
            if(!$npaySetting->host || !$npaySetting->api_key) throw new \Exception('Pengaturan NPAY belum diisi');

            $toko = $this->db->query("SELECT * FROM toko WHERE id = '$id_toko'")->getRow();
            if(!$toko) throw new \Exception('Toko tidak ditemukan');

            $npay = new Npay($npaySetting->host, $npaySetting->api_key);

            $npayToken = $npay->getCardToken($nomor_kartu, $pin);
            $npay->createTransaction([
                'ref' => uniqid(),
                'amount' => doubleval($total),
            ], $npayToken['token']);

            return $this->response->setJSON([
                'status' => true,
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function notification(){
        $data = [];
        // Dapatkan builder untuk tabel 'notification'
        $builder = $this->db->table('notification');

        // Lakukan join dengan tabel 'penjualan'
        $builder->join('penjualan', 'notification.id_pesanan = penjualan.id', 'left');

        // Tambahkan kondisi WHERE
        $builder->where('notification.status', 0);

        // Eksekusi query dan dapatkan hasilnya
        $query = $builder->get();
        $results = $query->getResult();

        if(count($results) > 0){
            foreach ($results as $key => $value) {
                $listBarang = $this->db->table('detail_penjualan');
                $listBarang->where('id_penjualan', $results[$key]->id_pesanan);
                $listBarangQuery = $listBarang->get();
                $listBarangResult = $listBarangQuery->getResult();
                $tmpData = [
                    'detail_penjualan' => $listBarangResult,
                    'data_notif' => $results[$key]
                ];
                array_push($data, $tmpData);
            }
        }

        return $this->response->setJSON([
            'data' => $data
        ]);          
    }
}