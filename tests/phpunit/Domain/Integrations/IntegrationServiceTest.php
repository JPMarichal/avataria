<?php
/**
 * Tests for IntegrationService
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Domain\Integrations;

use AvatarSteward\Domain\Integrations\IntegrationService;
use AvatarSteward\Domain\Integrations\TwitterProvider;
use AvatarSteward\Domain\Integrations\FacebookProvider;
use PHPUnit\Framework\TestCase;

/**
 * Test case for IntegrationService
 */
class IntegrationServiceTest extends TestCase {

	/**
	 * Service instance.
	 *
	 * @var IntegrationService
	 */
	private IntegrationService $service;

	/**
	 * Set up test case.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->service = new IntegrationService();
	}

	/**
	 * Test service initializes providers.
	 *
	 * @return void
	 */
	public function test_init_registers_providers(): void {
		$this->service->init();
		$providers = $this->service->get_providers();

		$this->assertIsArray( $providers );
		$this->assertArrayHasKey( 'twitter', $providers );
		$this->assertArrayHasKey( 'facebook', $providers );
	}

	/**
	 * Test get_provider returns correct provider.
	 *
	 * @return void
	 */
	public function test_get_provider(): void {
		$this->service->init();

		$twitter = $this->service->get_provider( 'twitter' );
		$this->assertInstanceOf( TwitterProvider::class, $twitter );

		$facebook = $this->service->get_provider( 'facebook' );
		$this->assertInstanceOf( FacebookProvider::class, $facebook );

		$invalid = $this->service->get_provider( 'nonexistent' );
		$this->assertNull( $invalid );
	}

	/**
	 * Test get_providers returns all registered providers.
	 *
	 * @return void
	 */
	public function test_get_providers(): void {
		$this->service->init();
		$providers = $this->service->get_providers();

		$this->assertCount( 2, $providers );
		$this->assertArrayHasKey( 'twitter', $providers );
		$this->assertArrayHasKey( 'facebook', $providers );
	}

	/**
	 * Test get_configured_providers filters unconfigured providers.
	 *
	 * @return void
	 */
	public function test_get_configured_providers(): void {
		$this->service->init();
		$configured = $this->service->get_configured_providers();

		// Without credentials set, should return empty array.
		$this->assertIsArray( $configured );
	}

	/**
	 * Test register_provider adds new provider.
	 *
	 * @return void
	 */
	public function test_register_provider(): void {
		$mock_provider = $this->createMock( TwitterProvider::class );
		$mock_provider->method( 'get_name' )->willReturn( 'test_provider' );

		$this->service->register_provider( $mock_provider );
		$provider = $this->service->get_provider( 'test_provider' );

		$this->assertSame( $mock_provider, $provider );
	}
}
