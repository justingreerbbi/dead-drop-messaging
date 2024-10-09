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

// Include in admin only.
if ( is_admin() ) {
	require_once DDM_PLUGIN_DIR . 'includes/admin/settings-page.php';
}

// Include in the API only.
require_once DDM_PLUGIN_DIR . 'includes/api/api.php';
