/**
 * ブロックパターンがエディターで編集できるかを確認する E2E テスト.
 *
 * 各パターンについて「登録済みパターンの内容」と「既存の固定ページの内容」をコピーした
 * 一時的な下書きを作り、以下を確認する（元の固定ページは変更しない）。
 * - ブロック検証エラー・未登録ブロック・未登録のブロックスタイルがないこと
 * - RichText に文字を入力して保存でき、再読み込み後も壊れないこと
 * - 画像の差し替え、メディアの左右入れ替えをして保存でき、再読み込み後も壊れないこと
 */
const fs = require("fs");
const path = require("path");
const { test, expect } = require("@wordpress/e2e-test-utils-playwright");
const {
	ROOT,
	PATTERNS,
	getRegisteredPatterns,
	openEditor,
	getCanvas,
	getBlockReport,
	expectValidBlocks,
	savePost,
	deletePage,
} = require("../utils");

const TEST_IMAGE_NAME = "e2e-replaced-image";

const SOURCES = [
	...PATTERNS.map((pattern) => ({
		type: "pattern",
		pattern,
		label: `pattern:${pattern.key}`,
	})),
	...PATTERNS.map((pattern) => ({
		type: "page",
		pattern,
		label: `page:${pattern.pageSlug}`,
	})),
];

/**
 * テスト対象の内容（ブロックのマークアップ）を取得する.
 */
async function getSourceContent(requestUtils, source) {
	if (source.type === "pattern") {
		const patterns = await getRegisteredPatterns(requestUtils);
		return (
			patterns.find((p) => p.name === source.pattern.patternName)?.content ??
			null
		);
	}
	const pages = await requestUtils.rest({
		path: "/wp/v2/pages",
		params: {
			slug: source.pattern.pageSlug,
			context: "edit",
			status: "publish,draft,private",
		},
	});
	return pages[0]?.content.raw ?? null;
}

