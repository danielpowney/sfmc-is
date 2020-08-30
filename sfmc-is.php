<?php
/**
 * Plugin Name: JavaScript Beacon for Interaction Studio
 * Plugin URI: https://github.com/danielpowney/sfmc-is
 * Description: Integrates the JavaScript Beacon for Interaction Studio onto your website.
 * Author: Daniel Powney
 * Author URI: https://danielpowney.com
 * Version: 1.0
 * Text Domain: sfmc-is
 * Domain Path: languages
 *
 * JavaScript Beacon for Interaction Studio is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * JavaScript Beacon for Interaction Studio is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy Digital Downloads. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     SFMC_IS 
 * @author 		Daniel Powney
 * @version		1.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'SFMC_IS' ) ) :


/**
 * Main SFMC_IS Class.
 *
 * @since 1.0
 */
final class SFMC_IS {

	/** Singleton *************************************************************/

	/**
	 * @var SFMC_IS The one true SFMC_IS
	 * @since 1.0
	 */
	private static $instance;

	/**
	 * Used to identify multiple chatbots on the same page...
	 */
	public static $sequence = 0;


	/**
	 * Main SFMC_IS Instance.
	 *
	 * Insures that only one instance of SFMC_IS exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @uses SFMC_IS::setup_constants() Setup the constants needed.
	 * @uses SFMC_IS::includes() Include the required files.
	 * @uses SFMC_IS::load_textdomain() load the language files.
	 * @see SFMC_IS ()
	 * @return object|SFMC_IS The one true SFMC_IS
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SFMC_IS ) ) {

			self::$instance = new SFMC_IS;
			self::$instance->setup_constants();

			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

			self::$instance->includes();
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'sfmc-is' ), '1.6' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'sfmc-is' ), '1.6' );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function setup_constants() {

		// Plugin version.
		if ( ! defined( 'SFMC_IS_VERSION' ) ) {
			define( 'SFMC_IS_VERSION', '1.1' );
		}

		// Plugin slug.
		if ( ! defined( 'SFMC_IS_SLUG' ) ) {
			define( 'SFMC_IS_SLUG', 'sfmc-is' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'SFMC_IS_PLUGIN_DIR' ) ) {
			define( 'SFMC_IS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'SFMC_IS_PLUGIN_URL' ) ) {
			define( 'SFMC_IS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'SFMC_IS_PLUGIN_FILE' ) ) {
			define( 'SFMC_IS_PLUGIN_FILE', __FILE__ );
		}
	}

	/**
	 * Include required files.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function includes() {
		global $sfmc_is_options;

		require_once SFMC_IS_PLUGIN_DIR . 'includes/admin/settings/register-settings.php';
		$sfmc_is_options = sfmc_is_get_settings();

		require_once SFMC_IS_PLUGIN_DIR . 'includes/scripts.php';

		if ( is_admin() ) {
			require_once SFMC_IS_PLUGIN_DIR . 'includes/admin/admin-pages.php';
			require_once SFMC_IS_PLUGIN_DIR . 'includes/admin/settings/display-settings.php';
		}

	}

	/**
	 * Loads the plugin language files.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function load_textdomain() {
		global $wp_version;

		// Set filter for plugin's languages directory.
		$sfmc_is_lang_dir  = dirname( plugin_basename( SFMC_IS_PLUGIN_FILE ) ) . '/languages/';
		$sfmc_is_lang_dir  = apply_filters( 'sfmc_is_languages_directory', $sfmc_is_lang_dir );

		// Traditional WordPress plugin locale filter.

		$get_locale = get_locale();

		if ( $wp_version >= 4.7 ) {

			$get_locale = get_user_locale();
		}

		/**
		 * Defines the plugin language locale used.
		 *
		 * @var $get_locale The locale to use. Uses get_user_locale()` in WordPress 4.7 or greater,
		 *                  otherwise uses `get_locale()`.
		 */
		$locale        = apply_filters( 'plugin_locale',  $get_locale, 'sfmc-is' );
		$mofile        = sprintf( '%1$s-%2$s.mo', 'sfmc-is', $locale );

		// Look for wp-content/languages/myc/sfmc-is-{lang}_{country}.mo
		$mofile_global1 = WP_LANG_DIR . '/myc/sfmc-is-' . $locale . '.mo';

		// Look for wp-content/languages/myc/sfmc-is-{lang}_{country}.mo
		$mofile_global2 = WP_LANG_DIR . '/myc/sfmc-is-' . $locale . '.mo';

		// Look in wp-content/languages/plugins/sfmc-is
		$mofile_global3 = WP_LANG_DIR . '/plugins/sfmc-is/' . $mofile;

		if ( file_exists( $mofile_global1 ) ) {

			load_textdomain( 'sfmc-is', $mofile_global1 );

		} elseif ( file_exists( $mofile_global2 ) ) {

			load_textdomain( 'sfmc-is', $mofile_global2 );

		} elseif ( file_exists( $mofile_global3 ) ) {

			load_textdomain( 'sfmc-is', $mofile_global3 );

		} else {

			// Load the default language files.
			load_plugin_textdomain( 'sfmc-is', false, $sfmc_is_lang_dir );
		}

	}

}

endif; // End if class_exists check.

/**
 * Checks whether function is disabled.
 *
 * @param string  $function Name of the function.
 * @return bool Whether or not function is disabled.
 */
function sfmc_is_is_func_disabled( $function ) {
	$disabled = explode( ',',  ini_get( 'disable_functions' ) );

	return in_array( $function, $disabled );
}


/**
 * The main function for that returns SFMC_IS
 *
 * The main function responsible for returning the one true SFMC_IS
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $myc = SFMC_IS (); ?>
 *
 * @since 1.0
* @return object|SFMC_IS The one true SFMC_IS Instance.
 */
function SFMC_IS() {
	return SFMC_IS::instance();
}

// Get SFMC_IS Running.
SFMC_IS();
