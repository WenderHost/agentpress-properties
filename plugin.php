<?php
/*
Plugin Name: AgentPress Properties
Plugin URI: http://www.wenderhost.com/
Description: AgentPress Properties is a plugin which adds a Properties custom post type for Property Managers.
Author: WenderHost
Author URI: http://www.wenderhost.com/

Version: 1.2.3

License: GNU General Public License v2.0 (or later)
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

register_activation_hook( __FILE__, 'agentpress_properties_activation' );
/**
 * This function runs on plugin activation. It checks to make sure the required
 * minimum Genesis version is installed. If not, it deactivates itself.
 *
 * @since 0.1.0
 */
function agentpress_properties_activation() {

		$latest = '2.0.2';

		if ( 'genesis' != get_option( 'template' ) ) {

			//* Deactivate ourself
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( sprintf( __( 'Sorry, you can\'t activate unless you have installed <a href="%s">Genesis</a>', 'agentpress-properties' ), 'http://my.studiopress.com/themes/genesis/' ) );

		}

		if ( version_compare( wp_get_theme( 'genesis' )->get( 'Version' ), $latest, '<' ) ) {

			//* Deactivate ourself
			deactivate_plugins( plugin_basename( __FILE__ ) ); /** Deactivate ourself */
			wp_die( sprintf( __( 'Sorry, you cannot activate without <a href="%s">Genesis %s</a> or greater', 'agentpress-properties' ), 'http://www.studiopress.com/support/showthread.php?t=19576', $latest ) );

		}

		/** Flush rewrite rules */
		if ( ! post_type_exists( 'property' ) ) {

			agentpress_property_init();
			global $_agentpress_properties, $_agentpress_taxonomies;
			$_agentpress_properties->create_post_type();
			$_agentpress_taxonomies->register_taxonomies();

		}

		flush_rewrite_rules();

}

add_action( 'after_setup_theme', 'agentpress_properties_init' );
/**
 * Initialize AgentPress Properties.
 *
 * Include the libraries, define global variables, instantiate the classes.
 *
 * @since 0.1.0
 */
function agentpress_properties_init() {

	/** Do nothing if a Genesis child theme isn't active */
	if ( ! function_exists( 'genesis_get_option' ) )
		return;

	global $_agentpress_properties, $_agentpress_taxonomies;

	define( 'APL_URL', plugin_dir_url( __FILE__ ) );
	define( 'APL_VERSION', '1.0.0' );

	/** Load textdomain for translation */
	load_plugin_textdomain( 'agentpress-properties', false, basename( dirname( __FILE__ ) ) . '/languages/' );

	/** Includes */
	require_once( dirname( __FILE__ ) . '/includes/functions.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-properties.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-taxonomies.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-featured-properties-widget.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-property-search-widget.php' );

	/** Instantiate */
	$_agentpress_properties = new AgentPress_Properties;
	$_agentpress_taxonomies = new AgentPress_Taxonomies;

	add_action( 'widgets_init', 'agentpress_register_widgets' );

}

/**
 * Register Widgets that will be used in the AgentPress Properties plugin
 *
 * @since 0.1.0
 */
function agentpress_register_widgets() {

	$widgets = array( 'AgentPress_Featured_Properties_Widget', 'AgentPress_Properties_Search_Widget' );

	foreach ( (array) $widgets as $widget ) {
		register_widget( $widget );
	}

}

