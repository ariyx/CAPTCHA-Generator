<?php

class Captcha
{
    private $permitted_chars = 'QWERTYUIOPASDFGHJKLZXCVBNM';
    private $string_length;
    private $text = '';
    private $image;
    private $fonts;

    public function __construct($permitted_chars, $string_length, $text)
    {
        $this->permitted_chars = $permitted_chars;
        $this->string_length = $string_length ?? strlen($text);
        $this->fonts = [__DIR__ . '\fonts\Acme.ttf', __DIR__ . '\fonts\ARLRDBD.ttf'];

        if (!empty($text)) { 
            $this->text = $text;
        } else {
            $this->text = $this->random($this->permitted_chars, $this->string_length);
        }
    }

    private function random($input, $strength = 5, $secure = true)
    {
        $input_length = strlen($input);
        $random_string = '';
        for ($i = 0; $i < $strength; $i++) {
            if ($secure) {
                $random_character = $input[random_int(0, $input_length - 1)];
            } else {
                $random_character = $input[mt_rand(0, $input_length - 1)];
            }
            $random_string .= $random_character;
        }
        return $random_string;
    }

    private function generateColors()
    {
        $colors = [];
        $red = rand(125, 175);
        $green = rand(125, 175);
        $blue = rand(125, 175);
        for ($i = 0; $i < 5; $i++) {
            $colors[] = imagecolorallocate($this->image, $red - 20 * $i, $green - 20 * $i, $blue - 20 * $i);
        }
        return $colors;
    }

    private function generateImage()
    {
        $this->image = imagecreatetruecolor(150, 50);
        imageantialias($this->image, true);
        $colors = $this->generateColors();
        imagefill($this->image, 0, 0, $colors[0]);
        for ($i = 0; $i < 10; $i++) {
            imagesetthickness($this->image, rand(2, 10));
            $rect_color = $colors[rand(1, 4)];
            imagerectangle($this->image, rand(-10, 190), rand(-10, 10), rand(-10, 190), rand(40, 60), $rect_color);
        }
    }

    private function generateText()
    {
        $textcolors = [
            imagecolorallocate($this->image, 0, 0, 0),
            imagecolorallocate($this->image, 255, 255, 255)
        ];

        for ($i = 0; $i < strlen($this->text) ; $i++) {
            $letter_space = 145 / $this->string_length;
            $initial = 15;
            imagettftext($this->image, 18, rand(-15, 15), $initial + $i * $letter_space, rand(20, 40), $textcolors[rand(0, 1)], $this->fonts[array_rand($this->fonts)], $this->text[$i]);
        }
    }

    public function generateCaptcha()
    {
        $this->generateImage();
        $this->generateText();

        header('Content-type: image/png');
        imagepng($this->image);
        imagedestroy($this->image);
    }
}