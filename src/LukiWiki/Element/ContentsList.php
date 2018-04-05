<?php
/**
 * 目次ブロッククラス.
 *
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014,2018 Logue
 * @license   MIT
 */

namespace Logue\LukiWiki\Element;


/**
 * 目次リスト.
 */
class ContentsList extends ListContainer
{
    public function __construct($text, $level, $id)
    {
        $_text = str_repeat('-', $level).'[['.self::stripBracket($text).'>#'.$id.']]';
        parent::__construct('ul', 'li', '-', $_text);
    }

    public function prepend($parent)
    {
        parent::prepend($parent);
        $step = $this->level;
        if (isset($parent->parent) && ($parent->parent instanceof parent)) {
            $step -= $parent->parent->level;
        }
    }

    /**
     * [[～]]を削除する.
     */
    private static function stripBracket($str)
    {
        return preg_replace('/\[\[|\]\]/', '', $str);
    }
}
