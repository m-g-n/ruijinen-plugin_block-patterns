<?php
/**
 * 汎用的なクラス
 *
 * @package ruijinen-block-patterns-002-lp
 * @author mgn
 * @license GPL-2.0+
 */

// TODO：汎用的なクラスというという意味でCOMMONでいいのか検討する
namespace Ruijinen\Pattern\Common;

/**
* 動的にメソッドを追加
* ソース元： https://qiita.com/suin/items/99c95c6b2182b54d9405
*/
trait DynamicMethodDeclaration {
	/**
	 * @var \Closure[]
	 */
	private $__dynamicMethods = array();

	public function addMethod( string $name, \Closure $method ): void {
		var_dump('通った');
		$this->__dynamicMethods[ $name ] = $method->bindTo( $this, self::class );
		var_dump($__dynamicMethods);
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
