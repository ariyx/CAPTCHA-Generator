<?php

declare(strict_types=1);

const CAPTCHA_SESSION_KEY = 'captcha_example';

session_start();

$message = null;
$messageType = 'error';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $storedCaptcha = $_SESSION[CAPTCHA_SESSION_KEY] ?? null;
    $submittedAnswer = $_POST['captcha'] ?? null;

    // Every attempt consumes the current CAPTCHA, whether it succeeds or fails.
    unset($_SESSION[CAPTCHA_SESSION_KEY]);

    if (
        ! is_array($storedCaptcha)
        || ! isset($storedCaptcha['answer'], $storedCaptcha['expires_at'])
        || ! is_string($storedCaptcha['answer'])
        || ! is_int($storedCaptcha['expires_at'])
    ) {
        $message = 'No CAPTCHA is available. Please request a new one.';
    } elseif ($storedCaptcha['expires_at'] < time()) {
        $message = 'This CAPTCHA has expired. Please try a new one.';
    } elseif (! is_string($submittedAnswer) || trim($submittedAnswer) === '') {
        $message = 'Please enter the CAPTCHA code.';
    } elseif (hash_equals(strtoupper($storedCaptcha['answer']), strtoupper(trim($submittedAnswer)))) {
        $message = 'CAPTCHA verified successfully.';
        $messageType = 'success';
    } else {
        $message = 'The CAPTCHA code was incorrect. Please try a new one.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CAPTCHA example</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; }
        form { max-width: 360px; }
        .captcha-row { display: flex; align-items: center; gap: .75rem; margin: 1rem 0; }
        input, button { font: inherit; padding: .45rem .65rem; }
        input { width: 100%; box-sizing: border-box; }
        .message { padding: .75rem; margin-bottom: 1rem; }
        .success { background: #e6f4ea; color: #1e6b35; }
        .error { background: #fce8e6; color: #9b1c1c; }
    </style>
</head>
<body>
    <h1>CAPTCHA example</h1>

    <?php if ($message !== null): ?>
        <p class="message <?= $messageType ?>"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="captcha">Enter the code shown in the image</label>
        <div class="captcha-row">
            <img id="captcha-image" src="captcha.php" width="150" height="50" alt="CAPTCHA code">
            <button type="button" id="refresh-captcha">Refresh CAPTCHA</button>
        </div>
        <input id="captcha" name="captcha" type="text" autocomplete="off" required>
        <p><button type="submit">Submit</button></p>
    </form>

    <script>
        const captchaImage = document.getElementById('captcha-image');
        const captchaInput = document.getElementById('captcha');

        document.getElementById('refresh-captcha').addEventListener('click', () => {
            captchaImage.src = 'captcha.php?t=' + Date.now();
            captchaInput.value = '';
            captchaInput.focus();
        });
    </script>
</body>
</html>