<?php

namespace Modul\Laporan_penjualan_asli\Controllers;

use App\Controllers\BaseController;
use DateTime;
use Hermawan\DataTables\DataTable;

class Laporan_penjualan_asli extends BaseController
{
    public function index()
    {
        $id_toko = $this->session->get('id_toko');
        $tgl = date('Y-m-d');

        $total    = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND DATE(tgl) = '$tgl' AND `delete` <> 1")->getRow()->total;
        $omset    = $this->db->query("SELECT SUM(total) as total FROM penjualan WHERE id_toko = '$id_toko' AND DATE(tgl) = '$tgl' AND `delete` <> 1")->getRow()->total;
        $discount = $this->db->query("SELECT SUM(discount) as total FROM penjualan WHERE id_toko = '$id_toko' AND DATE(tgl) = '$tgl' AND `delete` <> 1")->getRow()->total;
        $laba     = $this->db->query("SELECT SUM(laba) as total FROM penjualan WHERE id_toko = '$id_toko' AND DATE(tgl) = '$tgl' AND `delete` <> 1")->getRow()->total;

        $tipe     = $this->db->query("SELECT * FROM tipe_bayar WHERE id_toko = '$id_toko'")->getResult();

        $data = [
            'title'       => 'Sales Summary',
            'menu'        => 'report',
            'submenu'     => 'summary',
            'total'       => $total,
            'omset'       => $omset,
            'discount'    => $discount,
            'laba'        => $laba,
            'tipe'        => $tipe
        ];

        return view('Modul\Laporan_penjualan_asli\Views\viewLaporanPenjualanAsli', $data);
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

    public function filter()
    {
        $id_toko = $this->session->get('id_toko');

        $tgl = $this->request->getPost('tgl');

        $total    = $this->db->query("SELECT COUNT(id) as total FROM penjualan WHERE id_toko = '$id_toko' AND DATE(tgl) = '$tgl' AND `delete` <> 1")->getRow()->total;
        $omset    = $this->db->query("SELECT SUM(total) as total FROM penjualan WHERE id_toko = '$id_toko' AND DATE(tgl) = '$tgl' AND `delete` <> 1")->getRow()->total;
        $discount = $this->db->query("SELECT SUM(discount) as total FROM penjualan WHERE id_toko = '$id_toko' AND DATE(tgl) = '$tgl' AND `delete` <> 1")->getRow()->total;
        $laba     = $this->db->query("SELECT SUM(laba) as total FROM penjualan WHERE id_toko = '$id_toko' AND DATE(tgl) = '$tgl' AND `delete` <> 1")->getRow()->total;

        $tipe     = $this->db->query("SELECT * FROM tipe_bayar WHERE id_toko = '$id_toko'")->getResult();

        $html = '<h6 class="fw-bold">Ringkasan ' . $tgl . '</h6>
                    <div class="row mt-4">
                        <div class="col-md-3 col-6">
                            <p class="mb-0 fw-bold"><i class="fas fa-receipt me-2"></i>Total Transaksi</p>
                            <p>' . $total . '</p>
                        </div>
                        <div class="col-md-3 col-6">
                            <p class="mb-0 fw-bold"><i class="fas fa-money-bill me-2"></i>Total Omset</p>
                            <p>Rp. ' . number_format($omset) . '</p>
                        </div>
                        <div class="col-md-3 col-6">
                            <p class="mb-0 fw-bold"><i class="fas fa-percentage me-2"></i>Total Discount</p>
                            <p>Rp. ' . number_format($discount) . '</p>
                        </div>
                        <div class="col-md-3 col-6">
                            <p class="mb-0 fw-bold"><i class="fas fa-wallet me-2"></i>Total Laba</p>
                            <p>Rp. ' . number_format($laba) . '</p>
                        </div>
                        <div class="col-12">
                            <hr>
                    </div>';

        foreach ($tipe as $key) {
            $omsett    = $this->db->query("SELECT SUM(total) as total FROM penjualan WHERE id_toko = '$id_toko' AND DATE(tgl) = '$tgl' AND id_tipe_bayar = '$key->id' AND `delete` <> 1")->getRow()->total;
            $html .= '<div class="col-md-3 col-6">
                        <p class="mb-0 fw-bold"><i class="' . $key->icon . ' me-2"></i>' . $key->nama_tipe . '</p>
                        <p>Rp. ' . number_format($omsett) . '</p>
                    </div>';
        }

        $respond = [
            'status'    => true,
            'html'      => $html
        ];

        echo json_encode($respond);
    }
}
