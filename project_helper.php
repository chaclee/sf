<?php

function asn1_length($length)
{
    if ($length <= 0x7F) return chr($length);
    $temp = ltrim(pack('N', $length), chr(0));
    return pack('Ca*', 0x80 | strlen($temp), $temp);
}

function rsa_pkey($exponent, $modulus)
{
    $modulus = pack('Ca*a*', 0x02, asn1_length(strlen($modulus)), $modulus);
    $exponent = pack('Ca*a*', 0x02, asn1_length(strlen($exponent)), $exponent);
    $oid = pack('H*', '300d06092a864886f70d0101010500');
    $pkey = $modulus . $exponent;
    $pkey = pack('Ca*a*', 0x30, asn1_length(strlen($pkey)), $pkey);
    $pkey = pack('Ca*', 0x00, $pkey);
    $pkey = pack('Ca*a*', 0x03, asn1_length(strlen($pkey)), $pkey);
    $pkey = $oid . $pkey;
    $pkey = pack('Ca*a*', 0x30, asn1_length(strlen($pkey)), $pkey);
    $pkey = '-----BEGIN PUBLIC KEY-----' . "\r\n" . chunk_split(base64_encode($pkey)) . '-----END PUBLIC KEY-----';
    return $pkey;
}

function rsa_encrypt($message, $e, $n)
{
    $exponent = hex2bin($e);
    $modulus = hex2bin($n);
    $pkey = rsa_pkey($exponent, $modulus);
    openssl_public_encrypt($message, $result, $pkey, OPENSSL_PKCS1_PADDING);
    return $result;
}
