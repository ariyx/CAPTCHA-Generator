# CAPTCHA Generator (PHP)

A small PHP CAPTCHA image generator with a session-backed example form.

## Requirements

- PHP 8.0 or newer
- PHP GD extension
- GD built with FreeType support

The included fonts are used by `Captcha.php`.

## Basic image generation

Create a CAPTCHA with a fixed value and send it as a PNG response:

```php
require 'Captcha.php';

$captcha = new Captcha(null, null, 'HELLO');
$captcha->output();
```

To save the generated PNG instead:

```php
require 'Captcha.php';

$captcha = new Captcha(null, null, 'HELLO');
$captcha->save(__DIR__ . '/captcha.png');
```

## Random CAPTCHA

Create a random six-character CAPTCHA:

```php
require 'Captcha.php';

$captcha = new Captcha('ABCDEFGHJKLMNPQRSTUVWXYZ', 6);
$captcha->output();
```

Use `$captcha->getText()` only on the server when you need to validate the response.

## Session validation example

The `examples/` directory keeps HTTP session handling outside of `Captcha`:

- `examples/captcha.php` creates a CAPTCHA, stores its answer in the PHP session for five minutes, and returns the PNG with cache prevention headers.
- `examples/index.php` displays a form, refreshes the image without reloading the page, and validates the submitted answer case-insensitively.

Each validation attempt removes the stored answer. A successful, failed, missing, or expired attempt therefore requires a new CAPTCHA. The expected answer is never placed in the HTML or JavaScript.

Run the example from the repository root:

```bash
php -S 127.0.0.1:8000
```

Then open [http://127.0.0.1:8000/examples/](http://127.0.0.1:8000/examples/).

## License

[MIT License](LICENSE)
