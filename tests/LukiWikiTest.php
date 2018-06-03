<?php

use Logue\LukiWiki\Parser;
use PHPUnit\Framework\TestCase;

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
     * ブロック型引用文テスト.
     */
    public function testBlockquote()
    {
        $html = $this->object::factory(implode("\n", [
            '> Someting cited',
            '> like E-mail text.',
        ]));
        $this->assertEquals('<blockquote class="blockquote">Someting cited'."\n".'like E-mail text.</blockquote>', $html->__toString());
    }

    /**
     * 定義文テスト.
     */
    public function testDefinitionList()
    {
        $html = $this->object::factory(implode("\n", [
            ': definition1 | description1',
            ': definition2 | description2',
            ': definition3 | description3',
        ]));
        $this->assertEquals(implode("\n", [
            '<dl><dt>definition1</dt>',
            '<dd>description1</dd>',
            '<dt>definition2</dt>',
            '<dd>description2</dd>',
            '<dt>definition3</dt>',
            '<dd>description3</dd></dl>', ]), $html->__toString());
    }

    /**
     * 整形テキストテスト.
     */
    public function testPreformattedText()
    {
        $html = $this->object::factory(implode("\n", [
            '```plain',
            'Preformatted Test',
            '```',
        ]));
        $this->assertEquals('<pre class="CodeMirror" data-lang="plain">Preformatted Test</pre>', $html->__toString());
    }

    /**
     * 見出しテスト.
     */
    public function testHeading()
    {
        $id_prefix = 'content_4_';
        $html = $this->object::factory(implode("\n", [
            '* Heading1',
            '** Heading2',
            '*** Heading3',
            '**** Heading4',
            '***** Heading5',
        ]));
        $this->assertEquals(implode("\n", [
            '<h2 id="content_4_0">Heading1</h2>',
            '<h3 id="content_4_1">Heading2</h3>',
            '<h4 id="content_4_2">Heading3</h4>',
            '<h5 id="content_4_3">Heading4</h5>',
            '<h6 id="content_4_4">Heading5</h6>', ]), $html->__toString());
    }

    /**
     * 水平線テスト.
     */
    public function testHr()
    {
        $html = $this->object::factory('----');
        $this->assertEquals('<hr />', $html->__toString());
    }

    /**
     * リストテスト.
     */
    public function testList()
    {
        $html = $this->object::factory(implode("\n", [
            '- level1',
            '-- level2',
            '--- level3',
            '+ level1',
            '++ level2',
            '+++ level3',
        ]));
        $this->assertEquals(implode("\n", [
            '<ul><li>level1',
            '<ul><li>level2',
            '<ul><li>level3</li></ul></li></ul></li></ul>',
            '<ol><li>level1',
            '<ol><li>level2',
            '<ol><li>level3</li></ol></li></ol></li></ol>', ]), $html->__toString());
    }

    /**
     * テーブルテスト.
     *
     * @test
     */
    public function testTable()
    {
        $html = $this->object::factory(implode("\n", [
            '| title1 | title2 | title3 |',
            '| cell1  | cell2  | cell3  |',
            '| cell4  | cell5  | cell6  |',
        ]));
        $this->assertEquals('<table class="table table-bordered mx-auto"><thead></thead><tfoot></tfoot><tbody><tr><td>title1</td><td>title2</td><td>title3</td></tr><tr><td>cell1</td><td>cell2</td><td>cell3</td></tr><tr><td>cell4</td><td>cell5</td><td>cell6</td></tr></tbody></table>', $html->__toString());
    }
}
