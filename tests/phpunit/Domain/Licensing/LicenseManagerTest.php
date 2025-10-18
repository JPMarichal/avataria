<?php
/**
 * Tests for LicenseManager class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Domain\Licensing;

use AvatarSteward\Domain\Licensing\LicenseManager;
use PHPUnit\Framework\TestCase;

/**
 * Test LicenseManager functionality.
 */
class LicenseManagerTest extends TestCase {

	/**
	 * License manager instance.
	 *
	 * @var LicenseManager
	 */
	private LicenseManager $manager;

	/**
	 * Set up test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		// Reset global options state.
		global $wp_test_options;
		$wp_test_options = array();

		$this->manager = new LicenseManager();
	}

	/**
	 * Tear down test environment.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		// Reset global options state.
		global $wp_test_options;
		$wp_test_options = array();

		parent::tearDown();
	}

	/**
	 * Test activation with valid license key.
	 *
	 * @return void
	 */
	public function test_activate_with_valid_license(): void {
		$result = $this->manager->activate( 'ABCD-1234-EFGH-5678' );

		$this->assertTrue( $result['success'] );
		$this->assertArrayHasKey( 'message', $result );
		$this->assertArrayHasKey( 'status', $result );
		$this->assertEquals( LicenseManager::STATUS_ACTIVE, $result['status'] );
	}

	/**
	 * Test activation with empty license key.
	 *
	 * @return void
	 */
	public function test_activate_with_empty_license(): void {
		$result = $this->manager->activate( '' );

		$this->assertFalse( $result['success'] );
		$this->assertArrayHasKey( 'message', $result );
		$this->assertStringContainsString( 'empty', strtolower( $result['message'] ) );
	}

	/**
	 * Test activation with invalid license format.
	 *
	 * @return void
	 */
	public function test_activate_with_invalid_format(): void {
		$result = $this->manager->activate( 'INVALID-KEY' );

		$this->assertFalse( $result['success'] );
		$this->assertArrayHasKey( 'message', $result );
		$this->assertStringContainsString( 'format', strtolower( $result['message'] ) );
	}

	/**
	 * Test activation with license key containing spaces.
	 *
	 * @return void
	 */
	public function test_activate_trims_whitespace(): void {
		$result = $this->manager->activate( '  ABCD-1234-EFGH-5678  ' );

		$this->assertTrue( $result['success'] );
	}

	/**
	 * Test deactivation removes license data.
	 *
	 * @return void
	 */
	public function test_deactivate_removes_license(): void {
		// First activate a license.
		$this->manager->activate( 'ABCD-1234-EFGH-5678' );

		// Then deactivate.
		$result = $this->manager->deactivate();

		$this->assertTrue( $result['success'] );
		$this->assertArrayHasKey( 'message', $result );

		// Verify status is inactive.
		$this->assertFalse( $this->manager->is_pro_active() );
	}

	/**
	 * Test is_pro_active returns false when no license.
	 *
	 * @return void
	 */
	public function test_is_pro_active_without_license(): void {
		$this->assertFalse( $this->manager->is_pro_active() );
	}

	/**
	 * Test is_pro_active returns true after activation.
	 *
	 * @return void
	 */
	public function test_is_pro_active_with_active_license(): void {
		$this->manager->activate( 'ABCD-1234-EFGH-5678' );

		$this->assertTrue( $this->manager->is_pro_active() );
	}

	/**
	 * Test get_license_status returns correct status.
	 *
	 * @return void
	 */
	public function test_get_license_status(): void {
		// Initially inactive.
		$this->assertEquals( LicenseManager::STATUS_INACTIVE, $this->manager->get_license_status() );

		// After activation.
		$this->manager->activate( 'ABCD-1234-EFGH-5678' );
		$this->assertEquals( LicenseManager::STATUS_ACTIVE, $this->manager->get_license_status() );
	}

	/**
	 * Test get_license_data returns empty array when no license.
	 *
	 * @return void
	 */
	public function test_get_license_data_empty(): void {
		$data = $this->manager->get_license_data();

		$this->assertIsArray( $data );
		$this->assertEmpty( $data );
	}

