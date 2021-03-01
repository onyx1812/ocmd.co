<?php
/*
 * Template Name: Quiz 49
 */
get_header();
while ( have_posts() ) : the_post();
?>

<section class="page-quiz">
  <div class="container">

    <label for="">What is your age?</label>
    <input type="radio" name="r1" id="r1_1"><label for="r1_1">Under 40</label>
    <input type="radio" name="r1" id="r1_1"><label for="r1_1">40 - 49</label>
    <input type="radio" name="r1" id="r1_1"><label for="r1_1">50 - 59</label>
    <input type="radio" name="r1" id="r1_1"><label for="r1_1">60 - 69</label>
    <input type="radio" name="r1" id="r1_1"><label for="r1_1">Over 70</label>

    <h3>What signs of aging do you want to improve? (Select all that apply)</h3>
    <input type="radio" name="r2" id="r2_1"><label for="r2_1">Loose or crepey skin</label>
    <input type="radio" name="r2" id="r2_1"><label for="r2_1">Fine lines</label>
    <input type="radio" name="r2" id="r2_1"><label for="r2_1">Crows feet</label>
    <input type="radio" name="r2" id="r2_1"><label for="r2_1">Deep wrinkles or creases</label>
    <input type="radio" name="r2" id="r2_1"><label for="r2_1">Expression lines</label>
    <input type="radio" name="r2" id="r2_1"><label for="r2_1">Lines around the mouth</label>


    <h3>Why do you want Dermal Rejuvenation Cream?</h3>
    <input type="radio" name="r3" id="r3_1"><label for="r3_1">I want to get my spark back</label>
    <input type="radio" name="r3" id="r3_2"><label for="r3_2">I want to erase the signs of age that appear on my face</label>
    <input type="radio" name="r3" id="r3_3"><label for="r3_3">I want other people to notice youthful changes</label>
    <input type="radio" name="r3" id="r3_4"><label for="r3_4">I want a deep, lasting moisturizer</label>
    <input type="radio" name="r3" id="r3_5"><label for="r3_5">I want to stop the signs of aging from progressing any more</label>

    <a href="https://ocmd.co/campaign-4">Next Step</a>

  </div>
</section>

<?php
endwhile;
get_footer(); ?>