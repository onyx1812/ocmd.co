<?php
/*
 * The template for displaying 404 pages (not found)
 */
get_header(); ?>

<section class="page404">
	<div class="container">
		<div class="bg-num">
			<h1>OOOPS! THE PAGE YOU’RE LOOKING FOR INS’T HERE</h1>
			<span>404</span>
		</div>
		<a href="<?php echo home_url(); ?>/" class="btn">Return Home</a>
	</div>
</section>

<?php get_footer(); ?>