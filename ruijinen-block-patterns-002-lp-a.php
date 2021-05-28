<?php
/**
 * Plugin name: 類人猿ブロックパターン（LPパターン 開発なう！）
 * Description: Snow Monkeyサイトをより素敵にするブロックパターンが入っています
 * Version: 0.0.1
 * Author: mgn Inc.,
 * Author URI: https://rui-jin-en.com/
 * License: GPL-2.0+
 *
 * @package ruijinen-block-patterns
 */


/**
 * 定数を宣言
 */
// TODO：定数の名称を再検討
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
// 管理画面に通知を表示.
require_once RJE_PLUGIN_PATH . 'inc/notification-widget.php';
// Composerの読み込み.
require_once RJE_PLUGIN_PATH . 'vendor/autoload.php';
// ブロック登録に関する処理のクラス.
require_once RJE_PLUGIN_PATH . 'inc/register-block-patterns.php';



// TODO：以下外部ファイルから呼び込む形にしてみても
define( 'RJE_P002LP_KEY', 'RJE_P002LP' ); // どの類人猿プロダクトなのかを示すキー

// LPブロックパターン用のカテゴリを登録
add_action(
	'init',
	function () {
		register_block_pattern_category( RJE_P002LP_KEY, array( 'label' => '[類人猿] LPサイト' ) );
	},
	10
);

//登録するパターンの指定
add_filter( 'rje_register_patterns_args', 'rje_P002lp_hero_media_and_text', 10 );
add_filter( 'rje_register_patterns_args', 'rje_P002lp_hero_media_and_text__alignright', 10 );
add_filter( 'rje_register_patterns_args', 'rje_P002lp_hero_one_column', 10 );
add_filter( 'rje_register_patterns_args', 'rje_P002lp_message_normal', 10 );
add_filter( 'rje_register_patterns_args', 'rje_P002lp_message_normal__alignright', 10 );
add_filter( 'rje_register_patterns_args', 'rje_P002lp_message_accent', 10 );
add_filter( 'rje_register_patterns_args', 'rje_P002lp_flow', 10 );

function rje_P002lp_hero_media_and_text ( $args ) {
	$args[] = array(
		'key'   => RJE_P002LP_KEY . '_hero_media_and_text',
		'title' => 'Heroイメージ（メディアと文章)',
		'cat'   => array( RJE_P002LP_KEY ),
		'style' => array( RJE_P002LP_KEY . '_hero_media_and_text' ),
	);
	return $args;
}
function rje_P002lp_hero_media_and_text__alignright ( $args ) {
	$args[] = array(
		'key'   => RJE_P002LP_KEY . '_hero_media_and_text__alignright',
		'title' => 'Heroイメージ（メディアと文章) - 右寄せ',
		'cat'   => array( RJE_P002LP_KEY ),
		'style' => array( RJE_P002LP_KEY.'_hero_media_and_text' ),
	);
	return $args;
}
function rje_P002lp_hero_one_column ( $args ) {
	$args[] = array(
		'key'   => RJE_P002LP_KEY . '_hero_one_column',
		'title' => 'Heroイメージ（1カラム)',
		'cat'   => array( RJE_P002LP_KEY ),
		'style' => array( RJE_P002LP_KEY.'_hero_one_column' ),
	);
	return $args;
}
function rje_P002lp_message_normal ( $args ) {
	$args[] = array(
		'key'   => RJE_P002LP_KEY . '_message_normal',
		'title' => '伝えたいこと（ノーマル)',
		'cat'   => array( RJE_P002LP_KEY ),
		'style' => array( RJE_P002LP_KEY.'_message_normal' ),
	);
	return $args;
}
function rje_P002lp_message_normal__alignright ( $args ) {
	$args[] = array(
		'key'   => RJE_P002LP_KEY . '_message_normal__alignright',
		'title' => '伝えたいこと（ノーマル) - 右寄せ',
		'cat'   => array( RJE_P002LP_KEY ),
		'style' => array( RJE_P002LP_KEY.'_message_normal' ),
	);
	return $args;
}
function rje_P002lp_message_accent ( $args ) {
	$args[] = array(
		'key'   => RJE_P002LP_KEY . '_message_accent',
		'title' => '伝えたいこと（アクセント)',
		'cat'   => array( RJE_P002LP_KEY ),
		'style' => array( RJE_P002LP_KEY.'_message_accent' ),
	);
	return $args;
}
function rje_P002lp_flow ( $args ) {
	$args[] = array(
		'key'   => RJE_P002LP_KEY . '_flow',
		'title' => '流れ・手順',
		'cat'   => array( RJE_P002LP_KEY ),
		'style' => array( RJE_P002LP_KEY.'_flow' ),
	);
	return $args;
}


//実行
$rje_p002lp_register_pattern = new Ruijinen\Pattern\RegisterBlockPatterns();
$rje_p002lp_register_pattern->file_path = RJE_PLUGIN_PATH;
$rje_p002lp_register_pattern->register_patterns();

