<?php
/**
 * ブロック型変換クラス.
 *
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2018 Logue
 * @license   MIT
 */

namespace Logue\LukiWiki;

/**
 * Converters of Block element.
 */
class ElementConverter
{
    /**
     * デフォルトの変換パターン.
     */
    private static $default_converters = [
        'Logue\LukiWiki\Element\Align',
        'Logue\LukiWiki\Inline\Note',
        'Logue\LukiWiki\Inline\Url',
        'Logue\LukiWiki\Inline\InterWiki',
        'Logue\LukiWiki\Inline\Mailto',
        'Logue\LukiWiki\Inline\InterWikiName',
        'Logue\LukiWiki\Inline\BracketName',
    //    'Logue\LukiWiki\Inline\WikiName',
        'Logue\LukiWiki\Inline\AutoLink',
        'Logue\LukiWiki\Inline\Telephone',
    ];
    /**
     * 変換クラス.
     */
    private $converters = [];
    /**
     * 変換処理に用いる正規表現パターン.
     */
    private $pattern;

    private static $clone_func;

    private $meta;

    /**
     * コンストラクタ
     *
     * @param array $converters 使用する変換クラス名
     * @param array $excludes   除外する変換クラス名
     * @param bool  $isAmp      AMP用HTMLを出力するか？
     */
    public function __construct(array $converters, array $excludes, bool $isAmp)
    {
        static $converters;
        if (!isset($converters)) {
            $converters = self::$default_converters;
        }
        // 除外するクラス
        if ($excludes !== null) {
            $converters = array_diff($converters, $excludes);
        }

        $this->converters = $patterns = [];
        $start = 1;

        foreach ($converters as $name) {
            if (empty($name)) {
                continue;
            }

            $converter = new $name($start, $isAmp);

            $pattern = $converter->getPattern();
            if (empty($pattern)) {
                continue;
            }
            //echo $name."\n";

            $patterns[] = '('.$pattern.')';
            $this->converters[$start] = $converter;
            $start += $converter->getCount();

            ++$start;
        }
        $this->pattern = implode('|', $patterns);
    }
}
