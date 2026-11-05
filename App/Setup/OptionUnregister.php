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
		add_action( 'admin_menu', array( $this, 'setting_menu' ) );
		add_action( 'admin_init', array( $this, 'set_section' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_style' ) );
	}

	/**
	* 設定項目（セクション・フィールドの情報）を取得する
	*/
	private function get_setting_contents() {
		$setting_contents = apply_filters( 'rje_option_unregister_args', array(), 'args' );
		return is_array( $setting_contents ) ? $setting_contents : array();
	}

	/**
	* セクション・フィールドを設定する
	*/
	public function set_section() {
		//設定項目
		register_setting(
			$this->group_name, //グループ名
			$this->page_slug, //オプション名
			array(
				'sanitize_callback' => array( $this, 'sanitize' ),
			)
		);

		foreach ( $this->get_setting_contents() as $value ) {
			if ( empty( $value['section_id'] ) || empty( $value['fields'] ) || ! is_array( $value['fields'] ) ) {
				continue;
			}
			$section_id = $value['section_id'];
			$fields     = $value['fields'];

			//fields配列を優先度順に並び替え
			$key = array_column( $fields, 'priority' );
			if ( count( $key ) === count( $fields ) ) {
				array_multisort( $key, SORT_ASC, $fields );
			}

			//セクションを作る
			add_settings_section(
				$section_id, // section ID
				'', // title (optional)
				'', // callback function to display the section (optional)
				$this->page_slug
			);
			//フィールドを作る
			add_settings_field(
				$section_id . '_field',
				isset( $value['section_name'] ) ? esc_html( $value['section_name'] ) : '', //フィールドタイトル
				function () use ( $fields, $section_id ) {
					$options = is_array( $this->options ) ? $this->options : array();
					$checked = isset( $options[ $section_id ] ) && is_array( $options[ $section_id ] ) ? $options[ $section_id ] : array();
					foreach ( $fields as $field ) {
						$id = $section_id . '_' . $field['name'];
						printf(
							'<div class="input_wrap"><input type="checkbox" id="%1$s" name="%2$s" value="%3$s"%4$s class="input_unregister"><label for="%1$s" class="input_label"><span class="input_toggle"></span>%5$s</label></div>',
							esc_attr( $id ), //id, label for
							esc_attr( $this->group_name . '[' . $section_id . '][]' ), //name
							esc_attr( $field['name'] ), //value
							checked( in_array( $field['name'], $checked, true ), true, false ), //checked
							esc_html( $field['label'] ) //label text
						);
					}
				},
				$this->page_slug,
				$section_id // section ID
			);
		}
	}

	/**
	* 設定メニューの設定
	*/
	public function setting_menu() {
		add_options_page(
			'[類人猿] ブロックパターンの無効化（ベータ版）', //ページタイトル
			'類人猿', //メニューのラベル
			'manage_options', //どの権限ユーザに見せるか
			$this->page_slug, //メニューslug名
			array( $this, 'option_page_content' ) //callback
		);
	}

	/**
	* 設定ページのコンテンツ
	*/
	public function option_page_content() {
		$this->options = get_option( $this->page_slug ); // Set class property
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p>各パターンの利用・無効化の切り替えができます（グレーアウトされたパターンは無効になります）</p>
			<form method="post" action="options.php">
				<?php
					settings_fields( $this->group_name ); // settings group name
					do_settings_sections( $this->page_slug ); // just a page slug
					submit_button(); // "Save Changes" button
				?>
			</form>
			<form method="post" action="options.php">
				<input type="hidden" name="<?php echo esc_attr( $this->group_name ); ?>[reset]" value="1">
				<?php
				settings_fields( $this->group_name );
				submit_button( '設定をリセット', 'secondary' );
				?>
			</form>
		</div>
		<?php
	}

	/**
	* 保存値のsanitize処理
	* 登録済みのセクションは登録済みのパターン名のみを保存する
	* 未登録のセクション（無効化中のアドオンなど）は既存の保存値を維持する
	*/
	public function sanitize( $option ) {
		if ( ! is_array( $option ) ) {
			$option = array(); // すべてのチェックを外した場合.
		} elseif ( isset( $option['reset'] ) && '1' === $option['reset'] ) {
			return array();
		}

		$sanitized = array();
		$current   = get_option( $this->page_slug );
		if ( is_array( $current ) ) {
			foreach ( $current as $section_id => $names ) {
				if ( is_string( $section_id ) && preg_match( '/\A[A-Za-z0-9_]+\z/', $section_id ) && is_array( $names ) ) {
					$sanitized[ $section_id ] = array_values( array_filter( $names, 'is_string' ) );
				}
			}
		}

		foreach ( $this->get_setting_contents() as $value ) {
			if ( empty( $value['section_id'] ) || empty( $value['fields'] ) || ! is_array( $value['fields'] ) ) {
				continue;
			}
			$section_id = $value['section_id'];
			unset( $sanitized[ $section_id ] );
			if ( empty( $option[ $section_id ] ) || ! is_array( $option[ $section_id ] ) ) {
				continue;
			}
			$allowed  = array_column( $value['fields'], 'name' );
			$selected = array_values( array_intersect( $allowed, array_filter( $option[ $section_id ], 'is_string' ) ) );
			if ( $selected ) {
				$sanitized[ $section_id ] = $selected;
			}
		}
		return $sanitized;
	}

	/**
	* 設定ページのスタイル読み込み
	*/
	public function enqueue_style( $hook ) {
		$path = 'dist/css/option-unregister.css';
		if ( 'settings_page_' . $this->page_slug !== $hook || ! file_exists( RJE_BP_PLUGIN_PATH . $path ) ) {
			return;
		}
		wp_enqueue_style( 'rje_patterns_unregister', RJE_BP_PLUGIN_URL . $path, array(), filemtime( RJE_BP_PLUGIN_PATH . $path ) );
	}
}
