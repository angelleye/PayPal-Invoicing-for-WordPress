=== PayPal Invoicing for WordPress ===
Contributors: angelleye
Donate link: http://www.angelleye.com/
Tags: paypal, invoice, invoicing, woocommerce, order, orders, angelleye, money, payment, payments
Requires at least: 3.0.1
Tested up to: 5.2.2
Stable tag: 2.1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add PayPal Invoicing functionality to your WordPress dashboard.  Includes full support for WooCommerce if installed!

== Description ==

= Introduction =

[youtube https://youtu.be/FsQ4dFG8lWE]

Easily create and manage PayPal Invoices from your WordPress / WooCommerce dashboard.

 * Create and Send new PayPal Invoices.
 * Update, Send Reminders, Cancel or Delete PayPal Invoices.
 * WooCommerce Compatibility!

= WooCommerce Compatibility =
Create your orders using the WooCommerce Order manager.  You will then see options for PayPal Invoicing available within the WooCommerce Order Actions menu.

 * Save Invoice Draft
 * Send Invoice
 * Send Invoice Reminder
 * Cancel Invoice

= Quality Control =
Payment processing can't go wrong.  It's as simple as that.  Our certified PayPal engineers have developed and thoroughly tested this plugin on the PayPal sandbox (test) servers to ensure you will not have any problems sending and managing PayPal Invoices from WordPress.

= Localization =
This plugin is fully localized and ready for translation.  If you're interested in helping translate please [let us know](http://www.angelleye.com/contact-us/)!

= Get Involved =
Developers can contribute to the source code on the [PayPal for WooCommerce GitHub repository](https://github.com/angelleye/PayPal-Invoicing-for-WordPress).

== Installation ==

= Minimum Requirements =

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't need to leave your web browser. To do an automatic install of PayPal Invoicing for WordPress, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type PayPal Invoicing for WordPress and click Search Plugins. Once you've found our plugin you can view details about it such as the the rating and description. Most importantly, of course, you can install it by simply clicking Install Now.

= Manual Installation =

1. Unzip the files and upload the folder into your plugins folder (/wp-content/plugins/) overwriting older versions if they exist
2. Activate the plugin in your WordPress admin area.

= Updating =

Automatic updates should work great for you.  As always, though, we recommend backing up your site prior to making any updates just to be sure nothing goes wrong.

== Screenshots ==

1. Create a new PayPal Invoice from within the WordPress dashboard (WooCommerce not required!)
2. PayPal Invoice as displayed to the buyer.
3. PayPal Invoice details as displayed in the WordPress dashboard.
4. View PayPal Invoice history within the WordPress dashboard.
5. PayPal Invoice options available within the Order Actions menu of a WooCommerce order.
6. PayPal Invoice options available within the Order Actions menu of a WooCommerce order (cont.)

== Frequently Asked Questions ==

= Do buyers need a PayPal account in order to pay a PayPal invoice?

* No.  Your customers can choose to pay via debit or credit card, or via their PayPal account. They’ll love how easy and secure it is to pay.

= How do I create sandbox accounts for testing? =

* Login at http://developer.paypal.com.
* Click the Applications tab in the top menu.
* Click Sandbox Accounts in the left sidebar menu.
* Click the Create Account button to create a new sandbox account.
* TIP: Create at least one "seller" account and one "buyer" account if you want to fully test Express Checkout or other PayPal wallet payments.
* TUTORIAL: See our [step-by-step instructions with video guide](https://www.angelleye.com/create-paypal-sandbox-account/).

= What other PayPal plugins do you have that might help me? =

* [PayPal for WooCommerce](https://www.angelleye.com/product/woocommerce-paypal-plugin/)
* [PayPal Here for WooCommerce](https://www.angelleye.com/product/paypal-here-woocommerce-pos/)
* [PayPal IPN for WordPress](https://www.angelleye.com/product/paypal-ipn-wordpress/)
* [PayPal WP Button Manager](https://www.angelleye.com/product/wordpress-paypal-button-manager/)
* [PayPal for Divi](https://www.angelleye.com/product/divi-paypal-module-plugin/)
* [PayPal Security](https://www.angelleye.com/product/wordpress-paypal-security/)

= What type of support should I expect? =

* We offer free support for installation and configuration questions.  You may [submit a ticket here](https://www.angelleye.com/support) for that.
* We offer [premium support](https://www.angelleye.com/product/paypal-help/) for things that fall outside the scope of installation and configuration.

== Changelog ==

= 2.x.x - xx.xx.2020 =
* Fix - Pass Full Billing / Shipping Address in PayPal Invoice. ([PPIW-175](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/82))
* Fix - Update WooCommerce Order Status when PayPal Invoice Status updates. ([PPIW-163](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/80))

= 2.1.0 - 07.19.2019 =
* Feature - Adds ability to Mark Invoice Paid at PayPal from the invoice screen in WordPress. ([PPIW-37](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/71))
* Feature - Adds ability to refund invoice payments from within WordPress invoice screen. ([PPIW-38](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/72))
* Feature - Adds AE notification system. ([PPIW-147](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/67)) ([PPIW-156](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/77))
* Fix - Resolves an issue with the PayPal Webhook handler getting stuck sometimes. ([PPIW-149](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/68))
* Fix - Resolves an issue where the user cannot clear out a company name entered in the plugin settings. ([PPIW-55](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/69))
* Fix - Resolves a broken link in the settings panel. ([PPIW-150](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/76))
* Fix - Resolves a PHP headers issue. ([PPIW-152](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/73))
* Fix - Removes the use of a WooCommerce function that was causing problems when WC is not being used. ([PPIW-151](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/74))
* Tweak - Adds validation to the settings form. ([PPIW-54](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/70))
* Tweak - Adjusts placeholder on feedback form. ([PPIW-153](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/75))

= 2.0.5 - 07.09.2019 =
* Tweak - Minor adjustment to PayPal API requests.

= 2.0.4 - 04.22.2019 =
* Fix - Resolves an issue where WooCommerce orders were not updating properly with PayPal Invoices. ([PPIW-57](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/66))

= 2.0.3 - 04.18.2019 =
* Fix - Resolves an issue with some international phone number formats. ([PPIW-56](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/65))

= 2.0.2 - 04.12.2019 =
* Tweak - Adds validation on invoice field values during creation. ([PPIW-50](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/63))
* Tweak - Adds better error handling for malformed request failures. ([PPIW-49](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/62))
* Fix - Resolves an issue where the sender email was displayed in the Invoice list instead of the recipient. ([PPIW-43](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/60))
* Fix - Resolves additional causes of malformed request. ([PPIW-48](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/61))
* Fix - Resolves an issue where default values did not carry on to additional line items during invoice creation. ([PPIW-51](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/64))

= 2.0.1 - 04.09.2019 =
* Fix - Resolves an issue with some invoices resulting in malformed request. ([PPIW-45](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/59))

= 2.0.0 - 04.04.2019 =
* Feature - Updates all current functionality to Invoicing v2 APIs. ([PPIW-19](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/56))
* Feature - Adds AE Updater compatibility for notices and automated updates. ([PPIW-35](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/57)) ([PPIW-42](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/58))
* Feature - Adds opt-in for basic tracking / feedback purposes. ([PPIW-5](https://github.com/angelleye/PayPal-Invoicing-for-WordPress/pull/55))

= 1.0.0 =
* Initial Release.