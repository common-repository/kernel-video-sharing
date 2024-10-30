<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://www.kernel-video-sharing.com/
 * @since             1.0.0
 * @package           Kvs
 *
 * @wordpress-plugin
 * Plugin Name:       Kernel Video Sharing Integration
 * Plugin URI:        https://www.kernel-video-sharing.com/en/wordpress/
 * Description:       Kernel Video Sharing plugin for WordPress. Provides integration with KVS video content manager and automates video import from KVS into your Wordpress projects.
 * Version:           1.0.9
 * Requires at least: 5.0
 * Requires PHP:      5.6
 * Author:            Kernel Video Sharing
 * Author URI:        https://www.kernel-video-sharing.com/en/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       kvs
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'KVS_WEBSITE', 'https://www.kernel-video-sharing.com/' );
define( 'KVS_VERSION', '1.0.9' );
define( 'KVS_PREFIX', 'kvs' );
define( 'KVS_DIRPATH', plugin_dir_path( __FILE__ ) );
define( 'KVS_DIRURL', plugin_dir_url( __FILE__ ) );

// Uncomment that debug option to ignore default log file naming
//define( 'KVS_LOGFILE', plugin_dir_path( __FILE__ ) . 'logs/' . date('d-m-Y') . '.log' );

// Uncomment that debug option to ignore settings in WP
//define( 'KVS_LOG_LEVEL', 'DEBUG' ); // values: NONE, WARNING, ERROR, DEBUG

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-kvs-activator.php
 */
function activate_kvs() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kvs-activator.php';
	Kvs_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_kvs' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-kvs-deactivator.php
 */
function deactivate_kvs() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kvs-deactivator.php';
	Kvs_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_kvs' );



/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-kvs.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_kvs() {
	global $kernel_video_sharing;

	$kernel_video_sharing = new Kvs();
	$kernel_video_sharing->run();
}
run_kvs();
