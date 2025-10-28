<?php

namespace Modul\Dashboard\Controllers;

use App\Controllers\BaseController;
use DateTime;
use Hermawan\DataTables\DataTable;
use Modul\Dashboard\Models\Model_toko;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Dashboard extends BaseController
{
    public function __construct()
    {
        $this->toko = new Model_toko();
    }

    public function index()
    {
        $id_toko     = $this->session->get('id_toko');

        $omset       = $this->db->query("SELECT SUM(total) as total FROM penjualan WHERE id_toko = '$id_toko' AND `delete` <> 1")->getRow()->total;
        $laba        = $this->db->query("SELECT SUM(laba) as total FROM penjualan WHERE id_toko = '$id_toko' AND `delete` <> 1")->getRow()->total;
        $trx         = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND `delete` <> 1")->getRow()->total;
        $discount    = $this->db->query("SELECT SUM(discount) as total FROM penjualan WHERE id_toko = '$id_toko' AND `delete` <> 1")->getRow()->total;
        $cash        = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND id_tipe_bayar = 1 AND `delete` <> 1")->getRow()->total;
        $noncash     = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND id_tipe_bayar != 1 AND `delete` <> 1")->getRow()->total;
        $pemasukan   = $this->db->query("SELECT SUM(jumlah) as total FROM pemasukan WHERE id_toko = '$id_toko'")->getRow()->total;
        $pengeluaran = $this->db->query("SELECT SUM(jumlah) as total FROM pengeluaran WHERE id_toko = '$id_toko'")->getRow()->total;
        $biayaProduksi = $this->db->query("SELECT SUM(nominal) as total FROM biaya_produksi WHERE id_toko = '$id_toko'")->getRow()->total;

        $grafik      = [];
        $dates       = [];
        for ($i = 9; $i >= 0; $i--) {
            $tgl = date('Y-m-d', strtotime("-$i day"));
            $penjualan = $this->db->query("SELECT SUM(total) as total FROM penjualan WHERE id_toko = '$id_toko' AND DATE(tgl) = '$tgl' AND `delete` <> 1")->getRow()->total;
            array_push($grafik, $penjualan);
            array_push($dates, $tgl);
        }

        $data = [
            'title'       => 'Dashboard',
            'menu'        => 'dashboard',
            'submenu'     => '',
            'pemasukan'   => number_format($pemasukan),
            'pengeluaran' => number_format($pengeluaran),
            'omset'       => number_format($omset),
            'laba'        => number_format($laba),
            'trx'         => number_format($trx),
            'discount'    => number_format($discount),
            'cash'        => number_format($cash),
            'noncash'     => number_format($noncash),
            'grafik'      => json_encode($grafik),
            'dates'       => json_encode($dates),
            'biayaProduksi' => number_format($biayaProduksi)
        ];

        return view('Modul\Dashboard\Views\viewDashboard', $data);
    }

    public function datatable()
    {
        $id_toko = $this->session->get('id_toko');

        $builder = $this->db->table('penjualan as a')
            ->select('a.tgl as tgl, a.total as total, a.laba as laba, b.nama as pelanggan, c.icon as icon, c.nama_tipe as nama_tipe')
            ->join('pelanggan as b', 'a.id_pelanggan = b.id', 'left')
            ->join('tipe_bayar as c', 'c.id = a.id_tipe_bayar')
            ->where('a.id_toko', $id_toko)
            ->where('a.delete <>', 1)
            ->orderBy('a.id', 'DESC')->limit(10);

        return DataTable::of($builder)
            ->addNumbering('no')
            ->setSearchableColumns(['LOWER(b.nama)'])
            ->add('metode', function ($row) {
                return '<i class="' . $row->icon . '"></i>&nbsp; ' . $row->nama_tipe . '';
            })->add('total', function ($row) {
                return 'Rp. ' . number_format($row->total);
            })->add('laba', function ($row) {
                return 'Rp. ' . number_format($row->laba);
            })->add('tgl', function ($row) {
                $tgl = new DateTime($row->tgl);
                $date = $tgl->format('d F Y, H:i');

                return $date;
            })->add('pelanggan', function ($row) {
                if ($row->pelanggan) {
                    return $row->pelanggan;
                } else {
                    return '--';
                }
            })
            ->toJson(true);
    }

    public function getRingkasan()
    {
        $f = $this->request->getPost('filter');
        $id_toko = $this->session->get('id_toko');
        $tgl = date("Y-m-d");

        switch ($f) {
            case '0':
                $omset       = $this->db->query("SELECT SUM(total) as total FROM penjualan WHERE id_toko = '$id_toko' AND `delete` <> 1")->getRow()->total;
                $laba        = $this->db->query("SELECT SUM(laba) as total FROM penjualan WHERE id_toko = '$id_toko' AND `delete` <> 1")->getRow()->total;
                $trx         = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND `delete` <> 1")->getRow()->total;
                $discount    = $this->db->query("SELECT SUM(discount) as total FROM penjualan WHERE id_toko = '$id_toko' AND `delete` <> 1")->getRow()->total;
                $cash        = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND id_tipe_bayar = 1 AND `delete` <> 1")->getRow()->total;
                $noncash     = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND id_tipe_bayar != 1 AND `delete` <> 1")->getRow()->total;
                $pemasukan   = $this->db->query("SELECT SUM(jumlah) as total FROM pemasukan WHERE id_toko = '$id_toko'")->getRow()->total;
                $pengeluaran = $this->db->query("SELECT SUM(jumlah) as total FROM pengeluaran WHERE id_toko = '$id_toko'")->getRow()->total;
                $biayaProduksi = $this->db->query("SELECT SUM(nominal) as total FROM biaya_produksi WHERE id_toko = '$id_toko'")->getRow()->total;
                break;
            case '1':
                $omset       = $this->db->query("SELECT SUM(total) as total FROM penjualan WHERE id_toko = '$id_toko' AND DATE(tgl) = '$tgl' AND `delete` <> 1")->getRow()->total;
                $laba        = $this->db->query("SELECT SUM(laba) as total FROM penjualan WHERE id_toko = '$id_toko' AND DATE(tgl) = '$tgl' AND `delete` <> 1")->getRow()->total;
                $trx         = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND DATE(tgl) = '$tgl' AND `delete` <> 1")->getRow()->total;
                $discount    = $this->db->query("SELECT SUM(discount) as total FROM penjualan WHERE id_toko = '$id_toko' AND DATE(tgl) = '$tgl' AND `delete` <> 1")->getRow()->total;
                $cash        = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND id_tipe_bayar = 1 AND DATE(tgl) = '$tgl' AND `delete` <> 1")->getRow()->total;
                $noncash     = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND id_tipe_bayar != 1 AND DATE(tgl) = '$tgl' AND `delete` <> 1")->getRow()->total;
                $pemasukan   = $this->db->query("SELECT SUM(jumlah) as total FROM pemasukan WHERE id_toko = '$id_toko' AND DATE(tgl) = '$tgl'")->getRow()->total;
                $pengeluaran = $this->db->query("SELECT SUM(jumlah) as total FROM pengeluaran WHERE id_toko = '$id_toko' AND DATE(tgl) = '$tgl'")->getRow()->total;
                $biayaProduksi = $this->db->query("SELECT SUM(nominal) as total FROM biaya_produksi WHERE id_toko = '$id_toko' AND DATE(tanggal) = '$tgl'")->getRow()->total;
                break;
            case '2':
                $omset       = $this->db->query("SELECT SUM(total) as total FROM penjualan WHERE id_toko = '$id_toko' AND WEEK(tgl) = WEEK(NOW()) AND `delete` <> 1")->getRow()->total;
                $laba        = $this->db->query("SELECT SUM(laba) as total FROM penjualan WHERE id_toko = '$id_toko' AND WEEK(tgl) = WEEK(NOW()) AND `delete` <> 1")->getRow()->total;
                $trx         = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND WEEK(tgl) = WEEK(NOW()) AND `delete` <> 1")->getRow()->total;
                $discount    = $this->db->query("SELECT SUM(discount) as total FROM penjualan WHERE id_toko = '$id_toko' AND WEEK(tgl) = WEEK(NOW()) AND `delete` <> 1")->getRow()->total;
                $cash        = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND id_tipe_bayar = 1 AND WEEK(tgl) = WEEK(NOW()) AND `delete` <> 1")->getRow()->total;
                $noncash     = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND id_tipe_bayar != 1 AND WEEK(tgl) = WEEK(NOW()) AND `delete` <> 1")->getRow()->total;
                $pemasukan   = $this->db->query("SELECT SUM(jumlah) as total FROM pemasukan WHERE id_toko = '$id_toko' AND WEEK(tgl) = WEEK(NOW())")->getRow()->total;
                $pengeluaran = $this->db->query("SELECT SUM(jumlah) as total FROM pengeluaran WHERE id_toko = '$id_toko' AND WEEK(tgl) = WEEK(NOW())")->getRow()->total;
                $biayaProduksi = $this->db->query("SELECT SUM(nominal) as total FROM biaya_produksi WHERE id_toko = '$id_toko' AND WEEK(tanggal) = WEEK(NOW())")->getRow()->total;
                break;
            case '3':
                $omset       = $this->db->query("SELECT SUM(total) as total FROM penjualan WHERE id_toko = '$id_toko' AND MONTH(tgl) = MONTH(NOW()) AND `delete` <> 1")->getRow()->total;
                $laba        = $this->db->query("SELECT SUM(laba) as total FROM penjualan WHERE id_toko = '$id_toko' AND MONTH(tgl) = MONTH(NOW()) AND `delete` <> 1")->getRow()->total;
                $trx         = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND MONTH(tgl) = MONTH(NOW()) AND `delete` <> 1")->getRow()->total;
                $discount    = $this->db->query("SELECT SUM(discount) as total FROM penjualan WHERE id_toko = '$id_toko' AND MONTH(tgl) = MONTH(NOW()) AND `delete` <> 1")->getRow()->total;
                $cash        = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND id_tipe_bayar = 1 AND MONTH(tgl) = MONTH(NOW()) AND `delete` <> 1")->getRow()->total;
                $noncash     = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND id_tipe_bayar != 1 AND MONTH(tgl) = MONTH(NOW()) AND `delete` <> 1")->getRow()->total;
                $pemasukan   = $this->db->query("SELECT SUM(jumlah) as total FROM pemasukan WHERE id_toko = '$id_toko' AND MONTH(tgl) = MONTH(NOW())")->getRow()->total;
                $pengeluaran = $this->db->query("SELECT SUM(jumlah) as total FROM pengeluaran WHERE id_toko = '$id_toko' AND MONTH(tgl) = MONTH(NOW())")->getRow()->total;
                $biayaProduksi = $this->db->query("SELECT SUM(nominal) as total FROM biaya_produksi WHERE id_toko = '$id_toko' AND MONTH(tanggal) = MONTH(NOW())")->getRow()->total;
                break;
            case '4':
                $omset       = $this->db->query("SELECT SUM(total) as total FROM penjualan WHERE id_toko = '$id_toko' AND YEAR(tgl) = YEAR(NOW()) AND `delete` <> 1")->getRow()->total;
                $laba        = $this->db->query("SELECT SUM(laba) as total FROM penjualan WHERE id_toko = '$id_toko' AND YEAR(tgl) = YEAR(NOW()) AND `delete` <> 1")->getRow()->total;
                $trx         = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND YEAR(tgl) = YEAR(NOW()) AND `delete` <> 1")->getRow()->total;
                $discount    = $this->db->query("SELECT SUM(discount) as total FROM penjualan WHERE id_toko = '$id_toko' AND YEAR(tgl) = YEAR(NOW()) AND `delete` <> 1")->getRow()->total;
                $cash        = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND id_tipe_bayar = 1 AND YEAR(tgl) = YEAR(NOW()) AND `delete` <> 1")->getRow()->total;
                $noncash     = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND id_tipe_bayar != 1 AND YEAR(tgl) = YEAR(NOW()) AND `delete` <> 1")->getRow()->total;
                $pemasukan   = $this->db->query("SELECT SUM(jumlah) as total FROM pemasukan WHERE id_toko = '$id_toko' AND YEAR(tgl) = YEAR(NOW())")->getRow()->total;
                $pengeluaran = $this->db->query("SELECT SUM(jumlah) as total FROM pengeluaran WHERE id_toko = '$id_toko' AND YEAR(tgl) = YEAR(NOW())")->getRow()->total;
                $biayaProduksi = $this->db->query("SELECT SUM(nominal) as total FROM biaya_produksi WHERE id_toko = '$id_toko' AND YEAR(tanggal) = YEAR(NOW())")->getRow()->total;
                break;
        }

        $respond = [
            'pemasukan'   => 'Rp. ' .  number_format($pemasukan),
            'pengeluaran' => 'Rp. ' .  number_format($pengeluaran),
            'omset'       => 'Rp. ' . number_format($omset),
            'laba'        => 'Rp. ' .  number_format($laba),
            'trx'         => number_format($trx),
            'discount'    => 'Rp. ' .  number_format($discount),
            'cash'        => number_format($cash),
            'noncash'     => number_format($noncash),
            'biayaProduksi' => 'Rp. ' . number_format($biayaProduksi)
        ];

        echo json_encode($respond);
    }

    public function change_profile()
    {
        $email = $this->session->get('email_toko');
        $rules = $this->validate([
            'namatoko' => [
                'label'  => 'Nama Toko',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'nohptoko' => [
                'label'  => 'Nomor HP',
                'rules'  => 'required',
                'errors' => [
                    'required'   => '{field} harus diisi!',
                ]
            ],
            'emailtoko' => [
                'label'  => 'E-mail',
                'rules'  => 'required|is_unique[toko.email,email,' . $email . ']|valid_email',
                'errors' => [
                    'required'     => '{field} harus diisi!',
                    'is_unique'    => '{field} telah terdaftar',
                    'valid_email'  => '{field} tidak valid',
                ]
            ],
            'logo'  => [
                'label' => 'Logo',
                'rules' => 'max_size[logo, 1024]|ext_in[logo,jpg,png,jpeg]',
                'errors' => [
                    'max_size' => 'Ukuran {field} terlalu besar!',
                    'ext_in'   => '{field} harus JPG,PNG atau JEPG!',
                ]
            ],
        ]);

        if (!$rules) {
            $errors = [
                'namatoko'  => $this->validation->getError('namatoko'),
                'nohptoko'  => $this->validation->getError('nohptoko'),
                'emailtoko' => $this->validation->getError('emailtoko'),
                'logo'      => $this->validation->getError('logo'),
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        } else {
            $id        = $this->session->get('id_toko');
            $nama      = $this->request->getPost('namatoko');
            $nohp      = $this->request->getPost('nohptoko');
            $email     = $this->request->getPost('emailtoko');
            $logo      = $this->request->getFile('logo');

            $data = [
                'id'              => $id,
                'nama_toko'       => $nama,
                'nohp'            => $nohp,
                'email'           => $email
            ];

            $ses_data = [
                'nama_toko'      => $nama,
                'email_toko'     => $email,
                'nohp_toko'      => $nohp,
            ];

            if ($logo->isValid() && !$logo->hasMoved()) {
                $namafile = $logo->getRandomName();
                $logo->move(ROOTPATH . 'public/assets/img/logo/', $namafile);

                if ($id) {
                    $logo = $this->db->table('toko')->select('logo')->where('id', $id)->get()->getRow();
                    $path = 'assets/img/logo/';
                    $unlink = @unlink($path . $logo->logo);
                }

                $data['logo'] = $namafile;
                $ses_data['logo'] = $namafile;
            }

            $save = $this->toko->save($data);

            if ($save) {
                $this->session->set($ses_data);
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

    public function exportExcelBalance(){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $dariTanggal = $this->request->getPost('dariBalance');
        $sampaiTanggal = $this->request->getPost('sampaiBalance');

        $tanggalTerlama = $this->db->query("SELECT DATE(MIN(tgl)) AS tanggal_terlama FROM penjualan WHERE `delete` <> 1")->getResult();
        $tanggalTerbaru = $this->db->query("SELECT DATE(MAX(tgl)) AS tanggal_terbaru FROM penjualan WHERE `delete` <> 1")->getResult();
        $tanggalTerlamaResult = $tanggalTerlama[0]->tanggal_terlama;
        $tanggalTerbaruResult = $tanggalTerbaru[0]->tanggal_terbaru;

        if($dariTanggal != "" && $sampaiTanggal !=""){
            // dd("Masok 1 ", $dariTanggal, $sampaiTanggal);
            $report = $this->db->query("SELECT DATE(tgl) AS date_only, SUM(total) AS total_sum, 
            SUM(CASE WHEN id_tipe_bayar = 1 THEN total ELSE 0 END) AS total_cash, 
            SUM(CASE WHEN id_tipe_bayar = 2 THEN total ELSE 0 END) AS total_debit 
            FROM penjualan WHERE DATE(tgl) BETWEEN '$dariTanggal' AND '$sampaiTanggal' AND `delete` <> 1 GROUP BY DATE(tgl) ORDER BY DATE(tgl)")->getResult();

        }else if($dariTanggal != "" && $sampaiTanggal == ""){
            // dd("Masok 2 ", $dariTanggal, $sampaiTanggal);
            $report = $this->db->query("SELECT DATE(tgl) AS date_only, SUM(total) AS total_sum, 
            SUM(CASE WHEN id_tipe_bayar = 1 THEN total ELSE 0 END) AS total_cash, 
            SUM(CASE WHEN id_tipe_bayar = 2 THEN total ELSE 0 END) AS total_debit 
            FROM penjualan WHERE DATE(tgl) BETWEEN '$dariTanggal' AND '$tanggalTerbaruResult' AND `delete` <> 1 GROUP BY DATE(tgl) ORDER BY DATE(tgl)")->getResult();

        }else if($dariTanggal == "" && $sampaiTanggal != ""){
            // dd("Masok 3", $dariTanggal, $sampaiTanggal);
            $report = $this->db->query("SELECT DATE(tgl) AS date_only, SUM(total) AS total_sum, 
            SUM(CASE WHEN id_tipe_bayar = 1 THEN total ELSE 0 END) AS total_cash, 
            SUM(CASE WHEN id_tipe_bayar = 2 THEN total ELSE 0 END) AS total_debit 
            FROM penjualan WHERE DATE(tgl) BETWEEN '$tanggalTerlamaResult' AND '$sampaiTanggal' AND `delete` <> 1 GROUP BY DATE(tgl) ORDER BY DATE(tgl)")->getResult();

        }else{
            // dd("Masok 4", $dariTanggal, $sampaiTanggal);
            $report = $this->db->query("SELECT DATE(tgl) AS date_only, SUM(total) AS total_sum, 
            SUM(CASE WHEN id_tipe_bayar = 1 THEN total ELSE 0 END) AS total_cash, 
            SUM(CASE WHEN id_tipe_bayar = 2 THEN total ELSE 0 END) AS total_debit 
            FROM penjualan WHERE `delete` <> 1 GROUP BY DATE(tgl) ORDER BY DATE(tgl)")->getResult();

        }

        // $report = $this->db->query("SELECT DATE(tgl) AS date_only, SUM(total) AS total_sum, SUM(CASE WHEN id_tipe_bayar = 1 THEN total ELSE 0 END) AS total_cash, SUM(CASE WHEN id_tipe_bayar = 2 THEN total ELSE 0 END) AS total_debit FROM penjualan GROUP BY DATE(tgl) ORDER BY DATE(tgl)")->getResult();

        $sheet->mergeCells('A1:E2');
        $sheet->setCellValue('A1', 'BALANCE Transaksi Foodcourt');
        $sheet->getStyle('A1:E3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00A300'); 
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:E1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension('1')->setRowHeight(30); // Tinggi baris 1

        $sheet->setCellValue('A3', "TTL");
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('B3', "TOTAL OMZET");
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('C3', "Total Transfer/Cashless");
        $sheet->getStyle('C3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("D3", "TOTAL CASH");
        $sheet->getStyle('D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("E3", "BALANCE");
        $sheet->getStyle('E3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getColumnDimension('A')->setWidth(20); // Lebar kolom A
        $sheet->getColumnDimension('B')->setWidth(30); // Lebar kolom A
        $sheet->getColumnDimension('C')->setWidth(30); // Lebar kolom B
        $sheet->getColumnDimension('D')->setWidth(30); // Lebar kolom C
        $sheet->getColumnDimension('E')->setWidth(30); // Lebar kolom C

        $row = 4;

        $totalBalance = 0;
        $totalOmset = 0;
        $totalDebit = 0;
        $totalCash = 0;
        foreach($report as $value){
            $balance = $value->total_sum - ($value->total_cash + $value->total_debit);
            $sheet->setCellValue('A' . $row, $value->date_only);
            $sheet->setCellValue('B' . $row, $value->total_sum);
            $sheet->setCellValue('C' . $row, $value->total_cash);
            $sheet->setCellValue('D' . $row, $value->total_debit);
            $sheet->setCellValue('E' . $row, $balance);

            $sheet->getStyle('B' . $row . ':' . 'E' . $row)
                ->getNumberFormat()
                ->setFormatCode('"Rp"#,##0.00_-');

            $totalOmset += $value->total_sum;
            $totalDebit += $value->total_debit;
            $totalCash += $value->total_cash;
            $totalBalance += $balance;
            $row++;    
        }
        
        $sheet->mergeCells('A' . $row . ':' . 'A' . ($row + 2));
        $sheet->setCellValue('A' . $row, "TOTAL");
        $sheet->getStyle('A' . $row . ':' . 'E' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00A300'); 
        $sheet->getStyle('B' . ($row+1) . ':' . 'E' . ($row+1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00'); 
        $sheet->getStyle('B' . ($row+2) . ':' . 'E' . ($row+2))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00A300'); 

        $sheet->getStyle('A' . $row .':' . 'A' . ($row+2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row .':' . 'A' . ($row+2))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('B' . $row . ':' . 'E' . $row)
                ->getNumberFormat()
                ->setFormatCode('"Rp"#,##0.00_-');

        $sheet->setCellValue('B' . $row, $totalOmset);
        $sheet->setCellValue('C' . $row, $totalDebit);
        $sheet->setCellValue('D' . $row, $totalCash);
        $sheet->setCellValue('E' . $row, $totalBalance);

        $sheet->getStyle('B' . ($row + 1) . ':' . 'E' . ($row+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('B' . ($row+1), "TOTAL OMZET");

        $sheet->setCellValue('C' . ($row+1), "TOTAL CASHLESS & CASH");
        $sheet->mergeCells('C'. ($row+1) . ':' . 'D' . ($row+1));

        $sheet->setCellValue('E' . ($row+1), "BALANCE");

        $sheet->setCellValue('B' . ($row + 2), $totalOmset);
        $sheet->getStyle('B' . ($row+2))
                ->getNumberFormat()
                ->setFormatCode('"Rp"#,##0.00_-');
        
        $sheet->setCellValue('C' . ($row+2), ($totalCash + $totalDebit));
        $sheet->mergeCells('C'. ($row+2) . ':' . 'D' . ($row+2));
        $sheet->getStyle('C' . ($row+2))
                ->getNumberFormat()
                ->setFormatCode('"Rp"#,##0.00_-');

        $sheet->setCellValue('E' . ($row + 2), $totalBalance);
        $sheet->getStyle('E' . ($row+2))
                ->getNumberFormat()
                ->setFormatCode('"Rp"#,##0.00_-');
    
        // set font style menjadi bold
        $sheet->getStyle('A1:E3')->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':' . 'E' . ($row+2))->getFont()->setBold(true);


         // Mengatur border untuk tabel
         $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle('A1:E' . ($row+2))->applyFromArray($styleArray);

        // Mengatur header HTTP untuk mengunduh file Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan Balance.xlsx"');
        header('Cache-Control: max-age=0');
        

        $writer = new Xlsx($spreadsheet);
        $writer->save("Laporan Balance.xlsx");

        // Menyimpan file Excel ke output
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;

    }

    public function exportExcelLabaRugi(){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $dariTanggal = $this->request->getPost('dariLabarugi');
        $sampaiTanggal = $this->request->getPost('sampaiLabarugi');

        $dariTanggalTime = date('Y-m-d H:i:s', strtotime($dariTanggal));
        $sampaiTanggalTime = date('Y-m-d H:i:s', strtotime($sampaiTanggal));

        // dd($dariTanggalTime, $sampaiTanggalTime);
        $tanggalTerlama = $this->db->query("SELECT DATE(MIN(tgl)) AS tanggal_terlama FROM penjualan WHERE `delete` <> 1")->getResult();
        $tanggalTerbaru = $this->db->query("SELECT DATE(MAX(tgl)) AS tanggal_terbaru FROM penjualan WHERE `delete` <> 1")->getResult();
        $tanggalTerlamaResult = $tanggalTerlama[0]->tanggal_terlama;
        $tanggalTerbaruResult = $tanggalTerbaru[0]->tanggal_terbaru;

        // dd($tanggalTerbaru[0]->tanggal_terbaru);
        if($dariTanggal != "" && $sampaiTanggal != ""){
            // dd("MASOK 1", $dariTanggal, $sampaiTanggal);
            $report = $this->db->query("SELECT b.id, b.nama_barang AS nama_barang, b.harga_jual AS harga_jual, b.harga_modal AS harga_modal,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.total ELSE 0 END) AS total_pendapatan,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.qty ELSE 0 END) AS qty_terjual FROM barang b 
             LEFT JOIN detail_penjualan dp ON dp.id_barang = b.id LEFT JOIN penjualan p ON dp.id_penjualan = p.id 
             WHERE p.tgl BETWEEN '$dariTanggalTime' AND '$sampaiTanggalTime' AND 'p.delete' <> 1 GROUP BY b.id, b.nama_barang, b.harga_jual, b.harga_modal")->getResult();   
             
             $sheet->setCellValue('A1', "LAPORAN LABA RUGI \n FoodCourt \n Periode $dariTanggal - $sampaiTanggal 2024");
        }else if($dariTanggal != "" && $sampaiTanggal == ""){
            // dd("MASOK 2", $dariTanggal, $sampaiTanggal);
            $report = $this->db->query("SELECT b.id, b.nama_barang AS nama_barang, b.harga_jual AS harga_jual, b.harga_modal AS harga_modal,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.total ELSE 0 END) AS total_pendapatan,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.qty ELSE 0 END) AS qty_terjual FROM barang b 
             LEFT JOIN detail_penjualan dp ON dp.id_barang = b.id LEFT JOIN penjualan p ON dp.id_penjualan = p.id 
             WHERE p.tgl BETWEEN '$dariTanggalTime' AND (SELECT MAX(tgl) AS tanggal_terbaru FROM penjualan) AND 'p.delete' <> 1 GROUP BY b.id, b.nama_barang, b.harga_jual, b.harga_modal")->getResult();    

            $sheet->setCellValue('A1', "LAPORAN LABA RUGI \n FoodCourt \n Periode $dariTanggal - $tanggalTerbaruResult 2024");
        }else if($dariTanggal == "" && $sampaiTanggal != ""){
            // dd("MASOK 3", $dariTanggal, $sampaiTanggal);
            $report = $this->db->query("SELECT b.id, b.nama_barang AS nama_barang, b.harga_jual AS harga_jual, b.harga_modal AS harga_modal,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.total ELSE 0 END) AS total_pendapatan,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.qty ELSE 0 END) AS qty_terjual FROM barang b 
             LEFT JOIN detail_penjualan dp ON dp.id_barang = b.id LEFT JOIN penjualan p ON dp.id_penjualan = p.id 
             WHERE p.tgl BETWEEN (SELECT MIN(tgl) AS tanggal_terlama FROM penjualan) AND '$sampaiTanggalTime' AND 'p.delete' <> 1 GROUP BY b.id, b.nama_barang, b.harga_jual, b.harga_modal")->getResult();    

            $sheet->setCellValue('A1', "LAPORAN LABA RUGI \n FoodCourt \n Periode $tanggalTerlamaResult - $sampaiTanggal 2024");
        }else{
            // dd("MASOK 4", $dariTanggal, $sampaiTanggal);
            $report = $this->db->query("SELECT b.id, b.nama_barang AS nama_barang, b.harga_jual AS harga_jual, b.harga_modal AS harga_modal,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.total ELSE 0 END) AS total_pendapatan,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.qty ELSE 0 END) AS qty_terjual FROM barang b 
             LEFT JOIN detail_penjualan dp ON dp.id_barang = b.id LEFT JOIN penjualan p ON dp.id_penjualan = p.id 
             WHERE 'p.delete' <> 1 GROUP BY b.id, b.nama_barang, b.harga_jual, b.harga_modal")->getResult();    

            $sheet->setCellValue('A1', "LAPORAN LABA RUGI \n FoodCourt \n Periode 2024");
        }


        $sheet->mergeCells('A1:D3');
        $sheet->setCellValue('A1', "LAPORAN LABA RUGI \n FoodCourt \n Periode 2024");
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00A300'); 
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:D1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->mergeCells('A4:D4');
        $sheet->setCellValue('A4', "Total Penjualan DOLCENT (OMZET)");
        $sheet->getStyle('A4')->getFont()->setBold(true);

        $rowOmzet = 5;
        $totalOmzet = 0;
        foreach($report as $key => $data){
            $sheet->setCellValue('A' . $rowOmzet, ($key+1));
            $sheet->setCellValue('B' . $rowOmzet, $data->nama_barang);
            $sheet->setCellValue('C' . $rowOmzet, $data->total_pendapatan);
            $sheet->getStyle('C' . $rowOmzet)
            ->getNumberFormat()
            ->setFormatCode('"Rp"#,##0.00_-');

            $totalOmzet += $data->total_pendapatan;
            $rowOmzet++;
        }

        $sheet->mergeCells('A' . $rowOmzet . ':' . 'C' . $rowOmzet);
        $sheet->setCellValue('D' . $rowOmzet, $totalOmzet);
        $sheet->getStyle('D' . $rowOmzet)
            ->getNumberFormat()
            ->setFormatCode('"Rp"#,##0.00_-');
        $sheet->getStyle('D' . $rowOmzet)->getFont()->setBold(true);

        $sheet->mergeCells('A' . ($rowOmzet+1) . ':' . 'D' . ($rowOmzet+1));
        $sheet->setCellValue('A' . ($rowOmzet+1), "Harga Pokok Penjualan Foodcourt HPP");
        $sheet->getStyle('A' . ($rowOmzet+1))->getFont()->setBold(true);

        $rowHpp = $rowOmzet + 2;
        $totalHpp = 0;
        foreach($report as $key => $data){
            $hpp = $data->harga_modal * $data->qty_terjual;
            $sheet->setCellValue('A' . $rowHpp, ($key+1));
            $sheet->setCellValue('B' . $rowHpp, $data->nama_barang);
            $sheet->setCellValue('C' . $rowHpp, $hpp);
            $sheet->getStyle('C' . $rowHpp)
            ->getNumberFormat()
            ->setFormatCode('"Rp"#,##0.00_-');

            $totalHpp += $hpp;
            $rowHpp++;
        }

        $grossProfit = ($totalOmzet - $totalHpp);
        $sheet->mergeCells('A' . $rowHpp . ':' . 'C' . $rowHpp);
        $sheet->setCellValue('D' . $rowHpp, $totalHpp);
        $sheet->getStyle('D' . $rowHpp)->getFont()->setBold(true);
        $sheet->getStyle('D' . $rowHpp)
            ->getNumberFormat()
            ->setFormatCode('"Rp"#,##0.00_-');

        $sheet->mergeCells('A' . ($rowHpp+1) . ':' . 'C' . ($rowHpp+1));
        $sheet->setCellValue('A' . ($rowHpp+1), "Gross Profit Foodcourt");
        $sheet->getStyle('A' . ($rowHpp+1))->getFont()->setBold(true);

        $sheet->setCellValue('D' . ($rowHpp+1), $grossProfit);
        $sheet->getStyle('D' . ($rowHpp+1))->getFont()->setBold(true);
        $sheet->getStyle('D' . ($rowHpp+1))
            ->getNumberFormat()
            ->setFormatCode('"Rp"#,##0.00_-');

        $sheet->mergeCells('A' . ($rowHpp+2). ':' . 'D' . ($rowHpp+2));
        $sheet->setCellValue('A' . ($rowHpp+2), "Beban Operasional");
        $sheet->getStyle('A' . ($rowHpp+2))->getFont()->setBold(true);

        $reportPengeluaran = $this->db->query('SELECT kp.id AS id , kp.nama_kategori AS nama_kategori, SUM(CASE WHEN p.id_kategori_pengeluaran = kp.id THEN p.jumlah ELSE 0 END) AS total_pengeluaran FROM kategori_pengeluaran kp LEFT JOIN pengeluaran p ON p.id_kategori_pengeluaran = kp.id GROUP BY kp.id, kp.nama_kategori')->getResult();
        $rowBebanOperasional = $rowHpp + 3;
        $totalPengeluaran = 0;
        foreach($reportPengeluaran as $key => $data){
            $sheet->setCellValue('A' . $rowBebanOperasional, ($key+1));
            $sheet->setCellValue('B' . $rowBebanOperasional, $data->nama_kategori);
            $sheet->setCellValue('C' . $rowBebanOperasional, $data->total_pengeluaran);
            $sheet->getStyle('C' . $rowBebanOperasional)
            ->getNumberFormat()
            ->setFormatCode('"Rp"#,##0.00_-');

            $totalPengeluaran += $data->total_pengeluaran;
            $rowBebanOperasional++;
        }   

        $sheet->mergeCells('A' . $rowBebanOperasional . ':' . 'C' . $rowBebanOperasional);
        $sheet->getStyle('A' . $rowBebanOperasional)->getFont()->setBold(true);
        $sheet->setCellValue('A' . $rowBebanOperasional, "Total Beban Operasional");

        $sheet->setCellValue('D' . $rowBebanOperasional, $totalPengeluaran);
        $sheet->getStyle('D' . $rowBebanOperasional)->getFont()->setBold(true);
        $sheet->getStyle('D' . $rowBebanOperasional)
            ->getNumberFormat()
            ->setFormatCode('"Rp"#,##0.00_-');

        $sheet->mergeCells('A' . ($rowBebanOperasional+1) . ':' . 'C' . ($rowBebanOperasional+1));
        $sheet->setCellValue('A' . ($rowBebanOperasional+1), "Laba Bersih");
        $sheet->getStyle('A' . ($rowBebanOperasional+1))->getFont()->setBold(true);

        $sheet->setCellValue('D' . ($rowBebanOperasional+1), ($grossProfit - $totalPengeluaran));
        $sheet->getStyle('D' . ($rowBebanOperasional+1))->getFont()->setBold(true);
        $sheet->getStyle('D' . ($rowBebanOperasional+1))
            ->getNumberFormat()
            ->setFormatCode('"Rp"#,##0.00_-');
        
        $sheet->mergeCells('A' . ($rowBebanOperasional+2) . ':' . 'C' . ($rowBebanOperasional+2));
        $sheet->setCellValue('A' . ($rowBebanOperasional+2), "Setor Ke NMS 100%");
        $sheet->getStyle('A' . ($rowBebanOperasional+2))->getFont()->setBold(true);

        $sheet->setCellValue('D' . ($rowBebanOperasional+2), ($grossProfit - $totalPengeluaran));
        $sheet->getStyle('D' . ($rowBebanOperasional+2))->getFont()->setBold(true);
        $sheet->getStyle('D' . ($rowBebanOperasional+2))
            ->getNumberFormat()
            ->setFormatCode('"Rp"#,##0.00_-');

        $sheet->getColumnDimension('A')->setWidth(5); 
        $sheet->getColumnDimension('B')->setWidth(40); 
        $sheet->getColumnDimension('C')->setWidth(30); 
        $sheet->getColumnDimension('D')->setWidth(30); 
    
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
        
        $sheet->getStyle('A1:D4')->applyFromArray($styleArray);
        $sheet->getStyle('A5' . ':' . 'C' . ($rowOmzet-1))->applyFromArray($styleArray);
        $sheet->getStyle('A5' . $rowOmzet . ':' . 'D' . $rowOmzet)->applyFromArray($styleArray);
        $sheet->getStyle('D5:' . 'D' . ($rowOmzet-1))->applyFromArray($rightBorder);

        $sheet->getStyle('A' . $rowOmzet . ':' . 'D' . ($rowOmzet+1))->applyFromArray($styleArray);

        $sheet->getStyle('A' . ($rowOmzet+2) . ':' . 'C' . ($rowHpp-1))->applyFromArray($styleArray);
        $sheet->getStyle('D' . ($rowOmzet+2) . ':' . 'D' . ($rowHpp-1))->applyFromArray($rightBorder);

        $sheet->getStyle('A' . $rowHpp . ':' . 'D' . ($rowHpp+2))->applyFromArray($styleArray);

        $sheet->getStyle('A' . ($rowHpp+3) . ':' . 'C' . ($rowBebanOperasional-1))->applyFromArray($styleArray);
        $sheet->getStyle('D' . ($rowHpp+3) . ':' . 'D' . ($rowBebanOperasional-1))->applyFromArray($rightBorder);
        
        $sheet->getStyle('A' . $rowBebanOperasional . ':' . 'D' . ($rowBebanOperasional+2))->applyFromArray($styleArray);



        // Mengatur header HTTP untuk mengunduh file Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan Laba Rugi.xlsx"');
        header('Cache-Control: max-age=0');
        

        $writer = new Xlsx($spreadsheet);
        $writer->save("Laporan Laba Rugi.xlsx");

        // Menyimpan file Excel ke output
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;

    }
}
