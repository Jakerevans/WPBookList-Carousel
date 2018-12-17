<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
Plugin Name: WPBookList Carousel Extension
Plugin URI: https://www.jakerevans.com
Description: A WPBookList Extension Boilerplate for an Extension with no Gui whatsoever
Version: 1.1.0
Text Domain: wpbooklist
Author: Jake Evans - Forward Creation
Author URI: https://www.jakerevans.com
License: GPL2
*/ 

/*
CHANGELOG
= 1.1.0 =
	1. Re-worked the code to allow multiple carousels on one page at a time.
*/

global $wpdb;
require_once('includes/carousel-functions.php');

// Root plugin folder directory.
if ( ! defined('WPBOOKLIST_VERSION_NUM' ) ) {
	define( 'WPBOOKLIST_VERSION_NUM', '6.1.2' );
}

// This Extension's Version Number.
define( 'WPBOOKLIST_CAROUSEL_VERSION_NUM', '6.1.2' );

// Root plugin folder URL of this extension
define('CAROUSEL_ROOT_URL', plugins_url().'/wpbooklist-carousel/');

// Grabbing database prefix
define('CAROUSEL_PREFIX', $wpdb->prefix);

// Root plugin folder directory for this extension
define('CAROUSEL_ROOT_DIR', plugin_dir_path(__FILE__));

// Root WordPress Plugin Directory.
define( 'CAROUSEL_ROOT_WP_PLUGINS_DIR', str_replace( '/wpbooklist-carousel', '', plugin_dir_path( __FILE__ ) ) );

// Root WPBL Dir.
if ( ! defined('ROOT_WPBL_DIR' ) ) {
	define( 'ROOT_WPBL_DIR', CAROUSEL_ROOT_WP_PLUGINS_DIR . 'wpbooklist/' );
}

// Root WPBL Url.
if ( ! defined('ROOT_WPBL_URL' ) ) {
	define( 'ROOT_WPBL_URL', plugins_url() . '/wpbooklist/' );
}

// Root WPBL Classes Dir.
if ( ! defined('ROOT_WPBL_CLASSES_DIR' ) ) {
	define( 'ROOT_WPBL_CLASSES_DIR', ROOT_WPBL_DIR . 'includes/classes/' );
}

// Root WPBL Transients Dir.
if ( ! defined('ROOT_WPBL_TRANSIENTS_DIR' ) ) {
	define( 'ROOT_WPBL_TRANSIENTS_DIR', ROOT_WPBL_CLASSES_DIR . 'transients/' );
}

// Root WPBL Translations Dir.
if ( ! defined('ROOT_WPBL_TRANSLATIONS_DIR' ) ) {
	define( 'ROOT_WPBL_TRANSLATIONS_DIR', ROOT_WPBL_CLASSES_DIR . 'translations/' );
}

// Root WPBL Root Img Icons Dir.
if ( ! defined('ROOT_WPBL_IMG_ICONS_URL' ) ) {
	define( 'ROOT_WPBL_IMG_ICONS_URL', ROOT_WPBL_URL . 'assets/img/icons/' );
}

// Root WPBL Root Utilities Dir.
if ( ! defined('ROOT_WPBL_UTILITIES_DIR' ) ) {
	define( 'ROOT_WPBL_UTILITIES_DIR', ROOT_WPBL_CLASSES_DIR . 'utilities/' );
}

// Root CSS URL for this extension
define('CAROUSEL_ROOT_CSS_URL', CAROUSEL_ROOT_URL.'assets/css/');

// Root JS URL for this extension
define('CAROUSEL_ROOT_JS_URL', CAROUSEL_ROOT_URL.'assets/js/');

// Root IMG URL for this extension
define('CAROUSEL_ROOT_IMG_URL', CAROUSEL_ROOT_URL.'assets/img/');

// Root UI directory
define('CAROUSEL_ROOT_INCLUDES_UI', CAROUSEL_ROOT_DIR.'includes/ui/');

// Adding the front-end ui css file for this extension
add_action('wp_enqueue_scripts', 'wpbooklist_jre_carousel_frontend_ui_style');

// For setting initial carousel UI
add_action('wp_footer', 'carousel_initial_ui_action_javascript');

// For Carousel rotation behavior
add_action( 'wp_footer', 'carousel_rotation_javascript' );

// Adding the carousel shortcode
add_shortcode('wpbooklist_carousel', 'wpbooklist_carousel_shortcode_function');

// For removing unneeded class names and span elements on each title, if using the 'action' shortcode argument.
add_action( 'wp_footer', 'carousel_remove_junk_action_javascript' );

// Verifies that the core WPBookList plugin is installed and activated - otherwise, the Extension doesn't load and a message is displayed to the user.
register_activation_hook( __FILE__, 'wpbooklist_carousel_core_plugin_required' );


?>