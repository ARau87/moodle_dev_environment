const gulp = require('gulp');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const babel = require('gulp-babel');
const browserify = require('gulp-browserify');
const rename = require('gulp-rename');

gulp.task('compile:sass', () => {

    return gulp.src('./src/index.scss')
               .pipe(sass())
               .pipe(rename('styles.css'))
               .pipe(gulp.dest('.'));

});

gulp.task('compile:js', () => {
     return gulp.src('./src/js/index.js')
                .pipe(sourcemaps.init())
                .pipe(babel({
                        presets: ['@babel/env']
                 }))
                .pipe(browserify({
                    sourceType: 'module'
                }))
                .pipe(sourcemaps.write('.'))
                .pipe(gulp.dest('lib'))
});

gulp.task('default', ['compile:sass']);