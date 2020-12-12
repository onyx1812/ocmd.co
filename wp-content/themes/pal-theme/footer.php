    <footer class="main-footer">
      <div class="container">
        <h2 class="section-title">Subscribe to our newsletters</h2>
        <p class="text-center">Receive Updates about New Products and Special Deals.</p>
        <form class="sform" id="sForm">
          <input type="email" name="s_email" id="s_email" placeholder="Your Email Address">
          <input type="submit" value="subscribe">
        </form>
        <div class="row">
          <div class="col">
            <a href="<?php echo home_url(); ?>" class="logo"><img src="<?php echo IMG.'/logo.png'; ?>" alt=""></a>
          </div>
          <div class="col">
            <p class="call">
              Need Help? Contact Us 24/7 <br><br>
              <a href="tel:8772310127">(877) 231-0127</a> <br><br>
              <a href="mailto:support@ocmd.co">support@ocmd.co</a>
            </p>
          </div>
          <div class="col">
            <h4>Quick links</h4>
            <?php
              wp_nav_menu(array(
                'menu'        => 'footer-menu',
                'container'      => '',
                'container_class'  => '',
                'menu_class'    => 'footer-menu',
                'container_id'    => ''
              ));
            ?>
          </div>
        </div>
      </div>
    </footer>

    <?php wp_footer(); ?>
    <?php // get_template_part( 'partials/mg', 'analitics' ); ?>
  </body>
</html>