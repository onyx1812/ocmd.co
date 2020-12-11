<?php
/*
 * Template Name: FAQ
 */
get_header();
while ( have_posts() ) : the_post(); ?>

<section class="page-faq">
  <div class="container">
    <h1 class="section-title">OCMD FAQ’s</h1>
    <h2 class="section-title">Tracking, Shipping, Delivery and More</h2>
    <div class="box">
      <h2>Shipping & Delivery FAQ</h2>
      <ul>
        <li>OCMD accepts all major credit cards as well as PayPal. All payments are processed upon ordering, COD deliveries are not accepted.</li>
        <li>Processing and shipping for orders generally takes 1-3 business days, but federal holidays may cause this time to be extended.</li>
        <li>Orders are shipped locally from our warehouses in Tampa and Salt Lake City. </li>
        <li>All billing information must match the credit card information for security purposes. If shipping and billing details differ, the order will not be processed. Please avoid using any abbreviations unless that is exactly how it appears on your statement.</li>
        <li>Packages do not ship from our distribution centers on weekends. All orders process and ship Monday – Friday, including federal holidays</li>
      </ul>
    </div>

    <div class="box">
      <h2>Tracking Information</h2>
      <ul>
        <li>Once your order ships, you will receive a confirmation email containing shipment details as well as tracking number. For additional shipping & tracking information you can contact us at <a href="mailto:support@ocmd.co">support@ocmd.co</a></li>
      </ul>
    </div>

    <div class="box">
      <h2>Product FAQs</h2>
      <div class="faqs">
        <div class="faq">
          <div class="question">How long does it take to see results?</div>
          <div class="answer">For most people, visible results will be seen within 2-4 weeks in most cases.</div>
        </div>
        <div class="faq">
          <div class="question">Was Rejuvenation tested on animals?</div>
          <div class="answer">Absolutely not! OCMD respects and honors all living things, none of our products are ever tested on animals.</div>
        </div>
        <div class="faq">
          <div class="question">Is Rejuvenation suitable for all skin types?</div>
          <div class="answer">Yes, it is suitable for dry, oily, normal and even skin with a combination of issues. It is primarily formulated with ingredients found within the human body or nature and is highly unlikely to cause any reaction or breakout-causing symptoms.</div>
        </div>
        <div class="faq">
          <div class="question">How does Rejuvenation reduce wrinkles, lift the skin and brighten complexion?</div>
          <div class="answer">It uses a combination of 3 different sizes of Hyaluronic Acid molecules and 17 carefully selected active ingredients, including apple stem cell extracts, vitamin liposomes, and natural compounds like lactic acid. Our unique formula gently exfoliates the epidermis (outer layer of skin) and penetrates into the lower skin levels to optimize product efficiency. The Shrunken Hyaluronic Acid molecules trap moisture. The Swiss Apple Stem Cells increase skin stem cell regeneration by up to 80% and reduce the damage of UV sun light on skin stem cells by as much as 46%. The vitamin liposomes nourish the skin. Other active ingredients trigger and enhance Elastin & Collagen production. The result of all this is a tighter, smoother skin and a brighter complexion.</div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
endwhile;
get_footer(); ?>