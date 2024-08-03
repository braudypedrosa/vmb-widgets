const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const cleanCSS = require('gulp-clean-css');
const concat = require('gulp-concat');
const terser = require('gulp-terser');
const gulpIf = require('gulp-if');
const yargs = require('yargs');

const argv = yargs.argv;
const isProduction = argv.prod;

function adminStyles() {
  return gulp.src('admin/assets/scss/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(concat('admin-main.css'))
    .pipe(gulpIf(isProduction, cleanCSS()))
    .pipe(gulp.dest('admin/dist/css'));
}

function publicStyles() {
  return gulp.src('public/assets/scss/*/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(concat('public-main.css'))
    .pipe(gulpIf(isProduction, cleanCSS()))
    .pipe(gulp.dest('public/dist/css'));
}

function adminScripts() {
  return gulp.src('admin/assets/js/*/*.js')
    .pipe(concat('admin-main.js'))
    .pipe(gulpIf(isProduction, terser()))
    .pipe(gulp.dest('admin/dist/js'));
}

function publicScripts() {
  return gulp.src('public/assets/js/*/*.js')
    .pipe(concat('public-main.js'))
    .pipe(gulpIf(isProduction, terser()))
    .pipe(gulp.dest('public/dist/js'));
}

function watchFiles() {
  gulp.watch('admin/assets/scss/*.scss', adminStyles);
  gulp.watch('public/assets/scss/*/*.scss', publicStyles);
  gulp.watch('admin/assets/js/*/*.js', adminScripts);
  gulp.watch('public/assets/js/*/*.js', publicScripts);
}

const build = gulp.parallel(adminStyles, publicStyles, adminScripts, publicScripts);
const watch = gulp.series(build, watchFiles);

gulp.task('build', build);
gulp.task('watch', watch);
