<?php get_header(); ?>

  <?php while ( have_posts() ) : the_post(); ?>
    <div class="container">
      <div class="single-content">
        <h1><?php the_title(); ?></h1>
        <?php the_content(); ?>
      </div>
    </div>
<style>
.single-content {
  padding: 60px 0;
}
.single-content h1 {
  font-size: 50px;
  font-weight: 700;
  margin-bottom: 30px;
}
.single-content p, .single-content li {
  font-size: 18px;
  color: #1f1f1f;
  margin-bottom: 15px;
}
.single-content ol {
  list-style: decimal outside;
  padding: 10px 30px;
}
</style>
  <?php endwhile; ?>

<?php get_footer(); ?>