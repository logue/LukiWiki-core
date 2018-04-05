<?php
/**
 * 水平線クラス.
 *
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014,2018 Logue
 * @license   MIT
 */

namespace Logue\LukiWiki\Element;

use Logue\LukiWiki\AbstractElement;

/**
 * Horizontal Rule.
 */
class HRule extends AbstractElement
{
    public function __toString()
    {
        return '<hr />';
    }
}
