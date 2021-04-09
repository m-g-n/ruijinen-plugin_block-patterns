<?php
/**
 * Activate auto update using GitHub 自動アップデートの参照先の設定
 *
 * @author mgn
 * @license GPL-2.0+
 * @package ruijinen
 */

use Inc2734\WP_GitHub_Plugin_Updater\Bootstrap as Updater;

/**
 * アップデートの有無の検知及び実施
 */
class RJEAutoUpdate {
	/**
	 * 必ず実施する項目として_plugins_loadedを実施
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, '_plugins_loaded' ) );
	}
	/**
	 * 実施する項目
	 */
	public function _plugins_loaded() {
		// アップデート通知機能の読み込みによる、アップデートの有無の確認.
		add_action( 'init', array( $this, '_activate_autoupdate' ) );
	}

	/**
	 * Activate auto update using GitHub 自動アップデートの参照先の設定
	 *
	 * @return void
	 */
	public function _activate_autoupdate() {
		new Updater(
			RJE_BASENAME,
			'm-g-n',
			'ruijinen-block-patterns-beta'
		);
	}
}

new RJEAutoUpdate();
