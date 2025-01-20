<?php


class KapitalBank
{

    public $merchantId, $order;
    public $url = 'https://tstpg.kapitalbank.az:5443/Exec';
    public $url2 = 'https://3dsrv.kapitalbank.az:5443/Exec';

    public function createpaymenturl()
    {

        $taksit = "TAKSİT=0";
        $input_xml = '<?xml version="1.0" encoding="UTF-8"?>
                <TKKPG>
                    <Request>
                        <Operation>CreateOrder</Operation>
                        <Language>AZ</Language>
                        <Order>
                            <OrderType>Purchase</OrderType>
                            <Merchant>' . $this->merchantId . '</Merchant>
                            <Amount>' . ($this->order->amount * 100) . '</Amount>
                            <Currency>944</Currency>
                            <Description>' . $taksit . '</Description>
                            <ApproveURL>' . route('payments.callback', [$this->order->id]) . '</ApproveURL>
                            <CancelURL>' . route('payments.callback', [$this->order->id]) . '</CancelURL>
                            <DeclineURL>' . route('payments.callback', [$this->order->id]) . '</DeclineURL>
                            </Order>
                </Request>
                </TKKPG>';
        $this->createpaymenturl($input_xml);
    }

    public function xmlRequest($request)
    {

        $url = $this->url;
        $keyFile  = realpath("payment/goycay_avm.key");

        $certFile = realpath("payment/goycay_avm.crt");
        // return $keyFile;
        $ch = curl_init();
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)',
            CURLOPT_URL => $url,
            CURLOPT_SSLCERT => $certFile,
            CURLOPT_SSLKEY => $keyFile,
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_POST => true
        );
        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);
        $array_data = json_decode(json_encode(simplexml_load_string($output)), true);

        return $array_data;
    }

    public function sendurl($request)
    {
        $response = $this->xmlRequest($request);
        $url = $response['Response']['Order']['URL'] . '?ORDERID=' . $response['Response']['Order']['OrderID'] . '&SESSIONID=' . $response['Response']['Order']['SessionID'];

        return redirect()->away($url);
    }
}
