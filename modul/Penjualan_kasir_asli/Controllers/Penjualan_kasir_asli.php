<?php

namespace Modul\Penjualan_kasir_asli\Controllers;

use App\Controllers\BaseController;
use DateTime;
use Hermawan\DataTables\DataTable;
use Modul\Kasir\Models\Model_penjualan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Penjualan_kasir_asli extends BaseController
{
    public function __construct()
    {
        $this->penjualan = new Model_penjualan();
    }

    public function index()
    {
        $id_toko = $this->session->get('id_toko');

        $pelanggan = $this->db->query("SELECT * FROM pelanggan WHERE id_toko = '$id_toko'")->getResult();
        $discount = $this->db->query("SELECT * FROM discount WHERE id_toko = '$id_toko' AND status = 1")->getResult();
        $metode = $this->db->query("SELECT id, nama_tipe FROM tipe_bayar WHERE id_toko = '$id_toko' AND status = 1")->getResult();
        $barang = $this->db->table("barang as a")->select('a.id, a.nama_barang, a.harga_jual, a.harga_modal, a.foto, b.nama_kategori')
            ->join('kategori as b', 'a.id_kategori = b.id')->where('a.id_toko', $id_toko)->where('a.status', 1)->get()->getResult();

        // var_dump($discount);
        $toko = $this->db->query("SELECT ppn, biaya_layanan FROM toko WHERE id = '$id_toko'")->getRow();
        $bayar = $this->db->query("SELECT * FROM tipe_bayar WHERE status = 1")->getResult();
        $data_page = [
            'menu'      => 'report',
            'submenu'   => 'penjualan',
            'title'     => 'Data Penjualan',
            'pelanggan' => $pelanggan,
            'metode'    => $metode,
            'discount'  => $discount,
            'barang'    => $barang,
            'toko'      => $toko,
            'bayar'     => $bayar
        ];

        return view('Modul\Penjualan_kasir_asli\Views\viewPenjualanKasirAsli', $data_page);
    }

    public function datatable()
    {
        $id_toko = $this->session->get('id_toko');

        $builder = $this->db->table('penjualan as a')
            ->select('a.id as id, a.tgl as tgl, a.total as total, a.subtotal as subtotal, a.ppn as ppn, a.discount as discount, a.laba as laba, a.pelanggan as pelanggan, a.buktibayar as buktibayar, b.nama as nama_pelanggan, b.nohp as nohp, c.icon as icon, c.nama_tipe as nama_tipe')
            ->join('pelanggan as b', 'a.id_pelanggan = b.id', 'left')
            ->join('tipe_bayar as c', 'c.id = id_tipe_bayar')
            ->where('a.id_toko', $id_toko)
            ->where('a.delete <>', 1)
            ->orderBy('a.tgl', 'DESC');

        return DataTable::of($builder)
            ->filter(function ($builder, $request) {
                $pelanggan = $request->pelanggan;
                $metode = $request->metode;
                $tgl = $request->tgl;

                if ($pelanggan != "") {
                    $builder->where('a.id_pelanggan', $pelanggan);
                }
                if ($metode != "") {
                    $builder->where('a.id_tipe_bayar', $metode);
                }
                if ($tgl != "") {
                    $builder->where('DATE(a.tgl)', $tgl);
                }
            })
            ->addNumbering('no')
            ->setSearchableColumns(['LOWER(b.nama)', 'LOWER(a.pelanggan)'])
            ->add('metode', function ($row) {
                return '<i class="' . $row->icon . '"></i>&nbsp; ' . $row->nama_tipe . '';
            })->add('total', function ($row) {
                return 'Rp. ' . number_format($row->total);
            })->add('subtotal', function ($row) {
                return 'Rp. ' . number_format($row->subtotal);
            })->add('ppn', function ($row) {
                return 'Rp. ' . number_format($row->ppn);
            })->add('discount', function ($row) {
                if ($row->discount) {
                    return 'Rp. ' . number_format($row->discount);
                } else {
                    return '-';
                }
            })
            ->add('laba', function ($row) {
                return 'Rp. ' . number_format($row->laba);
            })->add('tgl', function ($row) {
                $tgl = new DateTime($row->tgl);
                $date = $tgl->format('d F Y, H:i');

                return $date;
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
                    ->where('a.id_penjualan', $row->id)
                    ->where('a.delete <>', 1)->get()->getResult();

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

                // return '<a href="/kasir/struk/' . base64_encode($row->id) . '" class="btn btn-light" title="Cetak Struk"><i class="fas fa-receipt"></i></a>
                // <a href="' . $whatsappLink . '" class="btn btn-light" title="Cetak Struk" target="_blank"><i class="fas fa-file-invoice"></i></a>
                // <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->pelanggan . '\')"><i class="fa fa-trash"></i></button>';

                if ($row->buktibayar != null) {
                    $disabled = '';
                } else {
                    $disabled = 'disabled';
                }

                $userEmail = $this->session->get('email');
                if ($userEmail == "supersuperadmin@mail.com") {
                    return '<div class="btn-group">
                                <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/kasir/struk/' . base64_encode($row->id) . '" title="Cetak Struk" target="_blank"><i class="fas fa-receipt me-1"></i> Cetak Struk</a></li>
                                    <li><a class="dropdown-item" href="' . $whatsappLink . '" title="Kirim Invoice" target="_blank"><i class="fab fa-whatsapp me-1"></i> Kirim Invoice</a></li>
                                    <li><a class="dropdown-item ' . $disabled . '" href="/assets/img/buktibayar/' . $row->buktibayar . '" title="Kirim Invoice"><i class="fas fa-image me-1"></i> Bukti Bayar</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0);"title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->buktibayar . '\')"><i class="fa fa-trash me-1"></i> Hapus</a></li>
                                </ul>
                            </div>';
                } else {
                    return '<div class="btn-group">
                                <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/kasir/struk/' . base64_encode($row->id) . '" title="Cetak Struk" target="_blank"><i class="fas fa-receipt me-1"></i> Cetak Struk</a></li>
                                    <li><a class="dropdown-item" href="' . $whatsappLink . '" title="Kirim Invoice" target="_blank"><i class="fab fa-whatsapp me-1"></i> Kirim Invoice</a></li>
                                    <li><a class="dropdown-item ' . $disabled . '" href="/assets/img/buktibayar/' . $row->buktibayar . '" title="Kirim Invoice"><i class="fas fa-image me-1"></i> Bukti Bayar</a></li>
                                </ul>
                            </div>';
                }
            })->add('pelanggan', function ($row) {
                if ($row->nama_pelanggan) {
                    return $row->nama_pelanggan;
                } else if ($row->pelanggan) {
                    return $row->pelanggan;
                } else {
                    return '--';
                }
            })
            ->toJson(true);
    }

    function formatProducts($products)
    {
        return implode("\n", $products);
    }

    public function hapus()
    {
        $id   = $this->request->getPost('id');
        // $foto   = $this->request->getPost('foto');

        // $path = 'assets/img/buktibayar/';
        // $unlink = @unlink($path . $foto);

        $data = [
            'delete' => 1
        ];

        $dataPenjualan = $this->db->table('penjualan')->where('id', $id);
        $dataPenjualan->update($data);

        $builder = $this->db->table('detail_penjualan as a')->where('id_penjualan', $id);
        $detailPenjualan = $builder->get()->getResult();
        $builder->update($data);

        // dd(print_r($builder->get()->getResult()));

        $this->db->transStart();        

        foreach ($detailPenjualan as $value) {
            $bahanBarang = $this->db->table('bahan_barang')->where('id_barang', $value->id_barang)->get()->getResult();

            foreach ($bahanBarang as $resep) {
                $bahanBaku = $this->db->table('bahan_baku')->where('id', $resep->id_bahan_baku)->get()->getRow();

                if ($bahanBaku) {
                    $stokBaru = $bahanBaku->stok_penjualan + ($value->qty * $resep->qty);

                    // Update stok menggunakan query builder
                    $this->db->table('bahan_baku')
                        ->where('id', $resep->id_bahan_baku)
                        ->update(['stok_penjualan' => $stokBaru]);
                }
            }
        }

        $this->db->transComplete();

        // foreach($builder->get()->getResult() as $value){
        //     $bahanBarang = $this->db->table('bahan_barang')->where('id_barang', $value->id_barang)->get()->getResult();
        //     foreach($bahanBarang as $resep){
        //         $bahanBaku = $this->db->table('bahan_baku')->where('id', $resep->id_bahan_baku)->first();
        //         $stokBaru = $bahanBaku->stok_penjualan + ($value->qty * $resep->qty);

        //         $bahanBaku->update(['stok_penjualan' => $stokBaru]);
        //     }
        // }
        // $syncBahanBaku = $builder->join('bahan_barang as b', 'b.id_barang = a.id_barang')
        // ->join('bahan_baku as c', 'c.id = b.id_bahan_baku')
        // ->select('b.qty AS qty_produksi, c.stok_penjualan');

        if ($builder) {
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

    public function exportExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $dariTanggal = $this->request->getPost('dariPenjualan');
        $sampaiTanggal = $this->request->getPost('sampaiPenjualan');

        $dariTanggalTime = date('Y-m-d H:i:s', strtotime($dariTanggal));
        $sampaiTanggalTime = date('Y-m-d H:i:s', strtotime($sampaiTanggal));

        $tanggalTerlama = $this->db->query("SELECT DATE(MIN(tgl)) AS tanggal_terlama FROM penjualan WHERE `delete` <> 1")->getResult();
        $tanggalTerbaru = $this->db->query("SELECT DATE(MAX(tgl)) AS tanggal_terbaru FROM penjualan WHERE `delete` <> 1")->getResult();
        $tanggalTerlamaResult = $tanggalTerlama[0]->tanggal_terlama;
        $tanggalTerbaruResult = $tanggalTerbaru[0]->tanggal_terbaru;

        if ($dariTanggal != "" && $sampaiTanggal != "") {
            // dd("Masok 1", $dariTanggal, $sampaiTanggal);
            $report = $this->db->query("SELECT DATE(tgl) AS date_only, COUNT(*) AS total_data, 
            SUM(total) AS total_sum, SUM(laba) AS laba_sum 
            FROM penjualan WHERE DATE(tgl) BETWEEN '$dariTanggal' AND '$sampaiTanggal' AND `delete` <> 1 GROUP BY DATE(tgl) ORDER BY DATE(tgl) ASC")->getResult();
        } else if ($dariTanggal != "" && $sampaiTanggal == "") {
            // dd("Masok 2", $dariTanggal, $sampaiTanggal);
            $report = $this->db->query("SELECT DATE(tgl) AS date_only, COUNT(*) AS total_data, 
            SUM(total) AS total_sum, SUM(laba) AS laba_sum 
            FROM penjualan WHERE DATE(tgl) BETWEEN '$dariTanggal' AND '$tanggalTerbaru' AND `delete` <> 1 GROUP BY DATE(tgl) ORDER BY DATE(tgl) ASC")->getResult();
        } else if ($dariTanggal == "" && $sampaiTanggal != "") {
            // dd("Masok 3", $dariTanggal, $sampaiTanggal);
            $report = $this->db->query("SELECT DATE(tgl) AS date_only, COUNT(*) AS total_data, 
            SUM(total) AS total_sum, SUM(laba) AS laba_sum 
            FROM penjualan WHERE DATE(tgl) BETWEEN '$tanggalTerlama' AND '$sampaiTanggal' AND `delete` <> 1 GROUP BY DATE(tgl) ORDER BY DATE(tgl) ASC")->getResult();
        } else {
            // dd("Masok 4", $dariTanggal, $sampaiTanggal);
            $report = $this->db->query("SELECT DATE(tgl) AS date_only, COUNT(*) AS total_data, 
            SUM(total) AS total_sum, SUM(laba) AS laba_sum 
            FROM penjualan WHERE `delete` <> 1 GROUP BY DATE(tgl) ORDER BY DATE(tgl) ASC")->getResult();
        }

        // $report = $this->db->query("SELECT DATE(tgl) AS date_only, COUNT(*) AS total_data, SUM(total) AS total_sum, SUM(laba) AS laba_sum FROM penjualan GROUP BY DATE(tgl) ORDER BY DATE(tgl) ASC")->getResult();

        // Menulis header kolom
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'LAPORAN HASIL FOODCOURT');
        $sheet->getStyle("A1")->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:F1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension('1')->setRowHeight(30); // Tinggi baris 1

        $sheet->mergeCells("A2:A3");
        $sheet->getStyle('A2:F3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:F3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A2")->getFont()->setBold(true);
        $sheet->setCellValue('A2', "NO");

        $sheet->mergeCells("B2:B3");
        $sheet->getStyle('B2:B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B2:B3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("B2")->getFont()->setBold(true);
        $sheet->setCellValue("B2", "TANGGAL");

        $sheet->mergeCells("C2:C3");
        $sheet->getStyle('C2:C3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:C3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("C2")->getFont()->setBold(true);
        $sheet->setCellValue("C2", "JUMLAH");

        $sheet->mergeCells("D2:D3");
        $sheet->getStyle("D2")->getFont()->setBold(true);
        $sheet->setCellValue("D2", "Total Omzet");

        $sheet->mergeCells("E2:E3");
        $sheet->getStyle("E2")->getFont()->setBold(true);
        $sheet->setCellValue("E2", "Total HPP");

        $sheet->mergeCells("F2:F3");
        $sheet->getStyle("F2")->getFont()->setBold(true);
        $sheet->setCellValue("F2", "Total Laba");

        // Mengatur lebar kolom secara otomatis
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $row = 4; // Mulai dari baris kedua, karena baris pertama digunakan untuk header
        $totalJumlah = 0;
        $totalOmzet = 0;
        $totalHPP = 0;
        $totalLaba = 0;
        foreach ($report as $key => $datum) {
            $hpp = $datum->total_sum - $datum->laba_sum;
            $sheet->setCellValue('A' . $row, $key + 1);
            $sheet->setCellValue('B' . $row, $datum->date_only);
            $sheet->setCellValue('C' . $row, $datum->total_data);
            $sheet->setCellValue('D' . $row, $datum->total_sum);
            $sheet->setCellValue('E' . $row, $hpp);
            $sheet->setCellValue('F' . $row, $datum->laba_sum);

            // Mengatur format sel menjadi Rupiah
            $sheet->getStyle('D' . $row)
                ->getNumberFormat()
                ->setFormatCode('"Rp"#,##0.00_-');

            $sheet->getStyle('E' . $row)
                ->getNumberFormat()
                ->setFormatCode('"Rp"#,##0.00_-');

            $sheet->getStyle('F' . $row)
                ->getNumberFormat()
                ->setFormatCode('"Rp"#,##0.00_-');

            $totalJumlah += $datum->total_data;
            $totalOmzet += $datum->total_sum;
            $totalHPP += $hpp;
            $totalLaba += $datum->laba_sum;

            $row++;
        }

        $sheet->mergeCells('A' . $row . ':' . 'B' . $row);
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->getStyle('A' . $row . ':' . 'F' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $sheet->getStyle('A' . $row . ':' . 'B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('C' . $row, $totalJumlah);

        $sheet->setCellValue('D' . $row, $totalOmzet);
        $sheet->getStyle('D' . $row)
            ->getNumberFormat()
            ->setFormatCode('"Rp"#,##0.00_-');

        $sheet->setCellValue('E' . $row, $totalHPP);
        $sheet->getStyle('E' . $row)
            ->getNumberFormat()
            ->setFormatCode('"Rp"#,##0.00_-');

        $sheet->setCellValue('F' . $row, $totalLaba);
        $sheet->getStyle('F' . $row)
            ->getNumberFormat()
            ->setFormatCode('"Rp"#,##0.00_-');

        // Mengatur border untuk tabel
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle('A1:F' . $row)->applyFromArray($styleArray);

        // Mengatur header HTTP untuk mengunduh file Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan Penjualan.xlsx"');
        header('Cache-Control: max-age=0');


        $writer = new Xlsx($spreadsheet);
        $writer->save("Laporan Penjualan.xlsx");

        // Menyimpan file Excel ke output
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
