# OneSheet
OneSheet is a simple **single sheet** excel/xlsx file writer for php 5.3+

Since performance and memory usage were the main drivers, DOM and SimpleXml
where out of the question. Same goes for Cell and even Row objects.

This lib was built to satisfy the following needs:
- Write a single sheet with up to 2^20 rows fast and with a small
  memory footprint.
- Freeze the first [n] rows to have a fixed table header/headline.
- Option to use different fonts, styles and background colors on
  a row level.

Current major drawback(s):
- No cell individualisation, everything is applied at a row level
  and its intended to keep it that way.
- No calculated/formula cells. Only inlineStr and simple number type
  cells and it will probably stay that way.
- No control character escaping todo: RowHelper::addEscapeRow()


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