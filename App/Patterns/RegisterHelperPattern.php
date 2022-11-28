<?php
/**
 * ヘルパーブロックパターンの登録処理
 *
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\Master\App\Patterns;

class RegisterHelperPatterns {
	/**
	* constructor
	*/
	public function __construct( $variable_name ) {
		define( 'RJE_R000HELPER_KEY', 'RJE_R000HELPER' ); // どの類人猿プロダクトなのかを示すキー
		add_action( 'init', array( $this, 'register_pattern_cat' ), 10 ); //パターンカテゴリー登録

		//フィルターに登録したいメソッドの情報
		$methods = array(
			array(
				'name' => 'fullwidth_min_padding',
				'label' => 'フル幅時に最低限余白を保持',
				'priority' => 10
			),
			array(
				'name' => 'enforcement_fullwidth',
				'label' => '強制的にフル幅にする',
				'priority' => 10
			)
		);

		//オプション パターン使用の選択肢用の情報を登録
		add_filter(
			'rje_option_unregister_args',
			function ( $args ) use ( $variable_name, $methods ) {
				$helper = array(
					'section_id'   => $variable_name,
					'section_name' => 'ヘルパーパターン',
					'fields' => $methods
				);
				array_push( $args, $helper );
				return $args;
			}
		);

		//パターンの情報をフィルターに登録
		foreach ( $methods as $method ) {
			add_filter( 'rje_register_patterns_args', array( $this, $method['name'] ), $method['priority'] );
		}
	}

	/**
	* ヘルパーパターンのカテゴリを登録
	*/
	public function register_pattern_cat () {
		register_block_pattern_category( RJE_R000HELPER_KEY, array( 'label' => '[類人猿] ヘルパー' ) );
	}

	/**
	* フル幅時に最低限余白を保持.
	*/
	public function fullwidth_min_padding ( $args ) {
		$args[] = array(
			'key'            => RJE_R000HELPER_KEY . '_fullwidth_min_padding',
			'title'          => 'フル幅時に最低限余白を保持',
			'cat'            => array( RJE_R000HELPER_KEY ),
			'specific-style' => false,
			'block-style'    => array( RJE_R000HELPER_KEY . '_fullwidth_min_padding' ),
			'path'           => RJE_BP_PLUGIN_PATH,
		);
		return $args;
	}

	/**
	* 強制的にフル幅にする.
	*/
	public function enforcement_fullwidth ( $args ) {
		$args[] = array(
			'key'            => RJE_R000HELPER_KEY . '_enforcement_fullwidth',
			'title'          => '強制的にフル幅にする',
			'cat'            => array( RJE_R000HELPER_KEY ),
			'specific-style' => false,
			'block-style'    => array( RJE_R000HELPER_KEY . '_enforcement_fullwidth' ),
			'path'           => RJE_BP_PLUGIN_PATH,
		);
		return $args;
	}
}
