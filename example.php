<?php
require_once 'OCR.php';
$loginUrl = "http://210.32.33.91:8080/reader/redr_verify.php"; // 登录页面
$captchaUrl = "http://210.32.33.91:8080/reader/captcha.php"; // 验证码页面

$ocr = new OCR('./captcha/1.gif');
$captcha = $ocr->getCaptcha();
echo $captcha;