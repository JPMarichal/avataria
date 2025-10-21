<?php
/**
 * Integration tests for Pro license activation workflow.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Integration;

use AvatarSteward\Tests\Integration\Helpers\IntegrationTestCase;
use AvatarSteward\Tests\Integration\Fixtures\TestFixtures;
use AvatarSteward\Domain\Licensing\LicenseManager;

/**
 * Test Pro activation and license management workflow.
 */
class ProActivationIntegrationTest extends IntegrationTestCase {

	/**
	 * License manager instance.
	 *
	 * @var LicenseManager
	 */
	private LicenseManager $license_manager;

	/**
	 * Setup test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->license_manager = new LicenseManager();
	}

	/**
	 * Test end-to-end license activation workflow.
	 *
	 * @return void
	 */
	public function test_license_activation_workflow(): void {
		$licenses = TestFixtures::get_sample_license_keys();

		// Step 1: Verify Pro is inactive initially.
		$this->assertFalse(
			$this->license_manager->is_pro_active(),
			'Pro should be inactive initially'
		);

		// Step 2: Activate with valid license.
		$result = $this->license_manager->activate( $licenses['valid'] );
		
		$this->assertTrue( $result['success'], 'License activation should succeed' );
		$this->assertEquals( LicenseManager::STATUS_ACTIVE, $result['status'] );

		// Step 3: Verify Pro is now active.
		$this->assertTrue(
			$this->license_manager->is_pro_active(),
			'Pro should be active after activation'
		);

		// Step 4: Verify license info is stored correctly.
		$license_info = $this->license_manager->get_license_info();
		
		$this->assertEquals( $licenses['valid'], $license_info['key'] );
		$this->assertArrayHasKey( 'activated_at', $license_info );
		$this->assertArrayHasKey( 'domain', $license_info );

		// Step 5: Verify license status.
		$status = $this->license_manager->get_license_status();
		$this->assertEquals( LicenseManager::STATUS_ACTIVE, $status );
	}

	/**
	 * Test license deactivation workflow.
	 *
	 * @return void
	 */
	public function test_license_deactivation_workflow(): void {
		// Setup: Activate license first.
		$this->activate_pro_license();
		$this->assertTrue( $this->license_manager->is_pro_active() );

		// Deactivate license.
		$result = $this->license_manager->deactivate();
		
		$this->assertTrue( $result['success'], 'License deactivation should succeed' );

		// Verify Pro is now inactive.
		$this->assertFalse(
			$this->license_manager->is_pro_active(),
			'Pro should be inactive after deactivation'
		);

		// Verify license data is cleared.
		$license_info = $this->license_manager->get_license_info();
		$this->assertEmpty( $license_info );
	}

	/**
	 * Test invalid license key rejection.
	 *
	 * @return void
	 */
	public function test_invalid_license_rejection(): void {
		$licenses = TestFixtures::get_sample_license_keys();

		// Try to activate with invalid license.
		$result = $this->license_manager->activate( $licenses['invalid'] );
		
		$this->assertFalse( $result['success'], 'Invalid license should be rejected' );
		$this->assertFalse( $this->license_manager->is_pro_active() );
	}

	/**
	 * Test Pro feature access control.
	 *
	 * @return void
	 */
	public function test_pro_feature_access_control(): void {
		// Without license, Pro features should be blocked.
		$this->assertFalse(
			$this->license_manager->can_use_pro_feature( 'moderation' ),
			'Pro features should be blocked without license'
		);
		$this->assertFalse(
			$this->license_manager->can_use_pro_feature( 'social_import' )
		);
		$this->assertFalse(
			$this->license_manager->can_use_pro_feature( 'avatar_library' )
		);

		// Activate license.
		$this->activate_pro_license();

		// With license, Pro features should be available.
		$this->assertTrue(
			$this->license_manager->can_use_pro_feature( 'moderation' ),
			'Pro features should be available with active license'
		);
		$this->assertTrue(
			$this->license_manager->can_use_pro_feature( 'social_import' )
		);
		$this->assertTrue(
			$this->license_manager->can_use_pro_feature( 'avatar_library' )
		);
	}

	/**
	 * Test license reactivation with different key.
	 *
	 * @return void
	 */
	public function test_license_reactivation_with_new_key(): void {
		$licenses = TestFixtures::get_sample_license_keys();

		// Activate with first key.
		$this->license_manager->activate( $licenses['valid'] );
		$first_info = $this->license_manager->get_license_info();

		// Reactivate with different key.
		$new_key = 'AVATAR-STEWARD-PRO-99999-NEWKEY';
		$this->license_manager->activate( $new_key );
		$second_info = $this->license_manager->get_license_info();

		// Verify new key replaced old one.
		$this->assertNotEquals( $first_info['key'], $second_info['key'] );
		$this->assertEquals( $new_key, $second_info['key'] );
		$this->assertTrue( $this->license_manager->is_pro_active() );
	}
}
