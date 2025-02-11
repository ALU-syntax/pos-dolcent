<?php

namespace Modul\Piutang\Controllers;

use App\Controllers\BaseController;
use Hermawan\DataTables\DataTable;
use Modul\Piutang\Models\Model_bayarp;
use Modul\Piutang\Models\Model_piutang;

class Piutang extends BaseController
{
    public function __construct()
    {
        $this->piutang = new Model_piutang();
        $this->bayar = new Model_bayarp();
    }

    public function index()
    {
        $id_toko = $this->session->get('id_toko');
        $pelanggan = $this->db->query("SELECT * FROM pelanggan WHERE id_toko = '$id_toko' ORDER BY nama ASC")->getResult();

        $data_page = [
            'menu'      => 'pencatatan',
            'submenu'   => 'piutang',
            'title'     => 'Data Piutang',
            'pelanggan' => $pelanggan
        ];

        return view('Modul\Piutang\Views\viewPiutang', $data_page);
    }

    public function datatable()
    {
        $id_toko = $this->session->get('id_toko');

        $builder = $this->db->table('piutang as a')
            ->select('a.id as id, a.jumlah as jumlah, a.catatan as catatan, a.tgl as tgl, a.jt as jt, a.foto as foto, a.status as status, a.pelanggan as pelanggan, b.nama as nama_pelanggan')
            ->join('pelanggan as b', 'b.id = a.id_pelanggan', 'left')
            ->where('a.id_toko', $id_toko)->orderBy('a.id', 'DESC');

        return DataTable::of($builder)
            ->addNumbering('no')
            ->setSearchableColumns(['LOWER(b.nama)', 'LOWER(a.catatan)', 'LOWER(a.pelanggan)'])
            ->add('action', function ($row) {
                $bayar = $this->db->query("SELECT SUM(jumlah) as total FROM bayar_piutang WHERE id_piutang = '$row->id'")->getRow();
                $sisa  = $row->jumlah - $bayar->total;
                return '<button type="button" class="btn btn-light" title="Bayar Piutang" onclick="bayar(\'' . $row->id . '\', \'' . $row->pelanggan . '\', \'' . $row->status . '\', \'Rp. ' . number_format($bayar->total) . '\', \'Rp. ' . number_format($sisa) . '\')"><i class="fas fa-hand-holding-usd"></i></button>
                <button type="button" class="btn btn-light" title="Edit Data" onclick="edit(\'' . $row->id . '\')"><i class="fa fa-edit"></i></button>
                <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->foto . '\', \'' . $row->catatan . '\')"><i class="fa fa-trash"></i></button>';
            })->add('jumlah', function ($row) {
                if ($row->status == 0) {
                    $status = '<span class="badge bg-light-warning" style="font-size: 9px;">Belum lunas</span>';
                } elseif ($row->status == 1) {
                    $status = '<span class="badge bg-light-success" style="font-size: 9px;">Lunas</span>';
                }
                return 'Rp. ' . number_format($row->jumlah) . '<br>' . $status;
            })->add('foto', function ($row) {
                if ($row->foto) {
                    return '<image data-fancybox data-src="/assets/img/piutang/' . $row->foto . '" src="/assets/img/piutang/' . $row->foto . '" height="70" style="cursor: zoom-in; border-radius: 5px;"/>';
                } else {
                    return '<image src="/assets/img/noimage.png" height="70" style="cursor: zoom-in;"/>';
                }
            })->add('pelanggan', function ($row) {
                if ($row->nama_pelanggan) {
                    return $row->nama_pelanggan;
                } else {
                    return $row->pelanggan;
                }
            })
            ->toJson(true);
    }

