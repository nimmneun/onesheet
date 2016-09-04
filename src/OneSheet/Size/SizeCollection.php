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
    private $widths = array();

    /**
     * SizeCollection constructor.
     */
    public function __construct()
    {
        $this->loadWidthsFromCsv();
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
        if (isset($this->widths[$fontName][$fontSize])) {
            return $this->widths[$fontName][$fontSize];
        }

        return $this->calculate($fontName, $fontSize);
    }

    /**
     * Calculate character widths based on font name and size.
     *
     * @param string $fontName
     * @param int    $fontSize
     * @return array
     */
    private function calculate($fontName, $fontSize)
    {
        foreach ($this->getBaseWidths($fontName) as $character => $width) {
            if ('bold' !== $character) {
                $width = round($width / self::BASE_SIZE * $fontSize, 3);
            }
            $this->widths[$fontName][$fontSize][$character] = $width;
        }

        return $this->widths[$fontName][$fontSize];
    }

    /**
     * Get character base widths by font name or default.
     *
     * @param string $fontName
     * @return array
     */
    private function getBaseWidths($fontName)
    {
        if (isset($this->widths[$fontName])) {
            return $this->widths[$fontName][self::BASE_SIZE];
        }
        return $this->widths['Calibri'][self::BASE_SIZE];
    }

    /**
     * Initialize collection from csv file.
     */
    public function loadWidthsFromCsv()
    {
        $fh = fopen(dirname(__FILE__) . '/size_collection.csv', 'r');
        $head = fgetcsv($fh);
        unset($head[0], $head[1]);

        while ($row = fgetcsv($fh)) {
            $this->addSizesToCollection($head, $row);
        }
    }

    /**
     * Add character widths for a single font.
     *
     * @param array $head
     * @param array$row
     */
    private function addSizesToCollection(array $head, array $row)
    {
        $fontName = array_shift($row);
        $fontSize = array_shift($row);
        $this->widths[$fontName][$fontSize] = array_combine($head, $row);
    }
}
