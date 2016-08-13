<?php

namespace OneSheet\Style;

use OneSheet\Xml\StyleXml;

/**
 * Class Style
 *
 * @package OneSheet
 */
class Style
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Font
     */
    private $font;

    /**
     * @var Fill
     */
    private $fill;

    /**
     * Style constructor.
     */
    public function __construct()
    {
        $this->font = new Font($this);
        $this->fill = new Fill($this);
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Style
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Font
     */
    public function font()
    {
        return $this->font;
    }

    /**
     * @return Fill
     */
    public function fill()
    {
        return $this->fill;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return md5($this->font->asXml() . $this->fill->asXml());
    }

    /**
     * @return string
     */
    public function asXml()
    {
        return sprintf(StyleXml::DEFAULT_XF_XML, $this->getId(), $this->getId());
    }

    /**
     * @param string $name
     * @return Style
     */
    public function setFontName($name)
    {
        $this->font->setName($name);
        return $this;
    }

    /**
     * @param string $size
     * @return Style
     */
    public function setFontSize($size)
    {
        $this->font->setSize($size);
        return $this;
    }

    /**
     * @param string $color
     * @return Style
     */
    public function setFontColor($color)
    {
        $this->font->setColor($color);
        return $this;
    }

    /**
     * @return Style
     */
    public function setFontBold()
    {
        $this->font->setBold();
        return $this;
    }

    /**
     * @return Style
     */
    public function setFontItalic()
    {
        $this->font->setItalic();
        return $this;
    }

    /**
     * @return Style
     */
    public function setFontUnderline()
    {
        $this->font->setUnderline();
        return $this;
    }

    /**
     * @return Style
     */
    public function setFontStrikethrough()
    {
        $this->font->setStrikethrough();
        return $this;
    }

    /**
     * @param string $color
     * @return Style
     */
    public function setFillColor($color)
    {
        $this->fill->setColor($color);
        return $this;
    }

    /**
     * @param string $pattern
     * @return Style
     */
    public function setFillPattern($pattern)
    {
        $this->fill->setPattern($pattern);
        return $this;
    }
}
