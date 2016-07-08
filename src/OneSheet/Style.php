<?php
/**
 * @author neun
 * @since  2016-07-03
 */

namespace OneSheet;

/**
 * Class Style
 * @package OneSheet
 */
class Style
{
    /**
     * @var int
     */
    private $size = 11;

    /**
     * @var string
     */
    private $color = '000000';

    /**
     * @var string
     */
    private $name = 'Calibri';

    /**
     * @var string
     */
    private $bold;

    /**
     * @var string
     */
    private $italic;

    /**
     * @var string
     */
    private $underline;

    /**
     * @var string
     */
    private $fill;

    /**
     * @return string
     */
    public function getFontXml()
    {
        return sprintf('<font><sz val="%s"/><color rgb="%s"/><name val="%s"/>%s%s%s</font>',
            $this->size, $this->color, $this->name, $this->bold, $this->italic, $this->underline
        );
    }

    /**
     * @return string
     */
    public function getFillXml()
    {
        if (!$this->fill) {
            return '<fill><patternFill patternType="none"/></fill>';
        }
        return '<fill><patternFill patternType="solid"><fgColor rgb="' . $this->fill . '"/></patternFill></fill>';
    }

    /**
     * Set font size.
     *
     * @param int $size
     * @return Style
     */
    public function size($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Set font color.
     *
     * @param string $color
     * @return Style
     */
    public function color($color)
    {
        $this->color = strtoupper($color);
        return $this;
    }

    /**
     * Set font name.
     *
     * @param string $name
     * @return Style
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Make font bold.
     *
     * @return Style
     */
    public function bold()
    {
        $this->bold = '<b/>';
        return $this;
    }

    /**
     * Make font italic.
     *
     * @return Style
     */
    public function italic()
    {
        $this->italic = '<i/>';
        return $this;
    }

    /**
     * Underline the font text.
     *
     * @return Style
     */
    public function underline()
    {
        $this->underline = '<u/>';
        return $this;
    }

    /**
     * Set the cell fill/background color.
     *
     * @param string $fill
     * @return Style
     */
    public function fill($fill)
    {
        $this->fill = strtoupper($fill);
        return $this;
    }
}
