<?php
/**
 * Plugin Name: WooFunnels Funnel Builder Pro
 * Plugin URI: https://buildwoofunnels.com/wordpress-funnel-builder/
 * Description: Extend Funnel Builder PRO with One Click Upsells, Order Bumps, Optin Modals, In-depth Funnel Reporting and much more!
 * Version: 1.0.7
 * Author: BuildWooFunnels
 * Author URI: https://buildwoofunnels.com
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: Funnel Builder Pro
 * Domain Path: /languages/
 *
 * Requires at least: 4.9.0
 * Tested up to: 5.6
 * WooFunnels: true
 *
 * Funnel Builder Pro is free software.
 * You can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Funnel Builder Pro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Funnel Builder Pro. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Defining necessary constants
 */
define( 'WFFN_PRO_FILE', __FILE__ );
define( 'WFFN_PRO_BUILD_VERSION', '1.0.7' );


/**
 * Making sure to flush permalink on activation so that all posts works fine
 */
add_action( 'activated_plugin',  'wfn_pro_maybe_flush_permalink'  );
register_activation_hook( __FILE__, 'wfn_pro_maybe_flush_permalink' );

function wfn_pro_maybe_flush_permalink() {
    update_option( 'bwf_needs_rewrite', 'yes', true );
}

/**
 * Adding funnel builder powerpack
 */
require_once 'modules/funnel-builder-powerpack/funnel-builder-powerpack.php';


/**
 * once all modules files included, loading full modules
 */
add_action( 'wffn_pro_modules_loaded', function () {
	$modules = array(
		'one-click-upsells/woofunnels-upstroke-one-click-upsell.php',
		'checkout/woofunnels-aero-checkout.php',
		'order-bumps/woofunnels-order-bump.php',
		'one-click-upsells-powerpack/woofunnels-upstroke-power-pack.php',
	);

	if ( WFFN_Pro_Core()->is_dependency_exists ) {
		foreach ( $modules as $module ) {
			WFFN_Pro_Modules::maybe_load( $module );
		}
	}
} );