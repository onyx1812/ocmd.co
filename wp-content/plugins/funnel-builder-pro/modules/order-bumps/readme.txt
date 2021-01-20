=== OrderBumps: WooCommerce Checkout Offers ===
Contributors: WooFunnels
Tested up to: 5.5.4
Stable tag: 1.9.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html


== Change log ==

= 1.9.1 (2020-11-25) =
* Fixed: PHP error with bump reporting.
* Improved: Optimizations in bump reporting.
* Fixed: Product deletion in the admin in Bump, showing the deleted product, fixed.


= 1.9.0 (2020-11-20) =
* Added: Two new Bump skins added.
* Added: Product category rule: New option `doesn't contain` added.
* Added: New rule: Cart item tag added.
* Added: Compatible with `Price Based on Country` plugin by Oscar Gare.
* Compatible up to WordPress 5.5.3
* Compatible up to WooCommerce 4.8
* Compatible with upcoming WooFunnel releases.
* Improved: Order Bump reporting improved.
* Improved: Quantity switcher UI improved.
* Improved: Optimize ajax calls on the checkout page on bump acceptance and removal. Overall performance improved.
* Improved: Handled scenario when the cart is virtual and change to physical via bump or vice versa.
* Fixed: PHP error during customer rules inside WP admin dashboard.
* Fixed: Compatibility added WC subscription plugin, allowing of correct price display.
* Fixed: When free product i.e. $0 value bump accepted, Single order is not showing the bump price on order listing screen.
* Fixed: An issue arose with `WP clever smart bundle` plugin. Fixed.


= 1.8.1 (2020-01-29) =
* Compatible with WooCommerce 3.9
* Fixed: Sometimes preview shows PHP errors when saved product is not available, fixed.


= 1.8.0 (2019-12-11) =
* Added: Rule operator Coupon 'matches none of'. Now control visibility of Bumps based for certain coupons
* Added: Bump import and export feature to import bumps from one site to another.
* Added: Compatibility with A/B Experiments for WooFunnels.
* Improved: Admin UX at few places
* Updated: Woofunnels core


= 1.7.2 (2019-11-14) =
* Added: Future compatibility with Woofunnels A/B experiments plugin.
* Added: Compatibility with WordPress version 5.3.0.


= 1.7.1 (2019-10-14) =
* Fixed: Sustain credit card details when bump product added or Removed.
* Fixed: Compatibility with Porto theme.


= 1.7.0 (2019-10-11) =
* Added: Detailed order bump reports added. Reporting available by date and by specific bumps. Available under WooCommerce > Reports > OrderBump
* Added: Merge tag: {{quantity_incrementer}} to allow buyers to increment quantity of bump.
* Added: Merge tag: {{variation_attribute_html}} to display selected variants labels (for variable products).
* Added: Compatibility with Finale WooCommerce Countdown timer plugin. Allowing Finale Shortcode in Bump description.
* Added: Compatibility with WOOCS - WooCommerce Currency Switcher by Realmag777.
* Improved: UX improved for variable products 'Choose an option' popup.
* Improved: Few cases handled of shipping methods when subscription product as a bump product is added or removed.
* Fixed: On Mobile collapsible 'cart total' updated on adding OrderBump to cart.
* Fixed: An issue with Pagar.me gateway in case instalment plan is chosen.
* Fixed: 'AeroCheckout page is' rule wasn't working in case of Aero embed page, fixed now.
* Fixed: Compatibility improved with WC Checkout Add-ons plugin when the bump is added or removed.
* Fixed: A bug with a Braintree payment gateway with its latest version.


= 1.6.3 (2019-04-16) =
* Improved: Display product title in case variation is not added to cart, in case of variable product.
* Fixed: Gateway conflict with latest version of WC Germanized, compatibility updated.


= 1.6.2 (2019-04-23) =
* Fixed: Cart item contains product rule code fixed.


= 1.6.1 (2019-04-18) =
* Fixed: Global setting page JS error resolved.
* Fixed: Holding payment gateways refresh until gateways list changed while adding or removing Bump product.


= 1.6.0 (2019-04-17) =
* Added: New setting 'Bump display' positions introduced. * Added: New rule 'Cart total' added
* Added: Compatible with Aero v1.8 as much changes done to support it.
* Improved: Bump end-user experience improved.
* Improved: Removing 'Bump added' items from the cart if rules got invalidated.
* Improved: Re-validating Bump rules when a coupon is added or removed.
* Fixed: Fixed discount was restricted to value 100 max, fixed.
* Fixed: Cart item quantity rule had a small issue, fixed.


= 1.5.0 (2019-01-17) =
* Added: Custom CSS setting added globally.
* Added: Maximum Bump display count setting added globally.
* Added: Support with MercadoPago payment gateway when Bump added or removed from the order.
* Added: {{product_name}} merge tag added to show the product name.
* Added: Support with 'Variation swatch' plugin by 'theme alien' to display swatches in bump product preview.
* Improved: Quick hints added under fields in the admin area.
* Improved: Compatibility code improved for 'WC Germanized' v2.2.7
* Improved: Handling added for cart total 0 case.
* Improved: Product selection rule caused a PHP error when the selected product was removed from the store.
* Fixed: Impreza theme causing conflict in admin area during bump creation, fixed now.
* Fixed: Porto theme caused PHP error as used a native WC hook with less variables.


= 1.4.0 (2018-12-19) =
* Added: WPML compatibility also allows WPML language duplication of Bumps.
* Added: Did additional code handling with 'InfusedWoo' plugin, supporting their subscriptions now.
* Fixed: Mixed product type issue resolved with Bump on checkout pages.


= 1.3.0 (2018-11-16) =
* Added: Compatible with 'WC radio buttons' plugin.
* Added: Compatible with 'Improved variable product attributes' plugin.
* Added: New field for error color in the design section.
* Improved: Remove quantity input in cart page for bump product.
* Fixed: Variable product wasn't adding to cart with older AeroCheckout version. Now compatible with AeroCheckout v1.5.
* Fixed: Rules validation now working for cart item (variation product)


= 1.2.1 (2018-10-31) =
* Improved: Sustain credit card details when bump product add or Removed. Use woocommerce fragment functionality in our ajax and refresh the essential parts of checkout page
* Improved: when subscription product removed from order bump and cart get empty then we reload the checkout page. This occur due subscription plugin  not work with mixed cart.
* Improved: Show Product Stock Error message when user add bump to cart in case of out of stock.


= 1.2.0 (2018-10-21) =
* Added: pot file added for translations.
* Improved: 'Select option' text replaced to 'choose option' with WooCommerce textdomain. Auto change to multi languages.
* Fixed: Add to cart button was not displaying for order bump pop up, now resolved.


= 1.1.0 (2018-10-12) =
* Improved: Compatible with Aero Checkout new version.
* Improved: Bump skin change, opt modal UI improved.


= 1.0.2 (2018-10-05) =
* Added: Allowed Course product type from LearnDash plugin to include as a Bump.


= 1.0.1 (2018-10-05) =
* Fixed: Debug class calls directly, fixed now.


= 1.0.0 (2018-10-03) =
* Public Release
