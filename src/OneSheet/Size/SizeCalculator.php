<?php

namespace OneSheet\Size;

/**
 * Class SizeCalculator to calculate the approximate width
 * of cells based on the current font, size and content.
 *
 * @package OneSheet\Size
 */
class SizeCalculator
{
    /**
     * @var array
     */
    private $fonts;

    /**
     * @var array
     */
    private $sizes;

    /**
     * @param string|null $fontsDirectory
     */
    public function __construct($fontsDirectory)
    {
        $this->findFonts($this->determineFontsDir($fontsDirectory));
    }

    /**
     * Find fonts/paths in a given directory recursively.
     *
     * @param string|null $path
     * @return string|null
     */
    public function findFonts($path = null)
    {
        foreach (glob($path . DIRECTORY_SEPARATOR . '*') as $path) {
            if (is_dir($path)) {
                $path = $this->findFonts($path);
            } elseif (preg_match('~(.+\..tf)$~i', $path, $m)) {
                $meta = new FontMeta($path);
                if (strlen($meta->getFontName())) {
                    $this->fonts[$meta->getFontName()] = $path;
                }
            }
        }

        return $path;
    }

    /**
     * Return array of available font names and paths.
     *
     * @return array
     */
    public function getFonts()
    {
        return $this->fonts;
    }

    /**
     * Get the calculated cell width for a given value.
     *
     * @param string $fontName
     * @param int    $fontSize
     * @param mixed  $value
     *
     * @return float|int
     */
    public function getCellWidth($fontName, $fontSize, $value)
    {
        $width = 1;
        foreach ($this->getSingleCharacterArray($value) as $char) {
            $width += $this->getCharacterWidth($fontName, $fontSize, $char);
        }

        return $width;
    }

    /**
     * Get width of a single character. Calculate & cache if necessary.
     *
     * @param string $fontName
     * @param int    $fontSize
     * @param string $char
     *
     * @return float
     */
    private function getCharacterWidth($fontName, $fontSize, $char)
    {
        if (!isset($this->sizes[$fontName][$fontSize][$char])) {
            $this->sizes[$fontName][$fontSize][$char] =
                $this->calculateCharacterWith($fontName, $fontSize, $char);
        }

        return $this->sizes[$fontName][$fontSize][$char];
    }

    /**
     * Calculate the width of a single character for the given fontname and size.
     * Create image that contains the character 100 times to get more accurate results.
     *
     * @param string $fontName
     * @param int    $fontSize
     * @param string $char
     *
     * @return float
     */
    private function calculateCharacterWith($fontName, $fontSize, $char)
    {
        if (isset($this->fonts[$fontName])) {
            $box = imageftbbox($fontSize, 0, $this->fonts[$fontName], str_repeat($char, 100));
            $width = abs($box[4] - $box[0]) / 6.73 / 100;
            return round($width, 3);
        }

        return 0.1 * $fontSize;
    }

    /**
     * Split value into individual characters.
     *
     * @param mixed $value
     * @return array
     */
    private function getSingleCharacterArray($value)
    {
        if (mb_strlen($value) == strlen($value)) {
            return str_split($value);
        }

        return preg_split('~~u', $value, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Determine glob pattern by fonts directory.
     *
     * @param $fontsDirectory
     * @return string
     */
    private function determineFontsDir($fontsDirectory)
    {
        if (!isset($fontsDirectory) || !is_dir($fontsDirectory)) {
            $fontsDirectory = '/usr/share/fonts/truetype';
            if (false !== stripos(php_uname(), 'win')) {
                $fontsDirectory = 'C:/Windows/Fonts';
            }
        }

        return rtrim($fontsDirectory, DIRECTORY_SEPARATOR);
    }
}
