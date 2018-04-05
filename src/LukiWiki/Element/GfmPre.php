<?php
/**
 *  GitHub Flavored Markdown Pre.
 *
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2018 Logue
 * @license   MIT
 */

namespace Logue\LukiWiki\Element;

use Logue\LukiWiki\AbstractElement;

/**
 * ```lang ... ```.
 */
class GfmPre extends AbstractElement
{
    private $lang;

    public function __construct(object $root, string $text, string $lang)
    {
        parent::__construct();
        $this->lang = $lang;
        $this->elements[] = parent::processText($text);
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
        return $this->wrap(implode("\n", $this->elements), 'pre', ['class' => 'CodeMirror', 'data-lang' => $this->lang], false);
    }
}
