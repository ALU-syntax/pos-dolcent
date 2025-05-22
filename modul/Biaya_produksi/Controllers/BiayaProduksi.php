<?php 
namespace Modul\Biaya_produksi\Controllers;

use App\Controllers\BaseController;
use Hermawan\DataTables\DataTable;
use Modul\Bahan\Models\Model_bahan;
use Modul\Bahan\Models\Model_stok_bahan;
use Modul\Biaya_produksi\Models\Model_biaya_produksi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BiayaProduksi extends BaseController{

    public function __construct(){
        $this->biayaProduksi = new Model_biaya_produksi();
        $this->bahan = new Model_bahan();
        $this->stok_bahan = new Model_stok_bahan();
    }

    public function index(){
        $id_toko = $this->session->get('id_toko');

        $biayaProduksi = $this->db->query("SELECT * FROM biaya_produksi ORDER BY tanggal ASC")->getResult();

        //ini report dengan filter transaksi dihapus
        // $report = $this->db->query("SELECT b.id, b.nama_barang AS nama_barang, b.harga_jual AS harga_jual, b.harga_modal AS harga_modal,
        //      SUM(CASE WHEN dp.id_barang = b.id THEN dp.total ELSE 0 END) AS total_pendapatan,
        //      SUM(CASE WHEN dp.id_barang = b.id THEN dp.qty ELSE 0 END) AS qty_terjual FROM barang b 
        //      LEFT JOIN detail_penjualan dp ON dp.id_barang = b.id LEFT JOIN penjualan p ON dp.id_penjualan = p.id WHERE dp.delete <> 1
        //      GROUP BY b.id, b.nama_barang, b.harga_jual, b.harga_modal")->getResult();    

        //ini report untuk seluruh transaksi baik yang dihapus maupun tidak
        $report = $this->db->query("SELECT b.id, b.nama_barang AS nama_barang, b.harga_jual AS harga_jual, b.harga_modal AS harga_modal,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.total ELSE 0 END) AS total_pendapatan,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.qty ELSE 0 END) AS qty_terjual FROM barang b 
             LEFT JOIN detail_penjualan dp ON dp.id_barang = b.id LEFT JOIN penjualan p ON dp.id_penjualan = p.id
             GROUP BY b.id, b.nama_barang, b.harga_jual, b.harga_modal")->getResult();    

        $totalHpp = 0;
        foreach($report as $key => $data){
            $hpp = $data->harga_modal * $data->qty_terjual;

            $totalHpp += $hpp;
        }

        $totalNominalBiayaProduksi = 0;
        foreach($biayaProduksi as $data){
            $totalNominalBiayaProduksi += $data->nominal;
        }

        $bahanBaku = $this->db->query("SELECT * FROM bahan_baku WHERE id_toko = '$id_toko' AND status = 1 ORDER BY nama_bahan ASC")->getResult();
        $data_page = [
            'menu'      => 'pencatatan',
            'submenu'   => 'biaya-produksi',
            'title'     => 'Data Biaya Produksi',
            'biaya_produksi'  => $biayaProduksi,
            'balance' => $totalHpp,
            'nominal_biaya_produksi' => $totalNominalBiayaProduksi,
            'bahan_baku' => $bahanBaku
            // 'pelanggan' => $pelanggan
        ];

        return view('Modul\Biaya_produksi\Views\viewBiayaProduksi', $data_page);

    }

    public function datatable()
    {
        $id_toko = $this->session->get('id_toko');
        $startDate = $this->request->getPost('dari');
        $endDate = $this->request->getPost('sampai');
        $tanggalAwal = $this->db->query("SELECT DATE(MIN(tanggal)) AS tanggal_awal FROM biaya_produksi")->getResult();
        $tanggalAkhir = $this->db->query("SELECT DATE(MAX(tanggal)) AS tanggal_akhir FROM biaya_produksi")->getResult();
        $tanggalAwalResult = $tanggalAwal[0]->tanggal_awal;
        $tanggalAkhirResult = $tanggalAkhir[0]->tanggal_akhir;
    
        $builder = $this->db->table('biaya_produksi as b')
            ->join('bahan_baku as bb', 'bb.id = b.id_bahan', 'left')
            ->select('b.id as id, b.nominal as nominal, b.deskripsi as deskripsi, b.foto as foto, b.tanggal as tanggal, b.quantity AS quantity, bb.nama_bahan AS nama_bahan')
            ->where('b.id_toko', $id_toko)
            ->where('b.deleted_at IS NULL', null, false)
            ->orderBy('b.id', 'DESC');

        if ($startDate != "" && $endDate != "") {
            $builder->where('b.tanggal >=', $startDate);
            $builder->where('b.tanggal <=', $endDate);
        }else if($startDate != "" && $endDate == ""){
            $builder->where('b.tanggal >=', $startDate);
        }else if($startDate == "" && $endDate != ""){ 
            $builder->where('b.tanggal <=', $endDate);
        }
        

        return DataTable::of($builder)
            ->addNumbering('no')
            ->setSearchableColumns(['LOWER(b.deskripsi)'])
            ->add('action', function ($row) {
                return '<button type="button" class="btn btn-light" title="Edit Data" onclick="edit(\'' . $row->id . '\')"><i class="fa fa-edit"></i></button>
                <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->foto . '\')"><i class="fa fa-trash"></i></button>';
            })->add('nominal', function ($row) {
                return 'Rp. ' . number_format($row->nominal);
            })->add('foto', function ($row) {
                if ($row->foto) {
                    return '<image data-fancybox data-src="/assets/img/biaya-produksi/' . $row->foto . '" src="/assets/img/biaya-produksi/' . $row->foto . '" height="70" style="cursor: zoom-in; border-radius: 5px;"/>';
                } else {
                    return '<image src="/assets/img/noimage.png" height="70" style="cursor: zoom-in;"/>';
                }
            })
            ->toJson(true);
    }

    public function simpan(){

        $rules = $this->validate([
            'nominal' => [
                'label' => 'Nominal',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} harus diisi!'
                ]
            ],
            'deskripsi' => [
                'label' => 'Deskripsi',
                'rules' => 'permit_empty',
            ],
            'foto' => [
                'label' => 'Foto',
                'rules' => 'max_size[foto, 1024]|ext_in[foto,jpg,png,jpeg]',
                'errors' => [
                    'max_size' => 'Ukuran {field} terlalu besa!',
                    'ext_in' => '{field} harus JPG, PNG atau JPEG!'
                ]
            ],
            'tanggal' => [
                'label' => 'Tanggal',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} harus diisi!'
                ]
            ],
            'id_bahan' => [
                'label' => 'Nama Bahan',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} harus diisi!'
                ]
            ],
            'quantity' => [
                'label' => 'Quantity',
                'rules' => 'required|integer|greater_than[0]',
                'errors' => [
                    'required' => '{field} harus diisi!',
                    'integer' => '{field} harus berupa angka bulat!',
                    'greater_than' => '{field} minimal adalah 1!'
                ]
            ],
            'biaya_lain' => [
                'label' => 'Biaya lain-lain',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} harus diisi!'
                ]
            ],
            'biaya_pengiriman' => [
                'label' => 'Biaya Pengiriman',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} harus diisi!'
                ]
            ]    
        ]);

        if(!$rules){
            $errors = [
                'nominal' => $this->validation->getError('nominal'),
                'deskripsi' => $this->validation->getError('deskripsi'),
                'foto' => $this->validation->getError('foto'),
                'tanggal' => $this->validation->getError('tanggal'),
                'id_bahan' => $this->validation->getError('id_bahan'),
                'quantity' => $this->validation->getError('quantity'),
                'biaya_lain' => $this->validation->getError('biaya_lain'),
                'biaya_pengiriman' => $this->validation->getError('biaya_pengiriman')
            ];

            $respond = [
                'status' => FALSE,
                'errors' => $errors
            ];
        }else{
            $id = $this->request->getPost('id');
            $id_stok_bahan_baku = $this->request->getPost('id_stok_bahan_baku');
            $id_toko = $this->session->get('id_toko');
            $nominal = $this->request->getPost('nominal');
            $deskripsi = $this->request->getPost('deskripsi');
            $tanggal = $this->request->getPost('tanggal');
            $foto = $this->request->getFile("foto");
            $idBahan = $this->request->getPost('id_bahan');
            $quantity = $this->request->getPost('quantity');
            $biayaLain = $this->request->getPost('biaya_lain');
            $biayaPengiriman = $this->request->getPost('biaya_pengiriman');

            $data = [
                'id' => $id,
                'id_toko' => $id_toko,
                'nominal' => getAmount($nominal),
                'deskripsi' => $deskripsi,
                'tanggal' => $tanggal,
                'id_bahan' => $idBahan,
                'quantity' => $quantity,
                'biaya_lain' => getAmount($biayaLain),
                'biaya_pengiriman' => getAmount($biayaPengiriman)
            ];

            if($foto->isValid() && !$foto->hasMoved()){
                $namafile = $foto->getRandomName();
                $foto->move(ROOTPATH . 'public/assets/img/biaya-produksi/', $namafile);

                if($id){
                    $foto = $this->db->table('biaya_produksi')->select('foto')->where('id', $id)->get()->getRow();
                    $path = 'assets/img/biaya-produksi';
                    $unlink = @unlink($path . $foto->foto);
                }
                
                $data['foto'] = $namafile;
            }

            if($id){
                $dataLama = $this->biayaProduksi->find($id);
                $stokLama = $dataLama['quantity'];
            } else {
                $stokLama = 0; // Jika insert baru, stok lama dianggap 0
            }

            $save = $this->biayaProduksi->save($data);

            if($save){
                $lastInsertId = $id ? $id : $this->biayaProduksi->insertID();

                if($id){
                    $bahan = $this->bahan->find($idBahan);
                                    // Hitung selisih quantity lama dan baru
                    $selisih = $stokLama - $quantity;

                    

                    if ($selisih > 0) {
                        // Quantity lama lebih besar, berarti stok harus bertambah (dikurangi pembelian sebelumnya)
                        $bahan['stok'] -= $selisih;
                        $bahan['stok_penjualan'] -= $selisih;
                    } else if ($selisih < 0) {
                        // Quantity baru lebih besar, berarti stok harus berkurang (ada pembelian tambahan)
                        $bahan['stok'] -= $selisih; // selisih negatif, jadi stok berkurang
                        $bahan['stok_penjualan'] -= $selisih;
                    }
                    // Jika selisih == 0, tidak ada perubahan stok

                    $this->bahan->update($idBahan, $bahan);

                    $dataStokBahanBaku = [
                        'id' => $id_stok_bahan_baku,
                        'id_bahan'     => $idBahan,
                        'tanggal'       => $tanggal,
                        'jumlah'        => $quantity,
                        'tipe'          => 1,
                        'id_biaya_produksi' => $lastInsertId
                    ];

                    $this->stok_bahan->save($dataStokBahanBaku);

                    $respond = [
                        'status' => TRUE,
                        'notif' => "Data Berhasil diperbaharui",
                        'dataLama' => $dataLama
                    ];
                } else{
                    $dataStokBahanBaku = [
                        'id_bahan'     => $idBahan,
                        'tanggal'       => $tanggal,
                        'jumlah'        => $quantity,
                        'tipe'          => 1,
                        'id_biaya_produksi' => $lastInsertId
                    ];

                    $this->stok_bahan->save($dataStokBahanBaku);
                    
                    $bahan = $this->bahan->find($idBahan);

                     // Update kolom yang diinginkan
                    $bahan['stok'] += $quantity;
                    $bahan['stok_penjualan'] += $quantity;

                    // Simpan perubahan ke database
                    $this->bahan->update($idBahan, $bahan);

                    $respond = [
                        'status' => TRUE,
                        'notif' => "Data Berhasil ditambahkan",
                    ];
                }

            } else{
                $respond = [
                   'status' => FALSE 
                ];
            }
        }
        echo json_encode($respond);
        
    }

    public function getdata(){
        $id = $this->request->getPost('id');

        $data = $this->db->table('biaya_produksi b')
            ->join('stok_bahan_baku as bb', 'bb.id_biaya_produksi = b.id', 'left')
            ->where('b.id', $id)
            ->select('b.*, bb.id AS id_stok_bahan_baku')
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

    public function hapus(){
        $id   = $this->request->getPost('id');
        $foto = $this->request->getPost('foto');

        // Unlink IMG
        $path = 'assets/img/biaya-produksi/';
        $unlink = @unlink($path . $foto);

        $data = $this->biayaProduksi->find($id);
        
        if ($this->biayaProduksi->delete($id)) {
            $response = [
                'status' => true,
                'data' => $data,
            ];
        } else {
            $response = [
                'status' => false,
            ];
        }

        echo json_encode($response);
    }

    public function fetchBalance(){
        $dariTanggal = $this->request->getPost("dari");
        $sampaiTanggal = $this->request->getPost("sampai");
        $id_toko = $this->session->get('id_toko');

        $dariTanggalTime = date('Y-m-d H:i:s', strtotime($dariTanggal));
        $sampaiTanggalTime = date('Y-m-d H:i:s', strtotime($sampaiTanggal));

        // dd($dariTanggalTime, $sampaiTanggalTime);
        $tanggalTerlama = $this->db->query("SELECT DATE(MIN(tgl)) AS tanggal_terlama FROM penjualan WHERE `delete` <> 1")->getResult();
        $tanggalTerbaru = $this->db->query("SELECT DATE(MAX(tgl)) AS tanggal_terbaru FROM penjualan WHERE `delete` <> 1")->getResult();

        // dd($tanggalTerbaru[0]->tanggal_terbaru);
        if($dariTanggal != "" && $sampaiTanggal != ""){
            // dd("MASOK 1", $dariTanggal, $sampaiTanggal);
            // dd("MASOK 1", $dariTanggalTime, $sampaiTanggalTime);
            $report = $this->db->query("SELECT b.id, b.nama_barang AS nama_barang, b.harga_jual AS harga_jual, b.harga_modal AS harga_modal,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.total ELSE 0 END) AS total_pendapatan,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.qty ELSE 0 END) AS qty_terjual FROM barang b 
             LEFT JOIN detail_penjualan dp ON dp.id_barang = b.id LEFT JOIN penjualan p ON dp.id_penjualan = p.id 
             WHERE p.tgl BETWEEN '$dariTanggalTime' AND '$sampaiTanggalTime' AND p.delete <> 1 GROUP BY b.id, b.nama_barang, b.harga_jual, b.harga_modal")->getResult();   
 
        }else if($dariTanggal != "" && $sampaiTanggal == ""){
            // dd("MASOK 2", $dariTanggal, $sampaiTanggal);
            // dd("MASOK 2", $dariTanggalTime, $sampaiTanggalTime);
            $report = $this->db->query("SELECT b.id, b.nama_barang AS nama_barang, b.harga_jual AS harga_jual, b.harga_modal AS harga_modal,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.total ELSE 0 END) AS total_pendapatan,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.qty ELSE 0 END) AS qty_terjual FROM barang b 
             LEFT JOIN detail_penjualan dp ON dp.id_barang = b.id LEFT JOIN penjualan p ON dp.id_penjualan = p.id 
             WHERE p.tgl BETWEEN '$dariTanggalTime' AND (SELECT MAX(tgl) AS tanggal_terbaru FROM penjualan) AND p.delete <> 1 GROUP BY b.id, b.nama_barang, b.harga_jual, b.harga_modal")->getResult();    

        }else if($dariTanggal == "" && $sampaiTanggal != ""){
            // dd("MASOK 3", $dariTanggal, $sampaiTanggal);
            // dd("MASOK 3", $dariTanggalTime, $sampaiTanggalTime);
            $report = $this->db->query("SELECT b.id, b.nama_barang AS nama_barang, b.harga_jual AS harga_jual, b.harga_modal AS harga_modal,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.total ELSE 0 END) AS total_pendapatan,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.qty ELSE 0 END) AS qty_terjual FROM barang b 
             LEFT JOIN detail_penjualan dp ON dp.id_barang = b.id LEFT JOIN penjualan p ON dp.id_penjualan = p.id 
             WHERE p.tgl BETWEEN (SELECT MIN(tgl) AS tanggal_terlama FROM penjualan) AND '$sampaiTanggalTime' AND p.delete <> 1 GROUP BY b.id, b.nama_barang, b.harga_jual, b.harga_modal")->getResult();    

        }else{
            // dd("MASOK 4", $dariTanggal, $sampaiTanggal);
            // dd("MASOK 4", $dariTanggalTime, $sampaiTanggalTime);
            $report = $this->db->query("SELECT b.id, b.nama_barang AS nama_barang, b.harga_jual AS harga_jual, b.harga_modal AS harga_modal,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.total ELSE 0 END) AS total_pendapatan,
             SUM(CASE WHEN dp.id_barang = b.id THEN dp.qty ELSE 0 END) AS qty_terjual FROM barang b 
             LEFT JOIN detail_penjualan dp ON dp.id_barang = b.id LEFT JOIN penjualan p ON dp.id_penjualan = p.id WHERE p.delete <> 1
             GROUP BY b.id, b.nama_barang, b.harga_jual, b.harga_modal")->getResult();    

        }

        $nominalBiayaProduksi = $this->db->table('biaya_produksi as b')
            ->select('b.id as id, b.nominal as nominal, b.deskripsi as deskripsi, b.foto as foto, b.tanggal as tanggal')
            ->where('b.id_toko', $id_toko)
            ->orderBy('b.id', 'DESC');

        if ($dariTanggal != "" && $sampaiTanggal != "") {
            $nominalBiayaProduksi->where('b.tanggal >=', $dariTanggal);
            $nominalBiayaProduksi->where('b.tanggal <=', $sampaiTanggal);
        }else if($dariTanggal != "" && $sampaiTanggal == ""){
            $nominalBiayaProduksi->where('b.tanggal >=', $dariTanggal);
        }else if($dariTanggal == "" && $sampaiTanggal != ""){ 
            $nominalBiayaProduksi->where('b.tanggal <=', $sampaiTanggal);
        }

        $totalHpp = 0;
        foreach($report as $key => $data){
            $hpp = $data->harga_modal * $data->qty_terjual;

            $totalHpp += $hpp;
        }

        $nominalBiayaProduksiResult = $nominalBiayaProduksi->get()->getResult();
        $totalNominalBiayaProduksi = 0;
        foreach($nominalBiayaProduksiResult as $data){
            $totalNominalBiayaProduksi += $data->nominal;
        }


        if ($report || $nominalBiayaProduksi) {
            $response = [
                'status' => TRUE,
                'balance'   => $totalHpp,
                'nominalBiayaProduksi' => $totalNominalBiayaProduksi
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

        $dariTanggal = $this->request->getPost('startDateBiayaProduksi');
        $sampaiTanggal = $this->request->getPost('endDateBiayaProduksi');

        // dd($dariTanggalTime, $sampaiTanggalTime);
        $tanggalTerlama = $this->db->query("SELECT DATE(MIN(tgl)) AS tanggal_terlama FROM penjualan")->getResult();
        $tanggalTerbaru = $this->db->query("SELECT DATE(MAX(tgl)) AS tanggal_terbaru FROM penjualan")->getResult();
        $tanggalTerlamaResult = $tanggalTerlama[0]->tanggal_terlama;
        $tanggalTerbaruResult = $tanggalTerbaru[0]->tanggal_terbaru;

        // dd($tanggalTerbaru[0]->tanggal_terbaru);
        if($dariTanggal != "" && $sampaiTanggal != ""){
            // dd("MASOK 1", $dariTanggal, $sampaiTanggal);
            $report = $this->db->query("SELECT DATE(tgl) as tanggal, SUM(total) as jumlah_total, SUM(laba) as jumlah_laba 
                FROM penjualan WHERE DATE(penjualan.tgl) BETWEEN '$dariTanggal'
                AND '$sampaiTanggal' AND `delete` <> 1
                GROUP BY DATE(tgl) ORDER BY DATE(tgl)")->getResult();   

            $biayaProduksi = $this->db->query("SELECT tanggal, SUM(nominal) AS total_nominal FROM biaya_produksi WHERE tanggal BETWEEN 
                '$dariTanggal' AND '$sampaiTanggal' GROUP BY tanggal")->getResult();
             
        }else if($dariTanggal != "" && $sampaiTanggal == ""){
            // dd("MASOK 2", $dariTanggal, $sampaiTanggal);
            $report = $this->db->query("SELECT DATE(tgl) as tanggal, SUM(total) as jumlah_total, SUM(laba) as jumlah_laba 
                FROM penjualan WHERE DATE(penjualan.tgl) BETWEEN '$dariTanggal'
                AND (SELECT DATE(MAX(tgl)) AS tanggal_terbaru FROM penjualan) AND `delete` <> 1
                GROUP BY DATE(tgl) ORDER BY DATE(tgl)")->getResult();    

            $biayaProduksi = $this->db->query("SELECT tanggal, SUM(nominal) AS total_nominal FROM biaya_produksi WHERE tanggal BETWEEN 
                '$dariTanggal' AND (SELECT DATE(MAX(tgl)) AS tanggal_terbaru WHERE `delete` <> 1 FROM penjualan) GROUP BY tanggal")->getResult();

        }else if($dariTanggal == "" && $sampaiTanggal != ""){
            // dd("MASOK 3", $dariTanggal, $sampaiTanggal);
            $report = $this->db->query("SELECT DATE(tgl) as tanggal, SUM(total) as jumlah_total, SUM(laba) as jumlah_laba 
                FROM penjualan WHERE DATE(penjualan.tgl) BETWEEN (SELECT DATE(MIN(tgl)) AS tanggal_terlama FROM penjualan)
                AND '$sampaiTanggal' AND `delete` <> 1
                GROUP BY DATE(tgl) ORDER BY DATE(tgl)")->getResult();    

            $biayaProduksi = $this->db->query("SELECT tanggal, SUM(nominal) AS total_nominal FROM biaya_produksi WHERE tanggal BETWEEN 
                (SELECT DATE(MIN(tgl)) AS tanggal_terlama FROM penjualan WHERE `delete` <> 1) AND '$sampaiTanggal' GROUP BY tanggal")->getResult();

        }else{
            // dd("MASOK 4", $dariTanggal, $sampaiTanggal);
            $report = $this->db->query("SELECT DATE(tgl) as tanggal, SUM(total) as jumlah_total, SUM(laba) as jumlah_laba 
                FROM penjualan WHERE `delete` <> 1 GROUP BY DATE(tgl) ORDER BY DATE(tgl)")->getResult();    

            $biayaProduksi = $this->db->query("SELECT tanggal, SUM(nominal) AS total_nominal
                    FROM biaya_produksi
                    GROUP BY tanggal;
                    ")->getResult();

        }

        $sheet->mergeCells('A1:D3');
        $sheet->setCellValue('A1', "LAPORAN BIAYA PRODUKSI");
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00A300'); 
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:D1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->mergeCells('A4:A5');
        $sheet->setCellValue('A4', "TANGGAL");
        $sheet->getStyle('A4')->getFont()->setBold(true);
        $sheet->getStyle('A4:A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:A5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->mergeCells('B4:B5');
        $sheet->setCellValue('B4', "SALDO HPP");
        $sheet->getStyle('B4')->getFont()->setBold(true);
        $sheet->getStyle('B4:B5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B4:B5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->mergeCells('C4:C5');
        $sheet->setCellValue('C4', "BIAYA PRODUKSI");
        $sheet->getStyle('C4')->getFont()->setBold(true);
        $sheet->getStyle('C4:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C4:C5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->mergeCells('D4:D5');
        $sheet->setCellValue('D4', "SISA SALDO");
        $sheet->getStyle('D4')->getFont()->setBold(true);
        $sheet->getStyle('D4:D5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D4:D5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getColumnDimension('A')->setWidth(20); // Lebar kolom A
        $sheet->getColumnDimension('B')->setWidth(30); // Lebar kolom B
        $sheet->getColumnDimension('C')->setWidth(30); // Lebar kolom C
        $sheet->getColumnDimension('D')->setWidth(30); // Lebar kolom D

        $tanggalReport = [];
        $tanggalBiayaProduksi = [];
        foreach($report AS $data){
            array_push($tanggalReport, $data->tanggal);
        }

        foreach($biayaProduksi AS $data){
            array_push($tanggalBiayaProduksi, $data->tanggal);
        }

        // Menggabungkan kedua array
        $tanggalGabungan = array_merge($tanggalBiayaProduksi, $tanggalReport);

        // Menghilangkan duplikat
        $tanggalUnik = array_unique($tanggalGabungan);

        // Mengurutkan array
        sort($tanggalUnik);

        // dd($tanggalUnik, $report, $biayaProduksi);

        $row = 6;   

        $dataResult = [];

        foreach($tanggalUnik as $tglUnik){
            $dataResult[$tglUnik] = [];
        }


        foreach($dataResult AS $key=>$data){
            foreach($report AS $dataReport){
                if($key == $dataReport->tanggal){
                    $saldoHpp = intval($dataReport->jumlah_total) - intval($dataReport->jumlah_laba);
                    $dataResult[$key] = array_merge( $dataResult[$key], [
                        'saldoHpp' => $saldoHpp
                    ]);
                    
                }
            }

            foreach($biayaProduksi AS $dataProduksi){
                if($key == $dataProduksi->tanggal){
                    $dataResult[$key] = array_merge($dataResult[$key], [
                        'biayaProduksi' => $dataProduksi->total_nominal
                    ]);
                }
                
            }
        }

        // dd($dataResult, $report);

        foreach($dataResult as $key=>$data){
            $saldoHpp = (isset($data['saldoHpp'])) ? $data['saldoHpp'] : 0;
            $saldoBiayaProduksi = (isset($data['biayaProduksi'])) ? $data['biayaProduksi'] : 0;
            $sisaSaldo = $saldoHpp - $saldoBiayaProduksi;
            $sheet->setCellValue('A' . $row, $key);
            $sheet->setCellValue('B' . $row, $saldoHpp);
            $sheet->setCellValue('C' . $row, $saldoBiayaProduksi);
            $sheet->setCellValue('D' . $row, $sisaSaldo);

            $sheet->getStyle('B' . $row . ':' . 'D' . $row)
                ->getNumberFormat()
                ->setFormatCode('"Rp"#,##0.00_-');

            $row++;
        }

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle('A1:D' . ($row-1))->applyFromArray($styleArray);

        // Mengatur header HTTP untuk mengunduh file Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan Biaya Produksi.xlsx"');
        header('Cache-Control: max-age=0');
        

        $writer = new Xlsx($spreadsheet);
        $writer->save("Laporan Biaya Produksi.xlsx");

        // Menyimpan file Excel ke output
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;

    }
    
}