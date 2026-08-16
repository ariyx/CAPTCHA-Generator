<?php

declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/Captcha.php';

$captcha = new Captcha();

$_SESSION['captcha_example'] = [
    'answer' => $captcha->getText(),
    'expires_at' => time() + 300,
];

// Release the session lock before rendering the image.
session_write_close();

header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
header('X-Content-Type-Options: nosniff');

$captcha->output();