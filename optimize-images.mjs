import { mkdir, readFile, writeFile } from "node:fs/promises";
import path from "node:path";
import { globSync } from "glob";
import sharp from "sharp";
import { optimize } from "svgo";

const srcDir = "src/img";
const distDir = "dist/img";

// 拡張子ごとの圧縮処理（入力バッファを受け取り、圧縮後のバッファを返す）
const jpeg = (input) => sharp(input).jpeg({ quality: 75, mozjpeg: true }).toBuffer();
const optimizers = {
  ".jpg": jpeg,
  ".jpeg": jpeg,
  ".png": (input) =>
    sharp(input).png({ palette: true, quality: 80, compressionLevel: 9, effort: 10 }).toBuffer(),
  ".gif": (input) => sharp(input, { animated: true }).gif({ effort: 10 }).toBuffer(),
  ".svg": async (input) => Buffer.from(optimize(input.toString(), { multipass: true }).data),
};

const files = globSync("**/*.{jpg,jpeg,png,gif,svg}", { cwd: srcDir, nodir: true });

const results = await Promise.allSettled(
  files.map(async (file) => {
    const srcPath = path.join(srcDir, file);
    const distPath = path.join(distDir, file);
    const input = await readFile(srcPath);
    const optimized = await optimizers[path.extname(file).toLowerCase()](input);

    await mkdir(path.dirname(distPath), { recursive: true });
    // 圧縮で逆にサイズが増えた場合は元画像をそのまま出力する
    await writeFile(distPath, optimized.length < input.length ? optimized : input);
  })
);

const failures = files.filter((file, i) => {
  const result = results[i];
  if (result.status === "rejected") {
    console.error(`Failed to optimize ${file}: ${result.reason?.message ?? result.reason}`);
    return true;
  }
  return false;
});

if (failures.length) {
  process.exit(1);
}

console.log(`Images optimized! (${files.length} files)`);
