<?php

/**
 * Plugin Name: Unify
 * Plugin URI: https://www.codeclouds.com/unify/
 * Description: A CRM payment plugin which enables connectivity with LimeLight/Konnektive CRM and many more..
 * Author: CodeClouds <sales@codeclouds.com>
 * Author URI: https://www.CodeClouds.com/
 * Version: 2.5.2
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * 
 * WC requires at least: 3.0
 * WC tested up to: 4.4
 */

if (!defined('ABSPATH'))
{
    exit; // Exit if accessed directly
}

/**
 * Loaded Hoocks & Actions.
 */
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if(is_plugin_active('woocommerce/woocommerce.php'))
{
    require_once __DIR__ . '/Services/Hooks.php';
    require_once __DIR__ . '/Lib/_SelfLoader-1.0/autoload.php';
    require_once __DIR__ . '/Lib/autoload.php';
}
else
{
    add_action('admin_notices', function()
    {
    	echo '<div class="error"><p><strong>' .
        sprintf(esc_html__('Unify Plugin requires WooCommerce to be installed and active. You can download %s here.'), '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a>') .
                '</strong></p></div>';
    });
}