<?php
/*
 * Template Name: 1-jars-drc-downsell
 */
get_header();
while ( have_posts() ) : the_post();
?>

<section class="s1">
  <div class="container">
    <div class="steps">
      <div class="step">
        <b>STEP 1</b> ORDER APPROVED
      </div>
      <div class="step">
        <b>STEP 2</b> PRODUCT OPTIONS
      </div>
      <div class="step">
        <b>STEP 3</b> FINAL CONFIRMATION
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">
        <img src="<?php echo UPSELLS; ?>/img/dr.jpg" alt="" width="100%">
        <h5>Dr. Tess Mauricio</h5>
        <p>Board Certified Dermatologist and Founder of Orange County MD Skin Care</p>
      </div>
      <div class="col-md-8">
        <h2>Is 3  Jars Too Much? How About We Do This....!</h2>
        <p>Ok so 3 additional jars might be a bit much for you because you haven’t tried Rejuvenation Complex Cream for yourself yet. Completely understood! What I’d like to do is give you the opportunity to save big on just 1 additional jar. This way you can see just how magical this formula is before stocking up on larger amounts!</p>
        <p>Again, every single woman who's tried this cream before so far has been amazed by the results - but if for any reason at all you don’t love it as much as everyone else - just let me and my team know at any point in the next 90 days and we’ll give you your money back. Even if you’ve used all the jars! This is literally an “empty jar” guarantee. I’m only able to do this because I have total confidence in Rejuvenation Complex Cream and I know that you will fall in love, too! </p>
        <p>Click the button below</p>
      </div>
    </div>
  </div>
</section>

<section class="s2">
  <div class="container">
    <div class="row">
      <div class="col-md-5">
        <img src="<?php echo UPSELLS; ?>/img/lamer.jpg" alt="" width="100%">
      </div>
      <div class="col-md-7">
        <h2>How Would You Like To Add <br> <b>An Additional Jar at <span>75% Off?</span></b></h2>
        <h3>REGULAR PRICE: $120.00 <br> <span>INSTANT SAVINGS: $91.00</span></h3>
        <h4>YOUR PRICE: $29.00</h4>
      </div>
    </div>
    <a href="https://ocmd.co?wfocu-accept-link=yes" class="btn btn-yes">YES! Tess I want To Add 1 Jar To My Order And STILL Save Big</a>
    <a href="https://ocmd.co?wfocu-reject-link=yes" class="btn btn-no">NO THANKS, I UNDERSTAND THAT I WON'T SEE THIS OFFER AGAIN</a>
  </div>
</section>

<section class="s3">
  <div class="container">
    <div class="promise">
      <div class="img">
        <img src="<?php echo UPSELLS; ?>/img/guarantee.jpg" alt="">
      </div>
      <div class="text">
        <h2>My Promise To You</h2>
        <p>As “America’s Favorite Dermatologist” I developed this skin care line to deliver clinical-grade results to any woman who wants to look and feel her best. If for any reason you are unsatisfied with your purchase you can contact us for a full refund, no-questions asked, for a full 90 days. Simply contact us at <a href="mailto:support@ocmd.co" target="_blank">support@ocmd.co</a> or by calling <a href="tel:8663414691">(866) 341-4691</a> and one of my lovely customer care specialists will assist you in receiving a prompt and courteous refund.</p>
        <p>Sincerely, Dr. Tess Mauricio <br> Orange County MD</p>
      </div>
    </div>
  </div>
</section>

<?php
endwhile;
get_footer(); ?>