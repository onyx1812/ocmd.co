		<footer class="main-footer">
			<div class="container">
				<h2 class="section-title">Subscribe our newsletters</h2>
				<p class="text-center">Receive Updates about New Products and Special Deals.</p>
				<form class="sform" id="sForm">
					<input type="email" name="s_email" id="s_email" placeholder="Your Email Adress">
					<input type="submit" value="subscribe">
				</form>
				<div class="row">
					<div class="col">
						<a href="<?php echo home_url(); ?>" class="logo"><img src="<?php echo IMG.'/logo.svg'; ?>" alt=""></a>
					</div>
					<div class="col">
						<p class="call">
							Need Help? Contact Us 24/7 <br>
							US & CA: <a href="tel:+13152774969">+1 (315) 277-4969</a> <br>
							UK & EU: <a href="tel:+442039847994">+44 (20) 3984-7994</a> <br><br>
							<a href="mailto:support@sara7skin.com">support@sara7skin.com</a> <br><br>
							US: 144-42 Jewel Avenue, <br>
							FLUSHING, NY 11367 <br><br>
							ISRAEL: 6 Moshe Hess St., Lod
						</p>
					</div>
					<div class="col">
						<h4>Quick links</h4>
						<?php
							wp_nav_menu(array(
								'menu'				=> 'footer-menu',
								'container'			=> '',
								'container_class'	=> '',
								'menu_class'		=> 'footer-menu',
								'container_id'		=> ''
							));
						?>
					</div>
				</div>
			</div>
		</footer>

		<?php wp_footer(); ?>
		<?php get_template_part( 'partials/mg', 'analitics' ); ?>
	</body>
</html>