1. /patterns/ 配下に、/templates/patterns/ の中の pattern-1 ディレクトリを複製してください。
2. 複製したディレクトリ名をブロックパターンの名称に変更してください。また、{$pattern_name}-{ID} の形式は維持してください。
3. 複製したディレクトリ内の register.php を編集します。
    * $pattern_category に、ブロックパターンのカテゴリーを代入します。現在定義されているカテゴリーは /inc/load-register-block.php で確認できます。
    * $pattern_title に、ブロックパターン名を代入します。
    * $use_block_style に、そのブロックパターンで使用しているブロックスタイルのディレクトリ名を代入します。
4. パターン内で使用する画像は、複製したディレクトリ内の images ディレクトリに格納します。
5. 複製したディレクトリ内の pattern.php を編集します。前後に空行が入らないように注意して、ブロックパターンのHTMLを挿入します。
6. pattern.php の、画像のsrc属性のパス部分には「<?php echo esc_url( plugin_dir_url( __FILE__ ) ); ?>images/」を利用してください。

* register.php や pattern.php は、/inc/load-register-block.php によって自動で読み込まれます。