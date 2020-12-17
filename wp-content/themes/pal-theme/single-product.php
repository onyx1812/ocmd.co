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
  <div class="container">
    <h2>Thousands Of Happy Customers</h2>
    <div class="row" >
      <div class="col-md-4">
        <div class="box">
          <header>
            <img src="<?php echo IMG(); ?>/rev1.png" alt="">
            <h3>Marta Stans</h3>
            <span>Alabama, USA</span>
            <ul class="star-rating"><li class="star-f"></li><li class="star-f"></li><li class="star-f"></li><li class="star-f"></li><li class="star-f"><i></i></li></ul>
          </header>
          <h4>Just feels luxurious!</h4>
          <p>I saw a reduction in my eye craws appearance after only 4 weeks of using it. definitely going to order again.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="box">
          <header>
            <img src="<?php echo IMG(); ?>/rev2.png" alt="">
            <h3>Ann Wilkock</h3>
            <span>FL, USA</span>
            <ul class="star-rating"><li class="star-f"></li><li class="star-f"></li><li class="star-f"></li><li class="star-f"></li><li class="star-f"><i></i></li></ul>
          </header>
          <h4>Lovely Cream! </h4>
          <p>All I can say is "goodbye" to the rest of my creams :) This one is simply an "all-in-one" jar - my skin feels moisturized throughout the day, and I can definitely start seeing improvement around my eyes and forehead.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="box">
          <header>
            <img src="<?php echo IMG(); ?>/rev3.png" alt="">
            <h3>Brenda Wall</h3>
            <span>Liverpool, UK</span>
            <ul class="star-rating"><li class="star-f"></li><li class="star-f"></li><li class="star-f"></li><li class="star-f"></li><li class="star-h"><i></i></li></ul>
          </header>
          <h4>Awesome!</h4>
          <p>After about a week of using it my husband started complimenting my look :) with our marriage routine - haven't heard that in a long time lol and all I changed was using this cream. I'm sold for good.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="ingredients">
  <div class="container">
    <h2>Full List Of Ingredients</h2>
    <p class="text-center">WATER; ISOPROPYL MYRISTATE; RICINUS COMMUNIS (CASTOR) SEED OIL; SQUALANE; CYCLOPENTASILOXANE; DIMETHICONE; CAPRYLIC/CAPRIC TRIGLYCERIDE; GLYCERIN; TOCOPHERYL ACETATE; SODIUM ACRYLATE / SODIUM ACRYLOYLDIMETHYL TAURATE COPOLYMER; PHENOXYETHANOL; PANTHENOL; RETINYL PALMITATE; ISOHEXADECANE; POLYSORBATE 20; TITANIUM DIOXIDE; MATRICARIA CHAMOMILLA (GERMAN CHAMOMILE) FLOWER WATER; SODIUM BENZOATE; CALENDULA OFFICINALIS (CALENDULA) FLOWER WATER; POLYSORBATE 80; PERSEA GRATISSIMA (AVOCADO) OIL; TRITICUM VULGARE (WHEAT) GERM OIL; FRAGRANCE; SORBITAN OLEATE; MALUS DOMESTICA (APPLE) FRUIT CELL CULTURE EXTRACT; ALCOHOL; PRUNUS DULCIS (SWEET ALMOND) OIL; ETHYLHEXYLGLYCERIN; BORAGO OFFICINALIS (BORAGE) SEED OIL; SODIUM HYALURONATE; LECITHIN; XANTHAN GUM; BENZYL ALCOHOL; CITRIC ACID; ASCORBYL PALMITATE; POTASSIUM SORBATE; LINALOOK, CITRONELLOL; GERANIOL, LIMONENE.</p>
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