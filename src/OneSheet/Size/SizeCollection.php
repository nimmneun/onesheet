<?php

namespace OneSheet\Size;

/**
 * Class WidthCollection
 *
 * @package OneSheet
 */
class SizeCollection
{
    /**
     * Constant for default character size.
     */
    const BASE_SIZE = 12;

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
        self::loadWidthsFromCsv(dirname(__FILE__) . '/size_collection.csv');
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
        foreach (self::getBaseWidths($fontName) as $character => $width) {
            if ('bold' !== $character) {
                $width = round($width / self::BASE_SIZE * $fontSize, 3);
            }
            self::$widths[$fontName][$fontSize][$character] = $width;
        }

        return self::$widths[$fontName][$fontSize];
    }

    /**
     * Get character base widths by font name or default.
     *
     * @param string $fontName
     * @return array
     */
    private static function getBaseWidths($fontName)
    {
        if (isset(self::$widths[$fontName])) {
            return self::$widths[$fontName][self::BASE_SIZE];
        }
        return self::$widths['Calibri'][self::BASE_SIZE];
    }
}
