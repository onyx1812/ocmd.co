<?php
/*
 * The template for displaying pages
 */
get_header();
while ( have_posts() ) : the_post(); ?>

<section class="page-default">
	<div class="container">
		<h1 class="section-title"><?php the_title(); ?></h1>
		<?php the_content(); ?>
	</div>
</section>

<?php
endwhile;
get_footer(); ?>