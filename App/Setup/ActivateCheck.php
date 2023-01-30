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
	public $inactivate = false;

	//初期処理
	public function __construct() {
		$this->check_snow_monkey_blocks_activate();
		$this->check_php_version();
	}

	//Snow Monkey Blocksが有効かチェック.
	public function check_snow_monkey_blocks_activate () {
		if ( !class_exists('\Snow_Monkey\Plugin\Blocks\Bootstrap') ) {
			$this->messages['snow_monkey_blocks'] = 'Snow Monkey Blocksプラグインが必要です';
			$this->inactivate = true;
		}
	}

	//PHPバージョンが7.4以上かをチェック.（7.3以下でもアクティベートは維持したまま）
	public function check_php_version () {
		$version = phpversion();
		if ( $version < 7.4 ) {
			$this->messages['php'] = '<a href="https://rui-jin-en.com/2023/01/27/2739/" target="_blank" rel="noopener">当プロダクトはPHP7.4以上を推奨しています</a>';
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
