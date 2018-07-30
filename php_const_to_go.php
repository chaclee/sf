<?php
//不支持definded声明的变量和const声明的数组
$txt = "php const text";
$list = explode("\n", $txt);
$go_file = "const.go"; 
foreach ($list as $item) {
    if (!strpos($item, ";")) {
        //没有分号是空行或者注释
        file_put_contents($go_file, $item . PHP_EOL, FILE_APPEND);
        continue;
    }
    //按分号切开
    $line = str_replace("const ", "", $item);
    list($l1, $l2) = explode(";", $line);
    list($name, $value) = explode(" = ", $l1);
    //name 转成大驼峰
    $name = toup($name);
    //值单引号要换成双引号
    $value = str_replace("'", '"', $value);
    $tmp = "{$name} = {$value} {$l2}\n";
    file_put_contents($go_file, $tmp, FILE_APPEND);
}

function toup($name)
{
    $tmp = explode("_", $name);
    $str = "";
    foreach ($tmp as $item) {
        $str .= ucfirst(strtolower($item));
    }
    return $str;
}
