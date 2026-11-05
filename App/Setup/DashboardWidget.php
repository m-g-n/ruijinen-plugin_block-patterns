<?php
/**
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\Master\App\Setup;

/**
 * ダッシュボードウィジェットの登録に関する処理のクラス
 */
class DashboardWidget {

	/**
	 * Propaties.
	 */
	private $widget_slug  = 'rje-block-pattens-dashboard';
	private $rss_url      = 'https://rui-jin-en.com/tag/block-pettern-plugin/feed/';
	private $rss_item_num = 5;

	/**
	 * Constructor.
	 */
	public function __construct() {
		//ダッシュボードを追加.
		add_action(
			'wp_dashboard_setup',
			function () {
				wp_add_dashboard_widget( $this->widget_slug, '類人猿ブロックパターン関連の新着情報', array( $this, 'widget_content' ) );
			}
		);
		//ダッシュボード用のスタイルを設定
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * ダッシュボードウィジェットに表示する内容.
	 */
	public function widget_content() {
		$this->rje_news_rss();
		$this->footer_menu();
	}

	/**
	 * お知らせRSSの表示.
	 */
	private function rje_news_rss() {
		include_once ABSPATH . WPINC . '/feed.php';
		$rss       = fetch_feed( $this->rss_url ); // RSSのURLを指定
		$rss_items = array();
		if ( ! is_wp_error( $rss ) ) {
			$rss_items = $rss->get_items( 0, $rss->get_item_quantity( $this->rss_item_num ) );
		}
		?>
		<div class="rss-widget rje-dashboard_widget_news">
			<ul>
				<?php if ( empty( $rss_items ) ) : ?>
				<li>お知らせはありません</li>
				<?php else : ?>
					<?php foreach ( $rss_items as $item ) : ?>
				<li class="hentry">
					<a href="<?php echo esc_url( $item->get_permalink() ); ?>">
						<span class="entry-title"><?php echo esc_html( wp_strip_all_tags( $item->get_title() ) ); ?></span>
					</a>
				</li>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * フッターのリンク集.
	 */
	private function footer_menu() {
		?>
		<div class="rje-dashboard_widget_footer">
			<ul>
				<li><a href="https://rui-jin-en.com" target="_blank" rel="noopener noreferrer">公式サイト<span class="screen-reader-text">(新しいタブで開く)</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
				<li><a href="https://rui-jin-en.com/news/" target="_blank" rel="noopener noreferrer">お知らせ/ブログ<span class="screen-reader-text">(新しいタブで開く)</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
			</ul>
		</div>
		<?php
	}

	/**
	 * ダッシュボード用のスタイル（ダッシュボード画面のみ読み込む）.
	 *
	 * @param string $hook_suffix 現在の管理画面のフック名.
	 */
	public function enqueue_styles( $hook_suffix ) {
		$path = 'dist/css/dashboard-widget.css';
		if ( 'index.php' !== $hook_suffix || ! file_exists( RJE_BP_PLUGIN_PATH . $path ) ) {
			return;
		}
		wp_enqueue_style(
			'rje-dashboard-widget',
			RJE_BP_PLUGIN_URL . $path,
			array(),
			filemtime( RJE_BP_PLUGIN_PATH . $path )
		);
	}
}
