<?php
/**
 * サンプルブロックパターンの登録処理
 *
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\Master\App\Patterns;

class RegisterSamplePatterns {

	/**
	 * construct
	 */
	public function __construct( $variable_name ) {
		//LPパターン集が有効な場合は離脱（パターンが重複してしまうため）
		if ( class_exists( '\Ruijinen\Pattern\R002LP\Bootstrap' ) ) { return; }

		define( 'RJE_R000SAMPLE_KEY', 'RJE_R000SAMPLE' ); // どの類人猿プロダクトなのかを示すキー
		add_action( 'init', array( $this, 'register_pattern_cat' ), 10 ); //パターンカテゴリー登録

		//フィルターに登録したいメソッドの情報
		$methods = array(
			array(
				'name' => 'hero_one_column',
				'label' => 'Heroイメージ（1カラム)',
				'priority' => 10
			),
			array(
				'name' => 'message_accent2',
				'label' => '伝えたいこと',
				'priority' => 10
			),
			array(
				'name' => 'flow',
				'label' => '流れ・手順',
				'priority' => 10
			)
		);

		//オプションページの選択肢用の情報を登録
		add_filter(
			'rje_option_unregister_args',
			function ( $args ) use ( $variable_name, $methods ) {
				$sample = array(
					'section_id'   => $variable_name,
					'section_name' => 'サンプルパターン',
					'fields' => $methods
				);
				array_push( $args, $sample );
				return $args;
			}
		);

		//パターンの情報をフィルターに登録
		foreach ( $methods as $method ) {
			add_filter( 'rje_register_patterns_args', array( $this, $method['name'] ), $method['priority'] );
		}

		//スタイルを読み込む
		$this->common_styles();
	}

	/**
	* サンプルパターンの共通のスタイルを読み込む
	*/
	private function common_styles () {
		$sm_style_handles = ( method_exists('\Framework\Helper', 'get_main_style_handle') ) ? \Framework\Helper::get_main_style_handle() : ''; //Snow Monkeyの依存ハンドル名の取得
		$path = 'dist/css/sample-pattern-common.css'; //CSSのパス

		//サンプルパターン共通のスタイルを読み込む
		add_action(
			'wp_enqueue_scripts',
			function () use ($sm_style_handles, $path) {
				wp_enqueue_style( RJE_R000SAMPLE_KEY . 'sample-pattern-common', RJE_BP_PLUGIN_URL . $path, $sm_style_handles, filemtime( RJE_BP_PLUGIN_PATH . $path ) );
			},
			10
		);
		add_action(
			'enqueue_block_editor_assets',
			function () use ($sm_style_handles, $path) {
				wp_enqueue_style( RJE_R000SAMPLE_KEY . 'sample-pattern-common', RJE_BP_PLUGIN_URL . $path, $sm_style_handles, filemtime( RJE_BP_PLUGIN_PATH . $path ) );
			},
			10
		);
	}

	/**
	* サンプルパターンのカテゴリを登録.
	*/
	public function register_pattern_cat () {
		register_block_pattern_category( RJE_R000SAMPLE_KEY, array( 'label' => '[類人猿] サンプル' ) );
	}

	/**
	* Hero1カラム.
	*/
	public function hero_one_column( $args ) {
		$args[] = array(
			'key'            => RJE_R000SAMPLE_KEY . '_hero_one_column',
			'title'          => 'Heroイメージ（1カラム)',
			'cat'            => array( RJE_R000SAMPLE_KEY ),
			'specific-style' => false,
			'block-style'    => array( 'RJE_R002LP_hero_one_column' ),
			'path'           => RJE_BP_PLUGIN_PATH,
		);
		return $args;
	}

	/**
	* 伝えたいこと（アクセント2）.
	*/
	public function message_accent2( $args ) {
		$args[] = array(
			'key'            => RJE_R000SAMPLE_KEY . '_message_accent2',
			'title'          => '伝えたいこと',
			'cat'            => array( RJE_R000SAMPLE_KEY ),
			'specific-style' => FALSE,
			'block-style'    => array( 'RJE_R002LP_message_accent2' ),
			'path'           => RJE_BP_PLUGIN_PATH,
		);
		return $args;
	}

	/**
	* 流れ・手順.
	*/
	public function flow( $args ) {
		$args[] = array(
			'key'            => RJE_R000SAMPLE_KEY . '_flow',
			'title'          => '流れ・手順',
			'cat'            => array( RJE_R000SAMPLE_KEY ),
			'specific-style' => FALSE,
			'block-style'    => array( 'RJE_R002LP_section1', 'RJE_R002LP_flow_panels' ),
			'path'           => RJE_BP_PLUGIN_PATH,
		);
		return $args;
	}
}

