<?php
    $fname_png = "./graphics/Tiles.png";

    $img = imagecreatefrompng($fname_png);
    $width = imagesx($img);
    $height = imagesy($img);
    $tiles_dx = intval($width / 16);
    $tiles_dy = intval($height / 17);
    
    // tiles array
    $tilesArray = Array();

    $cur_tile = 0;
    $last_tile = 0;
    
    // scan image and create array
    for ($tiley=0; $tiley<$tiles_dy; $tiley++)
    {
        for ($tilex=0; $tilex<$tiles_dx; $tilex++)
        {
	        $tile = Array();
            $have_data = false;
	        for ($y=0; $y<17; $y++)
            {
                // first word
                $res = 0; 
		        for ($x=0; $x<8; $x++)
                {
                    $py = $tiley*17 + $y;
		            $px = $tilex*16 + $x;
		            $res = ($res >> 2) & 0xFFFF;
                    $rgb_index = imagecolorat($img, $px, $py);
                    $rgba = imagecolorsforindex($img, $rgb_index);
                    $r = $rgba['red'];
                    $g = $rgba['green'];
                    $b = $rgba['blue'];
                    // blue pixel
                    if ($b > 127) $res = $res | 0b0100000000000000;
                    // green pixel
                    if ($g > 127) $res = $res | 0b1000000000000000;
                    // red pixel
                    if ($r > 127) $res = $res | 0b1100000000000000;
                }
                array_push($tile, $res);
                if ($res !== 0) $have_data = true;
                // second word
                $res = 0; 
		        for ($x=8; $x<16; $x++)
                {
                    $py = $tiley*17 + $y;
		            $px = $tilex*16 + $x;
		            $res = ($res >> 2) & 0xFFFF;
                    $rgb_index = imagecolorat($img, $px, $py);
                    $rgba = imagecolorsforindex($img, $rgb_index);
                    $r = $rgba['red'];
                    $g = $rgba['green'];
                    $b = $rgba['blue'];
                    // blue pixel
                    if ($b > 127 && $b > $g && $b > $r) $res = $res | 0b0100000000000000;
                    // green pixel
                    if ($g > 127 && $g > $b && $g > $r) $res = $res | 0b1000000000000000;
                    // red pixel
                    if ($r > 127 && $r > $b && $r > $g) $res = $res | 0b1100000000000000;
                }
                array_push($tile, $res);
                if ($res !== 0) $have_data = true;
            }
	        array_push($tilesArray, $tile);
            $cur_tile++;
            if ($have_data) $last_tile = $cur_tile;
        }
    }
    
    echo "Image: $fname_png - $width x $height, tiles $tiles_dx x $tiles_dy, total $last_tile\n";
 
    ////////////////////////////////////////////////////////////////////////////
    
    $f = fopen ("acpu_tiles.mac", "w");
    fputs($f, "TilesCpuData:\n");
    for ($t=0; $t<$last_tile; $t++)
    {
        $tile = $tilesArray[$t];
        $n = 0;
	    for ($i=0; $i<34; $i++)
	    {
	        if ($n==0) fputs($f, "\t.word\t");
	        $ww = $tile[$i];
	        fputs($f, decoct($ww));
	        $n++; if ($n<8) fputs($f, ", "); else { $n=0; fputs($f, "\n"); }
	    }
        fputs($f, "\n");
    }
    fputs($f, "\n");
    fclose($f);

?>