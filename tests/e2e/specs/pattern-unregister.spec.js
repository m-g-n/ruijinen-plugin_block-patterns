/**
 * オプションページ（設定 > 類人猿）でブロックパターンを無効化できるかを確認する E2E テスト.
 *
 * テスト終了後は「設定をリセット」で元に戻す（テスト前の設定は保持しない）。
 */
const { test, expect } = require("@wordpress/e2e-test-utils-playwright");
const { PATTERNS, getRegisteredPatterns } = require("../utils");

const PAGE_SLUG = "rje_patterns_unregister";

async function resetOption(admin, page) {
	await admin.visitAdminPage("options-general.php", `page=${PAGE_SLUG}`);
	await page.getByRole("button", { name: "設定をリセット" }).click();
	await expect(page.getByText("Settings saved.")).toBeVisible();
}

test.describe("ブロックパターンの無効化", () => {
	test.afterEach(async ({ admin, page }) => {
		await resetOption(admin, page);
	});

	test("無効化したパターンだけが登録されなくなり、リセットで元に戻る", async ({
		admin,
		page,
		requestUtils,
	}) => {
		const before = (await getRegisteredPatterns(requestUtils)).map(
			(p) => p.name,
		);
		const target = PATTERNS.find((p) => before.includes(p.patternName));
		test.skip(!target, "無効化の対象にできるパターンがないためスキップ");

		await admin.visitAdminPage("options-general.php", `page=${PAGE_SLUG}`);
		const checkboxes = page.locator('input[type="checkbox"].input_unregister');
		expect(
			await checkboxes.count(),
			"登録済みパターンの数だけ切り替えの項目があること",
		).toBe(PATTERNS.filter((p) => before.includes(p.patternName)).length);

		// トグル表示のためチェックボックスはラベルのクリックで切り替える
		await page.locator(`label[for="${target.section}_${target.name}"]`).click();
		await expect(
			page.locator(`#${target.section}_${target.name}`),
		).toBeChecked();
		await page.getByRole("button", { name: "Save Changes" }).click();
		await expect(page.getByText("Settings saved.")).toBeVisible();
		await expect(
			page.locator(`#${target.section}_${target.name}`),
		).toBeChecked();

		const disabled = (await getRegisteredPatterns(requestUtils)).map(
			(p) => p.name,
		);
		expect(disabled, "無効化したパターンが登録されないこと").not.toContain(
			target.patternName,
		);
		expect(disabled.sort(), "他のパターンは登録されたままであること").toEqual(
			before.filter((name) => name !== target.patternName).sort(),
		);

		await resetOption(admin, page);
		await expect(
			page.locator(`#${target.section}_${target.name}`),
		).not.toBeChecked();
		const restored = (await getRegisteredPatterns(requestUtils)).map(
			(p) => p.name,
		);
		expect(restored.sort(), "リセットですべてのパターンが戻ること").toEqual(
			before.sort(),
		);
	});
});
