<?php
/**
 * 整形済みテキストクラス.
 *
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014,2018 Logue
 * @license   MIT
 */

namespace Logue\LukiWiki\Element;

use Logue\LukiWiki\AbstractElement;

/**
 * ' 'Space-beginning sentence.
 */
class Pre extends AbstractElement
{
    public function __construct(string $root, string $text)
    {
        parent::__construct();
        $this->elements[] = parent::processText(empty($text) || $text[0] !== ' ' ? $text : substr($text, 1));
    }

    public function canContain(object $obj)
    {
        return $obj instanceof self;
    }

    public function insert(object $obj)
    {
        $this->elements[] = $obj->elements[0];

        return $this;
    }

    public function __toString()
    {
        return $this->wrap(implode('', $this->elements), 'pre', [], false);
    }
}
