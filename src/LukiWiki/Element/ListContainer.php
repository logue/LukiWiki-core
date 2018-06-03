<?php
/**
 * リストコンテナクラス.
 *
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014,2018 Logue
 * @license   MIT
 */

namespace Logue\LukiWiki\Element;

use Logue\LukiWiki\AbstractElement;

/**
 * Lists (UL, OL, DL).
 */
class ListContainer extends AbstractElement
{
    protected $tag = 'ul';
    protected $tag2 = 'li';
    protected $level = 0;
    protected $isAmp;
    protected $pattern = '/^[\-|\+]{1,3}(\w+?)$/';

    public function __construct(string $tag, string $tag2, string $head, string $text, bool $isAmp = false)
    {
        parent::__construct();
        $this->tag = $tag;
        $this->tag2 = $tag2;
        $this->level = min(3, strspn($text, $head));
        $this->isAmp = $isAmp;
        $text = ltrim(substr($text, $this->level));

        $element = new ListElement($this->level, $tag2);

        $this->insert($element);
        if (!empty($text)) {
            $content = new InlineElement($text, $this->isAmp);
            $this->meta = $content->getMeta();
            $this->last = $this->last->insert($content);
        }
    }

    public function canContain(object $obj)
    {
        return !($obj instanceof self)
            || ($this->tag === $obj->tag && $this->level === $obj->level);
    }

    public function setParent(object $parent)
    {
        parent::setParent($parent);

        $step = $this->level;
        if (isset($parent->parent) && ($parent->parent instanceof self)) {
            $step -= $parent->parent->level;
        }
    }

    public function insert(object $obj)
    {
        if (!$obj instanceof self && $this->level > 3) {
            return $this->last = $this->last->insert($obj);
        }

        // Break if no elements found
        if (count($obj->elements) === 1 && empty($obj->elements[0]->elements)) {
            return $this->last->parent;
        } // up to ListElement

        // Move elements
        $keys = array_keys($obj->elements);
        foreach ($keys as $key) {
            parent::insert($obj->elements[$key]);
        }

        return $this->last;
    }

    public function __toString()
    {
        return $this->wrap(parent::__toString(), $this->tag, [], false);
    }
}
