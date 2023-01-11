[![Create Release](https://github.com/m-g-n/ruijinen-plugin_block-patterns/actions/workflows/release.yml/badge.svg)](https://github.com/m-g-n/ruijinen-plugin_block-patterns/actions/workflows/release.yml)

# 類人猿ブロックパターンプラグイン
WordPressテーマ Snow Monkeyを拡張するブロックパターン集のプラグインです。
本体には3つのパターンのみ導入されています。
他のパターンが必要な場合は別途ブロックパターンプラグインアドオンの入手をお願いします。
（※アドオンは準備中です。少々お待ちください）

# SCSSのコンパイル方法
当プラグインディレクトリーまで移動したあと、

- npm i でpackegeをインストール
- npm run watch でSCSSファイルの修正を常時監視（SCSSを修正したら即時CSSにコンパイルしてくれる）
- npm run build でCSSにコンパイル（コマンド走ったときだけCSSをコンパイル）

# 変更履歴
## 3.0.0
- Snow Monkeyテーマ必須チェックを削除
## 2.0.1
- パターン無効化設定ページ不具合対応
## 2.0.0
- パターン無効化機能のβ版 実装
- yarnのバージョンをv1からv3へアップグレード
- yarn run watch時のコマンドを調整
- 更新アラートに任意のメッセージを表示する機能の追加
## 1.14.0
- GitHub Actionsの調整
## 1.13.0
- 過去に使用していたクラスのプロパティを削除（LPパターン集0.0.1.7以前との互換対応を終了）
- dist/imgについてsrc/imgからdist/imgに出力する方式に変更

## 1.12.0
- 開発環境をlocalアプリからwp-envに変更
- BackstopJSを利用した差分チェックテストの導入

## 1.11.0
- README.mdのSCSSコンパイル コマンド間違えを修正
- 不要で削除済のCSSファイルを読み込むコードを削除

## 1.10.0
- バージョン番号の桁を2つ繰り上げ
- CSSのコンパイルをgulpからDart Sassに変更
- 編集画面用のCSS読み込みの処理を変更

## 0.0.1.9
- ルートファイルのバージョンファイルの変更ミスによるバージョンアップ無限ループバグの修正

## 0.0.1.8
- composerパッケージのアップデート
- PR作成時に行うプラグインのバージョン番号取得の仕組みを変更

## 0.0.1.7
- WordPress5.9対応
	- 編集画面でHeroイメージ（1カラム)のロゴ画像がセンターにならないバグ対応
	- 編集画面で編集画面用のCSSが一部読み込まれない部分の対応

## 0.0.1.6
- 初期読み込みするコードをPHPクラス化
- Snow Monkeyから呼び出すメソッドを存在確認してから呼び出す形式に変更
- Snow Monkeyテーマ・Snow Monkey Blocksの有効チェックの処理を変更
- load_textdomainの実行タイミングをinitに変更
- サンプルパターンのSCSSの調整

## 0.0.1.5
- サンプルパターン：段落ブロックで文字サイズが変更できないバグを修正

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
