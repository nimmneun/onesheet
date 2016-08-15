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
     * Holds unique styles.
     *
     * @var Style[]
     */
    private $styles = array();

    /**
     * Holds unique fonts.
     *
     * @var Font[]
     */
    private $fonts = array();

    /**
     * Holds unique fills.
     *
     * @var Fill[]
     */
    private $fills = array();

    /**
     * Holds unique borders.
     *
     * @var Border[]
     */
    private $borders = array();

    /**
     * Holds (hash => component id) mappings.
     *
     * @var array
     */
    private $hashes = array();

    /**
     * Styler constructor to initialize reserved default styles.
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
            $this->register($style->getBorder(), $this->borders);
            $this->register($style, $this->styles);
            $style->lock();
        }
    }

    /**
     * Register a new style/font/fill component to account
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
     * Return default style (Calibri,11).
     *
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
        $fontsXml = $this->getComponentXml($this->fonts);
        $fillsXml = $this->getComponentXml($this->fills);
        $bordersXml = $this->getComponentXml($this->borders);
        $cellXfsXml = $this->getComponentXml($this->styles);

        return sprintf(StyleXml::STYLE_SHEET_XML,
            sprintf(StyleXml::FONTS_XML, count($this->fonts), $fontsXml),
            sprintf(StyleXml::FILLS_XML, count($this->fills), $fillsXml),
            sprintf(StyleXml::BORDERS_XML, count($this->borders), $bordersXml),
            sprintf(StyleXml::CELL_XFS_XML, count($this->styles), $cellXfsXml)
        );
    }

    /**
     * Return fonts, fills, borders, xfs xml strings.
     *
     * @param Component[] $components
     * @return string
     */
    private function getComponentXml(array $components)
    {
        $componentXml = '';
        foreach ($components as $component) {
            $componentXml .= $component->asXml();
        }
        return $componentXml;
    }
}
