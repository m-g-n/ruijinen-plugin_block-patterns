<?php
/**
 * ブロックパターン無効化のオプションページ
 *
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\Master\App\Setup;

class OptionUnregister {
	/**
	* プロパティ
	*/
	private $page_slug  = 'rje_patterns_unregister';
	private $group_name = 'rje_patterns_unregister';
	private $options;

	/**
	* constructor
	*/
	public function __construct() {
		add_action( 'admin_menu', array($this, 'setting_menu') );
		add_action( 'admin_init', array($this, 'set_section') );
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_style') );
	}

	/**
	* セクション・フィールドを設定する
	*/
	public function set_section () {
		$setting_contents = apply_filters( 'rje_option_unregister_args', array(), 'args' );
		if ( $setting_contents ) {
			foreach ( $setting_contents as $value ) {
				$section_id = $value['section_id'];
				$fields     = $value['fields'];

				//フィールド値がない場合はこのループは離脱
				if ( !$fields ) { continue; }

				//設定項目
				register_setting(
					$this->group_name, //グループ名
					$this->page_slug, //オプション名
					array( $this, 'sanitize') //サニタイズ処理したい場合はこちらにメソッド追加
				);
				//セクションを作る
				add_settings_section(
					$section_id, // section ID
					'', // title (optional)
					'', // callback function to display the section (optional)
					$this->page_slug
				);
				//フィールドを作る
				add_settings_field(
					$section_id.'_field',
					$value['section_name'], //フィールドタイトル
					function () use ( $fields, $section_id ) {
						foreach ( $fields as $field ) {
							printf(
								'<div class="input_wrap"><input type="checkbox" id="%s" name="%s" value="%s"%s class="input_unregister"><label for="%s" class="input_label"><span class="input_toggle"></span>%s</label></div>',
								$section_id . '_' . $field['name'], //id
								$this->group_name.'['.$section_id.'][]', //name
								$field['name'], //value
								isset( $this->options[$section_id] ) && in_array($field['name'], $this->options[$section_id]) ? ' checked' : '', //checked
								$section_id . '_' . $field['name'], //label for
								$field['label'] //label text
							);
						}
					},
					$this->page_slug,
					$value['section_id'] // section ID
				);
			}
		}
	}

	/**
	* 設定メニューの設定
	*/
	public function setting_menu(){
		add_options_page(
			'[類人猿] ブロックパターンの無効化（ベータ版）', //ページタイトル
			'類人猿', //メニューのラベル
			'manage_options', //どの権限ユーザに見せるか
			$this->page_slug, //メニューslug名
			array( $this, 'option_page_content'), //callback
		);
	}

	/**
	* 設定ページのコンテンツ
	*/
	public function option_page_content(){
		$this->options = get_option( $this->page_slug ); // Set class property
		?>
		<div class="wrap">
			<h1><?php echo get_admin_page_title() ?></h1>
			<p>各パターンの利用・無効化の切り替えができます（グレーアウトされたパターンは無効になります）</p>
			<form method="post" action="options.php">
				<?php
					settings_fields( $this->group_name ); // settings group name
					do_settings_sections( $this->page_slug ); // just a page slug
					submit_button(); // "Save Changes" button
				?>
			</form>
			<form method="post" action="options.php">
						<input type="hidden" name="<?php echo $this->group_name; ?>[reset]" value="1">
						<?php
						settings_fields( $this->group_name );
						submit_button('設定をリセット', 'secondary');
						?>
					</form>
		</div>
		<?php
	}

	/**
	* 保存値のsanitize処理など
	*/
	public function sanitize ( $option ) {
		if ( isset( $option['reset'] ) && '1' === $option['reset'] ) {
			return array();
		} else {
			return $option;
		}
	}

	/**
	* 設定ページのスタイル読み込み
	*/
	function enqueue_style ( $hook ) {
    if( 'settings_page_'.$this->page_slug != $hook ) { return; }
    wp_enqueue_style('rje_patterns_unregister', RJE_BP_PLUGIN_URL.'dist/css/option-unregister.css', array(), filemtime( RJE_BP_PLUGIN_PATH . 'dist/css/option-unregister.css' ));
	}
}
