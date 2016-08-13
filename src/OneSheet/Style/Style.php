<?php

namespace OneSheet\Style;

use OneSheet\Xml\StyleXml;

/**
 * Class Style
 *
 * @package OneSheet
 */
class Style implements Component
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
     * @var bool
     */
    private $isLocked = false;

    /**
     * Style constructor.
     */
    public function __construct()
    {
        $this->font = new Font();
        $this->fill = new Fill();
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
    public function getFont()
    {
        if ($this->isLocked) {
            return clone $this->font;
        }
        return $this->font;
    }

    /**
     * @return Fill
     */
    public function getFill()
    {
        if ($this->isLocked) {
            return clone $this->fill;
        }
        return $this->fill;
    }

    /**
     * @param string $name
     * @return Style
     */
    public function setFontName($name)
    {
        $this->getFont()->setName($name);
        return $this;
    }

    /**
     * @param string $size
     * @return Style
     */
    public function setFontSize($size)
    {
        $this->getFont()->setSize($size);
        return $this;
    }

    /**
     * @param string $color
     * @return Style
     */
    public function setFontColor($color)
    {
        $this->getFont()->setColor($color);
        return $this;
    }

    /**
     * @return Style
     */
    public function setFontBold()
    {
        $this->getFont()->setBold();
        return $this;
    }

    /**
     * @return Style
     */
    public function setFontItalic()
    {
        $this->getFont()->setItalic();
        return $this;
    }

    /**
     * @return Style
     */
    public function setFontUnderline()
    {
        $this->getFont()->setUnderline();
        return $this;
    }

    /**
     * @return Style
     */
    public function setFontStrikethrough()
    {
        $this->getFont()->setStrikethrough();
        return $this;
    }

    /**
     * @param string $color
     * @return Style
     */
    public function setFillColor($color)
    {
        $this->getFill()->setColor($color);
        return $this;
    }

    /**
     * @param string $pattern
     * @return Style
     */
    public function setFillPattern($pattern)
    {
        $this->getFill()->setPattern($pattern);
        return $this;
    }

    /**
     * Lock current style.
     */
    public function lock()
    {
        $this->isLocked = true;
    }

    /**
     * @return string
     */
    public function asXml()
    {
        return sprintf(StyleXml::DEFAULT_XF_XML, $this->font->getId(), $this->fill->getId());
    }
}
