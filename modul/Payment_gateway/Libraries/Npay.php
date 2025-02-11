<?php

namespace Modul\Payment_gateway\Libraries;

use GuzzleHttp\Exception\ClientException;

class Npay {
    protected $host;
    protected $apiKey;

    function __construct($host, $apiKey) {
        $this->host = $host;
        $this->apiKey = $apiKey;
    }

    public function getMe() {
        $resp = $this->send('GET', 'api/v1/merchant/me');
        return $resp['data'];
    }

    public function getCardToken(string $id, string $pin)
    {
        $resp = $this->send('POST', 'api/v1/merchant/card/token', [
            'id' => $id,
            'pin' => $pin
        ]);
        
        return $resp['data'];
    }

    public function createTransaction(array $params, string $cardToken) {
        $resp = $this->send('POST', 'api/v1/merchant/transactions', [
            'amount' => $params['amount'],
            'ref' => $params['ref'],
            'token' => $cardToken
        ]);

        return $resp['data'];
    }

    public function findTransaction(string $ref) {
        $resp = $this->send('GET', 'api/v1/merchant/transactions', ['ref' => $ref]);
        return $resp['data'];
    }

    private function send(string $method, string $path, array $payload=null) {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->host,
        ]);

        $payloadKey = 'json';
        switch (strtoupper($method)) {
            case 'GET':
                $payloadKey = 'query';
                break;
            
            default:
                break;
        }

        try {
            $resp = $client->request($method, $path, [
                'headers' => [
                    'Authorization' => $this->apiKey,
                    'Accept' => 'application/json'
                ],
                $payloadKey => $payload,
                'on_stats' => function ($stats) use (&$url) {
                    $url = $stats->getEffectiveUri();
                }
            ]);

            return json_decode($resp->getBody(), true);
        } catch(ClientException $e) {
            $respBody = json_decode($e->getResponse()->getBody(), true);
            throw new \Exception($respBody['message'], $e->getCode());
        }
    }
}