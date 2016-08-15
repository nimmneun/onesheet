# OneSheet

[![Build Status](https://travis-ci.org/nimmneun/onesheet.svg?branch=master)](https://travis-ci.org/nimmneun/onesheet)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nimmneun/onesheet/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nimmneun/onesheet/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/nimmneun/onesheet/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/nimmneun/onesheet/?branch=master)

OneSheet is a simple **single sheet** excel/xlsx file writer for PHP 5.3+ / 7.0+.

![Resulting OneSheet File in Excel](./tests/generated_auto_column_sizing_sample.png)

#### What it does & doesnt
- Write a single sheet with up to 2^20 rows fast and with a small
  memory footprint.
- Freeze the first [n] rows to have a fixed table header/headline.
- Option to use different fonts, styles, borders and background colors on
  a row level.
- Option to auto-size column widths to fit cell contents. Only Arial, Calibri and Segoe UI size 9-15 are reliably working for now.
- PHP 5.3 compatibility.

Current major drawback(s):
- No cell individualisation, everything is applied at a row level.
- No calculated/formula cells.

This library is still WIP, so please be aware that there might be api breaking changes in minor versions (0.*.0) until version 1.0.0 is released!

####Install
Install via composer `composer require nimmneun/onesheet`

#####Example
```php
<?php

require_once '../vendor/autoload.php';

// create a header style
$headerStyle = new \OneSheet\Style\Style();
$headerStyle->setFontSize(13)->setFontBold()->setFontColor('FFFFFF')->setFillColor('777777');

// create a data style
$dataStyle1 = new \OneSheet\Style\Style();
$dataStyle1->setFontName('Segoe UI')->setFontSize(10);

// create a second data style
$dataStyle2 = new \OneSheet\Style\Style();
$dataStyle2->setFontName('Arial')->setFillColor('F7F7F7');

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
        uniqid(null, 1)
    );
}

// create new OneSheet instance
$onesheet = new \OneSheet\Writer();

// add header with style
$onesheet->addRow($dummyHeader, $headerStyle);

// freeze everything above cell A2 (the first row will be frozen)
$onesheet->setfreezePaneCellId('A2');

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

// write everything to the specified file
$onesheet->writeToFile(str_replace('.php', '_onesheet.xlsx', __FILE__));
```
