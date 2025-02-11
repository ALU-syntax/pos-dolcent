<?php

namespace Modul\Whatsapp\Libraries;

use Illuminate\Support\Facades\Http;
use Propaganistas\LaravelPhone\PhoneNumber;

class OneSender {
    private $host;
    private $key;

    function __construct($host, $key)
    {
        $this->host = $host;
        $this->key = $key;
    }

    public function sendText($to, $message) {
        $params = $this->_messagePayload($to, $message);
        return $this->send('api/v1/messages', $params);
    }

    public function sendTextBulk($data=[]) {
        $params = [];
        foreach($data as $d) {
            $params[] = $this->_messagePayload($d['to'], $d['message']);
        }
        
        return $this->send('api/v1/messages', $params);
    }
    
    public function sendImage($to, $imageLink){
        $params = $this->_messageImagePayload($to, $imageLink);
        return $this->send('api/v1/messages', $params);
    }

    private function _messagePayload($to, $message) {
        return [
            "recipient_type" => "individual",
            "to" => $to,
            "type" => "text",
            "text" => [
                "body" => $message,
            ],
        ];
    }
    
    private function _messageImagePayload($to, $linkImage){
        return [
            "recipient_type" => "individual",
            "to" => $to,
            "type" => "image",
            "image" => [
                "link" => "https://food.tukir.biz.id/assets/img/invoice/"  . $linkImage . ".png",
                "caption" => "Terima kasih, telah berbelanja di foodcourt, berikut adalah lampiran transaksi anda,

Jika anda belum melakukan pembayaran, silahkan untuk melakukan pembayaran ke rekening berikut:

A.n Foodcourt
1352615161

Silahkan kirim kesini bukti transfernya"
                ]
            ];
    }

    private function send($path, $params) {
        if(!$this->key) return false;

        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->host,
        ]);

        try {
            $client->request('POST', $path, [
                'json' => $params,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->key,
                ]
            ]);
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }
}