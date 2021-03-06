<?php
/*
Plugin Name: Auto fill post password from url
Plugin URI:  https://github.com/CrossMediaCloud/auto-fill-post-password-from-url
Description: Fills out the post password for the user if provided in the url
Version:     0.1
Author:      Cross Media Cloud
Author URI:  http://www.Cross-Media-Cloud.de
License:     GPL2
*/

/*
 * Redirect to login if password is required and provided in url
 */
add_action( 'template_redirect', 'cmc_afppwbu_redirect', 9 );
function cmc_afppwbu_redirect() {

	// Check if the post needs a post password
	if ( post_password_required() ) {

		// Post is password protected

		// if parameter was changed by filter
		$cmc_password_url_parameter = sanitize_title( apply_filters( 'cmc_afppwbu_url_parameter', 'access_token' ) );

		// Check if a access token was provided
		if ( isset( $_GET[ $cmc_password_url_parameter ] ) ) {

			// Make the token more safe
			$cmc_access_token = preg_replace( '/[^-a-zA-Z0-9_]/', '', $_GET[ $cmc_password_url_parameter ] );

			// Remove the old access token from the path
			// This prevents infinity loops, if a wrong password was given by url
			$cmc_ref = add_query_arg( array( $cmc_password_url_parameter => false ), $_SERVER['REQUEST_URI'] );

			// Prepare the redirect target
			$cmc_redirect_to_login_location_args_array = array(
				'action'       => 'postpass',
				'access_token' => $cmc_access_token,
				'ref'          => $cmc_ref,
			);
			$cmc_redirect_to_login_location            = add_query_arg( $cmc_redirect_to_login_location_args_array, '/wp-login.php' );

			// Redirect to login to try to authorise the user by setting a cookie
			wp_redirect( $cmc_redirect_to_login_location );

			// Stop this script. Login script will continue.
			exit;

		}

	}

}

/*
 *  Turn GET-vars into POST-vars before the login runs the postpass action to set cookie
 */
add_action( 'login_form_postpass', 'cmc_afppwbu_var_preparation' );
function cmc_afppwbu_var_preparation() {

	// if parameter was changed by filter
	$cmc_password_url_parameter = sanitize_title( apply_filters( 'cmc_afppwbu_url_parameter', 'access_token' ) );

	// Set var if page load comes from the redirect
	if ( isset( $_GET[ $cmc_password_url_parameter ] ) AND isset( $_GET['ref'] ) ) {

		// Inject data into vars so core login can use it
		$_POST['post_password'] = $_GET[ $cmc_password_url_parameter ];

		// Inject data into vars so after login the redirect end up on the right post
		$_REQUEST['_wp_http_referer'] = wp_sanitize_redirect( $_GET['ref'] );

	}
}