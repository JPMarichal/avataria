<?php
/**
 * Sectoral Template Service class for managing industry-specific avatar templates.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Library;

use AvatarSteward\Infrastructure\LoggerInterface;

/**
 * Handles sectoral template configurations and metadata for avatar library.
 */
class SectoralTemplateService {

	/**
	 * Meta key for template configuration.
	 *
	 * @var string
	 */
	const META_TEMPLATE_CONFIG = 'avatar_steward_template_config';

	/**
	 * Predefined sectoral templates.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	const SECTORAL_TEMPLATES = array(
		'elearning'  => array(
			'name'        => 'eLearning',
			'description' => 'Templates for educational platforms and online courses',
			'tags'        => array( 'education', 'learning', 'academic' ),
			'settings'    => array(
				'require_approval' => true,
				'allowed_roles'    => array( 'subscriber', 'student', 'teacher', 'administrator' ),
				'max_file_size'    => 1.5,
			),
		),
		'ecommerce'  => array(
			'name'        => 'eCommerce',
			'description' => 'Templates for online stores and marketplaces',
			'tags'        => array( 'shop', 'store', 'commerce' ),
			'settings'    => array(
				'require_approval' => false,
				'allowed_roles'    => array( 'customer', 'shop_manager', 'administrator' ),
				'max_file_size'    => 2.0,
			),
		),
		'forum'      => array(
			'name'        => 'Community Forum',
			'description' => 'Templates for community forums and discussion boards',
			'tags'        => array( 'forum', 'community', 'discussion' ),
			'settings'    => array(
				'require_approval' => true,
				'allowed_roles'    => array( 'subscriber', 'contributor', 'moderator', 'administrator' ),
				'max_file_size'    => 1.0,
			),
		),
		'membership' => array(
			'name'        => 'Membership Site',
			'description' => 'Templates for membership and subscription platforms',
			'tags'        => array( 'membership', 'subscription', 'members' ),
			'settings'    => array(
				'require_approval' => false,
				'allowed_roles'    => array( 'subscriber', 'member', 'administrator' ),
				'max_file_size'    => 2.0,
			),
		),
		'corporate'  => array(
			'name'        => 'Corporate',
			'description' => 'Templates for corporate and business websites',
			'tags'        => array( 'corporate', 'business', 'professional' ),
			'settings'    => array(
				'require_approval' => true,
				'allowed_roles'    => array( 'employee', 'manager', 'administrator' ),
				'max_file_size'    => 2.0,
			),
		),
		'healthcare' => array(
			'name'        => 'Healthcare',
			'description' => 'Templates for healthcare and medical platforms',
			'tags'        => array( 'healthcare', 'medical', 'health' ),
			'settings'    => array(
				'require_approval' => true,
				'allowed_roles'    => array( 'patient', 'doctor', 'administrator' ),
				'max_file_size'    => 1.5,
			),
		),
	);

	/**
	 * Logger instance.
	 *
	 * @var LoggerInterface|null
	 */
	private ?LoggerInterface $logger;

	/**
	 * Constructor.
	 *
	 * @param LoggerInterface|null $logger Optional logger instance.
	 */
	public function __construct( ?LoggerInterface $logger = null ) {
		$this->logger = $logger;
	}

	/**
	 * Get all sectoral templates.
	 *
	 * @return array<string, array<string, mixed>> Sectoral templates.
	 */
	public function get_templates(): array {
		return self::SECTORAL_TEMPLATES;
	}

	/**
	 * Get a specific sectoral template.
	 *
	 * @param string $sector Sector key.
	 *
	 * @return array|null Template data or null if not found.
	 */
	public function get_template( string $sector ): ?array {
		return self::SECTORAL_TEMPLATES[ $sector ] ?? null;
	}

	/**
	 * Check if a sector template exists.
	 *
	 * @param string $sector Sector key.
	 *
	 * @return bool True if exists, false otherwise.
	 */
	public function template_exists( string $sector ): bool {
		return isset( self::SECTORAL_TEMPLATES[ $sector ] );
	}

