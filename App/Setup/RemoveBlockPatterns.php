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
 * オプションページで無効化されたブロックパターンをフィルターから外すクラス
 */
class RemoveBlockPatterns {
	/**
	 * パターン情報を登録するフィルター名
	 */
	const FILTER_NAME = 'rje_register_patterns_args';

	/**
	 * constructer
	 */
	public function __construct() {
		// パターンの登録（RegisterBlockPatterns::register_patterns / init 15）の直前に外す.
		add_action( 'init', array( $this, 'remove_filter_patterns' ), 14 );
	}

	/**
	 * 無効化されたパターンのフィルターを外す
	 */
	public function remove_filter_patterns() {
		$removes = get_option( 'rje_patterns_unregister' );
		if ( ! $removes || ! is_array( $removes ) ) {
			return;
		}
		foreach ( $removes as $key => $methods ) {
			// キーはパターン登録クラスのインスタンスを格納したグローバル変数名.
			if ( ! $methods || ! is_array( $methods ) || ! is_string( $key ) || ! isset( $GLOBALS[ $key ] ) || ! is_object( $GLOBALS[ $key ] ) ) {
				continue;
			}
			$instance = $GLOBALS[ $key ];
			foreach ( $methods as $method ) {
				if ( ! is_string( $method ) ) {
					continue;
				}
				$priority = has_filter( self::FILTER_NAME, array( $instance, $method ) );
				if ( false !== $priority ) {
					remove_filter( self::FILTER_NAME, array( $instance, $method ), $priority );
				}
			}
		}
	}
}
