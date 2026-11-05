const { request } = require("@playwright/test");
const { RequestUtils } = require("@wordpress/e2e-test-utils-playwright");

// ログインして Cookie と REST の nonce を保存する（各テストはこの状態で開始する）
module.exports = async function globalSetup(config) {
	const { baseURL } = config.projects[0].use;
	const requestContext = await request.newContext({ baseURL });
	const requestUtils = new RequestUtils(requestContext, {
		storageStatePath: process.env.STORAGE_STATE_PATH,
		user: {
			username: process.env.WP_USERNAME,
			password: process.env.WP_PASSWORD,
		},
	});
	await requestUtils.setupRest();
	await requestContext.dispose();
};
