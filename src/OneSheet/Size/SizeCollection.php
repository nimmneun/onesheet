<?php

namespace OneSheet\Size;

/**
 * Class SizeCollection to build and hold widths & heights
 * of individual characters.
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
     * Array containing character widths/heights for each font & size.
     *
     * @var array
     */
    private $sizes = array();

    /**
     * SizeCollection constructor.
     */
    public function __construct()
    {
        $this->loadSizesFromCsv();
    }

    /**
     * Return character sizes for given font name.
     *
     * @param string $fontName
     * @param int    $fontSize
     * @return array
     */
    public function get($fontName, $fontSize)
    {
        if (isset($this->sizes[$fontName][$fontSize])) {
            return $this->sizes[$fontName][$fontSize];
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
        foreach ($this->getBaseSizes($fontName) as $character => $size) {
            if ('bold' !== $character) {
                $size = round($size / self::BASE_SIZE * $fontSize, 3);
            }
            $this->sizes[$fontName][$fontSize][$character] = $size;
        }

        return $this->sizes[$fontName][$fontSize];
    }

    /**
     * Get character base widths by font name or default.
     *
     * @param string $fontName
     * @return array
     */
    private function getBaseSizes($fontName)
    {
        if (isset($this->sizes[$fontName])) {
            return $this->sizes[$fontName][self::BASE_SIZE];
        }

        return $this->sizes['Calibri'][self::BASE_SIZE];
    }

    /**
     * Initialize collection from csv file.
     */
    public function loadSizesFromCsv()
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
        $this->sizes[$fontName][$fontSize] = array_combine($head, $row);
    }
}
