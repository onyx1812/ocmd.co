<?php
/*
 * Template Name: Contact
 */
get_header();
while ( have_posts() ) : the_post();



    $email = 'max@purpleadlab.com';
    $name = 'Max';
    $message = 'Hello world';

    $to = "onyx18121990@gmail.com";
    $subject = "Your order has been sent";
    $text =  $message;

    $headers = 'From: Thezense team <admin@ocmd.co>' . "\r\n" .
    'Reply-To: admin@ocmd.co' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

    $sending = mail($to, $subject, $text, $headers);

    if($sending) echo "Email sent!";



  ?>

<section class="page-contact">
  <div class="container">
    <h1 class="section-title">Contact Us</h1>
    <h3>Your Satisfaction Is Our Priority!</h3>
    <p class="text-center">We’d love to hear from you and answer any questions you might have about our products or services. <br>Feel free to contact us anytime!</p>
    <p>&nbsp;</p>
    <p>
      Give Us a Call: <br>
      America: <a href="tel:8772310127">(877) 231-0127</a>
    </p>
    <h4>Or Drop a message:</h4>
    <form class="contact row" id="contact">
      <div class="field col-sm-6">
        <label for="name">Name</label>
        <input type="text" name="name" id="name">
      </div>
      <div class="field col-sm-6">
        <label for="email">Email*</label>
        <input type="email" name="email" id="email" required>
      </div>
      <div class="field col-12">
        <label for="phone">Phone</label>
        <input type="tel" name="phone" id="phone">
      </div>
      <div class="field col-12">
        <label for="message">Message</label>
        <textarea name="message" id="message"></textarea>
      </div>
      <div class="field col-12">
        <input type="submit" value="Send">
      </div>
    </form>
  </div>
</section>

<?php
endwhile;
get_footer(); ?>