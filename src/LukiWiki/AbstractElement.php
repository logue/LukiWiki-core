<?php
/**
 * ブロック要素基底クラス.
 *
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2018 Logue
 * @license   MIT
 */

namespace Logue\LukiWiki;

/**
 * Block elements.
 */
abstract class AbstractElement
{
    protected $parent;
    protected $elements = [];
    protected $last;
    protected $meta = null;

    protected $pattern = '';

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->last = $this;
    }

    /**
     * デストラクタ
     */
    public function __destruct()
    {
        unset($this->parent);
        unset($this->elements);
        unset($this->last);
        unset($this->meta);
    }

    /**
     * 変換処理の判定.
     */
    public function match(string $line)
    {
        if ($this->pattern[0] === '/') {
            // 定義されているマッチパターンの先頭の文字が/だった場合は正規表現で判定する
            return preg_match($this->pattern, $line);
        }

        return $this->pattern === $line[0];
    }

    /**
     * 親要素に挿入.
     *
     * @param object $obj
     */
    public function setParent(object $parent)
    {
        $this->parent = $parent;
    }

    /**
     * 親要素に要素を追加.
     *
     * @param object $obj
     */
    public function append(object $obj)
    {
        if ($this->canContain($obj)) {
            return $this->insert($obj);
        }
        if (is_object($this->parent)) {
            return $this->parent->insert($obj);
        }
    }

    /**
     * 要素を追加.
     *
     * @param object $obj
     */
    public function insert(object $obj)
    {
        if (is_object($obj)) {
            $obj->setParent($this);
            $this->elements[] = $obj;

            $this->last = $obj->last;
        }

        return $this->last;
    }

    /**
     * 小要素を持つことができるか.
     *
     * @param object $obj
     *
     * @return bool
     */
    public function canContain(object $obj)
    {
        return false;
    }

    /**
     * タグで包む
     *
     * @param string $string  子要素
     * @param string $tag     タグ名
     * @param array  $param   タグに入れる属性
     * @param bool   $canomit
     *
     * @return string
     */
    public function wrap(string $innerHtml, string $tag, array $param, bool $canomit)
    {
        $attributes = [];
        foreach ($param as $key => $value) {
            $attributes[] = $key.'="'.self::processText($value).'"';
        }

        return ($canomit && empty($string)) ? '' :
        '<'.$tag.(count($attributes) !== 0 ? ' '.implode(' ', $attributes) : '').'>'.trim($innerHtml).'</'.$tag.'>';
    }

    /***
     * 変換結果を出力
     */
    public function __toString()
    {
        $ret = [];
        foreach ($this->elements as $obj) {
            $ret[] = trim($obj->__toString());
        }

        return implode("\n", $ret);
    }

    /**
     * メタデータを取得.
     *
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * 文字列をエスケープ.
     *
     * @param string $str
     *
     * @return string
     */
    protected static function processText(string $str)
    {
        return htmlspecialchars(trim($str), ENT_HTML5, 'UTF-8');
    }
}
