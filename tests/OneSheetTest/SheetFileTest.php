<?php

namespace OneSheetTest;

use OneSheet\SheetFile;
use PHPUnit\Framework\TestCase;

class SheetFileTest extends TestCase
{
    public function testSheetFileThrowsErrorOnWrite()
    {
        $this->expectException(\RuntimeException::class);
        $sheetFile = new SheetFile();
        $sheetFile->fwrite('should work');
        $sheetFilePointer = $this->getObjectProperty($sheetFile, 'filePointer');
        fclose($sheetFilePointer);
        self::assertNotSame('just make sure we', 'reach this point');
        $sheetFile->fwrite('should fail');
    }

    public function testSheetFileThrowsErrorOneRewind()
    {
        $this->expectException(\RuntimeException::class);
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
