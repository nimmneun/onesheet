<?php
/**
 * @author neun
 * @since  2016-07-10
 */

namespace OneSheet;

/**
 * Class CtrlCharater
 * @package OneSheet
 */
class CtrlCharater
{
    /**
     * Build and return the map containing the 39 ASCII
     * characters what should be escaped/converted.
     *
     * @return array
     */
    public static function getMap()
    {
        $map = array();
        foreach (range(0,255) as $int) {
            if (ctype_cntrl(chr($int))) {
                $map['from'][] = chr($int);
                $map['to'][] = sprintf('_x%04s_', strtoupper(dechex($int)));
            }
        }

        return $map;
    }
}