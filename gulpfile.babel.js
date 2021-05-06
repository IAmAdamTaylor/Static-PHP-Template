/**
 * ------------------------------------------------------------
 * Package Definitions
 * ------------------------------------------------------------
 */
let cwd = process.cwd(),
  
    // Require all gulp modules
    fs = require('fs'),
    path = require('path'),
    del = require('del'),

    gulp = require('gulp'),
    runSequence = require('run-sequence'),
    argv = require('yargs').argv,
    spawn = require('child_process').spawn,
    gutil = require('gulp-util'),
    browserSync = require('browser-sync').create(),

    autoprefixer = require('gulp-autoprefixer'),
    babel = require('gulp-babel'),
    cache = require('gulp-cache'),
    chmod = require('gulp-chmod'),
    concat = require('gulp-concat'),
    cssnano = require('gulp-cssnano'),
    htmlmin = require('gulp-htmlmin'),
    jshint = require('gulp-jshint'),
    notify = require('gulp-notify'),
    rename = require('gulp-rename'),
    rev = require('gulp-rev'),
    sass = require('gulp-ruby-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    uglify = require('gulp-uglify'),

    // Bring whole package.json into Gulp
    // so we can reference it at will
    pkg = require( './package.json' ),
    globs = pkg.globs;


/**
 * ------------------------------------------------------------
 * Function Definitions
 * ------------------------------------------------------------
 */

/**
 * Get the names of subfolders of a parent folder.
 * @param  {String} dir The parent folder path.
 * @return {Array}      An array of the folder names found.
 */
let getFolders = ( dir ) => {
  return fs.readdirSync( dir )
    .filter( ( file ) => {
      return fs.statSync( path.join( dir, file ) ).isDirectory();
    } );
};


/**
 * ------------------------------------------------------------
 * Level 1 (Base) Tasks
 * These tasks should be thought of as building blocks
 * to build more complex tasks from.
 * ------------------------------------------------------------
 */

/**
 * Delete all files and folders from the /dist directory.
 * Files which are not part of the build process will be preserved.
 */
gulp.task( 'clean', () => {
  let preserve = [
    'dist/.git', 
    'dist/.gitignore', 
    'dist/.keep', 
    'dist/.htaccess', 
    'dist/README.md'
  ];

  /**
   * Prepend ! to each preserved item to fit exclude format for del plugin.
   * @see https://github.com/sindresorhus/multimatch#globbing-patterns
   */
  preserve = preserve.map( (item) => '!' + item );

  return del( ['dist/**/*'].concat( preserve ) );
} );

/**
 * Process all Sass (.scss) files into CSS.
 * 
 * Uses autoprefixer for browser prefixes:
 *   Last 2 major versions of browsers,
 *   Any browser that has more than 5% global usage
 *   IE 9, 10 & 11 specifically
 *
 * Creates:
 *     Main .css file
 *     Minified .min.css file
 *     Sourcemap for minified file
 */
gulp.task( 'styles', () => {
  return sass( globs.styles.in, { style: 'expanded', require: 'sass-globbing', sourcemap: true } )
    .pipe( autoprefixer( {
      browsers: ['> 0.25%', 'IE 10', 'IE 11']
    } ) )
    .pipe( gulp.dest( globs.styles.out ) )
    .pipe( rename( { suffix: '.min' } ) )
    .pipe( cssnano( { 
      autoprefixer: false, 
      mergeIdents: false,
      reduceIdents: false 
    } ) )
    .pipe( gulp.dest( globs.styles.out ) )
    .pipe( sourcemaps.write('maps') )
    .pipe( gulp.dest( globs.styles.out ) )
    .pipe( browserSync.stream() )
    .pipe( notify( { message: 'Styles task complete', onLast: true } ) );
} );

/**
 * Process each subfolder of the scripts folder, 
 * into a single combined file per folder.
 *
 * Uses promises to know when each individual 
 * folder has been fully processed.
 * 
 * Creates:
 *     Single .js folder per subfolder
 *     Single .min.js minified file per subfolder
 *     Sourcemap for each minified file created
 */
gulp.task( 'scripts', () => {
  let promises = [],
      taskPromise;

  getFolders( globs.scripts.in ).map( function( folder ) {

    // Generate a promise per folder, resolved when stream ends
    let _promise = new Promise( (resolve, reject) => {

      gulp.src( path.join( globs.scripts.in, folder, '/*.js' ) )
        .pipe( jshint( { esversion: 6 } ) )
        .pipe( jshint.reporter('default') )
        .pipe( sourcemaps.init() )
        .pipe( babel() )
        .pipe( concat( folder + '.js' ) )
        .pipe( chmod(0o644) )
        .pipe( gulp.dest( globs.scripts.out ) )
        .pipe( rename( { suffix: '.min' } ) )
        .pipe( uglify() )
        .pipe( gulp.dest( globs.scripts.out ) )
        .pipe( sourcemaps.write('maps') )
        .pipe( gulp.dest( globs.scripts.out ) )
        .on( 'end', resolve )
        .pipe( notify( { message: 'Scripts task [' + folder + '] complete', onLast: true } ) );

    } );

    promises.push( _promise );

  } );

  taskPromise = Promise.all( promises );

  taskPromise.then( () => {
    browserSync.reload();
  } );

  return taskPromise;
} );

/**
 * Add a version string to any CSS/JS assets referenced in the HTML files.
 * The version strings are written to a .json file in the output directory.
 */
gulp.task( 'create-revisions', () => {
  return gulp.src( globs.createRevisions.in )
    .pipe( rev() )
    .pipe( gulp.dest( globs.createRevisions.out ) )
    .pipe( rev.manifest() )
    .pipe( gulp.dest( globs.createRevisions.out ) );
} );

/**
 * Start the local develpment server.
 * Should run by default on http://localhost:3000/
 * but URL will be displayed in terminal output.
 */
gulp.task( 'server', ( done ) => {
  browserSync.init( {
    proxy: 'http://DOMAIN:8888/',
    port: 1120,
    // Don't mirror clicks, scroll across instances
    ghostMode: false,
    // Don't open straight away (most likely already have 
    // a tab running so this causes duplication)
    open: false
  } );

  done();
} );

gulp.task( 'server-reload', ( done ) => {
  browserSync.reload();
  done();
} );


/**
 * ------------------------------------------------------------
 * Level 2 (Combined) Tasks
 * Built up of sequences of the Level 1 tasks.
 * ------------------------------------------------------------
 */

/**
 * Start watching for changes to source files.
 * Any changes will cause the associated task to be run.
 */
gulp.task( 'watch', ( done ) => {
  // Watch style files
  gulp.watch( globs.styles.watch, ['styles'] ).on( 'error', (error) => {
    gutil.log( error.toString() );
  } );

  // Watch script files
  gulp.watch( globs.scripts.watch, ['scripts'] ).on( 'error', (error) => {
    gutil.log( error.toString() );
  } );

  gulp.watch( "**/*.php", ['server-reload'] ).on( 'error', (error) => {
    gutil.log( error.toString() );
  } );

  done();
} );

/**
 * Builds all of the assets of the site.
 */
gulp.task( 'build', ( done ) => {
  runSequence( 'styles', 'scripts', done );
} );

/**
 * Converts the built assets into a production ready format.
 */
gulp.task( 'production-ready', ( done ) => {
  runSequence( 'create-revisions', done );
} );


/**
 * ------------------------------------------------------------
 * Level 3 (Complex) Tasks
 * Built up of sequences of the Level 1 & Level 2 tasks.
 * ------------------------------------------------------------
 */

/**
 * Run sequence for local development with live reloads.
 */
gulp.task( 'local', ( done ) => {
  argv.env = 'development';
  
  runSequence( 'clean', 'build', 'server', 'watch', done );
} );

/**
 * Run sequence for staging site.
 */
gulp.task( 'staging', ( done ) => {
  argv.env = 'development';

  runSequence( 'clean', 'build', done );
} );

/**
 * Run sequence for live site.
 * Doesn't use `build` as this could allow drafts to be created.
 */
gulp.task( 'live', ( done ) => {
  argv.env = 'production';

  runSequence( 'clean', 'build', 'production-ready', done );
} );

/**
 * Set default task - local with development server.
 */
gulp.task( 'default', ( done ) => {
  gulp.start( 'local' );
  done();
} );
