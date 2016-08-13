<?php

namespace OneSheet\Style;

use OneSheet\Xml\StyleXml;

/**
 * Class Styler to keep track of registered styles.
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
     * @var Font[]
     */
    private $fonts = array();

    /**
     * @var Fill[]
     */
    private $fills = array();

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
        $this->addStyle($grey->setFillPattern('grey125'));
    }

    /**
     * Add a new style, if it doesnt exists yet.
     *
     * @param Style $style
     */
    public function addStyle(Style $style)
    {
        if (null === $style->getId()) {
            $this->register($style->getFont(), $this->fonts);
            $this->register($style->getFill(), $this->fills);
            $this->register($style, $this->styles);
            $style->lock();
        }
    }

    /**
     * Register a new Style compoment its compoments to account
     * for reusable styles, fills, fonts, ...
     *
     * @param Component $component
     * @param array     $collection
     */
    private function register(Component $component, &$collection)
    {
        $hash = md5($component->asXml());
        if (isset($this->hashes[$hash])) {
            $component->setId($this->hashes[$hash]);
        } else {
            $newId = count($collection);
            $component->setId($newId);
            $collection[$newId] = $component;
            $this->hashes[$hash] = $newId;
        }
    }

    /**
     * @return Style
     */
    public function getDefaultStyle()
    {
        return $this->styles[0];
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
        }

        foreach ($this->fonts as $font) {
            $fontsXml .= $font->asXml();
        }

        foreach ($this->fills as $fill) {
            $fillsXml .= $fill->asXml();
        }

        return sprintf(StyleXml::STYLE_SHEET_XML,
            sprintf(StyleXml::FONTS_XML, count($this->fonts), $fontsXml),
            sprintf(StyleXml::FILLS_XML, count($this->fills), $fillsXml),
            sprintf(StyleXml::CELL_XFS_XML, count($this->styles), $cellXfsXml)
        );
    }
}
