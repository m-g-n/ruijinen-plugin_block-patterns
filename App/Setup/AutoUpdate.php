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
				'description_url' => 'https://rui-jin-en.com/block_patterns/',
				'faq_url' => 'https://rui-jin-en.com/help/',
				'changelog_url' => 'https://rui-jin-en.com/category/product-renew/',
				'icons' => [
				// 'svg' => '', // svg URL. Square recommended
				'1x' => 'https://rui-jin-en.com/wp-content/uploads/2022/02/icon-64x64-1.png', // Image URL 64×64
				'2x' => 'https://rui-jin-en.com/wp-content/uploads/2022/02/icon-128x128-1.png', // Image URL 128×128
				],
				// 'banners' => [
				// 'low' => '', // Image URL 772×250
				// 'high' => '', // Image URL 1554×500
				// ],
				'tested' => '6.7', // Tested up WordPress version
				'requires_php' => '7.4.0', // Requires PHP version
				'requires' => '6.3', // Requires WordPress version
			]
		);
	}
}
