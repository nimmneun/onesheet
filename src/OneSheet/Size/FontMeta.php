<?php

namespace OneSheet\Size;

/**
 * Class FontMeta to determine available ttf fonts for cell autosizing.
 *
 * @method string getCopyright()
 * @method string getFontFamily()
 * @method string getFontSubFamily()
 * @method string getFontIdentifier()
 * @method string getFontName()
 * @method string getFontVersion()
 * @method string getPostscriptName()
 * @method string getTrademark()
 * @method string getManufacturerName()
 * @method string getDesigner()
 * @method string getDescription()
 * @method string getVendorUrl()
 * @method string getDesignerUrl()
 * @method string getLicenseDescription()
 * @method string getLicenseUrl()
 * @method string getReservedField()
 * @method string getPreferredFamily()
 * @method string getPreferredSubFamily()
 * @method string getCompatibleFullName()
 * @method string getPostscriptCid()
 */
class FontMeta
{
    /**
     * @var string
     */
    private $fileName;

    /**
     * @var array
     */
    private $data = array(
        'copyright' => null,
        'fontFamily' => null,
        'fontSubFamily' => null,
        'fontIdentifier' => null,
        'fontName' => null,
        'fontVersion' => null,
        'postscriptName' => null,
        'trademark' => null,
        'manufacturerName' => null,
        'designer' => null,
        'description' => null,
        'vendorUrl' => null,
        'designerUrl' => null,
        'licenseDescription' => null,
        'licenseUrl' => null,
        'reservedField' => null,
        'preferredFamily' => null,
        'preferredSubFamily' => null,
        'compatibleFullName' => null,
        'postscriptCid' => null,
    );

    /**
     * FontMeta constructor.
     *
     * @param string $fileName
     * @throws \Exception
     */
    function __construct($fileName)
    {
        if (!is_readable($fileName)) {
            throw new \Exception('File ' . $fileName . ' is not readable');
        }

        $this->fileName = $fileName;
        $this->readFontMetadata();
    }

    /**
     * @param string $name
     * @param array  $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        $property = lcfirst(substr($name, 3));
        return isset($this->data[$property]) ? $this->data[$property] : null;
    }

    /**
     * Read the font Metadata
     *
     * @throws \Exception
     */
    public function readFontMetadata()
    {
        $fontHandle = fopen($this->fileName, "r");
        $offset = $this->getNameTableOffset($fontHandle);

        fseek($fontHandle, $offset, SEEK_SET);

        $nameTableHeader = fread($fontHandle, 6);

        $numberOfRecords = $this->unpack(substr($nameTableHeader, 2, 2));
        $storageOffset = $this->unpack(substr($nameTableHeader, 4, 2));

        for ($a = 0; $a < $numberOfRecords; $a++) {
            $this->findAndExtractCandidate($fontHandle, $offset, $storageOffset);
        }

        fclose($fontHandle);
    }

    /**
     * @param resource $fontHandle
     *
     * @return int
     * @throws \Exception
     */
    private function getNameTableOffset($fontHandle)
    {
        $numberOfTables = $this->unpack(substr(fread($fontHandle, 12), 4, 2));

        for ($t = 0; $t < $numberOfTables; $t++) {
            $tableDirectory = fread($fontHandle, 16);
            $szTag = substr($tableDirectory, 0, 4);
            if (strtolower($szTag) == 'name') {
                return $this->unpack(substr($tableDirectory, 8, 4), 'N');
            }
        }

        fclose($fontHandle);
        throw new \Exception('Can\'t find name table in ' . $this->fileName);
    }

    /**
     * @param resource $fontHandle
     * @param int      $offset
     * @param int      $storageOffset
     */
    private function findAndExtractCandidate($fontHandle, $offset, $storageOffset)
    {
        $nameRecord = fread($fontHandle, 12);

        $nameId = $this->unpack(substr($nameRecord, 6, 2));

        $stringLength = $this->unpack(substr($nameRecord, 8, 2));
        $stringOffset = $this->unpack(substr($nameRecord, 10, 2));

        if ($stringLength > 0) {
            $position = ftell($fontHandle);
            fseek($fontHandle, $offset + $stringOffset + $storageOffset, SEEK_SET);

            $testValue = fread($fontHandle, $stringLength);
            $this->extractCandidate($nameId, $testValue);

            fseek($fontHandle, $position, SEEK_SET);
        }
    }

    /**
     * Extract possible property/attribute. $data keys are
     * intentionally sorted in order of the font file table.
     *
     * @param int    $nameId
     * @param string $testValue
     */
    private function extractCandidate($nameId, $testValue)
    {
        $map = array_keys($this->data);
        if (strlen(trim($testValue)) && array_key_exists($nameId, $map)) {
            $this->data[$map[$nameId]] = $this->cleanupValue(trim($testValue));
        }
    }

    /**
     * Remove ASCII ctrl characters
     *
     * @param string $value
     * @return string
     */
    private function cleanupValue($value)
    {
        return preg_replace('/[[:cntrl:]]/', '', trim($value));
    }

    /**
     *  Convert big endian unsigned short[n] or long[N] value to an integer.
     *
     * @param string $value
     * @param string $format
     * @return int
     */
    private function unpack($value, $format = 'n')
    {
        $unpacked = unpack($format, $value);
        return (int)array_pop($unpacked);
    }
}
