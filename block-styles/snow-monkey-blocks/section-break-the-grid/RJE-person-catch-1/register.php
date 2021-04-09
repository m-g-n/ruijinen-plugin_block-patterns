<?php
/**
 * @package ruijinen-block-patterns-beta
 * @author mgn
 * @license GPL-2.0+
 */

$override_block_name = 'snow-monkey-blocks/section-break-the-grid';
$block_style_label   = '類人猿 人物キャッチ1';


register_block_style(
	$override_block_name,
	array(
		'name'  => basename( __DIR__ ),
		'label' => $block_style_label,
	)
);
wp_register_style( basename( __DIR__ ) . '-front', plugins_url( '', __FILE__ ) . '/style.css', $this->style_front_deps, filemtime( plugin_dir_path( __FILE__ ) . 'style.css' ) );
wp_register_style( basename( __DIR__ ) . '-editor', plugins_url( '', __FILE__ ) . '/editor.css', $this->style_editor_deps, filemtime( plugin_dir_path( __FILE__ ) . 'editor.css' ) );
