/**
 * Gulp File
 *
 * 1) Make sure you have node and npm installed locally
 *
 * 2) Install all the modules from package.json:
 * $ npm install
 *
 * 3) Run gulp to mifiy javascript and css using the 'gulp' command.
 */

var gulp            = require( 'gulp' );
var rename          = require( 'gulp-rename' );
var uglify          = require( 'gulp-uglify' );
var del             = require( 'del' );

var scripts = './assets/js/*.js';
var scriptsDestination= './assets/js/';

gulp.task( 'clean', function( cb ) {
	return del( ['assets/js/*.min.js'], cb );
});

gulp.task( 'default', [ 'JS' ] );


/**
 * Task: `customJS`.
 *
 * Concatenate and uglify custom JS scripts.
 *
 * This task does the following:
 *     1. Gets the source folder for JS custom files
 *     2. Concatenates all the files and generates custom.js
 *     3. Renames the JS file with suffix .min.js
 *     4. Uglifes/Minifies the JS file and generates custom.min.js
 */
gulp.task( 'JS',['clean'], function() {
    gulp.src( scripts )
        .pipe( rename( {
            suffix: '.min'
        }))
        .pipe( uglify() )
        .pipe( gulp.dest( scriptsDestination ) )
});
