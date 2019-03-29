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

var gulp = require("gulp");
var rename = require("gulp-rename");
var uglify = require("gulp-uglify");
var del = require("del");

// CSS-related plugins
var sass = require("gulp-sass"); // Gulp pluign for Sass compilation.
var minifycss = require("gulp-uglifycss"); // Minifies CSS files.
var autoprefixer = require("gulp-autoprefixer"); // Autoprefixing magic.
var filter = require("gulp-filter"); // Enables you to work on a subset of the original files by filtering them using globbing.
var sourcemaps = require("gulp-sourcemaps"); // Maps code in a compressed file (E.g. style.css) back to it’s original position in a source file (E.g. structure.scss, which was later combined with other css files to generate style.css)
var browserSync = require("browser-sync").create(); // Reloads browser and injects CSS. Time-saving synchronised browser testing.
var lineec = require("gulp-line-ending-corrector"); // Consistent Line Endings for non UNIX systems. Gulp Plugin for Line Ending Corrector (A utility that makes sure your files have consistent line endings)

// JS Locations
var scripts = "./assets/js/src/*.js";
var scriptsDestination = "./assets/js/";

// CSS Locations
var styleCustomSRC = "./assets/css/src/*.scss"; // Path to main .scss file.
var styleCustomDestination = "./assets/css/"; // Path to place the compiled CSS file.

//Walkthrough tour scripts and destination
var tourScripts = "./assets/js/tour-scripts/*.js";
var tourScriptsDestination = "./assets/js/tour-scripts";

// Browsers you care about for autoprefixing.
// Browserlist https        ://github.com/ai/browserslist
const AUTOPREFIXER_BROWSERS = [
  "last 2 version",
  "> 1%",
  "ie >= 9",
  "ie_mob >= 10",
  "ff >= 30",
  "chrome >= 34",
  "safari >= 7",
  "opera >= 23",
  "ios >= 7",
  "android >= 4",
  "bb >= 10"
];

gulp.task("clean", function(cb) {
  return del(["assets/js/*.min.js"], cb);
});

gulp.task("clean-tour-scripts", function(cb) {
  return del(["assets/js/tour-scripts/*.min.js"], cb);
});

gulp.task("default", ["JS", "customStyles"]);

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
gulp.task("JS", ["clean"], function() {
  gulp
    .src("./node_modules/sortablejs/Sortable.min.js")
    .pipe(gulp.dest(scriptsDestination));

  gulp
    .src(scripts)
    .pipe(
      rename({
        suffix: ".min"
      })
    )
    .pipe(uglify())
    .pipe(gulp.dest(scriptsDestination));
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
gulp.task("tourJS", ["clean-tour-scripts"], function() {
  // Concatenate and uglify tour scripts
  gulp
    .src(tourScripts)
    .pipe(
      rename({
        suffix: ".min"
      })
    )
    .pipe(uglify())
    .pipe(gulp.dest(tourScriptsDestination));
});

gulp.task("customStyles", function() {
  gulp
    .src(styleCustomSRC)
    .pipe(sourcemaps.init())
    .pipe(
      sass({
        errLogToConsole: true,
        outputStyle: "compact",
        // outputStyle: 'compressed',
        // outputStyle: 'nested',
        // outputStyle: 'expanded',
        precision: 10
      })
    )
    .on("error", console.error.bind(console))
    .pipe(autoprefixer(AUTOPREFIXER_BROWSERS))

    .pipe(lineec()) // Consistent Line Endings for non UNIX systems.
    .pipe(gulp.dest(styleCustomDestination))

    .pipe(filter("**/*.css")) // Filtering stream to only css files

    .pipe(browserSync.stream()) // Reloads style.css if that is enqueued.

    .pipe(rename({ suffix: ".min" }))
    .pipe(
      minifycss({
        maxLineLen: 10
      })
    )
    .pipe(lineec()) // Consistent Line Endings for non UNIX systems.
    .pipe(gulp.dest(styleCustomDestination))

    .pipe(filter("**/*.css")) // Filtering stream to only css files
    .pipe(browserSync.stream()); // Reloads style.min.css if that is enqueued.
});