test.describe("ブロックパターンの編集チェック", () => {
	let testImage;

	test.beforeAll(async ({ requestUtils }) => {
		// 差し替え用の画像を、識別しやすいファイル名でアップロードする
		const tmpDir = path.join(ROOT, "artifacts/tmp");
		fs.mkdirSync(tmpDir, { recursive: true });
		const file = path.join(tmpDir, `${TEST_IMAGE_NAME}.jpg`);
		fs.copyFileSync(path.join(ROOT, "src/img/photo-woman1.jpg"), file);
		testImage = await requestUtils.uploadMedia(file);
	});

	test.afterAll(async ({ requestUtils }) => {
		if (testImage) {
			await requestUtils.deleteMedia(testImage.id);
		}
	});

	for (const source of SOURCES) {
		test.describe(source.label, () => {
			let postId;
			const pageErrors = [];

			test.beforeEach(async ({ requestUtils, page }) => {
				const content = await getSourceContent(requestUtils, source);
				test.skip(
					content === null,
					`${source.label} が見つからないためスキップ`,
				);

				const post = await requestUtils.rest({
					method: "POST",
					path: "/wp/v2/pages",
					data: { title: `[E2E] ${source.label}`, content, status: "draft" },
				});
				postId = post.id;

				pageErrors.length = 0;
				page.on("pageerror", (error) => pageErrors.push(error.message));
			});

			test.afterEach(async ({ requestUtils }) => {
				await deletePage(requestUtils, postId);
				postId = undefined;
			});

			test("テキストを編集して保存できる", async ({
				admin,
				page,
				requestUtils,
			}) => {
				await openEditor(admin, page, postId);
				const before = await getBlockReport(page);
				expectValidBlocks(before, "読み込み時");
				if (before.outdated.length) {
					test.info().annotations.push({
						type: "warning",
						description: `旧形式のマークアップ（保存時に新形式へ変換される）: ${before.outdated.join(", ")}`,
					});
				}

				const canvas = await getCanvas(page);
				const clientIds = await page.evaluate(() =>
					window.wp.data
						.select("core/block-editor")
						.getClientIdsWithDescendants(),
				);

				const typed = [];
				const notEditable = [];
				const hidden = [];
				for (const clientId of clientIds) {
					// ブロックを選択状態にする（選択時のみ表示される入力欄を表示させるため）
					await page.evaluate(
						(id) =>
							window.wp.data.dispatch("core/block-editor").selectBlock(id),
						clientId,
					);
					const blockName = await page.evaluate(
						(id) => window.wp.data.select("core/block-editor").getBlockName(id),
						clientId,
					);
					const block = canvas.locator(`[data-block="${clientId}"]`);
					if (!(await block.count())) {
						continue;
					}
					// 選択時のスタイル（is-selected）が反映されるまで待つ
					await expect(block).toHaveClass(/\bis-selected\b/);
					// そのブロック直下の RichText（子ブロックのものは除く）
					const ownIndexes = await block.evaluate((el, id) => {
						const nodes = [
							el,
							...el.querySelectorAll('[contenteditable="true"]'),
						].filter((node) => node.getAttribute("contenteditable") === "true");
						return nodes
							.map((node, i) =>
								node.closest("[data-block]")?.dataset.block === id ? i : -1,
							)
							.filter((i) => i >= 0);
					}, clientId);

					for (const index of ownIndexes) {
						const marker = `E2E${String(typed.length + notEditable.length + 1).padStart(3, "0")}`;
						const target = await block.evaluateHandle(
							(el, i) =>
								[el, ...el.querySelectorAll('[contenteditable="true"]')].filter(
									(node) => node.getAttribute("contenteditable") === "true",
								)[i],
							index,
						);
						const element = target.asElement();
						if (!element || !(await element.isVisible())) {
							hidden.push(`${blockName} (#${index})`);
							continue;
						}
						try {
							await element.click({ timeout: 5_000 });
							await page.keyboard.press("End");
							await page.keyboard.type(marker);
							typed.push({ marker, blockName });
						} catch (error) {
							notEditable.push(
								`${blockName} (#${index}): ${error.message.split("\n")[0]}`,
							);
						}
					}
				}

				test.info().annotations.push({
					type: "info",
					description: `入力したテキスト: ${typed.length} 箇所 / 入力できなかった箇所: ${notEditable.length} / 非表示のため未入力: ${hidden.length}${hidden.length ? ` (${hidden.join(", ")})` : ""}`,
				});
				expect(
					notEditable,
					"表示されている RichText にはすべて入力できること（デザイン上非表示の欄は対象外）",
				).toEqual([]);
				expect(
					typed.length,
					"編集できるテキストが1つ以上あること",
				).toBeGreaterThan(0);

				await savePost(page);

				// 再読み込みして壊れていないか確認
				await openEditor(admin, page, postId);
				expectValidBlocks(await getBlockReport(page), "保存・再読み込み後");

				const saved = await requestUtils.rest({
					path: `/wp/v2/pages/${postId}`,
					params: { context: "edit" },
				});
				const lostInSave = typed.filter(
					({ marker }) => !saved.content.raw.includes(marker),
				);
				expect(
					lostInSave,
					"入力したテキストがすべて保存されていること",
				).toEqual([]);

				await page.goto(`/?page_id=${postId}&preview=true`);
				const html = await page.content();
				const notRendered = typed.filter(
					({ marker }) => !html.includes(marker),
				);
				expect
					.soft(notRendered, "入力したテキストがフロントに表示されること")
					.toEqual([]);

				expect(pageErrors, "JavaScript エラーが発生しないこと").toEqual([]);
			});

			test("画像の差し替え・左右の入れ替えをして保存できる", async ({
				admin,
				page,
			}) => {
				await openEditor(admin, page, postId);
				expectValidBlocks(await getBlockReport(page), "読み込み時");

				// ブロックの属性定義から「画像ID と URL の組」と「左右の位置」を探して変更する
				const changes = await page.evaluate(
					(media) => {
						const { select, dispatch } = window.wp.data;
						const result = [];
						const suffixes = { id: "url", Id: "Url", ID: "URL" };
						for (const clientId of select(
							"core/block-editor",
						).getClientIdsWithDescendants()) {
							const block = select("core/block-editor").getBlock(clientId);
							const attrs =
								window.wp.blocks.getBlockType(block.name)?.attributes ?? {};
							const updates = {};
							for (const key of Object.keys(attrs)) {
								const m = key.match(/^(.*?)(id|Id|ID)$/);
								const urlKey = m && m[1] + suffixes[m[2]];
								// 画像が設定されている枠だけを差し替える
								if (urlKey && urlKey in attrs && block.attributes[urlKey]) {
									updates[key] = media.id;
									updates[urlKey] = media.url;
								}
							}
							for (const key of ["mediaPosition", "imagePosition"]) {
								const value = block.attributes[key];
								if (key in attrs && (value === "left" || value === "right")) {
									updates[key] = value === "left" ? "right" : "left";
								}
							}
							if (Object.keys(updates).length) {
								dispatch("core/block-editor").updateBlockAttributes(
									clientId,
									updates,
								);
								result.push({ clientId, name: block.name, updates });
							}
						}
						return result;
					},
					{ id: testImage.id, url: testImage.source_url },
				);

				const imageChanges = changes.filter((c) =>
					Object.values(c.updates).includes(testImage.id),
				);
				const positionChanges = changes.filter(
					(c) => "mediaPosition" in c.updates || "imagePosition" in c.updates,
				);
				test.info().annotations.push({
					type: "info",
					description: `画像の差し替え: ${imageChanges.length} 箇所 / 左右の入れ替え: ${positionChanges.length} 箇所`,
				});
				test.skip(
					changes.length === 0,
					"画像・左右の入れ替え対象がないパターン",
				);

				// 変更直後もブロックが壊れていないこと
				expectValidBlocks(await getBlockReport(page), "変更直後");
				await savePost(page);

				// 再読み込み後、変更した属性が保持されていること
				await openEditor(admin, page, postId);
				expectValidBlocks(await getBlockReport(page), "保存・再読み込み後");
				const notKept = await page.evaluate((expected) => {
					const { select } = window.wp.data;
					// 保存後は clientId が変わるため、ブロックの出現順で比較する
					const all = select("core/block-editor")
						.getClientIdsWithDescendants()
						.map((id) => select("core/block-editor").getBlock(id));
					const used = new Set();
					return expected
						.filter(({ name, updates }) => {
							const index = all.findIndex(
								(block, i) =>
									!used.has(i) &&
									block.name === name &&
									Object.entries(updates).every(
										([k, v]) => block.attributes[k] === v,
									),
							);
							if (index >= 0) {
								used.add(index);
							}
							return index < 0;
						})
						.map(({ name, updates }) => `${name}: ${JSON.stringify(updates)}`);
				}, changes);
				expect(notKept, "変更した画像・位置が保存されていること").toEqual([]);

				if (imageChanges.length) {
					await page.goto(`/?page_id=${postId}&preview=true`);
					expect
						.soft(
							await page.content(),
							"差し替えた画像がフロントに表示されること",
						)
						.toContain(TEST_IMAGE_NAME);
				}

				expect(pageErrors, "JavaScript エラーが発生しないこと").toEqual([]);
			});
		});
	}
});
