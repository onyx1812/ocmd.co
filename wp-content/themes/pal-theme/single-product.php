<?php
get_header();
while ( have_posts() ) : the_post();
$id = get_the_ID();
?>

<?php if( !in_array($id, [1301, 1307]) ): ?>
  <section class="producto producto-default">
    <div class="container">
      <div class="row">
        <div class="col-md-8 order-md-2">
          <h1 class="producto-title"><?php the_title(); ?></h1>
          <?php echo $post->post_content; ?>
          <div class="product-price">
            <?php
              $product = wc_get_product( $id );
              echo '$'.$product->get_price().'/ea';
            ?>
          </div>
          <form class="cart" action="" method="post" enctype="multipart/form-data">
            <button type="submit" name="add-to-cart" value="<?php echo $id; ?>" class="single_add_to_cart_button button alt btn">Add to cart</button>
          </form>
        </div>
        <div class="col-md-4">
          <div class="producto-sidebar">
            <div class="gallery">
              <img class="gallery-img" src="<?php echo get_the_post_thumbnail_url(); ?>" width="100%">
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php else: ?>
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

  <?php if(get_the_ID() === 1301 ): ?>
  <section class="formula">
    <div class="container">
      <h2>Formula was born in three parts</h2>
      <p><b>1. Immediate impact -</b> this means seeing actual results within weeks, clients love quick results.</p>
      <p><b>2. Long-term care -</b> absorbs deeply into skins bottom layers, keeping balance and creating new, healthy tissue and cells.</p>
      <p><b>3. Jump-starting the body -</b> your skin will think it created the materials on its own, when the serum actually jump-started your body's own system.</p>
      <p>&nbsp;</p>
      <div class="row">
        <div class="col-md-6">
          <p><b>Serum is high in Retinol concentration.</b></p>
          <p>Quick skin lesson – retinol encourages the body’s production of collagen and elastin and both of these support the skin’s overall youthful look by adding moisture and diminishing wrinkles. As you get older, your body does not produce as much collagen or elastin. When the serum touches your skin, it penetrates the “dermis” layer, a gentle peeling will happen on the top layer of your skin which erases dead cells so new ones can grow. This rejuvenation will transform skin cells damaged by time, sun exposure or using harsh cosmetics. They begin to function like healthy skin cells once again.</p>
          <p>The serum is also proven to be an effective treatment for Acne. It prevents the pores from clogging and also prevents the formation of dead cells which leads to Acne. Retinol helps shed the dead skin cells and encourages new, healthy skin cells to grow creating a radiant glow and firmness in your skin. This reduces the discoloration of your skin and prevents black heads.</p>
          <p>Sometimes, Retinol has a “negative” side effect that when mixed with overexposure to sun it creates pigmentation. However, our unique formula and sophisticated scientific technology has “cancelled” this negative side-effect meaning Retinol can provide balance and support without danger of skin discoloration.</p>
          <p><b>Serum has a high concentration of natural Vitamin C.</b></p>
          <p>Normally, when we think of Vitamin C we see oranges and people trying to get over a cold. However, the Vitamin C in our serum releases when it touches the skin providing for deep penetration and provides youthful looking skin.</p>
        </div>
        <div class="col-md-6">
          <p><b>Formula is saturated in special (good) oils.</b></p>
          <p>There is a unique oil from Russia, the Obliphica oil, that originated in the Russian Obliphica fruit plant. This oil is known for its skin-preserving properties as it nourishes and helps heal many kinds of skin damage. This oil is rich in Vitamin C, Vitamin E, flavonoids and carotenoids. With strong antioxidant qualities, it helps protect the skin and delays the aging process. Obliphica oil is rich in essential fatty oils needed to rehab skin, provide nourishment and improve vitality.</p>
          <p>Richer, fuller, elastic skin that looks youthful and turns heads.</p>
          <p><b>Vitamin C with amino acids.</b></p>
          <p>Our serum enhances every layer of skin with a deep well of vitamin penetration. This means that the area of skin between the dermis layers and the epidermis is rich in protein creating a smoother, tighter skin than you have had in years!</p>
          <p>This serum formula has as many layers as your skin, so every part of your face (inside and out) is fuller, nourished, vibrant and youthful.</p>
        </div>
      </div>
    </div>
  </section>
  <style>
  .formula p{
    font-weight: 300;
    font-size: 16px;
    line-height: 125.9%;
    margin-bottom: 15px;
    color: #151515;
  }
  </style>
  <?php endif; ?>

  <?php if(get_the_ID() === 1301 ): ?>
    <section class="ingredients">
      <div class="container">
        <h2>Full List Of Ingredients</h2>
        <p class="text-center">WATER; PROPYLENE GLYCOL; TOCOPHERYL ACETATE; ISOPROPYL MYRISTATE; SQUALANE; PROPYLENE GLYCOL DICAPRYLATE / DICAPRATE; CYCLOPENTASILOXANE; GLYCERIN; RETINYL PALMITATE; POLYSORBATE 20; PHENOXYETHANOL; AMMONIUM ACRYLOYLDIMETHYLTAURATE / VP COPOLYMER; ALCOHOL; ISOPROPYL PALMITATE; POLYSORBATE 60; HIPPOPHAE RHAMNOIDES (SEA BUCKTHORN) OIL; SODIUM BENZOATE; DIMETHICONE; LECITHIN; BORAGO OFFICINALIS (BORAGE) SEED OIL; CAPRYLIC / CAPRIC TRIGLYCERIDE; ANIBA ROSAEODORA (ROSEWOOD) WOOD OIL; ETHYLHEXYLGLYCERIN; CITRUS AURANTIUM (BITTER ORANGE) PEEL OIL; CITRUS GRANDIS (GRAPEFRUIT) PEEL OIL; CYMBOPOGON CITRATUS (LEMONGRASS) LEAF OIL; MENTHA ARVENSIS LEAF OIL; SODIUM HYALURONATE; CARNOSINE; ASCORBYL PALMITATE; CHAMOMILLA RECUTITA (MATRICARIA) FLOWER EXTRACT; TOCOPHEROL; SILYBUM MARIANUM FRUIT EXTRACT; HEXANOYL DIPEPTIDE-3 NORLEUCINE ACETATE; LINALOOL; CITRAL; GERANIOL; LIMONENE; BENZYL BENZOATE.</p>
      </div>
    </section>
  <?php else: ?>
    <section class="ingredients">
      <div class="container">
        <h2>Full List Of Ingredients</h2>
        <p class="text-center">WATER; ISOPROPYL MYRISTATE; RICINUS COMMUNIS (CASTOR) SEED OIL; SQUALANE; CYCLOPENTASILOXANE; DIMETHICONE; CAPRYLIC/CAPRIC TRIGLYCERIDE; GLYCERIN; TOCOPHERYL ACETATE; SODIUM ACRYLATE / SODIUM ACRYLOYLDIMETHYL TAURATE COPOLYMER; PHENOXYETHANOL; PANTHENOL; RETINYL PALMITATE; ISOHEXADECANE; POLYSORBATE 20; TITANIUM DIOXIDE; MATRICARIA CHAMOMILLA (GERMAN CHAMOMILE) FLOWER WATER; SODIUM BENZOATE; CALENDULA OFFICINALIS (CALENDULA) FLOWER WATER; POLYSORBATE 80; PERSEA GRATISSIMA (AVOCADO) OIL; TRITICUM VULGARE (WHEAT) GERM OIL; FRAGRANCE; SORBITAN OLEATE; MALUS DOMESTICA (APPLE) FRUIT CELL CULTURE EXTRACT; ALCOHOL; PRUNUS DULCIS (SWEET ALMOND) OIL; ETHYLHEXYLGLYCERIN; BORAGO OFFICINALIS (BORAGE) SEED OIL; SODIUM HYALURONATE; LECITHIN; XANTHAN GUM; BENZYL ALCOHOL; CITRIC ACID; ASCORBYL PALMITATE; POTASSIUM SORBATE; LINALOOK, CITRONELLOL; GERANIOL, LIMONENE.</p>
      </div>
    </section>
  <?php endif; ?>

  <?php if(get_the_ID() !== 1301 ): ?>
  <section class="faq-section">
    <div class="container">
      <h2>Frequently Asked Questions</h2>
      <?php get_template_part( 'partials/mg', 'faq' ); ?>
    </div>
  </section>
  <?php endif; ?>
<?php endif ?>
<?php
endwhile;
get_footer(); ?>