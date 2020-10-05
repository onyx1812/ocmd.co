=== First payment date for WooCommerce Subscriptions ===
Contributors: carazo
Donate link: http://paypal.me/codection
Tags: woocommerce, subscriptions, woocommerce subscriptions, trial, period, date
Requires at least: 3.4
Tested up to: 5.5.1
Stable tag: 0.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to set a first payment date for a subscription, using WooCommerce Subscriptions. So you will be able to set a specific date (not 

== Description ==

When you are using a site with WooCommerce Subscriptions you can change the first payment date using trial periods. But a trial period is a period, not a date. So you can set "1 month free" but you cannot set "this subscriptions will start being paid this date", whenever you make the bought of the subscription. This plugin solves this problem.

## **Basics**

*   Set in every product or product variaition, which is the date of the first payment to be done
*	When a user buys your subscription, based in a product subscription or a product variable subscription, it will get the first payment in the specified date
*	User will see in cart and checkout how many days he can get the subscription for free until the first payment will be done

## **Usage**

Once the plugin is installed you can use it. Go to Product data, search "First payment date" and fill it. If you are using a variable product, you will find it in each variation.

== Screenshots ==

1. First date payment field in a product subscription
2. First date payment field in a product subscription variation

== Changelog ==

= 0.2.1 =
*	Internationalization improved thanks to @yordansoares thanks for advising me about us 

= 0.2 =
*	Now you can choose new options when setting the first date of the payment: first day of the next month and first day of the next year
*	Up to date compatibility

= 0.1.2 =
*   New requires up to included
*	Up to date compatibility


= 0.1.1 =
*   esc_attr changed to sanitize_text_field in calls to update_post_meta

= 0.1.0 =
*   First release

== Frequently Asked Questions ==

= Field does not appear =

Please, save your product subscription first. By default when you create a new product, this is created as simple product and this is not shown. When you save it and page reload, it will appear.

= Product variations =

You can set a first date of payment for each variation. Anyway, when a user visit your variable subscription product, he will see the first payment date of the first variation (in this way works WooCommerce Subscritpions with trial periods).

== Installation ==

### **Installation**

*   Install **First payment date for WooCommerce Subscriptions** automatically through the WordPress Dashboard or by uploading the ZIP file in the _plugins_ directory.
*   Then, after the package is uploaded and extracted, click&nbsp;_Activate Plugin_.

Now going through the points above, you should now see the new option in the "Product data" part of each variable product.


If you get any error after following through the steps above please contact us through item support comments so we can get back to you with possible helps in installing the plugin and more.
