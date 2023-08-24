/**
 * Required Modules.
 *
 * @type {Gulp}
 */
const gulp = require('gulp')
    , concat = require('gulp-concat')
    , terser = require('gulp-terser')
    , cleanCSS = require('gulp-clean-css')
;

/**
 * Source and Destiny directories.
 *
 * @type {string}
 */
const jsDest = 'public/dist/js'
    , cssDest = 'public/dist/css';


/**
 * Compressing *.css files
 */
gulp.task('css', function () {
    return gulp.src(
        [
            'vendor/almasaeed2010/adminlte/plugins/fontawesome-free/css/all.min.css',
            'vendor/almasaeed2010/adminlte/dist/css/adminlte.min.css',
            'vendor/almasaeed2010/adminlte/plugins/bootstrap-switch/css/bootstrap3/bootstrap-switch.css',
        ])
        .pipe(concat('css.min.css'))
        .pipe(cleanCSS({compatibility: 'ie8'}))
        .pipe(gulp.dest(cssDest));
});

/**
 * Compressing *.js files
 */
gulp.task('js', function () {
    return gulp.src(
        [
            'vendor/almasaeed2010/adminlte/plugins/jquery/jquery.min.js',
            'vendor/almasaeed2010/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js',
            'vendor/almasaeed2010/adminlte/dist/js/adminlte.min.js',
            'vendor/almasaeed2010/adminlte/plugins/bootstrap-switch/js/bootstrap-switch.js',
            'vendor/almasaeed2010/adminlte/plugins/jquery-validation/jquery.validate.min.js',
            'vendor/almasaeed2010/adminlte/plugins/jquery-validation/additional-methods.min.js',

        ])
        .pipe(concat('js.min.js'))
        .pipe(terser())
        .pipe(gulp.dest(jsDest));
});

/**
 * Compressing fonts
 */
gulp.task('fonts', function () {
    return gulp.src(
        [
            'vendor/almasaeed2010/adminlte/plugins/fontawesome-free/webfonts/fa-solid-900.woff2',
            'vendor/almasaeed2010/adminlte/plugins/fontawesome-free/webfonts/fa-regular-400.woff2',
            'vendor/almasaeed2010/adminlte/plugins/fontawesome-free/webfonts/fa-solid-900.woff',
            'vendor/almasaeed2010/adminlte/plugins/fontawesome-free/webfonts/fa-regular-400.woff',
            'vendor/almasaeed2010/adminlte/plugins/fontawesome-free/webfonts/fa-solid-900.ttf',
            'vendor/almasaeed2010/adminlte/plugins/fontawesome-free/webfonts/fa-regular-400.ttf',
        ])
        .pipe(gulp.dest('public/dist/webfonts'));
});

/**
 * Runs all tasks
 */
gulp.task('run', gulp.parallel(
    'css',
    'js',
    'fonts',
));