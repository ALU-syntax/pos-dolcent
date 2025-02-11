<?php

namespace Modul\Pemasukan\Controllers;

use App\Controllers\BaseController;
use Hermawan\DataTables\DataTable;
use Modul\Pemasukan\Models\Model_pemasukan;

class Pemasukan extends BaseController
{
    public function __construct()
    {
        $this->pemasukan = new Model_pemasukan();
    }

    public function index()
    {
        $id_toko = $this->session->get('id_toko');
        $kategori = $this->db->query("SELECT * FROM kategori_pemasukan ORDER BY nama_kategori ASC")->getResult();
        $pelanggan = $this->db->query("SELECT * FROM pelanggan WHERE id_toko = '$id_toko' ORDER BY nama ASC")->getResult();

        $data_page = [
            'menu'      => 'pencatatan',
            'submenu'   => 'pemasukan',
            'title'     => 'Data Pemasukan',
            'kategori'  => $kategori,
            'pelanggan' => $pelanggan
        ];

        return view('Modul\Pemasukan\Views\viewPemasukan', $data_page);
    }

    public function datatable()
    {
        $id_toko = $this->session->get('id_toko');

        $builder = $this->db->table('pemasukan as a')
            ->select('a.id as id, a.jumlah as jumlah, a.foto as foto, a.tgl as tgl, a.catatan as catatan, a.pelanggan as pelanggan, b.nama_kategori as kategori, c.nama as nama_pelanggan')
            ->join('kategori_pemasukan as b', 'b.id = a.id_kategori_pemasukan')
            ->join('pelanggan as c', 'c.id = a.id_pelanggan', 'left')
            ->where('a.id_toko', $id_toko)->orderBy('a.id', 'DESC');

        return DataTable::of($builder)
            ->addNumbering('no')
            ->setSearchableColumns(['LOWER(c.nama)', 'LOWER(a.pelanggan)'])
            ->add('action', function ($row) {
                return '<button type="button" class="btn btn-light" title="Edit Data" onclick="edit(\'' . $row->id . '\')"><i class="fa fa-edit"></i></button>
                <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->foto . '\')"><i class="fa fa-trash"></i></button>';
            })->add('jumlah', function ($row) {
                return 'Rp. ' . number_format($row->jumlah);
            })->add('foto', function ($row) {
                if ($row->foto) {
                    return '<image data-fancybox data-src="/assets/img/pemasukan/' . $row->foto . '" src="/assets/img/pemasukan/' . $row->foto . '" height="70" style="cursor: zoom-in; border-radius: 5px;"/>';
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

    public function simpan()
    {
        $rules = $this->validate([
            'kategori' => [
                'label'  => 'Kategori',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'jumlah' => [
                'label'  => 'Jumlah',
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

        if (!$rules) {
            $errors = [
                'kategori'   => $this->validation->getError('kategori'),
                'jumlah'     => $this->validation->getError('jumlah'),
                'foto'       => $this->validation->getError('foto'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id           = $this->request->getPost('id');
            $id_toko      = $this->session->get('id_toko');
            $kategori     = $this->request->getPost('kategori');
            $jumlah       = $this->request->getPost('jumlah');
            $catatan      = $this->request->getPost('catatan');
            $id_pelanggan = $this->request->getPost('pelanggan');
            $tgl          = $this->request->getPost('tgl');

            $foto         = $this->request->getFile('foto');

            $pelanggan = $this->db->query("SELECT nama FROM pelanggan WHERE id = '$id_pelanggan'")->getRow();

            $data = [
                'id'                    => $id,
                'id_toko'               => $id_toko,
                'id_kategori_pemasukan' => $kategori,
                'id_pelanggan'          => $id_pelanggan,
                'pelanggan'             => $pelanggan->nama,
                'jumlah'                => getAmount($jumlah),
                'tgl'                   => $tgl,
                'catatan'               => $catatan,
            ];

            if ($foto->isValid() && !$foto->hasMoved()) {
                $namafile = $foto->getRandomName();
                $foto->move(ROOTPATH . 'public/assets/img/pemasukan/', $namafile);

                if ($id) {
                    $foto = $this->db->table('pemasukan')->select('foto')->where('id', $id)->get()->getRow();
                    $path = 'assets/img/pemasukan/';
                    $unlink = @unlink($path . $foto->foto);
                }

                $data['foto'] = $namafile;
            }

            $save = $this->pemasukan->save($data);

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

        $data = $this->db->table('pemasukan')
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
        $foto = $this->request->getPost('foto');

        // Unlink IMG
        $path = 'assets/img/pemasukan/';
        $unlink = @unlink($path . $foto);

        if ($this->pemasukan->delete($id)) {
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
