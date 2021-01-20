=== Billmate Order Management for WooCommerce ===
Contributors: Billmate, Krokedil, NiklasHogefjord
Tags: woocommerce, billmate, ecommerce, e-commerce, checkout
Requires at least: 5.0
Tested up to: 5.5.3
Requires PHP: 5.6
WC requires at least: 4.0.0
WC tested up to: 4.8.0
Stable tag: trunk
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

== DESCRIPTION ==

When an order is created in WooCommerce and a order number exists in Billmates system, you have the possibility to handle the order management in Billmate directly from WooCommerce.
This way you can save time and don’t have to work in both systems simultaneously.

== Installation ==
1. Upload plugin folder to to the "/wp-content/plugins/" directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Go WooCommerce Settings –> Payment Gateways –> Billmate Checkout and configure your Billmate Order Management settings.
4. Read more about the configuration process in the [plugin documentation](https://docs.krokedil.com/article/366-billmate-order-management-introduction).


== Frequently Asked Questions ==
= Which countries does this payment gateway support? =
Billmate Checkout works for merchants in Sweden.

= Where can I find Billmate Order Management for WooCommerce documentation? =
For help setting up and configuring Billmate Order Management for WooCommerce please refer to our [documentation](https://docs.krokedil.com/article/366-billmate-order-management-introduction).

= Are there any specific requirements? =
* WooCommerce 4.0 or newer is required.
* PHP 5.6 or higher is required.
* A SSL Certificate is required.

== Changelog ==
= 2021.01.19    - version 1.0.0 =
* Release       - First release of new Billmate Order Management for WooCommerce

= 2020.12.17    - version 0.2.1 =
* Fix           - Don't make activate payment request if payment was made via a direct payment method.

= 2020.12.07    - version 0.2.0 =
* Tweak         - Added readme.txt file.

= 2020.11.05    - version 0.1.0 =