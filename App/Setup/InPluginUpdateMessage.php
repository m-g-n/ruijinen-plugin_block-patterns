<?php
/**
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\Master\App\Setup;

class InPluginUpdateMessage {

	/**
	 * お知らせJSONのキャッシュ時間（秒）.
	 */
	const CACHE_EXPIRATION = 12 * HOUR_IN_SECONDS;

	//初期処理
	public function __construct() {
		add_action( 'in_plugin_update_message-' . RJE_BP_PLUGIN_BASENAME, array( $this, 'in_plugin_update_message' ), 10, 2 );
	}

	/**
	 * 更新画面のアラートボックスにメッセージを追加.
	 *
	 * @param array  $data     プラグインの情報.
	 * @param object $response 更新情報.
	 */
	public function in_plugin_update_message( $data, $response ) {
		if ( empty( $data['new_version'] ) ) {
			return;
		}
		$notice = $this->get_the_notice( (string) $data['new_version'] );
		if ( ! $notice || empty( $notice['message'] ) ) {
			return;
		}
		echo '<br>' . wp_kses_post( $notice['message'] );
		if ( ! empty( $notice['url'] ) ) {
			echo '<a href="' . esc_url( $notice['url'] ) . '" target="_blank" rel="noopener"> &#62;&#62;詳細を見る</a>';
		}
	}

	/**
	 * JSONから指定のバージョンのメッセージ情報を返す.
	 *
	 * @param string $version バージョン.
	 * @return array|false
	 */
	private function get_the_notice( $version ) {
		$notices = $this->get_notices();
		return isset( $notices[ $version ] ) && is_array( $notices[ $version ] ) ? $notices[ $version ] : false;
	}

	/**
	 * お知らせJSONを取得してバージョンをキーにした配列で返す（結果は一定時間キャッシュする）.
	 *
	 * @return array
	 */
	private function get_notices() {
		$transient = 'rje_bp_update_notice_' . strtolower( RJE_BP_KEY );
		$cached    = get_transient( $transient );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$notices  = array();
		$response = wp_remote_get(
			'https://rui-jin-en.com/update-notice/' . RJE_BP_KEY . '.json',
			array( 'timeout' => 5 )
		);
		if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
			$json = wp_remote_retrieve_body( $response );
			if ( function_exists( 'mb_convert_encoding' ) ) {
				$json = mb_convert_encoding( $json, 'UTF-8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN' ); //文字コードをUTF-8に変換
			}
			$items = json_decode( $json, true );
			// [ { "バージョン": { "message": "", "url": "" } }, ... ] の形式のため、階層を1つ浅くする.
			if ( is_array( $items ) ) {
				foreach ( $items as $item ) {
					if ( ! is_array( $item ) ) {
						continue;
					}
					foreach ( $item as $item_version => $notice ) {
						$notices[ (string) $item_version ] = $notice;
					}
				}
			}
		}

		// 取得に失敗した場合も空配列をキャッシュし、プラグイン画面の表示ごとにリクエストしないようにする.
		set_transient( $transient, $notices, self::CACHE_EXPIRATION );
		return $notices;
	}
}
