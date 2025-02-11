<?php

namespace Modul\Kredit\Controllers;

use App\Controllers\BaseController;
use DateTime;
use Hermawan\DataTables\DataTable;
use Modul\Kredit\Models\Model_bayar_kredit;
use Modul\Kredit\Models\Model_detail_kredit;
use Modul\Kredit\Models\Model_kredit;
use Modul\Kredit\Models\Model_pembayaran_kredit;

class Kredit extends BaseController
{
    public function __construct()
    {
        $this->kredit      = new Model_kredit();
        $this->detail      = new Model_detail_kredit();
        $this->bayar       = new Model_bayar_kredit();
        $this->pembayaran  = new Model_pembayaran_kredit();
    }

    public function index()
    {
        $id_toko = $this->session->get('id_toko');

        $pelanggan = $this->db->query("SELECT * FROM pelanggan WHERE id_toko = '$id_toko'")->getResult();

        $data_page = [
            'menu'      => 'utang',
            'submenu'   => 'kredit',
            'title'     => 'Data Kredit',
            'pelanggan' => $pelanggan,
        ];

        return view('Modul\Kredit\Views\viewKredit', $data_page);
    }

    public function add()
    {
        $id_toko = $this->session->get('id_toko');

        $pelanggan = $this->db->query("SELECT * FROM pelanggan WHERE id_toko = '$id_toko'")->getResult();
        $totalb = $this->db->query("SELECT COUNT(id) as total FROM barang WHERE id_toko = '$id_toko'")->getRow()->total;

        $data_page = [
            'menu'      => 'utang',
            'submenu'   => 'kredit',
            'title'     => 'Add Kredit',
            'pelanggan' => $pelanggan,
            'totalb'    => $totalb
        ];

        return view('Modul\Kredit\Views\viewAdd', $data_page);
    }

    public function edit($id)
    {
        $id = base64_decode($id);
        $data = $this->db->query("SELECT a.*, b.nama as pelanggan FROM kredit a JOIN pelanggan b ON a.id_pelanggan = b.id WHERE a.id = '$id'")->getRow();
        $detail = $this->db->query("SELECT a.*, b.nama_barang FROM detail_kredit a JOIN barang b ON b.id = a.id_barang WHERE a.id_kredit = '$id'")->getResult();

        $id_toko = $this->session->get('id_toko');

        $pelanggan = $this->db->query("SELECT * FROM pelanggan WHERE id_toko = '$id_toko'")->getResult();
        $totalb = $this->db->query("SELECT COUNT(id) as total FROM barang WHERE id_toko = '$id_toko'")->getRow()->total;

        $data_page = [
            'menu'      => 'utang',
            'submenu'   => 'kredit',
            'title'     => 'Edit Kredit',
            'pelanggan' => $pelanggan,
            'totalb'    => $totalb,
            'data'      => $data,
            'detail'    => $detail
        ];

        return view('Modul\Kredit\Views\viewEdit', $data_page);
    }

