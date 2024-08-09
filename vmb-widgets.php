<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://buildupbookings.com/
 * @since             2.0.61
 * @package           Vmb_Widgets
 *
 * @wordpress-plugin
 * Plugin Name:       VMB Widgets
 * Plugin URI:        https://buildupbookings.com/
 * Description:       A lightweight collection of plugins for Vacation Myrtle Beach.
 * Version:           2.0.61
 * Author:            Braudy Pedrosa
 * Author URI:        https://buildupbookings.com//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       vmb-widgets
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.1 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'VMB_WIDGETS_VERSION', '2.0.61' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-vmb-widgets-activator.php
 */
function activate_vmb_widgets() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vmb-widgets-activator.php';
	Vmb_Widgets_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-vmb-widgets-deactivator.php
 */
function deactivate_vmb_widgets() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vmb-widgets-deactivator.php';
	Vmb_Widgets_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_vmb_widgets' );
register_deactivation_hook( __FILE__, 'deactivate_vmb_widgets' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-vmb-widgets.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_vmb_widgets() {

	$plugin = new Vmb_Widgets();
	$plugin->run();

}
run_vmb_widgets();
