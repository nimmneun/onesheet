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
     * @var Style
     */
    private $style;

    /**
     * Font constructor.
     *
     * @param Style $style
     */
    public function __construct(Style $style)
    {
        $this->style = $style;
    }

    /**
     * @return Style
     */
    public function style()
    {
        return $this->style;
    }

    /**
     * @param string $color
     * @return Fill
     */
    public function setColor($color)
    {
        $this->color = $color;
        $this->pattern = 'solid';
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
        return sprintf(StyleXml::BLANK_FILL_XML, $this->pattern);
    }
}
