# Clew : Command Line Execution Wrapper

Clew とは、PHP アプリケーション上で外部コマンドを実行する際のラッパーとなるライブラリです。
PHP で外部コマンドを実行するにはビルトイン関数の
[exec](https://www.php.net/manual/ja/function.exec.php) を用いるのが一般的ですが、
このライブラリはコマンド実行がよりセキュアかつ扱いやすくなるよう設計されています。

`exec()` が引数の文字列をそのまま 1 つのコマンドとして実行するのに対して、
Clew はコマンドライン引数を配列で指定します。以下に簡単なサンプルコードを記載します。

```php
<?php

use Clew\Executor;

require_once("vendor/autoload.php");

$executor = Executor::getInstance();
$result   = $executor->executeArray(["sed", "-e", "s/apples/oranges/g", "source.txt"]);
echo $result->getOutput(), PHP_EOL;
```

その他 Clew の利点を下記に挙げます。

* セキュリティの向上
    * ビルトイン関数の `exec()` は引数の文字列を直接コマンドとして実行するため、
    必要に応じてエスケープ処理などのセキュリティ対策を手動で行う必要があります。
    Clew では引数の文字列を自動でエスケープするため OS コマンドインジェクションのリスクを軽減することができます。
* エラーハンドリングの簡易化
    * Clew は、コマンドが想定しない終了ステータスを返した際に `CommandException` をスローさせることができます。
    終了ステータスの値をチェックして適切なエラーハンドリングを行うコードを、シンプルな
    try-catch 構文として実装することができるため、可読性の向上に繋がります。
* 標準出力・標準エラー出力の取得用 API
    * Clew は、コマンドの実行結果を `CommandResult` オブジェクトとして取得します。
    標準出力や標準エラー出力を、このオブジェクトからスムーズに取り出すことができます。

## チュートリアル

Clew の基本的な使い方は下記の通りです。

1. `Executor::getInstance()` を実行し、Executor クラスのインスタンスを取得します
2. Executor インスタンスの `executeArray()` または `execute()` を実行します
3. 実行結果の CommandResult オブジェクトから標準出力・標準エラー出力を取得します (必要な場合)

その他の機能について以下に紹介します。

### 終了ステータスを利用したエラーハンドリング

PHP から外部コマンドを実行する際、予期せぬ理由によりコマンドの実行が失敗することがあります。
(例えば環境変数 PATH の設定に不備があり、コマンドの呼び出しに失敗するなど)

コマンドを実行する際、想定される終了ステータスを列挙することで、
それ以外の終了ステータスが返ってきた時に適切なエラーハンドリングを行うことができます。

例として PHP アプリケーション内で `diff` コマンドを実行するサンプルコードを以下に挙げます。
diff コマンドの終了ステータスは通常 0, 1, 2 のいずれかとなるため、`executeArray()`
メソッドの第 2 引数に配列 `[0, 1, 2]` を指定します。

```php
<?php

use Clew\CommandException;
use Clew\Executor;

require_once("vendor/autoload.php");

$executor = Executor::getInstance();

try {
    $result = $executor->executeArray(["diff", "-b", "test01.html", "test02.html"], [0, 1, 2]);
    echo $result->getOutput(), PHP_EOL;
    echo $result->getError(), PHP_EOL;
} catch (CommandException $e) {
    echo $e->getCommandResult()->getError(), PHP_EOL;
}
```

このサンプルコードは、もしもコマンドの終了ステータスが 0, 1, 2 のいずれかでもなかった場合に
CommandException をスローし、その結果 `catch` 節の中が実行されます。

### パイプを利用したコマンドの連結

パイプ `|` を利用して複数のコマンドを連結したい場合は Command クラスの `pipeTo()` メソッドを使用してください。
このメソッドは 2 つの Command オブジェクトをパイプで連結し、その結果を新しい Command オブジェクトとして返します。

例えば `ls -la /tmp/ | wc` というコマンドを実行したい場合は下記のようになります。

```php
<?php

use Clew\Command;
use Clew\Executor;

require_once("vendor/autoload.php");

$executor = Executor::getInstance();
$command  = (new Command(["ls", "-la", "/tmp/"]))->pipeTo(new Command(["wc"]));
$result   = $executor->execute($command);
echo $result->getOutput(), PHP_EOL;
```

このライブラリはコマンド引数をすべて文字列としてエスケープするため、単純に文字列配列内に "|"
を含めてもパイプとして認識されないことに注意してください。

```php
// 間違った例
$executor->executeArray(["ls", "-la", "/tmp", "|", "wc"]);
```

### ファイルへのリダイレクト

リダイレクトを利用して標準出力をファイルに保存する場合は Command クラスの `redirectTo` メソッドを使用してください。
以下に `ls -la /tmp/ > output.txt` というコマンドを実行するためのサンプルコードを記載します。

```php
<?php

use Clew\Command;
use Clew\Executor;

require_once("vendor/autoload.php");

$executor = Executor::getInstance();
$command  = (new Command(["ls", "-la", "/tmp/"]))->redirectTo("output.txt");
$result   = $executor->execute($command);
var_dump($result->getOutput()); // string(0) ""
echo file_get_contents("output.txt"), PHP_EOL;
```

`redirectTo` メソッドの第 2 引数にフラグを指定することで、標準エラー出力のリダイレクトや、
ファイルから標準入力へのリダイレクトも指定することができます。

```php
// 標準エラー出力をリダイレクトする例
$cmd2 = $command->redirectTo("output.txt", Command::REDIRECT_STDERR);

// ファイルの中身を標準入力へリダイレクトする例
$cmd3 = $command->redirectTo("source.txt", Command::REDIRECT_STDIN);
```

ファイルを上書き保存ではなく追記する場合は `redirectTo` の第 3 引数に true を指定してください。

```php
// echo "Hello, World!" >> output.txt
$cmd = (new Command(["echo", "Hello, World!"]))->redirectTo("output.txt", Command::REDIRECT_STDOUT, true);
```

## インストール

Composer からインストールできます。

動作要件: PHP 5.4 以上
