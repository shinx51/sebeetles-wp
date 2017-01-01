<?php 
/*  
Plugin Name: Search In Place Advanced
Plugin URI: http://wordpress.dwbooster.com/content-tools/search-in-place
Version: 5.0.5
Author: <a href="http://www.codepeople.net">CodePeople</a>
Description: Search in Place improves blog search by displaying query results in real time. Search in place displays a list with results dynamically as you enter the search criteria. Search in place groups search results by their type, labeling them as post, page, or attachment, and highlights the searched terms. To get started: 1) Click the "Activate" link to the left of this description.
*/

include 'php/searchinplace.clss.php';

	//Initialize the admin panel 
	if (!function_exists("CodePeopleSearchInPlaceAdvanced_admin")) { 
		function CodePeopleSearchInPlaceAdvanced_admin() { 
			global $codepeople_search_in_place_advanced_obj; 
			if (!isset($codepeople_search_in_place_advanced_obj)) { 
				return; 
			} 
			if (function_exists('add_options_page')) { 
				add_options_page('Search In Place Advanced', 'Search In Place Advanced', 'manage_options', basename(__FILE__), array(&$codepeople_search_in_place_advanced_obj, 'printAdminPage')); 
			} 
		}    
	}
	
	// Initialize the public website code
	if(!function_exists("CodePeopleSearchInPlaceAdvanced")){	
		function CodePeopleSearchInPlaceAdvanced(){
			global $codepeople_search_in_place_advanced_obj;
			
			if (is_admin ())
				return false;

			wp_enqueue_style('codepeople-search-in-place-advanced-style', plugin_dir_url(__FILE__).'css/codepeople_shearch_in_place.css');
			wp_enqueue_script('codepeople-search-in-place-advanced', plugin_dir_url(__FILE__).'js/codepeople_shearch_in_place.js', array('jquery'));
			wp_localize_script('codepeople-search-in-place-advanced', 'codepeople_search_in_place_advanced', $codepeople_search_in_place_advanced_obj->javascriptVariables());
		}
	}	

$codepeople_search_in_place_advanced_obj = new CodePeopleSearchInPlaceAdvanced();
$codepeople_search_in_place_advanced_obj->init();

// Plugin activation
register_activation_hook( __FILE__, array( &$codepeople_search_in_place_advanced_obj, 'activePlugin' ) );
add_action( 'wpmu_new_blog', array( &$codepeople_search_in_place_advanced_obj, 'install_new_blog' ), 10, 6 );

$plugin = plugin_basename(__FILE__);
add_filter('plugin_action_links_'.$plugin, array(&$codepeople_search_in_place_advanced_obj, 'customizationLink'));
add_filter('plugin_action_links_'.$plugin, array(&$codepeople_search_in_place_advanced_obj, 'settingsLink'));
add_action('pre_get_posts', array(&$codepeople_search_in_place_advanced_obj, 'modifySearch'));
add_filter('posts_request', array(&$codepeople_search_in_place_advanced_obj, 'modifySearchQuery'));

add_action('init', 'CodePeopleSearchInPlaceAdvanced');
add_action('admin_menu', 'CodePeopleSearchInPlaceAdvanced_admin');
add_action('wp_ajax_nopriv_search_in_place', array(&$codepeople_search_in_place_advanced_obj, 'populate'));
add_action('wp_ajax_search_in_place', array(&$codepeople_search_in_place_advanced_obj, 'populate'));
add_action('wp_head', array(&$codepeople_search_in_place_advanced_obj, 'setStyles'));
?>