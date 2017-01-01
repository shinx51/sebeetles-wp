<?php

class CodePeopleSearchInPlaceAdvanced {
		
	
	private $text_domain = 'codepeople_search_in_place';
	private $javascriptVariable;
	private $id_list = array();
    private $plugins_list = array(
		'WooCommerce' => array(
			'post_type' => array(
				0 => array(
					'name'  => 'product',
					'label' => 'Product'
				)
			),
			'taxonomy' => array(
				0 => array(
					'name' => 'product_cat'
				),
				
				1 => array(
					'name' => 'product_tag'
				)
			)
		),
		
		'WP e-Commerce' => array(
			'post_type' => array(
				0 => array(
					'name'  => 'wpsc-product',
					'label' => 'Product'
				)
			),
			'taxonomy' => array(
				0 => array(
					'name' => 'product_tag'
				)
			)
		),
		
		'Jigoshop' => array(
			'post_type' => array(
				0 => array(
					'name'  => 'product',
					'label' => 'Product'
				)
			),
			'taxonomy' => array(
				0 => array(
					'name' => 'product_cat'
				),
				
				1 => array(
					'name' => 'product_tag'
				)
			)
		),
		
		'Ready! Ecommerce Shopping Cart' => array(
			'post_type' => array(
				0 => array(
					'name'  => 'product',
					'label' => 'Product'
				)
			),
			'taxonomy' => array(
				0 => array(
					'name' => 'products_categories'
				),
				
				1 => array(
					'name' => 'products_brands'
				)
			)
		)
	);
	
	/*
		Load the language file and initialize the javascript object to pass to the client side
	*/
	function init(){
		// I18n
		load_plugin_textdomain($this->text_domain, false, dirname( plugin_basename( __FILE__ ) ) . '/../languages/');
		$this->javascriptVariables = array(
									'more'  => __('More Results', $this->text_domain),
									'empty' => __('0 results', $this->text_domain),
									'char_number' => get_option('search_in_place_advanced_minimum_char_number'),
									'root'	 => trim( get_admin_url( get_current_blog_id() ), '/').'/',
									'home'	 => get_home_url( get_current_blog_id() )
							);

		// Fake variables to allow the translation for Poedit application
		$a = __('post', $this->text_domain); 		
		$a = __('page', $this->text_domain); 		
	} // End init
	
	
	public function javascriptVariables(){
		return $this->javascriptVariables;
	} // End javascritpVariables
	
	private function _searchQuery()
	{
		if( !empty( $_GET[ 's' ] ) )
		{	
			global $wpdb;
			$limit = get_option('search_in_place_advanced_number_of_posts'); // Number of results to display
			$metadata = get_option('search_in_place_advanced_post_metadata'); // Search in metadata
			$connection_operator = get_option( 'search_in_place_advanced_connection_operator', 'or' );
			$connection_operator = " ".( ( empty( $connection_operator ) ) ? "or" : $connection_operator )." ";

			// Get the posts and pages with the search terms
			$s = stripslashes($_GET['s']);
			
			$search = '';
			$taxonomy_search = '';
			$searchor = '';
			$search_connection_operator = '';
			
			preg_match_all('/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $s, $matches);
			$search_terms = array_map(create_function('$a', 'return trim($a, "\\"\'\\n\\r ");'), $matches[0]);

			foreach($search_terms as $term){
				$search .= "{$search_connection_operator}((wp_posts.post_title LIKE '%{$term}%') OR
						   (wp_posts.post_content LIKE '%{$term}%') OR
						   (wp_posts.post_name LIKE '%{$term}%') OR
						   (wp_posts.post_excerpt LIKE '%{$term}%')";
				
				$taxonomy_search .= "{$searchor}(terms.slug LIKE '%{$term}%')";					   
				
				if($metadata){
					$search .= " OR (CAST(wp_postmeta.meta_value AS CHAR) LIKE '%{$term}%')";
				}
				
				$search .= ")";	
				$searchor = " OR ";
				$search_connection_operator = $connection_operator;
			}
			
			$post_type_arr = get_option('search_in_place_advanced_post_type');
			$label_arr = array();
			
			if($post_type_arr !== false){
				$post_type = array();
				foreach($post_type_arr as $post_type_item){
					if(is_object($post_type_item)){
						$post_type[] = $post_type_item->name;
						if(!empty($post_type_item->label)){
							$label_arr[$post_type_item->name] = $post_type_item->label;
						}
					}else{
						$post_type[] = $post_type_item;
						$label_arr[$post_type_item] = $post_type_item;
					}
				}
			}else{
				$post_type = array('post', 'page');
			}
			
			$taxonomy_arr = get_option('search_in_place_advanced_taxonomy');
			$post_from_taxonomy_querystr = '';
			if($taxonomy_arr){
				$taxonomy_querystr  = 'SELECT term_taxonomy.term_id, term_taxonomy.taxonomy FROM ';
				$taxonomy_querystr .= $wpdb->prefix.'terms as terms INNER JOIN '.$wpdb->prefix.'term_taxonomy as term_taxonomy ON (terms.term_id = term_taxonomy.term_id) ';
				$taxonomy_querystr .= 'WHERE '.$taxonomy_search;
				
				$terms = $wpdb->get_results($taxonomy_querystr);
				$terms_id_list = array();
				foreach ($terms as $term){
					if(taxonomy_exists($term->taxonomy)){
						$terms_id_list = array_merge($terms_id_list, (array)$term->term_id, (array)get_term_children($term->term_id, $term->taxonomy));
					}
				}
				
				$post_from_taxonomy_querystr .= "(SELECT SQL_CALC_FOUND_ROWS wp_posts.* FROM ".$wpdb->prefix."posts as wp_posts INNER JOIN (".$wpdb->prefix."term_taxonomy as term_taxonomy INNER JOIN ".$wpdb->prefix."term_relationships as term_relationships ON (term_taxonomy.term_taxonomy_id=term_relationships.term_taxonomy_id)) ON (wp_posts.ID = term_relationships.object_id)
				WHERE term_taxonomy.term_id IN ('".implode("','", $terms_id_list)."') AND wp_posts.post_type IN ('".implode("','", $post_type)."') AND (wp_posts.post_status = 'publish'))";
				
				$post_from_taxonomy_querystr .= " UNION ";
			}
			
			$query = "
				{$post_from_taxonomy_querystr}
				(SELECT wp_posts.* 
				FROM $wpdb->posts AS wp_posts  LEFT JOIN $wpdb->postmeta AS wp_postmeta ON (wp_posts.ID = wp_postmeta.post_id) 
				WHERE 1=1  AND ((
				$search
				))  
				AND wp_posts.post_type IN ('".implode("','", $post_type)."') AND (wp_posts.post_status = 'publish') GROUP BY wp_posts.ID) ORDER BY post_date DESC LIMIT 0, $limit
			";
			return array( 'query' => $query, 'labels' => $label_arr );
		}
		return false;	
	} // End _searchQuery
	
