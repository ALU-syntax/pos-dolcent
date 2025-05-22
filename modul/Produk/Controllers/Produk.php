<?php

namespace Modul\Produk\Controllers;

use App\Controllers\BaseController;
use DateTime;
use Hermawan\DataTables\DataTable;
use Modul\Produk\Models\Model_bahan_barang;
use Modul\Produk\Models\Model_barang;
use Modul\Produk\Models\Model_kelola_stok;
use Modul\Produk\Models\Model_logs_barang;
use Modul\Produk\Models\Model_stok_barang;
use Modul\Produk\Models\Model_varian;

class Produk extends BaseController
{
    public function __construct()
    {
        $this->barang = new Model_barang();
        $this->bahan = new Model_bahan_barang();
        $this->logs = new Model_logs_barang();
        $this->stok_barang = new Model_stok_barang();
        $this->varian = new Model_varian();
        $this->stok   = new Model_kelola_stok();
    }

    public function index()
    {
        $id_toko  = $this->session->get('id_toko');
        $kategori = $this->db->query("SELECT * FROM kategori WHERE id_toko = '$id_toko' AND status = 1 ORDER BY nama_kategori ASC")->getResult();

        $data_page = [
            'menu'     => 'master',
            'submenu'  => 'produk',
            'title'    => 'Data Produk',
            'kategori' => $kategori,
        ];

        return view('Modul\Produk\Views\viewProduk', $data_page);
    }

    public function varian($id)
    {
        $id = base64_decode($id);
        $id_toko  = $this->session->get('id_toko');
        $produk   = $this->db->query("SELECT id, nama_barang FROM barang WHERE id = '$id'")->getRow();
        $cek =  $this->db->query("SELECT count(id) as total FROM varian WHERE id_satuan BETWEEN 12 AND 17 AND id_barang = '$id'")->getRow()->total;

        $data_page = [
            'menu'     => 'master',
            'submenu'  => 'produk',
            'title'    => 'Varian',
            'produk'   => $produk,
            'cek'      => $cek
        ];

        return view('Modul\Produk\Views\viewVarian', $data_page);
    }

