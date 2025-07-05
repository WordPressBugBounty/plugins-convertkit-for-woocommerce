<?php
/**
 * ConvertKit for WooCommerce Cron functions.
 *
 * @package CKWC
 * @author ConvertKit
 */

/**
 * Refresh the OAuth access token, triggered by WordPress' Cron.
 *
 * @since   1.9.8
 */
function ckwc_refresh_token() {

	// Bail if the integration is unavailable.
	if ( ! function_exists( 'WP_CKWC_Integration' ) ) {
		return;
	}

	// Bail if the integration is not enabled.
	if ( ! WP_CKWC_Integration()->is_enabled() ) {
		return;
	}

	// Bail if no access and refresh token exists.
	if ( empty( WP_CKWC_Integration()->get_option( 'access_token' ) ) ) {
		return;
	}
	if ( empty( WP_CKWC_Integration()->get_option( 'refresh_token' ) ) ) {
		return;
	}

	// Initialize the API.
	$api = new CKWC_API(
		CKWC_OAUTH_CLIENT_ID,
		CKWC_OAUTH_CLIENT_REDIRECT_URI,
		WP_CKWC_Integration()->get_option( 'access_token' ),
		WP_CKWC_Integration()->get_option( 'refresh_token' ),
		WP_CKWC_Integration()->get_option_bool( 'debug' )
	);

	// Refresh the token.
	$result = $api->refresh_token();

	// If an error occured, don't save the new tokens.
	// Logging is handled by the CKWC_API class.
	if ( is_wp_error( $result ) ) {
		return;
	}

	// Update settings with new tokens.
	WP_CKWC_Integration()->update_option( 'access_token', $result['access_token'] );
	WP_CKWC_Integration()->update_option( 'refresh_token', $result['refresh_token'] );
	WP_CKWC_Integration()->update_option( 'token_expires', ( time() + $result['expires_in'] ) );

}

// Register action to run above function; this action is created by WordPress' wp_schedule_event() function
// in ckwc_plugin_activate().
add_action( 'ckwc_refresh_token', 'ckwc_refresh_token' );
