<?php
/**
 * Tests for FacebookProvider
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Domain\Integrations;

use AvatarSteward\Domain\Integrations\FacebookProvider;
use PHPUnit\Framework\TestCase;

/**
 * Test case for FacebookProvider
 */
class FacebookProviderTest extends TestCase {

	/**
	 * Provider instance.
	 *
	 * @var FacebookProvider
	 */
	private FacebookProvider $provider;

	/**
	 * Set up test case.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->provider = new FacebookProvider();
	}

	/**
	 * Test get_name returns correct provider name.
	 *
	 * @return void
	 */
	public function test_get_name(): void {
		$this->assertSame( 'facebook', $this->provider->get_name() );
	}

	/**
	 * Test get_label returns correct display label.
	 *
	 * @return void
	 */
	public function test_get_label(): void {
		$this->assertSame( 'Facebook', $this->provider->get_label() );
	}

	/**
	 * Test is_configured returns false without credentials.
	 *
	 * @return void
	 */
	public function test_is_configured_without_credentials(): void {
		$this->assertFalse( $this->provider->is_configured() );
	}

	/**
	 * Test is_connected returns false without token.
	 *
	 * @return void
	 */
	public function test_is_connected_without_token(): void {
		$this->assertFalse( $this->provider->is_connected( 1 ) );
	}

	/**
	 * Test get_authorization_url returns null when not configured.
	 *
	 * @return void
	 */
	public function test_get_authorization_url_not_configured(): void {
		$url = $this->provider->get_authorization_url( 1, 'http://example.com/callback' );
		$this->assertNull( $url );
	}

	/**
	 * Test disconnect removes user data.
	 *
	 * @return void
	 */
	public function test_disconnect(): void {
		$result = $this->provider->disconnect( 1 );
		$this->assertTrue( $result );
		$this->assertFalse( $this->provider->is_connected( 1 ) );
	}
}