    public function datatable()
    {
        $id_toko = $this->session->get('id_toko');

        $builder = $this->db->table('kredit as a')
            ->select('a.id as id, a.tgl_kredit as tgl_kredit, a.subtotal_barang as subtotal_barang, a.total_kredit as total_kredit, a.dp as dp, a.periode as periode, a.status as status, b.nama as pelanggan')
            ->join('pelanggan as b', 'b.id = a.id_pelanggan')
            ->where('a.id_toko', $id_toko)
            ->orderBy('a.id', 'DESC');

        return DataTable::of($builder)
            ->filter(function ($builder, $request) {
                $pelanggan = $request->pelanggan;
                $status = $request->status;
                $tgl = $request->tgl;

                if ($pelanggan != "") {
                    $builder->where('a.id_pelanggan', $pelanggan);
                }
                if ($status != "") {
                    $builder->where('a.status', $status);
                }
                if ($tgl != "") {
                    $builder->where('a.tgl_kredit', $tgl);
                }
            })
            ->addNumbering('no')
            ->setSearchableColumns(['LOWER(b.nama)'])
            ->add('total', function ($row) {
                return 'Rp. ' . number_format($row->total_kredit);
            })->add('subtotal', function ($row) {
                return 'Rp. ' . number_format($row->subtotal_barang);
            })->add('dp', function ($row) {
                return 'Rp. ' . number_format($row->dp);
            })
            ->add('action', function ($row) {
                $bayar = $this->db->query("SELECT SUM(a.nominal) as total FROM bayar_kredit a JOIN pembayaran_kredit b ON a.id_pembayaran_kredit = b.id WHERE b.id_kredit = '$row->id'")->getRow();
                $sisa  = $row->total_kredit - $bayar->total - $row->dp;
                if ($sisa < 1) {
                    $sisa = 0;
                }
                return '<button type="button" class="btn btn-light" title="Priode Bayar Kredit" onclick="jadwal_bayar(\'' . $row->id . '\', \'' . $row->pelanggan . '\', \'' . $row->status . '\', \'Rp. ' . number_format($bayar->total + $row->dp) . '\', \'Rp. ' . number_format($sisa) . '\')"><i class="fas fa-calendar-alt"></i></button>
                <button type="button" class="btn btn-light" title="Priode Bayar Kredit" onclick="bayar(\'' . $row->id . '\', \'' . $row->pelanggan . '\', \'' . $row->status . '\', \'Rp. ' . number_format($bayar->total + $row->dp) . '\', \'Rp. ' . number_format($sisa) . '\')"><i class="fas fa-hand-holding-usd"></i></button>
                <a href="/kredit/edit/' . base64_encode($row->id) . '" class="btn btn-light" title="Edit Data"><i class="fa fa-edit"></i></a>
                <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->pelanggan . '\')"><i class="fa fa-trash"></i></button>';
            })->add('status', function ($row) {
                if ($row->status == 1) {
                    return '<span class="badge bg-success">Lunas</span>';
                } else {
                    return '<span class="badge bg-warning text-dark">Belum lunas</span>';
                }
            })->add('periode', function ($row) {
                return $row->periode . ' Bulan';
            })
            ->toJson(true);
    }

    public function datatable_jadwal()
    {
        $builder = $this->db->table('pembayaran_kredit')->orderBy('jt', 'ASC');

        return DataTable::of($builder)
            ->filter(function ($builder, $request) {
                $kredit = $request->kredit;
                $builder->where('id_kredit', $kredit);
            })
            ->addNumbering('no')
            ->add('nominal', function ($row) {
                return 'Rp. ' . number_format($row->nominal);
            })->add('status', function ($row) {
                if ($row->status == 0) {
                    return '<span class="badge bg-danger">belum lunas</span>';
                } else {
                    return '<span class="badge bg-success">lunas</span>';
                }
            })->add('sudah_byr', function ($row) {
                $bayar = $this->db->query("SELECT SUM(nominal) as total FROM bayar_kredit WHERE id_pembayaran_kredit = '$row->id'")->getRow()->total;
                return 'Rp. ' . number_format($bayar);
            })->add('sisa', function ($row) {
                $bayar = $this->db->query("SELECT SUM(nominal) as total FROM bayar_kredit WHERE id_pembayaran_kredit = '$row->id'")->getRow()->total;
                $sisa = $row->nominal - $bayar;
                return 'Rp. ' . number_format($sisa);
            })
            ->toJson(true);
    }

    public function datatable_byr()
    {
        $builder = $this->db->table('bayar_kredit as a')
            ->select('a.id as id, a.id_pembayaran_kredit as id_pembayaran_kredit, a.tgl_bayar as tgl_bayar, a.nominal as nominal, a.foto as foto, b.jt as periode, b.id_kredit as id_kredit')
            ->join('pembayaran_kredit as b', 'a.id_pembayaran_kredit = b.id')->orderBy('b.jt', 'ASC');

        return DataTable::of($builder)
            ->filter(function ($builder, $request) {
                $kredit = $request->kredit;
                $builder->where('id_kredit', $kredit);
            })
            ->addNumbering('no')
            ->add('nominal', function ($row) {
                return 'Rp. ' . number_format($row->nominal);
            })->add('action', function ($row) {
                return '<button type="button" class="btn btn-light" title="Hapus Data" onclick="batal_bayar(\'' . $row->id . '\', \'' . $row->id_kredit . '\', \'' . $row->id_pembayaran_kredit . '\', \'' . $row->foto . '\')"><i class="fa fa-trash"></i></button>';
            })->add('foto', function ($row) {
                if ($row->foto) {
                    return '<a href="/assets/img/kredit/' . $row->foto . '"><image src="/assets/img/kredit/' . $row->foto . '" height="70" style="cursor: zoom-in; border-radius: 5px;"/><a/>';
                } else {
                    return '<image src="/assets/img/noimage.png" height="70" style="cursor: zoom-in;"/>';
                }
            })
            ->toJson(true);
    }

