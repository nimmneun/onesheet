<?php

namespace OneSheet\Width;

use OneSheet\Style\Font;

/**
 * Class WidthCalculator to calculate the approximate width of
 * a cell content.
 *
 * @package OneSheet\Width
 */
class WidthCalculator
{
    /**
     * @var WidthCollection
     */
    private $widthCollection;

    /**
     * @var array
     */
    private $characterWidths;

    /**
     * Calculator constructor.
     */
    public function __construct()
    {
        $this->widthCollection = new WidthCollection();
    }

    /**
     * Returns a approximate width of a cell value.
     *
     * @param mixed $value
     * @param bool  $isBold
     * @return float
     */
    public function getCellWidth($value, $isBold)
    {
        $width = 1;
        foreach (str_split($value) as $character) {
            if (isset($this->characterWidths[$character])) {
                $width += $this->characterWidths[$character];
            } else {
                $width += 0.66;
            }
        }

        return $isBold ? $width * 1.1 : $width;
    }

    /**
     * Set proper font sizes by font.
     *
     * @param Font $font
     */
    public function setFont(Font $font)
    {
        $this->characterWidths = $this->widthCollection->get($font->getName(), $font->getSize());
    }
}
