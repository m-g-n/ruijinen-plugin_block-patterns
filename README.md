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

# changelog
0.0.0.3
AutoUpdate周りのバグ対応

0.0.0.2
アップデートテスト

0.0.0.1.1
README.mdにreleaseCIのバッジを追加

0.0.0.1
本体のみのプラグインを作成
github actionsの導入
composerの導入