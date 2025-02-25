<?php

namespace App\Helpers;

use App\Models\Payments;
use Illuminate\Support\Facades\Http;

class KapitalbankNew
{
    public $merchantId, $order, $url, $payment_id;
    public $parametres = [

        'KAPITAL_ORDER_URL' => "https://e-commerce.kapitalbank.az/api/order",
        'KAPITAL_PAYMENT_URL' => "https://e-commerce.kapitalbank.az/flex",
        'KAPITAL_USERNAME' => "TerminalSys/E1090100",
        'KAPITAL_PASSWORD' => "aC(:^nW5R2R)O;zJ6eBm",
        'KAPITAL_TYPE_RID' => "Order_SMS",
        'KAPITAL_CURRENCY' => "AZN",
        'KAPITAL_LANG' => "az",
        'KAPITAL_REDIRECT_URL' => "https://digitalexam.az/checkpayment",

        'KAPITAL_ORDER_URL_TEST' => "https://txpgtst.kapitalbank.az/api/order",
        'KAPITAL_PAYMENT_URL_TEST' => "https://txpgtst.kapitalbank.az/flex",
        'KAPITAL_USERNAME_TEST' => "TerminalSys/kapital",
        'KAPITAL_PASSWORD_TEST' => "kapital123",
        'KAPITAL_TYPE_RID_TEST' => "Order_SMS",
        'KAPITAL_CURRENCY_TEST' => "AZN",
        'KAPITAL_LANG_TEST' => "az",
        'KAPITAL_REDIRECT_URL_TEST' => "https://digitalexam.az/checkpayment",

    ];

    public function __construct($payment_id = null)
    {
        $this->payment_id = $payment_id;
    }

    public function handle()
    {
        try {
            $order = null;
            if (isset($this->payment_id) && !empty($this->payment_id)) {
                $order = Payments::find($this->payment_id);
            } else {
                $order = new Payments();
            }

            $amount = $order->amount;

            $data = [
                'order' => [
                    'typeRid' => $this->parametres['KAPITAL_TYPE_RID'],
                    'amount' => $amount ?? 1,
                    'currency' => $this->parametres['KAPITAL_CURRENCY'],
                    'language' => $this->parametres['KAPITAL_LANG'],
                    'description' => $order->exam->name['az_name'],
                    'hppRedirectUrl' => $this->parametres['KAPITAL_REDIRECT_URL'],
                    'hppCofCapturePurposes' => ['Cit']
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->parametres['KAPITAL_USERNAME'] . ':' . $this->parametres['KAPITAL_PASSWORD'])
            ])->post($this->parametres['KAPITAL_ORDER_URL'], $data);

            if ($response->successful()) {
                $responseData = $response->object();

                $payment_url = $this->parametres['KAPITAL_PAYMENT_URL'] . '?id=' . $responseData->order->id . '&password=' . $responseData->order->password;

                $order->frompayment = json_encode($responseData);
                $order->transaction_id =  $responseData->order->id;

                $order->save();

                return ['payment_url' => $payment_url];
            } else {
                $order->frompayment = $response;
                $order->save();

                return ['msg' => ('Ödəniş baş tutmadı')];
            }
        } catch (\Exception $e) {
            $order->frompayment = $e->getMessage();
            $order->save();

            return ['msg' => ('Ödəniş baş tutmadı')];
        }
    }
}