    public function modifySearchQuery( $query )
	{
		if( !is_admin() && is_search() && isset( $_GET[ 's' ] ) )
		{
			$arr = $this->_searchQuery();
			if( $arr !== false ) $query = $arr[ 'query' ];
		}	

		return $query;	
	} // End modifySearchQuery
	
	public function populate() {
		global $wpdb;
		$post_list = array();
		$arr = $this->_searchQuery();
		if( $arr !== false )
		{	
			$querystr  = $arr[ 'query' ];
			$label_arr = $arr[ 'labels' ];
			$s = stripslashes($_GET['s']);
			
			$posts = $wpdb->get_results($querystr, OBJECT);

			foreach($posts as $result){
				
				if(in_array($result->ID, $this->id_list)){
					continue;
				}else{
					array_push($this->id_list, $result->ID);
				}
				
				$obj = new stdClass();
				
				// Include the author in search results
				if(get_option('search_in_place_advanced_display_author') == 1){
					$author = get_userdata($result->post_author);
					$obj->author = $author->display_name;
				}	
				
				// The link to the item is required
				$obj->link = get_permalink($result->ID);

				$obj->link .= ((strpos($obj->link, '?') === false) ? '?' : '&').'highlight='.urlencode($_GET['s']);

				// Include the thumbnail in search results
				if(get_option('search_in_place_advanced_display_thumbnail')){
				   if ( function_exists('has_post_thumbnail') && has_post_thumbnail($result->ID) ) {
						// If post thumbnail is used
						$obj->thumbnail = wp_get_attachment_thumb_url(get_post_thumbnail_id($result->ID, 'thumbnail'));
					}elseif(function_exists('get_post_image_id')) {
						// Support for WP 2.9 post thumbnails
						$imgID = get_post_image_id($result->ID);
						$img = wp_get_attachment_image_src($imgID, apply_filters('post_image_size', 'thumbnail'));
						$obj->thumbnail = $img[0];
					}
					else {
						// If not post thumbnail, grab the first image from the post
						// Get images for this post
						
						$imgArr = @get_children('post_type=attachment&post_mime_type=image&post_parent=' . $result->ID );
						
						// If images exist for this page
						if( !empty( $imgArr ) ) {
							$flag = PHP_INT_MAX;
							
							foreach($imgArr as $img) {
								if($img->menu_order < $flag){
									$flag = $img->menu_order;
									$img_selected = $img;	
								}
							}
							$obj->thumbnail = wp_get_attachment_thumb_url($img_selected->ID);
						}
					}
					
				}
				
				// Include a post summary in search results, the summary is limited to the number of letters declared in configuration
				if(get_option('search_in_place_advanced_display_summary')){
					$length = get_option('search_in_place_advanced_summary_char_number');
					if(!empty($result->post_excerpt)){
						$resume = preg_replace( '/\[[^\]]*\]/', '', $result->post_excerpt );
						$resume = substr(apply_filters("localization", $resume), 0, $length);
					}else{
						$resume = preg_replace( '/\[[^\]]*\]/', '', $result->post_content );
						$c = strip_tags(apply_filters("localization", $resume));
						$l = strlen($c);
						$p = strpos(strtolower($c), strtolower($s));
						
						$p = ($p !== false && $p-$length/2 > 0) ? $p-$length/2 : 0;
						
						// Start the summary from the begining of word
						if($p > 0){
							if($c[$p] == ' '){
								$p++;
							}elseif($c[$p-1] !== ' '){
								$k = strrpos($c, " ", -1*($l-$p));
								$k = ($k < 0) ? 0 : $k+1;
								$length += $p-$k;
								$p = $k;
							}	
						}
						$resume = substr($c, $p, $length);
					}
					
					// Set the search terms in bold
					$obj->resume = preg_replace('/('.$s.')/i', '<strong>$1</strong>', $resume).'[...]';
				}	
				
				// Include the publication date in search results
				if(get_option('search_in_place_advanced_display_date')){
					$obj->date = date_i18n(get_option('search_in_place_advanced_date_format'), strtotime($result->post_date));
				}	
				
				// The post title is a required field
				$obj->title = apply_filters("localization", $result->post_title); 
				
				$type = $result->post_type;
				if(!isset($post_list[$type])){
					$post_list[$type] = new stdClass();
					$post_list[$type]->items = array();
					if(isset($label_arr[$type]))
						$post_list[$type]->label = apply_filters("localization", $label_arr[$type]);
				}
				$post_list[$type]->items[] = $obj;
				
			}
		}
		print json_encode($post_list);die;
	
	} // End populate
	
