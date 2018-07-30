<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Weibo_login
{
    public $username;
    public $password;
    public $su;
    public $cookie;
    public $cookie_file;

    public function __construct()
    {
        $this->username = '';
        $this->password = '';
        $this->su = base64_encode(WEIBO_ACCOUNT);
        $this->cookie_file = APPPATH . "logs/weibo_cookie.txt";
    }

    public function login()
    {
        $pre_login_data = $this->get_pre_login_data();
        $login_url = 'http://login.sina.com.cn/sso/login.php?client=ssologin.js(v1.4.18)';
        $servertime = $pre_login_data["servertime"];
        $pubkey = $pre_login_data["pubkey"];
        $nonce = $pre_login_data["nonce"];
        $rsakv = $pre_login_data["rsakv"];
        $message = $servertime . "\t" . $nonce . "\n" . WEIBO_PASSWORD;
        $sp = bin2hex(rsa_encrypt($message, "010001", $pubkey));
        $login_data = array(
            'entry' => 'weibo',
            'gateway' => '1',
            'from' => '',
            'savestate' => '7',
            'useticket' => '1',
            'pagerefer' => '',
            'vsnf' => '1',
            'su' => $this->su,
            'service' => 'miniblog',
            'servertime' => $servertime,
            'nonce' => $nonce,
            'pwencode' => 'rsa2',
            'rsakv' => $rsakv,
            'sp' => $sp,
            'sr' => '1280*800',
            'encoding' => 'UTF-8',
            'prelt' => '48',
            'url' => 'http://weibo.com/ajaxlogin.php?framelogin=1&callback=parent.sinaSSOController.feedBackUrlCallBack',
            'returntype' => 'META',
        );
        if ($pre_login_data["showpin"] == 1) {
            //需要输入验证码
            $rand = rand(10000000, 99999999);
            $pcid = $pre_login_data["pcid"];
            $pinurl = "http://login.sina.com.cn/cgi/pin.php?r={$rand}&s=0&p={$pcid}";
            $captcha = \Httpful\Request::get($pinurl)
                ->addOnCurlOption(CURLOPT_COOKIEJAR, $this->cookie_file)
                ->addOnCurlOption(CURLOPT_COOKIEFILE, $this->cookie_file)
                ->send();
            echo "输入验证码：" . PHP_EOL;
            $save_path = FCPATH . "1.png";
            file_put_contents($save_path, $captcha->raw_body);
            echo "输入验证码：http://tool.zy62.com/1.png?_=" . time() . PHP_EOL;
            $login_data["door"] = trim(fgets(STDIN));
        }
        $res = \Httpful\Request::post($login_url)
            ->addHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->addOnCurlOption(CURLOPT_COOKIEJAR, $this->cookie_file)
            ->addOnCurlOption(CURLOPT_COOKIEFILE, $this->cookie_file)
            ->body(http_build_query($login_data))->send();
        $res->body = iconv("UTF-8", "GB2312//IGNORE", $res->body);
        $res->raw_body = iconv("UTF-8", "GB2312//IGNORE", $res->raw_body);
        preg_match("/https:\/\/passport(.*?)retcode=0/i", $res->body, $match);
        echo $res->raw_body;
        if (isset($match[0]) && $match[0]) {
            $url = $match[0];
            \Httpful\Request::get($url)
                ->addOnCurlOption(CURLOPT_COOKIEJAR, $this->cookie_file)
                ->addOnCurlOption(CURLOPT_COOKIEFILE, $this->cookie_file)
                ->send();
            echo "login success" . PHP_EOL;
        } else {
            echo "login fail" . PHP_EOL;
        }
    }

    private function get_pre_login_data()
    {
        $pre_login_url = 'https://login.sina.com.cn/sso/prelogin.php';
        $pre_login_request_params = array(
            'entry' => 'weibo',
            'callback' => 'sinaSSOController.preloginCallBack',
            'su' => $this->su,
            'rsakt' => 'mod',
            'checkpin' => '1',
            'client' => 'ssologin.js(v1.4.18)',
            '_' => time(),
        );
        $request_url = $pre_login_url . "?" . http_build_query($pre_login_request_params);
        $res = \Httpful\Request::get($request_url)
            ->addOnCurlOption(CURLOPT_COOKIEJAR, $this->cookie_file)
            ->addOnCurlOption(CURLOPT_COOKIEFILE, $this->cookie_file)
            ->send();
        $pre_login_json = substr($res->body, strlen($pre_login_request_params["callback"]) + 1, -1);
        return json_decode($pre_login_json, true);
    }

    public function get_cookie()
    {
        return $this->cookie;
    }

    public function set_cookie($cookie)
    {
        $this->cookie = $cookie;
    }
}
