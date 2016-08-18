=== ShareASale Dealsbar ===
Tags: Affiliate, marketing, ShareASale
Requires at least: 3.0.1
Tested up to: 4.6
Stable tag: 2.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The ShareASale Dealsbar lets you to pick merchants you're partnered and display random deals in a simple, customizable bar on pages/posts. 

== Installation ==

Login to ShareASale and visit Tools >> Affiliate API. If it's not enabled, click "enable API." Otherwise, scroll down and find your API TOKEN and API KEY. Copy these so you can paste them into the Dealsbar plugin settings. Also jot down your Affiliate ID. It's printed at the top of the Affiliate dashboard in ShareASale. 
 
Change the "IP Restrictions" drop-down setting on the Affiliate API page to "Require IP address match for versions 1.3 or lower." You can also keep the default setting ("Require IP address match for all API calls") if you know your site's hosting IP address and can enter it above the Token field. Press "Update Settings."

Then in your Wordpress admin Dealsbar setting page (star icon on sidebar), input your API settings (key/token) and Affiliate ID. Save your settings. If there is an error saving your settings and a red warning is at the top, contact ShareASale support (shareasale@shareasale.com) for assistance. 

Click the checkbox for "Dealsbar enabled." Then you can enter in text that will precede all the merchant deals e.g. "Check out these sweet deals!" You can customize whether the bar is at the top or bottom, the height, color, text color, or input your own CSS settings if familiar. 
 
Most important, pick some of your merchants from the select menu whose deals you want to showcase. These are organized by merchant ID, so some merchants with several stores might have more then one entry underneath their ID number. Ctrl+click (or cmd+click on a Mac) to select multiple stores. Click on a merchant ID to select ALL the stores beneath in one sweep for convenience.
 
Finally, click save settings again and you'll see your new Dealsbar populated with deals that will earn you commissions when they turn into sales!

== Changelog ==

= 2.0.2 =
* Update to coupon/deals syncing in case a Merchant has oddly named deal descriptions. Thanks, Marcia!

= 2.0.1 =
* Small tweaks to make upgrade from v1.0 smoother for certain users.

= 2.0 =
* NOTE: You will likely need to re-input your dealsbar customization and API settings!
* Second release
* Plugin re-written for performance, security, and ease of future updating
* Added a setting for custom subid/Affiliate-defined tracking values in dealsbar links
