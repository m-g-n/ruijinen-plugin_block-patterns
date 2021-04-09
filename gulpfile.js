var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');

gulp.task('sass', () => {
    return gulp.src('./block-styles/**/*.scss')
        .pipe(sass({outputStyle: 'compressed'}))
        .pipe(autoprefixer())
        .pipe(gulp.dest('./block-styles'));
});
 
gulp.task('watch', () => {
    gulp.watch('./**/*.scss', gulp.task('sass'));
});