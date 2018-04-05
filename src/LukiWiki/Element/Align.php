<?php
/**
 * 位置決めクラス.
 *
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014,2018 Logue
 * @license   MIT
 */

namespace Logue\LukiWiki\Element;

use Logue\LukiWiki\AbstractElement;
use Logue\LukiWiki\Rules\Alignment;

/**
 * LEFT: / CENTER: / RIGHT: / JUSTIFY:.
 */
class Align extends AbstractElement
{
    protected $align;

    public function __construct(string $align)
    {
        parent::__construct();
        $this->align = $align;
    }

    public function canContain(object $obj)
    {
        if ($obj instanceof Table || $obj instanceof YTable) {
            $obj->align = $this->align;
        }

        return $obj instanceof InlineElement;
    }

    public function __toString()
    {
        if (empty($this->align)) {
            return $this->wrap(parent::__toString(), 'div', [], false);
        }

        return $this->wrap(parent::__toString(), 'div', ['class' => Alignment::block($this->align)], false);
    }
}
