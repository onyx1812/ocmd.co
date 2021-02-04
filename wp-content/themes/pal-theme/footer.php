    <?php if( !is_page_template( array('upsells/page-1-2-3-deep-perfecting-cerum-cross-sell.php', 'upsells/page-1-jars-drc-downsell.php', 'upsells/page-3-jars-drc-downsell.php', 'upsells/page-3-jars-drc-upsell.php', 'upsells/page-6-jars-drc-downsell.php', 'offer/page-camp1.php', 'offer/page-camp2.php', 'offer/page-camp3.php', 'offer/page-camp4.php') ) ): ?>
    <footer class="main-footer">
      <div class="container">
        <?php get_template_part( 'partials/mg', 'subscribe' ); ?>
        <div class="row">
          <div class="col">
            <a href="<?php echo home_url(); ?>" class="logo"><img src="<?php echo IMG.'/logo.svg'; ?>" alt=""></a>
          </div>
          <div class="col">
            <p class="call">
              <a href="tel:8663414691">(866) 341-4691</a> <br><br>
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
  <?php endif; ?>

    <?php wp_footer(); ?>
    <?php // get_template_part( 'partials/mg', 'analitics' ); ?>
  </body>
</html>