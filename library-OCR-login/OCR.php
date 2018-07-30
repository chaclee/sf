<?php

class OCR
{

    public $imagePath;

    private $tempFile;

    private $dic = array(
        '00011000001111000110011011000011110000111100001111000011011001100011110000011000' => 0,
        '00011000001110000111100000011000000110000001100000011000000110000001100001111110' => 1,
        '00111100011001101100001100000011000001100000110000011000001100000110000011111111' => 2,
        '01111100110001100000001100000110000111000000011000000011000000111100011001111100' => 3,
        '00000110000011100001111000110110011001101100011011111111000001100000011000000110' => 4,
        '11111110110000001100000011011100111001100000001100000011110000110110011000111100' => 5,
        '00111100011001101100001011000000110111001110011011000011110000110110011000111100' => 6,
        '11111111000000110000001100000110000011000001100000110000011000001100000011000000' => 7,
        '00111100011001101100001101100110001111000110011011000011110000110110011000111100' => 8,
        '00111100011001101100001111000011011001110011101100000011010000110110011000111100' => 9
    );

    public function __construct($imgpath)
    {
        $this->imagePath = $imgpath;
    }

    public function test()
    {
        // var_dump($this->dic);
        echo $this->imagePath;
    }

    public function getCaptcha()
    {
        $im = imagecreatefromgif($this->imagePath);
        $this->tempFile = __DIR__ . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR . date('YmdHis') . '.gif';
        imagegif($im, $this->tempFile);
        //imagegif($im, $this->imagePath);
        $rgbArray = array();
        $res = $im;
        $size = getimagesize($this->imagePath);
        
        $wid = $size['0'];
        $hid = $size['1'];
        for ($i = 0; $i < $hid; ++ $i) {
            for ($j = 0; $j < $wid; ++ $j) {
                $rgb = imagecolorat($res, $j, $i);
                $rgbArray[$i][$j] = imagecolorsforindex($res, $rgb);
            }
        }
        $str = [];
        for ($i = 0; $i < $hid; $i ++) {
            for ($j = 0; $j < $wid; $j ++) {
                if ($i > 15 && $i < 26) {
                    if ($j > 5 && $j < 50) {
                        if ($rgbArray[$i][$j]['red'] == 212) {
                            $str[] = '0';
                        } else {
                            $str[] = '1';
                        }
                    }
                }
            }
        }
        $temp = array_chunk($str, 44);
        $one = '';
        $two = '';
        $three = '';
        $four = '';
        for ($i = 0; $i < 10; $i ++) {
            for ($j = 0; $j < 44; $j ++) {
                if ($j < 8) {
                    $one .= $temp[$i][$j];
                }
                if ($j >= 12 && $j <= 19) {
                    $two .= $temp[$i][$j];
                }
                if ($j >= 24 && $j <= 31) {
                    $three .= $temp[$i][$j];
                }
                if ($j >= 36 && $j <= 43) {
                    $four .= $temp[$i][$j];
                }
            }
        }
        $captcha = $this->dic[$one] . $this->dic[$two] . $this->dic[$three] . $this->dic[$four];
        return $captcha;
    }

    public function getTempFile()
    {
        return $this->tempFile;
    }
}