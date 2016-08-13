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
     * @throws \Exception
     */
    public function __construct()
    {
        $this->filePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid(null, 1) . '.xml';
        if (!$this->filePointer = fopen($this->filePath, 'wb+')) {
            throw new \Exception("Failed to create temporary file {$this->filePath}!");
        }
    }

    /**
     * @param $string
     * @return bool
     */
    public function fwrite($string)
    {
        return (bool)fwrite($this->filePointer, $string);
    }

    /**
     * @return bool
     */
    public function rewind()
    {
        return rewind($this->filePointer);
    }

    /**
     * Close and delete the file.
     */
    public function __destruct()
    {
        fclose($this->filePointer);
        unlink($this->filePath);
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }
}
