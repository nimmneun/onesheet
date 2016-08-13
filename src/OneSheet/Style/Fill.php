<?php

namespace OneSheet\Style;

use OneSheet\Xml\StyleXml;

/**
 * Class Fill
 *
 * @package OneSheet
 */
class Fill
{
    /**
     * @var string
     */
    private $color;

    /**
     * @var string
     */
    private $pattern = 'none';

    /**
     * @param string $color
     * @return Fill
     */
    public function setColor($color)
    {
        $this->color = strtoupper($color);
        return $this;
    }

    /**
     * @param string $pattern
     * @return Fill
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * @return string
     */
    public function asXml()
    {
        if ($this->color) {
            return sprintf(StyleXml::COLORED_FILL_XML, $this->color);
        }
        return sprintf(StyleXml::PATTERN_FILL_XML, $this->pattern);
    }
}
