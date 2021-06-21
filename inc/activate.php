<?php
/**
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\Master;

class PluginActivate {

	//初期処理
	public function __construct() {
		add_action( 'admin_init',  array( $this, 'this_plugin_activate' ) );
	}

	//アクティベートチェック
	public function this_plugin_activate() {
		if ( is_admin() && current_user_can( 'activate_plugins' ) ) {
			$noactivate_message = NULL; //アクティベートできなかった場合のメッセージ出力関数名
			if ( $this->check_snow_monkey_activate() ) {
				$noactivate_message = 'alert_notice_snow_monkey';
			// } elseif ( $this->check_rje_petterns_bata_activate() ) {
			// 	$noactivate_message = 'alert_notice_petterns_bata';
			}
			if ( $noactivate_message != NULL ) {
				deactivate_plugins( RJE_BP_PLUGIN_BASENAME );
				add_action( 'admin_notices', array ( $this, $noactivate_message ) );
				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
			}
		}
	}

	//必要な Snow Monkeyパッケージのアクティベートチェック
	public function check_snow_monkey_activate () {
		$theme = wp_get_theme( get_template() );
		if ( ( 'snow-monkey' !== $theme->template && 'snow-monkey/resources' !== $theme->template ) || !is_plugin_active( 'snow-monkey-blocks/snow-monkey-blocks.php' ) ) {
				return TRUE;
		}
	}

	//ベータ版の類人猿のアクティべートチェック
	public function check_rje_petterns_bata_activate () {
		if ( is_plugin_active( 'ruijinen-block-patterns-beta/ruijinen-block-patterns-beta.php' ) ) {
				return TRUE;
		}
	}

	//必要なSnow Monkeyパッケージがアクティベートされてない場合のエラーメッセージ
	public function alert_notice_snow_monkey() {
		?>
	<div class="error">
		<p><?php esc_html_e( '[RUI-JIN-EN Block Patterns] This Plugin must need the premium theme Snow Monkey and a plugin Snow Monkey Blocks.', 'ruijinen-block-patterns' ); ?></p>
	</div>
		<?php
	}

	//類人猿パターンプラグインベータ版がアクティベートされてた場合のエラーメッセージ
	public function alert_notice_petterns_bata() {
		?>
	<div class="error">
		<p><?php esc_html_e( '[RUI-JIN-EN Block Patterns] Sorry. This Plugin Cannot be used with the beta version.', 'ruijinen-block-patterns' ); ?></p>
	</div>
		<?php
	}

}

new PluginActivate();