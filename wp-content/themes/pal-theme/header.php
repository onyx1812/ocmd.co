<!DOCTYPE html>
<html lang="en">
  <head>

    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="keywords" content="<?php bloginfo('keywords'); ?>"/>
    <meta name="description" content="<?php bloginfo('description'); ?>"/>
    <meta name="copyright" content="<?php bloginfo('copyright'); ?>">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, user-scalable=no, user-scalable=0, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0 user-scalable=0">
    <meta name="author" content="MaxGloba">
    <meta name="theme-color" content="#ffffff">

    <title><?php wp_title( '|', true, 'right' ); echo get_bloginfo('name'); ?></title>

    <link rel="shortcut icon" type="image/x-icon" href="<?php echo get_template_directory_uri(); ?>/img/favicon.ico?v=1.00" />

    <script>
      const url = window.location.href;
      console.log(url);
      if( url == 'http://ocmd.co/shop' || url == 'https://ocmd.co/shop' || url == 'http://www.ocmd.co/shop' || url == 'https://ocmd.co/shop/' ){
        window.location.href = 'https://ocmd.co/#productsSection';
      } else if( url == 'http://ocmd.loc/shop' ){
        window.location.href = 'http://ocmd.loc/#productsSection';
      }

      const vh = window.innerHeight * 0.01;
      document.documentElement.style.setProperty('--vh', `${vh}px`);
      const pageID = <?php echo get_the_ID(); ?>;
    </script>

    <!-- Facebook Pixel Code -->
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js'
    );
      fbq('init', '767835227441749');
      fbq('track', 'PageView');
    </script>
    <!-- End Facebook Pixel Code -->

    <?php wp_head(); ?>
  </head>

  <body <?php body_class(); ?> >

    <header class="main-header">
      <?php if(TYPE() === 'post' || is_page_template( array('upsells/page-1-2-3-deep-perfecting-cerum-cross-sell.php', 'upsells/page-1-jars-drc-downsell.php', 'upsells/page-3-jars-drc-downsell.php', 'upsells/page-3-jars-drc-upsell.php', 'upsells/page-6-jars-drc-downsell.php', 'offer/page-camp1.php', 'offer/page-camp2.php', 'offer/page-camp3.php', 'offer/page-camp4.php') )): ?>
      <div class="container">
      <?php else: ?>
      <div class="container-fluid">
      <?php endif; ?>
        <?php if(TYPE() === 'post' || is_page_template( array('offer/page-camp1.php', 'offer/page-camp2.php', 'offer/page-camp3.php', 'offer/page-camp4.php') )): ?>
        <a href="<?php echo home_url(); ?>" title="Home page | OCMD LLC"><img src="<?php echo IMG.'/logo.svg'; ?>" alt="" class="logo logo-post" width="160px"></a>
        <?php elseif( is_page_template( array('upsells/page-1-2-3-deep-perfecting-cerum-cross-sell.php', 'upsells/page-1-jars-drc-downsell.php', 'upsells/page-3-jars-drc-downsell.php', 'upsells/page-3-jars-drc-upsell.php', 'upsells/page-6-jars-drc-downsell.php') ) ): ?>
        <div class="row">
          <div class="col-md-4">
            <a href="<?php echo home_url(); ?>" title="Home page | OCMD LLC"><img src="<?php echo IMG.'/logo.svg'; ?>" alt="" class="logo" ></a>
          </div>
          <div class="col-md-8">
            <h2>Wait! Your Order Is Not Complete!</h2>
            <h4>Do not hit the back button, as it could cause multiple charges on your card</h4>
          </div>
        </div>
        <?php else: ?>
        <div class="row">
          <div class="col-md-3 col-7">
            <a href="<?php echo home_url(); ?>" title="Home page | OCMD LLC"><img src="<?php echo IMG.'/logo.svg'; ?>" alt="" class="logo"></a>
          </div>
          <div class="col-md-2 col-5 order-md-2">
            <div class="mini-cart">
              <img src="<?php echo IMG.'/icon-cart.svg'; ?>" alt="">
              <?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
            </div>
            <button class="main-nav-btn" id="mainNavBtn">
              <span></span>
              <span></span>
              <span></span>
            </button>
          </div>
          <div class="col-md-7 col-12">
            <?php
              wp_nav_menu(array(
                'menu'        => 'index-menu',
                'container'      => 'nav',
                'container_class'  => 'main-nav',
                'container_id'    => 'mainNav'
              ));
            ?>
          </div>
        </div>
      <?php endif; ?>
      </div>
    </header>