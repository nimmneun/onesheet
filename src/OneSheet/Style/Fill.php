<?php

namespace OneSheet\Style;

use OneSheet\Xml\StyleXml;

class Fill implements Component
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $color;

    /**
     * @var string
     */
    private $pattern = 'none';

    /**
     * @param int $id
     * @return Fill
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

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
