var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var merge = require('merge-stream');

gulp.task('sass', () => {
	var common_style = gulp.src('./src/css/**/*.scss')
        .pipe(sass({outputStyle: 'compressed'}))
        .pipe(autoprefixer())
        .pipe(gulp.dest('dist/css/'));
    var block_style = gulp.src('./block-styles/**/*.scss')
        .pipe(sass({outputStyle: 'compressed'}))
        .pipe(autoprefixer())
        .pipe(gulp.dest('dist/css/block-styles'));
    var specific_style = gulp.src(['./patterns/**/*.scss', '!patterns/__patterns_examples/*.scss'])
        .pipe(sass({outputStyle: 'compressed'}))
        .pipe(autoprefixer())
        .pipe(gulp.dest('dist/css/patterns'));
	return merge(common_style, block_style, specific_style);
});

gulp.task('watch', () => {
	gulp.watch('./block-styles/**/*.scss', gulp.task('sass'));
	gulp.watch('./patterns/**/*.scss', gulp.task('sass'));
	gulp.watch('./src/css/**/*.scss', gulp.task('sass'));
});