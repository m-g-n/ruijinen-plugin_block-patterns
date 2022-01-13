<?php
/**
 * Plugin name: 類人猿ブロックパターン
 * Description: Snow Monkeyサイトをより素敵にするブロックパターンを提供
 * Version: 0.0.1.5
 * Tested up to: 5.8.1
 * Requires at least: 5.8.1
 * Author: mgn Inc.,
 * Author URI: https://rui-jin-en.com/
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ruijinen-block-patterns
 *
 * @package ruijinen-block-patterns
 * @author mgn Inc.,
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\Master;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * declaration constant.
 */
define( 'RJE_BP_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) . '/' );  // このプラグインのURL.
define( 'RJE_BP_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/' ); // このプラグインのパス.
define( 'RJE_BP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); // このプラグインのベースネーム.
define( 'RJE_BP_TEXTDOMAIN', 'ruijinen-block-patterns' ); //テキストドメイン名.

/**
 * include files.
 */
// require_once(RJE_BP_PLUGIN_PATH . 'vendor/autoload.php'); //アップデート用composer.

//各処理用のクラスを読み込む
foreach (glob(RJE_BP_PLUGIN_PATH.'App/**/*.php') as $filename) {
	require_once $filename;
}

/**
 * 初期設定.
 */
class Bootstrap {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'bootstrap' ] );
		add_action( 'init', [ $this, 'load_textdomain' ] );
	}

	/**
	 * Bootstrap.
	 */
	public function bootstrap() {
		//クラスオブジェクト作成
		new App\Setup\AutoUpdate();
		new App\Setup\DashboardWidget();

		//アクティベートチェックを行い問題がある場合はメッセージを出し離脱する.
		$activate_check = new App\Setup\ActivateCheck();
		if ( !empty( $activate_check->messages ) ) {
			add_action('admin_notices', array( $activate_check,'make_alert_message'));
			return;
		}

		//ヘルパー・サンプルパターンの追加
		new App\Patterns\RegisterHelperPatterns();
		new App\Patterns\RegisterSamplePatterns();

		//パターンの登録
		add_action( 'after_setup_theme', [ $this, 'register_patterns' ], 9999 );
	}

	/**
	 * Load Textdomain.
	 */
	public function load_textdomain() {
		new App\Setup\TextDomain();
	}

	/**
	 * register Helper and Sample Patterns.
	 */
	public function add_patterns() {
		new App\Patterns\RegisterHelperPatterns();
		new App\Patterns\RegisterSamplePatterns();
	}

	/**
	 * Register RJE Block Patterns.
	 */
	public function register_patterns() {
		new \Ruijinen\Pattern\RegisterBlockPatterns();
	}
}

new Bootstrap();
