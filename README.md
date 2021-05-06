README.md

This project requires the following packages to run. See below for installation instructions.

- [PHP](#php)
- [Sass](#sass)
- [Sass Globbing Gem](#sass-globbing-gem)
- [Gulp.js](#gulpjs)

# PHP

See http://php.net/

This website is built in PHP. You must be able to run PHP as a server side language in order to dynamically generate this site.

For local installations and testing, [MAMP Pro](https://www.mamp.info/en/mamp-pro/) can create a server on your computer and is capable of running this site.

The `index.php` file is used as the entry point for the site and all requests (apart from for existing files and folders) are rewritten to it using the `.htaccess` file and Apache's `RewriteRule`.  
This file runs a small amount of setup, defining global file paths and loading modules, then attempts to load the requested page. If the page does not exist the `page-templates/404.php` file is loaded instead.

## .htaccess

A .htaccess file is required for this site to run. It should include the following lines:

    # BEGIN Redirect to trailing slashs
    <IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*[^/])$ /$1/ [L,R=301]
    </IfModule>
    # END Redirect to trailing slashs 
     
    # BEGIN Static Rewrite
    <IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^index\.php$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?q=$1 [L,QSA]
    </IfModule>
    # END Static Rewrite

# Sass

Sass is used to compile `.scss` files into browser readable CSS.

Sass can be installed as a Ruby gem from the command line by running,

	sudo gem install sass

Once this has run, you can use `sass -v` to check the install has succeeded.

This project is built against "Sass 3.5.1 (Bleeding Edge)". You will need at least that version of Sass to run this project.

# Sass Globbing Gem

The Sass Globbing Gem allows `.scss` files to `@import` all of the `.scss` files inside a folder in one command, instead of referencing each file individually.

It can be installed similarly to Sass. Open a Terminal and run,

	sudo gem install sass-globbing

This gem is automatically required when using Gulp to run the `styles` task.

# Gulp.js

See https://gulpjs.com/

Gulp.js is a task runner that allows us to define and then run specific tasks or build processes required for this project.  
For this project, build files are included in the `/src` folder and compiled into the `/dist` folder.

## Installation

Gulp.js can be installed by Node Package Manager (NPM). If you already have NPM set up you can run,

    sudo npm install --global gulp

in your Terminal to install Gulp's command line tools globally.  
A specific version of Gulp will also be packaged with each project.

If you do not have NPM installed, you can download and run the LTS Node.js installer from https://nodejs.org/en/download/.

## Glob File Paths

All file in and out paths are defined within the `package.json` file in the `globs` key. This provides a quick and easy way to switch folder names or where assets are stored if the project structure ever changes.

## Usage & Running tasks

To run a Gulp task, change directory (`cd`) in Terminal to the project root (the folder this ReadMe file is contained within).

Inside that folder you can run,

    gulp <task-name>

to run a task. Tasks are split up into Level 1, 2 & 3:
- Level 1: Base level tasks with no dependencies on each other.
- Level 2: Sequences of Level 1 tasks.
- Level 3: The most complex tasks containing sequences of both Level 1 & 2 tasks. 

Level 3 tasks should be reserved as tasks that can be run to create a set of files for publishing to specific destinations.
For example, the tasks that currently exist can run a full set of deployable files for local, staging and live environments.

### Tasks with command line flags

Some tasks have options that can be specified by passing a command line flag when running the task. These are documented in the respective tasks that use them, but can be added to higher level tasks that depend on those tasks as well.

To specify a flag, add 2 dashes before the flag name and place this after the task name. Multiple flags can be passed for each command if required.

Examples:

    gulp taskName --flag
    gulp taskName --flag --flag2
    gulp taskName --flag=true --flag2=development
    gulp taskName --flag2=development

Some tasks will purposefully overwrite the flags to ensure they run with the settings you would assume. This will be mentioned in the documentation for the task performing the overwriting.

## Defined tasks

### default 
    gulp
    gulp default

Every Gulp setup defines a default task which can be called without a task name parameter.

The default task for this project is the `local` build task.  
**N.B.** This will start a local development server and watch for changes.

### Level 1 Tasks

#### clean
    gulp clean

Deletes the contents of the `/dist` folder. Certain folders and files inside are preserved - see the task definition within the `gulpfile.babel.js` for which files these are.

#### styles
    gulp styles

Compiles `.scss` files in `/src/css/sass` into CSS files. Generates a minified file and `.map` for each file compiled.

Since Autoprefixer is run across the CSS generated there is no need to add separate prefixes.

#### scripts
    gulp scripts

Concatenates the `.js` files inside each folder in `/src/js` into a single file per folder. 

The compiled file will be named the same as the folder which was compiled. For example, the `.js` files inside the `footer` folder will be compiled into `footer.js` 

A minified file will also be generated for each folder compiled.

#### create-revisions
    gulp create-revisions

Adds a version hash string to any minifed CSS and JavaScript files in the `/dist` directory.  
A `.json` manifest file is created listing the original file names and their changed versions.

It does not replace the references to these files within the HTML files. This is handled by the `get_revision()` function in the `/load.php` file. Any links to files within the site that have been revisioned should call this function passing the original file name. The name of the revision will be returned.

#### images 
	gulp images

Optimises all images within the `/src/images` folder. The folder structure inside will be maintained.
It is not included in the `watch` task as it tends to run before the image has been fully saved, causing an error which stops the task.

#### server 
	gulp server

Creates a browserSync instance and runs a local development server, loading the root directory.

The default port is 3000, therefore the local URL should be http://localhost:3000/. This port number can change if 2 or more servers are running simultaneously.

You must set the proxy URL for this task so that it can wrap the PHP server functionality provided by MAMP.

#### server-reload 
	gulp server-reload

Refreshes the browserSync instance created by the `server` task.

### Level 2 Tasks

#### watch 
	gulp watch

The `watch` task starts a process that will wait until changes are made to specific files and then runs tasks associated with them.

For this project, that means:
- Changes to `.scss` files cause `styles` task to be run and the styles to be dynamically inserted into the local server.
- Changes to `.js` files cause the `scripts` task to be run. Once all folders have been compiled the local server will be refreshed.
- Changes to PHP source files from the root folder and reloads the site as they change.

#### build 
	gulp build

Runs a full build process, compiling any CSS, JavaScript and image assets.

Uses `styles`, `scripts` and `images` tasks.

#### production-ready
	gulp production-ready

Transforms the, assumed to be staging, output in the `/dist` directory to be ready for deployment on a live production server.

Uses `create-revisions` tasks.

### Level 3 Tasks

#### local
    gulp local

Runs all tasks required for local development.

If the `--environment` flag is specified it will be purposefully overwritten to "development".

Uses `clean`, `build`, `server` and `watch` tasks.

#### staging 
	gulp staging

Runs all tasks required for deploying the output to a staging server.

If the `--environment` flag is specified it will be purposefully overwritten to "development".

Uses `clean` and `build` tasks.

#### live
	gulp live

Runs all tasks required for deploying the output to a live server.

If the `--environment` flag is specified it will be purposefully overwritten to "production".  

Uses `clean`, `build` and `production-ready` tasks.
