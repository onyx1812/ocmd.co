<?php get_header(); ?>

  <?php while ( have_posts() ) : the_post(); ?>
    <div class="container">
      <div class="single-content">
        <h1><?php the_title(); ?></h1>
        <?php the_content(); ?>
        <p class="text-center"><a class="link" title="Home page OCMD" href="<?php echo home_url(); ?>">< Go to Home page</a></p>
      </div>
    </div>

  <?php endwhile; ?>

<?php get_footer(); ?>