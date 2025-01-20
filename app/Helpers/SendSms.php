<?php

namespace App\Helpers;

class SendSms
{
    public $login = '', $password = '', $key = '', $sender = '';

    public function __construct()
    {
        $this->login = 'artvariumsms';
        $password = 'Hf5hoKF!6';
        $this->password = md5($password);
        $this->sender = "Artvarium";
    }

    public function send($number, $message)
    {
        try {
            $gsm = '994' . substr($number, 1);
            $message = 'Giriş üçün şifrəniz:' . $message;

            $response=$this->createXmlRequest($number,$message);

            return $response;
        } catch (\Exception $e) {
            dd($e->getMessage(),$e->getLine());
        }
    }

    public function createXmlRequest($number, $message)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<request>';
        $xml .= '<response>';
        $xml .= '<head>';
        $xml .= '<operation>submit</operation>';
        $xml .= '<responsecode>000</responsecode>';
        $xml .= '<login>your_login</login>';
        $xml .= '<password>your_password</password>';
        $xml .= '</head>';
        $xml .= '<body>';
        $xml .= '<title>TITLE</title>';
        $xml .= '<taskid>4837</taskid>';
        $xml .= '<bulkmessage>' . htmlspecialchars($message) . '</bulkmessage>';
        $xml .= '</body>';
        $xml .= '<scheduled>' . date('Y-m-d H:i:s') . '</scheduled>';
        $xml .= '</response>';
        $xml .= '<isbulk>true</isbulk>';
        $xml .= '<controlid>111</controlid>';
        $xml .= '<head></head>';
        $xml .= '<body>';
        $xml .= '<msisdn>' . $number . '</msisdn>';
        $xml .= '</body>';
        $xml .= '</request>';
        
        return $xml;
    }
    
}
