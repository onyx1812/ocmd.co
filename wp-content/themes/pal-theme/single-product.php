<?php
get_header();
while ( have_posts() ) : the_post(); ?>

<section class="page-product">
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

<section class="settings">
	<div class="container-fluid">
		<h2><?php the_field('s_title'); ?></h2>
		<div class="row">
			<div class="col-md-4 order-md-2">
				<img src="<?php the_field('s_image'); ?>" alt="" width="100%;">
			</div>

		<?php if( have_rows('s_items_left') ): ?>
			<div class="col-md-4">
				<?php while( have_rows('s_items_left') ): the_row(); ?>
				<div class="settings-list settings-list--right">
					<img src="<?php the_sub_field('img'); ?>" alt="">
					<div>
						<h4><?php the_sub_field('title'); ?></h4>
						<p><?php the_sub_field('text'); ?></p>
					</div>
				</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>

		<?php if( have_rows('s_items_right') ): ?>
			<div class="col-md-4 order-md-2">
				<?php while( have_rows('s_items_right') ): the_row(); ?>
				<div class="settings-list">
					<img src="<?php the_sub_field('img'); ?>" alt="">
					<div>
						<h4><?php the_sub_field('title'); ?></h4>
						<p><?php the_sub_field('text'); ?></p>
					</div>
				</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>

		</div>
	</div>
</section>

<section class="reviews">
	<div class="container-fluid">
		<h2>Thousands Of Happy Customers</h2>
		<div class="slider slider-reviews" id="sliderReviews">
			<div class="item"><div class="box"><img src="<?php echo IMG.'/r1.jpg'; ?>" alt=""></div></div>
			<div class="item"><div class="box"><img src="<?php echo IMG.'/r2.jpg'; ?>" alt=""></div></div>
			<div class="item"><div class="box"><img src="<?php echo IMG.'/r3.jpg'; ?>" alt=""></div></div>
			<div class="item"><div class="box"><img src="<?php echo IMG.'/r3.jpg'; ?>" alt=""></div></div>
		</div>
	</div>
</section>

<section class="ingredients">
	<div class="container">
		<h2>Full List Of Ingredients</h2>
		<p class="text-center">WATER; ISOPROPYL MYRISTATE; RICINUS COMMUNIS (CASTOR) SEED OIL; SQUALANE; CYCLOPENTASILOXANE; DIMETHICON; CAPRYLIC/CAPRIC TRIGLYCERIDE; GLYCERIN; TOCOPHERYL ACETATE; SODIUM ACRYLATE / SODIUM ACRYLOYLDIMETHYL TAURATE COPOLYMER; PANTHENOL; RETINYL PALMITATE; ISOHEXADECANE; POLYSORBATE 20; PHENOXYETHANOL; TITANIUM DIOXIDE; MATRICARIA CHAMOMILLA (GERMAN CHAMOMILE) FLOWER WATER; CALENDULA OFFICINALIS (CALENDULA) FLOWER WATER; POLYSORBATE 80; PERSEA GRATISSIMA (AVOCADO) OIL; TRITICUM VULGARE (WHEAT) GERM OIL; FRAGRANCE; SORBITAN OLEATE; MALUS DOMESTICA (APPLE) FRUIT CELL CULTURE EXTRACT; ALCOHOL; PRUNUS DULCIS (SWEET ALMOND) OIL; ETHYLHEXYLGLYCERIN; BORAGO OFFICINALIS (BORAGE) SEED OIL; SODIUM HYALURONATE; LECITHIN; XANTHAN GUM; BENZYL ALCOHOL; CITRIC ACID; ASCORBYL PALMITATE; SODIUM BENZOATE; POTASSIUM SORBATE</p>
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