const { defineConfig } = require("@playwright/test");

// wp-env（npm start）で起動したローカル環境のフロント画面を、ログインしていない状態で撮影して比較する
const baseURL = process.env.WP_BASE_URL || "http://localhost:1001";

// BackstopJS で使っていた画面幅
const VIEWPORTS = [
	{ name: "sp", width: 375, height: 667 },
	{ name: "md", width: 768, height: 1024 },
	{ name: "lg", width: 1024, height: 768 },
	{ name: "xg", width: 1280, height: 768 },
];

module.exports = defineConfig({
	testDir: ".",
	testMatch: "*.spec.js",
	outputDir: "../../artifacts/visual-results",
	// 基準画像はローカル環境ごとに作成する（Git には含めない）
	snapshotPathTemplate: "{testDir}/__screenshots__/{projectName}/{arg}{ext}",
	timeout: 60_000,
	reporter: [
		["list"],
		["html", { outputFolder: "../../artifacts/visual-report", open: "never" }],
	],
	expect: {
		toHaveScreenshot: {
			// BackstopJS の misMatchThreshold 0.1（%）と同じ許容値
			maxDiffPixelRatio: 0.001,
			animations: "disabled",
		},
	},
	use: {
		baseURL,
		headless: true,
		locale: "ja-JP",
	},
	projects: VIEWPORTS.map(({ name, width, height }) => ({
		name,
		use: { viewport: { width, height } },
	})),
});
