<?php

namespace App\Services;

use GuzzleHttp\Client;

class PayUService
{
    protected $client;
    protected $apiLogin;
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiLogin = config('payu.api_login', env('PAYU_API_LOGIN'));
        $this->apiKey = config('payu.api_key', env('PAYU_API_KEY'));
        $this->apiUrl = config('payu.api_url', env('PAYU_API_URL'));
    }

    public function createToken(array $data)
    {
        $requestData = [
            'language' => 'es',
            'command' => 'CREATE_TOKEN',
            'merchant' => [
                'apiLogin' => $this->apiLogin,
                'apiKey' => $this->apiKey,
            ],
            'creditCardToken' => $data
        ];

        $response = $this->client->post($this->apiUrl, [
            'json' => $requestData
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