	/*
		modifySearch add new attributes to the javascript variable for higlight the search terms
	*/
	function modifySearch( &$query ){
    
        if(!is_admin() && ( $query->is_search || isset( $_GET['highlight'] ) )){
            $terms = preg_replace('/[\s+\+]/', ' ', ($query->is_search) ? $query->query_vars['s'] : $_GET['highlight']);
			$this->javascriptVariables['terms'] = explode(' ', $terms);
            
			if($query->is_search){
                $pt = get_option('search_in_place_advanced_post_type', array('post', 'page'));
                foreach($pt as $k => $v){
                    if(is_object($v)){
                        if(isset($v->name)){
                            $pt[$k] = $v->name;
                        }else{
                            unset($pt[$k]);
                        }
                    }
                }
                
                $query->set('post_type', $pt);
                $query->set('post_status', array('publish'));

                // Search page
				$this->javascriptVariables['highlight'] = get_option('search_in_place_advanced_highlight');
				$this->javascriptVariables['highlight_resulting_page'] = 0;
				$this->javascriptVariables['identify_post_type'] = get_option('search_in_place_advanced_mark_post_type');
				$post_types = get_option('search_in_place_advanced_post_type');
                
				if($post_types){
					$this->javascriptVariables['post_types'] = json_encode($post_types);
				}
			}else{
				// Resulting page
				$terms = preg_replace('/[\s+\+]/', ' ', $_GET['highlight']);
				$this->javascriptVariables['highlight'] = 0;
				$this->javascriptVariables['highlight_resulting_page'] = get_option('search_in_place_advanced_highlight_resulting_page');
				$this->javascriptVariables['identify_post_type'] = 0;
			}
			
			wp_localize_script('codepeople-search-in-place-advanced', 'codepeople_search_in_place_advanced', $this->javascriptVariables);
        }
    } // End modifySearch
	
	/*
		Set a link to plugin settings
	*/
	function settingsLink($links) { 
		$settings_link = '<a href="options-general.php?page=codepeople_search_in_place_advanced.php">'.__('Settings').'</a>'; 
		array_unshift($links, $settings_link); 
		return $links; 
	} // End settingsLink
	
	/*
		Set a link to contact page
	*/
	function customizationLink($links) { 
		$settings_link = '<a href="http://wordpress.dwbooster.com/contact-us" target="_blank">'.__('Request custom changes').'</a>'; 
		array_unshift($links, $settings_link); 
		return $links; 
	} // End customizationLink
 
	/**
		Print out the admin page
	*/
	function printAdminPage(){
		// Load the picker color resources
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );
		
