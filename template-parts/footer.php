<?php

/**
 * The footer of the site.
 */

global $meta;

?>

	<footer class="site-footer" role="contentinfo">

		<div class="container">

			<p>
				&copy; Copyright 
				<a href="TODO:CLIENT_URL" target="_blank" rel="noopener">TODO:CLIENT_NAME</a>
				<?php echo date('Y'); ?>
			</p>

			<p>
				<a href="/privacy-policy/">Privacy Policy</a>
			</p>

			<p>
				<a href="https://www.core-marketing.co.uk/" target="_blank" rel="noopener">Crafted by Core</a>
			</p>

		</div> <!-- /.flex container -->
	</footer> <!-- /.site-footer -->

</div> <!-- /.body-wrapper -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>jQuery.noConflict();</script>
<script src="/dist/<?php echo get_revision('js/footer.min.js'); ?>"></script>

<?php
	// Conditionally load the Google Maps library and initialiser
	// The API key will need to be generated and locked down to the site referer
?>
<?php if ( 'TODO:PAGE_SLUG' == $meta->slug ): ?>
	<script src="https://maps.googleapis.com/maps/api/js?key=TODO:API_KEY"></script>
	<script src="/dist/<?php echo get_revision('js/google-maps.min.js'); ?>"></script>
<?php endif; ?>

</body>
</html>
