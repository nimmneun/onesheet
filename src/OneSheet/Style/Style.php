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
     *
     * @param int|null $id
     */
    public function __construct($id = null)
    {
        $this->id = $id;
        $this->font = new Font($this);
        $this->fill = new Fill($this);
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
    public function asXml()
    {
        return sprintf(StyleXml::DEFAULT_XF_XML, $this->getId(), $this->getId());
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
     * @return string
     */
    public function getHash()
    {
        return md5($this->font->asXml() . $this->fill->asXml());
    }
}
