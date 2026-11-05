<?php
/**
 * プラグイン更新時にプラグインのディレクトリー名を維持する
 *
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\Master\App\Setup;

/**
 * 更新パッケージ（zip）の展開結果のディレクトリー名を、現在のプラグインのディレクトリー名に揃える.
 *
 * WordPress は zip 直下が単一ディレクトリーの場合のみそのディレクトリー名を使い、
 * それ以外（直下にファイルを平置き、別名のディレクトリー）の場合は展開先の名前がそのまま
 * プラグインのディレクトリー名になる。ディレクトリー名が変わるとプラグインのベースネームが変わり、
 * 有効化状態が外れるなどの不具合が起きるため、更新時に元のディレクトリー名へ揃える。
 */
class KeepPluginDirectory {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'upgrader_source_selection', array( $this, 'keep_directory_name' ), 10, 4 );
	}

	/**
	 * 展開されたソースのディレクトリー名を現在のプラグインのディレクトリー名に揃える.
	 *
	 * @param string|\WP_Error $source        展開されたソースのパス.
	 * @param string           $remote_source 展開先の作業ディレクトリーのパス.
	 * @param \WP_Upgrader     $upgrader      WP_Upgrader インスタンス.
	 * @param array            $hook_extra    追加情報.
	 * @return string|\WP_Error
	 */
	public function keep_directory_name( $source, $remote_source, $upgrader, $hook_extra = array() ) {
		global $wp_filesystem;

		if ( is_wp_error( $source ) || ! $wp_filesystem ) {
			return $source;
		}

		// このプラグインの更新以外は対象外.
		if ( ! is_array( $hook_extra ) || empty( $hook_extra['plugin'] ) || RJE_BP_PLUGIN_BASENAME !== $hook_extra['plugin'] ) {
			return $source;
		}

		$dirname = dirname( RJE_BP_PLUGIN_BASENAME );
		if ( '.' === $dirname || '' === $dirname ) {
			return $source;
		}

		$source        = trailingslashit( $source );
		$remote_source = trailingslashit( $remote_source );

		// 既に同じディレクトリー名であれば何もしない.
		if ( basename( $source ) === $dirname ) {
			return $source;
		}

		$new_source = $remote_source . $dirname . '/';

		// zip 直下にファイルが平置きされている場合は、作業ディレクトリー内に作ったディレクトリーへ移動する.
		if ( $source === $remote_source ) {
			$entries = $wp_filesystem->dirlist( $remote_source );
			if ( ! is_array( $entries ) || ! $wp_filesystem->mkdir( $new_source, FS_CHMOD_DIR ) ) {
				return $this->error();
			}
			foreach ( array_keys( $entries ) as $entry ) {
				if ( ! $wp_filesystem->move( $remote_source . $entry, $new_source . $entry ) ) {
					return $this->error();
				}
			}
			return $new_source;
		}

		// 別名のディレクトリーの場合はリネームする.
		if ( $wp_filesystem->exists( $new_source ) || ! $wp_filesystem->move( $source, $new_source ) ) {
			return $this->error();
		}
		return $new_source;
	}

	/**
	 * ディレクトリー名を揃えられなかった場合のエラー.
	 *
	 * @return \WP_Error
	 */
	private function error() {
		return new \WP_Error(
			'rje_bp_keep_plugin_directory',
			'[類人猿ブロックパターン] 更新ファイルのディレクトリー名を変更できなかったため、更新を中止しました。'
		);
	}
}
