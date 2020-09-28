<?php

namespace OneSheetTest;

use OneSheet\SheetFile;

class SheetFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testSheetFileThrowsErrorOnWrite()
    {
        $sheetFile = new SheetFile();
        $sheetFile->fwrite('should work');
        $sheetFilePointer = $this->getObjectProperty($sheetFile, 'filePointer');
        fclose($sheetFilePointer);
        self::assertNotSame('just make sure we', 'reach this point');
        $sheetFile->fwrite('should fail');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSheetFileThrowsErrorOneRewind()
    {
        $sheetFile = new SheetFile();
        $sheetFilePointer = $this->getObjectProperty($sheetFile, 'filePointer');
        fclose($sheetFilePointer);
        self::assertNotSame('just make sure we', 'reach this point');
        $sheetFile->rewind();
    }

    private function getObjectProperty($object, $propertyName)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}
