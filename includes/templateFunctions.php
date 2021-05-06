<?php

/**
 * Helper functions for loading templates.
 */

/**
 * Include a partial from the 'template-part/' folder.
 * @param  string $filename The file name of the partial.
 *                          If inside a subfolder include the folder name, e.g.
 *                          get_template_part( 'header/social' );
 */
function get_template_part( $filename ) {
	if ( !preg_match( '#^template-parts/#', $filename ) ) {
		$filename = 'template-parts/' . $filename;
	}

	if ( !preg_match( '#\.php$#', $filename ) ) {
		$filename .= '.php';
	}

	$filename = ABS_PATH . $filename;

	if ( file_exists( $filename ) && is_readable( $filename ) ) {
		include $filename;
	}
}

/**
 * Load the header partial.
 */
function get_header() {
	get_template_part( 'header' );
}

/**
 * Load the footer partial.
 */
function get_footer() {
	get_template_part( 'footer' );
}

/**
 * Include an assset file into the main document.
 * The file will only be included if it exists.
 * @param  string $file_path The path to the asset, including extenstion.
 *                           If it is contained in a subfolder this folder name 
 *                           should be passed as part of this parameter.
 * @return string
 */
function include_asset( $file_path ) {
	$file_path = ABS_PATH . $file_path;

	if ( file_exists( $file_path ) && is_readable( $file_path ) ) {
		include $file_path;
	}
}
