<?php

namespace OneSheet;

/**
 * Class SheetFile
 *
 * @package OneSheet
 */
class SheetFile
{
    /**
     * @var resource
     */
    private $filePointer;

    /**
     * @var string
     */
    private $filePath;

    /**
     * SheetFile constructor.
     *
     * @throws \RuntimeException
     */
    public function __construct()
    {
        $this->filePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid(null, 1) . '.xml';
        if (!$this->filePointer = fopen($this->filePath, 'wb+')) {
            throw new \RuntimeException("Failed to create temporary file {$this->filePath}!");
        }
    }

    /**
     * @param $string
     */
    public function fwrite($string)
    {
        if (!fwrite($this->filePointer, $string)) {
            throw new \RuntimeException("Failed to write to sheet file!");
        }
    }

    /**
     * @return bool
     */
    public function rewind()
    {
        return rewind($this->filePointer);
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Close file pointer and delete sheet file.
     */
    public function __destruct()
    {
        if (!fclose($this->filePointer) || !unlink($this->filePath)) {
            throw new \RuntimeException('Failed to close sheetfile!');
        }
    }
}
