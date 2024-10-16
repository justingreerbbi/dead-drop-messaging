<?php
/**
 * Plugin Name: Dead Drop Messaging
 * Plugin URI: https://jsutin-greer.com/dead-drop-messaging-mobile-application
 * Description: A WordPress plugin that adds encrytped messaging API functionality to your WordPress site . This plugin connects any WordPress site to the Dead Drop Messaging Mobile Application .
 * Version: 1.0
 * Author: Justin Greer
 * Author URI: https:// justin-greer.com
 * License: GPL2
 *
 * @todo Look into ProtoBufs for data transmission
 *
 * @package Dead Drop Messaging
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'DDM_PLUGIN_VERSION', '1.0' );
define( 'DDM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DDM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'DDM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// @todo This will be derived from the plugin settings.
define( 'DDM_DEFAULT_ACCESS_TOKEN_LENGTH', 40 );

// Include the main functions.
require_once DDM_PLUGIN_DIR . 'includes/functions.php';

// Include in admin only.
if ( is_admin() ) {
	require_once DDM_PLUGIN_DIR . 'includes/admin/settings-page.php';
}

// Include in the API only.
require_once DDM_PLUGIN_DIR . 'includes/api/api.php';

// Register the activation hook.
register_activation_hook( __FILE__, 'ddm_install_database_tables' );

/**
 * Install the database tables.
 *
 * This function creates the necessary database tables for the Dead Drop Messaging plugin.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 */
function ddm_install_database_tables() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	$access_token_table_name         = $wpdb->prefix . 'ddm_access_tokens';
	$access_token_table_creation_sql = "CREATE TABLE IF NOT EXISTS $access_token_table_name (
        ID bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL UNIQUE,
        access_token varchar(255) NOT NULL,
        refresh_token varchar(255) NOT NULL,
        expiration datetime NOT NULL,
        PRIMARY KEY (ID)
    ) $charset_collate;";

	$direct_messages_table_name         = $wpdb->prefix . 'ddm_direct_messages';
	$direct_messages_table_creation_sql = "CREATE TABLE IF NOT EXISTS $direct_messages_table_name (
        ID bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        recipient bigint(20) NOT NULL,
        message_content text NOT NULL,
        posted datetime NOT NULL,
        PRIMARY KEY (ID)
    ) $charset_collate;";

	$group_messages_table_name         = $wpdb->prefix . 'ddm_group_messages';
	$group_messages_table_creation_sql = "CREATE TABLE IF NOT EXISTS $group_messages_table_name (
        ID bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        message_content text NOT NULL,
        group_id bigint(20) NOT NULL,
        posted datetime NOT NULL,
        PRIMARY KEY (ID)
    ) $charset_collate;";

	$groups_table_name         = $wpdb->prefix . 'ddm_groups';
	$groups_table_creation_sql = "CREATE TABLE IF NOT EXISTS $groups_table_name (
        ID bigint(20) NOT NULL AUTO_INCREMENT,
        group_name bigint(20) NOT NULL,
        group_owner bigint(20) NOT NULL,
        PRIMARY KEY (ID)
    ) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $access_token_table_creation_sql );
	dbDelta( $direct_messages_table_creation_sql );
	dbDelta( $group_messages_table_creation_sql );
	dbDelta( $groups_table_creation_sql );
}
