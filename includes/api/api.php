<?php
/**
 * Main API file
 *
 * @package Dead Drop Messaging
 */

/**
 * Register DDM API endpoint
 */
function ddm_register_api_endpoints() {
	register_rest_route(
		'ddm/v1',
		'/authenticate',
		array(
			'methods'  => 'POST',
			'callback' => 'ddm_handle_authentication_request',
		)
	);
	register_rest_route(
		'ddm/v1',
		'/send-message',
		array(
			'methods'  => 'POST',
			'callback' => 'ddm_handle_send_message_request',
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

	// Validate the ID Token for the mobile application.
	$ddm_option_id_token = get_option( 'ddm_id_token', false );
	if ( false === $ddm_option_id_token ) {
		return new WP_REST_Response( array( 'error' => 'Invalid Request' ), 401 );
	}

	// @todo Validate the access token. The access token will be for the app itself and not the user.
	// If authentication is successful, a user access token will be issued to the user.
	if ( $ddm_option_id_token !== $id_token ) {
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
	do_action( 'ddm_after_authentication_request', $user, $id_token );

	$ddm_option_access_token_length = get_option( 'ddm_access_token_length', DDM_DEFAULT_ACCESS_TOKEN_LENGTH );

	// Generate an access token for the user.
	$generated_access_token = wp_generate_password( $ddm_option_access_token_length, false, false );

	// Generate a refresh token for the user.
	$generated_refresh_token = wp_generate_password( $ddm_option_access_token_length, false, false );

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

/**
 * Handle DDM API send message request
 * This will handle the send message request from the mobile application.
 * All Fields MUST be sanitized before using them.
 * This also provides custom actions to allow for custom hooks.
 *
 * access_token: The access token issued to the user.
 * refresh_token: The refresh token issued to the user.
 * recipient: The recipient's email address.
 * message: The message to send to the recipient.
 *
 * @param WP_REST_Request $request The request object containing the message data.
 * @return WP_REST_Response The response object to send back to the mobile application.
 */
function ddm_handle_send_message_request( WP_REST_Request $request ) {

	// Always check for SSL. This is a requirement for the API. NO EXCEPTIONS.
	if ( ! is_ssl() ) {
		return new WP_REST_Response( array( 'error' => 'SSL Required' ), 400 );
	}

	// Allow for custom actions before the authentication request is processed.
	do_action( 'ddm_before_send_message_request', $request );

	// Sanitize the request parameters.
	$access_token = sanitize_text_field( $request->get_param( 'access_token' ) );

	// Recipient is the users ID.
	$recipient = sanitize_text_field( $request->get_param( 'recipient' ) );

	// Note the message will already be encrypted by the mobile application so we just need to sanitize it and store it.
	$message = sanitize_text_field( $request->get_param( 'message' ) );

	// Validate the request parameters.
	if ( empty( $access_token ) || empty( $recipient ) || empty( $message ) ) {
		return new WP_REST_Response( array( 'error' => 'Missing Required Parameters' ), 400 );
	}

	// @todo Validate the access token. The access token will be for the app itself and not the user.
	// Get User ID from the access token. For now, we will simply use a static user ID.
	$user_id = 1;

	// Build the response to send back to the mobile application.
	$response = array(
		'message' => 'Messaga Received and Sent',
	);

	// Apply a filter to the response before sending it back to the mobile application.
	$response = apply_filters( 'ddm_send_message_successfull_response', $response, $user_id, $recipient, $message );

	return new WP_REST_Response( $response, 200 );
}
