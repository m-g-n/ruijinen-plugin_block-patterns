<?php
/**
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

/**
 * Snow Monkey および Snow Monkey Blocks が有効化されていない場合はプラグイン自体を有効化しない
 */
add_action( 'admin_init', 'rje_pattern_activate' );
function rje_pattern_activate() {
	$theme = wp_get_theme( get_template() );
	if ( is_admin() && current_user_can( 'activate_plugins' ) && ( ( 'snow-monkey' !== $theme->template && 'snow-monkey/resources' !== $theme->template ) || ( ! is_plugin_active( 'snow-monkey-blocks/snow-monkey-blocks.php' ) ) ) ) {
		add_action( 'admin_notices', 'rje_pattern_notice' );

		deactivate_plugins( RJE_BASENAME );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
}

function rje_pattern_notice() {
	?>
<div class="error">
	<p><?php esc_html_e( '[RUI-JIN-EN Block Patterns] This Plugin must need the premium theme Snow Monkey and a plugin Snow Monkey Blocks.', 'ruijinen-block-patterns-beta' ); ?></p>
</div>
	<?php
}
