<?php

namespace OneSheetTest\Width;

use OneSheet\Width\WidthCollection;

class WidthCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $collection = new WidthCollection();
        $fontWidths = $collection->get('Arial', 15);

        $this->assertGreaterThanOrEqual(1.9, $fontWidths['A']);
        $this->assertLessThanOrEqual(2.1, $fontWidths['A']);
    }

    public function testGetNonExistantFontAndSize()
    {
        $collection = new WidthCollection();
        $fontWidths = $collection->get('Somefont', 25);
        $defaultFontWidths = $collection->get('Calibri', 13);

        $this->assertLessThanOrEqual(array_sum($defaultFontWidths) * 2, array_sum($fontWidths));
    }
}
