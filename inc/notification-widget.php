<?php
/**
 * @package ruijinen-block-patterns-beta
 * @author mgn
 * @license GPL-2.0+
 */

/**
 * ダッシュボードに通知用のウィジェットを追加
 */
add_action( 'wp_dashboard_setup', 'rje_notification_widget' );
// ダッシュボードにウィジェットを追加する
function rje_notification_widget() {
	wp_add_dashboard_widget( 'rje-notification-widget', __( 'RUI-JIN-EN Block Patterns Beta', 'ruijinen-block-patterns-beta' ), 'rje_notification_content' );
}

// ウィジェットへ出力する内容
function rje_notification_content() {
	global $wp_version;

	$transient = get_transient( 'rje-notification-widget' );

	// WP_DEBUG が true か $transient がなかったら
	if ( WP_DEBUG || ! $transient ) {
		$wp_api_posts = wp_remote_get(
			'https://rui-jin-en.com/wp-json/custom/v1/info'
		);

		if ( is_wp_error( $wp_api_posts ) || 200 !== $wp_api_posts['response']['code'] ) {
			$wp_api_posts = [];
		} else {
			$wp_api_posts = json_decode( $wp_api_posts['body'] );
		}

		set_transient( 'rje-notification-widget', $wp_api_posts, 60 * 60 );
	} else {
		$wp_api_posts = $transient;
	}

	if ( ! $wp_api_posts ) {
		return;
	}

	$posts_data = [];

	$i = 0;
	foreach ( $wp_api_posts as $item ) {
		if ( 0 === $i ) {
			$meta_data = $item->info;
		} else {
			$posts_data[] = $item;
		}
		$i++;
	}
	?>
	<div class="wordpress-news hide-if-no-js">
		<div class="rss-widget">
			<h3><?php esc_html_e( __( 'Information', 'ruijinen-block-patterns-beta' ) ); ?></h3>
	<?php if ( ! empty( $meta_data ) ) : ?>
			<div class="rje-nortification-text"><?php echo wp_kses_post( $meta_data ); ?></div>
	<?php endif; ?>
			<ul id="rje-notification-widget">
				<?php foreach ( $posts_data as $item ) : ?>
					<li><a href="<?php echo esc_url( $item->link ); ?>" target="_blank" rel="noreferrer"><?php echo esc_html( $item->title->rendered ); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<ul class="rje-widget-btn-area">
				<li><a href="https://rui-jin-en.com/" class="rje-widget-btn button-primary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( __( 'Official Site', 'ruijinen-block-patterns-beta' ) ); ?></a></li>
<?php /* // todo: 準備できたらコメントアウトを外す
				<li><a href="" class="rje-widget-btn button-primary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( __( 'How to use', 'ruijinen-block-patterns-beta' ) ); ?></a></li>
*/ ?>
				<li><a href="https://rui-jin-en.com/faq/" class="rje-widget-btn button-primary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( __( 'FAQ', 'ruijinen-block-patterns-beta' ) ); ?></a></li>
			</ul>
		</div>
	</div>
	<?php
}

// ウィジェット向けのスタイルを追加
function enuqeue_notification_widget_style() {
	wp_enqueue_style(
		'notification-widget',
		RJE_PLUGIN_URL . '/src/css/notification-widget.css',
		array(),
		filemtime( RJE_PLUGIN_PATH . '/src/css/notification-widget.css' )
	);
}
add_action( 'admin_enqueue_scripts', 'enuqeue_notification_widget_style' );