    private function validation()
    {
        $rules = $this->validate([
            'pelanggan' => [
                'label'  => 'Pelanggan',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi',
                ]
            ],
            'jumlah' => [
                'label'  => 'Jumlah',
                'rules'  => 'required',
                'errors' => [
                    'required'     => '{field} harus diisi',
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
            'catatan' => [
                'label'  => 'Catatan',
                'rules'  => 'required',
                'errors' => [
                    'required'     => '{field} harus diisi',
                ]
            ],
            'tgl' => [
                'label'  => 'Tanggal',
                'rules'  => 'required',
                'errors' => [
                    'required'     => '{field} harus diisi',
                ]
            ],
            'jt' => [
                'label'  => 'Jatuh Tempo',
                'rules'  => 'required',
                'errors' => [
                    'required'     => '{field} harus diisi',
                ]
            ],
        ]);

        return $rules;
    }

    public function simpan()
    {
        if (!$this->validation()) {
            $errors = [
                'pelanggan'  => $this->validation->getError('pelanggan'),
                'jumlah'     => $this->validation->getError('jumlah'),
                'foto'       => $this->validation->getError('foto'),
                'catatan'    => $this->validation->getError('catatan'),
                'tgl'        => $this->validation->getError('tgl'),
                'jt'         => $this->validation->getError('jt'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id        = $this->request->getPost('id');
            $id_toko   = $this->session->get('id_toko');
            $id_pelanggan = $this->request->getPost('pelanggan');
            $jumlah    = $this->request->getPost('jumlah');
            $catatan   = $this->request->getPost('catatan');
            $tgl       = $this->request->getPost('tgl');
            $jt        = $this->request->getPost('jt');
            $status    = $this->request->getPost('status');

            $foto      = $this->request->getFile('foto');

            $pelanggan = $this->db->query("SELECT nama FROM pelanggan WHERE id = '$id_pelanggan'")->getRow();

            $data = [
                'id'                    => $id,
                'id_toko'               => $id_toko,
                'id_pelanggan'          => $id_pelanggan,
                'pelanggan'             => $pelanggan->nama,
                'jumlah'                => getAmount($jumlah),
                'tgl'                   => $tgl,
                'jt'                    => $jt,
                'catatan'               => $catatan,
                'status'                => $status,
            ];

            if ($foto->isValid() && !$foto->hasMoved()) {
                $namafile = $foto->getRandomName();
                $foto->move(ROOTPATH . 'public/assets/img/piutang/', $namafile);
                if ($id) {
                    $foto = $this->db->table('piutang')->select('foto')->where('id', $id)->get()->getRow();
                    $path = 'assets/img/piutang/';
                    $unlink = @unlink($path . $foto->foto);
                }
                $data['foto'] = $namafile;
            }

            $save = $this->piutang->save($data);

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

        $data = $this->db->table('piutang')
            ->where('id', $id)
            ->get()->getRow();

        if ($data) {
            $response = [
                'status' => TRUE,
                'data'   => $data
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
        $id   = $this->request->getPost('id');

        // Delete foto & data bayar utang
        $fotoBayar = $this->db->query("SELECT foto FROM bayar_piutang WHERE id_piutang = '$id'")->getResult();
        foreach ($fotoBayar as $key) {
            $path = 'assets/img/piutang/';
            $unlink = @unlink($path . $key->foto);
        }
        $builder = $this->db->table('bayar_piutang')->where('id_piutang', $id);
        $builder->delete();

        $foto = $this->request->getPost('foto');

        // Delete data utang
        $path = 'assets/img/piutang/';
        $unlink = @unlink($path . $foto);

        if ($this->piutang->delete($id)) {
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

    // Bayar Utang

    public function datatable_byr()
    {
        $builder = $this->db->table('bayar_piutang')->orderBy('id', 'DESC');

        return DataTable::of($builder)
            ->filter(function ($builder, $request) {
                $piutang = $request->piutang;
                $builder->where('id_piutang', $piutang);
            })
            ->addNumbering('no')
            ->add('action', function ($row) {
                return '<button type="button" class="btn btn-light" title="Hapus Data" onclick="hapusBayar(\'' . $row->id . '\', \'' . $row->jumlah . '\', \'' . $row->foto . '\', \'' . $row->id_piutang . '\')"><i class="fa fa-trash"></i></button>';
            })->add('jumlah', function ($row) {
                return 'Rp. ' . number_format($row->jumlah);
            })->add('foto', function ($row) {
                if ($row->foto) {
                    return '<a target="_blank" href="/assets/img/piutang/' . $row->foto . '"><image src="/assets/img/piutang/' . $row->foto . '" height="70" style="cursor: zoom-in; border-radius: 5px;"/></a>';
                } else {
                    return '<image src="/assets/img/noimage.png" height="70"/>';
                }
            })
            ->toJson(true);
    }

    public function updateBayar()
    {
        $rules = $this->validate([
            'jmlbyr' => [
                'label'  => 'Jumlah',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi',
                ]
            ],
            'tglbyr' => [
                'label'  => 'Tanggal',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi',
                ]
            ],
            'fotobyr'  => [
                'label' => 'Foto',
                'rules' => 'max_size[fotobyr, 1024]|ext_in[fotobyr,jpg,png,jpeg]',
                'errors' => [
                    'max_size' => 'Ukuran {field} terlalu besar!',
                    'ext_in'   => '{field} harus JPG,PNG atau JEPG!',
                ]
            ],
        ]);

        if (!$rules) {
            $errors = [
                'jmlbyr'    => $this->validation->getError('jmlbyr'),
                'tglbyr'    => $this->validation->getError('tglbyr'),
                'fotobyr'   => $this->validation->getError('fotobyr'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id_piutang = $this->request->getPost('id_piutang');
            $jml        = $this->request->getPost('jmlbyr');
            $tgl        = $this->request->getPost('tglbyr');
            $foto       = $this->request->getFile('fotobyr');

            // Update Status Piutang
            $piutang = $this->db->query("SELECT jumlah FROM piutang WHERE id = '$id_piutang'")->getRow();
            $bayar = $this->db->query("SELECT SUM(jumlah) as total FROM bayar_piutang WHERE id_piutang = '$id_piutang'")->getRow();
            $totalbyr = getAmount($jml) + $bayar->total;

            if ($totalbyr <= $piutang->jumlah) {
                if ($piutang->jumlah == $totalbyr) {
                    $data = [
                        'id'     => $id_piutang,
                        'status' => 1
                    ];

                    $update = $this->piutang->save($data);
                }

                // Save pembayaran
                $data = [
                    'id_piutang'  => $id_piutang,
                    'jumlah'    => getAmount($jml),
                    'tgl_bayar' => $tgl,
                ];

                if ($foto->isValid() && !$foto->hasMoved()) {
                    $namafile = $foto->getRandomName();
                    $foto->move(ROOTPATH . 'public/assets/img/piutang/', $namafile);
                    $data['foto'] = $namafile;
                }

                $save = $this->bayar->save($data);

                if ($save) {
                    $sudahbyr = $this->db->query("SELECT SUM(jumlah) as total FROM bayar_piutang WHERE id_piutang = '$id_piutang'")->getRow();
                    $sisa     = $piutang->jumlah - $sudahbyr->total;

                    $respond = [
                        'status' => TRUE,
                        'sudahbyr' => 'Rp. ' . number_format($sudahbyr->total),
                        'sisa'     => 'Rp. ' . number_format($sisa)
                    ];
                    if (isset($update)) {
                        $respond['lunas'] = true;
                    }
                } else {
                    $respond = [
                        'status' => FALSE
                    ];
                }
            } else {
                $respond = [
                    'status_jml'    => false
                ];
            }
        }

        echo json_encode($respond);
    }

    public function hapusBayar()
    {
        $id   = $this->request->getPost('id');
        $id_piutang = $this->request->getPost('id_piutang');
        $jumlah = $this->request->getPost('jumlah');
        $foto = $this->request->getPost('foto');

        $path = 'assets/img/piutang/';
        $unlink = @unlink($path . $foto);

        if ($this->bayar->delete($id)) {
            $data = [
                'id'     => $id_piutang,
                'status' => 0
            ];
            $this->piutang->save($data);

            $piutang    = $this->db->query("SELECT jumlah FROM piutang WHERE id = '$id_piutang'")->getRow();
            $sudahbyr   = $this->db->query("SELECT SUM(jumlah) as total FROM bayar_piutang WHERE id_piutang = '$id_piutang'")->getRow();
            $sisa       = $piutang->jumlah - $sudahbyr->total;

            $response = [
                'status'   => true,
                'sudahbyr' => 'Rp. ' . number_format($sudahbyr->total),
                'sisa'     => 'Rp. ' . number_format($sisa)
            ];
        } else {
            $response = [
                'status' => false,
            ];
        }

        echo json_encode($response);
    }
}
