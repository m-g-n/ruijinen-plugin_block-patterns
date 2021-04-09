1. /block-styles/gutenberg | snow-monkey | snow-monkey-blocks/ 直下に、ブロックスタイルを作成したいブロック名のディレクトリを作成します。
    * 例）Snow Monkey Blocks の「ディレクトリ構造」ブロックなら、「directory-structure」
2. そのディレクトリ配下に、/templates/block-styles/ の中の RJE-sample-1 ディレクトリを複製してください。
3. 複製したディレクトリ名をブロックスタイルの名称に変更してください。また、RJE-{$block-style_name}-{ID} の形式は維持してください。
4. 複製したディレクトリ内の register.php を編集します。
    * $override_block_name に、ブロックスタイルを作成したいブロック名を代入します。
        * 例）Snow Monkey Blocks の「ディレクトリ構造」ブロックなら、「snow-monkey-blocks/directory-structure」
    * $block_style_label に、ブロックスタイル名を代入します。
5. 複製したディレクトリ内の style.scss を編集します。コンパイルについては、当プラグインディレクトリ直下の README.md を参照してください。

* register.php や style.css は、/inc/load-register-block.php によって自動で読み込まれます。
* CSS は、このブロックスタイルを使用しているブロックパターンが存在しないと読み込まれません。