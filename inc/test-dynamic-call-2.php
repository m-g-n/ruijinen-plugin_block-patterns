<?php

//動的にメソッドを追加する処理
//ソース元： https://qiita.com/suin/items/99c95c6b2182b54d9405
trait DynamicMethodDeclaration {

	/**
	 * @var \Closure[]
	 */
	private $__dynamicMethods = array();

	public function addMethod( string $name, \Closure $method ): void {
		$this->__dynamicMethods[ $name ] = $method->bindTo( $this, self::class );
	}

	public function __call( string $name, array $arguments ) {
		if ( ! array_key_exists( $name, $this->__dynamicMethods ) ) {
			throw new \BadMethodCallException(
				'Call to undefined method ' . __CLASS__ . "::$name()"
			);
		}
		return $this->__dynamicMethods[ $name ](...$arguments);
	}
}

class fuga {

	use DynamicMethodDeclaration;

	private static $cat       = array( 'RJE-company' ); //デフォルトのカテゴリー
	private static $priority  = 10; //デフォルトの優先度

	//宣言時に実行
	public function __construct() {
		$register_patterns = array(
			array(
				'key'   => 'test_pettern1',
				'title' => 'テストパターンX',
				'order' => $this->priority,
				'cat'   => $this->cat,
				'style' => array(),
			),
			// 'test_pettern2' => array(
			// 	'order' => $this->priority,
			// 	'cat'   => $this->cat,
			// 	'title' => 'テストパターンY',
			// 	'style' => array(),
			// ),
		);
		$this->register_block_pattern_category();
		foreach ( $register_patterns as $properties ) {
			$method_name = 'register_'.$properties['key'];
			// 後からメソッドを追加する
			$this->addMethod(
				$method_name,
				function() {
					$this->rje_register_patterns($properties);
				}
			);
			//フックをかける
			add_action( 'register_petterns', array( $this, $method_name ), $properties['order'] );
		}
	}


	public function register_block_pattern_category() {
		register_block_pattern_category( 'RJE-company', array( 'label' => '[類人猿] 企業サイト' ) );
	}


	//パターン宣言処理
	public static function rje_register_patterns( $args ) {
		$args = array(
				'key'   => 'test_pettern1',
				'title' => 'テストパターンX',
				'order' => 10,
				'cat'   => array( 'RJE-company' ),
				'style' => array(),
		);
		// 例外処理
		// TODO:どこかでエラーでそう、正しい判断のものを書いたほうが良さそう
		if ( !$args['key'] || !$args['cat'] || !$args['title'] ) {
			return;
		}

		// 使用ブロックスタイル定義
		// FIXME: 指定のメソッドがないため動かないと予測
		// foreach ( $use_block_style as $block_style_name ) {
		// 	$this->load_style_handle[ $block_style_name ][] = $pattern_title;
		// }
		// パターン内容を取得
		$contents = '';
		ob_start();
		require_once RJE_PLUGIN_PATH . 'patterns/' . $args['key'] . '/pattern.php';
		$contents = ob_get_contents();
		ob_end_clean();
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

new fuga();