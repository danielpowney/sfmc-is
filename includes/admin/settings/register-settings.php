<?php
/**
 * Register Settings
 *
 * @package     SFMC_IS 
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2017, Daniel Powney
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since 0.1
 * @return array SFMC_IS settings
 */
function sfmc_is_get_settings() {

	$settings = get_option( 'sfmc_is_settings' );

	if( empty( $settings ) ) {

		// Update old settings with new single option

		$general_settings = is_array( get_option( 'sfmc_is_general_settings' ) )    ? get_option( 'sfmc_is_general_settings' )    : array();

		$settings = array_merge( $general_settings );

		update_option( 'sfmc_is_settings', $settings );
	}

	return apply_filters( 'sfmc_is_get_settings', $settings );
}

/**
 * Reister settings
 */
function sfmc_is_register_settings() {

	register_setting( 'sfmc_is_general_settings', 'sfmc_is_general_settings', 'sfmc_is_sanitize_general_settings' );
	
	add_settings_section( 'sfmc_is_section_general', null, 'sfmc_is_section_general_desc', 'sfmc-is' );

	$setting_fields = array(
			'account_id' => array(
					'title' 	=> __( 'Account ID', 'sfmc-is' ),
					'callback' 	=> 'sfmc_is_field_input',
					'page' 		=> 'sfmc-is',
					'section' 	=> 'sfmc_is_section_general',
					'args' => array(
							'option_name' 	=> 'sfmc_is_general_settings',
							'setting_id' 	=> 'account_id',
							'label' 		=> __( '', 'sfmc-is' ),
							'placeholder'	=> __( 'Enter account ID...', 'sfmc-is' ),
							'required'		=> true
					)
			),
			'dataset_id' => array(
					'title' 	=> __( 'Dataset ID', 'sfmc-is' ),
					'callback' 	=> 'sfmc_is_field_input',
					'page' 		=> 'sfmc-is',
					'section' 	=> 'sfmc_is_section_general',
					'args' => array(
							'option_name' 	=> 'sfmc_is_general_settings',
							'setting_id' 	=> 'dataset_id',
							'label' 		=> __( 'Note default Dataset ID is "engage".', 'sfmc-is' ),
							'placeholder'	=> __( 'Enter dataset ID...', 'sfmc-is' ),
							'required'		=> true
					)
			),
			'integration_method' => array(
					'title' 	=> __( 'Integration Method', 'sfmc-is' ),
					'callback' 	=> 'sfmc_is_field_radio_buttons',
					'page' 		=> 'sfmc-is',
					'section' 	=> 'sfmc_is_section_general',
					'args' => array(
							'option_name' 	=> 'sfmc_is_general_settings',
							'setting_id' 	=> 'integration_method',
							'radio_buttons' => array(
									array(
											'value' => 'sync',
											'label' => __( 'Synchronous', 'sfmc-is' )
									),
									array(
											'value' => 'hybrid',
											'label' => __( 'Hybrid', 'sfmc-is' )
									),
									array(
											'value' => 'async',
											'label' => __( 'Asynchronous', 'sfmc-is' )
									)
							),
							'required'		=> true
					)
			),
			'in_footer' => array(
					'title' 	=> __( 'In Footer', 'sfmc-is' ),
					'callback' 	=> 'sfmc_is_field_checkbox',
					'page' 		=> 'sfmc-is',
					'section' 	=> 'sfmc_is_section_general',
					'args' => array(
							'option_name' 	=> 'sfmc_is_general_settings',
							'setting_id' 	=> 'in_footer',
							'label' => __( 'Move scripts to footer', 'sfmc-is' ),
					)
			),
			'set_user' => array(
					'title' 	=> __( 'Set User', 'sfmc-is' ),
					'callback' 	=> 'sfmc_is_field_checkbox',
					'page' 		=> 'sfmc-is',
					'section' 	=> 'sfmc_is_section_general',
					'args' => array(
							'option_name' 	=> 'sfmc_is_general_settings',
							'setting_id' 	=> 'set_user',
							'label' => __( 'Tell Interaction Studio who the current logged in user is.', 'sfmc-is' ),
					)
			),
			'endpoint' => array(
					'title' 	=> __( 'Endpoint', 'sfmc-is' ),
					'callback' 	=> 'sfmc_is_field_input',
					'page' 		=> 'sfmc-is',
					'section' 	=> 'sfmc_is_section_general',
					'args' => array(
							'option_name' 	=> 'sfmc_is_general_settings',
							'setting_id' 	=> 'endpoint',
							'label' 		=> __( 'You should not need to change this.', 'sfmc-is' ),
							'placeholder'	=> __( 'Enter endpoint...', 'sfmc-is' ),
							'required'		=> true
					)
			),
	);

	foreach ( $setting_fields as $setting_id => $setting_data ) {
		// $id, $title, $callback, $page, $section, $args
		add_settings_field( $setting_id, $setting_data['title'], $setting_data['callback'], $setting_data['page'], $setting_data['section'], $setting_data['args'] );
	}
}

/**
 * Set default settings if not set
 */
function sfmc_is_default_settings() {

	$general_settings = (array) get_option( 'sfmc_is_general_settings' );

	$general_settings = array_merge( array(
			'account_id' 					=> '',
			'dataset_id' 					=> 'engage',
			'integration_method'			=> 'sync',
			'in_footer'						=> false,
			'set_user'						=> true,
			'endpoint' 						=> 'cdn.evgnet.com',

	), $general_settings );

	update_option( 'sfmc_is_general_settings', $general_settings );

}

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	add_action( 'admin_init', 'sfmc_is_default_settings', 10, 0 );
	add_action( 'admin_init', 'sfmc_is_register_settings' );

}

/**
 * Sanitize general settings
 * @param 	$input
 */
function sfmc_is_sanitize_general_settings( $input ) {

	if ( isset( $input['in_footer'] ) && $input['in_footer'] == 'true' ) {
		$input['in_footer'] = true;
	} else {
		$input['in_footer'] = false;
	}

	if ( isset( $input['set_user'] ) && $input['set_user'] == 'true' ) {
		$input['set_user'] = true;
	} else {
		$input['set_user'] = false;
	}

	return $input;
}
