<?php

namespace App\Http\Utils;

use DateInterval;
use DateTime;

class WebCheckout
{

    /**
     * Returns the auth data needed to consume the place to play endpoints
     * @param $currentDate
     * @return array
     */
    public function getAuthData($currentDate)
    {
        $seed = $currentDate->format('c'); // Returns ISO8601 in proper format
        $nonce = (string)time();
        $tranKey = base64_encode(sha1($nonce . $seed . env('PTP_SECRET_KEY'), true));

        return [
            "login" => env('PTP_LOGIN'),
            "tranKey" => $tranKey,
            "nonce" => base64_encode($nonce),
            "seed" => $seed
        ];
    }

    /**
     * Returns the fields for the session request
     * @param $orderId
     * @return false|string
     * @throws \Exception
     */
    public function prepareSessionRequest($orderId)
    {
        $currentDate = new DateTime();
        $authData = $this->getAuthData($currentDate);
        $expires = $currentDate->add(new DateInterval('PT' . env('PTP_SESSION_LIMIT') . 'M'))->format('c');

        try {
            $fields = json_encode([
                "locale" => "es_CO",
                "auth" => $authData,
                "payment" => [
                    "reference" => (string)$orderId,
                    "description" => "Prueba",
                    "amount" => [
                        "currency" => "COP",
                        "total" => 9999
                    ]
                ],
                "expiration" => $expires,
                "returnUrl" => route('orders.show', $orderId),
                "ipAddress" => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                "userAgent" => "PlacetoPay Sandbox"
            ], JSON_THROW_ON_ERROR);

        } catch (\Exception $e) {
            $fields = "";
            error_log($e->getMessage());
        }
        return $fields;
    }

    /**
     * Makes a request to the endpoint passed with the fields
     * @param $endpoint
     * @param $fields
     * @return array|bool|string
     */
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

    /**
     * Check the session endpoint response to return the request id and process url
     * @param $response
     * @return string[]
     */
    public function parseSessionResponse($response)
    {
        $arrResponse = json_decode($response, true);

        $responseData = ['requestId' => '', 'processUrl' => ''];
        if (is_array($arrResponse) && array_key_exists('status', $arrResponse)
            && $arrResponse['status']['status'] === 'OK') {
            $responseData['requestId'] = $arrResponse['requestId'];
            $responseData['processUrl'] = $arrResponse['processUrl'];
        }
        return $responseData;
    }

    /**
     * Return the status of the session/{id} endpoint
     * @param $response
     * @return mixed|string
     */
    public function parseSessionStatusResponse($response)
    {
        $arrResponse = json_decode($response, true);

        $status = '';
        if (is_array($arrResponse) && array_key_exists('status', $arrResponse)) {
            $status = $arrResponse['status']['status'];
        }
        return $status;
    }

}
