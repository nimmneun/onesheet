<?php

namespace OneSheet;

/**
 * Class SheetFile, just to abstract file operations awayyy.
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
            throw new \RuntimeException("Failed to create temporary sheet file {$this->filePath}!");
        }
    }

    /**
     * Write a single string.
     *
     * @param $string
     */
    public function fwrite($string)
    {
        if (false === fwrite($this->filePointer, $string)) {
            throw new \RuntimeException("Failed to write to sheet file!");
        }
    }

    /**
     * Rewind file (to write header and column widths).
     */
    public function rewind()
    {
        if (false === rewind($this->filePointer)) {
            throw new \RuntimeException("Failed to rewind sheet file!");
        }
    }

    /**
     * Return full path of the file.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Close file pointer and delete file.
     */
    public function __destruct()
    {
        if (!fclose($this->filePointer) || !unlink($this->filePath)) {
            throw new \RuntimeException('Failed to close sheet file!');
        }
    }
}
