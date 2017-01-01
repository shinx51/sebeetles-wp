=== Current Year and Copyright Shortcodes ===
Contributors: dgewirtz
Donate link: http://zatzlabs.com/lab-notes/
Tags: copyright, date, year, shortcode, plugin
Requires at least: 3.0
Tested up to: 4.6
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides Shortcodes to display the Current Year and/or a Copyright symbol.

== Description ==

**IMPORTANT: Support has moved to the ZATZLabs site and is no longer provided on the WordPress.org forums. Please visit the new [ZATZLabs Forums](http://zatzlabs.com/forums/). If you need a timely reply from the developer, please [open a ticket](http://zatzlabs.com/submit-ticket/).**

If you need to display the current year, and have it always kept up to date, inserting this plugin's `[y]` shortcode almost anywhere on your web site will do just that.

If you need to display an official Copyright notice, inserting this plugin's `[c]` shortcode provides the official C with a circle around it ("&copy;") required legally in many countries to Copyright many forms of intellectual property.

Used on its own, this plugin allows you to place the Copyright symbol and/or the current year in the body of a Page or Post.  For other areas of your web site, you will need to install the [jonradio Shortcodes Anywhere or Everywhere](http://wordpress.org/plugins/jonradio-shortcodes-anywhere-or-everywhere/ "jonradio Shortcodes Anywhere or Everywhere") plugin.  Such places as Page Titles, Post Titles, Custom Fields of both Pages and Posts, Site Title, Description and other bloginfo options, Widgets and Widget Titles, which covers most Sidebars, Menus, Headers and even the HTML `<title>` element that appears in the Title Bar of most browsers.

The `[y]` shortcode inserts the four digit current year into any Page or Post no matter whether you use the Visual or the HMTL editor tab in Edit Post or Edit Page.  The `[c]` shortcode inserts the &copy; Copyright symbol, using the official HTML coding for a copyright symbol.  To address restrictions within older WordPress versions of using two Shortcodes side-by-side, the `[cy]` shortcode inserts the &copy; Copyright symbol, a blank and the current year.

For example, if you are continually refining an older article that you want to copyright on a WordPress Page, you could put the following at the bottom of the page:
`[c] 1997-[y] John Smith`
Assuming it is currently the year 2014, that would display as:

&copy; 1997-2014 John Smith

In 2015, it would automatically display as &copy; 1997-2015 John Smith without anyone having to edit the Page.

> <strong>Adoption Notice</strong><br>
> This plugin was recently adopted by David Gewirtz and ongoing support and updates will continue. Feel free to visit [David's Lab Notes](http://zatzlabs.com/lab-notes/) for additional details and to sign up for emailed news updates.

Special thanks to Jon 'jonradio' Pearkins for creating the plugin and making adoption possible.

== Installation ==

This section describes how to install the *jonradio Current Year and Copyright Shortcodes* plugin and get it working.

1. Use **Add Plugin** within the WordPress Admin panel to download and install this *jonradio Current Year and Copyright Shortcodes* plugin from the WordPress.org plugin repository (preferred method).  Or download and unzip this plugin, then upload the `/jonradio-current-year-and-copyright-shortcodes/` directory to your WordPress web site's `/wp-content/plugins/` directory.
1. Activate the *jonradio Current Year and Copyright Shortcodes* plugin through the **Installed Plugins** Admin panel in WordPress.  If you have a WordPress Network ("Multisite"), you can either **Network Activate** this plugin, or Activate it individually on the sites where you wish to use it.  Activating on individual sites within a Network avoids some of the confusion created by WordPress' hiding of Network Activated plugins on the Plugin menu of individual sites.  Alternatively, to avoid this confusion, you can install the [jonradio Reveal Network Activated Plugins](http://wordpress.org/plugins/jonradio-reveal-network-activated-plugins/ "jonradio Reveal Network Activated Plugins") plugin.
1. Insert the `[y]`, `[c]` and/or `[cy]` shortcodes as many times as you wish in one or more WordPress Pages or Posts.

== Frequently Asked Questions ==

= Why are some or all of the Shortcodes showing instead of their values being displayed? =

This can happen if this plugin is not activated on the site you are using it on.  Or if you mistyped the name of the Shortcode.

If you need to use a Shortcode other than in the body of a Page or Post, you will also need the [jonradio Reveal Network Activated Plugins](http://wordpress.org/plugins/jonradio-reveal-network-activated-plugins/ "jonradio Reveal Network Activated Plugins") plugin.  It must be activated and have the relevant Settings selected that correspond with where you have inserted the Shortcodes.

= How can I change the names of the Shortcodes? =

If there is enough interest, we will add that capability to a future version.  Please let us know, including your ideas on how you would like it to work, and any shortcode naming conflicts with other plugins that you found.

You can fill out this [Contact Form (click here)](http://zatzlabs.com/contact-us/ "Contact Form").

== Screenshots ==

1. `[c] 1997-[y] John Smith` as it was displayed in the year 2012

== Changelog ==

= 1.0 =
* Copyright shortcode added
* Current Year Shortcode converted from Shortcode Exec PHP plugin to standalone Plugin

== Upgrade Notice ==

= 1.0 =
Production version, updated to meet WordPress Repository standards