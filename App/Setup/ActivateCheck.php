<?php
/**
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\Master\App\Setup;

class ActivateCheck {
	//プロパティ
	public $messages   = array();

	//初期処理
	public function __construct() {
		$this->check_snow_monkey_activate();
		$this->check_snow_monkey_blocks_activate();
	}

	//Snow Monkeyテーマが有効かチェック.
	public function check_snow_monkey_activate () {
		$theme = wp_get_theme( get_template() );
		if ( 'snow-monkey' != $theme->template && 'snow-monkey/resources' != $theme->template ) {
			$this->messages['snow_monkey'] = 'Snow Monkeyテーマが必要です';
		}
	}

	//Snow Monkey Blocksが有効かチェック.
	public function check_snow_monkey_blocks_activate () {
		if ( !class_exists('\Snow_Monkey\Plugin\Blocks\Bootstrap') ) {
		// if ( is_plugin_active( 'snow-monkey-blocks/snow-monkey-blocks.php' ) ) {
			$this->messages['snow_monkey_blocks'] = 'Snow Monkey Blocksプラグインが必要です';
		}
	}

	//必要なパッケージがアクティベートされてない場合のエラーメッセージ
	public function make_alert_message() {
		$alert_html = '<div class="notice notice-warning is-dismissible"><p><strong>[類人猿ブロックパターンプラグイン]</strong></p>';
		foreach ( $this->messages as $text ) {
			$alert_html .= '<p>'.$text.'</p>';
		}
		$alert_html .= '</div>';
		echo $alert_html;
	}
}