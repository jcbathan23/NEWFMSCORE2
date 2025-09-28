<?php
session_start();

// Generate random 4-digit code
$captcha_code = rand(1000, 9999);
$_SESSION['captcha_code'] = $captcha_code;

// Create image
$width = 100;
$height = 40;
$image = imagecreate($width, $height);

// Colors
$bg = imagecolorallocate($image, 240, 240, 240); // light background
$textcolor = imagecolorallocate($image, 50, 50, 50); // dark text
$linecolor = imagecolorallocate($image, 100, 100, 100); // for lines

// Fill background
imagefill($image, 0, 0, $bg);

// Add random lines for distortion
for ($i=0; $i<5; $i++) {
    imageline($image, rand(0,$width), rand(0,$height), rand(0,$width), rand(0,$height), $linecolor);
}

// Add the CAPTCHA code
$font_size = 5;
imagestring($image, $font_size, 20, 10, $captcha_code, $textcolor);

// Output image
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
?>
