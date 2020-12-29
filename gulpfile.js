/* globals require */
var gulp = require('gulp');
var sort = require('gulp-sort');
var wpPot = require('gulp-wp-pot');

var translateFiles = '**/*.php';

gulp.task('makePOT', function () {
	return gulp.src(translateFiles)
		.pipe(sort())
		.pipe(wpPot({
			domain: 'billmate-order-management-for-woocommerce',
			destFile: 'src/languages/billmate-order-management-for-woocommerce.pot',
			package: 'billmate-order-management-for-woocommerce',
			bugReport: 'http://krokedil.se',
			lastTranslator: 'Krokedil <info@krokedil.se>',
			team: 'Krokedil <info@krokedil.se>'
		}))
		.pipe(gulp.dest('src/languages/billmate-order-management-for-woocommerce.pot'));
});