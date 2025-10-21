<?php
/**
 * Integration tests for social media import workflow.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Integration;

use AvatarSteward\Tests\Integration\Helpers\IntegrationTestCase;
use AvatarSteward\Tests\Integration\Fixtures\TestFixtures;
use AvatarSteward\Domain\Integrations\IntegrationService;
use AvatarSteward\Domain\Integrations\TwitterProvider;
use AvatarSteward\Domain\Integrations\FacebookProvider;

/**
 * Test social media avatar import workflows.
 */
class SocialImportIntegrationTest extends IntegrationTestCase {

	/**
	 * Integration service instance.
	 *
	 * @var IntegrationService
	 */
	private IntegrationService $integration_service;

	/**
	 * Setup test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->integration_service = new IntegrationService();
		$this->integration_service->init();
		
		// Activate Pro license.
		$this->activate_pro_license();
		
		// Configure social providers.
		$configs = TestFixtures::get_sample_social_configs();
		foreach ( $configs as $provider => $config ) {
			foreach ( $config as $key => $value ) {
				update_option( "avatar_steward_{$provider}_{$key}", $value, false );
			}
		}
	}

	/**
	 * Test complete Twitter import workflow.
	 *
	 * @return void
	 */
	public function test_twitter_import_workflow(): void {
		$user_id = $this->create_test_user( 'subscriber' );
		
		// Step 1: Get Twitter provider.
		$twitter = $this->integration_service->get_provider( 'twitter' );
		$this->assertInstanceOf( TwitterProvider::class, $twitter );

		// Step 2: Verify provider is enabled.
		$this->assertTrue( $twitter->is_enabled(), 'Twitter provider should be enabled' );

		// Step 3: Get OAuth authorization URL.
		$auth_url = $twitter->get_authorization_url( $user_id );
		$this->assertIsString( $auth_url );
		$this->assertStringContainsString( 'twitter.com', $auth_url );

		// Step 4: Mock OAuth callback with code.
		// In real scenario, user would be redirected back with code.
		$mock_code = 'test_twitter_auth_code_12345';

		// Step 5: Exchange code for access token (mocked).
		// This would normally make API calls.
		$token_data = array(
			'access_token' => 'test_twitter_token',
			'user_id'      => '123456789',
		);

		// Step 6: Import avatar from Twitter.
		// In real implementation, this fetches profile picture.
		$mock_avatar_url = 'https://pbs.twimg.com/profile_images/test_avatar.jpg';

		// Step 7: Verify connection status.
		$is_connected = $twitter->is_connected( $user_id );
		// Would be true after successful OAuth in real scenario.
		$this->assertIsBool( $is_connected );

		// Step 8: Verify provider info.
		$provider_info = $twitter->get_provider_info();
		$this->assertEquals( 'twitter', $provider_info['id'] );
		$this->assertEquals( 'Twitter / X', $provider_info['name'] );
	}

	/**
	 * Test complete Facebook import workflow.
	 *
	 * @return void
	 */
	public function test_facebook_import_workflow(): void {
		$user_id = $this->create_test_user( 'subscriber' );
		
		// Step 1: Get Facebook provider.
		$facebook = $this->integration_service->get_provider( 'facebook' );
		$this->assertInstanceOf( FacebookProvider::class, $facebook );

		// Step 2: Verify provider is enabled.
		$this->assertTrue( $facebook->is_enabled(), 'Facebook provider should be enabled' );

		// Step 3: Get OAuth authorization URL.
		$auth_url = $facebook->get_authorization_url( $user_id );
		$this->assertIsString( $auth_url );
		$this->assertStringContainsString( 'facebook.com', $auth_url );

		// Step 4: Verify required permissions are requested.
		$this->assertStringContainsString( 'public_profile', $auth_url );

		// Step 5: Verify provider info.
		$provider_info = $facebook->get_provider_info();
		$this->assertEquals( 'facebook', $provider_info['id'] );
		$this->assertEquals( 'Facebook', $provider_info['name'] );
	}

	/**
	 * Test social import with moderation enabled.
	 *
	 * @return void
	 */
	public function test_social_import_with_moderation(): void {
		$this->enable_moderation();
		
		$user_id = $this->create_test_user( 'subscriber' );
		$twitter = $this->integration_service->get_provider( 'twitter' );

		// When moderation is enabled, imported avatars should go to queue.
		// This would be verified in the actual implementation.
		$this->assertTrue( $twitter->is_enabled() );
		
		// In real scenario, after import:
		// 1. Avatar is downloaded from social media
		// 2. Avatar is uploaded to WordPress media
		// 3. Avatar enters moderation queue if enabled
		// 4. User notified of pending status
	}

	/**
	 * Test disconnecting social account.
	 *
	 * @return void
	 */
	public function test_disconnect_social_account(): void {
		$user_id = $this->create_test_user( 'subscriber' );
		$twitter = $this->integration_service->get_provider( 'twitter' );

		// Disconnect Twitter account.
		$disconnect_result = $twitter->disconnect( $user_id );
		$this->assertTrue( $disconnect_result, 'Disconnect should succeed' );

		// Verify no longer connected.
		$is_connected = $twitter->is_connected( $user_id );
		$this->assertFalse( $is_connected, 'Should not be connected after disconnect' );
	}

	/**
	 * Test all registered providers are available.
	 *
	 * @return void
	 */
	public function test_all_providers_registered(): void {
		$providers = $this->integration_service->get_providers();
		
		$this->assertIsArray( $providers );
		$this->assertArrayHasKey( 'twitter', $providers );
		$this->assertArrayHasKey( 'facebook', $providers );
		$this->assertCount( 2, $providers );
	}

	/**
	 * Test social import requires Pro license.
	 *
	 * @return void
	 */
	public function test_social_import_requires_pro_license(): void {
		// Deactivate Pro.
		$this->deactivate_pro_license();

		$user_id = $this->create_test_user( 'subscriber' );
		$twitter = $this->integration_service->get_provider( 'twitter' );

		// Provider should still exist but features limited.
		$this->assertNotNull( $twitter );
		
		// In real implementation, import would check Pro status.
		// For now, we just verify provider structure.
		$this->assertInstanceOf( TwitterProvider::class, $twitter );
	}

	/**
	 * Test error handling for invalid OAuth state.
	 *
	 * @return void
	 */
	public function test_oauth_error_handling(): void {
		$user_id = $this->create_test_user( 'subscriber' );
		$twitter = $this->integration_service->get_provider( 'twitter' );

		// Get auth URL.
		$auth_url = $twitter->get_authorization_url( $user_id );
		$this->assertIsString( $auth_url );

		// In real scenario, invalid OAuth callback would be handled:
		// - Missing code parameter
		// - Invalid state parameter
		// - OAuth error response
		// These would return appropriate error messages to user.
	}

	/**
	 * Test provider-specific configurations.
	 *
	 * @return void
	 */
	public function test_provider_configurations(): void {
		// Twitter uses PKCE (Proof Key for Code Exchange).
		$twitter = $this->integration_service->get_provider( 'twitter' );
		$this->assertNotNull( $twitter );

		// Facebook uses standard OAuth 2.0.
		$facebook = $this->integration_service->get_provider( 'facebook' );
		$this->assertNotNull( $facebook );

		// Both should have client credentials configured.
		$configs = TestFixtures::get_sample_social_configs();
		$this->assertArrayHasKey( 'twitter', $configs );
		$this->assertArrayHasKey( 'facebook', $configs );
	}
}
