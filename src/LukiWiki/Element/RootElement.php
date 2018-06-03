<?php
/**
 * 基底要素クラス.
 *
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014,2018 Logue
 * @license   MIT
 */

namespace Logue\LukiWiki\Element;

use Logue\LukiWiki\AbstractElement;
use Logue\LukiWiki\Rules\HeadingAnchor;

/**
 * RootElement.
 */
class RootElement extends AbstractElement
{
    const MULTILINE_DELIMITER = "\r";

    protected $id;
    protected $count = 0;

    public function __construct(string $text, array $option)
    {
        $this->id = $option['id'] ?? 0;
        $this->isAmp = $option['isAmp'] ?? false;
        parent::__construct();
        $this->parse($text);
    }

    public function parse(string $lines)
    {
        //Debugbar::startMeasure('LukiWiki', 'LukiWiki');
        $lines = explode("\n", str_replace([chr(0x0d).chr(0x0a), chr(0x0d), chr(0x0a)], "\n", $lines));
        $this->last = $this;
        $matches = [];

        $count = count($lines);
        for ($i = 0; $i < $count; ++$i) {
            $line = rtrim(array_shift($lines), "\t\r\n\0\x0B");	// スペース以外の空白文字をトリム;

            // Empty
            if (empty($line)) {
                $this->last = $this;
                continue;
            }

            if (preg_match('/^(LEFT|CENTER|RIGHT|JUSTIFY|TITLE):(.*)$/', $line, $matches)) {
                $cmd = strtolower($matches[1]);

                if (!empty($cmd)) {
                    if ($cmd === 'title') {
                        $this->meta['title'] = $matches[2];
                    } elseif (is_object($this->last)) {
                        $this->last = $this->last->append(new Align($cmd));
                    }
                }
                if (empty($matches[2])) {
                    continue;
                }
                $line = $matches[2];
            }

            // Multiline-enabled block plugin #plugin{{ ... }}
            if (preg_match('/^\@[^{]+(\{\{+)\s*$/', $line, $matches)) {
                $len = strlen($matches[1]);
                $line .= self::MULTILINE_DELIMITER;
                while (!empty($lines)) {
                    $next_line = preg_replace('/['.self::MULTILINE_DELIMITER.'\n]*$/', '', array_shift($lines));
                    if (preg_match('/\}{'.$len.'}/', $next_line)) {
                        $line .= $next_line;
                        break;
                    } else {
                        $line .= $next_line .= self::MULTILINE_DELIMITER;
                    }
                }
            }

            // Github Markdown互換シンタックスハイライト記法
            $lang = null;
            if (preg_match('/^```?:(\w+?)$/', $line, $matches)) {
                $line .= self::MULTILINE_DELIMITER;
                while (!empty($lines)) {
                    $next_line = preg_replace('/['.self::MULTILINE_DELIMITER.'\n]*$/', '', array_shift($lines));
                    if (preg_match('/^```$/', $next_line)) {
                        $line .= $next_line;
                        break;
                    } else {
                        $line .= $next_line .= self::MULTILINE_DELIMITER;
                    }
                }
            }

            // The first character
            $head = $line[0];

            // Other Character
            if (is_object($this->last)) {
                $content = null;
                switch ($head) {
                    case '#':
                        $this->insert(new Heading($this, $line, $this->isAmp));
                        continue;
                        break;
                    case '`':
                        // Pre
                        if (preg_match('/```(\w+?)\r(.*)\r```/', $line, $matches)) {
                            $content = new PreformattedText($this, $matches[2], $matches[1]);
                        }
                        break;
                    case '-':
                        if (substr($line, 0, 4) === '----') {
                            // Horizontal Rule
                            $content = new HRule($this, $line, $this->isAmp);
                            continue;
                        }
                        // List
                        $content = new UnorderedList($this, $line, $this->isAmp);
                        break;
                    case '+':
                        $content = new OrderedList($this, $line, $this->isAmp);
                        break;
                    case '>':
                    case '<':
                        $content = new Blockquote($this, $line, $this->isAmp);
                        break;
                    case ':':
                        $out = explode('|', ltrim($line), 2);
                        if (!count($out) < 2) {
                            $content = new DefinitionList($out, $this->isAmp);
                        }
                        break;
                    case '|':
                        if (preg_match('/^\|(.+)\|([hHfFcC]?)$/', $line, $out)) {
                            $content = new Table($out, $this->isAmp);
                        }
                        break;
                    case '@':
                        $matches = [];

                        if (preg_match('/^\@([^\(\{]+)(?:\(([^\r]*)\))?(\{*)/', $line, $matches)) {
                            // Plugin
                            $len = strlen($matches[3]);
                            $body = [];
                            if (preg_match('/\{{'.$len.'}\s*\r(.*)\r\}{'.$len.'}/', $line, $body)) {
                                // Seems multiline-enabled block plugin
                                $matches[2] .= "\r".$body[1]."\r";
                            }
                            $content = new BlockPlugin($matches);
                        }
                        break;
                    case '~':
                        $content = new Paragraph(' '.substr($line, 1), $this->isAmp);
                        break;
                    case '/':
                        // Escape comments
                        if ($line[1] === '/') {
                            continue;
                        }
                        break;
                    default:
                        $content = new InlineElement($line, $this->isAmp);
                        break;
                }

                // Default
                if (!empty($content)) {
                    if (is_object($content)) {
                        $meta = $content->getMeta();

                        if (!empty($meta)) {
                            foreach ($meta as $key => $value) {
                                $this->meta[$key][] = $value;
                            }
                        }
                    }
                    $this->last = $this->last->append($content);
                }
                unset($content);
                continue;
            }
        }
        //Debugbar::stopMeasure('LukiWiki');
    }

    public function getAnchor(string $text, int $level)
    {
        // Heading id (auto-generated)
        $autoid = 'content_'.$this->id.'_'.$this->count;
        ++$this->count;

        list($_text, $id, $level) = HeadingAnchor::get($text, false); // Cut fixed-anchor from $text

        $this->meta['contents'][] = str_repeat('-', $level).'[['.$_text.'>#'.$autoid.']]';

        // Add heding
        return [$_text, null, $autoid];
    }

    public function canContain(object $obj)
    {
        return true;
    }

    public function insert(object $obj)
    {
        if ($obj instanceof InlineElement) {
            $obj = $obj->toPara();
        }

        return parent::insert($obj);
    }
}
