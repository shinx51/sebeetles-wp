=== Site Last Modified ===
Contributors: exed internet
Version: 1.0.6
Tags: website info, modified, last, date, time
Stable tag: 1.0.6
Requires at least: 2.5
Tested up to: 4.1.0
License: GPLv2 or later

== Description ==
Adds the possibility to display the date and time that the website was last modified, both directly by a function in the template or with a shortcode.
The plugin uses the timestamp of the last modified post for this information.

Please note, this plugin does not show the specific date per post, there are other plugins for that feature.

Requires WordPress 2.5 and php 5.

= Contact =
In case of suggestions or improvements please contact EXED at service@exed.nl, or through the GitHub repository (see below).

= More information =
Read the following items for more information:

* Follow EXED internet on [Facebook](https://www.facebook.com/MixComMediaGroup), [Twitter](https://twitter.com/MixComMedia) and our [blog](https://mixcom.nl/blog) *(Dutch only)*
* Other WordPress Plugins by EXED internet - http://profiles.wordpress.org/exed-internet/
* EXED internet official website - [https://mixcom.nl/online](https://mixcom.nl/online?utm_source=wordpress&utm_medium=social%2Bmedia)

== Installation ==
1. Download the code from Wordpress or GitHub https://github.com/exed/WP-Site-Last-Modified/archive/1.0.zip
2. Upload `site-last-modified.php` to the `/wp-content/plugins/` directory.
3. Activate the plugin through the `Plugins` menu in WordPress.

== Configuration ==
Custom date / time formats can be passed into the function or into the shortcode,
see <http://php.net/date> for possible values.

= Examples =

**Template**

Place `<?php site_last_modified(); ?>` in your templates.  
Dutch date/time example: `<?php site_last_modified('l j F Y, H.i.s'); ?>`

**Shortcode**

Use `[site_last_modified]` directly in your post.  
Dutch date/time example: `[site_last_modified date_format="l j F Y, H.i.s"]`

== Frequently Asked Questions ==
Does this plugin support a shortcode?  
A:  Yes, this is `[site_last_modified]`. You can add a custom date format, e.g. : `[site_last_modified date_format="Y-m-d H:i:s"]`

Where can I find valid time/date formats?  
A: http://php.net/date

How can I implement this date in my template?  
A: `<?php site_last_modified(); ?>`.  You can add a custom date format, e.g. : `<?php site_last_modified( "Y-m-d H:i:s" );?>`

== Changelog ==

= 1.0.6 =
Added support for WordPress 4.1

= 1.0.4 =
Improved readme

= 1.0 =
First release, supplying template function and shortcode to display the date your site was last modified.
