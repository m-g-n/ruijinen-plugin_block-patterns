/**
 * ブロックパターンをエディターのインサーターから挿入できるかを確認する E2E テスト.
 *
 * 各パターンについて新規の固定ページ（下書き）を作り、以下を確認する（終了後に削除する）。
 * - インサーターでパターン名を検索し、クリックで挿入できること
 * - 挿入されたブロックが登録済みパターンの内容と一致し、ブロック検証エラー・未登録ブロック・
 *   未登録のブロックスタイルがないこと
 * - 保存・再読み込み後も壊れないこと
 * - フロントでパターンのブロックスタイル用 CSS が読み込まれること
 */
const { test, expect } = require("@wordpress/e2e-test-utils-playwright");
const {
	PATTERNS,
	getRegisteredPatterns,
	openEditor,
	getBlockReport,
	expectValidBlocks,
	savePost,
	deletePage,
} = require("../utils");

test.describe("ブロックパターンの挿入チェック", () => {
	let registered = [];

	test.beforeAll(async ({ requestUtils }) => {
		registered = await getRegisteredPatterns(requestUtils);
	});

	for (const pattern of PATTERNS) {
		test.describe(pattern.key, () => {
			let postId;
			const pageErrors = [];

			test.beforeEach(async ({ page }) => {
				pageErrors.length = 0;
				page.on("pageerror", (error) => pageErrors.push(error.message));
			});

			test.afterEach(async ({ requestUtils }) => {
				await deletePage(requestUtils, postId);
				postId = undefined;
			});

			test("インサーターから挿入して保存できる", async ({ admin, page }) => {
				const source = registered.find((p) => p.name === pattern.patternName);
				test.skip(
					!source,
					`${pattern.patternName} が登録されていないためスキップ（LP向けパターン集が有効な場合のサンプルパターンなど）`,
				);

				await admin.createNewPost({
					postType: "page",
					title: `[E2E] insert ${pattern.key}`,
				});
				postId = await page.evaluate(() =>
					window.wp.data.select("core/editor").getCurrentPostId(),
				);

				// インサーターを開き、パターン名で検索して挿入する
				await page
					.getByRole("toolbar", { name: "Document tools" })
					.getByRole("button", {
						name: /^(Block Inserter|Toggle block inserter)$/,
					})
					.click();
				const inserter = page.getByRole("region", { name: /Block Library/i });
				await inserter.getByRole("searchbox").fill(pattern.title);
				await inserter
					.getByRole("listbox", { name: /patterns/i })
					.getByRole("option", { name: pattern.title, exact: true })
					.click();

				// 登録済みパターンと同じ構成のブロックが挿入されていること
				await page.waitForFunction(
					() =>
						window.wp.data.select("core/block-editor").getBlocks().length > 0,
				);
				const { inserted, expected } = await page.evaluate((content) => {
					const names = (blocks) =>
						blocks.map((b) => [b.name, names(b.innerBlocks)]);
					return {
						inserted: names(
							window.wp.data.select("core/block-editor").getBlocks(),
						),
						expected: names(window.wp.blocks.parse(content)),
					};
				}, source.content);
				expect(
					inserted,
					"パターンの内容どおりにブロックが挿入されること",
				).toEqual(expected);
				expectValidBlocks(await getBlockReport(page), "挿入直後");

				await savePost(page);

				// 再読み込みして壊れていないか確認
				await openEditor(admin, page, postId);
				expectValidBlocks(await getBlockReport(page), "保存・再読み込み後");

				// フロントでブロックスタイル用の CSS が読み込まれること
				const styles = [
					...new Set(
						[...source.content.matchAll(/is-style-(RJE_[A-Za-z0-9_]+)/g)].map(
							(m) => m[1],
						),
					),
				];
				await page.goto(`/?page_id=${postId}&preview=true`);
				for (const style of styles) {
					await expect(
						page.locator(`link#is-style-${style}-front-css`),
						`${style} のフロント用 CSS が読み込まれること`,
					).toHaveCount(1);
				}

				expect(pageErrors, "JavaScript エラーが発生しないこと").toEqual([]);
			});
		});
	}
});
