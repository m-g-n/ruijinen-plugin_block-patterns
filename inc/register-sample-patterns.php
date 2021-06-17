<?php
/**
 * ブロックパターンの登録処理
 * 
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\SamplePattern;

class RegisterSamplePatterns {

	public function __construct() {
		define( 'RJE_R000SAMPLE_KEY', 'RJE_R000SAMPLE' ); // どの類人猿プロダクトなのかを示すキー
		$this->init();
	}

	/**
	 * ブロックパターンの登録に関する処理を実行する
	 */
	public function init() {
		add_action( 'init', array( $this, 'register_pattern_cat' ), 10 ); //パターンカテゴリー登録
		add_action( 'plugins_loaded', array( $this, 'register_add_pettern_args' ) ); //サンプルパターンの情報を追加

		//サンプルパターン共通のスタイルを読み込む
		add_action(
			'wp_enqueue_scripts',
			function () {
				$path = 'dist/css/sample-pattern-common.css';
				wp_enqueue_style( RJE_R000SAMPLE_KEY . 'sample-pattern-common', RJE_BP_PLUGIN_URL . $path, array( 'snow-monkey', 'snow-monkey-blocks', 'snow-monkey-snow-monkey-blocks', 'snow-monkey-blocks-background-parallax' ), filemtime( RJE_BP_PLUGIN_PATH . $path ) );
			},
			10
		);
		add_action(
			'enqueue_block_editor_assets',
			function () {
				$path = 'dist/css/sample-pattern-common.css';
				wp_enqueue_style( RJE_R000SAMPLE_KEY . 'sample-pattern-common', RJE_BP_PLUGIN_URL . $path,  array( 'snow-monkey-snow-monkey-blocks-editor' ), filemtime( RJE_BP_PLUGIN_PATH . $path ) );
			},
			10
		);
	}

	/**
	* サンプルパターンのカテゴリを登録
	*/
	public function register_pattern_cat () {
		register_block_pattern_category( RJE_R000SAMPLE_KEY, array( 'label' => '[類人猿] サンプル' ) );
	}

	/**
	* サンプルパターン情報を追加
	*/
	function register_add_pettern_args() {
		// 登録するパターンをhookに追加
		add_filter( 'rje_register_patterns_args', array( $this, 'rje_r000sample_hero_one_column' ), 10 );
		add_filter( 'rje_register_patterns_args', array( $this, 'rje_r000sample_message_accent2' ), 10 );
		add_filter( 'rje_register_patterns_args', array( $this, 'rje_r000sample_flow' ), 10 );
	}
	public function rje_r000sample_hero_one_column( $args ) {
		$args[] = array(
			'key'            => RJE_R000SAMPLE_KEY . '_hero_one_column',
			'title'          => 'Heroイメージ（1カラム)',
			'cat'            => array( RJE_R000SAMPLE_KEY ),
			'specific-style' => false,
			'block-style'    => array( RJE_R000SAMPLE_KEY . '_hero_one_column' ),
			'path'           => RJE_BP_PLUGIN_PATH,
		);
		return $args;
	}
	public function rje_r000sample_message_accent2( $args ) {
		$args[] = array(
			'key'            => RJE_R000SAMPLE_KEY . '_message_accent2',
			'title'          => '伝えたいこと',
			'cat'            => array( RJE_R000SAMPLE_KEY ),
			'specific-style' => FALSE,
			'block-style'    => array( RJE_R000SAMPLE_KEY . '_message_accent2' ),
			'path'           => RJE_BP_PLUGIN_PATH,
		);
		return $args;
	}
	public function rje_r000sample_flow( $args ) {
		$args[] = array(
			'key'            => RJE_R000SAMPLE_KEY . '_flow',
			'title'          => '流れ・手順',
			'cat'            => array( RJE_R000SAMPLE_KEY ),
			'specific-style' => FALSE,
			'block-style'    => array( RJE_R000SAMPLE_KEY . '_section1', RJE_R000SAMPLE_KEY . '_flow_panels' ),
			'path'           => RJE_BP_PLUGIN_PATH,
		);
		return $args;
	}
}

new RegisterSamplePatterns();