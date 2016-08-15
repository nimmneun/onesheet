<?php

namespace OneSheet\Style;

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
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $type
     * @param string $style
     * @param string $color
     * @return Border
     */
    public function set($type, $style, $color)
    {
        $this->styles[$type] = $style;
        $this->colors[$type] = strtoupper($color);
        return $this;
    }

    /**
     * @return string
     */
    public function asXml()
    {
        if (!count($this->styles)) return '<border/>';

        $borderXml = '';
        foreach (array('left', 'right', 'top', 'bottom', 'diagonal') as $type) {
            $borderXml .= $this->getTypeXml($type);
        }

        $diagonal = isset($this->styles[BorderType::DIAGONAL]) ? ' diagonalUp="1"' : '';
        return sprintf('<border%s>%s</border>', $diagonal, $borderXml);
    }

    /**
     * @param $type
     * @return string
     */
    private function getTypeXml($type)
    {
        if (!isset($this->styles[$type])) {
            return "<{$type}/>";
        }
        return sprintf('<%s style="%s"><color rgb="%s"/></%s>',
            $type, $this->styles[$type], $this->colors[$type], $type
        );
    }
}
