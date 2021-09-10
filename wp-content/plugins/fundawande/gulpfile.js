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

//Walkthrough tour scripts and destination
var tourScripts = './assets/js/tour-scripts/*.js';
var tourScriptsDestination= './assets/js/tour-scripts';


gulp.task( 'clean', function( cb ) {
	return del( ['assets/js/*.min.js'], cb );
});

gulp.task( 'clean-tour-scripts', function( cb ) {
	return del( ['assets/js/tour-scripts/*.min.js'], cb );
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
    gulp.src( './node_modules/sortablejs/Sortable.min.js' )
        .pipe( gulp.dest( scriptsDestination ) );

    gulp.src( scripts )
        .pipe( rename( {
            suffix: '.min'
        }))
        .pipe( uglify() )
        .pipe( gulp.dest( scriptsDestination ) )

});

/**
 * Task: `tourJS`.
 *
 * Concatenate and uglify custom tour scripts.
 *
 * This task does the following:
 *     1. Gets the source folder for tour scripts
 *     2. Concatenates all the tour script files and generates custom.js
 *     3. Renames the tour script file with suffix .min.js
 *     4. Uglifes/Minifies the tour-script file and generates custom.min.js
 */
gulp.task( 'tourJS',['clean-tour-scripts'], function() {

    // Concatenate and uglify tour scripts
    gulp.src( tourScripts )
    .pipe( rename( {
        suffix: '.min'
    }))
    .pipe( uglify() )
    .pipe( gulp.dest( tourScriptsDestination ) )    

});