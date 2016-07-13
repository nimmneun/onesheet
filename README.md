# OneSheet

[![Build Status](https://travis-ci.org/nimmneun/OneSheet.svg?branch=master)](https://travis-ci.org/nimmneun/OneSheet)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nimmneun/OneSheet/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nimmneun/OneSheet/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/nimmneun/OneSheet/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/nimmneun/OneSheet/?branch=master)

OneSheet is a simple **single sheet** excel/xlsx file writer for PHP 5.3+ / 7.0+.

To control wheter a numeric value is generated as a string or number field,
simply typecast integers and doubles before adding the row to the sheet.

![Resulting OneSheet File in Excel](./tests/generated_xlsx_sample.png)

Since performance and memory usage were the main drivers, DOM and SimpleXml
where out of the question. Same goes for cell or even row objects.

This XLSX writer/generator is still WIP and was built to satisfy the following needs:
- Write a single sheet with up to 2^20 rows fast and with a small
  memory footprint.
- Freeze the first [n] rows to have a fixed table header/headline.
- Option to use different fonts, styles and background colors on
  a row level.
- PHP 5.3 compatibility.

Current major drawback(s):
- No cell individualisation, everything is applied at a row level.
- No calculated/formula cells.

```php
<?php require_once '../vendor/autoload.php';

// generate some dummy data
$dataRows = array();
for ($i = 1; $i <= 10000; $i++) {
    $dataRows[] = range($i, $i+4);
}

// memorize timings and memory usage
$t = -microtime(1);
$m = -memory_get_usage(1);

// create new sheet & freeze everything above the 2nd row
$sheet = OneSheet\Sheet::fromDefaults('A2');

// create new style and add headline using the style
$headerStyle = new \OneSheet\Style();
$sheet->addRow(
    array('some', 'fancy', 'table', 'header', 'here'),
    $headerStyle->bold()->color('FFFFFF')->fill('555555')
);

// add all data rows at once
$sheet->addRows($dataRows);

// create/write the xlsx file
$writer = new \OneSheet\Writer($sheet, 'somefile.xlsx');
$writer->close();

// echo OneSheet timings und memory usage
echo (microtime(1) + $t) . ' seconds' . PHP_EOL;
echo (memory_get_usage(1) + $m) . ' bytes' . PHP_EOL;
```
