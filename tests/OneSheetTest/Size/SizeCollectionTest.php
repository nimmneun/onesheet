<?php

namespace OneSheetTest\Width;

use OneSheet\Size\SizeCollection;

class SizeCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $collection = new SizeCollection();
        $fontWidths = $collection->get('Arial', 15);

        $this->assertGreaterThanOrEqual(1.9, $fontWidths['A']);
        $this->assertLessThanOrEqual(2.1, $fontWidths['A']);
    }

    public function testGetNonExistantFontAndSize()
    {
        $collection = new SizeCollection();
        $fontWidths = $collection->get('Somefont', 25);
        $defaultFontWidths = $collection->get('Calibri', 13);

        $this->assertLessThanOrEqual(array_sum($defaultFontWidths) * 2, array_sum($fontWidths));
    }
}
