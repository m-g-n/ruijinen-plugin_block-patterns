/**
 * 固定ページ（rje_r000helper_* / rje_r002lp_*）の見た目の差分チェック.
 *
 * 基準画像の作成・更新: npm run test:visual:update
 * 基準画像との比較:     npm run test:visual
 */
const { test, expect } = require("@playwright/test");
const scenarios = require("./scenarios.json");

for (const { label, path, selector, hide } of scenarios) {
	test(label, async ({ page }) => {
		const response = await page.goto(path);
		expect(response?.status(), `${path} が表示できること`).toBe(200);
		await page.waitForLoadState("networkidle");
		// 比較対象から除外する要素（パンくず・ページタイトルなど）を非表示にする
		if (hide.length) {
			await page.addStyleTag({
				content: `${hide.join(",")} { display: none !important; }`,
			});
		}
		await page.evaluate(() => document.fonts.ready);
		// 画像の遅延読み込みやアニメーションの完了を待つ
		await page.waitForTimeout(1000);

		if (selector) {
			await expect(page.locator(selector).first()).toHaveScreenshot(
				`${label}.png`,
			);
		} else {
			await expect(page).toHaveScreenshot(`${label}.png`, { fullPage: true });
		}
	});
}
