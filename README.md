[![Create Release](https://github.com/m-g-n/ruijinen-plugin_block-patterns/actions/workflows/release.yml/badge.svg)](https://github.com/m-g-n/ruijinen-plugin_block-patterns/actions/workflows/release.yml)

# 類人猿ブロックパターンプラグイン
WordPressテーマ Snow Monkeyを拡張するブロックパターン集のプラグインです。
本体には3つのパターンのみ導入されています。
他のパターンが必要な場合は別途ブロックパターンプラグインアドオンの入手をお願いします。
（※アドオンは準備中です。少々お待ちください）

# SCSSのコンパイル方法
当プラグインディレクトリーまで移動したあと、

- npm i でpackegeをインストール
- npx gulp watch でSCSSファイルの修正を常時監視（SCSSを修正したら即時CSSにコンパイルしてくれる）
- npx gulp sass でCSSにコンパイル（コマンド走ったときだけCSSをコンパイル）

# 変更履歴
## 0.0.1.4
- サンプルパターン：Heroイメージ（1カラム)をフル幅テンプレートで利用すると、コンテンツが中央に寄らないバグ修正

## 0.0.1.3
- Snow Monkey Blocks v13.3.0対応

## 0.0.1.2
- Snow Monkey Blocks v13.0.0対応

## 0.0.1.1
- Snow Monkeyが有効化ではない場合に当プラグインを有効化するとエラーになるバグを修正

## 0.0.1.0
- 製品版リリース