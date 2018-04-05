<?php

namespace Logue\LukiWiki;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;

class Log
{
    public static function write($message)
    {
        $log = new Logger('my-package');
        // ログのパスを設定する
        $log_path = __DIR__.'/../../logs/test.log';
        //ログレベルをDEBUG（最も低い）に設定
        $handler = new StreamHandler($log_path, Logger::DEBUG);
        $log->pushHandler($handler);
        // ログローテーションのハンドラーを使う
        $rotating_handler = new RotatingFileHandler($log_path, 30, Logger::DEBUG, true);
        $log->pushHandler($rotating_handler);

        // 書き込む
        return $log->addDebug($message);
    }
}
