<?php
/**
 * Admin Pages
 *
 * @package     SFMC_IS 
 * @subpackage  Admin/Pages
 * @copyright   Copyright (c) 2017, Daniel Powney
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Creates an options page for plugin settings and links it to a global variable
 *
 * @since 0.1
 * @return void
 */
function sfmc_is_add_options_link() {
	global $sfmc_is_settings_page;

	$sfmc_is_settings_page      = 	add_options_page( __( 'Interaction Studio', 'sfmc-is' ), __( 'Interaction Studio', 'sfmc-is' ), 'manage_options', 'sfmc-is', 'sfmc_is_options_page');
	
}
add_action( 'admin_menu', 'sfmc_is_add_options_link', 10 );