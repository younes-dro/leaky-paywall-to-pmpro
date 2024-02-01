<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://expresstechsoftwares.com
 * @since             1.0.0
 * @package           Ets_Leaky_To_Pmpro
 *
 * @wordpress-plugin
 * Plugin Name:       Ets Leaky Paywall To PMPro
 * Plugin URI:        https://expresstechsoftwares.com
 * Description:       LeakyToPMPro Migrator seamlessly transfers subscribers from Leaky Paywall to Paid Memberships Pro (PMPro), ensuring a smooth transition. With this plugin, subscribers gain exclusive privileges, enabling them to comment on and like posts, creating an enhanced and engaging community experience
 * Version:           1.0.3
 * Author:            ExpressTech Softwares Solutions Pvt Ltd
 * Author URI:        https://expresstechsoftwares.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ets-leaky-to-pmpro
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ETS_LEAKY_TO_PMPRO_VERSION', '1.0.3' );
define( 'ETS_LEAKY_TO_PMPRO_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'ETS_LEAKY_TO_PMPRO_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'ETS_LEAKY_TO_PMPRO_CSV_FOLDER', 'ets-leaky-to-pmpro-csv' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ets-leaky-to-pmpro-activator.php
 */
function activate_ets_leaky_to_pmpro() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ets-leaky-to-pmpro-activator.php';
	Ets_Leaky_To_Pmpro_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ets-leaky-to-pmpro-deactivator.php
 */
function deactivate_ets_leaky_to_pmpro() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ets-leaky-to-pmpro-deactivator.php';
	Ets_Leaky_To_Pmpro_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ets_leaky_to_pmpro' );
register_deactivation_hook( __FILE__, 'deactivate_ets_leaky_to_pmpro' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ets-leaky-to-pmpro.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
// Hook your plugin's initialization to run after the theme and plugins are loaded
add_action( 'after_setup_theme', 'run_ets_leaky_to_pmpro', 11 );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ets_leaky_to_pmpro() {
	$plugin = new Ets_Leaky_To_Pmpro();
	$plugin->run();
}

