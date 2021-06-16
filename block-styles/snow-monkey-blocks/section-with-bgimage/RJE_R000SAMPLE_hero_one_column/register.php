<?php
/**
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

$override_block_name = 'snow-monkey-blocks/section-with-bgimage';
$block_style_label   = '類人猿R000 SAMPLEHero（1カラム)';
$basename = basename( __DIR__ );
$front_filename  = 'dist/css/block-styles/' . $override_block_name . '/' . $basename . '/style-front.css';
$editor_filename = 'dist/css/block-styles/' . $override_block_name . '/' . $basename . '/style-editor.css';

//ブロックスタイル登録
register_block_style(
	$override_block_name,
	array(
		'name'  => $basename,
		'label' => $block_style_label,
	)
);

//フロント・エディター用のCSSファイルを登録
wp_register_style( 'is-style-' . $basename . '-front', RJE_BP_PLUGIN_URL . $front_filename, $this->style_front_deps, filemtime( RJE_BP_PLUGIN_PATH . $front_filename ) );
wp_register_style( 'is-style-' . $basename . '-editor', RJE_BP_PLUGIN_URL . $editor_filename, $this->style_editor_deps, filemtime( RJE_BP_PLUGIN_PATH . $editor_filename ) );