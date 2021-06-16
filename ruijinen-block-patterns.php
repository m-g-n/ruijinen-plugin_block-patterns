<?php
/**
 * Plugin name: 類人猿ブロックパターン
 * Description: Snow Monkeyサイトをより素敵にするブロックパターンを提供
 * Version: 0.0.0.7
 * Author: mgn Inc.,
 * Author URI: https://rui-jin-en.com/
 * License: GPL-2.0+
 *
 * @package ruijinen-block-patterns
 */


/**
 * 定数を宣言
 */
define( 'RJE_BP_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) . '/' );  // このプラグインのURL.
define( 'RJE_BP_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/' ); // このプラグインのパス.
define( 'RJE_BP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); // このプラグインのベースネーム.


/**
 * テキストドメインを宣言
 */
function rje_pattern_load_textdomain() {
	load_plugin_textdomain( 'ruijinen-block-patterns-beta', false, dirname( RJE_BP_PLUGIN_BASENAME ) . '/languages/' );
}
add_action( 'plugins_loaded', 'rje_pattern_load_textdomain' );


/**
 * ファイルの読み込み /inc
 */
// Snow Monkey および Snow Monkey Blocks が有効化されていない場合の処理.
require_once RJE_BP_PLUGIN_PATH . 'inc/activate.php';
// 自動アップデート.
require_once RJE_BP_PLUGIN_PATH . 'inc/auto-update.php';
// 管理画面に通知を表示.
require_once RJE_BP_PLUGIN_PATH . 'inc/notification-widget.php';
// Composerの読み込み.
require_once RJE_BP_PLUGIN_PATH . 'vendor/autoload.php';
// テスト用のパターンの登録.
require_once RJE_BP_PLUGIN_PATH . 'inc/register-test-patterns.php';
// ブロック登録に関する処理のクラス.
require_once RJE_BP_PLUGIN_PATH . 'inc/register-block-patterns.php';

/**
 * ブロックパターン登録実行
 */
add_action( 
	'after_setup_theme',
	function(){
		new Ruijinen\Pattern\RegisterBlockPatterns();
	},
	9999
);