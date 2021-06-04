<?php
/**
 * ブロックパターンの登録処理
 * 
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */


namespace Ruijinen\Pattern;

// TODO：クラス名を変更する
/**
 * ブロックパターンの登録に関する処理のクラス
 */
class RegisterBlockPatterns {

	// プロパティ.
	public $load_style_handle = ''; //登録するブロックスタイル情報.
	public $style_front_deps  = '';
	public $style_editor_deps = '';

	public function __construct() {
		//プロパティ初期値設定.
		$this->load_style_handle = array();
		$this->style_front_deps  = array( 'snow-monkey', 'snow-monkey-blocks', 'snow-monkey-snow-monkey-blocks', 'snow-monkey-blocks-background-parallax' );
		$this->style_editor_deps = array( 'snow-monkey-snow-monkey-blocks-editor' );
		//処理実行
		$this->init();
	}

	/**
	 * ブロックパターンの登録に関する処理を実行する
	 */
	public function init() {
		add_action( 'init', array( $this, 'register_patterns' ), 15 ); //パターン登録
		add_action( 'init', array( $this, 'register_block_style' ), 20 ); //ブロックスタイル登録
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style_front' ) ); //フロント用のCSS
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_style_editor' ) ); //エディタ用のCSS
	}

	/**
	 * ブロックパターンを登録
	 */
	public function register_patterns () {
		$patterns = apply_filters( 'rje_register_patterns_args', array(), 'args' );
		if ( $patterns && is_array( $patterns ) ) {
			foreach ( $patterns as $pattern ) {
				// 例外処理.
				if ( ! $pattern['key'] || ! $pattern['cat'] || ! $pattern['title'] || ! $pattern['path'] ) {
					continue;
				}

				// 使用するブロックスタイルを設定.
				foreach ( $pattern['style'] as $block_style_name ) {
					$this->load_style_handle[ $block_style_name ]['path'] = $pattern['path'];
					$this->load_style_handle[ $block_style_name ]['use_list'][] = $pattern['title'];
				}

				// パターンの内容を取得.
				$contents = '';
				ob_start();
				require_once $pattern['path'] . 'patterns/' . $pattern['key'] . '/pattern.php';
				$contents = ob_get_contents();
				ob_end_clean();

				// パターン登録.
				register_block_pattern(
					'RJE-pattern/' . $pattern['key'],
					array(
						'title'      => $pattern['title'],
						'content'    => $contents,
						'categories' => $pattern['cat'],
					)
				);
			}
		}
	}

	/**
	 * ブロックスタイルの登録
	 */
	public function register_block_style() {
		foreach ( $this->load_style_handle as $handle => $use_patterns ) {
			foreach ( glob( $use_patterns['path'] . 'block-styles/*/*/' . $handle . '/register.php' ) as $file ) {
				require_once $file;
			}
		}
	}

	/**
	 * ブロックスタイルのフロントスタイル
	 */
	public function enqueue_style_front() {
		foreach ( $this->load_style_handle as $handle => $use_patterns ) {
			wp_enqueue_style( $handle . '-front' );
		}
	}

	/**
	 * ブロックスタイルのエディタスタイル
	 */
	public function enqueue_style_editor() {
		foreach ( $this->load_style_handle as $handle => $use_patterns ) {
			wp_enqueue_style( $handle . '-editor' );
		}
	}
}