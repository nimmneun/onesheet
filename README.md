# OneSheet
OneSheet is a simple **single sheet** excel/xlsx file writer for php 5.3+

```php
require_once '../vendor/autoload.php';

$writer = new \OneSheet\Writer('dummy.xlsx', new \OneSheet\Sheet('A5'));

$style1 = new \OneSheet\Style();
$writer->sheet()->addRow($header, $style1->bold()->setSize(12)->color('FFFFFF')->fill('777777'));

$style2 = new \OneSheet\Style();
$styleId = $writer->sheet()->addStyle($style2->setSize(11));

foreach ($resultRows as $resultRow) {
    $writer->sheet()->addRow($resultRow, $styleId);
}

$writer->close();
```