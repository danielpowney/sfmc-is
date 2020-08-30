<?php
/**
 * Scripts
 *
 * @package     SFMC_IS
 * @subpackage  Functions
 * @copyright   Copyright (c) 2020, Daniel Powney
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Load Scripts
 *
 * Enqueues the required scripts.
 *
 * @since 0.1
 * @return void
 */
function sfmc_is_load_scripts() {

	$general_settings = (array) get_option( 'sfmc_is_general_settings' );
	
	$account_id = $general_settings['account_id'];
	$dataset_id = $general_settings['dataset_id'];
	$integration_method = $general_settings['integration_method'];
	$in_footer = $general_settings['in_footer'];
	$add_page_data = $general_settings['add_page_data'];

	// do not load scripts if account id and or dataset are not set
	if ( strlen( trim( $account_id ) ) === 0 || strlen( trim( $dataset_id ) ) === 0 ) {
		return;
	}

	// Add page data first so that it can be accessed by the sitemap
	if ( $add_page_data ) {
		wp_register_script( 'sfmc-is-page-data', false, null, null, $in_footer );
		wp_add_inline_script( 'sfmc-is-page-data', sfmc_is_page_data_script() );
		wp_enqueue_script( 'sfmc-is-page-data' );
	}

	// Add JavaScript beacon
	$js_beacon_src = '//cdn.evgnet.com/beacon/' . $account_id . '/' . $dataset_id . '/scripts/evergage.min.js';

	// Push custom variables to sitemap
	$script = 'var _aaq = window._aaq || (window._aaq = []);';
	$script .= apply_filters( 'sfmc_is_aaq', '' );

	if ( $integration_method === 'sync' ) {
		wp_register_script( 'sfmc-is-js-beacon-aaq', false, null, null, $in_footer );
		wp_add_inline_script( 'sfmc-is-js-beacon-aaq', $script );
		wp_enqueue_script( 'sfmc-is-js-beacon-aaq' );
		wp_enqueue_script( 'sfmc-is-js-beacon', $js_beacon_src, null, null, $in_footer );
		

	} else {

		// add inline script
		$script .= '
		(function(){
    		var d = document, g = d.createElement("script"), s = d.getElementsByTagName("script")[0];
        	g.type = "text/javascript"; g.async = true;
        	g.src = document.location.protocol + "' . $js_beacon_src . '";
        	s.parentNode.insertBefore(g, s);
  		})();';


  		wp_register_script( 'sfmc-is-js-beacon', false, null, null, $in_footer );
  		wp_add_inline_script( 'sfmc-is-js-beacon', $script );
  		wp_enqueue_script( 'sfmc-is-js-beacon' );

	}

	
	
}
add_action( 'wp_enqueue_scripts', 'sfmc_is_load_scripts' );


/**
 * Gets page data script
 */
function sfmc_is_page_data_script() {
	$page_type = sfmc_is_get_page_type();
	$post_id = get_the_id();
	$post_type = get_post_type();

	$page_data_script = '    window.IS_PAGE_DATA = {';

	if ( $page_type !== null ) {
	  	$page_data_script .= '
		"pageType" : "' . $page_type . '"';
	}

	$post_page_types = [ 'single', 'page', 'attachment', 'front' ];
	    
	if ( in_array( $page_type, $post_page_types ) ) {
	   	$page_data_script .= ',
	    "postType" : "' . $post_type . '",
		"postId" : ' . $post_id . ',
		"postThumbnail" : "' . $post_thumbnail . '",
		"postTitle" : "' . get_the_title() . '"';
	        
	    if ( $post_type == 'post' ) {
	    	$categories = get_the_category();
	    	$tags = get_the_tags();
	    	if ( $categories ) {
	    		$page_data_script .= ',
	    "postCategories" : ' . json_encode( get_the_category() );
			}
			if ( $tags ) {
				$page_data_script .= ',
		"postTags" : ' . json_encode( get_the_tags() );
			}
	    }
	}

	$page_data_script .= '
	};';

	return $page_data_script;
}

/**
 * Captures logged in WordPress user id
 */
function sfmc_is_aaq( $aaq ) {

	global $current_user; 
	wp_get_current_user();
	
	if ( is_user_logged_in() ) { 
		return $aaq .= '
_aaq.push(["setUser", "' . $current_user->user_login . '"]);';
    }

    return '';
}

$general_settings = (array) get_option( 'sfmc_is_general_settings' );
$set_user = $general_settings['set_user'];
if ( $set_user ) {
	add_filter( 'sfmc_is_aaq', 'sfmc_is_aaq', 10, 3 );
}


/**
 * Load Admin Scripts
 *
 * Enqueues the required admin scripts.
 *
 * @since 0.1
 * @return void
 */
function smfc_is_load_admin_scripts() {

	$current_screen = get_current_screen();
	if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() || method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
		sfmc_is_load_scripts();
	}

}
add_action( 'admin_enqueue_scripts', 'smfc_is_load_admin_scripts' );


/**
 * Helper function which returns page type
 */
function sfmc_is_get_page_type() {
    global $wp_query;
    $page_type = null;

    if ( $wp_query->is_page ) {
        $page_type = is_front_page() ? 'front' : 'page';
    } elseif ( $wp_query->is_home ) {
        $page_type = 'home';
    } elseif ( $wp_query->is_single ) {
        $page_type = ( $wp_query->is_attachment ) ? 'attachment' : 'single';
    } elseif ( $wp_query->is_category ) {
        $page_type = 'category';
    } elseif ( $wp_query->is_tag ) {
        $page_type = 'tag';
    } elseif ( $wp_query->is_tax ) {
        $page_type = 'tax';
    } elseif ( $wp_query->is_archive ) {
        if ( $wp_query->is_day ) {
            $page_type = 'day';
        } elseif ( $wp_query->is_month ) {
            $page_type = 'month';
        } elseif ( $wp_query->is_year ) {
            $page_type = 'year';
        } elseif ( $wp_query->is_author ) {
            $page_type = 'author';
        } else {
            $page_type = 'archive';
        }
    } elseif ( $wp_query->is_search ) {
        $page_type = 'search';
    } elseif ( $wp_query->is_404 ) {
        $page_type = 'notfound';
    }

    return $page_type;
}