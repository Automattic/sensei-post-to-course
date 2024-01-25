<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://automattic.com
 * @since             1.0.0
 * @package           Sensei_Post_To_Course
 *
 * @wordpress-plugin
 * Plugin Name:       Sensei Post to Course
 * Plugin URI:        https://github.com/automattic/sensei-post-to-course
 * Description:       Turn your blog posts into online courses.
 * Version:           1.2.1
 * Author:            Automattic
 * Author URI:        https://automattic.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sensei-post-to-course
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'SENSEI_POST_TO_COURSE_VERSION', '1.2.1' );
define( 'SENSEI_POST_TO_COURSE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once dirname( __FILE__ ) . '/includes/class-sptc-dependency-checker.php';

if ( ! Sensei_Post_To_Course_Dependency_Checker::are_system_dependencies_met() ) {
	return;
}

/**
 * Load composer dependencies.
 */
require __DIR__ . '/vendor/autoload.php';

/**
 * The core plugin class that is used to define internationalization and
 * admin-specific hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sensei-post-to-course.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sensei_post_to_course() {
	$plugin = new Sensei_Post_To_Course();
	$plugin->run();
}

/**
 * Make sure to bootstrap the plugin only after Sensei LMS is ready.
 *
 * @since 1.1.1
 */
add_action('plugins_loaded', function () {
	if ( Sensei_Post_To_Course_Dependency_Checker::are_plugin_dependencies_met() ) {
		run_sensei_post_to_course();
	}
});
