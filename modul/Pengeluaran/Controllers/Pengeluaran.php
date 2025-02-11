<?php

namespace Modul\Pengeluaran\Controllers;

use App\Controllers\BaseController;
use Hermawan\DataTables\DataTable;
use Modul\Pengeluaran\Models\Model_pengeluaran;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Pengeluaran extends BaseController
{
    public function __construct()
    {
        $this->pengeluaran = new Model_pengeluaran();
    }

    public function index()
    {
        $id_toko = $this->session->get('id_toko');
        $kategori = $this->db->query("SELECT * FROM kategori_pengeluaran ORDER BY nama_kategori ASC")->getResult();
        $pelanggan = $this->db->query("SELECT * FROM pelanggan WHERE id_toko = '$id_toko' ORDER BY nama ASC")->getResult();

        $data_page = [
            'menu'      => 'pencatatan',
            'submenu'   => 'pengeluaran',
            'title'     => 'Data Pengeluaran',
            'kategori'  => $kategori,
            'pelanggan' => $pelanggan
        ];

        return view('Modul\Pengeluaran\Views\viewPengeluaran', $data_page);
    }

    public function datatable()
    {
        $id_toko = $this->session->get('id_toko');

        $builder = $this->db->table('pengeluaran as a')
            ->select('a.id as id, a.jumlah as jumlah, a.foto as foto, a.tgl as tgl, a.catatan as catatan, a.pelanggan as pelanggan, b.nama_kategori as kategori, c.nama as nama_pelanggan')
            ->join('kategori_pengeluaran as b', 'b.id = a.id_kategori_pengeluaran')
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
                    return '<image data-fancybox data-src="/assets/img/pengeluaran/' . $row->foto . '" src="/assets/img/pengeluaran/' . $row->foto . '" height="70" style="cursor: zoom-in; border-radius: 5px;"/>';
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
                'id'                      => $id,
                'id_toko'                 => $id_toko,
                'id_kategori_pengeluaran' => $kategori,
                'id_pelanggan'            => $id_pelanggan,
                'pelanggan'               => $pelanggan->nama ?? null,
                'jumlah'                  => getAmount($jumlah),
                'tgl'                     => $tgl,
                'catatan'                 => $catatan,
            ];

            if ($foto->isValid() && !$foto->hasMoved()) {
                $namafile = $foto->getRandomName();
                $foto->move(ROOTPATH . 'public/assets/img/pengeluaran/', $namafile);

                if ($id) {
                    $foto = $this->db->table('pengeluaran')->select('foto')->where('id', $id)->get()->getRow();
                    $path = 'assets/img/pengeluaran/';
                    $unlink = @unlink($path . $foto->foto);
                }

                $data['foto'] = $namafile;
            }

            $save = $this->pengeluaran->save($data);

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

        $data = $this->db->table('pengeluaran')
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
        $path = 'assets/img/pengeluaran/';
        $unlink = @unlink($path . $foto);

        if ($this->pengeluaran->delete($id)) {
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

    public function exportExcel(){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $pengeluaran = $this->db->query("SELECT p.tgl AS tanggal, kp.nama_kategori AS kategori, p.pelanggan AS pelanggan,
            p.jumlah AS jumlah, p.catatan AS catatan
            FROM pengeluaran p LEFT JOIN kategori_pengeluaran kp 
            ON p.id_kategori_pengeluaran = kp.id;")->getResult();

        $sheet->mergeCells('A1:E2');
        $sheet->setCellValue('A1', 'CATATAN PENGELUARAN');
        $sheet->getStyle('A1:E3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00A300'); 
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:E1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension('1')->setRowHeight(30); // Tinggi baris 1

        $sheet->setCellValue('A3', "TANGGAL");
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('B3', "KATEGORI");
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('C3', "PELANGGAN");
        $sheet->getStyle('C3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("D3", "JUMLAH");
        $sheet->getStyle('D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("E3", "CATATAN");
        $sheet->getStyle('E3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getColumnDimension('A')->setWidth(20); // Lebar kolom A
        $sheet->getColumnDimension('B')->setWidth(30); // Lebar kolom B
        $sheet->getColumnDimension('C')->setWidth(30); // Lebar kolom C
        $sheet->getColumnDimension('D')->setWidth(30); // Lebar kolom D
        $sheet->getColumnDimension('E')->setAutoSize(true); // Lebar kolom E

        $row = 4;

        foreach($pengeluaran as $value){
            $wrapText = $this->wrapText($value->catatan, 5);
            $sheet->setCellValue('A' . $row, $value->tanggal);
            $sheet->setCellValue('B' . $row, $value->kategori);
            $sheet->setCellValue('C' . $row, $value->pelanggan);
            $sheet->setCellValue('D' . $row, $value->jumlah);
            $sheet->setCellValue('E' . $row, $wrapText);

            $sheet->getStyle('B' . $row . ':' . 'E' . $row)
                ->getNumberFormat()
                ->setFormatCode('"Rp"#,##0.00_-');

            $row++;    
        }

        // Mengatur border untuk tabel
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $rightBorder = [
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN, 
                    'color' => ['argb' => 'FF000000'], 
                ],
            ],
        ];
        
        $sheet->getStyle('A1:' . 'E' . ($row-1))->applyFromArray($styleArray);

        // Mengatur header HTTP untuk mengunduh file Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan Pengeluaran.xlsx"');
        header('Cache-Control: max-age=0');
        

        $writer = new Xlsx($spreadsheet);
        $writer->save("Laporan Pengeluaran.xlsx");

        // Menyimpan file Excel ke output
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
        
    }

    private function wrapText($text, $maxLines) {
        $lines = explode("\n", wordwrap($text, 50, "\n")); // Menentukan lebar karakter untuk memotong
        $wrappedText = implode("\n", array_slice($lines, 0, $maxLines));

        return $wrappedText;
    }
}
