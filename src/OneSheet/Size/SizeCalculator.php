<?php

namespace OneSheet\Size;

use OneSheet\Style\Font;

/**
 * Class SizeCalculator to calculate the approximate width
 * and height of cells based on the font and content.
 *
 * @package OneSheet\Size
 */
class SizeCalculator
{
    /**
     * @var SizeCollection
     */
    private $sizeCollection;

    /**
     * @var array
     */
    private $characterSizes;

    /**
     * SizeCalculator constructor.
     *
     * @param SizeCollection $sizeCollection
     */
    public function __construct(SizeCollection $sizeCollection)
    {
        $this->sizeCollection = $sizeCollection;
    }

    /**
     * Return the estimated width of a cell value.
     *
     * @param mixed $value
     * @param Font  $font
     * @return float
     */
    public function getCellWidth($value, Font $font)
    {
        $width = 0.3 + (0.05 * $font->getSize());

        foreach ($this->getSingleCharacterArray($value) as $character) {
            if (isset($this->characterSizes[$character])) {
                $width += $this->characterSizes[$character];
            } elseif (strlen($character)) {
                $width += 0.06 * $font->getSize();
            }
        }

        return $font->isBold() ? $width * $this->characterSizes['bold'] : $width;
    }

    /**
     * Return the font height, but no smaller than 14pt.
     *
     * @return number
     */
    public function getRowHeight()
    {
        if (14 > $this->characterSizes['height']) {
            return 14;
        }
        return floor($this->characterSizes['height']);
    }

    /**
     * Set proper font sizes by font.
     *
     * @param Font $font
     */
    public function setFont(Font $font)
    {
        $this->characterSizes = $this->sizeCollection->get($font->getName(), $font->getSize());
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
}
