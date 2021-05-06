<?php

/**
 * Header for the site.
 */

global $meta;

?><!DOCTYPE html>
<html class="no-js" lang="en-gb">
<head>
	<?php // Don't index certain pages ?>
	<?php if ( isset( $meta->noindex ) && $meta->noindex ): ?>
		<meta name="robots" content="noindex">
	<?php endif; ?>

	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?php echo $meta->title; ?> | TODO:SITE_TITLE</title>
	<meta name="description" content="<?php echo $meta->description; ?>">

	<!-- Start Favicons -->
	
	<!-- End Favicons -->

	<link rel="stylesheet" type="text/css" href="/dist/<?php echo get_revision('css/screen.min.css'); ?>">
	<link rel="stylesheet" type="text/css" media="print" href="/dist/<?php echo get_revision('css/print.min.css'); ?>">

	<script type="text/javascript" src="/dist/<?php echo get_revision('js/head.min.js'); ?>"></script>

	<!-- Start Google Analytics -->
	
	<!-- End Google Analytics -->
</head>
<body class="page page-<?php echo $meta->slug; ?>">
	<div class="body-wrapper">
		
		<header class="site-header" role="banner">
			<div class="container">
					
				<a href="/">
					<div class="site-logo">
						<?php include_asset('static/logo.svg'); ?>
						<span class="sr-only">TODO:SITE_TITLE</span>
					</div>
				</a>

				<nav class="site-nav"  aria-labelledby="site-nav-label">
					<h2 id="site-nav-label" class="sr-only">Main Menu</h2>

					<ul class="site-nav__list list-horizontal">
						<li class="<?php echo ( 'home' == $meta->slug ? 'is-current' : '' ); ?>"> 
							<a href="/">
								Home
							</a>
						</li>
						<li class="<?php echo ( 'contact' == $meta->slug ? 'is-current' : '' ); ?>">
							<a href="/contact/">
								Contact
							</a>
						</li>
					</ul>
				</nav>
				
			</div> <!-- /.container -->
		</header> <!-- /.site-header -->
