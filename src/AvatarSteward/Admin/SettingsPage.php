<?php
/**
 * Settings Page class.
 *
 * Handles the Avatar Steward settings page in WordPress admin.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Admin;

use AvatarSteward\Domain\Licensing\LicenseManager;

/**
 * Class SettingsPage
 *
 * Manages the Avatar Steward settings page using WordPress Settings API.
 */
class SettingsPage {

	/**
	 * Settings group name.
	 *
	 * @var string
	 */
	private const SETTINGS_GROUP = 'avatar_steward_settings';

	/**
	 * Option name for settings.
	 *
	 * @var string
	 */
	private const OPTION_NAME = 'avatar_steward_options';

	/**
	 * License Manager instance.
	 *
	 * @var LicenseManager|null
	 */
	private ?LicenseManager $license_manager;

	/**
	 * Constructor.
	 *
	 * @param LicenseManager|null $license_manager Optional License Manager instance.
	 */
	public function __construct( ?LicenseManager $license_manager = null ) {
		$this->license_manager = $license_manager ?? new LicenseManager();
	}

	/**
	 * Initialize the settings page.
	 *
	 * @return void
	 */
	public function init(): void {
		if ( ! function_exists( 'add_action' ) ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'register_menu_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Register the settings page in the admin menu.
	 *
	 * @return void
	 */
	public function register_menu_page(): void {
		if ( ! function_exists( 'add_options_page' ) ) {
			return;
		}

		add_options_page(
			__( 'Avatar Steward Settings', 'avatar-steward' ),
			__( 'Avatar Steward', 'avatar-steward' ),
			'manage_options',
			'avatar-steward',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register settings, sections, and fields.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		if ( ! function_exists( 'register_setting' ) ) {
			return;
		}

		register_setting(
			self::SETTINGS_GROUP,
			self::OPTION_NAME,
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => $this->get_default_settings(),
			)
		);

		// Register social credentials separately for security.
		register_setting( self::SETTINGS_GROUP, 'avatarsteward_twitter_client_id', 'sanitize_text_field' );
		register_setting( self::SETTINGS_GROUP, 'avatarsteward_twitter_client_secret', 'sanitize_text_field' );
		register_setting( self::SETTINGS_GROUP, 'avatarsteward_facebook_app_id', 'sanitize_text_field' );
		register_setting( self::SETTINGS_GROUP, 'avatarsteward_facebook_app_secret', 'sanitize_text_field' );

		// Upload Restrictions Section.
		add_settings_section(
			'avatar_steward_upload_restrictions',
			__( 'Upload Restrictions', 'avatar-steward' ),
			array( $this, 'render_upload_restrictions_section' ),
			'avatar-steward'
		);

		// Max file size field.
		add_settings_field(
			'max_file_size',
			__( 'Max File Size (MB)', 'avatar-steward' ),
			array( $this, 'render_max_file_size_field' ),
			'avatar-steward',
			'avatar_steward_upload_restrictions'
		);

		// Allowed formats field.
		add_settings_field(
			'allowed_formats',
			__( 'Allowed Formats', 'avatar-steward' ),
			array( $this, 'render_allowed_formats_field' ),
			'avatar-steward',
			'avatar_steward_upload_restrictions'
		);

		// Max width field.
		add_settings_field(
			'max_width',
			__( 'Max Width (pixels)', 'avatar-steward' ),
			array( $this, 'render_max_width_field' ),
			'avatar-steward',
			'avatar_steward_upload_restrictions'
		);

		// Max height field.
		add_settings_field(
			'max_height',
			__( 'Max Height (pixels)', 'avatar-steward' ),
			array( $this, 'render_max_height_field' ),
			'avatar-steward',
			'avatar_steward_upload_restrictions'
		);

		// Convert to WebP field.
		add_settings_field(
			'convert_to_webp',
			__( 'Convert to WebP', 'avatar-steward' ),
			array( $this, 'render_convert_to_webp_field' ),
			'avatar-steward',
			'avatar_steward_upload_restrictions'
		);

		// Performance Optimization Section.
		add_settings_section(
			'avatar_steward_performance',
			__( 'Performance Optimization', 'avatar-steward' ),
			array( $this, 'render_performance_section' ),
			'avatar-steward'
		);

		// Low bandwidth mode field.
		add_settings_field(
			'low_bandwidth_mode',
			__( 'Low Bandwidth Mode', 'avatar-steward' ),
			array( $this, 'render_low_bandwidth_mode_field' ),
			'avatar-steward',
			'avatar_steward_performance'
		);

		// Bandwidth threshold field.
		add_settings_field(
			'bandwidth_threshold',
			__( 'File Size Threshold (KB)', 'avatar-steward' ),
			array( $this, 'render_bandwidth_threshold_field' ),
			'avatar-steward',
			'avatar_steward_performance'
		);

		// Roles & Permissions Section.
		add_settings_section(
			'avatar_steward_roles_permissions',
			__( 'Roles & Permissions', 'avatar-steward' ),
			array( $this, 'render_roles_permissions_section' ),
			'avatar-steward'
		);

		// Allowed roles field.
		add_settings_field(
			'allowed_roles',
			__( 'Allowed Roles', 'avatar-steward' ),
			array( $this, 'render_allowed_roles_field' ),
			'avatar-steward',
			'avatar_steward_roles_permissions'
		);

		// Require approval field.
		add_settings_field(
			'require_approval',
			__( 'Require Approval', 'avatar-steward' ),
			array( $this, 'render_require_approval_field' ),
			'avatar-steward',
			'avatar_steward_roles_permissions'
		);

		// Social Integrations Section.
		add_settings_section(
			'avatar_steward_social_integrations',
			__( 'Social Integrations', 'avatar-steward' ),
			array( $this, 'render_social_integrations_section' ),
			'avatar-steward'
		);

		// Twitter Client ID.
		add_settings_field(
			'twitter_client_id',
			__( 'Twitter Client ID', 'avatar-steward' ),
			array( $this, 'render_twitter_client_id_field' ),
			'avatar-steward',
			'avatar_steward_social_integrations'
		);

		// Twitter Client Secret.
		add_settings_field(
			'twitter_client_secret',
			__( 'Twitter Client Secret', 'avatar-steward' ),
			array( $this, 'render_twitter_client_secret_field' ),
			'avatar-steward',
			'avatar_steward_social_integrations'
		);

		// Facebook App ID.
		add_settings_field(
			'facebook_app_id',
			__( 'Facebook App ID', 'avatar-steward' ),
			array( $this, 'render_facebook_app_id_field' ),
			'avatar-steward',
			'avatar_steward_social_integrations'
		);

		// Facebook App Secret.
		add_settings_field(
			'facebook_app_secret',
			__( 'Facebook App Secret', 'avatar-steward' ),
			array( $this, 'render_facebook_app_secret_field' ),
			'avatar-steward',
			'avatar_steward_social_integrations'
		);

		// Delete attachment when removing avatar field.
		add_settings_field(
			'delete_attachment_on_remove',
			__( 'Delete Attachment on Remove', 'avatar-steward' ),
			array( $this, 'render_delete_attachment_on_remove_field' ),
			'avatar-steward',
			'avatar_steward_roles_permissions'
		);

		// Pro Features Section (only if Pro is active).
		if ( $this->license_manager->is_pro_active() ) {
			add_settings_section(
				'avatar_steward_pro_features',
				__( 'Pro Features', 'avatar-steward' ),
				array( $this, 'render_pro_features_section' ),
				'avatar-steward'
			);

			// Role-based file size limits.
			add_settings_field(
				'role_file_size_limits',
				__( 'Role-Based File Size Limits', 'avatar-steward' ),
				array( $this, 'render_role_file_size_limits_field' ),
				'avatar-steward',
				'avatar_steward_pro_features'
			);

			// Role-based format restrictions.
			add_settings_field(
				'role_format_restrictions',
				__( 'Role-Based Format Restrictions', 'avatar-steward' ),
				array( $this, 'render_role_format_restrictions_field' ),
				'avatar-steward',
				'avatar_steward_pro_features'
			);

			// Avatar expiration settings.
			add_settings_field(
				'avatar_expiration_enabled',
				__( 'Enable Avatar Expiration', 'avatar-steward' ),
				array( $this, 'render_avatar_expiration_enabled_field' ),
				'avatar-steward',
				'avatar_steward_pro_features'
			);

			add_settings_field(
				'avatar_expiration_days',
				__( 'Avatar Expiration Days', 'avatar-steward' ),
				array( $this, 'render_avatar_expiration_days_field' ),
				'avatar-steward',
				'avatar_steward_pro_features'
			);
		}
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( self::SETTINGS_GROUP );
				do_settings_sections( 'avatar-steward' );
				submit_button( __( 'Save Settings', 'avatar-steward' ) );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render upload restrictions section description.
	 *
	 * @return void
	 */
	public function render_upload_restrictions_section(): void {
		echo '<p>' . esc_html__( 'Configure restrictions for avatar uploads.', 'avatar-steward' ) . '</p>';
	}

	/**
	 * Render max file size field.
	 *
	 * @return void
	 */
	public function render_max_file_size_field(): void {
		$options = $this->get_settings();
		$value   = isset( $options['max_file_size'] ) ? $options['max_file_size'] : 2;
		?>
		<input type="number" 
				name="<?php echo esc_attr( self::OPTION_NAME . '[max_file_size]' ); ?>" 
				value="<?php echo esc_attr( $value ); ?>" 
				min="0.1" 
				max="10" 
				step="0.1" 
				class="small-text" />
		<p class="description">
			<?php esc_html_e( 'Maximum file size for avatar uploads in megabytes. Default: 2 MB.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Render allowed formats field.
	 *
	 * @return void
	 */
	public function render_allowed_formats_field(): void {
		$options           = $this->get_settings();
		$allowed_formats   = isset( $options['allowed_formats'] ) ? $options['allowed_formats'] : array();
		$available_formats = array(
			'image/jpeg' => 'JPEG',
			'image/png'  => 'PNG',
			'image/gif'  => 'GIF',
			'image/webp' => 'WebP',
		);

		foreach ( $available_formats as $mime => $label ) {
			$checked = in_array( $mime, $allowed_formats, true ) ? 'checked' : '';
			?>
			<label>
				<input type="checkbox" 
						name="<?php echo esc_attr( self::OPTION_NAME . '[allowed_formats][]' ); ?>" 
						value="<?php echo esc_attr( $mime ); ?>" 
						<?php echo esc_attr( $checked ); ?> />
				<?php echo esc_html( $label ); ?>
			</label><br>
			<?php
		}
		?>
		<p class="description">
			<?php esc_html_e( 'Select which image formats are allowed for avatar uploads.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Render max width field.
	 *
	 * @return void
	 */
	public function render_max_width_field(): void {
		$options = $this->get_settings();
		$value   = isset( $options['max_width'] ) ? $options['max_width'] : 2048;
		?>
		<input type="number" 
				name="<?php echo esc_attr( self::OPTION_NAME . '[max_width]' ); ?>" 
				value="<?php echo esc_attr( $value ); ?>" 
				min="100" 
				max="5000" 
				step="1" 
				class="small-text" />
		<p class="description">
			<?php esc_html_e( 'Maximum width for avatar images in pixels. Default: 2048px.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Render max height field.
	 *
	 * @return void
	 */
	public function render_max_height_field(): void {
		$options = $this->get_settings();
		$value   = isset( $options['max_height'] ) ? $options['max_height'] : 2048;
		?>
		<input type="number" 
				name="<?php echo esc_attr( self::OPTION_NAME . '[max_height]' ); ?>" 
				value="<?php echo esc_attr( $value ); ?>" 
				min="100" 
				max="5000" 
				step="1" 
				class="small-text" />
		<p class="description">
			<?php esc_html_e( 'Maximum height for avatar images in pixels. Default: 2048px.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Render convert to WebP field.
	 *
	 * @return void
	 */
	public function render_convert_to_webp_field(): void {
		$options = $this->get_settings();
		$checked = ! empty( $options['convert_to_webp'] ) ? 'checked' : '';
		?>
		<label>
			<input type="checkbox" 
					name="<?php echo esc_attr( self::OPTION_NAME . '[convert_to_webp]' ); ?>" 
					value="1" 
					<?php echo esc_attr( $checked ); ?> />
			<?php esc_html_e( 'Automatically convert uploaded images to WebP format', 'avatar-steward' ); ?>
		</label>
		<p class="description">
			<?php esc_html_e( 'WebP format provides better compression and smaller file sizes.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Render performance optimization section description.
	 *
	 * @return void
	 */
	public function render_performance_section(): void {
		echo '<p>' . esc_html__( 'Optimize avatar delivery for slow connections by automatically using SVG avatars when images exceed a size threshold.', 'avatar-steward' ) . '</p>';
	}

	/**
	 * Render low bandwidth mode field.
	 *
	 * @return void
	 */
	public function render_low_bandwidth_mode_field(): void {
		$options = $this->get_settings();
		$checked = ! empty( $options['low_bandwidth_mode'] ) ? 'checked' : '';
		?>
		<label>
			<input type="checkbox" 
					name="<?php echo esc_attr( self::OPTION_NAME . '[low_bandwidth_mode]' ); ?>" 
					value="1" 
					<?php echo esc_attr( $checked ); ?> />
			<?php esc_html_e( 'Use SVG avatars when images exceed threshold', 'avatar-steward' ); ?>
		</label>
		<p class="description">
			<?php esc_html_e( 'When enabled, SVG avatars with user initials are served instead of large image files, reducing bandwidth usage.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Render bandwidth threshold field.
	 *
	 * @return void
	 */
	public function render_bandwidth_threshold_field(): void {
		$options = $this->get_settings();
		$value   = isset( $options['bandwidth_threshold'] ) ? $options['bandwidth_threshold'] : 100;
		?>
		<input type="number" 
				name="<?php echo esc_attr( self::OPTION_NAME . '[bandwidth_threshold]' ); ?>" 
				value="<?php echo esc_attr( $value ); ?>" 
				min="10" 
				max="5000" 
				step="10" 
				class="small-text" />
		<p class="description">
			<?php esc_html_e( 'Avatar files larger than this threshold (in kilobytes) will be replaced with SVG versions. Default: 100 KB.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Render roles & permissions section description.
	 *
	 * @return void
	 */
	public function render_roles_permissions_section(): void {
		echo '<p>' . esc_html__( 'Configure which user roles can upload avatars and whether approval is required.', 'avatar-steward' ) . '</p>';
	}

	/**
	 * Render allowed roles field.
	 *
	 * @return void
	 */
	public function render_allowed_roles_field(): void {
		$options       = $this->get_settings();
		$allowed_roles = isset( $options['allowed_roles'] ) ? $options['allowed_roles'] : array();

		if ( ! function_exists( 'wp_roles' ) ) {
			return;
		}

		$roles = wp_roles()->roles;

		foreach ( $roles as $role_id => $role_data ) {
			$checked = in_array( $role_id, $allowed_roles, true ) ? 'checked' : '';
			?>
			<label>
				<input type="checkbox" 
						name="<?php echo esc_attr( self::OPTION_NAME . '[allowed_roles][]' ); ?>" 
						value="<?php echo esc_attr( $role_id ); ?>" 
						<?php echo esc_attr( $checked ); ?> />
				<?php echo esc_html( $role_data['name'] ); ?>
			</label><br>
			<?php
		}
		?>
		<p class="description">
			<?php esc_html_e( 'Select which user roles are allowed to upload avatars.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Render require approval field.
	 *
	 * @return void
	 */
	public function render_require_approval_field(): void {
		$options = $this->get_settings();
		$checked = ! empty( $options['require_approval'] ) ? 'checked' : '';
		?>
		<label>
			<input type="checkbox" 
					name="<?php echo esc_attr( self::OPTION_NAME . '[require_approval]' ); ?>" 
					value="1" 
					<?php echo esc_attr( $checked ); ?> />
			<?php esc_html_e( 'Require approval before avatars are published', 'avatar-steward' ); ?>
		</label>
		<p class="description">
			<?php esc_html_e( 'When enabled, uploaded avatars will be held for moderation before being displayed.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Render delete attachment on remove field.
	 *
	 * @return void
	 */
	public function render_delete_attachment_on_remove_field(): void {
		$options = $this->get_settings();
		$checked = ! empty( $options['delete_attachment_on_remove'] ) ? 'checked' : '';
		?>
		<label>
			<input type="checkbox" 
					name="<?php echo esc_attr( self::OPTION_NAME . '[delete_attachment_on_remove]' ); ?>" 
					value="1" 
					<?php echo esc_attr( $checked ); ?> />
			<?php esc_html_e( 'Delete attachment from Media Library when removing avatar', 'avatar-steward' ); ?>
		</label>
		<p class="description">
			<?php esc_html_e( 'When enabled, the avatar attachment will be permanently deleted from the Media Library when a user removes their avatar. The attachment will only be deleted if it is not used by other content.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Get current settings.
	 *
	 * @return array Current settings.
	 */
	public function get_settings(): array {
		if ( ! function_exists( 'get_option' ) ) {
			return $this->get_default_settings();
		}

		$options = get_option( self::OPTION_NAME, array() );

		return wp_parse_args( $options, $this->get_default_settings() );
	}

	/**
	 * Get default settings.
	 *
	 * @return array Default settings.
	 */
	public function get_default_settings(): array {
		return array(
			'max_file_size'               => 2.0,
			'allowed_formats'             => array( 'image/jpeg', 'image/png' ),
			'max_width'                   => 2048,
			'max_height'                  => 2048,
			'convert_to_webp'             => false,
			'low_bandwidth_mode'          => false,
			'bandwidth_threshold'         => 100,
			'allowed_roles'               => array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' ),
			'require_approval'            => false,
			'delete_attachment_on_remove' => false,
			'role_file_size_limits'       => array(),
			'role_format_restrictions'    => array(),
			'avatar_expiration_enabled'   => false,
			'avatar_expiration_days'      => 365,
		);
	}

	/**
	 * Render social integrations section description.
	 *
	 * @return void
	 */
	public function render_social_integrations_section(): void {
		?>
		<p><?php esc_html_e( 'Configure API credentials for social media integrations. Users will be able to import their profile pictures from connected accounts.', 'avatar-steward' ); ?></p>
		<p class="description">
			<?php
			printf(
				/* translators: 1: Twitter Developer Portal URL, 2: Facebook Developers URL */
				wp_kses_post( __( 'To obtain API credentials, create applications at <a href="%1$s" target="_blank">Twitter Developer Portal</a> and <a href="%2$s" target="_blank">Facebook for Developers</a>.', 'avatar-steward' ) ),
				'https://developer.twitter.com/en/portal/dashboard',
				'https://developers.facebook.com/'
			);
			?>
		</p>
		<?php
	}

	/**
	 * Render Twitter Client ID field.
	 *
	 * @return void
	 */
	public function render_twitter_client_id_field(): void {
		$value = get_option( 'avatarsteward_twitter_client_id', '' );
		?>
		<input type="text" 
				name="avatarsteward_twitter_client_id" 
				value="<?php echo esc_attr( $value ); ?>" 
				class="regular-text" />
		<p class="description">
			<?php esc_html_e( 'OAuth 2.0 Client ID from Twitter Developer Portal.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Render Twitter Client Secret field.
	 *
	 * @return void
	 */
	public function render_twitter_client_secret_field(): void {
		$value         = get_option( 'avatarsteward_twitter_client_secret', '' );
		$display_value = ! empty( $value ) ? '••••••••••••••••' : '';
		?>
		<input type="password" 
				name="avatarsteward_twitter_client_secret" 
				value="<?php echo esc_attr( $value ); ?>" 
				placeholder="<?php echo esc_attr( $display_value ); ?>"
				class="regular-text" />
		<p class="description">
			<?php esc_html_e( 'OAuth 2.0 Client Secret from Twitter Developer Portal.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Render Facebook App ID field.
	 *
	 * @return void
	 */
	public function render_facebook_app_id_field(): void {
		$value = get_option( 'avatarsteward_facebook_app_id', '' );
		?>
		<input type="text" 
				name="avatarsteward_facebook_app_id" 
				value="<?php echo esc_attr( $value ); ?>" 
				class="regular-text" />
		<p class="description">
			<?php esc_html_e( 'App ID from Facebook for Developers.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Render Facebook App Secret field.
	 *
	 * @return void
	 */
	public function render_facebook_app_secret_field(): void {
		$value         = get_option( 'avatarsteward_facebook_app_secret', '' );
		$display_value = ! empty( $value ) ? '••••••••••••••••' : '';
		?>
		<input type="password" 
				name="avatarsteward_facebook_app_secret" 
				value="<?php echo esc_attr( $value ); ?>" 
				placeholder="<?php echo esc_attr( $display_value ); ?>"
				class="regular-text" />
		<p class="description">
			<?php esc_html_e( 'App Secret from Facebook for Developers.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Render Pro features section description.
	 *
	 * @return void
	 */
	public function render_pro_features_section(): void {
		?>
		<p><?php esc_html_e( 'Advanced Pro features for role-based restrictions and avatar expiration.', 'avatar-steward' ); ?></p>
		<p class="description">
			<?php esc_html_e( 'These settings allow you to configure different upload restrictions for each user role and set automatic avatar expiration policies.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Render role-based file size limits field.
	 *
	 * @return void
	 */
	public function render_role_file_size_limits_field(): void {
		$options = $this->get_settings();
		$limits  = isset( $options['role_file_size_limits'] ) ? $options['role_file_size_limits'] : array();

		if ( ! function_exists( 'wp_roles' ) ) {
			return;
		}

		$roles = wp_roles()->roles;

		echo '<table class="form-table" style="margin-top: 0;">';
		echo '<tbody>';
		foreach ( $roles as $role_id => $role_data ) {
			$value = isset( $limits[ $role_id ] ) ? $limits[ $role_id ] : 2.0;
			?>
			<tr>
				<th scope="row"><?php echo esc_html( $role_data['name'] ); ?></th>
				<td>
					<input type="number" 
							name="<?php echo esc_attr( self::OPTION_NAME . '[role_file_size_limits][' . $role_id . ']' ); ?>" 
							value="<?php echo esc_attr( $value ); ?>" 
							min="0.1" 
							max="10" 
							step="0.1" 
							class="small-text" />
					<span class="description"><?php esc_html_e( 'MB', 'avatar-steward' ); ?></span>
				</td>
			</tr>
			<?php
		}
		echo '</tbody>';
		echo '</table>';
		?>
		<p class="description">
			<?php esc_html_e( 'Set maximum file size for each user role. Leave default (2 MB) if not specified.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Render role-based format restrictions field.
	 *
	 * @return void
	 */
	public function render_role_format_restrictions_field(): void {
		$options      = $this->get_settings();
		$restrictions = isset( $options['role_format_restrictions'] ) ? $options['role_format_restrictions'] : array();

		if ( ! function_exists( 'wp_roles' ) ) {
			return;
		}

		$roles             = wp_roles()->roles;
		$available_formats = array(
			'image/jpeg' => 'JPEG',
			'image/png'  => 'PNG',
			'image/gif'  => 'GIF',
			'image/webp' => 'WebP',
		);

		echo '<table class="form-table" style="margin-top: 0;">';
		echo '<tbody>';
		foreach ( $roles as $role_id => $role_data ) {
			$role_formats = isset( $restrictions[ $role_id ] ) ? $restrictions[ $role_id ] : array( 'image/jpeg', 'image/png' );
			?>
			<tr>
				<th scope="row"><?php echo esc_html( $role_data['name'] ); ?></th>
				<td>
					<?php foreach ( $available_formats as $mime => $label ) : ?>
						<?php $checked = in_array( $mime, $role_formats, true ) ? 'checked' : ''; ?>
						<label style="display: inline-block; margin-right: 15px;">
							<input type="checkbox" 
									name="<?php echo esc_attr( self::OPTION_NAME . '[role_format_restrictions][' . $role_id . '][]' ); ?>" 
									value="<?php echo esc_attr( $mime ); ?>" 
									<?php echo esc_attr( $checked ); ?> />
							<?php echo esc_html( $label ); ?>
						</label>
					<?php endforeach; ?>
				</td>
			</tr>
			<?php
		}
		echo '</tbody>';
		echo '</table>';
		?>
		<p class="description">
			<?php esc_html_e( 'Select which image formats each user role can upload. If no formats are selected for a role, the default allowed formats will be used.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Render avatar expiration enabled field.
	 *
	 * @return void
	 */
	public function render_avatar_expiration_enabled_field(): void {
		$options = $this->get_settings();
		$checked = ! empty( $options['avatar_expiration_enabled'] ) ? 'checked' : '';
		?>
		<label>
			<input type="checkbox" 
					name="<?php echo esc_attr( self::OPTION_NAME . '[avatar_expiration_enabled]' ); ?>" 
					value="1" 
					<?php echo esc_attr( $checked ); ?> />
			<?php esc_html_e( 'Automatically expire avatars after a set period', 'avatar-steward' ); ?>
		</label>
		<p class="description">
			<?php esc_html_e( 'When enabled, avatars will be automatically removed after the specified number of days.', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Render avatar expiration days field.
	 *
	 * @return void
	 */
	public function render_avatar_expiration_days_field(): void {
		$options = $this->get_settings();
		$value   = isset( $options['avatar_expiration_days'] ) ? $options['avatar_expiration_days'] : 365;
		?>
		<input type="number" 
				name="<?php echo esc_attr( self::OPTION_NAME . '[avatar_expiration_days]' ); ?>" 
				value="<?php echo esc_attr( $value ); ?>" 
				min="1" 
				max="3650" 
				step="1" 
				class="small-text" />
		<p class="description">
			<?php esc_html_e( 'Number of days after which avatars will expire. Default: 365 days (1 year).', 'avatar-steward' ); ?>
		</p>
		<?php
	}

	/**
	 * Sanitize settings before saving.
	 *
	 * @param array $input Raw input from form.
	 * @return array Sanitized settings.
	 */
	public function sanitize_settings( $input ): array {
		$sanitized = array();

		// Sanitize max file size.
		if ( isset( $input['max_file_size'] ) ) {
			$sanitized['max_file_size'] = floatval( $input['max_file_size'] );
			$sanitized['max_file_size'] = max( 0.1, min( 10.0, $sanitized['max_file_size'] ) );
		}

		// Sanitize allowed formats.
		if ( isset( $input['allowed_formats'] ) && is_array( $input['allowed_formats'] ) ) {
			$valid_formats                = array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp' );
			$sanitized['allowed_formats'] = array_intersect( $input['allowed_formats'], $valid_formats );
		} else {
			$sanitized['allowed_formats'] = array();
		}

		// Sanitize max width.
		if ( isset( $input['max_width'] ) ) {
			$sanitized['max_width'] = intval( $input['max_width'] );
			$sanitized['max_width'] = max( 100, min( 5000, $sanitized['max_width'] ) );
		}

		// Sanitize max height.
		if ( isset( $input['max_height'] ) ) {
			$sanitized['max_height'] = intval( $input['max_height'] );
			$sanitized['max_height'] = max( 100, min( 5000, $sanitized['max_height'] ) );
		}

		// Sanitize convert to WebP.
		$sanitized['convert_to_webp'] = ! empty( $input['convert_to_webp'] );

		// Sanitize low bandwidth mode.
		$sanitized['low_bandwidth_mode'] = ! empty( $input['low_bandwidth_mode'] );

		// Sanitize bandwidth threshold.
		if ( isset( $input['bandwidth_threshold'] ) ) {
			$sanitized['bandwidth_threshold'] = intval( $input['bandwidth_threshold'] );
			$sanitized['bandwidth_threshold'] = max( 10, min( 5000, $sanitized['bandwidth_threshold'] ) );
		}

		// Sanitize allowed roles.
		if ( isset( $input['allowed_roles'] ) && is_array( $input['allowed_roles'] ) ) {
			if ( function_exists( 'wp_roles' ) ) {
				$valid_roles                = array_keys( wp_roles()->roles );
				$sanitized['allowed_roles'] = array_intersect( $input['allowed_roles'], $valid_roles );
			} else {
				$sanitized['allowed_roles'] = array();
			}
		} else {
			$sanitized['allowed_roles'] = array();
		}

		// Sanitize require approval.
		$sanitized['require_approval'] = ! empty( $input['require_approval'] );

		// Sanitize delete attachment on remove.
		$sanitized['delete_attachment_on_remove'] = ! empty( $input['delete_attachment_on_remove'] );

		// Sanitize Pro features (only if Pro is active).
		if ( $this->license_manager->is_pro_active() ) {
			// Sanitize role-based file size limits.
			if ( isset( $input['role_file_size_limits'] ) && is_array( $input['role_file_size_limits'] ) ) {
				$sanitized['role_file_size_limits'] = array();
				if ( function_exists( 'wp_roles' ) ) {
					$valid_roles = array_keys( wp_roles()->roles );
					foreach ( $input['role_file_size_limits'] as $role => $size ) {
						if ( in_array( $role, $valid_roles, true ) ) {
							$sanitized_size                              = floatval( $size );
							$sanitized_size                              = max( 0.1, min( 10.0, $sanitized_size ) );
							$sanitized['role_file_size_limits'][ $role ] = $sanitized_size;
						}
					}
				}
			}

			// Sanitize role-based format restrictions.
			if ( isset( $input['role_format_restrictions'] ) && is_array( $input['role_format_restrictions'] ) ) {
				$sanitized['role_format_restrictions'] = array();
				$valid_formats                         = array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp' );
				if ( function_exists( 'wp_roles' ) ) {
					$valid_roles = array_keys( wp_roles()->roles );
					foreach ( $input['role_format_restrictions'] as $role => $formats ) {
						if ( in_array( $role, $valid_roles, true ) && is_array( $formats ) ) {
							$sanitized['role_format_restrictions'][ $role ] = array_intersect( $formats, $valid_formats );
						}
					}
				}
			}

			// Sanitize avatar expiration settings.
			$sanitized['avatar_expiration_enabled'] = ! empty( $input['avatar_expiration_enabled'] );

			if ( isset( $input['avatar_expiration_days'] ) ) {
				$sanitized['avatar_expiration_days'] = intval( $input['avatar_expiration_days'] );
				$sanitized['avatar_expiration_days'] = max( 1, min( 3650, $sanitized['avatar_expiration_days'] ) );
			}
		}

		// Note: Social integration credentials are stored separately for security.
		// They are handled via update_option calls in the individual render methods.

		return $sanitized;
	}

	/**
	 * Get the option name used for settings storage.
	 *
	 * @return string Option name.
	 */
	public function get_option_name(): string {
		return self::OPTION_NAME;
	}
}
