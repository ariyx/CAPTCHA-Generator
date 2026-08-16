<?php

class Captcha
{
    private const DEFAULT_CHARS = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    private string $permittedChars;
    private int $stringLength;
    private string $text;

    private int $width;
    private int $height;
    private int $fontSize;
    private int $noiseCount;

    private array $fonts;

    private $image = null;

    public function __construct(
        ?string $permittedChars = null,
        ?int $stringLength = null,
        ?string $text = null,
        int $width = 150,
        int $height = 50,
        int $fontSize = 20,
        int $noiseCount = 6
    ) {
        $this->checkRequirements();

        $this->permittedChars = $permittedChars ?? self::DEFAULT_CHARS;

        $this->width      = $width;
        $this->height     = $height;
        $this->fontSize   = $fontSize;
        $this->noiseCount = $noiseCount;

        $this->fonts = [
            __DIR__ . '/fonts/Acme.ttf',
            __DIR__ . '/fonts/Retro.ttf',
        ];

        $this->validateFonts();

        if ($text !== null && $text !== '') {
            $this->text         = $text;
            $this->stringLength = strlen($text);
        } else {
            $this->stringLength = $stringLength ?? 5;
            $this->text         = $this->random($this->permittedChars, $this->stringLength);
        }
    }

    private function checkRequirements(): void
    {
        if (! extension_loaded('gd')) {
            throw new RuntimeException('The GD extension is required.');
        }

        if (! function_exists('imagettftext')) {
            throw new RuntimeException('GD must have FreeType support enabled.');
        }
    }

    private function validateFonts(): void
    {
        foreach ($this->fonts as $font) {
            if (! is_file($font)) {
                throw new RuntimeException("Captcha font not found: {$font}");
            }
        }
    }

    private function random(string $characters, int $length): string
    {
        if ($characters === '') {
            throw new InvalidArgumentException('Permitted characters cannot be empty.');
        }

        if ($length < 1) {
            throw new InvalidArgumentException('Captcha length must be greater than 0.');
        }

        $result = '';
        $max    = strlen($characters) - 1;

        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[random_int(0, $max)];
        }

        return $result;
    }

    private function generateColors(): array
    {
        $colors = [];

        $red   = random_int(125, 175);
        $green = random_int(125, 175);
        $blue  = random_int(125, 175);

        for ($i = 0; $i < 5; $i++) {
            $colors[] = imagecolorallocate(
                $this->image,
                max(0, $red - (20 * $i)),
                max(0, $green - (20 * $i)),
                max(0, $blue - (20 * $i))
            );
        }

        return $colors;
    }

    private function generateImage(): void
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        imageantialias($this->image, true);

        $colors = $this->generateColors();

        imagefill($this->image, 0, 0, $colors[0]);

        for ($i = 0; $i < $this->noiseCount; $i++) {
            imagesetthickness($this->image, random_int(1, 2));

            $rectColor = $colors[random_int(1, 4)];

            $rectWidth  = random_int(15, 40);
            $rectHeight = random_int(12, 28);

            $x1 = random_int(-10, $this->width - 10);
            $y1 = random_int(-10, $this->height - 10);

            $x2 = $x1 + $rectWidth;
            $y2 = $y1 + $rectHeight;

            imagerectangle($this->image, $x1, $y1, $x2, $y2, $rectColor);
        }
    }

    private function generateText(): void
    {
        $textColors = [
            imagecolorallocate($this->image, 20, 20, 20),
            imagecolorallocate($this->image, 215, 215, 215),
        ];

        $padding     = 12;
        $usableWidth = $this->width - ($padding * 2);
        $cellWidth   = $usableWidth / $this->stringLength;

        for ($i = 0; $i < $this->stringLength; $i++) {
            $character = $this->text[$i];

            // random font for each character
            $font  = $this->fonts[array_rand($this->fonts)];
            $angle = random_int(-14, 14);

            $box = imagettfbbox($this->fontSize, $angle, $font, $character);

            $charWidth = max(
                abs($box[2] - $box[0]),
                abs($box[4] - $box[6])
            );

            $charHeight = max(
                abs($box[7] - $box[1]),
                abs($box[5] - $box[3])
            );

            $cellStart = $padding + ($i * $cellWidth);

            $x = (int) (
                $cellStart +
                (($cellWidth - $charWidth) / 2) +
                random_int(-2, 2)
            );

            $y = (int) (
                (($this->height + $charHeight) / 2) +
                random_int(-2, 2)
            );

            $color = $textColors[array_rand($textColors)];

            imagettftext(
                $this->image,
                $this->fontSize,
                $angle,
                $x,
                $y,
                $color,
                $font,
                $character
            );
        }
    }

    private function addOverlayNoise(): void
    {
        $noiseColors = [
            imagecolorallocate($this->image, 40, 40, 40),
            imagecolorallocate($this->image, 225, 225, 225),
            imagecolorallocate($this->image, 80, 100, 80),
        ];

        imagesetthickness($this->image, 1);

        $color = $noiseColors[array_rand($noiseColors)];

        imagearc(
            $this->image,
            random_int(
                (int) ($this->width * 0.35),
                (int) ($this->width * 0.65)
            ),
            random_int(10, $this->height - 10),
            random_int(
                (int) ($this->width * 0.65),
                (int) ($this->width * 0.90)
            ),
            random_int(12, 24),
            random_int(0, 120),
            random_int(220, 360),
            $color
        );

        $dotCount = random_int(25, 45);

        for ($i = 0; $i < $dotCount; $i++) {
            $color = $noiseColors[array_rand($noiseColors)];

            imagesetpixel(
                $this->image,
                random_int(0, $this->width - 1),
                random_int(0, $this->height - 1),
                $color
            );
        }
    }

    private function render(): void
    {
        $this->generateImage();
        $this->generateText();
        $this->addOverlayNoise();
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function output(): void
    {
        $this->render();

        header('Content-Type: image/png');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        imagepng($this->image);

        $this->destroy();
    }

    public function save(string $path): void
    {
        $this->render();

        $saved = imagepng($this->image, $path);

        $this->destroy();

        if (! $saved) {
            throw new RuntimeException("Could not save CAPTCHA to: {$path}");
        }
    }

    private function destroy(): void
    {
        if ($this->image !== null) {
            imagedestroy($this->image);
            $this->image = null;
        }
    }

    public function generateCaptcha(): void
    {
        $this->output();
    }
}