		if(isset($_POST['search_in_place_advanced_submit'])){
			
			echo '<div class="updated"><p><strong>'.__("Settings Updated").'</strong></div>';
			
			$_POST['number_of_posts'] = $_POST['number_of_posts']*1;
			$_POST['minimum_char_number'] = $_POST['minimum_char_number']*1;
			$_POST['summary_char_number'] = $_POST['summary_char_number']*1;
			
			$search_in_place_advanced_number_of_posts = (!empty($_POST['number_of_posts']) && is_int($_POST['number_of_posts']) && $_POST['number_of_posts'] > 0) ? $_POST['number_of_posts'] : 10;
			$search_in_place_advanced_minimum_char_number = (!empty($_POST['minimum_char_number']) && is_int($_POST['minimum_char_number']) && $_POST['minimum_char_number'] > 0) ? $_POST['minimum_char_number'] : 3;
			$search_in_place_advanced_summary_char_number = (!empty($_POST['summary_char_number']) && is_int($_POST['summary_char_number']) && $_POST['summary_char_number'] >= 0) ? $_POST['summary_char_number'] : 20;
			$search_in_place_advanced_date_format = $_POST['date_format'];
			$search_in_place_advanced_display_thumbnail = (!empty($_POST['thumbnail'])) ? $_POST['thumbnail'] : 0;
			$search_in_place_advanced_display_date = (!empty($_POST['date'])) ? $_POST['date'] : 0;
			$search_in_place_advanced_display_summary = (!empty($_POST['summary'])) ? $_POST['summary'] : 0;
			$search_in_place_advanced_display_author = (!empty($_POST['author'])) ? $_POST['author'] : 0;
			$search_in_place_advanced_highlight = (isset($_POST['highlight'])) ? 1 : 0;
			$search_in_place_advanced_highlight_resulting_page = (isset($_POST['highlight_resulting_page'])) ? 1 : 0;
			$search_in_place_advanced_mark_post_type = (isset($_POST['mark_post_type'])) ? 1 : 0;
			$search_in_place_advanced_post_metadata = (isset($_POST['post_metadata'])) ? 1 : 0;
			$search_in_place_advanced_post_type = array();
			$search_in_place_advanced_taxonomy = array();
			$search_in_place_advanced_connection_operator = ( !empty( $_POST[ 'connection_operator' ] ) ) ? $_POST[ 'connection_operator' ] : 'or';
			
			if(isset($_POST['search_in_place_advanced_post_type'])){
				foreach($_POST['search_in_place_advanced_post_type'] as $key => $post_type){
					if(!empty($post_type)){
						$post_type_obj = new stdClass();
						$post_type_obj->name = $post_type;
						if(!empty($_POST['search_in_place_advanced_post_type_label'][$key]))
							$post_type_obj->label = $_POST['search_in_place_advanced_post_type_label'][$key];
						$search_in_place_advanced_post_type[] = $post_type_obj;
					}
				}
			}
			
			if(isset($_POST['search_in_place_advanced_taxonomy'])){
				foreach($_POST['search_in_place_advanced_taxonomy'] as $taxonomy){
					if(!empty($taxonomy)){
						$search_in_place_advanced_taxonomy[] = $taxonomy;
					}
				}
			}
			
			if(!empty($_POST['box_background_color'])) $box_background_color = $_POST['box_background_color'];
			if(!empty($_POST['box_border_color'])) $box_border_color = $_POST['box_border_color'];
			if(!empty($_POST['label_text_color'])) $label_text_color = $_POST['label_text_color'];
			if(!empty($_POST['label_text_shadow'])) $label_text_shadow = $_POST['label_text_shadow'];
			if(!empty($_POST['label_background_start_color'])) $label_background_start_color = $_POST['label_background_start_color'];
			if(!empty($_POST['label_background_end_color'])) $label_background_end_color = $_POST['label_background_end_color'];
			if(!empty($_POST['active_item_background_color'])) $active_item_background_color = $_POST['active_item_background_color'];
			
			update_option('search_in_place_box_background_color', $box_background_color);
			update_option('search_in_place_box_border_color', $box_border_color);
			update_option('search_in_place_label_text_color', $label_text_color);
			update_option('search_in_place_label_text_shadow', $label_text_shadow);
			update_option('search_in_place_label_background_start_color', $label_background_start_color);
			update_option('search_in_place_label_background_end_color', $label_background_end_color);
			update_option('search_in_place_active_item_background_color', $active_item_background_color);
			
			update_option('search_in_place_advanced_number_of_posts', $search_in_place_advanced_number_of_posts);
			update_option('search_in_place_advanced_minimum_char_number', $search_in_place_advanced_minimum_char_number);
			update_option('search_in_place_advanced_summary_char_number', $search_in_place_advanced_summary_char_number);
			update_option('search_in_place_advanced_date_format', $search_in_place_advanced_date_format);
			update_option('search_in_place_advanced_display_thumbnail', $search_in_place_advanced_display_thumbnail);
			update_option('search_in_place_advanced_display_date', $search_in_place_advanced_display_date);
			update_option('search_in_place_advanced_display_summary', $search_in_place_advanced_display_summary);
			update_option('search_in_place_advanced_display_author', $search_in_place_advanced_display_author);
			update_option('search_in_place_advanced_highlight', $search_in_place_advanced_highlight);
			update_option('search_in_place_advanced_highlight_resulting_page', $search_in_place_advanced_highlight_resulting_page);
			update_option('search_in_place_advanced_mark_post_type', $search_in_place_advanced_mark_post_type);
			update_option('search_in_place_advanced_post_metadata', $search_in_place_advanced_post_metadata);
			update_option('search_in_place_advanced_post_type', $search_in_place_advanced_post_type);
			update_option('search_in_place_advanced_taxonomy', $search_in_place_advanced_taxonomy);
			update_option('search_in_place_advanced_connection_operator', $search_in_place_advanced_connection_operator);
			
		}else{
			$search_in_place_advanced_number_of_posts = get_option('search_in_place_advanced_number_of_posts');
			$search_in_place_advanced_minimum_char_number = get_option('search_in_place_advanced_minimum_char_number');
			$search_in_place_advanced_summary_char_number = get_option('search_in_place_advanced_summary_char_number');
			$search_in_place_advanced_date_format = get_option('search_in_place_advanced_date_format');
			$search_in_place_advanced_display_thumbnail = get_option('search_in_place_advanced_display_thumbnail');
			$search_in_place_advanced_display_date = get_option('search_in_place_advanced_display_date');
			$search_in_place_advanced_display_summary = get_option('search_in_place_advanced_display_summary');
			$search_in_place_advanced_display_author = get_option('search_in_place_advanced_display_author');
			$search_in_place_advanced_highlight = get_option('search_in_place_advanced_highlight');
			$search_in_place_advanced_highlight_resulting_page = get_option('search_in_place_advanced_highlight_resulting_page');
			$search_in_place_advanced_mark_post_type = get_option('search_in_place_advanced_mark_post_type');
			$search_in_place_advanced_post_metadata = get_option('search_in_place_advanced_post_metadata');
			$search_in_place_advanced_post_type = get_option('search_in_place_advanced_post_type');
			$search_in_place_advanced_taxonomy = get_option('search_in_place_advanced_taxonomy');
			$search_in_place_advanced_connection_operator = get_option('search_in_place_advanced_connection_operator', 'or' );
			if( empty( $search_in_place_advanced_connection_operator ) ) $search_in_place_advanced_connection_operator = 'or';
			
			$box_background_color = get_option('search_in_place_box_background_color');
			$box_border_color = get_option('search_in_place_box_border_color');
			$label_text_color = get_option('search_in_place_label_text_color');
			$label_text_shadow = get_option('search_in_place_label_text_shadow');
			$label_background_start_color = get_option('search_in_place_label_background_start_color');
			$label_background_end_color = get_option('search_in_place_label_background_end_color');
			$active_item_background_color = get_option('search_in_place_active_item_background_color');
			
		}
?>
<script>
	function remove_element(e, clss){
		var p = jQuery(e).parents(clss);
		if(p.length){
			p.next('br').remove();
			p.remove();	
		}
	}
	
