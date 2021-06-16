<?php
/**
 * ブロックパターンの登録処理
 * 
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern;

/**
 * ブロックパターンの登録に関する処理のクラス
 */
class RegisterBlockPatterns {

	// プロパティ.
	public $load_block_style_handle = ''; //登録するブロックスタイル情報.
	public $load_specific_style_handle = ''; //登録するパターン固有スタイル情報.
	public $style_front_deps  = '';
	public $style_editor_deps = '';

	public function __construct() {
		//プロパティ初期値設定.
		$this->load_block_style_handle = array();
		$this->load_specific_style_handle = array();
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
		add_action( 'init', array( $this, 'register_specific_style' ), 20 ); //パターン固有のスタイル登録
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
				//例外処理 - 必要な値が取得できない場合は飛ばす
				if ( empty( $pattern['key'] ) || empty( $pattern['cat'] ) || empty( $pattern['title'] ) || empty( $pattern['path'] )  ) {
					continue;
				}

				// パターン固有のスタイル（ブロックパターンではないもの）が存在する場合は設定.
				if ( !empty( $pattern['specific-style'] ) && TRUE == $pattern['specific-style'] ) {
					$this->load_specific_style_handle[ $pattern['key'] ]['path'] = $pattern['path'];
					$this->load_specific_style_handle[ $pattern['key'] ]['use_list'][] = $pattern['title'];
				}

				// 使用するブロックスタイルを設定.
				if ( !empty( $pattern['block-style'] ) ) {
					foreach ( (array)$pattern['block-style'] as $block_style_name ) {
						$this->load_block_style_handle[ $block_style_name ]['path'] = $pattern['path'];
						$this->load_block_style_handle[ $block_style_name ]['use_list'][] = $pattern['title'];
					}
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
	 * パターン固有スタイルの登録
	 */
	public function register_specific_style () {
		foreach ( $this->load_specific_style_handle as $handle => $use_patterns ) {
			foreach ( glob( $use_patterns['path'] . 'patterns/' . $handle . '/register-style.php' ) as $file ) {
				require_once $file;
			}
		}
	}

	/**
	 * ブロックスタイルの登録
	 */
	public function register_block_style() {
		foreach ( $this->load_block_style_handle as $handle => $use_patterns ) {
			foreach ( glob( $use_patterns['path'] . 'block-styles/*/*/' . $handle . '/register.php' ) as $file ) {
				require_once $file;
			}
		}
	}

	/**
	 * 指定のハンドルのスタイルを読み込む（フロント用）
	 */
	public function enqueue_style_front() {
		//ブロックスタイル
		foreach ( $this->load_block_style_handle as $handle => $use_patterns ) {
			wp_enqueue_style( 'is-style-' . $handle . '-front' );
		}
		//固有のパターンのスタイル
		foreach ( $this->load_specific_style_handle as $handle => $use_patterns ) {
			wp_enqueue_style( $handle . '-front' );
		}
	}

	/**
	 * 指定のハンドルのスタイルを読み込む（エディター用）
	 */
	public function enqueue_style_editor() {
		//ブロックスタイル
		foreach ( $this->load_block_style_handle as $handle => $use_patterns ) {
			wp_enqueue_style( 'is-style-' . $handle . '-editor' );
		}
		//固有のパターンのスタイル
		foreach ( $this->load_specific_style_handle as $handle => $use_patterns ) {
			wp_enqueue_style( $handle . '-editor' );
		}
	}
}