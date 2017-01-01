<?php
/*
Plugin Name: Site Last Modified
Plugin URI: 
Description: Adds the possibility to display the date and time that the website was last modified, both directly by a function in the template or with a shortcode.
Version: 1.0.6
Author: EXED internet (MR,BHdH)
Author URI: http://www.exed.nl/
License: GPLv2 or later
*/
/*  Copyright 2013  EXED internet  (email : service@exed.nl)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// shortcode
add_shortcode( 'site_last_modified', 'get_site_last_modified' );

/**
 * @var date_format string
 * @return string date
 */
function get_site_last_modified( $atts ) {
  global $wpdb;

	if ( is_array( $atts ) ) {
		extract( shortcode_atts( array(
			'date_format' => '', //use site wide format (default)
			), $atts ) );
	} else {
		$date_format = $atts;
	}

	$query = "SELECT post_modified
			FROM $wpdb->posts
			ORDER BY post_date DESC
		";
	//Using $wpdb for a cached result
	$site_mod_date = $wpdb->get_var( $query );

	if ( empty( $date_format ))
		$date_format = get_option( 'date_format' ) . ' - ' . get_option( 'time_format' );

	return mysql2date( $date_format, $site_mod_date );
}

/**
 * @var date_format string
 * Displays the date / time of the last modified post
 */
function site_last_modified( $date_format = '' ) {
	echo get_site_last_modified( $date_format );
}