    public function datatable()
    {
        $id_toko = $this->session->get('id_toko');

        $builder = $this->db->table('barang as a')
            ->select('a.id as id, a.nama_barang as nama_barang, a.harga_jual as harga_jual, a.status as status, a.kelola_stok as kelola_stok, a.foto as foto, b.nama_kategori as nama_kategori')
            ->join('kategori as b', 'b.id = a.id_kategori')
            ->where('a.id_toko', $id_toko)->orderBy('a.id', 'DESC');

        return DataTable::of($builder)
            ->addNumbering('no')
            ->setSearchableColumns(['LOWER(a.nama_barang)'])
            ->add('action', function ($row) {
                if ($row->kelola_stok == 0) {
                    $disabled = 'disabled';
                } else {
                    $disabled = '';
                }

                // return '<button type="button" class="btn btn-light ' . $disabled . '" title="Kelola Stok" onclick="stok(\'' . $row->id . '\')" id="btnkel' . $row->id . '"><i class="fa fa-sitemap"></i></button>
                // <a href="/produk/varian/' . base64_encode($row->id) . '" class="btn btn-light" title="Varian"><i class="fas fa-th"></i></a>
                // <button type="button" class="btn btn-light" title="Edit Data" onclick="edit(\'' . $row->id . '\')"><i class="fa fa-edit"></i></button>
                // <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->nama_barang . '\', \'' . $row->foto . '\')"><i class="fa fa-trash"></i></button>';
                return '<div class="btn-group">
                            <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                
                                <li><a class="dropdown-item" href="/produk/varian/' . base64_encode($row->id) . '"><i class="fas fa-th me-1"></i> Kelola Varian</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0);" onclick="logs(\'' . $row->id . '\', \'' . $row->nama_barang . '\')"><i class="fas fa-history me-1"></i> Logs</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0);" title="Edit Data" onclick="edit(\'' . $row->id . '\')"><i class="fa fa-edit me-1"></i> Edit</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0);"title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->nama_barang . '\', \'' . $row->foto . '\')"><i class="fa fa-trash me-1"></i> Hapus</a></li>
                            </ul>
                        </div>';
            })->add('is_active', function ($row) {
                return '<div class="form-switch">
                            <input type="checkbox" class="form-check-input"  onclick="changeStatus(\'' . $row->id . '\');" id="set_active' . $row->id . '" ' . isChecked($row->status) . '>
                            <label class="form-check-label" for="set_active' . $row->id . '">' . isLabelChecked($row->status) . '</label>
                        </div>';
            })
            ->add('foto', function ($row) {
                if ($row->foto) {
                    return '<image data-fancybox data-src="/assets/img/barang/' . $row->foto . '" src="/assets/img/barang/' . $row->foto . '" height="70" width="80" style="cursor: zoom-in; border-radius: 5px;"/>';
                } else {
                    return '<image src="/assets/img/noimage.png" height="70" width="80" style="cursor: zoom-in;"/>';
                }
            })->add('varian', function ($row) {
                $varian = $this->db->query("SELECT COUNT(id) as total FROM varian WHERE id_barang = '$row->id'")->getRow()->total;
                return '<span class="badge bg-success">Total varian: ' . $varian . '</span>';
            // })->add('stok', function ($row) {
            //     return '<div class="form-switch">
            //                 <input type="checkbox" class="form-check-input"  onclick="changeStok(\'' . $row->id . '\');" id="set_active2' . $row->id . '" ' . isChecked($row->kelola_stok) . '>
            //                 <label class="form-check-label" for="set_active2' . $row->id . '">' . isLabelChecked($row->kelola_stok) . '</label>
            //             </div>';
            })->add("harga_jual", function ($row) {
                $bahan = $this->db->query("SELECT SUM(b.harga * a.qty) as harga FROM bahan_barang a JOIN bahan_baku b ON a.id_bahan_baku = b.id WHERE a.id_barang = '$row->id'")->getRow();
                // if ($bahan) {
                //     $harga_jual = $row->harga_jual + $bahan->harga;
                // } else {
                //     $harga_jual = $row->harga_jual;
                // }

                $harga_jual = $row->harga_jual;

                return 'Rp ' . number_format($harga_jual);
            })
            ->toJson(true);
    }

    public function datatable_logs()
    {
        $builder = $this->db->table('logs_barang as a')
            ->select("a.tgl as tgl, a.keterangan as keterangan, b.nama_barang as nama_barang, c.nama as nama_user")
            ->join("barang as b", "b.id = a.id_barang")->join("user as c", "c.id = a.id_user")->orderBy('a.id', 'DESC');

        return DataTable::of($builder)
            ->filter(function ($builder, $request) {
                $barang = $request->barang;
                $builder->where('id_barang', $barang);
            })
            ->addNumbering('no')
            ->toJson(true);
    }

