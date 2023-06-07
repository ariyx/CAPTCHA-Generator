
# Captcha Generator (PHP)

Let's embrace our CAPTCHA and embark on a whimsical, laugh-inducing, and enigmatic journey towards enhanced security.


## Screenshots

[See Screenshot's..](https://github.com/ariyx/CAPTCHA-Generator/tree/main/img)


## Installation

Install CAPTCHA-generator

```bash
git clone git@github.com:ariyx/CAPTCHA-Generator.git CAPTCHA
```
## How to use ?

**Example 1:** Using a random string to generate a captcha.
```php
require 'Captcha.php';

$permitted_chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
$string_length = 6;

$captcha = new Captcha($permitted_chars, $string_length);
$captcha->generateCaptcha();
```

**Example 2:** Captcha generation with specific text.
```php
require 'Captcha.php';

$captcha = new Captcha(null, null, 'ariyx');
$captcha->generateCaptcha();
```
    
## License

Because everyone uses this license, I also chose this license :)

[MIT license](https://choosealicense.com/licenses/mit/)

