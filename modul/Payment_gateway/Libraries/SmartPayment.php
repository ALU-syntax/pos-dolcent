<?php

namespace Modul\Payment_gateway\Libraries;

class SmartPayment {
    private $host;
    private $token;
    private $storeName;
    private $method = 'PaymentBELANJAKantinLuar';
    private $params = [];
    
    function __construct($host, $token, $store) {
        $this->host = $host;
        $this->token = $token;
        $this->storeName = $store;

        $this->params = [
            'method' => $this->method,
            'namakantin' => $this->storeName,
        ];
    }

    public function pay($params) {
        $this->validate($params, [
            'nominal', 
            'nokartu', 
            'pin'
        ]);
        
        return $this->send($params);
    }

    private function validate($params, $paramsKey) {
        foreach ($paramsKey as $key) {
            if(!array_key_exists($key, $params) || !$params[$key]) {
                throw new \Exception("Parameter '$key' tidak boleh kosong.", 400);
            }
        }
    }

    private function send($params) {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->host,
        ]);

        $payload = $this->encryptParams(array_merge($this->params, $params));

        $res = $client->request('POST', "token/$payload");
        $resArray = json_decode($res->getBody()->getContents(), true)[0];

        if($resArray['RESULT'] != 'OK') $this->throwError($resArray);

        return $resArray;
    }

    private function encryptParams($params) {
        $payload = [];
        foreach($params as $key => $value) {
            $payload[strtoupper($key)] = $value;
        }
        $payload['iat'] = strtotime(date('Y-m-d H:i:s'));

        return \Firebase\JWT\JWT::encode($payload, $this->token, 'HS256');
    }
    
    private function throwError($res) {
        $errors = [
            'UNKNOWN_OR_BLOCKED_CARD' => 'Nomor Kartu tidak ditemukan.',
            'Daily_Transaction_Limit_Exceeded' => 'Transaksi melebihi batas harian.',
            'Insufficient_Balance' => 'Saldo tidak mencukupi.',
        ];
        

        $message = $res['RESULT'];
        if(array_key_exists($res['RESULT'], $errors)) {
            $message = $errors[$res['RESULT']];
        }
        
        throw new \Exception($message, 400);
    }
}