<?php

namespace Modul\Bahan\Controllers;

use App\Controllers\BaseController;
use Hermawan\DataTables\DataTable;
use Modul\Bahan\Models\Model_bahan;
use DateTime;
use Modul\Bahan\Models\Model_logs_bahan;
use Modul\Bahan\Models\Model_stok_bahan;

class Bahan extends BaseController
{
    public function __construct()
    {
        $this->bahan = new Model_bahan();
        $this->logs = new Model_logs_bahan();
        $this->stok_bahan = new Model_stok_bahan();
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
            ->select("a.id as id, a.nama_bahan as nama_bahan, a.biaya as biaya, a.harga as harga, a.stok as stok, a.stokmin as stokmin, a.status as status, a.stok_penjualan as stok_penjualan, b.nama_satuan as nama_satuan")
            ->join("satuan as b", "b.id = a.id_satuan")
            ->where('a.id_toko', $id_toko)->orderBy('a.id', 'DESC');

            // <li><a class="dropdown-item ' . $disabled . '" href="javascript:void(0);" title="Kelola Stok" onclick="stok(\'' . $row->id . '\')" id="btnkel' . $row->id . '"><i class="fa fa-sitemap me-1"></i> Kelola Stok</a></li>
        return DataTable::of($builder)
            ->addNumbering('no')
            ->setSearchableColumns(['LOWER(nama_bahan)'])
            ->add('action', function ($row) {
                return '<button type="button" class="btn btn-light" title="Edit Data" onclick="edit(\'' . $row->id . '\')"><i class="fa fa-edit"></i></button>
                <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->nama_bahan . '\')"><i class="fa fa-trash"></i></button>
                <button type="button" class="btn btn-light" title="Kelola Stok" onclick="stok(\''. $row->id . '\')" id="btnkel' . $row->id . '"><i class="fa fa-sitemap me-1"></i></button>';
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
                'stok_penjualan'  => $stok,
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

    public function getStokBahan()
    {
        $id = $this->request->getPost('id');
        $stok = $this->db->query("SELECT sbb.*, dp.barang AS nama_barang FROM stok_bahan_baku sbb LEFT JOIN detail_penjualan dp ON dp.id = sbb.id_detail_penjualan WHERE id_bahan = '$id'")->getResult();
        // $sale = $this->db->table('detail_penjualan as a')
        //         ->select('a.qty, b.tgl, c.qty AS qty_produksi, d.nama_barang')
        //         ->join('penjualan as b', 'b.id = a.id_penjualan')
        //         ->join('bahan_barang as c', 'c.id_barang = a.id_barang')
        //         ->join('barang as d', 'd.id = c.id_barang')
        //         ->where('c.id_bahan_baku', $id)
        //         ->where('a.delete <>', 1)
        //         ->get()
        //         ->getResult();

        $html = '';

        if ($stok) {
            foreach ($stok as $key) {
                $date = new DateTime($key->tanggal);
                $date = $date->format('d F Y, H:i');
                if ($key->tipe == 1) {
                    $html .= '<div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
                            <div class="card-body">
                                Penambahan Stok &nbsp;<span class="text-success">+' . $key->jumlah . '</span>
                                <br>
                                <small style="font-size: x-small;">' . $date . '</small>
                            </div>
                         </div>';
                } else {
                    if($key->id_detail_penjualan){
                        $html .= '<div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
                            <div class="card-body">
                                Penjualan ' . $key->nama_barang . ' &nbsp;<span class="text-danger">-' . $key->jumlah . '</span>
                                <br>
                                <small style="font-size: x-small;">' . $date . '</small>
                            </div>
                         </div>';
                    }else{
                        $html .= '<div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
                            <div class="card-body">
                                Pengurangan Stok &nbsp;<span class="text-danger">-' . $key->jumlah . '</span>
                                <br>
                                <small style="font-size: x-small;">' . $date . '</small>
                            </div>
                         </div>';
                    }
                }
            }
        } else {
            $html .= '<div class="card mb-2 mt-2" style="background-color: #f2f7ff;" id="nostok">
                            <div class="card-body">
                                <p class="text-center mb-0">Tidak ada riwayat pengaturan stok pada bahan ini.</p>
                            </div>
                         </div>';
        }

        // if ($sale) {
        //     foreach ($sale as $key) {
        //         $date = new DateTime($key->tgl);
        //         $date = $date->format('d F Y, H:i');
        //         $html .= '<div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
        //                     <div class="card-body">
        //                         Penjualan ' . $key->nama_barang . ' &nbsp;<span class="text-danger">-' . $key->qty_produksi . '</span>
        //                         <br>
        //                         <small style="font-size: x-small;">' . $date . '</small>
        //                     </div>
        //                  </div>';
        //     }
        // }

        $response = [
            'html'  => $html,
            'data'  => $this->db->query("SELECT id, nama_bahan, stok, stokmin, stok_penjualan FROM bahan_baku WHERE id = '$id'")->getRow()
        ];

        echo json_encode($response);
    }

    public function updateStokBahan()
    {
        $rules = $this->validate([
            'tipe' => [
                'label'  => 'Tipe pengaturan',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi',
                ]
            ],
            'jumlah' => [
                'label'  => 'Jumlah stok',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi',
                ]
            ],
        ]);

        if (!$rules) {
            $errors = [
                'tipe'      => $this->validation->getError('tipe'),
                'jumlah'    => $this->validation->getError('jumlah'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id_user = $this->session->get("id");
            $id_bahan = $this->request->getPost('id_bahan');
            $tanggal = date('Y-m-d H:i');
            $tipe = $this->request->getPost('tipe');
            $jumlah = $this->request->getPost('jumlah');

            $data = [
                'id_bahan'     => $id_bahan,
                'tanggal'       => $tanggal,
                'jumlah'        => $jumlah,
                'tipe'          => $tipe,
            ];

            $save = $this->stok_bahan->save($data);

            if ($save) {
                $date = new DateTime($tanggal);
                $date = $date->format('d F Y, H:i');

                // Update Stok barang
                $stok_barang = $this->db->query("SELECT stok, stok_penjualan FROM bahan_baku WHERE id = '$id_bahan'")->getRow();
                if ($tipe == 1) {
                    $new_stok = $stok_barang->stok + $jumlah;
                    $new_stok_penjualan = $stok_barang->stok_penjualan + $jumlah;
                } else {
                    $new_stok = $stok_barang->stok - $jumlah;
                    $new_stok_penjualan = $stok_barang->stok_penjualan - $jumlah;
                }

                $update = [
                    'id'    => $id_bahan,
                    'stok'  => $new_stok,
                    'stok_penjualan' => $new_stok_penjualan
                ];

                $this->bahan->save($update);

                if ($tipe == 1) {
                    $data = [
                        'id_bahan'  => $id_bahan,
                        'id_user'    => $id_user,
                        'tgl'        => date("Y-m-d H:i:s"),
                        'keterangan' => 'Penambahan stok (+' . $jumlah . '), Stok update: ' . $new_stok . '',
                        'tipe'       => 1
                    ];

                    $save = $this->logs->save($data);
                } else {
                    $data = [
                        'id_bahan'  => $id_bahan,
                        'id_user'    => $id_user,
                        'tgl'        => date("Y-m-d H:i:s"),
                        'keterangan' => 'Pengurangan stok (-' . $jumlah . '), Stok update: ' . $new_stok . '',
                        'tipe'       => 0
                    ];

                    $save = $this->logs->save($data);
                }

                $respond = [
                    'status'    => true,
                    'data'      => $data,
                    'date'      => $date,
                    'jumlah'    => $jumlah,
                    'stok'      => $new_stok,
                    'stok_penjualan'      => $new_stok_penjualan
                ];
            } else {
                $respond = [
                    'status'    => false
                ];
            }
        }

        echo json_encode($respond);
    }
}
