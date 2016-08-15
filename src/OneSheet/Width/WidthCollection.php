<?php

namespace OneSheet\Width;

/**
 * Class WidthCollection
 *
 * @package OneSheet
 */
class WidthCollection
{
    /**
     * Array containing character widths for each font & size.
     *
     * @var array
     */
    private static $widths = array();

    /**
     * Create character width map for each font.
     */
    public function __construct()
    {
        self::loadWidthsFromCsv(dirname(__FILE__) . '/widths.csv');
//        self::loadAdditionalWidths();
    }

    /**
     * Dirty way to allow developers to load character widths that
     * are not yet included.
     *
     * @param string $csvPath
     */
    public static function loadWidthsFromCsv($csvPath)
    {
        foreach (file($csvPath) as $line) {
            if ($line[0] == 'f') continue;
            $widths = explode(',', trim($line));
            if (count(range(33, 126)) + 3 == count($widths)) {
                $fontName = array_shift($widths);
                $fontSize = array_shift($widths);
                $boldMulti = array_shift($widths);
                self::$widths[$fontName][$fontSize] = array_combine(array_map('chr', range(33, 126)), $widths);
                self::$widths[$fontName][$fontSize] += array('bold' => $boldMulti);
            }
        }
    }

//    Testing additional character widths ... fixed types.
//    private static function loadAdditionalWidths()
//    {
//        $hiragana = array('ぁ','あ','ぃ','い','ぅ','う','ぇ','え','ぉ','お','か','が','き','ぎ','く','ぐ','け','げ','こ','ご','さ','ざ','し','じ','す','ず','せ','ぜ','そ','ぞ','た','だ','ち','ぢ','っ','つ','づ','て','で','と','ど','な','に','ぬ','ね','の','は','ば','ぱ','ひ','び','ぴ','ふ','ぶ','ぷ','へ','べ','ぺ','ほ','ぼ','ぽ','ま','み','む','め','も','ゃ','や','ゅ','ゆ','ょ','よ','ら','り','る','れ','ろ','ゎ','わ','ゐ','ゑ','を','ん','ゔ','ゕ','ゖ');
//        foreach (self::$widths as &$width) {
//            $width[9] += array_combine($hiragana, array_fill(0, count($hiragana), 1.76));
//            $width[10] += array_combine($hiragana, array_fill(0, count($hiragana), 2.06));
//            $width[11] += array_combine($hiragana, array_fill(0, count($hiragana), 2.35));
//            $width[12] += array_combine($hiragana, array_fill(0, count($hiragana), 2.35));
//            $width[13] += array_combine($hiragana, array_fill(0, count($hiragana), 2.65));
//            $width[14] += array_combine($hiragana, array_fill(0, count($hiragana), 2.94));
//            $width[15] += array_combine($hiragana, array_fill(0, count($hiragana), 2.94));
//        }
//    }

    /**
     * Return character widths for given font name.
     *
     * @param string $fontName
     * @param int    $fontSize
     * @return array
     */
    public function get($fontName, $fontSize)
    {
        if (isset(self::$widths[$fontName][$fontSize])) {
            return self::$widths[$fontName][$fontSize];
        } elseif (isset(self::$widths['Calibri'][$fontSize])) {
            return self::$widths['Calibri'][$fontSize];
        }

        return self::$widths['Calibri'][11];
    }
}
