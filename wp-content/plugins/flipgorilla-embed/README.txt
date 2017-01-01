=== Flipgorilla Embed ===
Contributors: flipgorilla.com
Tags: flipgorilla, flipbook, pageflip, html5, css3, responsive
Requires at least: 3.5.1
Tested up to: 3.9.1
Stable tag: 1.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

flipgorilla is an online service which converts PDFs to flipbooks. Free accounts are available!
This plugin allows to easily create, manage and embed flipgorilla flipbooks in WordPress.

== Description ==

This plugin allows to create and configure flipgorilla flipbooks for easy embedding via a wordpress shortcode.

The plugin has two main pages. The "flipgorilla" and "manage" page.

The "flipgorilla" page
is used to view and manage your flipgorilla account from within the plugin.
It allows logging on to the flipgorilla website, to manage the publications and to copy the respective flipgorilla link to create a new flipbook entry.

The "manage" page
allows to set the display options (width, height, layout, responsiveness) for each individual publication.

== Installation ==

1. Extract the contents of the flipgorilla-plugin archive to `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How can I embed a flipgorilla flipbook via the wordpress content-editor? =

* If you defined a flipbook via this plugin, it is very easy to embed it by using the shortcode presented on the "manage" page, like "[flipgorilla flipbook='1']", and copy this into your wordpress content-editor

* You can also embed a flibgorilla flipbook by defining all/some of it's properties directly on the shortcode [flipgorilla id='23023990364702936' width='1000' height='800' layout='1' responsive='true' ]

* Furthermore you can overwrite the properties for an already defined flipbook by adding the attributes and it's values on the shortcode direclty, like [flipgorilla flipbook="1" id='23023990364702936' width='1000' height='800' layout='1' responsive='false' ]

== Screenshots ==

1. Screenshot of the flipgorilla-plugin main-page
2. Screenshot of the flipgorilla-plugin manage-page

== Changelog ==

= 1.0 =
* Initial Version of the plugin

= 1.0.1 =
* Fixed CSS incompatibilities

= 1.0.2 =
* Fixed backend bug with layout

== Upgrade Notice ==

= 1.0.1 =
* fixed CSS incompatibilities

= 1.0.2 =
* Fixed backend bug with layout

= 1.0.3 =
* Fixed some bugs

= 1.0 =
* Use this plugin to easily embed flipgorillla flipbooks to your wordpress-blog