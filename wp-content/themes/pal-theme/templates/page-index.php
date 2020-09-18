<?php
/*
 * Template Name: Index
 */
get_header(); ?>

<section class="banner">
	<div class="container-fluid">
		<picture>
			<source media="(max-width: 767px)" srcset="<?php echo IMG.'/sara-banner-mob.jpg'; ?>">
			<source media="(min-width: 768px)" srcset="<?php echo IMG.'/sara-banner.jpg'; ?>">
			<img src="<?php echo IMG.'/sara-banner.jpg'; ?>"  width="100%">
		</picture>
	</div>
</section>

<section class="s1">
	<div class="container">
		<div class="row">
			<div class="col-sm-6 order-md-2">
				<h2 class="section-title-sm">Just Arrived - Lumia Platinum Eye Cream</h2>
				<p>Get that clear, smooth, younger looking skin around your eye area and feel fabulous every day.</p>
				<a href="/product/smoothn" class="btn btn-learn">Learn More</a>
			</div>
			<div class="col-sm-6">
				<img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/arrived.jpg'; ?>" alt="">
			</div>
		</div>
	</div>
</section>

<section class="s2">
	<div class="container">
		<div class="row">
			<div class="col-sm-6">
				<h2 class="section-title-sm">Just Arrived - Lumia Platinum Eye Cream</h2>
				<p>Get that clear, smooth, younger looking skin around your eye area and feel fabulous every day.</p>
				<a href="/product/awakn" class="btn btn-learn">Learn More</a>
			</div>
			<div class="col-sm-6">
				<img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/arrived2.jpg'; ?>" alt="">
			</div>
		</div>
	</div>
</section>

<section class="products-section" id="productsSection">
	<div class="container">
		<h2 class="section-title">Our Products</h2>
		<?php echo do_shortcode('[products ids="1301, 1303, 1307" limit="3" columns="3" ]'); ?>
	</div>
</section>

<section class="s3">
	<div class="container">
		<div class="row">
			<div class="col-sm-6 order-md-2">
				<h2 class="section-title-sm">Our Story</h2>
				<p>With passion for high-end cosmetics, and the vision to make it accessible to every man and woman, Lavie Labs invested years in developing the highest quality formulas focusing on the most recent research and premium ingredients mix. <br><br>By selling direct-to-consumer, we are able to cut off all middlemen and simply focus on quality and service for YOU - Our Customers!</p>
				<a href="/product/tightn" class="btn btn-learn">Learn more</a>
			</div>
			<div class="col-sm-6">
				<img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/arrived3.jpg'; ?>" alt="">
			</div>
		</div>
	</div>
</section>

<section class="why">
	<div class="container-fluid">
		<h2 class="section-title">Why SARA?</h2>
		<p>Here are just a few of the reasons you should consider and choose Lavie for your <br> daily Skin-Care routine. We focus on Quailty, we focus on Science, we focus on your <br> Experience, we focus on YOU!</p>
		<div class="row">
			<div class="col-xl-3 col-lg-4 col-sm-6"><img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/why_01.png'; ?>" alt="Eco Friendly"><span>Eco Friendly</span></div>
			<div class="col-xl-3 col-lg-4 col-sm-6"><img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/why_02.png'; ?>" alt="Dermatologies Recommended"><span>Dermatologies Recommended</span></div>
			<div class="col-xl-3 col-lg-4 col-sm-6"><img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/why_03.png'; ?>" alt="Cruelty Free"><span>Cruelty Free</span></div>
			<div class="col-xl-3 col-lg-4 col-sm-6"><img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/why_04.png'; ?>" alt="Healthy & Safe Ingredients"><span>Healthy & Safe Ingredients</span></div>
			<div class="col-xl-3 col-lg-4 col-sm-6"><img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/why_05.png'; ?>" alt="100% Tested"><span>100% Tested</span></div>
			<div class="col-xl-3 col-lg-4 col-sm-6"><img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/why_06.png'; ?>" alt="100% Satisfaction Guarantee"><span>100% Satisfaction Guarantee</span></div>
			<div class="col-xl-3 col-lg-4 col-sm-6"><img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/why_07.png'; ?>" alt="Natural Based"><span>Natural Based</span></div>
			<div class="col-xl-3 col-lg-4 col-sm-6"><img src="data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo IMG.'/why_08.png'; ?>" alt="Fits all Skin Types"><span>Fits all Skin Types</span></div>
		</div>
		<p>
			<a href="<?php the_permalink(1325);?>" class="btn btn-white">Need Help? Click Here</a>
		</p>
	</div>
</section>

<!-- <div id="cursor" style="position: fixed;height: 30px;width: 30px;border: 1px solid rgb(21, 21, 21);border-radius: 50%;box-shadow:0 0 4px #000 inset;z-index: 99999999999;"></div> -->

<?php get_footer();