<?php
/**
 * Example: Custom Color Palette for Initials Generator
 *
 * This example demonstrates how to create a custom color palette for the
 * Avatar Steward initials generator. Add this code to your theme's functions.php
 * or create a custom plugin.
 *
 * @package AvatarSteward
 * @subpackage Examples
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register a custom color palette for the initials generator.
 *
 * This function hooks into Avatar Steward's palette system to add
 * your own color combinations for generated avatars.
 *
 * @param array $palettes Existing color palettes.
 * @return array Modified palettes with custom additions.
 */
function my_custom_avatar_palette( $palettes ) {
	// Define your custom palette.
	$custom_palette = array(
		'brand-palette'  => array(
			'label'  => __( 'Brand Colors', 'your-textdomain' ),
			'colors' => array(
				array(
					'background' => '#1a2332', // Dark blue.
					'text'       => '#ffffff', // White text.
				),
				array(
					'background' => '#e74c3c', // Brand red.
					'text'       => '#ffffff',
				),
				array(
					'background' => '#3498db', // Brand blue.
					'text'       => '#ffffff',
				),
				array(
					'background' => '#2ecc71', // Brand green.
					'text'       => '#ffffff',
				),
				array(
					'background' => '#f39c12', // Brand orange.
					'text'       => '#ffffff',
				),
			),
		),
		'pastel-palette' => array(
			'label'  => __( 'Soft Pastels', 'your-textdomain' ),
			'colors' => array(
				array(
					'background' => '#FFB3BA', // Light pink.
					'text'       => '#333333',
				),
				array(
					'background' => '#BAFFC9', // Light green.
					'text'       => '#333333',
				),
				array(
					'background' => '#BAE1FF', // Light blue.
					'text'       => '#333333',
				),
				array(
					'background' => '#FFFFBA', // Light yellow.
					'text'       => '#333333',
				),
				array(
					'background' => '#E0BBE4', // Light purple.
					'text'       => '#333333',
				),
			),
		),
	);

	// Merge with existing palettes.
	return array_merge( $palettes, $custom_palette );
}
add_filter( 'avatarsteward/initials/palettes', 'my_custom_avatar_palette', 10, 1 );

/**
 * Set a default palette for your site.
 *
 * This function sets which palette should be used by default.
 *
 * @param string $palette_id Current default palette ID.
 * @return string New default palette ID.
 */
function my_default_avatar_palette( $palette_id ) {
	return 'brand-palette'; // Use our custom brand palette by default.
}
add_filter( 'avatarsteward/initials/default_palette', 'my_default_avatar_palette', 10, 1 );

/**
 * Customize palette selection per user role.
 *
 * Example: Use different palettes for admins, subscribers, etc.
 *
 * @param string $palette_id Current palette ID.
 * @param int    $user_id    User ID.
 * @return string Modified palette ID.
 */
function my_role_based_palette( $palette_id, $user_id ) {
	$user = get_user_by( 'id', $user_id );

	if ( ! $user ) {
		return $palette_id;
	}

	// Admins get brand colors.
	if ( in_array( 'administrator', $user->roles, true ) ) {
		return 'brand-palette';
	}

	// Subscribers get pastel colors.
	if ( in_array( 'subscriber', $user->roles, true ) ) {
		return 'pastel-palette';
	}

	return $palette_id;
}
add_filter( 'avatarsteward/initials/user_palette', 'my_role_based_palette', 10, 2 );

/**
 * How to use this example:
 *
 * 1. Copy this code to your theme's functions.php or a custom plugin.
 * 2. Adjust the color values to match your brand or preferences.
 * 3. Test by viewing user profiles or areas where avatars are displayed.
 * 4. Users without uploaded avatars will see initials with your custom colors.
 *
 * Expected results:
 * - New palette options appear in Avatar Steward settings.
 * - Generated avatars use your custom colors.
 * - Different user roles can have different color schemes.
 *
 * Tips:
 * - Use high contrast between background and text for readability.
 * - Test colors with WCAG accessibility guidelines (minimum 4.5:1 ratio).
 * - Consider your brand identity when choosing colors.
 * - You can define as many color combinations as needed in each palette.
 */
