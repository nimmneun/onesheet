<?php

namespace OneSheet\Width;

use OneSheet\Style\Font;

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
     * @param mixed $value
     * @param bool  $bold
     * @return float
     */
    public function getCellWidth($value, $bold)
    {
        $width = 1;
        foreach (str_split($value) as $character) {
            if (isset($this->characterWidths[$character])) {
                $width += $this->characterWidths[$character];
            } else {
                $width += 0.66;
            }
        }

        return !$bold ? $width : $width * 1.1;
    }

    /**
     * @param Font $font
     */
    public function setFont(Font $font)
    {
        $this->characterWidths = $this->widthCollection->get($font->getName(), $font->getSize());
    }
}
