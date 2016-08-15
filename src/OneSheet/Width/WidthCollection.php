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
            if ($line[0] == 'f') continue;
            $widths = explode(',', trim($line));
            if (count(range(33, 126)) + 3 == count($widths)) {
                $fontName = array_shift($widths);
                $fontSize = array_shift($widths);
                $boldMulti = array_shift($widths);
                self::$widths[$fontName][$fontSize] = array_combine(array_map('chr', range(33, 126)), $widths);
                self::$widths[$fontName][$fontSize] += array('bold' => $boldMulti);
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
        } elseif (isset(self::$widths['Calibri'][$fontSize])) {
            return self::$widths['Calibri'][$fontSize];
        }

        return self::$widths['Calibri'][11];
    }
}
