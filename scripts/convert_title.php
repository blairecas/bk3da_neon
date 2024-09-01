<?php
    $fname_png = "./graphics/Title.png";

    $img = imagecreatefrompng($fname_png);
    $arr = Array();

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

    echo "Title: $fname_png\n";

    $fname = dirname(__FILE__) . "/../_title.bin";
    $fname_zx0 = dirname(__FILE__) . "/../_title_zx0.bin";

    // write binary temp file
    $f = fopen($fname, "w");
    for ($i=0; $i<count($arr); $i++) fwrite($f, chr($arr[$i]), 1);
    fclose($f);

    // compress it and remove temp file
    exec(dirname(__FILE__)."/../../scripts/zx0 -f -q ".$fname." ".$fname_zx0);
    unlink($fname);

    // write array of bytes    
    // echo "Writing title picture...\n";
    // $f = fopen ("acpu_title.mac", "w");
    // fputs($f, "TitleData:\n");
    // for ($i=0, $n=0; $i<count($arr); $i++)
    // {
    //     if ($n==0) fputs($f, "\t.byte\t");
    //     $bb = $arr[$i];
    //     fputs($f, decoct($ww));
    //     $n++; if ($n<8) fputs($f, ", "); else { $n=0; fputs($f, "\n"); }
    // }
    // fputs($f, "\n");
    // fclose($f);
