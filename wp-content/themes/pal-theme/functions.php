<?php

define('ROOT', get_template_directory_uri());
define('IMG', ROOT . '/img');
define('OFFER', ROOT . '/offer');
define('UPSELLS', ROOT . '/upsells');
function IMG(){
  echo IMG;
}
define('VIDEO', ROOT . '/video');

function TYPE(){
  global $post;
  return $post->post_type;
}

function replace_core_jquery_version() {
  wp_deregister_script( 'jquery' );
  wp_register_script( 'jquery', "https://code.jquery.com/jquery-3.1.1.min.js", array(), '3.1.1' );
}
add_action( 'wp_enqueue_scripts', 'replace_core_jquery_version' );

include('include/clear.php');
include('include/contactForm.php');
include('include/subscribeForm.php');


add_theme_support( 'menus' );

function front_scripts() {

// Index page
  if( is_page_template( 'templates/page-index.php' ) ){
    wp_enqueue_style( 'styles', get_template_directory_uri().'/css/styles-index.css');
    wp_enqueue_script( 'scripts', get_template_directory_uri() . '/js/scripts-index.js', false, false, 'in_footer');
  }

// Thank you page
  if( is_order_received_page() ){
    wp_enqueue_style( 'styles', get_template_directory_uri().'/css/styles-thank_you.css');
    wp_enqueue_script( 'scripts', get_template_directory_uri() . '/js/scripts-thank_you.js');
  }

// Checkout and Cart page
  if( is_checkout() || is_cart() ){
    wp_enqueue_style( 'styles', get_template_directory_uri().'/css/styles-checkout.css');
    wp_enqueue_script( 'scripts', get_template_directory_uri() . '/js/scripts-checkout.js', false, false, 'in_footer');
  }

// Single page
  if( is_single() ){
    if(TYPE() === 'product'){
      wp_enqueue_style( 'styles', get_template_directory_uri().'/css/styles-single.css');
      wp_enqueue_script( 'scripts', get_template_directory_uri() . '/js/scripts-single.js', false, false, 'in_footer');
    } else if(TYPE() === 'post'){
      wp_enqueue_style( 'styles', get_template_directory_uri().'/css/styles-blog.css');
    }
  }

//Default page template
  if( is_page_template('default') ){
    wp_enqueue_style( 'styles', get_template_directory_uri().'/css/styles-page.css');
    wp_enqueue_script( 'scripts', get_template_directory_uri() . '/js/scripts-page.js', false, false, 'in_footer');
  }

//FAQ page template
  if( is_page_template( 'templates/page-faq.php' ) ){
    wp_enqueue_style( 'styles', get_template_directory_uri().'/css/styles-faq.css');
    wp_enqueue_script( 'scripts', get_template_directory_uri() . '/js/scripts-faq.js', false, false, 'in_footer');
  }

//Contact page template
  if( is_page_template( 'templates/page-contact.php' ) ){
    wp_enqueue_style( 'styles', get_template_directory_uri().'/css/styles-contact.css');
    wp_enqueue_script( 'scripts', get_template_directory_uri() . '/js/scripts-contact.js', false, false, 'in_footer');
  }

// 404 page
  if( is_404() ){
    wp_enqueue_style( 'styles', get_template_directory_uri().'/css/styles-404.css');
  }

  if( is_page_template( array('offer/page-camp1.php', 'offer/page-camp2.php', 'offer/page-camp3.php', 'offer/page-camp4.php') ) ){
    wp_enqueue_style( 'styles', get_template_directory_uri().'/offer/css/style.css');
    wp_enqueue_script( 'scripts', get_template_directory_uri() . '/offer/js/scripts.js', false, false, 'in_footer');
  }

  if( is_page_template( array('upsells/page-1-2-3-deep-perfecting-cerum-cross-sell.php', 'upsells/page-1-jars-drc-downsell.php', 'upsells/page-3-jars-drc-downsell.php', 'upsells/page-3-jars-drc-upsell.php', 'upsells/page-6-jars-drc-downsell.php') ) ){
    wp_enqueue_style( 'styles', get_template_directory_uri().'/upsells/css/style.css');
    wp_enqueue_script( 'scripts', get_template_directory_uri() . '/upsells/js/scripts.js', false, false, 'in_footer');
  }

}
add_action( 'wp_enqueue_scripts', 'front_scripts' );

