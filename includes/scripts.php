<?php
/**
 * Scripts
 *
 * @package     SFMC_IS
 * @subpackage  Functions
 * @copyright   Copyright (c) 2017, Daniel Powney
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
	$sfmc_is_endpoint = $general_settings['endpoint'];

	$js_beacon_src = '//' . $sfmc_is_endpoint . '/beacon/' . $account_id . '/' . $dataset_id . '/scripts/evergage.min.js';
	$flicker_def_src = '//' . $sfmc_is_endpoint . '/beacon/' . $account_id . '/' . $dataset_id . '/scripts/evergageFlickerDefender.min.js';

	$script = 'var _aaq = window._aaq || (window._aaq = []);';
	$script .= apply_filters( 'sfmc_is_aaq', '' );

	if ( $integration_method === 'sync' ) {
		wp_register_script( 'sfmc-is-js-beacon-aaq', false, null, null, $in_footer );
		wp_add_inline_script( 'sfmc-is-js-beacon-aaq', $script );
		wp_enqueue_script( 'sfmc-is-js-beacon-aaq' );
		wp_enqueue_script( 'sfmc-is-js-beacon', $js_beacon_src, null, null, $in_footer );
		

	} else {

		if ( $integration_method === 'hybrid') {
			wp_enqueue_script( 'sfmc-is-flickerdef', $flicker_def_src, null, null, $in_footer );		
		}

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
 * Captureslogged in WordPress user id
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
