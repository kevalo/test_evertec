<?php

namespace App\Http\Utils;

use DateInterval;
use DateTime;

class WebCheckout
{
    public function prepareSessionRequest()
    {
        $currentDate = new DateTime();
        $seed = $currentDate->format('c'); // Returns ISO8601 in proper format
        $expires = $currentDate->add(new DateInterval('PT' . env('PTP_SESSION_LIMIT') . 'M'))->format('c');

        $nonce = (string)time();

        $tranKey = base64_encode(sha1($nonce . $seed . env('PYP_SECRET_KEY'), true));

        try {
            $fields = json_encode([
                "locale" => "es_CO",
                "auth" => [
                    "login" => env('PTP_LOGIN'),
                    "tranKey" => $tranKey,
                    "nonce" => base64_encode($nonce),
                    "seed" => $seed
                ],
                "payment" => [
                    "reference" => "1122334455",
                    "description" => "Prueba",
                    "amount" => [
                        "currency" => "USD",
                        "total" => 100
                    ]
                ],
                "expiration" => $expires,
                "returnUrl" => env('APP_URL'),
                "ipAddress" => $_SERVER['REMOTE_ADDR'],
                "userAgent" => "PlacetoPay Sandbox"
            ], JSON_THROW_ON_ERROR);

        } catch (\Exception $e) {
            $fields = "";
            error_log($e->getMessage());
        }
        return $fields;
    }

    public function makeRequest($endpoint, $fields)
    {
        $response = [];

        try {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => env('PTP_BASE_URL') . $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $fields,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json"
                ],
            ]);

            $response = curl_exec($curl);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        return $response;
    }
}
