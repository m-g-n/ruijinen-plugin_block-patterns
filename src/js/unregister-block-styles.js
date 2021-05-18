/**
* 類人猿のブロックスタイルをデフォルトで読み込まないようにする
*/

//TODO：デバッグ用
wp.domReady(() => {
	// find blocks styles
	wp.blocks.getBlockTypes().forEach((block) => {
		if (_.isArray(block['styles'])) {
			console.log(block.name, _.pluck(block['styles'], 'name'));
		}
	});
});