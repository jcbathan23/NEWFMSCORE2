<?php
session_start();
$_SESSION['captcha_code'] = rand(1000, 9999);
echo $_SESSION['captcha_code'];