    public function getPeriode()
    {
        $id_kredit = $this->request->getPost("id_kredit");
        $searchTerm = "";
        $data       = [];
        $searchTerm = strtolower($this->request->getVar('q'));
        $builder    = $this->db->table('pembayaran_kredit');
        $query      = $builder
            ->where("LOWER(jt) like '%" . $searchTerm . "%' ")
            ->where("id_kredit", $id_kredit)
            ->where("status", 0)
            ->select('id as id, jt as text')
            ->orderBy('jt', 'ACS')->get();
        $data = $query->getResult();

        echo json_encode($data);
    }

    private function validation_bayar()
    {
        $rules = $this->validate([
            'periode' => [
                'label'  => 'Periode pembayaran',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus dipilih!',
                ]
            ],
            'nominal' => [
                'label'  => 'Nominal',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'foto'  => [
                'label' => 'Foto',
                'rules' => 'max_size[foto, 1024]|ext_in[foto,jpg,png,jpeg]',
                'errors' => [
                    'max_size' => 'Ukuran {field} terlalu besar!',
                    'ext_in'   => '{field} harus JPG,PNG atau JEPG!',
                ]
            ],
        ]);

        return $rules;
    }

    public function bayar()
    {
        if (!$this->validation_bayar()) {
            $errors = [
                'periode'   => $this->validation->getError('periode'),
                'nominal'   => $this->validation->getError('nominal'),
                'foto'      => $this->validation->getError('foto'),
            ];

            $response = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id        = $this->request->getPost('id');
            $id_kredit = $this->request->getPost('id_kredit');
            $periode   = $this->request->getPost('periode');
            $nominal   = $this->request->getPost('nominal');
            $foto      = $this->request->getFile('foto');

            $kredit    = $this->db->query("SELECT total_kredit FROM kredit WHERE id = '$id_kredit'")->getRow();
            $bayar     = $this->db->query("SELECT SUM(nominal) as total FROM bayar_kredit WHERE id_pembayaran_kredit = '$periode'")->getRow();
            $sisa      = $kredit->total_kredit - $bayar->total;

            if (getAmount($nominal) > $sisa) {
                $response = [
                    'status_cek'    => false,
                    'sisa'          => 'Rp. ' . number_format($sisa)
                ];
            } else {
                $kredit = $this->db->query("SELECT id, total_kredit, dp, status FROM kredit WHERE id = '$id_kredit'")->getRow();
                $byr = $this->db->query("SELECT SUM(nominal) as total FROM pembayaran_kredit WHERE id_kredit = '$id_kredit'")->getRow();
                $sdhbyr = $this->db->query("SELECT SUM(a.nominal) as total FROM bayar_kredit a JOIN pembayaran_kredit b ON a.id_pembayaran_kredit = b.id WHERE b.id_kredit = '$id_kredit'")->getRow();
                $sdhbyr = $sdhbyr->total + getAmount($nominal) + $kredit->dp;

                $data = [
                    'id'                   => $id,
                    'id_pembayaran_kredit' => $periode,
                    'tgl_bayar'            => date('Y-m-d'),
                    'nominal'              => getAmount($nominal)
                ];

                if ($foto->isValid() && !$foto->hasMoved()) {
                    $namafile = $foto->getRandomName();
                    $foto->move(ROOTPATH . 'public/assets/img/kredit/', $namafile);
                    $data['foto'] = $namafile;
                }

                $save = $this->bayar->save($data);

                if ($kredit->total_kredit <= $sdhbyr) {
                    $data = [
                        'id'     => $id_kredit,
                        'status' => 1
                    ];

                    $save = $this->kredit->save($data);

                    $data = [
                        'status'    => 1
                    ];
                    $builder = $this->db->table('pembayaran_kredit')->where('id_kredit', $id_kredit)->update($data);
                }

                $byr_periode = $this->db->query("SELECT nominal FROM pembayaran_kredit WHERE id = '$periode'")->getRow();
                if ($byr_periode->nominal <= $sdhbyr) {
                    $data = [
                        'id'     => $periode,
                        'status' => 1
                    ];

                    $save = $this->pembayaran->save($data);
                }

                if ($save) {
                    $bayar = $this->db->query("SELECT SUM(a.nominal) as total FROM bayar_kredit a JOIN pembayaran_kredit b ON a.id_pembayaran_kredit = b.id WHERE b.id_kredit = '$id_kredit'")->getRow();
                    $sisa  = $kredit->total_kredit - $kredit->dp - $bayar->total;
                    if ($sisa < 1) {
                        $sisa = 0;
                    }
                    $response = [
                        'status'    => true,
                        'bayar'     => 'Rp. ' . number_format($bayar->total + $kredit->dp),
                        'sisa'      => 'Rp. ' . number_format($sisa),
                        'data'      => $kredit
                    ];
                } else {
                    $response = [
                        'status'    => false
                    ];
                }
            }
        }

        echo json_encode($response);
    }

