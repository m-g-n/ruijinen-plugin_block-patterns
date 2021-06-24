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
## 0.0.0.13
- composerの設定を修正

## 0.0.0.12
- LPパターン集が有効化の場合はLPに関するサンプルパターンを読み込まないようにする処理を追加
- ベータ版と共存ができるようになったためベータ版のアクティベートチェックを無効化

## 0.0.0.11
- サンプルパターンのスタイルバグを修正

## 0.0.0.10
- LPパターン集購入後もサンプルパターンのスタイルがそのまま維持できるように修正

## 0.0.0.9
- ベータ版が有効化されている場合関数名がバッティングするため、このプラグインを有効にしないように修正
- お知らせウィジェットが準備できてないため機能を無効化

## 0.0.0.8
release PR CIの修正

## 0.0.0.7
アップデートテスト

## 0.0.0.6
gitにてdist/cssをignore を定義（0.0.0.5ではできてなかった）
リリース時にCSSをコンパイルするようにrelease.ymlを修正

## 0.0.0.5
gitにてdist/cssをignore

## 0.0.0.4
テストパターンの導入

## 0.0.0.3
AutoUpdate周りのバグ対応

## 0.0.0.2
アップデートテスト

## 0.0.0.1.1
README.mdにreleaseCIのバッジを追加

## 0.0.0.1
本体のみのプラグインを作成
github actionsの導入
composerの導入