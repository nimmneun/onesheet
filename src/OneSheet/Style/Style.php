<?php

namespace OneSheet\Style;

use OneSheet\Xml\StyleXml;

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
     * @var Border
     */
    private $border;

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
        $this->border = new Border();
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
     * @return Border
     */
    public function getBorder()
    {
        if ($this->isLocked) {
            return clone $this->border;
        }
        return $this->border;
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
     * Convenience method to set all borders at once.
     *
     * @param string $style
     * @param string $color
     * @return Style
     */
    public function setSurroundingBorder($style = BorderStyle::THIN, $color = '000000')
    {
        $this->setBorderLeft($style, $color);
        $this->setBorderRight($style, $color);
        $this->setBorderTop($style, $color);
        $this->setBorderBottom($style, $color);
        return $this;
    }
    
    /**
     * @param string $style
     * @param string $color
     * @return Style
     */
    public function setBorderLeft($style = BorderStyle::THIN, $color = '000000')
    {
        $this->getBorder()->set(BorderType::LEFT, $style, $color);
        return $this;
    }

    /**
     * @param string $style
     * @param string $color
     * @return Style
     */
    public function setBorderRight($style = BorderStyle::THIN, $color = '000000')
    {
        $this->getBorder()->set(BorderType::RIGHT, $style, $color);
        return $this;
    }

    /**
     * @param string $style
     * @param string $color
     * @return Style
     */
    public function setBorderTop($style = BorderStyle::THIN, $color = '000000')
    {
        $this->getBorder()->set(BorderType::TOP, $style, $color);
        return $this;
    }

    /**
     * @param string $style
     * @param string $color
     * @return Style
     */
    public function setBorderBottom($style = BorderStyle::THIN, $color = '000000')
    {
        $this->getBorder()->set(BorderType::BOTTOM, $style, $color);
        return $this;
    }

    /**
     * @param string $style
     * @param string $color
     * @return Style
     */
    public function setBorderDiagonalUp($style = BorderStyle::THIN, $color = '000000')
    {
        $this->getBorder()->set(BorderType::DIAGONAL, $style, $color, BorderType::DIRECTION_UP);
        return $this;
    }

    /**
     * @param string $style
     * @param string $color
     * @return Style
     */
    public function setBorderDiagonalDown($style = BorderStyle::THIN, $color = '000000')
    {
        $this->getBorder()->set(BorderType::DIAGONAL, $style, $color, BorderType::DIRECTION_DOWN);
        return $this;
    }

    /**
     * Lock current style to prevent overwriting of existing styles.
     *
     * @return Style
     */
    public function lock()
    {
        $this->isLocked = true;
        return $this;
    }

    /**
     * Return single <xf> string for current style.
     *
     * @return string
     */
    public function asXml()
    {
        return sprintf(
            StyleXml::DEFAULT_XF_XML,
            $this->font->getId(),
            $this->fill->getId(),
            $this->border->getId()
        );
    }
}
