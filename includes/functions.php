<?php
/**
 * Main Functions File for Dead Drop Messaging
 *
 * @package Dead Drop Messaging
 */

/**
 * Validate the access token.
 *
 * @param string $access_token The access token to validate.
 * @return bool True if the access token is valid, false otherwise.
 */
function ddm_validate_access_token( $access_token ) {
	global $wpdb;
	$table_name   = $wpdb->prefix . 'ddm_access_tokens';
	$access_token = sanitize_text_field( $access_token );

	$access_token = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ddm_access_tokens WHERE access_token = %s", $access_token ) );

	if ( ! $access_token ) {
		return false;
	}

	return $access_token;
}
