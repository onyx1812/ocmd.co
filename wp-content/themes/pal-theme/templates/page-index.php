<?php
/*
 * Template Name: Index
 */
get_header(); ?>

<?php

    // $email = 'max@purpleadlab.com';
    // $name = 'Max';
    // $message = 'Hello world';

    // $to = "onyx18121990@gmail.com";
    // $subject = "Your order has been sent";
    // $text =  $message;

    // $headers = 'From: Thezense team <admin@ocmd.co>' . "\r\n" .
    // 'Reply-To: admin@ocmd.co' . "\r\n" .
    // 'X-Mailer: PHP/' . phpversion();

    // $sending = mail($to, $subject, $text, $headers);

    // if($sending) echo "Email sent!";

 ?>

<section class="banner">
  <div class="container-fluid">
    <div class="row row-center">
      <div class="col-lg-7 order-md-2">
        <h2 class="section-title">Dr. Tess Mauricio</h2>
        <h4 class="section-subtitle">“You can now bring the same procedures that thousands of my patients use in my clinic to erase years of wrinkles to your home.”</h4>
        <p><span class="link link-def" style="text-decoration: none!important;">Board Certified Dermatologist, fellow of the American Academy of Dermatology</span></p>
      </div>
      <div class="col-lg-5 text-right"><img src="<?php IMG(); ?>/tessmauricio.jpg" alt="" width="400"></div>
    </div>
  </div>
</section>

<section class="s1">
  <div class="container">
    <div class="row row-center">
      <div class="col-lg-5">
        <h2 class="section-title-sm">Just Arrived <br> Rejuvenation <br> Complex Cream</h2>
        <p>Our #1 rated "age-defying" formula that's fast-acting with premium grade ingredients.</p>
      </div>
      <div class="col-lg-7">
        <img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/arrived.jpg'; ?>" alt="" width="770">
      </div>
    </div>
  </div>
</section>

<section class="s2">
  <div class="container">
    <div class="row row-center">
      <div class="col-sm-6 col-lg-5 order-md-2">
        <h2 class="section-title">Just Arrived – Deep Perfecting Face & Neck Serum</h2>
        <p>Reignite your skin's youth with the revolutionary Deep Perfecting Face & Neck Serum.</p>
      </div>
      <div class="col-sm-5 text-right">
        <img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/arrived2.jpg'; ?>" alt="" width="600">
      </div>
    </div>
  </div>
</section>

<section class="products-section" id="productsSection">
  <div class="container">
    <h2 class="section-title">Our Products</h2>
    <?php echo do_shortcode('[products ids="1301, 1307" limit="2" columns="2" ]'); ?>
  </div>
</section>

<section class="s3">
  <div class="container">
    <div class="row row-center">
      <div class="col-sm-6 order-md-2">
        <h2 class="section-title">The OCMD Story</h2>
        <p>Our founders saw women buying "high-end" cosmetics that were no better than a store brand knock off. We envisioned making high-quality, ethical, rejuvenating products available to every man and woman.</p>
        <p>After investing years into researching and developing our state-of-the-art products, we focused on formulas that combined the earth's goodness with premium ingredients.</p>
        <p>We decided to sell direct-to-consumer to save you money by cutting out the middleman.</p>
        <p>High-quality, effective products are just a click away.</p>
      </div>
      <div class="col-sm-6 col-md-5">
        <img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/arrived3.jpg'; ?>" alt="" width="680">
      </div>
    </div>
  </div>
</section>

<section class="why">
  <div class="container-fluid">
    <h2 class="section-title">Why OCMD?</h2>
    <p>Why purchase OCMD products instead of some supposedly "high-end" product with a brand-name? Here are 8 reasons:</p>
    <div class="row">
      <div class="col-xl-3 col-lg-4 col-sm-6"><img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/why_06.png'; ?>" ><span>100% Satisfaction Guarantee</span></div>
      <div class="col-xl-3 col-lg-4 col-sm-6"><img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/why_02.png'; ?>" ><span>Recommended by Top Dermatologists</span></div>
      <div class="col-xl-3 col-lg-4 col-sm-6"><img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/why_04.png'; ?>" ><span>Safe, Healthy Ingredients</span></div>
      <div class="col-xl-3 col-lg-4 col-sm-6"><img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/why_05.png'; ?>" ><span>100% Tested</span></div>
      <div class="col-xl-3 col-lg-4 col-sm-6"><img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/why_03.png'; ?>" ><span>Cruelty Free</span></div>
      <div class="col-xl-3 col-lg-4 col-sm-6"><img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/why_01.png'; ?>" ><span>Eco Friendly</span></div>
      <div class="col-xl-3 col-lg-4 col-sm-6"><img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/why_08.png'; ?>" ><span>Fits all Skin Types</span></div>
      <div class="col-xl-3 col-lg-4 col-sm-6"><img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/why_07.png'; ?>" ><span>Organic-Based</span></div>
    </div>
    <p>
      <a href="<?php the_permalink(1325);?>" class="btn">Need Help? Click Here</a>
    </p>
  </div>
</section>

<section class="who">
  <div class="container">
    <h2 class="section-title">Who is Dr. Tess Mauricio</h2>
    <p>Dr. Tess Mauricio is a Board Certified Dermatologist, fellow of the American Academy of Dermatology, graduate of Stanford University School of Medicine, and a Summa Cum Laude graduate of The University of California San Diego. </p>
    <p>She is the founder of M Beauty Clinic by Dr Tess in San Diego and Beverly Hills. A Past President of The San Diego Society for Dermatologic Surgery, she is the recipient of the UCSD Warren College Distinguished Alumni Award and Hollywood Daytime Beauty Award for Outstanding Achievement in Medicine.</p>
    <p>Dr. Tess is a favorite medical expert on TV with regular network appearances on NBC’s California Live, The Dr Oz Show, The Talk, The Real, Dr Phil, The Rachael Ray Show, Fox News, and The Doctors. Dr Tess produced 6 seasons of “The Dr Tess Show” and her family’s reality show, “All in Family with Dr Tess,” premieres its 3rd season in 2020.Dr Tess believes that we should all do and be our very best, and always be a blessing to others by sharing your stories and unique gifts.</p>
  </div>
</section>

<?php get_footer();