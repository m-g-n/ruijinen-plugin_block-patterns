<?php
/**
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\Master\App\Setup;

class InPluginUpdateMessage {

	//初期処理
	public function __construct() {
		add_action( 'in_plugin_update_message-' . RJE_BP_PLUGIN_BASENAME, array( $this, 'in_plugin_update_message'), 10, 2 );
	}

	//更新画面のアラートボックスにメッセージを追加
	public function in_plugin_update_message( $data, $response ) {
		$messages = $this->get_the_notice_json( $data['new_version'] );
		if ( $messages ) {
			$message = '<br>'.$messages['message'];
			if ( $messages['url'] ) {
				$message .= '<a href="'.$messages['url'] .'" target="_blank" rel="noopener"> &#62;&#62;詳細を見る</a>';
			}
			echo $message;
		}
	}

	//JSONからデータを取得して必要なメッセージを返す
	private function get_the_notice_json ( $version = NULL ) {
		$url = 'https://rui-jin-en.com/update-notice/' . RJE_BP_KEY . '.json';
		$json = file_get_contents($url);
		if ( !$json )
			return;
		$json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN'); //文字コードをUTF-8に変換
		$arr = json_decode( $json, true ); //JSONを連想配列に変換
		$arr = call_user_func_array("array_merge", $arr); //階層が１つ深いため配列の階層を1つ浅くする
		$value = array_key_exists( $version,  $arr ) ? $arr[$version] : false; //指定のバージョンのキーがあったらメッセージ情報を取得
		return $value;
	}
}
