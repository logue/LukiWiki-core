<?php
/**
 * ブロック型プラグインクラス.（予定）
 *
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014,2018 Logue
 * @license   MIT
 */

namespace Logue\LukiWiki\Element;

use Logue\LukiWiki\AbstractElement;

/**
 *  Block plugin: #something (started with '#').
 */
class BlockPlugin extends Element
{
    protected $name;
    protected $param;

    public function __construct(string $out)
    {
        parent::__construct();
        list(, $this->name, $this->param) = array_pad($out, 3, null);
    }

    public function __toString()
    {
        // TODO:Call #plugin
        return '<p class="p-2 mb-2 rounded text-white bg-secondary">#'.$this->name.'</div>';
    }
}
