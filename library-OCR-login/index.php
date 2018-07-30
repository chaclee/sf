<?php
require_once 'OCR.php';
$loginUrl = "http://210.32.33.91:8080/reader/redr_verify.php"; // 登录页面
$captchaUrl = "http://210.32.33.91:8080/reader/captcha.php"; // 验证码页面

$cookie_file = __DIR__ . DIRECTORY_SEPARATOR . 'cookies' . DIRECTORY_SEPARATOR . date('YmdHis') . '.txt';

// 获取验证码
$captchaString = get($captchaUrl, $cookie_file, true);
$tempCaptchaFile = __DIR__ . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR . date('YmdHis') . '.gif';
file_put_contents($tempCaptchaFile, $captchaString);
$ocr = new OCR($tempCaptchaFile);
$captcha = $ocr->getCaptcha();

/* 开始登陆 */
$username = '';
$passwd = '';
$postArray = [
    'number' => $username,
    'passwd' => $passwd,
    'captcha' => $captcha,
    'select' => 'cert_no',
    'returnUrl' => ''
];
$postData = http_build_query($postArray);
echo post($loginUrl, $postData, $cookie_file);

function get($url, $cookie_file, $isCookiesSave = false)
{
    // 初始化
    $curl = curl_init($url);
    $header = array();
    $header[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36';
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    // 不输出header头信息
    curl_setopt($curl, CURLOPT_HEADER, 0);
    if ($isCookiesSave) {
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_file); // 存储cookies
    } else {
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
    }
    // 保存到字符串而不是输出
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // 是否抓取跳转后的页面
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $info = curl_exec($curl);
    curl_close($curl);
    return $info;
}

function post($url, $data, $cookie_file)
{
    // 初始化
    $curl = curl_init($url);
    $header = array();
    $header[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36';
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    // 不输出header头信息
    curl_setopt($curl, CURLOPT_HEADER, 0);
    // 保存到字符串而不是输出
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
    // post数据
    curl_setopt($curl, CURLOPT_POST, 1);
    // 请求数据
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    // 是否抓取跳转后的页面
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}