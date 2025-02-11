<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Tasks.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Tasks\Scheduler;
use Modul\Kasir\Controllers\Kasir;
use Modul\Whatsapp\Libraries\OneSender;
use CodeIgniter\Tasks\Config\Tasks as BaseTasks;


class Tasks extends BaseTasks
{
    public function __construct() {
        $this->session    = Services::session();
        $this->db         = Database::connect();
    }

    /**
     * --------------------------------------------------------------------------
     * Should performance metrics be logged
     * --------------------------------------------------------------------------
     *
     * If true, will log the time it takes for each task to run.
     * Requires the settings table to have been created previously.
     */
    public bool $logPerformance = false;

    /**
     * --------------------------------------------------------------------------
     * Maximum performance logs
     * --------------------------------------------------------------------------
     *
     * The maximum number of logs that should be saved per Task.
     * Lower numbers reduced the amount of database required to
     * store the logs.
     */
    public int $maxLogsPerTask = 10;

    function formatProducts($products)
    {
        return implode("\n", $products);
    }

    /**
     * Register any tasks within this method for the application.
     * Called by the TaskRunner.
     */
    public function init(Scheduler $schedule)
    {
        $db = Database::connect();
        // $schedule->command('foo:bar')->daily();
        $schedule->call(function () use ($db) {

            //kosongkan list table preorder notif untuk hari ini
            $notif = $db->table('notification');
            $notif->truncate();

            // Mendapatkan tanggal besok
            $builder = $db->table('penjualan');
            $tomorrow = date('Y-m-d', strtotime('+1 day'));

            // Query untuk mendapatkan pesanan H-1
            $builder->where('DATE(tanggal_preorder)', $tomorrow);
            $builder->where('status_preorder', 0);
            $orders = $builder->get()->getResult();
            // $result = $db->query("SELECT * FROM penjualan WHERE tipe_pesanan = 2 AND status_preorder = 0")->getResult();
            if(count($orders) > 0){
                foreach($orders as $order){
                    $data = [
                        'id_pesanan' => $order->id,
                        'status'     => 0,
                    ];
                    
                    $db->table('notification')->insert($data);               

                    $id_toko = 1;
                    $id_penjualan = $order->id;
                    
                    $row = $db->query("SELECT a.id as id, a.tgl as tgl, a.total as total, a.subtotal as subtotal, a.ppn as ppn, a.discount as discount, a.laba as laba, a.pelanggan as pelanggan, a.buktibayar as buktibayar, b.nama as nama_pelanggan, b.nohp as nohp, c.icon as icon, c.nama_tipe as nama_tipe
                        FROM penjualan as a
                        LEFT JOIN pelanggan as b ON a.id_pelanggan = b.id
                        JOIN tipe_bayar as c ON c.id = id_tipe_bayar
                        WHERE a.id_toko = $id_toko
                        AND a.id = $id_penjualan
                        AND a.delete <> 1
                    ")->getRow();


                    $orderNumber = $row->id;
                    $orderDate = $row->tgl;

                    $products = [];
                    $detail = $db->table("detail_penjualan as a")
                        ->select("a.qty, b.nama_barang, d.nama_satuan")
                        ->join("barang as b", "b.id = a.id_barang")
                        ->join("varian as c", "c.id = a.id_varian", "left")
                        ->join("satuan as d", "d.id = c.id_satuan", "left")
                        ->where('a.id_penjualan', $row->id)
                        ->where('a.delete <>', 1)
                        ->get()
                        ->getResult();

                        
                    foreach ($detail as $key) {
                        $prod = $key->qty . 'x ' . $key->nama_barang . ' - ' . $key->nama_satuan;
                        array_push($products, $prod);
                    }
                    
                    // $subtotal = "Rp " . number_format($row->subtotal);
                    // $total = "Rp " . number_format($row->total);
                    // $paymentMethod = $row->nama_tipe . ": Rp " . number_format($row->total);

                    $subtotal = "Rp " . $row->subtotal;
                    $total = "Rp " . $row->total;
                    $paymentMethod = $row->nama_tipe . ": Rp " . $row->total;

                    
                    $message = "Halo, berikut ini adalah Pesanan Preorder Hari ini:\n\nOrder from " . $row->nama_pelanggan . "\n*{$orderNumber}* ({$orderDate})\n\nProduct:\n" . $this->formatProducts($products) . "\n\nSubtotal: {$subtotal}\n\nTotal: {$total}\n\nPayment:\n{$paymentMethod}\n\nThank you for shopping with us";
                    // $message = "Test";
                    $oneSenderSetting = $db->query("SELECT * FROM onesender WHERE id_toko = '$id_toko'")->getRow();
                    $oneSender = new OneSender($oneSenderSetting->host, $oneSenderSetting->key);
                    $send = $oneSender->sendText("6285784753102", $message);
                    $sendTwo = $oneSender->sendText("6285161529485", $message);
                }
            }
        })->daily();

        // $schedule->shell('cp foo bar')->daily('11:00 pm');

        //        $schedule->call(static function () {
        //            // do something....
        //        })->mondays()->named('foo');
    }
}
