<?php
/* ------------------------------------------------------------------------- *
 *  Custom functions
/* ------------------------------------------------------------------------- */
	
	// Use a child theme instead of placing custom functions here
	// http://codex.wordpress.org/Child_Themes



	
		if ( function_exists('register_sidebar') ) {
register_sidebar(array(
'name' => 'RetroSpeak Sidebar',
'id' => 'retrosp-sidebar',
'description' => 'Appears as the sidebar on the custom homepage',
'before_widget' => '<aside id="%1$s" class="widget %2$s">',
'after_widget' => '</li>',
'before_title' => '<h2 class="widgettitle">',
'after_title' => '</h2>',
));
}

	
	if ( function_exists('register_sidebar') ) {
register_sidebar(array(
'name' => 'Articles Sidebar',
'id' => 'articles-sidebar',
'description' => 'Appears as the sidebar on the custom homepage',
'before_widget' => '<aside id="%1$s" class="widget %2$s">',
'after_widget' => '</li>',
'before_title' => '<h2 class="widgettitle">',
'after_title' => '</h2>',
));
}

	
	if ( function_exists('register_sidebar') ) {
register_sidebar(array(
'name' => 'Tuners Sidebar',
'id' => 'tuners-sidebar',
'description' => 'Appears as the sidebar on the custom homepage',
'before_widget' => '<aside id="%1$s" class="widget %2$s">',
'after_widget' => '</li>',
'before_title' => '<h2 class="widgettitle">',
'after_title' => '</h2>',
));
}


/* royal slider fixes */

function newrs_add_resp_img_variable($m, $slide_data, $options) {
    
    $m->addHelper('responsive_image_tag', function() use ($slide_data) {

            $attachment_id = 0;
            if(is_object($slide_data) && isset($slide_data->ID) ) {
                $attachment_id = $slide_data->ID;
            } else if( isset($slide_data['image']) && isset($slide_data['image']['attachment_id']) ) {
                $attachment_id = $slide_data['image']['attachment_id'];
            } 

            if( $attachment_id ) {
                $att_src = wp_get_attachment_image_url( $attachment_id, 'large' );
                $att_srcbig = wp_get_attachment_image_url( $attachment_id, 'full' );
                $att_srcset = wp_get_attachment_image_srcset( $attachment_id, 'large' );
                $att_alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
                return '<img src="'.$att_src.'" data-rsBigImg="'.$att_srcbig.'" srcset="'.$att_srcset.'" alt="'.$att_alt.'" />';
            }

            return '{{image_tag}}';
    } );

}
add_filter('new_rs_slides_renderer_helper','newrs_add_resp_img_variable', 10, 4);
			
			
			// Add code to your theme functions.php
function royalslider_change_image_size($sizes) {
    // here is how sizes object looks by default
    /* $sizes = array(
        'full' => 'full',
        'large' => 'large',
        'thumbnail' => 'thumbnail'
    ); */

    // here is how to modify large image
    $sizes['large'] = 'your-custom-size-name';
    return $sizes;
}
add_filter( 'new_rs_image_sizes', 'royalslider_change_image_size' );