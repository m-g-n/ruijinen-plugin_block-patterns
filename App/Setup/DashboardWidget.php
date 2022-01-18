<?php
/**
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\Master\App\Setup;

/**
 * ブロックパターンの登録に関する処理のクラス
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
			function() {
				wp_add_dashboard_widget( $this->widget_slug, '類人猿ブロックパターン関連の新着情報', array( $this, 'widget_content' ) );
			}
		);
		//ダッシュボード用のスタイルを設定
		add_action( 'admin_enqueue_scripts', array( $this, 'enuqeue_styles' ) );
	}

	/**
	 * ダッシュボードウィジェットに表示する内容.
	 */
	public function widget_content() {
		global $wp_version;
		$transient = get_transient( $this->widget_slug );
		echo $this->rje_news_rss();
		echo $this->footer_menu();
	}

	/**
	 * お知らせRSSの取得.
	 */
	private function rje_news_rss() {
		include_once(ABSPATH . WPINC . '/feed.php');
		$rss      = fetch_feed( $this->rss_url ); // RSSのURLを指定
		$maxitems = 0;
		if ( !is_wp_error( $rss ) ) {
			$maxitems  = $rss->get_item_quantity( $this->rss_item_num );
			$rss_items = $rss->get_items( 0, $maxitems );
		}
		ob_start();
		?>
		<div class="rss-widget rje-dashboard_widget_news">
			<ul>
				<?php if ( $maxitems == 0 ) : ?>
				<li>お知らせはありません</li>
				<?php else: ?>
				<?php foreach( $rss_items as $item ) : ?>
				<li class="hentry">
					<a href="<?php echo $item->get_permalink(); ?>">
						<span class="entry-title"><?php echo $item->get_title(); ?></span>
					</a>
				</li>
				<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>
		<?php
		$html = ob_get_contents();
		ob_end_clean(); 
		return $html;
	}

	/**
	 * フッターのリンク集.
	 */
	private function footer_menu() {
		ob_start();
		?>
		<div class="rje-dashboard_widget_footer">
			<ul>
				<li><a href="https://rui-jin-en.com" target="_blank"  rel="noopener noreferrer">公式サイト<span class="screen-reader-text">(新しいタブで開く)</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
				<li><a href="https://rui-jin-en.com/news/" target="_blank"  rel="noopener noreferrer">お知らせ/ブログ<span class="screen-reader-text">(新しいタブで開く)</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
			</ul>
		</div>
		<?php
		$html = ob_get_contents();
		ob_end_clean(); 
		return $html;
	}

	/**
	 * styles.
	 */
	function enuqeue_styles() {
		wp_enqueue_style(
			'rje-dashboard-widget',
			RJE_BP_PLUGIN_URL . 'dist/css/dashboard-widget.css',
			array(),
			filemtime( RJE_BP_PLUGIN_PATH . 'dist/css/dashboard-widget.css' )
		);
	}
}