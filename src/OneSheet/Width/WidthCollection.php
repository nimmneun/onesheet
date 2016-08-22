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
        self::loadWidthsFromCsv(dirname(__FILE__) . '/width_collection.csv');
    }

    /**
     * Dirty way to allow developers to load character widths that
     * are not yet included.
     *
     * @param string $csvPath
     */
    public static function loadWidthsFromCsv($csvPath)
    {
        $fh = fopen($csvPath, 'r');
        $head = fgetcsv($fh);
        unset($head[0], $head[1]);
        while ($row = fgetcsv($fh)) {
            $fontName = array_shift($row);
            $fontSize = array_shift($row);
            self::$widths[$fontName][$fontSize] = array_combine($head, $row);
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

        return self::calculate($fontName, $fontSize);
    }

    /**
     * Calculate character widths based on font name and size.
     *
     * @param string $fontName
     * @param int    $fontSize
     * @return array
     */
    private static function calculate($fontName, $fontSize)
    {
        if (isset(self::$widths[$fontName])) {
            $baseWidths = self::$widths[$fontName][12];
        } else {
            $baseWidths = self::$widths['Calibri'][12];
        }

        foreach ($baseWidths as $character => $width) {
            self::$widths[$fontName][$fontSize][$character] = $width / 12 * $fontSize;
        }

        return self::$widths[$fontName][$fontSize];
    }
}
