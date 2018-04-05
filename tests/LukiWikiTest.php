<?php

use PHPUnit\Framework\TestCase;
use Logue\LukiWiki\Parser;

class LukiWikiTest extends TestCase
{
    /**
     * @var Parser
     */
    protected $object;

    /**
     * オブジェクト生成.
     */
    protected function setUp()
    {
        // テストするオブジェクトを生成する
        $this->object = new Parser();
    }

    /**
     * 定義文テスト.
     */
    public function DListTest()
    {
        $html = $this->object::factory(join("\n", [
            ': definition1 | description1',
            ': definition2 | description2',
            ': definition3 | description3',
         ]));
        var_dump($html);
        //$this->assertEquals('<table class="table table-bordered mx-auto"><thead></thead><tfoot></tfoot><tbody><tr><td>title1</td><td>title2</td><td>title3</td></tr><tr><td>cell1</td><td>cell2</td><td>cell3</td></tr><tr><td>cell4</td><td>cell5</td><td>cell6</td></tr></tbody></table>', $html->__toString());
    }

    /**
     * テーブルテスト.
     *
     * @test
     */
    public function TableTest()
    {
        $html = $this->object::factory(join("\n", [
            '| title1 | title2 | title3 |',
            '| cell1  | cell2  | cell3  |',
            '| cell4  | cell5  | cell6  |',
         ]));
        $this->assertEquals('<table class="table table-bordered mx-auto"><thead></thead><tfoot></tfoot><tbody><tr><td>title1</td><td>title2</td><td>title3</td></tr><tr><td>cell1</td><td>cell2</td><td>cell3</td></tr><tr><td>cell4</td><td>cell5</td><td>cell6</td></tr></tbody></table>', $html->__toString());
    }
}
