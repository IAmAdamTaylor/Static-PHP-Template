<?php

/**
 * The entry point for all requests.
 * Sets up some global vars and then routes the request 
 * in the simplest manner possible.
 */

date_default_timezone_set('Europe/London');

require_once 'load.php';

if ( isset( $_GET['q'] ) ) {
	$q = $_GET['q'];
} else {
	// If path is not set, this file has been requested -> show home page
	$q = 'home';
}

// Strip any trailing slashes
$q = rtrim( $q, '/\\' );

/**
 * Set up any 301 redirects.
 * OLD_URL_PATH - The path to be redirected.
 * 		E.g. to redirect 'http://domain.com/foobar', use 'foobar'
 * REDIRECT_TO - Any absolute URL, do not use relative paths.
 * 		E.g. http://example.com to redirect to an external site.
 * 		Start the URL with a slash, e.g. '/privacy-policy' to redirect to an internal page.
 * @var array
 */
$redirects = array(
	// 'OLD_URL_PATH' => 'REDIRECT_TO',
);

$q_filepath = ABS_PATH . 'page-templates/' . $q . '.php';

if ( file_exists( $q_filepath ) && is_readable( $q_filepath ) && '__template' !== $q ) {

	// Add to the global meta object
	$meta->slug = $q;
	include_once $q_filepath;

} else if ( isset( $redirects[ $q ] ) ) {

	http_response_code(301);
	header( "Location: " . $redirects[ $q ], true, 301 );
	exit(); 

} else {

	$meta->slug = '404';

	// Send a 404 header
	http_response_code(404);
	include_once ABS_PATH . 'page-templates/404.php';

}