	function add_new_type(e, post_type_name, post_type_label){
		jQuery(e).before('<span class="post-type-container"><input type="text" name="search_in_place_advanced_post_type[]" class="post-type" value="'+((post_type_name) ? post_type_name : '')+'" /> <input type="text" name="search_in_place_advanced_post_type_label[]" class="post-type" value="'+((post_type_label) ? post_type_label : '')+'" /><input type="button" value="Remove type" onclick="remove_element(this, \'.post-type-container\');" /></span><br />');
	}
	
	function add_new_taxonomy(e, taxonomy){
		jQuery(e).before('<span class="taxonomy-container"><input type="text" name="search_in_place_advanced_taxonomy[]" class="taxonomy" value="'+((taxonomy) ? taxonomy : '' )+'" /><input type="button" value="Remove taxonomy" onclick="remove_element(this, \'.taxonomy-container\');" /></span><br />');
	}
	
	function plugin_selected(index){
		var plugin = search_in_place_advanced_plugin_list[index] || {};
		// Add the plugins post_type
		if (plugin['post_type']){
			var btn = '#add_post_type',
				post_types = plugin['post_type'];
			
			for(var i in post_types){
				if(jQuery('input[value="'+post_types[i]['name']+'"]').length == 0){
					add_new_type(btn, post_types[i]['name'], post_types[i]['label']);
				}
			}
		}
		
		// Add the plugins taxonomies
		if(plugin['taxonomy']){
			var btn = '#add_taxonomy',
				taxonomies = plugin['taxonomy'];
			
			for(var j in taxonomies){
				if(jQuery('input[value="'+taxonomies[j]['name']+'"]').length == 0){
					add_new_taxonomy(btn, taxonomies[j]['name']);
				}
			}
		}
	}
	
