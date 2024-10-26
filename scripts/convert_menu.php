<?php

function convertImage($name)
{
    global $img, $arr;

    // scan image and create array
    for ($y=0; $y<256; $y++)
    {
        for ($bytex=0; $bytex<64; $bytex++)
        {
            $res = 0; 
            for ($x=0; $x<4; $x++)
            {
                $py = $y;
                $px = $bytex*4 + $x;
                $res = ($res >> 2) & 0xFF;
                $rgb_index = imagecolorat($img, $px, $py);
                $rgba = imagecolorsforindex($img, $rgb_index);
                $r = $rgba['red'];
                $g = $rgba['green'];
                $b = $rgba['blue'];
                // blue pixel
                if ($b > 127) $res = $res | 0b01000000;
                // green pixel
                if ($g > 127) $res = $res | 0b10000000;
                // red pixel
                if ($r > 127) $res = $res | 0b11000000;
            }
            array_push($arr, $res);
        }
    }

    $fname = dirname(__FILE__) . "/../_".$name.".bin";
    $fname_zx0 = dirname(__FILE__) . "/../_".$name."_zx0.bin";
    // write binary temp file
    $f = fopen($fname, "w");
    for ($i=0; $i<count($arr); $i++) fwrite($f, chr($arr[$i]), 1);
    fclose($f);
    // compress it and remove temp file
    exec(dirname(__FILE__)."/../../scripts/zx0 -f -q ".$fname." ".$fname_zx0);
    unlink($fname);
}

    $fname_png = "./graphics/Menu.png";
    $img = imagecreatefrompng($fname_png);
    $arr = Array();
    convertImage("menu");
    
    $fname_png = "./graphics/Loading.png";
    $img = imagecreatefrompng($fname_png);
    $arr = Array();
    convertImage("loading");