    public function datatableVarian()
    {
        $builder = $this->db->table('varian as a')
            ->select('a.id as id, a.nama_varian as nama_varian, a.harga_jual as harga_jual, a.harga_modal as harga_modal, a.kelola_stok as kelola_stok, a.status as status, a.created_at as tgl, b.nama_satuan as nama_satuan')
            ->join('satuan as b', 'b.id = a.id_satuan')
            ->orderBy('a.id', 'DESC');

        return DataTable::of($builder)
            ->filter(function ($builder, $request) {
                $builder->where('a.id_barang', $request->id_barang);
            })
            ->addNumbering('no')
            ->setSearchableColumns(['LOWER(b.nama_satuan)', 'LOWER(a.nama_varian)'])
            ->add('action', function ($row) {
                if ($row->kelola_stok == 0) {
                    $disabled = 'disabled';
                } else {
                    $disabled = '';
                }

                return '<button type="button" class="btn btn-light ' . $disabled . '" title="Kelola Stok" onclick="stok(\'' . $row->id . '\')" id="btnkel' . $row->id . '"><i class="fa fa-sitemap"></i></button>
                <button type="button" class="btn btn-light" title="Edit Data" onclick="edit(\'' . $row->id . '\')"><i class="fa fa-edit"></i></button>
                <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->nama_varian . '\')"><i class="fa fa-trash"></i></button>';
            })->add('is_active', function ($row) {
                return '<div class="form-switch">
                            <input type="checkbox" class="form-check-input"  onclick="changeStatus(\'' . $row->id . '\');" id="set_active' . $row->id . '" ' . isChecked($row->status) . '>
                            <label class="form-check-label" for="set_active' . $row->id . '">' . isLabelChecked($row->status) . '</label>
                        </div>';
            })->add('stok', function ($row) {
                return '<div class="form-switch">
                            <input type="checkbox" class="form-check-input"  onclick="changeStok(\'' . $row->id . '\');" id="set_active2' . $row->id . '" ' . isChecked($row->kelola_stok) . '>
                            <label class="form-check-label" for="set_active2' . $row->id . '">' . isLabelChecked($row->kelola_stok) . '</label>
                        </div>';
            })->add('harga_jual', function ($row) {
                return 'Rp.' . number_format($row->harga_jual);
            })->add('harga_modal', function ($row) {
                return 'Rp.' . number_format($row->harga_modal);
            })->add('tgl', function ($row) {
                return date('d-m-Y', strtotime($row->tgl));
            })
            ->toJson(true);
    }

    public function setStatus()
    {
        $id_user = $this->session->get("id");
        $builder = $this->db->table('barang');

        $getData = $builder->where('id', $this->request->getPost('id'))
            ->get()
            ->getRowArray();

        if (!$getData) {
            $response = [
                'status' => false,
                'errors' => 'Data Tidak Ditemukan.'
            ];
        } else {
            $this->barang->update($this->request->getPost('id'), ['status' => ($getData['status']) ? "0" : "1"]);
            if ($getData["status"] == 0) {
                $data = [
                    'id_barang'  => $this->request->getPost('id'),
                    'id_user'    => $id_user,
                    'tgl'        => date("Y-m-d H:i:s"),
                    'keterangan' => 'Status produk diaktfikan'
                ];

                $save = $this->logs->save($data);
            } else {
                $data = [
                    'id_barang'  => $this->request->getPost('id'),
                    'id_user'    => $id_user,
                    'tgl'        => date("Y-m-d H:i:s"),
                    'keterangan' => 'Status produk dinonaktifkan'
                ];

                $save = $this->logs->save($data);
            }
            $response = [
                'status'   => TRUE,
            ];
        }

        echo json_encode($response);
    }

    public function setStokBarang()
    {
        $builder = $this->db->table('barang');
        $id = $this->request->getPost('id');

        $getData = $builder->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$getData) {
            $response = [
                'status' => false,
                'errors' => 'Data Tidak Ditemukan.'
            ];
        } else {
            $this->barang->update($id, ['kelola_stok' => ($getData['kelola_stok']) ? "0" : "1"]);
            $response = [
                'status'   => TRUE,
                'data'     => $this->db->query("SELECT kelola_stok FROM barang WHERE id = '$id'")->getRow(),
                'id'       => $id
            ];
        }

