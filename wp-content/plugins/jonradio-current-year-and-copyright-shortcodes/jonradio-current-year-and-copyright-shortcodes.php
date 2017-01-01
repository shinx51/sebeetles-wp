<?php
/*
Plugin Name: Current Year and Copyright Shortcodes
Plugin URI: http://zatzlabs.com/plugins/
Description: Provides Shortcodes to display the Current Year and/or a Copyright symbol.
Version: 1.0.1
Author: David Gewirtz
Author URI: http://zatzlabs.com/plugins/
License: GPLv2
*/

/*  Copyright 2012  jonradio  (email : info@zatz.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_shortcode( 'y', 'jr_cy_y' );

/**
 * [y] Shortcode
 * 
 * Returns the Current Year as a string in four digits.
 *
 * @return   string              Current Year
 */
function jr_cy_y() {
	$date = getdate();
	return $date['year'];
}

add_shortcode( 'c', 'jr_cy_c' );

/**
 * [c] Shortcode
 * 
 * Returns the Copyright Symbol.
 *
 * @return   string              HTML for a Copyright symbol
 */
function jr_cy_c() {
	return '&copy;';
}

add_shortcode( 'cy', 'jr_cy_cy' );

/**
 * [cy] Shortcode
 * 
 * Returns the Copyright Symbol followed by the current year, with a blank between.
 *
 * @return   string              HTML for a Copyright symbol, blank, current year
 */
function jr_cy_cy() {
	return jr_cy_c() . ' ' . jr_cy_y();
}

?>