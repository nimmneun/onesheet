<?php

namespace OneSheet\Style;

use OneSheet\Xml\StyleXml;

/**
 * Class Styler
 *
 * @package OneSheet
 */
class Styler
{
    /**
     * @var Style[]
     */
    private $styles = array();

    /**
     * @var array
     */
    private $hashes = array();

    /**
     * Styler constructor.
     */
    public function __construct()
    {
        $this->addStyle(new Style());
        $grey = new Style();
        $grey->fill()->setPattern('grey125');
        $this->addStyle($grey);
    }

    /**
     * Register a new style if it doesnt exists.
     *
     * @param Style $style
     */
    public function addStyle(Style $style)
    {
        if (!isset($this->hashes[$style->getHash()])) {
            $this->registerStyle($style, $style->getHash());
        } else {
            $style->setId($this->styles[$this->hashes[$style->getHash()]]->getId());
        }
    }

    /**
     * Register a new style.
     *
     * @param Style  $style
     * @param string $styleHash
     */
    private function registerStyle(Style $style, $styleHash)
    {
        $style->setId(count($this->styles))->getId();
        $this->styles[$style->getId()] = $style;
        $this->hashes[$styleHash] = $style->getId();
    }

    /**
     * Return style by its id.
     *
     * @param int $id
     * @return Style
     */
    public function getStyleById($id)
    {
        return $this->styles[$id];
    }

    /**
     * Return entire xml string for the style sheet.
     *
     * @return string
     */
    public function getStyleSheetXml()
    {
        $fontsXml = $fillsXml = $cellXfsXml = '';
        foreach ($this->styles as $style) {
            $cellXfsXml .= $style->asXml();
            $fontsXml .= $style->font()->asXml();
            $fillsXml .= $style->fill()->asXml();
        }

        return sprintf(StyleXml::STYLE_SHEET_XML,
            sprintf(StyleXml::FONTS_XML, count($this->styles), $fontsXml),
            sprintf(StyleXml::FILLS_XML, count($this->styles), $fillsXml),
            sprintf(StyleXml::CELL_XFS_XML, count($this->styles), $cellXfsXml)
        );
    }
}
