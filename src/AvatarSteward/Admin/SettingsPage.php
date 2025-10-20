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

		// Delete attachment when removing avatar field.
		add_settings_field(
			'delete_attachment_on_remove',
			__( 'Delete Attachment on Remove', 'avatar-steward' ),
			array( $this, 'render_delete_attachment_on_remove_field' ),
			'avatar-steward',
			'avatar_steward_roles_permissions'
		);
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
		);
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
