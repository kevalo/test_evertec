<?php

namespace App\Http\Utils;

use DateInterval;
use DateTime;

final class WebCheckout
{

    public static function prepareSessionRequest($data = [])
    {
        if (!$data) {
            return null;
        }

        $currentDate = new DateTime();
        $seed = $currentDate->format('c'); // Returns ISO8601 in proper format
        $expires = $currentDate->add(new DateInterval('PT60M'))->format('c');

        $nonce = (string)time();

        $tranKey = base64_encode(sha1($nonce . $seed . env('PYP_SECRET_KEY'), true));

        try {
            $fields = json_encode([
                "locale" => "es_CO",
                "auth" => [
                    "login" => "6dd490faf9cb87a9862245da41170ff2",
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
                "ipAddress" => "127.0.0.1",
                "userAgent" => "PlacetoPay Sandbox"
            ], JSON_THROW_ON_ERROR);

        } catch (\Exception $e) {
            $fields = "";
            error_log($e->getMessage());
        }
        return $fields;
    }

    public static function callApi($endpoint, $fields)
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
