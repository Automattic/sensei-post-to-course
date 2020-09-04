<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://automattic.com
 * @since      1.0.0
 *
 * @package    Sensei_Post_To_Course
 * @subpackage Sensei_Post_To_Course/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Sensei_Post_To_Course
 * @subpackage Sensei_Post_To_Course/includes
 * @author     Automattic
 */
class Sensei_Post_To_Course_i18n {
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'sensei-post-to-course',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
