<?php
/**
 * @package ruijinen-block-patterns-beta
 * @author mgn
 * @license GPL-2.0+
 */

class RJE_load_register_block {

	public $load_style_handle = '';
	public $style_front_deps  = '';
	public $style_editor_deps = '';

	public function __construct() {

		$this->load_style_handle = array();
		$this->style_front_deps  = array( 'snow-monkey', 'snow-monkey-blocks', 'snow-monkey-snow-monkey-blocks', 'snow-monkey-blocks-background-parallax' );
		$this->style_editor_deps = array( 'snow-monkey-snow-monkey-blocks-editor' );

		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	public function init() {
		add_action( 'init', array( $this, 'register_block_pattern_category' ), 10 );
		add_action( 'init', array( $this, 'register_block_pattern' ), 15 );
		add_action( 'init', array( $this, 'register_block_style' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style_front' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_style_editor' ) );
	}

	public function register_block_pattern_category() {
		register_block_pattern_category( 'RJE-company', array( 'label' => '[類人猿] 企業サイト' ) );
	}

	public function register_block_pattern() {
		foreach ( glob( RJE_PLUGIN_PATH . 'patterns/*/register.php' ) as $file ) {
			require_once $file;
		}
	}

	public function register_block_style() {
		foreach ( $this->load_style_handle as $handle => $use_patterns ) {
			foreach ( glob( RJE_PLUGIN_PATH . 'block-styles/*/*/' . $handle . '/register.php' ) as $file ) {
				require_once $file;
			}
		}
	}

	public function enqueue_style_front() {
		foreach ( $this->load_style_handle as $handle => $use_patterns ) {
			wp_enqueue_style( $handle . '-front' );
		}
	}

	public function enqueue_style_editor() {
		foreach ( $this->load_style_handle as $handle => $use_patterns ) {
			wp_enqueue_style( $handle . '-editor' );
		}
	}

}
new RJE_load_register_block();