	// Set the picker colors
	jQuery(function(){
		jQuery('#box_background_color_picker').hide();
		jQuery('#box_background_color_picker').farbtastic("#box_background_color");
		jQuery("#box_background_color").click(function(){jQuery('#box_background_color_picker').slideToggle()});
		
		jQuery('#box_border_color_picker').hide();
		jQuery('#box_border_color_picker').farbtastic("#box_border_color");
		jQuery("#box_border_color").click(function(){jQuery('#box_border_color_picker').slideToggle()});
		
		jQuery('#label_text_color_picker').hide();
		jQuery('#label_text_color_picker').farbtastic("#label_text_color");
		jQuery("#label_text_color").click(function(){jQuery('#label_text_color_picker').slideToggle()});
		
		jQuery('#label_text_shadow_picker').hide();
		jQuery('#label_text_shadow_picker').farbtastic("#label_text_shadow");
		jQuery("#label_text_shadow").click(function(){jQuery('#label_text_shadow_picker').slideToggle()});
		
		jQuery('#label_background_start_color_picker').hide();
		jQuery('#label_background_start_color_picker').farbtastic("#label_background_start_color");
		jQuery("#label_background_start_color").click(function(){jQuery('#label_background_start_color_picker').slideToggle()});
		
		jQuery('#label_background_end_color_picker').hide();
		jQuery('#label_background_end_color_picker').farbtastic("#label_background_end_color");
		jQuery("#label_background_end_color").click(function(){jQuery('#label_background_end_color_picker').slideToggle()});
		
		jQuery('#active_item_background_color_picker').hide();
		jQuery('#active_item_background_color_picker').farbtastic("#active_item_background_color");
		jQuery("#active_item_background_color").click(function(){jQuery('#active_item_background_color_picker').slideToggle()});
	});
</script>
<?php		
		echo '
			<div class="wrap">
				<form method="post" action="'.$_SERVER['REQUEST_URI'].'">
					<h2>Search In Place</h2>
					<p  style="border:1px solid #E6DB55;margin-bottom:10px;padding:5px;background-color: #FFFFE0;">'.__('For more information go to the <a href="http://wordpress.dwbooster.com/content-tools/search-in-place" target="_blank">Search in Place</a> plugin page.').' <br />'.__('For any issues with Search in Place, go to our <a href="http://wordpress.dwbooster.com/contact-us" target="_blank">contact page</a> and leave us a message.').'
					<br/><br />'.__('If you want test the premium version of Search in Place go to the following links:<br/> <a href="http://demos.net-factor.com/search-in-place/wp-login.php" target="_blank">Administration area: Click to access the administration area demo</a><br/> <a href="http://demos.net-factor.com/search-in-place/" target="_blank">Public page: Click to access the Search in Place</a>').'
					</p>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="number_of_posts">'.__('Enter the number of posts to display', $this->text_domain).'</label>
								</th>
								<td>
									<input type="text" id="number_of_posts" name="number_of_posts" value="'.$search_in_place_advanced_number_of_posts.'" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="minimum_char_number">'.__('Enter the minimum of characters number for start the search', $this->text_domain).'</label>
								</th>
								<td>
									<input type="text" id="minimum_char_number" name="minimum_char_number" value="'.$search_in_place_advanced_minimum_char_number.'" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="operator">'.__('Connection operator', $this->text_domain).'</label>
								</th>
								<td>
									<input type="radio" name="connection_operator" value="or" '.( ( $search_in_place_advanced_connection_operator == 'or' ) ? 'CHECKED' : '' ).' /> OR&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" name="connection_operator" value="and" '.( ( $search_in_place_advanced_connection_operator == 'and' ) ? 'CHECKED' : '' ).' /> AND <br />
									'.__( 'Get results with any or all of words in the search box.',$this->text_domain ).'
								</td>
							</tr>
						</tbody>
					</table>
					<h3>'.__('Search in', $this->text_domain).'</h3>
					<table class="form-table">	
						<tbody>
							<tr valign="top">
								<th>
									'.__('Posts/Pages common data (title, content):').'
								</th>
								<td>
									<input type="checkbox" name="post_data" id="post_data" checked disabled />
								</td>
							</tr>
							<tr valign="top">
								<th>
									'.__('Posts/Pages metadata (additional data of articles):').'
								</th>
								<td>
									<input type="checkbox" name="post_metadata" id="post_metadata" value="1" '.(($search_in_place_advanced_post_metadata == 1) ? 'checked' : '').' />
								</td>
							</tr>';
		$js_script_code = '<script>
							var search_in_place_advanced_plugin_list = [];
						';					
		if(isset($this->plugins_list) && count($this->plugins_list)){
			echo '					
							<tr>
								<th colspan="2">
								'.__('If you are using in your website some of plugins listed below, press the related button for searching in its custom post-types and taxonomies.').'
								</th>
							</tr>
							<tr>
								<th colspan="2">
							';
			$counter = 0;				
			foreach($this->plugins_list as $plugin_name => $plugin_data){
				echo '<input type="button" class="button-secondary" value="'.$plugin_name.'" onclick="plugin_selected('.$counter.');" /> ';
				$js_script_code .= 'search_in_place_advanced_plugin_list['.$counter.'] = '.json_encode($plugin_data).';';
				$counter++;
			}				
			
			echo '
								</th>
							</tr>';
		}
		$js_script_code .= '</script>';					
		
		
		echo '					
							<tr valign="top">
								<th>
									'.__('Posts Type:').'
								</th>
								<td>
									POST_TYPE  /  LABEL <br />';
	
