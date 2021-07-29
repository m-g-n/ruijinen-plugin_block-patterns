<?php
/**
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

$override_block_name = 'snow-monkey-blocks/panels';
$block_style_label   = '類人猿R002_LP 流れ・手順';
$basename = basename( __DIR__ );
$front_filename  = 'dist/css/block-styles/' . $override_block_name . '/' . $basename . '/style-front.css';
$editor_filename = 'dist/css/block-styles/' . $override_block_name . '/' . $basename . '/style-editor.css';

$front_filetime = ( file_exists( RJE_R002LP_PATH . $front_filename ) ) ? filemtime( RJE_R002LP_PATH . $front_filename ) : NULL;
$editor_filetime = ( file_exists( RJE_R002LP_PATH . $editor_filename ) ) ? filemtime( RJE_R002LP_PATH . $editor_filename ) : NULL;

//ファイルパス（プラグインのルートから相対）
register_block_style(
	$override_block_name,
	array(
		'name'  => $basename,
		'label' => $block_style_label,
	)
);

//フロント・エディター用のCSSファイルを登録
wp_register_style( 'is-style-' . $basename . '-front', RJE_R002LP_URL . $front_filename, $this->style_front_deps, $front_filetime );
wp_register_style( 'is-style-' . $basename . '-editor', RJE_R002LP_URL . $editor_filename, $this->style_editor_deps, $editor_filetime );