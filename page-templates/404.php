<?php

/**
 * The 404 page of the site.
 */

$meta->title = '404 Page Not Found';
$meta->description = 'We couldn\'t find the page you were looking for.';

get_header();

?>

<main class="site-main" role="main">
	<article>
		
		<div class="container">

			<h1 class="">404<br>Page Not Found</h1>				
			<div class="s-flow-content">

				<p>We couldn't find the page you were looking for.</p>
				<p>If you typed the URL, please check it for spelling mistakes. Alternatively, you can try refreshing the page.</p>
				<p>
					<a href="/">&lt; Back to Home</a>
				</p>

			</div> <!-- /.s-flow-content -->

		</div> <!-- /.container -->

	</article>
</main> <!-- /.site-main -->

<?php get_footer(); ?>
