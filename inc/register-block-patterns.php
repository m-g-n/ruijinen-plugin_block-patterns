<?php

//TODO：名前空間の再考
namespace Ruijinen\Pattern\Common;

// TODO：クラス名を変更する
/**
 * ブロックパターンの登録に関する処理のクラス
 */
class RegisterBlockPatterns {

	use \Ruijinen\Pattern\Common\DynamicMethodDeclaration; // 動的メソッド生成traitを使用

	// プロパティ.
	public $load_style_handle = '';
	public $style_front_deps  = '';
	public $style_editor_deps = '';
	public $hook_id; // WPから割当られたフックのID.
	public $register_patterns; // 登録するパターンの全情報.
	public $target_pattern; //登録するパターン（単独）.
	public $file_path; //呼び出されたプラグインのパス.

	// デフォルトの値を設定.
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
		add_action( 'init', array( $this, 'register_block_patterns' ), 15 ); //パターン登録
		add_action( 'init', array( $this, 'register_block_style' ), 20 ); //ブロックスタイル登録
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style_front' ) ); //フロント用のCSS
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_style_editor' ) ); //エディタ用のCSS
		add_action( 'init', array( $this, 'get_hook_id' ), 50 ); //フックのID値取得
	}


	/**
	 * 各ブロックパターン登録について動的にメソッドを生成し、フックで実行するように処理
	 */
	//TODO：メソッド名の再考
	public function register_block_patterns() {
		foreach ( $this->register_patterns as $properties ) {
			$this->target_pattern = $properties; //メソッド作成時に値を渡すため、プロパティとして値を代入しておく.
			$method_name = 'register_' . $properties['key']; // 作成するメソッド名.
			// 各ブロックパターン宣言のメソッドを作成
			$this->addMethod(
				$method_name,
				function() {
					$this->register_block_pattern();
				}
			);
			add_action( 'RJE_register_petterns', array( $this, $method_name ), $properties['order'] ); // フックをかける.
		}
	}


	/**
	 * 指定したパターンの登録処理
	 */
	public function register_block_pattern() {
		//登録したいパターン情報を取得
		$args = $this->target_pattern;

		// 例外処理
		// TODO:どこかでエラーでそう、正しい判断のものを書いたほうが良さそう
		if ( ! $args['key'] || ! $args['cat'] || ! $args['title'] ) {
			return;
		}

		// 使用ブロックスタイル定義
		foreach ( $args['style'] as $block_style_name ) {
			$this->load_style_handle[ $block_style_name ][] = $pattern_title;
		}

		// パターン内容を取得
		$contents = '';
		ob_start();
		require_once $this->file_path . 'patterns/' . $args['key'] . '/pattern.php';
		$contents = ob_get_contents();
		ob_end_clean();

		// パターン登録
		register_block_pattern(
			'RJE-pattern/' . $args['key'],
			array(
				'title'      => $args['title'],
				'content'    => $contents,
				'categories' => $args['cat'],
			)
		);
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

	/**
	 * パターンを宣言するアクションフックのキーの値を取得するための処理
	 *
	 * @param int $priority IDを取得ためのフックの優先度（最優先・ユニークにしたいのでありえない負の数値を設定）.
	 */
	public function get_hook_id( $priority = -9999 ) {
		global $wp_filter;

		// 割当られたアクションフックのIDを取得すために何も実行しない無名関数をhookさせる.
		add_action( 'RJE_register_petterns', function(){}, $priority );

		// $wp_filterにてかけてあるフックの情報を取得し、IDをプロパティに格納.
		$func_args     = $wp_filter['RJE_register_petterns'][ $priority ];
		$this->hook_id = key( $func_args );

		// 無名関数フックは必要なくなったためremoveする.
		remove_action( 'RJE_register_petterns', $this->hook_id, $priority );
	}
}


/**
 * 類人猿のブロックパターンを登録するアクション
 */
//TODO：しかるべき場所に移動する
add_action(
	'wp_loaded',
	function(){
		do_action('RJE_register_petterns', $args);
	},
	99
);
