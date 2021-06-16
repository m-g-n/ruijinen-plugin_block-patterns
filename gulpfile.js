var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');

gulp.task('sass', () => {
    return gulp.src('./src/css/*.scss')
        .pipe(sass({outputStyle: 'compressed'}))
        .pipe(autoprefixer())
        .pipe(gulp.dest('./dist/css'));
});
 
gulp.task('watch', () => {
    gulp.watch('./**/*.scss', gulp.task('sass'));
});