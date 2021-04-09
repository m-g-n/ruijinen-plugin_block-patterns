
[![release](https://github.com/m-g-n/ruijinen-block-patterns-beta/workflows/Upload%20Release%20Asset/badge.svg)](https://github.com/megane9988/megane-auto-update/actions?query=workflow%3A%22Upload+Release+Asset%22)

# 類人猿ブロックパターン（ベータ）
WordPressテーマ Snow Monkeyを拡張するブロックパターン（ベータ）

# SCSSのコンパイル方法

当プラグインディレクトリーまで移動したあと、

- npm i でpackegeをインストール
- npx gulp watch でSCSSファイルの修正を常時監視（SCSSを修正したら即時CSSにコンパイルしてくれる）
- npx gulp sass でCSSにコンパイル（コマンド走ったときだけCSSをコンパイル）