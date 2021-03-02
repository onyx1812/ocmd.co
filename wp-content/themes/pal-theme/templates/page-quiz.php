<?php
/*
 * Template Name: Quiz
 */
get_header();
while ( have_posts() ) : the_post();
?>

<section class="page-quiz">
  <div class="container">

    <h3>What is your age?</h3>
    <ul>
      <li><input type="radio" name="r1" id="r1_1" checked><label for="r1_1">Under 40</label></li>
      <li><input type="radio" name="r1" id="r1_2"><label for="r1_2">40 - 49</label></li>
      <li><input type="radio" name="r1" id="r1_3"><label for="r1_3">50 - 59</label></li>
      <li><input type="radio" name="r1" id="r1_4"><label for="r1_4">60 - 69</label></li>
      <li><input type="radio" name="r1" id="r1_5"><label for="r1_5">Over 70</label></li>
    </ul>

    <h3>What signs of aging do you want to improve? <br>(Select all that apply)</h3>
    <ul>
      <li><input type="radio" name="r2" id="r2_1" checked><label for="r2_1">Loose or crepey skin</label></li>
      <li><input type="radio" name="r2" id="r2_2"><label for="r2_2">Fine lines</label></li>
      <li><input type="radio" name="r2" id="r2_3"><label for="r2_3">Crows feet</label></li>
      <li><input type="radio" name="r2" id="r2_4"><label for="r2_4">Deep wrinkles or creases</label></li>
      <li><input type="radio" name="r2" id="r2_5"><label for="r2_5">Expression lines</label></li>
      <li><input type="radio" name="r2" id="r2_6"><label for="r2_6">Lines around the mouth</label></li>
    </ul>

    <h3>Why do you want Dermal Rejuvenation Cream?</h3>
    <ul>
      <li><input type="radio" name="r3" id="r3_1" checked><label for="r3_1">I want to get my spark back</label></li>
      <li><input type="radio" name="r3" id="r3_2"><label for="r3_2">I want to erase the signs of age that appear on my face</label></li>
      <li><input type="radio" name="r3" id="r3_3"><label for="r3_3">I want other people to notice youthful changes</label></li>
      <li><input type="radio" name="r3" id="r3_4"><label for="r3_4">I want a deep, lasting moisturizer</label></li>
      <li><input type="radio" name="r3" id="r3_5"><label for="r3_5">I want to stop the signs of aging from progressing any more</label></li>
    </ul>
    <a href="https://ocmd.co/campaign-4" class="link">Next Step</a>

  </div>
</section>


<?php
endwhile;
get_footer(); ?>