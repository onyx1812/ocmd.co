'use strict';

var
	gulp = require('gulp'),
	sass = require('gulp-sass'),
	autoprefixer = require('gulp-autoprefixer'),
	imagemin = require('gulp-imagemin'),
	pngquant = require('imagemin-pngquant'),
	rigger = require('gulp-rigger'),
	browserSync = require('browser-sync').create();


sass.compiler = require('node-sass');

gulp.task('sass', function () {
	return gulp.src('src/scss/**/*.scss')
		.pipe(sass.sync({outputStyle: 'uncompressed'})
		.on('error', sass.logError))
		.pipe(autoprefixer())
		.pipe(gulp.dest('../css'))
		.pipe(browserSync.stream());
});

gulp.task('js', function(){
	return gulp.src('src/js/**/*.js')
		.pipe(rigger())
		.pipe(gulp.dest('../js'))
		.pipe(browserSync.stream());
});

gulp.task('images', function () {
	return gulp.src('src/img/**')
		.pipe(imagemin({
			progressive: true,
			svgoPlugins: [{removeViewBox: false}],
			use: [pngquant()],
			interlaced: true
		}))
		.pipe(gulp.dest('../img'))
});

gulp.task('browser-sync', ['sass', 'js'], function() {
	browserSync.init({
		proxy: "ocmd.loc",
		port: 8080
	});
});

gulp.task('default', ['browser-sync', 'sass', 'images', 'js'], function () {
	gulp.watch('src/scss/**/*.scss', ['sass']);
	gulp.watch('src/img/**', ['images']);
	gulp.watch('src/js/**/*.js', ['js']);

	gulp.watch('../css/*.css').on('change', browserSync.reload);
	gulp.watch('../js/**/*.js').on('change', browserSync.reload);
	gulp.watch('../partials/**/*.php').on('change', browserSync.reload);
	gulp.watch('../templates/**/*.php').on('change', browserSync.reload);
});