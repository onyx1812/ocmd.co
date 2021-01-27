<?php
/*
 * Template Name: Camp 3
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
                  <?php product_offer(1671, 1, false); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_offer(1673, 6, true); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_offer(1672, 3, false); ?>
                </div>
              </div>
            </div>
            <div class="products-tabs__tab tab2">
              <div class="row">
                <div class="col-12 col-lg-4">
                  <?php product_offer(1674, 1, false); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_offer(1676, 6, true); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_offer(1675, 3, false); ?>
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