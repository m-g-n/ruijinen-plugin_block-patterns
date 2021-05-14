<?php

// TODO：パターンごとにプラグインわけわけになるので、名前空間か接頭辞つけてかぶらないようにする
namespace Ruijinen\Pattern\P002_LP;

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
	public $cat; // パターンカテゴリー.
	public $priority; // 優先度.
	public $register_patterns; // 宣言するパターン情報.

	// デフォルトの値を設定
	public function __construct() {
		$this->load_style_handle = array();
		$this->style_front_deps  = array( 'snow-monkey', 'snow-monkey-blocks', 'snow-monkey-snow-monkey-blocks', 'snow-monkey-blocks-background-parallax' );
		$this->style_editor_deps = array( 'snow-monkey-snow-monkey-blocks-editor' );

		// TODO：動的に値を引っ張れないか
		$this->cat               = array( 'RJE-company' ); // デフォルトのカテゴリ
		$this->priority          = 10; // デフォルトの優先度
		$this->register_patterns = array( // 宣言するパターン
			array(
				'key'   => 'test_pettern1',
				'title' => 'テストパターンX',
				'order' => $this->priority,
				'cat'   => $this->cat,
				'style' => array(),
			),
		);

		add_action( 'plugins_loaded', array( $this, 'init' ) );

		$this->get_hook_id(); // フックID取得処理
	}

	/**
	 * パターン登録に関する事前設定処理
	 */
	public function init() {
		add_action( 'init', array( $this, 'register_block_pattern_category' ), 10 );
		add_action( 'init', array( $this, 'add_action_register_patterns' ), 15 );
		add_action( 'init', array( $this, 'register_block_style' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style_front' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_style_editor' ) );
	}

	/**
	 * ブロックパターンカテゴリーの登録
	 */
	// TODO：あとで適切な場所に移動
	public function register_block_pattern_category() {
		register_block_pattern_category( 'RJE-company', array( 'label' => '[類人猿] 企業サイト' ) );
	}

	/**
	 * ブロックスタイルの登録
	 */
	public function register_block_style() {
		foreach ( $this->load_style_handle as $handle => $use_patterns ) {
			foreach ( glob( RJE_PLUGIN_PATH . 'block-styles/*/*/' . $handle . '/register.php' ) as $file ) {
				require_once $file;
			}
		}
	}

	/**
	 * 表示用のCSSの設定
	 */
	public function enqueue_style_front() {
		foreach ( $this->load_style_handle as $handle => $use_patterns ) {
			wp_enqueue_style( $handle . '-front' );
		}
	}

	/**
	 * 編集画面用のCSSの設定
	 */
	public function enqueue_style_editor() {
		foreach ( $this->load_style_handle as $handle => $use_patterns ) {
			wp_enqueue_style( $handle . '-editor' );
		}
	}

//TODO：宣言→登録に各種文言変更する
	/**
	 * 各パターンの実行処理を動的に出力したメソッドに格納、パターン登録フックに動的メソッドをそれぞれ掛ける
	 */
	public function add_action_register_patterns () {
		foreach ( $this->register_patterns as $properties ) {
			$method_name = 'register_' . $properties['key']; // 作成するメソッド名
			// 各ブロックパターン宣言のメソッドを作成
			$this->addMethod(
				$method_name,
				function() {
					$this->register_block_pattern( $properties );
				}
			);
			add_action( 'RJE_register_petterns', array( $this, $method_name ), $properties['order'] ); // フックをかける
		}
	}

	/**
	 * パターンを宣言するアクションフックのキーの値を取得するための処理
	 *
	 * @param int $priority IDを取得ためのフックの優先度（最優先・ユニークにしたいのでありえない負の数値を設定）.
	 */
	private function get_hook_id( $priority = -9999 ) {
		global $wp_filter;

		// 割当られたアクションフックのIDを取得すために何も実行しない無名関数をhookさせる.
		add_action( 'RJE_register_petterns', function(){}, $priority );

		// $wp_filterにてかけてあるフックの情報を取得し、IDをプロパティに格納.
		$func_args     = $wp_filter['RJE_register_petterns'][ $priority ];
		$this->hook_id = key( $func_args );

		// 無名関数フックは必要なくなったためremoveする.
		remove_action( 'RJE_register_petterns', $this->hook_id, $priority );
	}


	/**
	 * argsで指定したパターンの登録処理
	 *
	 * @param $args 登録したいパターンの情報
	 */
	public function register_block_pattern( $args ) {
		// TODO：引数でarg引っ張れるようにする
		$args = array(
			'key'   => 'test_pettern1',
			'title' => 'テストパターンX',
			'order' => 10,
			'cat'   => array( 'RJE-company' ),
			'style' => array(),
		);
		// 例外処理
		// TODO:どこかでエラーでそう、正しい判断のものを書いたほうが良さそう
		if ( ! $args['key'] || ! $args['cat'] || ! $args['title'] ) {
			return;
		}

		// 使用ブロックスタイル定義.
		foreach ( $args['style'] as $block_style_name ) {
			$this->load_style_handle[ $block_style_name ][] = $pattern_title;
		}

		// パターン内容を取得.
		$contents = '';
		ob_start();
		require_once RJE_PLUGIN_PATH . 'patterns/' . $args['key'] . '/pattern.php';
		$contents = ob_get_contents();
		ob_end_clean();

		// パターン登録.
		register_block_pattern(
			'RJE-pattern/' . $args['key'],
			array(
				'title'      => $args['title'],
				'content'    => $contents,
				'categories' => $args['cat'],
			)
		);
	}
}

new RegisterBlockPatterns();