<?php
/**
 * Avatar Override Verification Script
 *
 * This script demonstrates and verifies the avatar override functionality.
 * Run this in a WordPress environment after Avatar Steward is installed.
 *
 * @package AvatarSteward
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Verify that the avatar override system is working correctly.
 *
 * @return array Verification results.
 */
function avatarsteward_verify_override() {
	$results = array(
		'status'  => 'success',
		'checks'  => array(),
		'errors'  => array(),
		'summary' => array(),
	);

	// Check 1: Plugin instance exists.
	try {
		$plugin = AvatarSteward\Plugin::instance();
		$results['checks']['plugin_instance'] = array(
			'status'  => 'pass',
			'message' => 'Plugin instance created successfully',
		);
	} catch ( Exception $e ) {
		$results['checks']['plugin_instance'] = array(
			'status'  => 'fail',
			'message' => 'Failed to create plugin instance: ' . $e->getMessage(),
		);
		$results['status'] = 'error';
		$results['errors'][] = 'Plugin instance creation failed';
		return $results;
	}

	// Check 2: Avatar handler exists.
	$handler = $plugin->get_avatar_handler();
	if ( $handler instanceof AvatarSteward\Core\AvatarHandler ) {
		$results['checks']['avatar_handler'] = array(
			'status'  => 'pass',
			'message' => 'Avatar handler instance available',
		);
	} else {
		$results['checks']['avatar_handler'] = array(
			'status'  => 'fail',
			'message' => 'Avatar handler not available',
		);
		$results['status'] = 'error';
		$results['errors'][] = 'Avatar handler initialization failed';
		return $results;
	}

	// Check 3: Filter hooks are registered.
	$filters_registered = array();
	
	if ( has_filter( 'pre_get_avatar_data' ) ) {
		$filters_registered[] = 'pre_get_avatar_data';
	}
	
	if ( has_filter( 'get_avatar_url' ) ) {
		$filters_registered[] = 'get_avatar_url';
	}

	if ( count( $filters_registered ) === 2 ) {
		$results['checks']['filter_hooks'] = array(
			'status'  => 'pass',
			'message' => 'All required filters registered: ' . implode( ', ', $filters_registered ),
		);
	} else {
		$results['checks']['filter_hooks'] = array(
			'status'  => 'warning',
			'message' => 'Some filters may not be registered correctly',
		);
		$results['status'] = 'warning';
	}

	// Check 4: Test with a real user (if available).
	$users = get_users( array( 'number' => 1 ) );
	if ( ! empty( $users ) ) {
		$test_user = $users[0];
		
		// Test has_local_avatar method.
		$has_avatar = $handler->has_local_avatar( $test_user->ID );
		$results['checks']['user_avatar_check'] = array(
			'status'  => 'pass',
			'message' => sprintf(
				'User %d (%s) %s a local avatar',
				$test_user->ID,
				$test_user->user_login,
				$has_avatar ? 'has' : 'does not have'
			),
		);

		// Test get_avatar function.
		$avatar = get_avatar( $test_user->ID, 96 );
		if ( ! empty( $avatar ) ) {
			$results['checks']['get_avatar_function'] = array(
				'status'  => 'pass',
				'message' => 'get_avatar() returns valid HTML',
			);
		} else {
			$results['checks']['get_avatar_function'] = array(
				'status'  => 'warning',
				'message' => 'get_avatar() returned empty result',
			);
		}

		// Test get_avatar_url function.
		$avatar_url = get_avatar_url( $test_user->ID, array( 'size' => 96 ) );
		if ( ! empty( $avatar_url ) && filter_var( $avatar_url, FILTER_VALIDATE_URL ) ) {
			$results['checks']['get_avatar_url_function'] = array(
				'status'  => 'pass',
				'message' => 'get_avatar_url() returns valid URL: ' . esc_url( $avatar_url ),
			);
		} else {
			$results['checks']['get_avatar_url_function'] = array(
				'status'  => 'warning',
				'message' => 'get_avatar_url() did not return valid URL',
			);
		}
	} else {
		$results['checks']['user_tests'] = array(
			'status'  => 'skip',
			'message' => 'No users available for testing',
		);
	}

	// Check 5: Performance check.
	$start_time = microtime( true );
	if ( ! empty( $users ) ) {
		for ( $i = 0; $i < 100; $i++ ) {
			get_avatar_url( $users[0]->ID, array( 'size' => 96 ) );
		}
	}
	$end_time = microtime( true );
	$execution_time = ( $end_time - $start_time ) * 1000; // Convert to milliseconds.

	$results['checks']['performance'] = array(
		'status'  => $execution_time < 100 ? 'pass' : 'warning',
		'message' => sprintf(
			'100 avatar URL lookups completed in %.2f ms (avg: %.2f ms per lookup)',
			$execution_time,
			$execution_time / 100
		),
	);

	// Generate summary.
	$pass_count    = 0;
	$warning_count = 0;
	$fail_count    = 0;
	$skip_count    = 0;

	foreach ( $results['checks'] as $check ) {
		switch ( $check['status'] ) {
			case 'pass':
				++$pass_count;
				break;
			case 'warning':
				++$warning_count;
				break;
			case 'fail':
				++$fail_count;
				break;
			case 'skip':
				++$skip_count;
				break;
		}
	}

	$results['summary'] = array(
		'total'    => count( $results['checks'] ),
		'passed'   => $pass_count,
		'warnings' => $warning_count,
		'failed'   => $fail_count,
		'skipped'  => $skip_count,
	);

	if ( $fail_count > 0 ) {
		$results['status'] = 'error';
	} elseif ( $warning_count > 0 ) {
		$results['status'] = 'warning';
	} else {
		$results['status'] = 'success';
	}

	return $results;
}

/**
 * Display verification results.
 *
 * @param array $results Verification results from avatarsteward_verify_override().
 */
function avatarsteward_display_verification_results( $results ) {
	echo "\n=== Avatar Steward Override Verification ===\n\n";

	echo "Overall Status: " . strtoupper( $results['status'] ) . "\n\n";

	echo "Checks:\n";
	foreach ( $results['checks'] as $check_name => $check ) {
		$status_symbol = '';
		switch ( $check['status'] ) {
			case 'pass':
				$status_symbol = '✓';
				break;
			case 'warning':
				$status_symbol = '⚠';
				break;
			case 'fail':
				$status_symbol = '✗';
				break;
			case 'skip':
				$status_symbol = '○';
				break;
		}

		printf(
			"  %s %s: %s\n",
			$status_symbol,
			str_replace( '_', ' ', ucwords( $check_name, '_' ) ),
			$check['message']
		);
	}

	echo "\nSummary:\n";
	printf(
		"  Total Checks: %d | Passed: %d | Warnings: %d | Failed: %d | Skipped: %d\n",
		$results['summary']['total'],
		$results['summary']['passed'],
		$results['summary']['warnings'],
		$results['summary']['failed'],
		$results['summary']['skipped']
	);

	if ( ! empty( $results['errors'] ) ) {
		echo "\nErrors:\n";
		foreach ( $results['errors'] as $error ) {
			echo "  - $error\n";
		}
	}

	echo "\n";
}

// Run verification if this script is executed directly.
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	$results = avatarsteward_verify_override();
	avatarsteward_display_verification_results( $results );

	if ( $results['status'] === 'error' ) {
		WP_CLI::error( 'Avatar override verification failed!' );
	} elseif ( $results['status'] === 'warning' ) {
		WP_CLI::warning( 'Avatar override verification completed with warnings.' );
	} else {
		WP_CLI::success( 'Avatar override verification passed!' );
	}
}
