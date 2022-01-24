<?php

namespace VeChainSDK;

class VeChainSDK
{
    protected $app_id, $app_key, $operator_uid;

    public function __construct($app_id, $app_key, $operator_uid)
    {
        $this->app_id       = $app_id;
        $this->app_key      = $app_key;
        $this->operator_uid = $operator_uid;
    }

    public function createToken()
    {
        $nonce            = time() * 1000000;
        $timestamp        = time();
        $signature_string = 'appid=' . $this->app_id . '&appkey=' . $this->app_key . '&nonce=' . $nonce . '&timestamp=' . $timestamp;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://developer.vetoolchain.com/api/v2/tokens');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'appid'     => $this->app_id,
            'nonce'     => $nonce,
            'signature' => hash('sha256', $signature_string),
            'timestamp' => $timestamp,
            'source'    => 'production_thread_x'
        ]));

        $headers   = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $response = json_decode($response);

        $ve_chain_response_log = 've chain token response...' . PHP_EOL . PHP_EOL;
        file_put_contents('./log_' . date('j.n.Y') . '.log', $ve_chain_response_log, FILE_APPEND);

        if (curl_errno($ch)) {
            $ve_chain_error_log = 've chain token error... ' . PHP_EOL . PHP_EOL;
            file_put_contents('./log_' . date('j.n.Y') . '.log', $ve_chain_error_log, FILE_APPEND);
        }

        curl_close($ch);

        return $response;
    }

    public function generateVID($request_number = '')
    {
        $token_response = $this->createToken();
        if (!empty($token_response) && $token_response->code !== 'common.success') {
            return $token_response;
        }

        $request_number = !empty($request_number) ? $request_number : time() . rand(111111111, 999999999);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://developer.vetoolchain.com/api/v2/vid/generate');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'requestNo' => $request_number,
            'quantity'  => 1
        ]));

        $headers   = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'x-api-token: ' . $token_response->data->token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $response = json_decode($response);

        $ve_chain_response_log = 've chain generate vid response...' . PHP_EOL . PHP_EOL;
        file_put_contents('./ve_chain_sdk.log', $ve_chain_response_log, FILE_APPEND);

        if (curl_errno($ch)) {
            $ve_chain_error_log = 've chain generate vid error... ' . PHP_EOL . PHP_EOL;
            file_put_contents('./ve_chain_sdk.log', $ve_chain_error_log, FILE_APPEND);
        }

        curl_close($ch);

        return $response;
    }

    public function createHash($data_hash, $vid, $request_number = '')
    {
        $token_response = $this->createToken();
        if (!empty($token_response) && $token_response->code !== 'common.success') {
            return $token_response;
        }

        $request_number = !empty($request_number) ? $request_number : time() . rand(111111111, 999999999);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://developer.vetoolchain.com/api/v2/provenance/hash/create');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'hashList'    => [
                [
                    'dataHash' => $data_hash,
                    'vid'      => $vid
                ]
            ],
            'requestNo'   => $request_number,
            'operatorUID' => $this->operator_uid
        ]));

        $headers   = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'x-api-token: ' . $token_response->data->token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $response = json_decode($response);

        $ve_chain_response_log = 've chain create hash response...' . PHP_EOL . PHP_EOL;
        file_put_contents('./ve_chain_sdk.log', $ve_chain_response_log, FILE_APPEND);

        if (curl_errno($ch)) {
            $ve_chain_error_log = 've chain create hash error... ' . PHP_EOL . PHP_EOL;
            file_put_contents('./ve_chain_sdk.log', $ve_chain_error_log, FILE_APPEND);
        }

        curl_close($ch);

        return $response;
    }
}