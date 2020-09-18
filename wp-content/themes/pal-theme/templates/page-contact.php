<?php
/*
 * Template Name: Contact
 */
get_header();
while ( have_posts() ) : the_post(); ?>

<section class="page-contact">
	<div class="container">
		<h1 class="section-title">Contact Us</h1>
		<h3>Your Satisfaction Is Our Priority!</h3>
		<p class="text-center">We’d love to hear from you and answer any questions you might have about our products or services. <br>Feel free to contact us anytime!</p>
		<p>&nbsp;</p>
		<p>
			Give Us a Call: <br>
			Europe: <a href="tel:+442039847994">+44 20 3984 7994</a> <br>
			America, Asia, Africa: <a href="tel:+13152774969">+1 315 277 4969</a>
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