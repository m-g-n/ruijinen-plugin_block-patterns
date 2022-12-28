<?php
/**
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\Master\App\Setup;

class CheckActiveThemes {
	//プロパティ
	public $name = '';

	//初期処理
	public function __construct() {
		$this->get_themes_name();
	}

	//有効化されてるテーマ名を取得
	private function get_themes_name () {
		$theme = wp_get_theme( get_template() );
		switch ( $theme->template ) {
			case 'snow-monkey' :
			case 'snow-monkey/resources' :
				$this->name = 'snow-monkey';
				break;
		}
	}

}
