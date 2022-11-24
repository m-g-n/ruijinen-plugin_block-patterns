<?php
/**
 * ブロックパターンのオプションページ
 *
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\Master\App\Setup;

class OptionPage {
	/**
	* プロパティ
	*/
	private $page_slug = 'rje_option';
	private $group_name = 'rje_option';
	private $options;

	/**
	* constructor
	*/
	public function __construct() {
		add_action( 'admin_menu', array($this, 'setting_menu') );
		add_action( 'admin_init', array($this, 'set_section') );
	}

	/**
	* セクション・フィールドを設定する
	*/
	public function set_section () {
		$setting_contents = apply_filters( 'rje_option_args', array(), 'args' );
		if ( $setting_contents ) {
			foreach ( $setting_contents as $value ) {
				$section_id = $value['section_id'];
				$fields     = $value['fields'];
				//フィールド値がない場合はこのループは離脱
				if ( !$fields )
					continue;
				//設定項目
				register_setting(
					$this->group_name, //グループ名
					$this->page_slug, //オプション名
					'' //サニタイズ処理したい場合はこちらにメソッド追加
				);
				//セクションを作る
				add_settings_section(
					$section_id, // section ID
					$value['section_name'], // title (optional)
					'', // callback function to display the section (optional)
					$this->page_slug
				);
				foreach ( $fields as $field ) {
					//フィールドを作る
					add_settings_field(
						$field['name'],
						'<label for="'.$field['name'].'">'.$field['label'].'</label>', //フィールドタイトル
						function () use ( $field, $section_id ) {
							printf(
									'<input type="checkbox" id="%s" name="%s" value="%s"%s>',
									$field['name'], //id
									$this->group_name.'['.$section_id.'][]', //name
									$field['name'], //value
									isset( $this->options[$section_id]) && in_array($field['name'], $this->options[$section_id]) ? ' checked' : ''//checked
							);
						},
						$this->page_slug,
						$value['section_id'] // section ID
					);
				}
			}
		}
	}

	/**
	* 設定メニューの設定
	*/
	public function setting_menu(){
		add_options_page(
			'類人猿の設定', //ページタイトル
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
			<form method="post" action="options.php">
				<?php
					settings_fields( $this->group_name ); // settings group name
					do_settings_sections( $this->page_slug ); // just a page slug
					submit_button(); // "Save Changes" button
				?>
			</form>
		</div>
		<?php
	}
}
