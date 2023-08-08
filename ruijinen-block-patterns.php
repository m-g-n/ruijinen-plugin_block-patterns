<?php
/**
 * Plugin name: 類人猿ブロックパターン
 * Description: Snow Monkeyサイトをより素敵にするブロックパターンを提供
 * Version: 3.1.1
 * Tested up to: 6.2.2
 * Requires at least: 6.2
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
define( 'RJE_BP_KEY', 'RJE_R000MASTER' ); // どの類人猿プロダクトなのかを示すキー
define( 'RJE_BP_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) . '/' );  // このプラグインのURL.
define( 'RJE_BP_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/' ); // このプラグインのパス.
define( 'RJE_BP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); // このプラグインのベースネーム.
define( 'RJE_BP_TEXTDOMAIN', 'ruijinen-block-patterns' ); //テキストドメイン名.
define( 'RJE_BP_DIRNAME', basename(__DIR__) ); //このディレクトリーのパス.

/**
 * include files.
 */
require_once(RJE_BP_PLUGIN_PATH . 'vendor/autoload.php'); //アップデート用composer.

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
		//初期実行
		new App\Setup\AutoUpdate(); //自動更新確認
		new App\Setup\DashboardWidget(); //ダッシュボードウィジェット
		new App\Setup\InPluginUpdateMessage(); //更新アラートメッセージに追加でメッセージを表示

		//アクティベートチェックを行い問題がある場合はメッセージを出し離脱する.
		$activate_check = new App\Setup\ActivateCheck();
		if ( !empty( $activate_check->messages ) ) {
			add_action('admin_notices', array( $activate_check,'make_alert_message'));
		}
		if ( true === $activate_check->inactivate ) {
			return;
		}

		//アクティベートテーマのチェック
		$this->get_activate_theme();

		//パターン情報の削除（remove_filter）
		new App\Patterns\RemoveBlockPatterns();

		//オプションページ作成
		new App\Setup\OptionUnregister();

		//パターンの登録
		add_theme_support( 'editor-styles' );
		$this->add_patterns();
		add_action( 'after_setup_theme', [ $this, 'unregister_patterns' ], 100 );
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
		//ヘルパー・サンプルパターンの追加
		global $rje_r000helper_patterns, $rje_r000sample_patterns;
		$rje_r000helper_patterns = new App\Patterns\RegisterHelperPatterns('rje_r000helper_patterns');
		$rje_r000sample_patterns = new App\Patterns\RegisterSamplePatterns('rje_r000sample_patterns');
	}

	/**
	 * unregister RJE Block Patterns.
	 */
	public function unregister_patterns() {
		new App\Patterns\RemoveBlockPatterns();
	}

	/**
	 * Register RJE Block Patterns.
	 */
	public function register_patterns() {
		new \Ruijinen\Pattern\RegisterBlockPatterns();
	}

	/**
	 * Get Activate Themes.
	 */
	public function get_activate_theme() {
		if ( defined ('RJE_ACTIVATE_THEME') ) { return; } //アクティブテーマ用の定数が存在する場合は離脱
		$theme = wp_get_theme( get_template() );
		if ( 'snow-monkey/resources' === $theme->template ) { //古いバージョンのSnow Monkey対応
			$name = 'snow-monkey';
		} else {
			$name = $theme->template;
		}
		define( 'RJE_ACTIVATE_THEME', $name );
	}
}

new Bootstrap();
