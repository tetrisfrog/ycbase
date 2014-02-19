#!/usr/bin/php
<?php 
/*
  Font Rasterizer
  Modified July 2011 - j. hipps
  Used with vMusician's interfaces needing special cool fonts
  Copyright (c) 2010, 2011 Jacob Hipps

*/

// jacob hipps - tetrisfrog - 18 Dec 2010
// kitten kaboodle! font rasterizer via FT2 with auto image sizing/bounds checking
// outputs a 32-bit PNG with transparent alpha channel

//error_reporting(E_ERROR);

$in_font   = $_GET['f'];
$in_fsz    = $_GET['s'];
$in_texty  = $_GET['t'];
$in_colorx = $_GET['c'];
$in_option = $_GET['o'];
$tangle    = 0;


$font_dir = substr($_GET['f'],-3);

$fontfile = realpath("/usr/share/fonts/truetype/mplus/mplus-1c-medium.ttf");

//if(!isset($_GET['f'])) realpath("fonts/visitor1.ttf");
//else                   $fontfile = realpath("$font_dir/".$in_font);

if(!isset($_GET['s'])) $fsz = 72;
else                   $fsz = intval($in_fsz);

if(!isset($_GET['t'])) $texty = $argv[1];
else                   $texty = $in_texty;

// convert hex color code to dec RGB
if(isset($_GET['c'])) {
	list($cc_r,$cc_g,$cc_b) = sscanf($in_colorx,"%02x%02x%02x");
} else {
	$cc_r = 0x00;	// default color
	$cc_g = 0x00;
	$cc_b = 0x00;
}

if($_GET['o'] == "aa-off") $ft2 = false;
else $ft2 = true;

// convert \r back to \n
$texty = str_replace("\r","\n",$texty);

// debug vvvv
//$texty = "You're runnin on server time ".date("h:i:sa");

$im = imagecreatetruecolor(2048,1024);

imagesavealpha($im, true);

$colorx = imagecolorallocatealpha($im, $cc_r, $cc_g, $cc_b,1);
$alphax = imagecolorallocatealpha($im,255,255,255,0); // make transparent

imagefill($im, 0,0,$alphax);

if($ft2) $bounds = imagefttext($im, $fsz, $tangle, 100, 100, $colorx, $fontfile, $texty);
else     $bounds = imagettftext($im, $fsz, $tangle, 100, 100, -$colorx, $fontfile, $texty);


// $bounds contains array of bounding-box info
// we want [2] and [3] which is bottom-right (x,y)

$im_x = $bounds[2] - $bounds[6]; // calculate font width/height
$im_y = $bounds[3] - $bounds[7];

$imfinal = imagecreatetruecolor($im_x,$im_y);
imagefill($imfinal, 0,0,$alphax); // make transparent

imagecopy($imfinal,$im,0,0,$bounds[6],$bounds[7],$im_x,$im_y);

imagedestroy($im);

imagealphablending($imfinal,true);
imagesavealpha($imfinal,true);

header("Content-type: image/png");
imagepng($imfinal,"xout.png");

imagedestroy($imfinal); 

?>