									if($search_in_place_advanced_post_type){
										foreach($search_in_place_advanced_post_type as $key=>$post_type_obj){
											$post_type_name = $post_type_obj->name;
											$post_type_label = (isset($post_type_obj->label)) ? $post_type_obj->label : '';
											echo '
												<span class="post-type-container">
												<input type="text" name="search_in_place_advanced_post_type[]" class="post-type" value="'.$post_type_name.'" />  
												<input type="text" name="search_in_place_advanced_post_type_label[]" class="post-type" value="'.$post_type_label.'" />
												<input type="button" value="Remove type" onclick="remove_element(this, \'.post-type-container\');" >
												</span><br />
											';
										}
									}
							  echo '<input type="button" id="add_post_type" value="Add new type" class="button-primary" onclick="add_new_type(this);"/>
								</td>
							</tr>
							<tr>
								<th>
									'.__('Taxonomy:').'
								</th>
								<td>';
								if($search_in_place_advanced_taxonomy && count($search_in_place_advanced_taxonomy)){
									foreach($search_in_place_advanced_taxonomy as $taxonomy){
										if(!empty($taxonomy)){
											echo '<span class="taxonomy-container"><input type="text" name="search_in_place_advanced_taxonomy[]" class="taxonomy" value="'.$taxonomy.'" /><input type="button" value="Remove taxonomy" onclick="remove_element(this, \'.taxonomy-container\')"></span><br />';
										}
									}
								}else{
									echo '<span class="taxonomy-container"><input type="text" name="search_in_place_advanced_taxonomy[]" class="taxonomy" value="" /><input type="button" value="Remove taxonomy" onclick="remove_element(this, \'.taxonomy-container\');"></span><br />';			
								}		
					echo'			
									<input type="button" id="add_taxonomy" value="Add new taxonomy" class="button-primary" onclick="add_new_taxonomy(this);"/>
								</td>
							</tr>
						</tbody>
					</table>
					<h3>'.__('Elements to display', $this->text_domain).'</h3>
					<table class="form-table">	
						<tbody>
							<tr valign="top">
								<td>
									<input type="checkbox" checked disabled name="title" id="title"> '.__('Post title', $this->text_domain).' <input type="checkbox" name="thumbnail" id="thumbnail" value="1" '.(($search_in_place_advanced_display_thumbnail == 1) ? 'checked' : '').' /> '.__('Post thumbnail', $this->text_domain).' <input type="checkbox" name="author" value="1" id="author" '.(($search_in_place_advanced_display_author == 1) ? 'checked' : '').' /> '.__('Post author', $this->text_domain).' <input type="checkbox" name="date" id="date" value="1" '.(($search_in_place_advanced_display_date == 1) ? 'checked' : '').' /> '.__('Post date', $this->text_domain).' <input type="checkbox" name="summary" id="summary" value="1" '.(($search_in_place_advanced_display_summary == 1) ? 'checked' : '').' /> '.__('Post summary', $this->text_domain).'
								</td>
							</tr>
						</tbody>
					</table>	
					<table class="form-table">	
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="date_format">'.__("Select the date format", $this->text_domain).'</label>
								</th>
								<td>
									<select name="date_format" id="date_format" style="width:135px;">
										<option value="Y-m-d" '.(($search_in_place_advanced_date_format == 'Y-m-d') ? 'selected' : '').'>yyyy-mm-dd</option>
										<option value="Y-d-m" '.(($search_in_place_advanced_date_format == 'Y-d-m') ? 'selected' : '').'>yyyy-dd-mm</option>
										<option value="m-d-Y" '.(($search_in_place_advanced_date_format == 'm-d-Y') ? 'selected' : '').'>mm-dd-yyyy</option>
										<option value="d-m-Y" '.(($search_in_place_advanced_date_format == 'd-m-Y') ? 'selected' : '').'>dd-mm-yyyy</option>
									</select>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="summary_char_number">'.__("Enter the number of characters for posts' summaries", $this->text_domain).'</label>
								</th>
								<td>
									<input type="text" id="summary_char_number" name="summary_char_number" value="'.$search_in_place_advanced_summary_char_number.'" />
								</td>
							</tr>
						</tbody>
					</table>
					<h3>'.__('Search box design').'</h3>
					<table class="form-table">	
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="box_background_color">'.__("Background color").'</label>
								</th>
								<td>
									<input type="text" name="box_background_color" id="box_background_color" value="'.$box_background_color.'" />
									<div id="box_background_color_picker"></div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="box_border_color">'.__("Border color").'</label>
								</th>
								<td>
									<input type="text" name="box_border_color" id="box_border_color" value="'.$box_border_color.'" />
									<div id="box_border_color_picker"></div>
								</td>
							</tr>
							
							<tr valign="top">
								<th scope="row">
									<label for="label_text_color">'.__("Label text color").'</label>
								</th>
								<td>
									<input type="text" name="label_text_color" id="label_text_color" value="'.$label_text_color.'" />
									<div id="label_text_color_picker"></div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="label_text_shadow">'.__("Label text shadow").'</label>
								</th>
								<td>
									<input type="text" name="label_text_shadow" id="label_text_shadow" value="'.$label_text_shadow.'" />
									<div id="label_text_shadow_picker"></div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label>'.__("Label background color").'</label>
								</th>
								<td>
									Gradient start color: 
									<input type="text" name="label_background_start_color" id="label_background_start_color" value="'.$label_background_start_color.'" />
									<div id="label_background_start_color_picker"></div>
									Gradient end color:
									<input type="text" name="label_background_end_color" id="label_background_end_color" value="'.$label_background_end_color.'" />
									<div id="label_background_end_color_picker"></div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="active_item_background_color">'.__("Background color of active item").'</label>
								</th>
								<td>
									<input type="text" name="active_item_background_color" id="active_item_background_color" value="'.$active_item_background_color.'" />
									<div id="active_item_background_color_picker"></div>
								</td>
							</tr>
							
