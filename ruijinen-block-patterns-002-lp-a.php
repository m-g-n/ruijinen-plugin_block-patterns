<?php
/**
 * Plugin name: 類人猿ブロックパターン（テスト！）
 * Description: Snow Monkeyサイトをより素敵にするブロックパターンが入っています
 * Version: 0.0.1
 * Author: mgn Inc.,
 * Author URI: https://rui-jin-en.com/
 * License: GPL-2.0+
 *
 * @package ruijinen-block-patterns-beta
  */

/**
 * 定数を宣言
 */
define( 'RJE_PLUGIN_URL', plugins_url( '', __FILE__ ) );  // このプラグインのURL.
define( 'RJE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) ); // このプラグインのパス.
define( 'RJE_BASENAME', plugin_basename( __FILE__ ) );    // このプラグインのベースネーム.

/**
 * テキストドメインを宣言
 */
function rje_pattern_load_textdomain() {
	load_plugin_textdomain( 'ruijinen-block-patterns-beta', false, dirname( RJE_BASENAME ) . '/languages/' );
}
add_action( 'plugins_loaded', 'rje_pattern_load_textdomain' );

/**
 * ファイルの読み込み /inc
 */
// Snow Monkey および Snow Monkey Blocks が有効化されていない場合の処理.
require_once RJE_PLUGIN_PATH . 'inc/activate.php';
// 自動アップデート.
require_once RJE_PLUGIN_PATH . 'inc/auto-update.php';
// ブロックスタイル及びブロックパターンの設定の読み込み.
// require_once RJE_PLUGIN_PATH . 'inc/load-register-block.php';
// 管理画面に通知を表示.
require_once RJE_PLUGIN_PATH . 'inc/notification-widget.php';
// Composerの読み込み.
require_once RJE_PLUGIN_PATH . 'vendor/autoload.php';

//テスト - 動的メソッド追加
//TODO:テスト後消す
require_once RJE_PLUGIN_PATH . 'inc/test-dynamic-call-2.php';



class hogefuag {
	public function aaaaa(){
		//iroiro
	}
}
$class_desu = new hogefuag();

add_action(
	'register_petterns',
	array($class_desu, 'aaaaa')
);

remove_action('register_petterns','aaaaa');

do_action('register_petterns', $args);





$action_list = $wp_filter['register_petterns'];
var_dump($action_list); //これで優先度は取得できた




