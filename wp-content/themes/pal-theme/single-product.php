<?php
get_header();
while ( have_posts() ) : the_post();
$id = get_the_ID();
?>
<section class="producto">
  <div class="container">
    <div class="row">
      <div class="col-md-8 order-md-2">
        <h1 class="producto-title"><?php the_title(); ?></h1>
        <?php echo get_field('description'); ?>
        <div class="producto-tabs">
          <ul class="producto-tabs__nav">
            <li data-tab="tab1" class="active">One Time Purchase</li>
            <li data-tab="tab2">Subscribe and Save</li>
          </ul>
          <div class="producto-tabs__tab tab1 active">
            <div class="row">
              <?php if ($id === 1307): ?>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1476, false); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1479, true); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1478, false); ?>
                </div>
              <?php elseif ($id === 1303): ?>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1486, false); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1488, true); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1487, false); ?>
                </div>
              <?php elseif ($id === 1301): ?>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1489, false); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1491, true); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1490, false); ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
          <div class="producto-tabs__tab tab2">
            <div class="row">
              <?php if ($id === 1307): ?>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1504, false); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1506, true); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1505, false); ?>
                </div>
              <?php elseif ($id === 1303): ?>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1507, false); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1509, true); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1508, false); ?>
                </div>
              <?php elseif ($id === 1301): ?>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1510, false); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1512, true); ?>
                </div>
                <div class="col-12 col-lg-4">
                  <?php product_custom(1511, false); ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <!-- END .producto-tabs -->
      </div>
      <div class="col-md-4">
        <div class="producto-sidebar">
          <?php $images = get_field('gallery'); if($images): ?>
          <div class="gallery">
            <img class="gallery-img" src="<?php echo esc_url($images[0]['url']); ?>" width="100%">
            <ul class="gallery-nav">
              <?php foreach( $images as $image ): ?>
              <li data-src="<?php echo esc_url($image['url']); ?>">
                <img src="<?php echo esc_url($image['sizes']['thumbnail']); ?>">
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>
          <div class="solutions">
            <h4 class="solutions-title"><span><?php the_field('solution_title'); ?></span></h4>
            <?php if( have_rows('solution_list') ): ?>
            <ul class="solutions-list">
              <?php while( have_rows('solution_list') ): the_row(); ?>
                <li>
                  <img src="<?php the_sub_field('image'); ?>">
                  <?php the_sub_field('text'); ?>
                 </li>
              <?php endwhile; ?>
            </ul>
          <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="page-product" style="display: none;">
  <div class="container-fluid">
    <?php the_content(); ?>
  </div>
</section>

<?php if( have_rows('cr_images') ): ?>
<section class="customers">
  <div class="container">
    <h2>Customers Results</h2>
    <div class="slider slider-results" id="sliderResults">
      <?php while( have_rows('cr_images') ): the_row(); ?>
        <div class="item"><img src="<?php echo get_sub_field('image'); ?>" alt=""></div>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if( have_rows('s_items') ): ?>
<section class="settings">
  <div class="container">
    <h2><?php the_field('s_title'); ?></h2>
    <div class="row">
      <?php while( have_rows('s_items') ): the_row(); ?>
      <div class="col-md-4">
        <img src="<?php the_sub_field('img'); ?>" alt="">
        <h4><?php the_sub_field('title'); ?></h4>
        <p><?php the_sub_field('text'); ?></p>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="reviews">
  <div class="container-fluid">
    <h2>Thousands Of Happy Customers</h2>
    <div class="slider slider-reviews" id="sliderReviews">
      <div class="item"><div class="box"><img src="<?php echo IMG.'/r1.jpg'; ?>" alt=""></div></div>
      <div class="item"><div class="box"><img src="<?php echo IMG.'/r2.jpg'; ?>" alt=""></div></div>
      <div class="item"><div class="box"><img src="<?php echo IMG.'/r3.jpg'; ?>" alt=""></div></div>
      <div class="item"><div class="box"><img src="<?php echo IMG.'/r3.jpg'; ?>" alt=""></div></div>
    </div>
  </div>
</section>

<section class="ingredients">
  <div class="container">
    <h2>Full List Of Ingredients</h2>
    <p class="text-center">WATER; ISOPROPYL MYRISTATE; RICINUS COMMUNIS (CASTOR) SEED OIL; SQUALANE; CYCLOPENTASILOXANE; DIMETHICON; CAPRYLIC/CAPRIC TRIGLYCERIDE; GLYCERIN; TOCOPHERYL ACETATE; SODIUM ACRYLATE / SODIUM ACRYLOYLDIMETHYL TAURATE COPOLYMER; PANTHENOL; RETINYL PALMITATE; ISOHEXADECANE; POLYSORBATE 20; PHENOXYETHANOL; TITANIUM DIOXIDE; MATRICARIA CHAMOMILLA (GERMAN CHAMOMILE) FLOWER WATER; CALENDULA OFFICINALIS (CALENDULA) FLOWER WATER; POLYSORBATE 80; PERSEA GRATISSIMA (AVOCADO) OIL; TRITICUM VULGARE (WHEAT) GERM OIL; FRAGRANCE; SORBITAN OLEATE; MALUS DOMESTICA (APPLE) FRUIT CELL CULTURE EXTRACT; ALCOHOL; PRUNUS DULCIS (SWEET ALMOND) OIL; ETHYLHEXYLGLYCERIN; BORAGO OFFICINALIS (BORAGE) SEED OIL; SODIUM HYALURONATE; LECITHIN; XANTHAN GUM; BENZYL ALCOHOL; CITRIC ACID; ASCORBYL PALMITATE; SODIUM BENZOATE; POTASSIUM SORBATE</p>
  </div>
</section>

<section class="faq-section">
  <div class="container">
    <h2>Frequently Asked Questions</h2>
    <?php get_template_part( 'partials/mg', 'faq' ); ?>
  </div>
</section>

<?php
endwhile;
get_footer(); ?>