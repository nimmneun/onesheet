<?php

namespace OneSheet\Style;

use OneSheet\Xml\StyleXml;

/**
 * Class Border for reusable border definitions.
 *
 * @package OneSheet
 */
class Border implements Component
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var array
     */
    private $styles = array();

    /**
     * @var array
     */
    private $colors = array();

    /**
     * Direction of diagonal border (up/down).
     *
     * @var null
     */
    private $direction = null;

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string      $type
     * @param string      $style
     * @param string      $color
     * @param string|null $direction
     * @return Border
     */
    public function set($type, $style, $color, $direction = null)
    {
        $this->styles[$type] = $style;
        $this->colors[$type] = strtoupper($color);
        $this->direction = $direction;
        return $this;
    }

    /**
     * @return string
     */
    public function asXml()
    {
        if (!count($this->styles)) {
            return '<border/>';
        }

        $borderXml = '';
        foreach (array('left', 'right', 'top', 'bottom', 'diagonal') as $type) {
            $borderXml .= $this->getTypeXml($type);
        }

        $diagonal = isset($this->styles[BorderType::DIAGONAL])
            ? sprintf(' diagonal%s="1"', $this->direction) : '';

        return sprintf('<border%s>%s</border>', $diagonal, $borderXml);
    }

    /**
     * Return type specific border part xml string,
     * e.g. <left><color rgb="FF9900"/></left>.
     *
     * @param string $type
     * @return string
     */
    private function getTypeXml($type)
    {
        if (!isset($this->styles[$type])) {
            return "<{$type}/>";
        }

        return sprintf(StyleXml::BORDER_TYPE_XML,
            $type, $this->styles[$type], $this->colors[$type], $type);
    }
}
