<?php
/**
 * Test fixtures for integration tests.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Integration\Fixtures;

/**
 * Provides test data fixtures for integration tests.
 */
class TestFixtures {

	/**
	 * Get sample user data.
	 *
	 * @return array<array<string, mixed>>
	 */
	public static function get_sample_users(): array {
		return array(
			array(
				'user_login' => 'john_doe',
				'user_email' => 'john@example.com',
				'first_name' => 'John',
				'last_name'  => 'Doe',
				'role'       => 'subscriber',
			),
			array(
				'user_login' => 'jane_smith',
				'user_email' => 'jane@example.com',
				'first_name' => 'Jane',
				'last_name'  => 'Smith',
				'role'       => 'editor',
			),
			array(
				'user_login' => 'admin_user',
				'user_email' => 'admin@example.com',
				'first_name' => 'Admin',
				'last_name'  => 'User',
				'role'       => 'administrator',
			),
		);
	}

	/**
	 * Get sample library avatar metadata.
	 *
	 * @return array<array<string, mixed>>
	 */
	public static function get_sample_library_avatars(): array {
		return array(
			array(
				'filename' => 'corporate-male-1.jpg',
				'author'   => 'Avatar Steward',
				'license'  => 'GPL-2.0',
				'sector'   => 'corporate',
				'tags'     => array( 'professional', 'male', 'suit' ),
			),
			array(
				'filename' => 'corporate-female-1.jpg',
				'author'   => 'Avatar Steward',
				'license'  => 'GPL-2.0',
				'sector'   => 'corporate',
				'tags'     => array( 'professional', 'female', 'business' ),
			),
			array(
				'filename' => 'tech-developer-1.jpg',
				'author'   => 'Avatar Steward',
				'license'  => 'GPL-2.0',
				'sector'   => 'technology',
				'tags'     => array( 'developer', 'casual', 'tech' ),
			),
			array(
				'filename' => 'healthcare-doctor-1.jpg',
				'author'   => 'Avatar Steward',
				'license'  => 'GPL-2.0',
				'sector'   => 'healthcare',
				'tags'     => array( 'doctor', 'medical', 'professional' ),
			),
			array(
				'filename' => 'education-teacher-1.jpg',
				'author'   => 'Avatar Steward',
				'license'  => 'GPL-2.0',
				'sector'   => 'education',
				'tags'     => array( 'teacher', 'academic', 'professional' ),
			),
		);
	}

	/**
	 * Get sample social provider configurations.
	 *
	 * @return array<string, array<string, string>>
	 */
	public static function get_sample_social_configs(): array {
		return array(
			'twitter' => array(
				'client_id'     => 'test_twitter_client_id',
				'client_secret' => 'test_twitter_client_secret',
				'enabled'       => '1',
			),
			'facebook' => array(
				'app_id'     => 'test_facebook_app_id',
				'app_secret' => 'test_facebook_app_secret',
				'enabled'    => '1',
			),
		);
	}

	/**
	 * Get sample moderation queue data.
	 *
	 * @return array<array<string, mixed>>
	 */
	public static function get_sample_moderation_queue(): array {
		return array(
			array(
				'user_id'       => 101,
				'attachment_id' => 201,
				'status'        => 'pending',
				'submitted_at'  => time() - 3600,
			),
			array(
				'user_id'       => 102,
				'attachment_id' => 202,
				'status'        => 'pending',
				'submitted_at'  => time() - 7200,
			),
			array(
				'user_id'       => 103,
				'attachment_id' => 203,
				'status'        => 'approved',
				'submitted_at'  => time() - 86400,
				'reviewed_at'   => time() - 3600,
				'reviewed_by'   => 1,
			),
			array(
				'user_id'       => 104,
				'attachment_id' => 204,
				'status'        => 'rejected',
				'submitted_at'  => time() - 172800,
				'reviewed_at'   => time() - 86400,
				'reviewed_by'   => 1,
				'reject_reason' => 'Inappropriate content',
			),
		);
	}

	/**
	 * Get sample audit log entries.
	 *
	 * @return array<array<string, mixed>>
	 */
	public static function get_sample_audit_logs(): array {
		return array(
			array(
				'action'    => 'avatar_uploaded',
				'user_id'   => 101,
				'details'   => array( 'attachment_id' => 201 ),
				'timestamp' => time() - 3600,
			),
			array(
				'action'    => 'avatar_approved',
				'user_id'   => 103,
				'details'   => array(
					'attachment_id' => 203,
					'approved_by'   => 1,
				),
				'timestamp' => time() - 1800,
			),
			array(
				'action'    => 'avatar_rejected',
				'user_id'   => 104,
				'details'   => array(
					'attachment_id' => 204,
					'rejected_by'   => 1,
					'reason'        => 'Inappropriate content',
				),
				'timestamp' => time() - 900,
			),
			array(
				'action'    => 'social_import',
				'user_id'   => 105,
				'details'   => array(
					'provider'      => 'twitter',
					'attachment_id' => 205,
				),
				'timestamp' => time() - 450,
			),
			array(
				'action'    => 'library_avatar_selected',
				'user_id'   => 106,
				'details'   => array(
					'library_id'    => 10,
					'attachment_id' => 206,
				),
				'timestamp' => time() - 225,
			),
		);
	}

	/**
	 * Get sample license keys for testing.
	 *
	 * @return array<string, string>
	 */
	public static function get_sample_license_keys(): array {
		return array(
			'valid'   => 'AVATAR-STEWARD-PRO-12345-VALID',
			'invalid' => 'INVALID-KEY',
			'expired' => 'AVATAR-STEWARD-PRO-67890-EXPIRED',
		);
	}

	/**
	 * Get sample avatar settings configurations.
	 *
	 * @return array<array<string, mixed>>
	 */
	public static function get_sample_settings(): array {
		return array(
			'basic' => array(
				'max_file_size'    => 2097152, // 2MB.
				'max_dimensions'   => 2048,
				'allowed_formats'  => array( 'jpg', 'jpeg', 'png', 'gif', 'webp' ),
				'convert_to_webp'  => false,
				'allowed_roles'    => array( 'administrator', 'editor', 'author', 'subscriber' ),
			),
			'restrictive' => array(
				'max_file_size'    => 524288, // 512KB.
				'max_dimensions'   => 1024,
				'allowed_formats'  => array( 'jpg', 'png' ),
				'convert_to_webp'  => true,
				'allowed_roles'    => array( 'administrator', 'editor' ),
			),
			'permissive' => array(
				'max_file_size'    => 10485760, // 10MB.
				'max_dimensions'   => 5000,
				'allowed_formats'  => array( 'jpg', 'jpeg', 'png', 'gif', 'webp' ),
				'convert_to_webp'  => false,
				'allowed_roles'    => array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' ),
			),
		);
	}
}
