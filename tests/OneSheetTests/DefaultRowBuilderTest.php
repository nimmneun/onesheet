<?php

namespace OneSheetTests;

use OneSheet\DefaultRowBuilder;

class RowHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $data = range(1,10);
        $string = sprintf('<row r="1" spans="1:%s"></row>', count($data));
        $rowBuilder = new DefaultRowBuilder($this->getMockBuilder('\\OneSheet\\CellBuilderInterface')->getMock());
        $this->assertEquals($string, $rowBuilder->build($data));
    }
}