function front_variables(){
  if( isset($_GET["style"]) && $_GET["style"] == 'white' ){
    $style = 'white';
  } else {
    $style = 'dark';
  }

  if( isset($_GET['amount']) ){
    $amount = $_GET['amount'];
  } else if( isset($_COOKIE['amount']) ){
    $amount = $_COOKIE['amount'];
  }

  if( isset($_GET['taboola']) ){
    $tb = $_GET['taboola'];
  } else if( isset($_COOKIE['taboola']) ){
    $tb = $_COOKIE['taboola'];
  }

  if( isset($_GET['fb']) ){
    $fb = $_GET['fb'];
  } else if( isset($_COOKIE['fb']) ){
    $fb = $_COOKIE['fb'];
  }

  if( isset($_GET['version']) ){
    $version = $_GET['version'];
  } else if( isset($_COOKIE['version']) ){
    $version = $_COOKIE['version'];
  }

  if( isset($_GET['affid']) ){
    $affid = $_GET['affid'];
  } else if( isset($_COOKIE['affid']) ){
    $affid = $_COOKIE['affid'];
  }

  if( isset($_GET['click_id']) ){
    $click_id = $_GET['click_id'];
  } else if( isset($_COOKIE['click_id']) ){
    $click_id = $_COOKIE['click_id'];
  }

  if( isset($_GET['oid']) ){
    $oid = $_GET['oid'];
  } else if( isset($_COOKIE['oid']) ){
    $oid = $_COOKIE['oid'];
  }

  if( isset($_GET['uid']) ){
    $uid = $_GET['uid'];
  } else if( isset($_COOKIE['uid']) ){
    $uid = $_COOKIE['uid'];
  }

  wp_localize_script( 'scripts', 'data',
    array(
      'ajax' => admin_url('admin-ajax.php'),
      'theme' => get_template_directory_uri(),
      'thanks' => get_permalink(41),
      'checkout' => wc_get_checkout_url(),
      'style' => $style,
      'amount' => $amount,
      'taboola' => $tb,
      'fb' => $fb,
      'version' => $version,
      'affid' => $affid,
      'click_id' => $click_id,
      'oid' => $oid,
      'uid' => $uid,
    )
  );

}
add_action( 'wp_enqueue_scripts', 'front_variables' );



if( function_exists('acf_add_options_page') ) {
  acf_add_options_page();
}


$expiration = 2*24*60*60;

if( isset($_GET["uid"]) ){
  setcookie("uid", $_GET["uid"], time()+$expiration, '/');
}
if( isset($_GET["oid"]) ){
  setcookie("oid", $_GET["oid"], time()+$expiration, '/');
}
if( isset($_GET["taboola"]) ){
  setcookie("taboola", $_GET["taboola"], time()+$expiration, '/');
}
if( isset($_GET["fb"]) ){
  setcookie("fb", $_GET["fb"], time()+$expiration, '/');
}
if( isset($_GET["click_id"]) ){
  setcookie("click_id", $_GET["click_id"], time()+$expiration, '/');
}
if( isset($_GET["affid"]) ){
  setcookie("affid", $_GET["affid"], time()+$expiration, '/');
}
if( isset($_GET["style"])){
  setcookie("style", $_GET["style"], time()+$expiration, '/');
}


add_filter( 'woocommerce_add_to_cart_redirect', 'bbloomer_redirect_checkout_add_cart' );

function bbloomer_redirect_checkout_add_cart() {
   return wc_get_checkout_url();
}


// Product thumbnail in checkout
add_filter( 'woocommerce_cart_item_name', 'product_thumbnail_in_checkout', 20, 3 );
function product_thumbnail_in_checkout( $product_name, $cart_item, $cart_item_key ){
    if ( is_checkout() ) {

        $thumbnail   = $cart_item['data']->get_image(array( 70, 70));
        $image_html  = $thumbnail;

        $product_name = $image_html . $product_name;
    }

    return $product_name;
}

remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open' );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close' );

add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 1 );
add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 99 );

add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_single_excerpt', 1 );
remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
add_action( 'woocommerce_checkout_after_customer_details', 'woocommerce_checkout_payment', 10 );

