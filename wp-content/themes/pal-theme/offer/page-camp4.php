<?php
/*
 * Template Name: Camp 4
 */
get_header();
while ( have_posts() ) : the_post();
?>

  <?php get_template_part( 'offer/partials/offer', 'sold' ); ?>

  <section class="products">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <?php get_template_part( 'offer/partials/offer', 'sidebar' ); ?>
        </div>
        <div class="col-md-8">

          <?php get_template_part( 'offer/partials/offer', 'info' ); ?>

          <div class="products-tabs">
            <ul class="products-tabs__nav">
              <li data-tab="tab1" class="active">One Time Purchase</li>
              <li data-tab="tab2">Subscribe and Save</li>
            </ul>
            <div class="products-tabs__tab tab1 active">
              <div class="row">
                <div class="col-12 col-lg-4">
                  <?php product_offer(1677, 1, false); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_offer(1678, 6, true); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_offer(1679, 3, false); ?>
                </div>
              </div>
            </div>
            <div class="products-tabs__tab tab2">
              <div class="row">
                <div class="col-12 col-lg-4">
                  <?php product_offer(1680, 1, false); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_offer(1682, 6, true); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_offer(1681, 3, false); ?>
                </div>
              </div>
            </div>
          </div>
          <!-- END .products-tabs -->

        </div>
      </div>
      <?php get_template_part( 'offer/partials/offer', 'promise' ); ?>
    </div>
  </section>

  <?php get_template_part( 'offer/partials/offer', 'details' ); ?>
  <?php get_template_part( 'offer/partials/offer', 'testi' ); ?>
  <?php get_template_part( 'offer/partials/offer', 'faq' ); ?>
  <?php get_template_part( 'offer/partials/offer', 'footer' ); ?>

<?php
endwhile;
get_footer(); ?>