	/**
	 * Apply sectoral template configuration.
	 *
	 * @param string $sector Sector key.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function apply_template( string $sector ): bool {
		if ( $this->logger ) {
			$this->logger->info( 'Applying sectoral template', array( 'sector' => $sector ) );
		}

		$template = $this->get_template( $sector );
		if ( ! $template ) {
			if ( $this->logger ) {
				$this->logger->error( 'Template not found', array( 'sector' => $sector ) );
			}
			return false;
		}

		// Store active template configuration.
		update_option(
			self::META_TEMPLATE_CONFIG,
			array(
				'sector'   => $sector,
				'applied'  => current_time( 'mysql' ),
				'settings' => $template['settings'],
			)
		);

		// Apply settings if SettingsPage is available.
		if ( isset( $template['settings'] ) ) {
			$this->apply_template_settings( $template['settings'] );
		}

		if ( $this->logger ) {
			$this->logger->info( 'Template applied successfully', array( 'sector' => $sector ) );
		}

		return true;
	}

	/**
	 * Apply template settings to plugin settings.
	 *
	 * @param array $settings Template settings.
	 *
	 * @return void
	 */
	private function apply_template_settings( array $settings ): void {
		$current_settings = get_option( 'avatar_steward_settings', array() );

		// Merge template settings with current settings.
		$merged_settings = array_merge( $current_settings, $settings );

		update_option( 'avatar_steward_settings', $merged_settings );
	}

	/**
	 * Get active template configuration.
	 *
	 * @return array|null Active template config or null if none applied.
	 */
	public function get_active_template(): ?array {
		$config = get_option( self::META_TEMPLATE_CONFIG );

		if ( empty( $config ) || ! is_array( $config ) ) {
			return null;
		}

		return $config;
	}

	/**
	 * Clear active template.
	 *
	 * @return bool True on success.
	 */
	public function clear_template(): bool {
		if ( $this->logger ) {
			$this->logger->info( 'Clearing active template' );
		}

		delete_option( self::META_TEMPLATE_CONFIG );

		return true;
	}

	/**
	 * Get template preview metadata.
	 *
	 * @param string $sector Sector key.
	 *
	 * @return array|null Preview data or null if not found.
	 */
	public function get_template_preview( string $sector ): ?array {
		$template = $this->get_template( $sector );
		if ( ! $template ) {
			return null;
		}

		return array(
			'sector'      => $sector,
			'name'        => $template['name'],
			'description' => $template['description'],
			'tags'        => $template['tags'],
			'settings'    => array(
				'require_approval' => $template['settings']['require_approval'] ?? false,
				'allowed_roles'    => $template['settings']['allowed_roles'] ?? array(),
				'max_file_size'    => $template['settings']['max_file_size'] ?? 2.0,
			),
		);
	}

	/**
	 * Import avatars with sectoral template metadata.
	 *
	 * @param string $sector       Sector key.
	 * @param array  $avatar_files Array of file paths.
	 *
	 * @return array Import results.
	 */
	public function import_sectoral_avatars( string $sector, array $avatar_files ): array {
		if ( $this->logger ) {
			$this->logger->info(
				'Importing sectoral avatars',
				array(
					'sector' => $sector,
					'count'  => count( $avatar_files ),
				)
			);
		}

		$template = $this->get_template( $sector );
		if ( ! $template ) {
			return array(
				'success' => 0,
				'failed'  => count( $avatar_files ),
				'errors'  => array( 'Template not found for sector: ' . $sector ),
			);
		}

		// Use LibraryService to import with sectoral metadata.
		$library_service = new LibraryService( $this->logger );

		$metadata = array(
			'sector'  => $sector,
			'license' => 'Template',
			'tags'    => $template['tags'],
		);

		return $library_service->import_sectoral_templates( $sector, $avatar_files );
	}
}
