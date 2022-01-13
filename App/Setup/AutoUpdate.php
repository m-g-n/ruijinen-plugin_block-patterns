<?php
/**
 * Activate auto update using GitHub 自動アップデートの参照先の設定
 *
 * @author mgn
 * @license GPL-2.0+
 * @package ruijinen
 */

namespace Ruijinen\Pattern\Master\App\Setup;

use Inc2734\WP_GitHub_Plugin_Updater\Bootstrap as Updater;

/**
 * アップデートの有無の検知及び実施
 */
class AutoUpdate {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'activate_autoupdate' ) );
	}

	/**
	 * Activate auto update using GitHub,
	 *
	 * @return void
	 */
	public function activate_autoupdate() {
		new Updater(
			RJE_BP_PLUGIN_BASENAME,
			'm-g-n',
			'ruijinen-plugin_block-patterns',
			[
				'homepage' => 'https://rui-jin-en.com',
			]
		);
	}
}
