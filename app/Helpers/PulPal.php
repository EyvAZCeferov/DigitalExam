<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use App\Models\Payments;

class PulPal
{
    private $public_key = "ab5ea6ba-0c81-4688-a089-21936d68ca44";
    private $private_key = "1IynyQc81CW5HaXiq3vBimlXJb0BONXcLQzTh8SKxYG9cbpW7CiAjzkNGLkgjV3SIxglX09vbO7PjDHVqPCGjQ==";
    private $merchant_id = 3950;
    private $host = 'https://payment-api.pulpal.az';
    private $path = '/api/merchant_payment/External/product/info?externalId=';
    private $nonce = '';
    private $external_id = '';
    private $payurl = 'https://pay.pulpal.az/payment';
    public function createPayment($external_id)
    {
        try {
            $payment = Payments::where("transaction_id", $external_id)->first();
            $name_az = $payment->exam->name['az_name'];
            $name_ru = $payment->exam->name['ru_name'];
            $name_en = $payment->exam->name['en_name'];
            $nonce=$payment->data['nonce']??1;
            $price = $payment->amount *100;
            $fromEpoch     = floor(time() / 300);
            $signature     = sha1($name_en . $name_az . $name_ru . $this->merchant_id . $external_id . $price . $fromEpoch . $nonce);
            $this->external_id = $external_id;

            $body=[
                'externalId'=>$external_id,
                'name_az' => $name_az,
                'name_ru' => $name_ru,
                'name_en' => $name_en,
                'price' => $price,
                'signature2' => $signature,
                'merchantId' => $this->merchant_id,
            ];
            $url = $this->payurl .http_build_query($body);
            return $url;
        } catch (\Exception $e) {
            \Log::info(['--------------CREATE PAYMENT URL--------------', $e->getMessage(), 'line' => $e->getLine()]);
        }
    }
    public function generateSignature(): string
    {
        return base64_encode(hash_hmac('sha256', $this->public_key . $this->nonce . $this->path, base64_decode($this->private_key), true));
    }

    public function generateUrl(): string
    {
        return $this->host . $this->path;
    }
    public function generateHeaders(): array
    {
        return array(
            'Accept: text/plain',
            'Merchant-Id: ' . $this->merchant_id,
            'Api-Key: ' . $this->public_key,
            'Signature: ' . $this->generateSignature(),
            'Nonce: ' . $this->nonce,
        );
    }

    public function getStatus($nonce, $external_id)
    {
        $this->nonce = $nonce;
        $this->external_id = $external_id;
        $this->path .= $external_id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->generateUrl());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->generateHeaders());
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
