<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class Epoint
{

    public $epoint_transaction;
    public $order_id;
    public $card_uid;
    public $private_key = "11hSxXEUIGoaVrmsSQ74EaZK";
    public $public_key = "i000200279";
    public $amount;
    public $currency = 'AZN';
    public $language = 'az';
    public $description;
    public $success_redirect_url = "https://sovqat369777.az/api/successpayment";
    public $error_redirect_url = "https://sovqat369777.az/api/errorpayment";

    public $signature;
    public $data;

    public $languages = ['az', 'en', 'ru'];
    public $client;
    public $response;

    public function __construct($data = [])
    {
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    public function instantiateForSendingRequest()
    {
        $this->client = new Client();

        if (!empty($_SESSION['lang']) && in_array($_SESSION['lang'], $this->languages)) {
            $this->language = $_SESSION['lang'];
        } else {
            $this->language = 'az';
        }

        return $this;
    }

    public function sign($json_data)
    {
        $this->data = base64_encode(json_encode($json_data));

        $this->signature = base64_encode(sha1("{$this->private_key}{$this->data}{$this->private_key}", true));
    }

    public function createSignatureByData()
    {
        return base64_encode(sha1("{$this->private_key}{$this->data}{$this->private_key}", true));
    }

    public function isSignatureValid()
    {
        return $this->signature === $this->createSignatureByData();
    }

    public function getDataAsJson()
    {
        return base64_decode($this->data);
    }

    public function getDataAsObject()
    {
        return json_decode(base64_decode($this->data));
    }

    public function generatePaymentUrlWithTypingCard()
    {
        $json_data = [
            'public_key' => $this->public_key,
            'language' => $this->language,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'order_id' => $this->order_id,
            'description' => $this->description,
            'success_redirect_url' => $this->success_redirect_url,
            'error_redirect_url' => $this->error_redirect_url,
        ];

        $this->sign($json_data);

        $response = $this->client->request('POST', 'https://epoint.az/api/1/request', [
            'form_params' => [
                'data' => $this->data,
                'signature' => $this->signature,
            ]
        ]);

        $this->response = json_decode($response->getBody());

        return $this;
    }

    public function getStatus()
    {
        $json_data = [
            'public_key' => $this->public_key,
        ];

        if ($this->order_id) {
            $json_data['order_id'] = $this->order_id;
        }

        if ($this->epoint_transaction) {
            $json_data['transaction'] = $this->epoint_transaction;
        }

        $this->sign($json_data);

        $response = $this->client->request('POST', 'https://epoint.az/api/1/get-status', [
            'form_params' => [
                'data' => $this->data,
                'signature' => $this->signature,
            ]
        ]);

        $this->response = json_decode($response->getBody());

        return $this;
    }

    public function registerCardForPayment()
    {
        $json_data = [
            'public_key' => $this->public_key,
            'language' => $this->language,
            'refund' => 0,
            'description' => $this->description,
            'success_redirect_url' => $this->success_redirect_url,
            'error_redirect_url' => $this->error_redirect_url,
        ];

        $this->sign($json_data);

        $response = $this->client->request('POST', "https://epoint.az/api/1/card-registration", [
            'form_params' => [
                'data' => $this->data,
                'signature' => $this->signature,
            ]
        ]);

        $this->response = json_decode($response->getBody());

        return $this;
    }

    public function registerCardForRefund()
    {
        $json_data = [
            'public_key' => $this->public_key,
            'language' => $this->language,
            'refund' => 1,
            'description' => $this->description,
            'success_redirect_url' => $this->success_redirect_url,
            'error_redirect_url' => $this->error_redirect_url,
        ];

        $this->sign($json_data);

        $response = $this->client->request('POST', "https://epoint.az/api/1/card-registration", [
            'form_params' => [
                'data' => $this->data,
                'signature' => $this->signature,
            ]
        ]);

        $this->response = json_decode($response->getBody());

        return $this;
    }

    public function payWithSavedCard()
    {
        $json_data = [
            'public_key' => $this->public_key,
            'language' => $this->language,
            'card_uid' => $this->card_uid,
            'order_id' => $this->order_id,
            'amount' => $this->amount,
            'description' => $this->description,
            'currency' => $this->currency,
        ];

        $this->sign($json_data);

        $response = $this->client->request('POST', 'https://epoint.az/api/1/execute-pay', [
            'form_params' => [
                'data' => $this->data,
                'signature' => $this->signature,
            ]
        ]);

        $this->response = json_decode($response->getBody());

        return $this;
    }

    public function cancelPayment()
    {
        $json_data = [
            'public_key' => $this->public_key,
            'language' => $this->language,
            'transaction' => $this->epoint_transaction,
            'currency' => $this->currency,
        ];

        if ($this->amount) {
            $json_data['amount'] = $this->amount;
        }

        $this->sign($json_data);

        $response = $this->client->request('POST', 'https://epoint.az/api/1/reverse', [
            'form_params' => [
                'data' => $this->data,
                'signature' => $this->signature,
            ]
        ]);

        $this->response = json_decode($response->getBody());

        return $this;
    }

    public function refundPayment()
    {
        $json_data = [
            'public_key' => $this->public_key,
            'language' => $this->language,
            'card_uid' => $this->card_uid,
            'order_id' => $this->order_id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
        ];

        $this->sign($json_data);

        $response = $this->client->request('POST', 'https://epoint.az/api/1/refund-request', [
            'form_params' => [
                'data' => $this->data,
                'signature' => $this->signature,
            ]
        ]);

        $this->response = json_decode($response->getBody());

        return $this;
    }

    public static function instantiate()
    {
        $epoint = new self();
        $epoint->instantiateForSendingRequest();
        return $epoint;
    }

    public static function checkPayment($uid, $epoint_transaction = false)
    {
        $epoint = static::instantiate();

        if ($epoint_transaction) {
            $epoint->epoint_transaction = $uid;
        } else {
            $epoint->order_id = $uid;
        }

        $epoint->getStatus();

        return $epoint->response;
    }

    public static function typeCard($order_id, $amount, $description)
    {
        $epoint = static::instantiate();

        $epoint->order_id = $order_id;
        $epoint->amount = $amount;
        $epoint->description = $description;

        $epoint->generatePaymentUrlWithTypingCard();

        return $epoint->response;
    }

    public static function saveCardForPayment($description)
    {
        $epoint = static::instantiate();

        $epoint->description = $description;
        $epoint->registerCardForPayment();

        return $epoint->response;
    }

    public static function saveCardForRefund($description)
    {
        $epoint = static::instantiate();

        $epoint->description = $description;


        $epoint->registerCardForRefund();

        return $epoint->response;
    }

    public static function payWithSaved($card_uid, $order_id, $amount, $description)
    {
        $epoint = static::instantiate();

        $epoint->card_uid = $card_uid;
        $epoint->order_id = $order_id;
        $epoint->amount = $amount;
        $epoint->description = $description;

        $epoint->payWithSavedCard();

        return $epoint->response;
    }

    /*
     * if $amount is not given all amount per transaction will return to the card
     * */
    public static function cancel($epoint_transaction, $amount = null)
    {
        $epoint = static::instantiate();

        $epoint->epoint_transaction = $epoint_transaction;

        if ($amount) {
            $epoint->amount = $amount;
        }

        $epoint->cancelPayment();

        return $epoint->response;
    }

    public static function refund($card_uid, $order_id, $amount, $description)
    {
        $epoint = static::instantiate();

        $epoint->card_uid = $card_uid;
        $epoint->order_id = $order_id;
        $epoint->amount = $amount;
        $epoint->description = $description;

        $epoint->refundPayment();

        return $epoint->response;
    }
}
