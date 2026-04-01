<?php
session_save_path('/tmp');
session_start();

if (isset($_GET['generate'])) {
    $text = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6);
    $_SESSION['captcha'] = $text;

    $image      = imagecreate(150, 50);
    $bg         = imagecolorallocate($image, 20, 20, 40);
    $text_color = imagecolorallocate($image, 255, 200, 50);

    for ($i = 0; $i < 6; $i++) {
        $lc = imagecolorallocate($image, rand(80,180), rand(80,180), rand(80,180));
        imageline($image, 0, rand()%50, 150, rand()%50, $lc);
    }
    for ($i = 0; $i < 120; $i++) {
        $pc = imagecolorallocate($image, rand(0,255), rand(0,255), rand(0,255));
        imagesetpixel($image, rand()%150, rand()%50, $pc);
    }

    $x = 12;
    $y = 16;
    for ($i = 0; $i < strlen($text); $i++) {
        $cc = imagecolorallocate($image, rand(180,255), rand(180,255), rand(180,255));
        imagestring($image, 5, $x, $y + rand(-5, 5), $text[$i], $cc);
        $x += 22;
    }

    header('Content-Type: image/png');
    imagepng($image);
    imagedestroy($image);
    exit;
}
?>
