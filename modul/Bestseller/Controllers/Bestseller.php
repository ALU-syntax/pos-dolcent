<?php

namespace Modul\Bestseller\Controllers;

use App\Controllers\BaseController;
use DateTime;
use Hermawan\DataTables\DataTable;

class Bestseller extends BaseController
{
    public function index()
    {
        $id_toko = $this->session->get('id_toko');

        $best = $this->db->table('varian as a')
            ->select('SUM(b.qty) as total, c.nama_satuan, d.nama_barang')
            ->join('detail_penjualan as b', 'b.id_varian = a.id')
            ->join('satuan as c', 'c.id = a.id_satuan')
            ->join('barang as d', 'd.id = a.id_barang')->groupBy('total, c.nama_satuan, d.nama_barang')->where('d.id_toko', $id_toko)->where('d.delete <>', 1)->limit(5)->orderBy('total', 'DESC')->get()->getResult();

        $data = [
            'title'       => 'Best Seller Produk',
            'menu'        => 'report',
            'submenu'     => 'bestseller',
            'best'        => $best
        ];

        return view('Modul\Bestseller\Views\viewBestseller', $data);
    }

    public function filter()
    {
        $id_toko = $this->session->get('id_toko');
        $start = $this->request->getPost('start');
        $end   = $this->request->getPost('end');

        $best = $this->db->table('barang as a')
            ->select('SUM(b.qty) as total, a.nama_barang')
            ->join('detail_penjualan as b', 'b.id_barang = a.id')->join('penjualan as c', 'c.id = b.id_penjualan', 'left')
            ->groupBy('a.nama_barang')->where('a.id_toko', $id_toko)->where('DATE(c.tgl) >=', $start)->where('DATE(c.tgl) <=', $end)->where('c.delete <>', 1)
            ->limit(5)->orderBy('total', 'DESC')->get()->getResult();

        $best = $this->db->table('varian as a')
            ->select('SUM(b.qty) as total, c.nama_satuan, d.nama_barang')
            ->join('detail_penjualan as b', 'b.id_varian = a.id')->join('penjualan as e', 'e.id = b.id_penjualan')
            ->join('satuan as c', 'c.id = a.id_satuan')
            ->join('barang as d', 'd.id = a.id_barang')->groupBy('c.nama_satuan, d.nama_barang')
            ->where('d.id_toko', $id_toko)->where('DATE(e.tgl) >=', $start)->where('DATE(e.tgl) <=', $end)->where('e.delete <>', 1)->limit(5)
            ->orderBy('total', 'DESC')->get()->getResult();

        if ($best) {
            $html = '';
            $no = 0;

            foreach ($best as $key) {
                $no++;
                if ($no == 1) {
                    $html .= '<tr>
                                <td class="number">1</td>
                                <td class="name">' . $key->nama_barang . ' - ' . $key->nama_satuan . '</td>
                                <td class="points">
                                    ' . $key->total . ' <img class="gold-medal" src="https://github.com/malunaridev/Challenges-iCodeThis/blob/master/4-leaderboard/assets/gold-medal.png?raw=true" alt="gold medal" />
                                </td>
                              </tr>';
                } else {
                    $html .= '<tr>
                                <td class="number">' . $no . '</td>
                                <td class="name">' . $key->nama_barang . ' - ' . $key->nama_satuan . '</td>
                                <td class="points">' . $key->total . '</td>
                             </tr>';
                }
            }

            $respond = [
                'status'    => true,
                'html'      => $html
            ];
        } else {
            $respond = [
                'status'    => false,
                'start'     => $start,
                'end'       => $end
            ];
        }

        echo json_encode($respond);
    }
}
