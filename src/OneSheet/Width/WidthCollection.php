<?php

namespace OneSheet\Width;

/**
 * Class WidthCollection
 *
 * @package OneSheet
 */
class WidthCollection
{
    /**
     * Array containing character widths for each font & size.
     *
     * @var array
     */
    private static $widths = array();

    /**
     * Create character width map for each font.
     */
    public function __construct()
    {
        self::loadWidthsFromCsv(dirname(__FILE__) . '/widths.csv');
    }

    /**
     * Dirty way to allow developers to load character widths that
     * are not yet included.
     *
     * @param string $csvPath
     */
    public static function loadWidthsFromCsv($csvPath)
    {
        foreach (file($csvPath) as $line) {
            $widths = explode(',', trim($line));
            if (count(range(33, 126)) + 2 == count($widths)) {
                self::$widths[array_shift($widths)][array_shift($widths)] =
                    array_combine(array_map('chr', range(33, 126)), $widths);
            }
        }
    }

    /**
     * Return character widths for given font name.
     *
     * @param string $fontName
     * @param int    $fontSize
     * @return array
     */
    public function get($fontName, $fontSize)
    {
        if (isset(self::$widths[$fontName][$fontSize])) {
            return self::$widths[$fontName][$fontSize];
        }

        return self::$widths['Calibri'][11];
    }
}