	/**
	 * Test get_license_data returns data after activation.
	 *
	 * @return void
	 */
	public function test_get_license_data_after_activation(): void {
		$this->manager->activate( 'ABCD-1234-EFGH-5678' );
		$data = $this->manager->get_license_data();

		$this->assertIsArray( $data );
		$this->assertArrayHasKey( 'key', $data );
		$this->assertArrayHasKey( 'activated_at', $data );
		$this->assertArrayHasKey( 'domain', $data );
		$this->assertArrayHasKey( 'activated_by', $data );
	}

	/**
	 * Test can_use_pro_feature returns false without license.
	 *
	 * @return void
	 */
	public function test_can_use_pro_feature_without_license(): void {
		$this->assertFalse( $this->manager->can_use_pro_feature( 'moderation' ) );
	}

	/**
	 * Test can_use_pro_feature returns true with active license.
	 *
	 * @return void
	 */
	public function test_can_use_pro_feature_with_license(): void {
		$this->manager->activate( 'ABCD-1234-EFGH-5678' );

		$this->assertTrue( $this->manager->can_use_pro_feature( 'moderation' ) );
		$this->assertTrue( $this->manager->can_use_pro_feature( 'library' ) );
		$this->assertTrue( $this->manager->can_use_pro_feature( 'social' ) );
	}

	/**
	 * Test get_license_info returns status when inactive.
	 *
	 * @return void
	 */
	public function test_get_license_info_inactive(): void {
		$info = $this->manager->get_license_info();

		$this->assertIsArray( $info );
		$this->assertArrayHasKey( 'status', $info );
		$this->assertEquals( LicenseManager::STATUS_INACTIVE, $info['status'] );
		$this->assertArrayNotHasKey( 'key', $info );
	}

	/**
	 * Test get_license_info returns full data when active.
	 *
	 * @return void
	 */
	public function test_get_license_info_active(): void {
		$this->manager->activate( 'ABCD-1234-EFGH-5678' );
		$info = $this->manager->get_license_info();

		$this->assertIsArray( $info );
		$this->assertArrayHasKey( 'status', $info );
		$this->assertArrayHasKey( 'key', $info );
		$this->assertArrayHasKey( 'activated_at', $info );
		$this->assertArrayHasKey( 'domain', $info );

		// Verify license key is masked.
		$this->assertStringContainsString( '****', $info['key'] );
		$this->assertStringContainsString( '5678', $info['key'] );
	}

	/**
	 * Test license key is masked correctly.
	 *
	 * @return void
	 */
	public function test_license_key_masking(): void {
		$this->manager->activate( 'ABCD-1234-EFGH-5678' );
		$info = $this->manager->get_license_info();

		$this->assertEquals( '****-****-****-5678', $info['key'] );
	}

	/**
	 * Test various valid license key formats.
	 *
	 * @return void
	 */
	public function test_valid_license_formats(): void {
		$valid_keys = array(
			'AAAA-BBBB-CCCC-DDDD',
			'1234-5678-9012-3456',
			'A1B2-C3D4-E5F6-G7H8',
			'abcd-efgh-ijkl-mnop',
		);

		foreach ( $valid_keys as $key ) {
			$result = $this->manager->activate( $key );
			$this->assertTrue( $result['success'], "License key '$key' should be valid" );

			// Deactivate for next iteration.
			$this->manager->deactivate();
		}
	}

	/**
	 * Test various invalid license key formats.
	 *
	 * @return void
	 */
	public function test_invalid_license_formats(): void {
		$invalid_keys = array(
			'AAAA-BBBB-CCCC',           // Too short.
			'AAAA-BBBB-CCCC-DDDD-EEEE', // Too long.
			'AAA-BBBB-CCCC-DDDD',       // Wrong segment length.
			'AAAA_BBBB_CCCC_DDDD',      // Wrong separator.
			'AAAA BBBB CCCC DDDD',      // Space separator.
			'AAAA-BBBB-CCCC-DDD',       // Short last segment.
			'AAAAbBBBBCCCCDDDD',        // No separators.
		);

		foreach ( $invalid_keys as $key ) {
			$result = $this->manager->activate( $key );
			$this->assertFalse( $result['success'], "License key '$key' should be invalid" );
		}
	}
}
