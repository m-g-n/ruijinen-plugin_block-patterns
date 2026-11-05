/**
 * E2E テストの共通処理.
 */
const fs = require("fs");
const path = require("path");
const { expect } = require("@wordpress/e2e-test-utils-playwright");

const ROOT = path.join(__dirname, "../..");
const PATTERN_PREFIX = "RJE-pattern/";

/**
 * パターン登録クラスの $methods から、パターンの一覧を取得する（パターン追加時に自動で対象になる）.
 *
 * @param {string} file     登録クラスのファイル（プラグインのルートからの相対パス）.
 * @param {string} keyBase  パターンのキーの接頭辞（例: RJE_R000HELPER）.
 * @param {string} section  オプションページのセクションID（グローバル変数名）.
 * @param {(name: string) => string} pageSlug パターン名から確認用の固定ページのスラッグを返す.
 */
function readPatterns(file, keyBase, section, pageSlug) {
	const php = fs.readFileSync(path.join(ROOT, file), "utf8");
	return [
		...php.matchAll(
			/'name'\s*=>\s*'([a-z0-9_]+)',\s*'label'\s*=>\s*'([^']+)'/g,
		),
	].map(([, name, title]) => ({
		name,
		title,
		section,
		key: `${keyBase}_${name}`,
		patternName: `${PATTERN_PREFIX}${keyBase}_${name}`,
		pageSlug: pageSlug(name),
	}));
}

const PATTERNS = [
	...readPatterns(
		"App/Patterns/RegisterHelperPattern.php",
		"RJE_R000HELPER",
		"rje_r000helper_patterns",
		(name) => `rje_r000helper_${name}`,
	),
	// サンプルパターンの確認用ページは LP向けパターン集のスラッグを使っている
	...readPatterns(
		"App/Patterns/RegisterSamplePattern.php",
		"RJE_R000SAMPLE",
		"rje_r000sample_patterns",
		(name) => `rje_r002lp_${name}`,
	),
];

/**
 * 登録済みの RJE のブロックパターン（REST API）を返す.
 */
async function getRegisteredPatterns(requestUtils) {
	const patterns = await requestUtils.rest({
		path: "/wp/v2/block-patterns/patterns",
	});
	return patterns.filter((p) => p.name.startsWith(PATTERN_PREFIX));
}

/**
 * エディターを開き、ブロックの読み込みが終わるまで待つ.
 */
async function openEditor(admin, page, postId) {
	await admin.editPost(postId);
	await page.waitForFunction(
		() => window.wp?.data?.select("core/block-editor").getBlocks().length > 0,
	);
}

/**
 * エディターのキャンバス（iframe の場合はその中）を返す.
 */
async function getCanvas(page) {
	const iframe = page.locator('iframe[name="editor-canvas"]');
	return (await iframe.count())
		? page.frameLocator('iframe[name="editor-canvas"]')
		: page;
}

/**
 * 全ブロックの検証結果を返す.
 */
function getBlockReport(page) {
	return page.evaluate(() => {
		const { select } = window.wp.data;
		const walk = (blocks, parent = []) =>
			blocks.flatMap((block, i) => {
				const at = [...parent, i];
				return [{ block, at: at.join(".") }, ...walk(block.innerBlocks, at)];
			});
		const all = walk(select("core/block-editor").getBlocks());

		// RJE のブロックスタイルが、そのブロックに登録されているか
		const unregisteredStyles = all.flatMap(({ block, at }) =>
			(block.attributes.className || "")
				.split(/\s+/)
				.filter((c) => c.startsWith("is-style-RJE_"))
				.map((c) => c.replace(/^is-style-/, ""))
				.filter(
					(style) =>
						!select("core/blocks")
							.getBlockStyles(block.name)
							.some((s) => s.name === style),
				)
				.map((style) => `${block.name} @${at}: ${style}`),
		);

		// 現在の save 関数の出力と、元のマークアップを比較する
		const normalize = (html) =>
			html.replace(/>\s+</g, "><").replace(/\s+/g, " ").trim();
		const outdated = all
			.filter(
				({ block }) =>
					block.isValid &&
					typeof block.originalContent === "string" &&
					normalize(
						window.wp.blocks.getSaveContent(block.name, block.attributes),
					) !== normalize(block.originalContent),
			)
			.map(({ block }) => block.name);

		return {
			count: all.length,
			invalid: all
				.filter(({ block }) => !block.isValid)
				.map(({ block, at }) => `${block.name} @${at}`),
			missing: all
				.filter(({ block }) => block.name === "core/missing")
				.map(({ block, at }) => `${block.attributes.originalName} @${at}`),
			unregisteredStyles,
			// 旧形式のマークアップ（deprecation で読み込めているが、保存すると新形式に変わるブロック）
			outdated: [...new Set(outdated)],
		};
	});
}

/**
 * ブロックの検証結果を確認する（問題があればテスト失敗）.
 */
function expectValidBlocks(report, when) {
	expect(
		report.count,
		`${when}: ブロックが読み込まれていること`,
	).toBeGreaterThan(0);
	expect(report.invalid, `${when}: ブロック検証エラーがないこと`).toEqual([]);
	expect(report.missing, `${when}: 未登録ブロックがないこと`).toEqual([]);
	expect(
		report.unregisteredStyles,
		`${when}: ブロックスタイルが登録されていること`,
	).toEqual([]);
}

/**
 * 保存し、成功したことを確認する.
 */
async function savePost(page) {
	const succeeded = await page.evaluate(async () => {
		await window.wp.data.dispatch("core/editor").savePost();
		return window.wp.data.select("core/editor").didPostSaveRequestSucceed();
	});
	expect(succeeded, "下書きの保存に成功すること").toBe(true);
}

/**
 * 固定ページを完全に削除する.
 */
async function deletePage(requestUtils, postId) {
	if (!postId) {
		return;
	}
	await requestUtils.rest({
		method: "DELETE",
		path: `/wp/v2/pages/${postId}`,
		params: { force: true },
	});
}

module.exports = {
	ROOT,
	PATTERNS,
	getRegisteredPatterns,
	openEditor,
	getCanvas,
	getBlockReport,
	expectValidBlocks,
	savePost,
	deletePage,
};
