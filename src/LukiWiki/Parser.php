<?php
/**
 * WikiテキストをHTMLに変換する.
 *
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014,2018 Logue
 * @license   MIT
 */

namespace Logue\LukiWiki;

use Logue\LukiWiki\Element\RootElement;

/**
 * パーサー.
 */
class Parser
{
    private static $instance = 0;

    /**
     * LukiWikiファクトリークラス.
     *
     * @param string $text  Wikiのソース
     * @param bool   $isAmp AMP対応フラグ
     *
     * @return string
     */
    public static function factory(string $text, bool $isAmp = false)
    {
        return new RootElement($text, ['id' => ++self::$instance, 'isAmp' => $isAmp]);
    }
}
