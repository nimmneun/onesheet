# OneSheet
OneSheet is a simple **single sheet** excel/xlsx file writer for php 5.3+

```php
<?php require_once '../vendor/autoload.php';

$sheet = new \OneSheet\Sheet('A2');

$dataRows = array();
for ($i = 0; $i <= 1000; $i++) {
    $dataRows[$i]['SomeInt'] = $i;
    $dataRows[$i]['SomeFloat'] = number_format($i/3, 3);
    $dataRows[$i]['SomeString'] = substr(md5(microtime(1)+$i), rand(10,20));
}

$headerStyle = new \OneSheet\Style();

$sheet->addRow(array_keys($dataRows[0]), $headerStyle->bold()->color('FFFFFF')->fill('777777'));
$sheet->addRows($dataRows);

$writer = new \OneSheet\Writer($sheet, 'somefile.xlsx');
$writer->close();
```