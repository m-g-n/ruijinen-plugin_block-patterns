<?php

//TODO：名前空間の再考
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
	public $hook_id; // WPから割当られたフックのID.
	public $file_path; //呼び出されたプラグインのパス.

	public function __construct() {
		//プロパティ初期値設定.
		$this->load_style_handle = array();
		$this->style_front_deps  = array( 'snow-monkey', 'snow-monkey-blocks', 'snow-monkey-snow-monkey-blocks', 'snow-monkey-blocks-background-parallax' );
		$this->style_editor_deps = array( 'snow-monkey-snow-monkey-blocks-editor' );
	}

	/**
	 * ブロックパターンの登録に関する処理を実行する
	 */
	public function init() {
		add_action( 'init', array( $this, 'register_patterns' ), 15 ); //パターン登録
		add_action( 'wp_loaded', array( $this, 'register_block_style' ), 100 ); //ブロックスタイル登録（パターン登録後ではないと動かないため優先度低）
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style_front' ) ); //フロント用のCSS
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_style_editor' ) ); //エディタ用のCSS
	}

	/**
	 * ブロックパターンを登録
	 */
	public function register_patterns () {
		$patterns = apply_filters( 'rje_register_patterns_args', array(), $patterns );
		foreach ( $patterns as $pattern ) {
			//例外処理
			if ( ! $pattern['key'] || ! $pattern['cat'] || ! $pattern['title'] ) {
				continue;
			}

			// 使用するブロックスタイルを設定
			foreach ( $pattern['style'] as $block_style_name ) {
				$this->load_style_handle[ $block_style_name ][] = $pattern['title'];
			}

			// パターンの内容を取得
			$contents = '';
			ob_start();
			require_once $this->file_path . 'patterns/' . $pattern['key'] . '/pattern.php';
			$contents = ob_get_contents();
			ob_end_clean();

			// パターン登録
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

	/**
	 * ブロックスタイルの登録
	 */
	public function register_block_style() {
		foreach ( $this->load_style_handle as $handle => $use_patterns ) {
			foreach ( glob( $this->file_path . 'block-styles/*/*/' . $handle . '/register.php' ) as $file ) {
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