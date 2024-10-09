<?php
/**
 * Dead Drop Messaging Authentication API Settings Page
 *
 * @package Dead Drop Messaging
 */

/**
 * Add an admin notice when the plugin settings page is updated.
 */
function ddm_admin_notice_on_settings_update() {
	if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) {
		add_action(
			'admin_notices',
			function () {
				?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Dead Drop Messaging settings have been updated.', 'dead-drop-messaging' ); ?></p>
			</div>
				<?php
			}
		);
	}
}
add_action( 'admin_init', 'ddm_admin_notice_on_settings_update' );

/**
 * Add a menu item for the plugin settings page.
 */
function ddm_add_admin_menu() {
	add_menu_page(
		'Dead Drop Settings',
		'Dead Drop',
		'manage_options',
		'ddm-settings',
		'ddm_settings_page',
		'dashicons-email-alt',
		100
	);
}

/**
 * Add an admin notice on plugin update
 * This function checks if the plugin version has been updated and displays an admin notice.
 *
 * @return void
 */
function ddm_admin_notice_on_update() {
	// Check if the plugin version has been updated.
	if ( get_option( 'ddm_plugin_version' ) !== DDM_PLUGIN_VERSION ) {

		// Update the stored plugin version.
		update_option( 'ddm_plugin_version', DDM_PLUGIN_VERSION );

		// Display the admin notice.
		add_action(
			'admin_notices',
			function () {
				?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Dead Drop Messaging plugin has been updated to version ' . DDM_PLUGIN_VERSION . '.', 'dead-drop-messaging' ); ?></p>
			</div>
				<?php
			}
		);
	}
}
add_action( 'admin_init', 'ddm_admin_notice_on_update' );
add_action( 'admin_menu', 'ddm_add_admin_menu' );

/**
 * Display the plugin settings page.
 */
function ddm_settings_page() {
	?>
	<div class="wrap">
		<h1>Dead Drop Messaging Settings</h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'ddm_settings_group' );
			do_settings_sections( 'ddm-settings' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Register plugin settings.
 */
function ddm_register_settings() {
	register_setting( 'ddm_settings_group', 'ddm_id_token' );
	register_setting( 'ddm_settings_group', 'ddm_access_token_length' );

	add_settings_section(
		'ddm_settings_section',
		'API Settings',
		'ddm_settings_section_callback',
		'ddm-settings'
	);

	add_settings_field(
		'ddm_id_token',
		'ID Token',
		'ddm_id_token_callback',
		'ddm-settings',
		'ddm_settings_section'
	);

	add_settings_field(
		'ddm_access_token_length',
		'Access Token Length',
		'ddm_access_token_length_callback',
		'ddm-settings',
		'ddm_settings_section'
	);
}
add_action( 'admin_init', 'ddm_register_settings' );

/**
 * Settings section callback.
 */
function ddm_settings_section_callback() {
	echo 'Configure the API settings for Dead Drop Messaging.';
}

/**
 * ID Token field callback.
 */
function ddm_id_token_callback() {
	$ddm_id_token = get_option( 'ddm_id_token', '' );
	echo '<input type="text" name="ddm_id_token" value="' . esc_attr( $ddm_id_token ) . '" class="regular-text">';
	echo '<p class="description">The ID Token is used to authorize the mobile application with the API.</p>';
}

/**
 * Access Token Length field callback.
 */
function ddm_access_token_length_callback() {
	$ddm_access_token_length = get_option( 'ddm_access_token_length', DDM_DEFAULT_ACCESS_TOKEN_LENGTH );
	echo '<input type="number" name="ddm_access_token_length" value="' . esc_attr( $ddm_access_token_length ) . '" class="small-text">';
	if ( $ddm_access_token_length < 40 ) {
		echo '<p class="description">The token length is set below a recommended value.<br />Minimuim Recommended Length is ' . esc_attr( DDM_DEFAULT_ACCESS_TOKEN_LENGTH ) . '</p>';
	}
}