						</tbody>
					</table>	
					<h3>'.__('In Search Page', $this->text_domain).'</h3>
					<table class="form-table">	
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="highlight">'.__("Highlight the terms in result", $this->text_domain).'</label>
								</th>
								<td>
									<input type="checkbox" name="highlight" id="highlight" value="1" '.(($search_in_place_advanced_highlight == 1) ? 'checked' : '').' />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="mark_post_type">'.__("Identify the posts type in search result", $this->text_domain).'</label>
								</th>
								<td>
									<input type="checkbox" name="mark_post_type" id="mark_post_type" value="1" '.(($search_in_place_advanced_mark_post_type == 1) ? 'checked' : '').' />
								</td>
							</tr>
						</tbody>
					</table>
					<h3>'.__('In Resulting Pages', $this->text_domain).'</h3>
					<table class="form-table">	
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="highlight">'.__("Highlight the terms in resulting pages", $this->text_domain).'</label>
								</th>
								<td>
									<input type="checkbox" name="highlight_resulting_page" id="highlight_resulting_page" value="1" '.(($search_in_place_advanced_highlight_resulting_page == 1) ? 'checked' : '').' />
								</td>
							</tr>
						</tbody>
					</table>
					<input type="hidden" name="search_in_place_advanced_submit" value="ok" />
					<div class="submit"><input type="submit" class="button-primary" value="'.__('Update Settings', $this->text_domain).'" /></div>
				</form>
			</div>
		'.$js_script_code;		
	} // End printAdminPage
	
	/*
		Set configuration variables
	*/
	function _initialize_configuration_variables(){
		update_option('search_in_place_advanced_number_of_posts', 10);
		update_option('search_in_place_advanced_minimum_char_number', 3);
		update_option('search_in_place_advanced_summary_char_number', 20);
		update_option('search_in_place_advanced_display_thumbnail', 1);
		update_option('search_in_place_advanced_display_date', 1);
		update_option('search_in_place_advanced_display_summary', 1);
		update_option('search_in_place_advanced_display_author', 1);
		update_option('search_in_place_advanced_highlight', 1);
		update_option('search_in_place_advanced_mark_post_type', 1);
		update_option('search_in_place_box_background_color', '#F9F9F9');
		update_option('search_in_place_box_border_color', '#DDDDDD');
		update_option('search_in_place_label_text_color', '#333333');
		update_option('search_in_place_label_text_shadow', '#FFFFFF');
		update_option('search_in_place_label_background_start_color', '#F9F9F9');
		update_option('search_in_place_label_background_end_color', '#ECECEC');
		update_option('search_in_place_active_item_background_color', '#FFFFFF');
		update_option('search_in_place_advanced_post_type', array( (object)array( 'name' => 'post', 'label' => 'Post' ), (object)array( 'name' => 'page', 'label' => 'Page' )));
	}
	
	function activePlugin( $networkwide ){
		global $wpdb;
			
		if (function_exists('is_multisite') && is_multisite()) {
			if ($networkwide) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					$this->_initialize_configuration_variables();
				}
				switch_to_blog($old_blog);
				return;
			}
		}
		$this->_initialize_configuration_variables( );
	} // End activePlugin
	
	/* 
	* A new blog has been created in a multisite WordPress
	*/
	function install_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ){
		global $wpdb;
		if ( is_plugin_active_for_network() ) 
		{
			$current_blog = $wpdb->blogid;
			switch_to_blog( $blog_id );
			$this->_initialize_configuration_variables();
			switch_to_blog( $current_blog );
		}
	}
		
	/*
		Set the style tag on website header
	*/
	function setStyles(){
		$box_background_color = get_option('search_in_place_box_background_color');
		$box_border_color = get_option('search_in_place_box_border_color');
		$label_text_color = get_option('search_in_place_label_text_color');
		$label_text_shadow = get_option('search_in_place_label_text_shadow');
		$label_background_start_color = get_option('search_in_place_label_background_start_color');
		$label_background_end_color = get_option('search_in_place_label_background_end_color');
		$active_item_background_color = get_option('search_in_place_active_item_background_color');		
		
		echo "<style>\n";
		if($box_background_color) echo ".search-in-place {background-color: $box_background_color;}\n";
		if($box_border_color){ 
			echo ".search-in-place {border: 1px solid $box_border_color;}\n";
			echo ".search-in-place .item{border-bottom: 1px solid $box_border_color;}";
		}	
		if($label_text_color) echo ".search-in-place .label{color:$label_text_color;}\n";
		if($label_text_shadow) echo ".search-in-place .label{text-shadow: 0 1px 0 $label_text_shadow;}\n";
		if($label_background_start_color && $label_background_end_color) 
			echo ".search-in-place .label{
				background: $label_background_end_color;
				background: -moz-linear-gradient(top,  $label_background_start_color 0%, $label_background_end_color 100%);
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,$label_background_start_color), color-stop(100%,$label_background_end_color));
				background: -webkit-linear-gradient(top,  $label_background_start_color 0%,$label_background_end_color 100%);
				background: -o-linear-gradient(top,  $label_background_start_color 0%,$label_background_end_color 100%);
				background: -ms-linear-gradient(top,  $label_background_start_color 0%,$label_background_end_color 100%);
				background: linear-gradient(to bottom,  $label_background_start_color 0%,$label_background_end_color 100%);
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='$label_background_start_color', endColorstr='$label_background_end_color',GradientType=0 );
			}\n";
		if($active_item_background_color) echo ".search-in-place .item.active{background-color:$active_item_background_color;}\n";
		
		echo "</style>";
		
	}
} // End SearchInPlace
?>