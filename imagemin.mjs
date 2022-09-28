import imagemin from 'imagemin-keep-folder'
import imageminMozjpeg from 'imagemin-mozjpeg'
import imageminPngquant from 'imagemin-pngquant'
// import imageminWebp from 'imagemin-webp'
import imageminSvgo from 'imagemin-svgo'
import imageminGifsicle from 'imagemin-gifsicle'

const srcDir = "./src/img/**/*.{jpg,jpeg,png,gif,svg}";
const outDir = "./dist/img/**/*";

// const convertWebp = (targetFiles) => {
//   imagemin([targetFiles], {
//     use: [imageminWebp({ quality: 50 })], // qualityを指定しないと稀にwebpが走らない場合があるので注意する。（{ quality: 50 }）で指定すれば大体いけそう
//   });
// };

imagemin([srcDir], {
  plugins: [
    imageminMozjpeg(),
    imageminPngquant(),
    imageminGifsicle(),
    imageminSvgo(),
  ],
  replaceOutputDir: (output) => {
    return output.replace(/img\//, "../dist/img/");
  },
}).then(() => {
  // convertWebp(outDir);
  console.log("Images optimized!");
});
