const path = require("path");
const { defineConfig } = require("@playwright/test");

// wp-env（npm start）で起動したローカル環境に対してテストする
const baseURL = process.env.WP_BASE_URL || "http://localhost:1001";
process.env.WP_BASE_URL = baseURL;
process.env.WP_USERNAME = process.env.WP_USERNAME || "e2e-admin";
process.env.WP_PASSWORD = process.env.WP_PASSWORD || "password";
process.env.STORAGE_STATE_PATH =
	process.env.STORAGE_STATE_PATH ||
	path.join(__dirname, "../../artifacts/storage-states/e2e-admin.json");

module.exports = defineConfig({
	testDir: "./specs",
	globalSetup: require.resolve("./global-setup"),
	outputDir: "../../artifacts/test-results",
	// 同じ WordPress の DB・オプションを使うため直列で実行する
	workers: 1,
	fullyParallel: false,
	timeout: 120_000,
	reporter: [
		["list"],
		[
			"html",
			{ outputFolder: "../../artifacts/playwright-report", open: "never" },
		],
	],
	use: {
		baseURL,
		storageState: process.env.STORAGE_STATE_PATH,
		headless: true,
		viewport: { width: 1600, height: 1000 },
		locale: "en-US",
		screenshot: "only-on-failure",
		trace: "retain-on-failure",
	},
});
