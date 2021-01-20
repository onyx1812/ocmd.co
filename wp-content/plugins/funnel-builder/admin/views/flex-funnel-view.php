<?php
/**
 * Adding contact page
 */
defined( 'ABSPATH' ) || exit; //Exit if accessed directly
$funnel    = WFFN_Core()->admin->get_funnel();
$funnel_id = $funnel->get_id();
include_once( __DIR__ . '/commons/single-wffn-head.php' ); ?>

<div class="wffn-sec-head_wrap"><?php include_once( __DIR__ . '/commons/section-head.php' ); ?> </div>
<div id="wffn-contacts" class="wffn-page"></div>