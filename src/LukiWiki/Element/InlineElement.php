<?php
/**
 * インライン要素クラス.
 *
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014,2018 Logue
 * @license   MIT
 */

namespace Logue\LukiWiki\Element;

use Logue\LukiWiki\AbstractElement;
use Logue\LukiWiki\InlineConverter;

/**
 * Inline elements.
 */
class InlineElement extends AbstractElement
{
    private static $converter;

    public function __construct(string $text, bool $isAmp)
    {
        parent::__construct();
        $text = trim($text);

        if (substr($text, 0, 1) === "\n") {
            $this->elements[] = $text;
        } else {
            if (!isset(self::$converter)) {
                static::$converter = new InlineConverter([], [], $isAmp);
            }

            $clone = static::$converter->getClone(static::$converter);
            $this->elements[] = $clone->convert($text);
            $this->meta = $clone->getMeta();
        }
    }

    public function insert(object $obj)
    {
        if (!empty($obj->elements[0])) {
            $this->elements[] = $obj->elements[0];
        }

        return $this;
    }

    public function canContain(object $obj)
    {
        return $obj instanceof self;
    }

    public function __toString()
    {
        return implode('', $this->elements);
    }

    public function toPara(string $class = '')
    {
        $obj = new Paragraph(null, $class);
        $obj->insert($this);

        return $obj;
    }
}