/*RATING*/
add_action( 'woocommerce_after_shop_loop_item_title', function (){
  echo '<footer><div class="product-desc">';
}, 5 );
add_action( 'woocommerce_after_shop_loop_item_title', function (){
  echo '<ul class="star-rating"><li class="star-f"></li><li class="star-f"></li><li class="star-f"></li><li class="star-f"></li><li class="star-h"><i></i></li></ul>';
}, 5 );
add_action( 'woocommerce_after_shop_loop_item_title', function (){
  echo '</div><a href="'.get_the_permalink().'" class="button product_type_simple add_to_cart_button ajax_add_to_cart">SHOP NOW</a>';
}, 15 );

add_action( 'woocommerce_after_shop_loop_item', function(){
  echo '</footer>';
}, 10);

add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 1 );



add_filter( 'woocommerce_product_add_to_cart_text', 'bryce_archive_add_to_cart_text' );
function bryce_archive_add_to_cart_text() {
  return __('SHOP NOW', 'shop-now' );
}



//SINGLE PRODUCT PAGE
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

add_action( 'woocommerce_after_single_product_summary', function(){
  echo '<div class="quantity__wrap"><h3>Stock Up & Save, Choose Quantity:</h3><ul class="mg-variation"></ul>';
}, 20 );
add_action( 'woocommerce_after_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
add_action( 'woocommerce_after_single_product_summary', function(){
  echo '</div>';
}, 31 );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'woocommerce_single_product_summary', function(){
  the_content();
}, 20 );




add_filter('woocommerce_cart_item_subtotal', 'custom_filter_wc_cart_item_remove_link', 10, 3);
function custom_filter_wc_cart_item_remove_link($product_name, $cart_item, $cart_item_key)
{

    if (is_checkout()) {
        $product_name .= apply_filters('woocommerce_cart_item_remove_link', sprintf(
            '<a href="%s" rel="nofollow" class="remove" >&times;</a>',
            esc_url(WC_Cart::get_remove_url($cart_item_key)),
            __('Remove this item', 'woocommerce'),
            esc_attr($cart_item['product_id']),
            esc_attr($cart_item['data']->get_sku())
        ), $cart_item_key);
        return $product_name;
    }
}






function product_custom($id, $best){
  $product = wc_get_product( $id );
  $checkout_url = WC()->cart->get_checkout_url();
  if($best){
    $class = 'product product-best';
  } else {
    $class = 'product';
  }

  echo '
    <div class="'. $class .'">
      <div class="product-quantity">'. get_field('simple_jars', $id) .'</div>
      <div class="product-inner">
        <div class="product-price">'. get_field('simple_price', $id) .'</div>
        <div class="product-save">'. get_field('simple_save', $id) .'</div>
      </div>
      <footer>
        <a href="'.$checkout_url.'?add-to-cart='.$id.'" class="buy-now button">Add To Cart</a>
        <span>'. get_field('simple_ship', $id) .'</span>
      </footer>
    </div>
  ';
}

function product_offer($id, $q, $best){
  $product = wc_get_product( $id );
  if($q === 1){
    $url_part = '1-jar';
  }elseif($q === 3){
    $url_part = '3-jars';
  }elseif($q === 6){
    $url_part = '6-jars';
  }
  $url = 'https://ocmd.co/checkouts/rejuvenation-complex-cream-'.$url_part.'-'.$product->sku;
  if($best){
    $class = 'product product-best';
  } else {
    $class = 'product';
  }

  echo '
    <div class="'. $class .'">
      <div class="product-quantity">'. get_field('simple_jars', $id) .'</div>
      <div class="product-inner">
        <div class="product-price">'. get_field('simple_price', $id) .'</div>
        <div class="product-save">'. get_field('simple_save', $id) .'</div>
      </div>
      <footer>
        <a href="'.$url.'" class="buy-now button">Add To Cart</a>
        <span>'. get_field('simple_ship', $id) .'</span>
      </footer>
    </div>
  ';
}


/**
 * Auto Complete all WooCommerce orders.
 */
add_action( 'woocommerce_thankyou', 'custom_woocommerce_auto_complete_order' );
function custom_woocommerce_auto_complete_order( $order_id ) {
    if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );
    $order->update_status( 'completed' );
}






add_filter( 'woocommerce_default_address_fields', 'customising_checkout_fields', 1000, 1 );
function customising_checkout_fields( $address_fields ) {
    $address_fields['email']['required'] = true;
    $address_fields['first_name']['required'] = true;
    $address_fields['last_name']['required'] = true;
    $address_fields['city']['required'] = true;
    $address_fields['state']['required'] = true;
    $address_fields['postcode']['required'] = true;

    return $address_fields;
}