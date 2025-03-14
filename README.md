# OneSheet

[![Build Status](https://travis-ci.org/nimmneun/onesheet.svg?branch=master)](https://travis-ci.org/nimmneun/onesheet)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nimmneun/onesheet/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nimmneun/onesheet/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/nimmneun/onesheet/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/nimmneun/onesheet/?branch=master)
[![Downloads](https://img.shields.io/packagist/dm/nimmneun/onesheet)](https://img.shields.io/packagist/dm/nimmneun/onesheet)


OneSheet is a simple **single/multi sheet** excel/xlsx file writer for PHP 5 (until v1.2.6), PHP 7 & PHP 8 with cell auto-sizing and styling support.

![alt text](autosizing_excel_screencap.png "OneSheet excel output example")

### What it does
- Write a single/multiple spreadsheet(s) fast and with a small memory footprint.
- Freeze the first [n] rows to have a fixed table header/headline per sheet.
- Use different fonts, styles, borders and background colors on a row level.
- Set your own custom column width per column.
- Autosize column widths to fit cell contents. If no fonts are found, rough estimates are used.
- Define minimum and maximum column widths to keep exceptionally large or small cell contents in check.

### What it doesn't
- No cell individualisation, everything is applied at a row level.
- No calculated / formula cells.
- No conditional formatting.
- No number formats.
- No charts.

### Install via composer
```
$ composer require nimmneun/onesheet
```

### Manual installation
If you can't or don't want to use composer for some reason,
[download](https://github.com/nimmneun/onesheet/releases/latest) & extract onsheet and require the file autoload.php from the releases root folder.
```php
<?php
// path to onesheet autoload file on your server / webspace e.g.:
require_once '/srv/fancydomain.com/libs/onesheet/autoload.php';
```

### Minimal working example
```php
<?php

require_once '../vendor/autoload.php';

$onesheet = new \OneSheet\Writer('/optional/fonts/directory');
$onesheet->addRow(array('hello', 'world'));
$onesheet->writeToFile('hello_world.xlsx');
```

#### Available Writer operations
```
Writer::setFreezePaneCellId(string $cellId)
Writer::setPrintTitleRange(int $startRow, int $endRow)
Writer::switchSheet(string $sheetName)
Writer::setFixedColumnWidths(array $columnWidths)
Writer::setColumnWidthLimits(float $minWidth, float $maxWidth)
Writer::enableCellAutosizing()
Writer::disableCellAutosizing()
Writer::addRows(array $rows, Style $style)
Writer::addRow(array $row, Style $style)
Writer::writeToFile(string $fileName)
Writer::writeToBrowser(string $fileName)
```

#### Adding font styles
```
Style::setFontName(string $name)
Style::setFontSize(int $size)
Style::setFontColor(string $color)
Style::setFontBold()
Style::setFontItalic()
Style::setFontUnderline()
Style::setFontStrikethrough()
```
#### Adding background colors (fills)
```
Style::setFillColor(string $color)
```

#### Adding borders
```
Style::setSurroundingBorder(string $style, string $color)
Style::setBorderLeft(string $style, string $color)
Style::setBorderRight(string $style, string $color)
Style::setBorderTop(string $style, string $color)
Style::setBorderBottom(string $style, string $color)
Style::setBorderDiagonalUp(string $style, string $color)
Style::setBorderDiagonalDown(string $style, string $color)
```

### Cell auto-sizing
##### ... is cool, but comes with heavy performance impacts - especially when dealing with multibyte characters like ä, ß, Æ, ポ.
Keep in mind though ... you can improve runtimes for larger datasets by disabling it after adding a decent number of rows.

| Impacts of autosizing                 | 100k rows * 10 cols * 5 chars | 100k rows * 10 cols * 10 chars | 100k rows * 10 cols * 20 chars | 100k rows * 10 cols * 40 chars |
| ------------------------------------- | ----------------------------- | ------------------------------ | ------------------------------ | ------------------------------ |
| Autosizing OFF (Single Byte Chars)    | 7.6 seconds                   | 7.6 seconds                    | 7.7 seconds                    | 7.7 seconds                    |
| Autosizing ON  (Single Byte Chars)    | 9.4 seconds (+23%)            | 10.3 seconds (+35%)            | 12.4 seconds (+61%)            | 16.5 seconds (+114%)           |
| Autosizing OFF (Multi Byte Chars)     | 7.9 seconds                   | 8.4 seconds                    | 9.1 seconds                    | 9.8 seconds                    |
| Autosizing ON  (Multi Byte Chars)     | 10.7 seconds (+35%)           | 13.0 seconds (+54%)            | 17.1 seconds (+87%)            | 23.3 seconds (+137%)           |

*Intel Xeon E3-1220, Debian GNU/Linux 9.13, PHP 7.2.27-1+0~20200123.34+debian9~1.gbp63c0bc* 

### Additional examples
```php
<?php

require_once '../vendor/autoload.php';

// create a header style
$headerStyle = (new \OneSheet\Style\Style())
    ->setFontSize(13)
    ->setFontBold()
    ->setFontColor('FFFFFF')
    ->setFillColor('777777');

// create a data style
$dataStyle1 = (new \OneSheet\Style\Style())
    ->setFontName('Segoe UI')
    ->setFontSize(10);

// create a second data style
$dataStyle2 = (new \OneSheet\Style\Style())
    ->setFontName('Arial')
    ->setFillColor('F7F7F7');

// prepare some dummy header data
$dummyHeader = array('Strings', 'Ints', 'Floats', 'Dates', 'Times', 'Uids');

// prepare some dummy data
$dummyData = array();
for ($i = 1; $i <= 100; $i++) {
    $dummyData[] = array(
        substr(md5(microtime()), rand(11,22)),
        rand(333,333333),
        microtime(1),
        date(DATE_RSS, time() + $i*60*60*24),
        date('H:i:s', time() + $i),
        uniqid('', true)
    );
}

// create new OneSheet instance
$onesheet = new \OneSheet\Writer();

// add header with style
$onesheet->addRow($dummyHeader, $headerStyle);

// freeze everything above cell A2 (the first row will be frozen)
$onesheet->setFreezePaneCellId('A2');

// enable autosizing of column widths and row heights
$onesheet->enableCellAutosizing();

// add dummy data row by row and switch between styles
foreach ($dummyData as $key=> $data) {
    if ($key % 2) {
        $onesheet->addRow($data, $dataStyle1);
    } else {
        $onesheet->addRow($data, $dataStyle2);
    }
}

// ignore the coming rows for autosizing
$onesheet->disableCellAutosizing();

// add an oversized dummy row
$onesheet->addRow(array('no one cares about my size and I dont even have a special style!'));

// add the all the dummy rows once more, because we can =)
$onesheet->addRows($dummyData);

// Override column widths for columns 6, 7, 8 (column 0 is the first)
$onesheet->setFixedColumnWidths(array(5 => 10, 6 => 10, 7 => 10));

// write everything to the specified file
$onesheet->writeToFile(str_replace('.php', '_onesheet.xlsx', __FILE__));
```

##### Writing to multiple sheets
```php
<?php

require_once '../vendor/autoload.php';

$boldHeader = (new OneSheet\Style\Style())->setFontBold();

// create initial writer instance with sheet name
$writer = new \OneSheet\Writer(null, 'Invoices');
$writer->enableCellAutosizing(); // enable for current sheet
$writer->addRow(['InvoiceNo', 'Amount', 'CustomerNo'], $boldHeader);
$writer->addRow(['']); // add empty row bcs fancy :D
$writer->addRow(['I-123', 123.45, 'C-123']);

// create new sheet with specific sheet name
$writer->switchSheet('Refunds');
$writer->enableCellAutosizing(); // enable for current sheet
$writer->addRow(['RefundNo', 'Amount', 'InvoiceNo'], $boldHeader);
$writer->addRow(['']); // add empty row bcs fancy :D
$writer->addRow(['R-123', 123.45, 'I-123']);

// create another sheet with specific sheet name
$writer->switchSheet('Customers');
$writer->enableCellAutosizing(); // enable for current sheet
$writer->addRow(['CustomerNo', 'FirstName', 'LastName'], $boldHeader);
$writer->addRow(['']); // add empty row bcs fancy :D
$writer->addRow(['C-123', 'Bob', 'Johnson']);

// send file to browser for downloading 
$writer->writeToBrowser();
```

### Issues, bugs, features and ...
Feel free to report any sightings =).
