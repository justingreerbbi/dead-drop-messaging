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
define( 'DDM_ID_TOKEN', 'QXV5dsw0toVFrpHU6vIoMeZ7cv3vpFs6eQrEfotQa7zohsjgl5cGWt96fDChTLq8' );
define( 'DDM_ACCESS_TOKEN_LENGTH', 40 );


/**
 * DDM Enqueue Scripts
 * This is used for the Administration side of the plugin.
 */
function ddm_enqueue_scripts() {
	// wp_enqueue_style( 'ddm-style', plugins_url( '/css/style.css', __FILE__ ) );
	// wp_enqueue_script( 'ddm-script', plugins_url( '/js/script.js', __FILE__ ), array( 'jquery' ), null, true );
}
add_action( 'wp_enqueue_scripts', 'ddm_enqueue_scripts' );

/**
 * Register DDM API endpoint
 */
function ddm_register_api_endpoints() {
	register_rest_route(
		'ddm/v1',
		'/authorize',
		array(
			'methods'  => 'POST',
			'callback' => 'ddm_handle_authentication_request',
		)
	);
}
add_action( 'rest_api_init', 'ddm_register_api_endpoints' );

/**
 * Handle DDM API authentication request
 *
 * This will handle the autnnetication request from the mobile application.
 * All Fields MUST be sanitized before using them.
 *
 * This also provides custom actions to allow for custom hooks.
 *
 * id_token: The public token used by the mobile application to authenticate the request as the mobile app.
 * user_login: The user's email address.
 * user_pass: The user's password.
 *
 * @param WP_REST_Request $request The request object containing the authentication data.
 * @return WP_REST_Response The response object to send back to the mobile application.
 */
function ddm_handle_authentication_request( WP_REST_Request $request ) {

	// Always check for SSL. This is a requirement for the API. NO EXCEPTIONS.
	if ( ! is_ssl() ) {
		return new WP_REST_Response( array( 'error' => 'SSL Required' ), 400 );
	}

	// Allow for custom actions before the authentication request is processed.
	do_action( 'ddm_before_authentication_request', $request );

	// Sanitize the request parameters.
	$id_token   = sanitize_text_field( $request->get_param( 'id_token' ) );
	$user_login = sanitize_email( $request->get_param( 'user_login' ) );
	$user_pass  = sanitize_text_field( $request->get_param( 'user_pass' ) );

	// Validate the request parameters.
	if ( empty( $id_token ) || empty( $user_login ) || empty( $user_pass ) ) {
		return new WP_REST_Response( array( 'error' => 'Missing Required Parameters' ), 400 );
	}

	// @todo Validate the access token. The access token will be for the app itself and not the user.
	// If authentication is successful, a user access token will be issued to the user.
	if ( DDM_ID_TOKEN !== $id_token ) {
		return new WP_REST_Response( array( 'error' => 'Invalid Request' ), 401 );
	}

	// Authenticate the user.
	$user = wp_authenticate( $user_login, $user_pass );

	if ( is_wp_error( $user ) ) {

		// Allow for custom actions when the authentication request fails.
		// This could be used to log failed login attempts and rate limiting, etc later on down the road.
		do_action( 'ddm_failed_authentication_request', $request );

		return new WP_REST_Response( array( 'error' => 'Invalid Login Credentials' ), 401 );
	}

	// Allow for custom actions after the authentication request is processed.
	do_action( 'ddm_after_authentication_request', $user, $access_token );

	// Generate an access token for the user.
	$generated_access_token = wp_generate_password( DDM_ACCESS_TOKEN_LENGTH, false, false );

	// Generate a refresh token for the user.
	$generated_refresh_token = wp_generate_password( DDM_ACCESS_TOKEN_LENGTH, false, false );

	// Assign an expiration time to the access token.
	$access_token_expires = time() + ( 60 * 60 * 24 * 30 );

	// Build the response to send back to the mobile application.
	$response = array(
		'access_token'  => $generated_access_token,
		'refresh_token' => $generated_refresh_token,
		'expires'       => $access_token_expires,
	);

	// Apply a filter to the response before sending it back to the mobile application.
	$response = apply_filters( 'ddm_authentication_successfull_response', $response, $user );

	return new WP_REST_Response( $response, 200 );
}