    public function batal_bayar()
    {
        $id = $this->request->getPost('id');
        $ipk = $this->request->getPost('ipk');
        $id_kredit = $this->request->getPost('id_kredit');
        $foto = $this->request->getPost('foto');
        $path = 'assets/img/kredit/';
        $unlink = @unlink($path . $foto);

        $delete = $this->bayar->delete($id);

        $data = [
            'id'     => $id_kredit,
            'status' => 0
        ];

        $save = $this->kredit->save($data);

        $data = [
            'id'     => $ipk,
            'status' => 0
        ];

        $save = $this->pembayaran->save($data);

        if ($delete && $save) {
            $kredit = $this->db->query("SELECT id, total_kredit, status FROM kredit WHERE id = '$id_kredit'")->getRow();
            $bayar = $this->db->query("SELECT SUM(a.nominal) as total FROM bayar_kredit a JOIN pembayaran_kredit b ON a.id_pembayaran_kredit = b.id WHERE b.id_kredit = '$id_kredit'")->getRow();
            $sisa  = $kredit->total_kredit - $bayar->total;
            if ($sisa < 1) {
                $sisa = 0;
            }
            $response = [
                'status'    => true,
                'bayar'     => 'Rp. ' . number_format($bayar->total),
                'sisa'      => 'Rp. ' . number_format($sisa),
                'data'      => $kredit
            ];
        } else {
            $response = [
                'status'    => false
            ];
        }

        echo json_encode($response);
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

    public function getBarang()
    {
        $id_toko    = $this->session->get('id_toko');
        $b = $this->request->getPost('b');
        $searchTerm = "";
        $data       = [];
        $searchTerm = strtolower($this->request->getVar('q'));
        $builder    = $this->db->table('barang');
        $query      = $builder
            ->where("LOWER(nama_barang) like '%" . $searchTerm . "%' ")
            ->where("id_toko", $id_toko)
            ->whereNotIn('id', $b)
            ->select('id as id, nama_barang as text')
            ->orderBy('nama_barang', 'ACS')->get();
        $data = $query->getResult();

        echo json_encode($data);
    }

    public function getHarga()
    {
        $id = $this->request->getPost('id');
        $no = $this->request->getPost('no');
        $harga = $this->db->query("SELECT harga_jual FROM barang WHERE id = '$id'")->getRow();

        $response = [
            'status'    => true,
            'hargarp'   => 'Rp. ' . number_format($harga->harga_jual),
            'harga'     => $harga->harga_jual,
            'no'        => $no
        ];

        echo json_encode($response);
    }

    private function validation()
    {
        $rules = $this->validate([
            'pelanggan' => [
                'label'  => 'Pelanggan',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'periode' => [
                'label'  => 'Periode',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'tgl' => [
                'label'  => 'Tanggal kredit',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'subtotal' => [
                'label'  => 'Subtotal',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'total' => [
                'label'  => 'Total Kredit',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
        ]);

        return $rules;
    }

    public function simpan()
    {
        if (!$this->validation()) {
            $errors = [
                'pelanggan' => $this->validation->getError('pelanggan'),
                'periode'   => $this->validation->getError('periode'),
                'tgl'       => $this->validation->getError('tgl'),
                'subtotal'  => $this->validation->getError('subtotal'),
                'total'     => $this->validation->getError('total'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $validasi = $this->request->getPost('validasi');
            if ($validasi != '0') {
                $respond = [
                    'status' => FALSE
                ];
            } else {
                $id             = $this->request->getPost('id');
                $id_toko        = $this->session->get('id_toko');
                $pelanggan      = $this->request->getPost('pelanggan');
                $tgl            = $this->request->getPost('tgl');
                $subtotal       = getAmount($this->request->getPost('subtotal'));
                $total          = getAmount($this->request->getPost('total'));
                $dp             = getAmount($this->request->getPost('dp'));
                $periode        = $this->request->getPost('periode');
                $periodee       = $this->request->getPost('periodee');

                $id_detail      = $this->request->getPost('id_detail[]');
                $barang         = $this->request->getPost('barang[]');
                $harga          = $this->request->getPost('harga[]');
                $qty            = $this->request->getPost('qty[]');

                $data = [
                    'id'              => $id,
                    'id_toko'         => $id_toko,
                    'id_pelanggan'    => $pelanggan,
                    'tgl_kredit'      => $tgl,
                    'subtotal_barang' => $subtotal,
                    'total_kredit'    => $total,
                    'dp'              => $dp,
                    'periode'         => $periode,
                    'status'          => 0
                ];

                $save = $this->kredit->save($data);
                if ($id) {
                    $id_kredit = $id;
                } else {
                    $id_kredit = $this->kredit->getInsertID();
                }

                // Save Detail Kredit
                foreach ($barang as $key => $value) {
                    if (isset($id_detail[$key])) {
                        $data = [
                            'id'           => $id_detail[$key],
                            'id_kredit'    => $id_kredit,
                            'id_barang'    => $value,
                            'harga'        => $harga[$key],
                            'qty'          => $qty[$key]
                        ];
                    } else {
                        $data = [
                            'id_kredit'    => $id_kredit,
                            'id_barang'    => $value,
                            'harga'        => $harga[$key],
                            'qty'          => $qty[$key]
                        ];
                    }

                    $save = $this->detail->save($data);
                }

                if ($dp != 0) {
                    $total = $total - $dp;
                }

                // Save Bayar Kredit
                if (!$id) {
                    $x = 1;
                    while ($x <= $periode) {
                        $currentDate = new DateTime($tgl);
                        $dueDate = $currentDate->modify('+' . $x . ' month');

                        $dueDate->setDate($dueDate->format('Y'), $dueDate->format('m'), $dueDate->format('d'));

                        $data = [
                            'id_kredit'     => $id_kredit,
                            'jt'            => $dueDate->format('Y-m-d'),
                            'nominal'       => $total / $periode,
                            'status'        => 0
                        ];

                        $save = $this->pembayaran->save($data);
                        $x++;
                    }
                } elseif ($periode != $periodee) {
                    $pembayaran = $this->db->table("pembayaran_kredit")->select("id")->where('id_kredit', $id)->get()->getResult();
                    foreach ($pembayaran as $key) {
                        $deletebyr = $this->db->table("bayar_kredit")->where("id_pembayaran_kredit", $key->id)->delete();
                    }
                    $bayar = $this->db->table("pembayaran_kredit")->where('id_kredit', $id)->delete();
                    $x = 1;
                    while ($x <= $periode) {
                        $currentDate = new DateTime($tgl);
                        $dueDate = $currentDate->modify('+' . $x . ' month');

                        $dueDate->setDate($dueDate->format('Y'), $dueDate->format('m'), $dueDate->format('d'));

                        $data = [
                            'id_kredit'     => $id_kredit,
                            'jt'            => $dueDate->format('Y-m-d'),
                            'nominal'       => $total / $periode,
                            'status'        => 0
                        ];

                        $save = $this->pembayaran->save($data);
                        $x++;
                    }
                } elseif ($id) {
                    $data = [
                        'nominal'       => $total / $periode,
                        'status'        => 0
                    ];

                    $save = $this->db->table("pembayaran_kredit")->where("id_kredit", $id)->update($data);
                }

                if ($save) {
                    $respond = [
                        'status' => TRUE,
                        'notif'  => 'Data berhasil diupdate'
                    ];
                } else {
                    $respond = [
                        'status' => FALSE
                    ];
                }
            }
        }
        echo json_encode($respond);
    }

    public function hapus()
    {
        $id   = $this->request->getPost('id');
        $detail = $this->db->table('detail_kredit')->where('id_kredit', $id)->delete();
        $pembayaran = $this->db->table('pembayaran_kredit')->select("id")->where('id_kredit', $id)->get()->getResult();
        foreach ($pembayaran as $key) {
            $bayar = $this->db->table("bayar_kredit")->where("id_pembayaran_kredit", $key->id)->get()->getResult();
            foreach ($bayar as $kuy) {
                $path = 'assets/img/kredit/';
                $unlink = @unlink($path . $kuy->foto);
                $this->bayar->delete($kuy->id);
            }
        }
        $pembayaran = $this->db->table('pembayaran_kredit')->where('id_kredit', $id)->delete();

        if ($this->kredit->delete($id)) {
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