        echo json_encode($response);
    }

    public function setStatusVarian()
    {
        $builder = $this->db->table('varian');

        $getData = $builder->where('id', $this->request->getPost('id'))
            ->get()
            ->getRowArray();

        if (!$getData) {
            $response = [
                'status' => false,
                'errors' => 'Data Tidak Ditemukan.'
            ];
        } else {
            $this->varian->update($this->request->getPost('id'), ['status' => ($getData['status']) ? "0" : "1"]);
            $response = [
                'status'   => TRUE,
            ];
        }

        echo json_encode($response);
    }

    public function setStok()
    {
        $builder = $this->db->table('varian');
        $id = $this->request->getPost('id');

        $getData = $builder->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$getData) {
            $response = [
                'status' => false,
                'errors' => 'Data Tidak Ditemukan.'
            ];
        } else {
            $this->varian->update($id, ['kelola_stok' => ($getData['kelola_stok']) ? "0" : "1"]);
            $response = [
                'status'   => TRUE,
                'data'     => $this->db->query("SELECT kelola_stok FROM varian WHERE id = '$id'")->getRow(),
                'id'       => $id
            ];
        }

        echo json_encode($response);
    }

    public function getStok()
    {
        $id = $this->request->getPost('id');
        $stok = $this->db->query("SELECT * FROM kelola_stok WHERE id_varian = '$id'")->getResult();
        $sale = $this->db->table('detail_penjualan as a')->select('a.qty, b.tgl')
            ->join('penjualan as b', 'b.id = a.id_penjualan')->where('a.id_varian', $id)->where('a.delete <>', 1)->get()->getResult();
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
                    $html .= '<div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
                            <div class="card-body">
                                Pengurangan Stok &nbsp;<span class="text-danger">-' . $key->jumlah . '</span>
                                <br>
                                <small style="font-size: x-small;">' . $date . '</small>
                            </div>
                         </div>';
                }
            }
        } else {
            $html .= '<div class="card mb-2 mt-2" style="background-color: #f2f7ff;" id="nostok">
                            <div class="card-body">
                                <p class="text-center mb-0">Tidak ada riwayat pengaturan stok pada varian ini.</p>
                            </div>
                         </div>';
        }

        if ($sale) {
            foreach ($sale as $key) {
                $date = new DateTime($key->tgl);
                $date = $date->format('d F Y, H:i');
                $html .= '<div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
                            <div class="card-body">
                                Penjualan &nbsp;<span class="text-danger">-' . $key->qty . '</span>
                                <br>
                                <small style="font-size: x-small;">' . $date . '</small>
                            </div>
                         </div>';
            }
        }

        $response = [
            'html'  => $html,
            'data'  => $this->db->query("SELECT id, nama_varian, stok, stok_min FROM varian WHERE id = '$id'")->getRow()
        ];

        echo json_encode($response);
    }

    public function getStokBarang()
    {
        $id = $this->request->getPost('id');
        $stok = $this->db->query("SELECT * FROM stok_barang WHERE id_barang = '$id'")->getResult();
        $sale = $this->db->table('detail_penjualan as a')->select('a.qty, b.tgl')
            ->join('penjualan as b', 'b.id = a.id_penjualan')->where('a.id_barang', $id)->where('a.delete <>', 1)->get()->getResult();
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
                    $html .= '<div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
                            <div class="card-body">
                                Pengurangan Stok &nbsp;<span class="text-danger">-' . $key->jumlah . '</span>
                                <br>
                                <small style="font-size: x-small;">' . $date . '</small>
                            </div>
                         </div>';
                }
            }
        } else {
            $html .= '<div class="card mb-2 mt-2" style="background-color: #f2f7ff;" id="nostok">
                            <div class="card-body">
                                <p class="text-center mb-0">Tidak ada riwayat pengaturan stok pada varian ini.</p>
                            </div>
                         </div>';
        }

        if ($sale) {
            foreach ($sale as $key) {
                $date = new DateTime($key->tgl);
                $date = $date->format('d F Y, H:i');
                $html .= '<div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
                            <div class="card-body">
                                Penjualan &nbsp;<span class="text-danger">-' . $key->qty . '</span>
                                <br>
                                <small style="font-size: x-small;">' . $date . '</small>
                            </div>
                         </div>';
            }
        }

        $response = [
            'html'  => $html,
            'data'  => $this->db->query("SELECT id, nama_barang, stok, stok_min FROM barang WHERE id = '$id'")->getRow()
        ];

        echo json_encode($response);
    }

    public function getSatuan()
    {
        $id_toko = $this->session->get("id_toko");
        $searchTerm = "";
        $data       = [];
        $searchTerm = strtolower($this->request->getVar('q'));
        $builder    = $this->db->table('satuan');
        $query      = $builder
            ->where("LOWER(nama_satuan) like '%" . $searchTerm . "%' ")
            ->where("id_toko", $id_toko)
            ->where("status", 1)
            ->select('id as id, nama_satuan as text')
            ->orderBy('nama_satuan', 'ACS')->get();
        $data = $query->getResult();

        echo json_encode($data);
    }

    public function updateStok()
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
            $id_varian = $this->request->getPost('id_varian');
            $tanggal = date('Y-m-d H:i');
            $tipe = $this->request->getPost('tipe');
            $jumlah = $this->request->getPost('jumlah');

            $data = [
                'id_varian'     => $id_varian,
                'tanggal'       => $tanggal,
                'jumlah'        => $jumlah,
                'tipe'          => $tipe,
            ];

            $save = $this->stok->save($data);

            if ($save) {
                $date = new DateTime($tanggal);
                $date = $date->format('d F Y, H:i');

                // Update Stok varian
                $stok_varian = $this->db->query("SELECT stok FROM varian WHERE id = '$id_varian'")->getRow();
                if ($tipe == 1) {
                    $new_stok = $stok_varian->stok + $jumlah;
                } else {
                    $new_stok = $stok_varian->stok - $jumlah;
                }

                $update = [
                    'id'    => $id_varian,
                    'stok'  => $new_stok
                ];

                $this->varian->save($update);

                $respond = [
                    'status'    => true,
                    'data'      => $data,
                    'date'      => $date,
                    'stok'      => $new_stok
                ];
            } else {
                $respond = [
                    'status'    => false
                ];
            }
        }

        echo json_encode($respond);
    }

    public function updateStokBarang()
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
            $id_barang = $this->request->getPost('id_barang');
            $tanggal = date('Y-m-d H:i');
            $tipe = $this->request->getPost('tipe');
            $jumlah = $this->request->getPost('jumlah');

            $data = [
                'id_barang'     => $id_barang,
                'tanggal'       => $tanggal,
                'jumlah'        => $jumlah,
                'tipe'          => $tipe,
            ];

            $save = $this->stok_barang->save($data);

            if ($save) {
                $date = new DateTime($tanggal);
                $date = $date->format('d F Y, H:i');

                // Update Stok barang
                $stok_barang = $this->db->query("SELECT stok FROM barang WHERE id = '$id_barang'")->getRow();
                if ($tipe == 1) {
                    $new_stok = $stok_barang->stok + $jumlah;
                } else {
                    $new_stok = $stok_barang->stok - $jumlah;
                }

                $update = [
                    'id'    => $id_barang,
                    'stok'  => $new_stok
                ];

                $this->barang->save($update);

                if ($tipe == 1) {
                    $data = [
                        'id_barang'  => $id_barang,
                        'id_user'    => $id_user,
                        'tgl'        => date("Y-m-d H:i:s"),
                        'keterangan' => 'Penambahan stok (+' . $jumlah . '), Stok update: ' . $new_stok . '',
                        'tipe'       => 1
                    ];

                    $save = $this->logs->save($data);
                } else {
                    $data = [
                        'id_barang'  => $id_barang,
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
                    'stok'      => $new_stok
                ];
            } else {
                $respond = [
                    'status'    => false
                ];
            }
        }

        echo json_encode($respond);
    }

    private function validation()
    {
        $stokcheck = $this->request->getPost('stokcheck');

        if ($stokcheck == 'on') {
            $rules = $this->validate([
                'harga' => [
                    'label'  => 'Harga jual',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'kategori' => [
                    'label'  => 'Kategori',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'modal' => [
                    'label'  => 'Harga modal',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'stok' => [
                    'label'  => 'Stok saat ini',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'stokmin' => [
                    'label'  => 'Stok minimum',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'nama' => [
                    'label'  => 'Nama barang',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
            ]);
        } else {
            $rules = $this->validate([
                'harga' => [
                    'label'  => 'Harga jual',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'kategori' => [
                    'label'  => 'Kategori',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'modal' => [
                    'label'  => 'Harga modal',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'nama' => [
                    'label'  => 'Nama barang',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
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
                'nama'       => $this->validation->getError('nama'),
                'kategori'   => $this->validation->getError('kategori'),
                'modal'      => $this->validation->getError('modal'),
                'harga'      => $this->validation->getError('harga'),
                'stok'       => $this->validation->getError('stok'),
                'stokmin'    => $this->validation->getError('stokmin'),
                'deskripsi' => $this->validation->getError('deskripsi'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id_user = $this->session->get("id");

            $id        = $this->request->getPost('id');
            $id_toko   = $this->session->get('id_toko');
            $nama      = $this->request->getPost('nama');
            $harga     = $this->request->getPost('harga');
            $modal     = $this->request->getPost('modal');
            $kategori  = $this->request->getPost('kategori');
            $deskripsi = $this->request->getPost('deskripsi');

            $foto      = $this->request->getFile('foto');

            $stok      = $this->request->getPost('stok');
            $stokmin   = $this->request->getPost('stokmin');

            $stokcheck = $this->request->getPost('stokcheck');
            if ($stokcheck == 'on') {
                $sc = 1;
            } else {
                $sc = 0;
            }

            $id_bahan_barang = $this->request->getPost('id_bahan_barang[]');
            $bahan           = $this->request->getPost('bahan[]');
            $qty             = $this->request->getPost('qty[]');

            $data = [
                'id'              => $id,
                'id_toko'         => $id_toko,
                'id_kategori'     => $kategori,
                'nama_barang'     => $nama,
                'harga_jual'      => getAmount($harga),
                'harga_modal'     => getAmount($modal),
                'kelola_stok'     => $sc,
                'stok'            => $stok,
                'stok_min'        => $stokmin,
                'deskripsi'       => $deskripsi,
            ];

            if (!$id) {
                $data['status']   = 1;
            }

            if ($foto->isValid() && !$foto->hasMoved()) {
                $namafile = $foto->getRandomName();
                $foto->move(ROOTPATH . 'public/assets/img/barang/', $namafile);
                if ($id) {
                    $foto = $this->db->table('barang')->select('foto')->where('id', $id)->get()->getRow();
                    $path = 'assets/img/barang/';
                    $unlink = @unlink($path . $foto->foto);
                }
                $data['foto'] = $namafile;
            }

            $save = $this->barang->save($data);
            if ($id) {
                $id_barang = $id;

                $data = [
                    'id_barang'  => $id_barang,
                    'id_user'    => $id_user,
                    'tgl'        => date("Y-m-d H:i:s"),
                    'keterangan' => 'Perubahan pada produk (Edit)'
                ];

                $save = $this->logs->save($data);
            } else {
                $id_barang = $this->barang->getInsertID();

                $data = [
                    'id_barang'  => $id_barang,
                    'id_user'    => $id_user,
                    'tgl'        => date("Y-m-d H:i:s"),
                    'keterangan' => 'Produk dibuat'
                ];

                $save = $this->logs->save($data);
            }

            if ($bahan) {
                foreach ($bahan as $key => $value) {
                    $data = [
                        'id'            => $id_bahan_barang[$key],
                        'id_barang'     => $id_barang,
                        'id_bahan_baku' => $value,
                        'qty'           => $qty[$key]
                    ];

                    $save = $this->bahan->save($data);
                }
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

    private function validationVarian()
    {
        $stokcheck = $this->request->getPost('stokcheck');

        if ($stokcheck == 'on') {
            $rules = $this->validate([
                'harga' => [
                    'label'  => 'Harga jual',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'modal' => [
                    'label'  => 'Harga modal',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'satuan' => [
                    'label'  => 'Satuan',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'stok' => [
                    'label'  => 'Stok saat ini',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'stokmin' => [
                    'label'  => 'Stok minimum',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'nama' => [
                    'label'  => 'Nama varian',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
            ]);
        } else {
            $rules = $this->validate([
                'harga' => [
                    'label'  => 'Harga jual',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'modal' => [
                    'label'  => 'Harga modal',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'satuan' => [
                    'label'  => 'Satuan',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
                'nama' => [
                    'label'  => 'Nama varian',
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => '{field} harus diisi',
                    ]
                ],
            ]);
        }

        return $rules;
    }

    public function simpanVarian()
    {
        if (!$this->validationVarian()) {
            $errors = [
                'satuan'     => $this->validation->getError('satuan'),
                'nama'       => $this->validation->getError('nama'),
                'harga'      => $this->validation->getError('harga'),
                'modal'      => $this->validation->getError('modal'),
                'stok'       => $this->validation->getError('stok'),
                'stokmin'    => $this->validation->getError('stokmin'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id         = $this->request->getPost('id');
            $id_barang  = $this->request->getPost('id_barang');
            $nama       = $this->request->getPost('nama');
            $harga      = $this->request->getPost('harga');
            $modal      = $this->request->getPost('modal');
            $satuan     = $this->request->getPost('satuan');
            $keterangan = $this->request->getPost('keterangan');

            $stok      = $this->request->getPost('stok');
            $stokmin   = $this->request->getPost('stokmin');

            $stokcheck = $this->request->getPost('stokcheck');
            if ($stokcheck == 'on') {
                $sc = 1;
            } else {
                $sc = 0;
            }

            $data = [
                'id'              => $id,
                'id_barang'       => $id_barang,
                'id_satuan'       => $satuan,
                'nama_varian'     => $nama,
                'harga_jual'      => getAmount($harga),
                'harga_modal'     => getAmount($modal),
                'keterangan'      => $keterangan,
                'kelola_stok'     => $sc,
                'stok'            => $stok,
                'stok_min'        => $stokmin
            ];

            if (!$id) {
                $data['status']   = 1;
            }

            $save = $this->varian->save($data);

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

        $data = $this->db->table('barang')
            ->where('id', $id)
            ->get()->getRow();

        $bahan = $this->db->table("bahan_barang as a")
            ->select("a.id as id_bahan_barang, a.qty, b.*, c.nama_satuan")
            ->join("bahan_baku as b", "b.id = a.id_bahan_baku")->join("satuan as c", "c.id = b.id_satuan")->where("a.id_barang", $id)
            ->get()->getResult();

        $bahanharga = $this->db->query("SELECT SUM(b.harga * a.qty) as harga, SUM(b.biaya * a.qty) as biaya FROM bahan_barang a JOIN bahan_baku b ON a.id_bahan_baku = b.id WHERE a.id_barang = '$id'")->getRow();
        if ($bahan) {
            $totalharga = $data->harga_jual + $bahanharga->harga;
        } else {
            $totalharga = $data->harga_jual;
        }

        $html = '';
        if ($bahan) {
            foreach ($bahan as $key) {
                $html .= '<tr id="bahan' . $key->id . '">
                    <input type="hidden" name="id_bahan_barang[]" value="' . $key->id_bahan_barang . '"></input>
                    <input type="hidden" name="bahan[]" value="' . $key->id . '"></input>
                    <td>' . $key->nama_bahan . '</td>
                    <td>' . $key->nama_satuan . '</td>
                    <td>Rp ' . number_format($key->biaya) . '</td>
                    <td>Rp ' . number_format($key->harga) . '</td>
                    <td>' . $key->stok . '</td>
                    <td>
                        <input type="number" class="form-control qty" name="qty[]" placeholder="Masukkan qty bahan yang digunakan" value="' . $key->qty . '" required></input>
                        <input type="hidden" class="hargab" value="' . $key->harga . '"></input>
                    </td>
                    <td><button type="button" class="btn btn-danger" title="Hapus bahan" onclick="hapusBahan(' . $key->id_bahan_barang .  ',' .  $key->id . ')"><i class="fas fa-trash"></i></button></td>
                </tr>';
            }
        }

        if ($data) {
            $response = [
                'status'       => TRUE,
                'data'         => $data,
                'totalharga'   => $totalharga,
                'harga'        => 'Rp. ' . number_format($data->harga_jual, 0, ',', '.'),
                'modal'        => 'Rp. ' . number_format($data->harga_modal, 0, ',', '.'),
                'html'         => $html
            ];
        } else {
            $response = [
                'status' => false,
            ];
        }

        echo json_encode($response);
    }

    public function getBahan()
    {
        $id_toko    = $this->session->get('id_toko');
        $edit = $this->request->getPost("edit");
        $bahan      = $this->request->getPost('bahan');

        $searchTerm = "";
        $data       = [];
        $searchTerm = strtolower($this->request->getVar('q'));
        $builder    = $this->db->table('bahan_baku');
        if ($edit == 1 && $bahan != null) {
            $builder->whereNotIn("id", $bahan);
        }
        $query      = $builder
            ->where("LOWER(nama_bahan) like '%" . $searchTerm . "%' ")
            ->where("id_toko", $id_toko)
            ->select('id as id, nama_bahan as text')
            ->orderBy('nama_bahan', 'ACS')->orderBy('nama_bahan', 'ASC')->get();
        $data = $query->getResult();

        echo json_encode($data);
    }

    public function getDataBahan()
    {
        $id = $this->request->getPost('id');
        $id = end($id);
        $bahan = $this->db->query("SELECT a.*, b.nama_satuan FROM bahan_baku a JOIN satuan b ON a.id_satuan = b.id WHERE a.id = '$id'")->getRow();
        $html = '<tr id="bahan' . $bahan->id . '">
                    <input type="hidden" name="id_bahan_barang[]" value=""></input>
                    <input type="hidden" name="bahan[]" value="' . $bahan->id . '"></input>
                    <td>' . $bahan->nama_bahan . '</td>
                    <td>' . $bahan->nama_satuan . '</td>
                    <td>Rp ' . number_format($bahan->biaya) . '</td>
                    <td>Rp ' . number_format($bahan->harga) . '</td>
                    <td>' . $bahan->stok . '</td>
                    <td>
                        <input type="number" class="form-control qty" name="qty[]" placeholder="Masukkan qty bahan yang digunakan" required></input>
                        <input type="hidden" class="hargab" value="' . $bahan->harga . '"></input>
                    </td>
                    <td>-</td>
                </tr>';

        $respond = [
            'html'  => $html
        ];

        echo json_encode($respond);
    }

    public function getdataVarian()
    {
        $id = $this->request->getPost('id');

        $data = $this->db->table('varian as a')
            ->select('a.*, b.nama_satuan')
            ->join('satuan as b', 'b.id = a.id_satuan')
            ->where('a.id', $id)
            ->get()->getRow();

        if ($data) {
            $response = [
                'status'  => TRUE,
                'data'    => $data,
                'harga'   => 'Rp. ' . number_format($data->harga_jual, 0, ',', '.'),
                'modal'   => 'Rp. ' . number_format($data->harga_modal, 0, ',', '.')
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
        $foto = $this->request->getPost('foto');

        try {
            $delete = $this->barang->delete($id);
            if ($delete) {
                $path = 'assets/img/barang/';
                $unlink = @unlink($path . $foto);
            }

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

    public function hapusVarian()
    {
        $id = $this->request->getPost('id');

        try {
            $this->varian->delete($id);
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

    public function hapusBahan()
    {
        $id = $this->request->getPost('id');

        try {
            $this->bahan->delete($id);
            return $this->response->setJSON(['status' => true, 'id' => $id]);
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
