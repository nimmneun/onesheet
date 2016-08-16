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
     *
     * @param WidthCollection $widthCollection
     */
    public function __construct(WidthCollection $widthCollection)
    {
        $this->widthCollection = $widthCollection;
    }

    /**
     * Returns the estimated width of a cell value.
     *
     * @param mixed $value
     * @param Font  $font
     * @return float
     */
    public function getCellWidth($value, Font $font)
    {
        $width = 0.07 * $font->getSize();
        foreach (preg_split('~~u', $value, -1, PREG_SPLIT_NO_EMPTY) as $character) {
            if (isset($this->characterWidths[$character])) {
                $width += $this->characterWidths[$character];
            } elseif (strlen($character)) {
                $width += 0.06 * $font->getSize();
            }
        }

        return $font->isBold() ? $width * $this->characterWidths['bold'] : $width;
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
