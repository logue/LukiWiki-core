# プラグインの仕様

概ねPukiWikiと似たような感じになっていますが、オブジェクト指向になっており、必要なパラメータは全てメンバ変数からアクセスします。

プラグインの呼び出し方はPukiWikiと同じですが、明確に()内の値と{}内の値が区別され、また()内の値は自動的に配列として処理されます。
このため、PukiWikiのプラグインの処理でありがちだった、末尾の変数を本文とするみたいな処理は不要となります。

```
@[プラグイン名](パラメータ[,パラメータ2 ...]){本文}
@[プラグイン名](パラメータ[,パラメータ2 ...]){{
本文
}}
&[プラグイン名](パラメータ[,パラメータ2 ...]){本文}
```

* $this->page ページ名
* $this->params パラメーター（,区切りで入力。プラグイン側には配列として渡されてくる）
* $this->body 本文、内容。

プラグインの雛形は以下のような感じです。

```php
<?php
/**
 * ダミープラグイン.
 *
 * @author    作者の名前とメールアドレス
 * @copyright 作者の著作権表記
 * @license   ライセンス
 */

namespace [任意の名前空間];

use LukiWiki\AbstractPlugin;
use LukiWiki\BlockPluginInterface;
use LukiWiki\InlinePluginInterface;
use LukiWiki\InlinePluginInterface;

class Dummy extends AbstractPlugin implements BlockPluginInterface, InlinePluginInterface, ApiPluginInterface
{
    /**
     * インライン型
     *
     * @return string
     */
    public function inline(): string
    {
        return '<span>ダミー</span>';
    }
    /**
     * ブロック型
     *
     * @return string
     */
    public function block(): string
    {
        return '<p>ダミー</p>';
    }
    /**
     * API型（最終的にはjsonオブジェクトになります）
     *
     * @return array
     */
    public function api(): array
    {
        return ['message'=>'ダミープラグイン'];
    }
    /**
     * コード補完用テキストを出力
     *
     * @return string
     */
    public function syntax(): string
    {
        return '';
    }
    /**
     * プラグインの説明文
     *
     * @return string
     */
    public function usage(): string
    {
        return 'このプラグインはダミーです。単に「ダミー」と出力します。';
    }
}
```

呼び出し時は、PukiWikiと異なり、単にプラグインをディレクトリに入れただけでは認識されません。
Facadeパターンを採用しており、config/lukiwiki.phpのプラグイン設定に以下のように追加する必要があります。

```
    'plugin' => [
        'dummy'      => App\LukiWiki\Plugins\Dummy::class,
    ],
```

このため、プラグインを呼び出すときの名前とプラグイン名を一致させる必要はありません。例えば、Abbrプラグインをtooltipで呼び出したい場合は以下のようにします。
同じ変換処理を実行させたい場合は複数定義しても問題ありません。

```
    'plugin' => [
        'abbr'         => App\LukiWiki\Plugins\Abbr::class,
        'tooltip'      => App\LukiWiki\Plugins\Abbr::class,
    ],
```

action型に相当する処理は未実装ですが、似たようなものにAPI型があります。これは```[ページ名]:plugin/[プラグイン名]```という形式でアクセスします。
DBの実行結果などをJSONもしくはXMLで返すためのものですが、脆弱性の原因になりかねないので、まだ仕様が確定していません。