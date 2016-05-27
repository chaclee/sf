<?php

class AliDirectMail
{

    public $AccessKeyId;

    public $AccessKey;

    public $ToAddress;

    public $Subject;

    public $HtmlBody;

    public $Format = 'JSON';

    public $Version = '2015-11-23';

    public static $gateway = 'https://dm.aliyuncs.com/?';

    public function __construct($ToAddress, $Subject, $HtmlBody)
    {
        $this->AccessKeyId = '';
        $this->AccessKey = '';
        $this->ToAddress = $ToAddress;
        $this->Subject = $Subject;
        $this->HtmlBody = $HtmlBody;
    }

    private function percentEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }

    public function setToAddress($ToAddress)
    {
        $this->ToAddress = $ToAddress;
    }

    public function setSubject($Subject)
    {
        $this->Subject = $Subject;
    }

    public function setHtmlBody($HtmlBody)
    {
        $this->HtmlBody = $HtmlBody;
    }

    public function setFromAlias($FromAlias)
    {
        $this->FromAlias = $FromAlias;
    }

    public function send()
    {
        $parameters = array(
            'Format' => $this->Format,
            'Version' => $this->Version,
            'AccessKeyId' => $this->AccessKeyId,
            'SignatureMethod' => 'HMAC-SHA1',
            'Timestamp' => date('c') . 'Z',
            'SignatureVersion' => '1.0',
            'SignatureNonce' => $this->createNonceStr(16),
            'Action' => 'SingleSendMail',
            'AccountName' => 'noreply@yi53.cc',
            'ReplyToAddress' => 'true',
            'AddressType' => '0',
            'ToAddress' => 'rainwsy@yopmail.com',
            'FromAlias' => 'rainwsy',
            'Subject' => $this->Subject,
            'HtmlBody' => $this->HtmlBody
        );
        $parameters['Signature'] = $this->makeSign($parameters, $this->AccessKey);
        return $this->buildUrl($parameters);
    }

    private function buildUrl($parameters)
    {
        $requestUrl = self::gateway;
        foreach ($parameters as $apiParamKey => $apiParamValue) {
            if ($apiParamKey != 'Signature') {
                $requestUrl .= "$apiParamKey=" . urlencode($apiParamValue) . "&";
            } else {
                $requestUrl .= "$apiParamKey=" . $apiParamValue . "&";
            }
        }
        return substr($requestUrl, 0, - 1);
    }

    private function makeSign($parameters, $secret)
    {
        ksort($parameters);
        
        $canonicalizedQueryString = '';
        foreach ($parameters as $key => $value) {
            $canonicalizedQueryString .= '&' . percentEncode($key) . '=' . percentEncode($value);
        }
        $stringToSign = 'GET' . '&%2F&' . percentencode(substr($canonicalizedQueryString, 1));
        $signature = hash_hmac('sha1', $stringToSign, $secret . "&", true);
        return urlencode(base64_encode($signature));
    }

    /*
     * 生成指定长度随机字符串
     */
    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i ++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}
