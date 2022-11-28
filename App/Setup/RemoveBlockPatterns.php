<?php
/**
 * ブロックパターンの無効化処理
 *
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\Master\App\Patterns;

/**
 * ブロックパターンの登録に関する処理のクラス
 */
class RemoveBlockPatterns {
	/**
	 * constructer
	 */
	public function __construct() {
		$option = get_option('rje_patterns_unregister');
		if ( !$option ) { return; }
		$this->remove_fileter_patterns( $option );
	}

	/**
	 * Remove_the_hook
	 */
	public function remove_fileter_patterns ( $removes = array() ) {
		if ( !$removes )
			return;
		add_action(
			'plugins_loaded',
			function () use( $removes ) {
				foreach ($removes as $key => $methods) {
					if ( !$methods ) { continue; }
					global ${$key};
					foreach ( $methods as $method ) {
						$filter_name = 'rje_register_patterns_args';
						$priority    = has_filter( $filter_name, array(${$key}, $method));
						remove_filter( $filter_name, array(${$key}, $method), $priority);
					}
				}
			},
			9999
		);
	}
}
