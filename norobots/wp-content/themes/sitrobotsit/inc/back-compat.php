<?php
/**
 * Sit Robot Sit back compat functionality
 *
 * Prevents Sit Robot Sit from running on WordPress versions prior to 3.6,
 * since this theme is not meant to be backward compatible beyond that
 * and relies on many newer functions and markup changes introduced in 3.6.
 *
 * @package WordPress
 * @subpackage SitRobotSit
 * @since Sit Robot Sit 1.0
 */

/**
 * Prevent switching to Sit Robot Sit on old versions of WordPress.
 *
 * Switches to the default theme.
 *
 * @since Sit Robot Sit 1.0
 *
 * @return void
 */
function twentyfourteen_switch_theme() {
	switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
	unset( $_GET['activated'] );
	add_action( 'admin_notices', 'twentyfourteen_upgrade_notice' );
}
add_action( 'after_switch_theme', 'twentyfourteen_switch_theme' );

/**
 * Add message for unsuccessful theme switch.
 *
 * Prints an update nag after an unsuccessful attempt to switch to
 * Sit Robot Sit on WordPress versions prior to 3.6.
 *
 * @since Sit Robot Sit 1.0
 *
 * @return void
 */
function twentyfourteen_upgrade_notice() {
	$message = sprintf( __( 'Sit Robot Sit requires at least WordPress version 3.6. You are running version %s. Please upgrade and try again.', 'twentyfourteen' ), $GLOBALS['wp_version'] );
	printf( '<div class="error"><p>%s</p></div>', $message );
}

/**
 * Prevent the Theme Customizer from being loaded on WordPress versions prior to 3.6.
 *
 * @since Sit Robot Sit 1.0
 *
 * @return void
 */
function twentyfourteen_customize() {
	wp_die( sprintf( __( 'Sit Robot Sit requires at least WordPress version 3.6. You are running version %s. Please upgrade and try again.', 'twentyfourteen' ), $GLOBALS['wp_version'] ), '', array(
		'back_link' => true,
	) );
}
add_action( 'load-customize.php', 'twentyfourteen_customize' );

/**
 * Prevent the Theme Preview from being loaded on WordPress versions prior to 3.4.
 *
 * @since Sit Robot Sit 1.0
 *
 * @return void
 */
function twentyfourteen_preview() {
	if ( isset( $_GET['preview'] ) ) {
		wp_die( sprintf( __( 'Sit Robot Sit requires at least WordPress version 3.6. You are running version %s. Please upgrade and try again.', 'twentyfourteen' ), $GLOBALS['wp_version'] ) );
	}
}
add_action( 'template_redirect', 'twentyfourteen_preview' );
