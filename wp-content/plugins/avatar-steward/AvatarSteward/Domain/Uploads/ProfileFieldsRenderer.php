<?php
/**
 * Profile Fields Renderer for avatar upload UI.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Uploads;

/**
 * Renders avatar upload fields in WordPress profile pages.
 */
class ProfileFieldsRenderer {

	/**
	 * Upload service instance.
	 *
	 * @var UploadService
	 */
	private UploadService $upload_service;

	/**
	 * Constructor.
	 *
	 * @param UploadService $upload_service Upload service instance.
	 */
	public function __construct( UploadService $upload_service ) {
		$this->upload_service = $upload_service;
	}

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'show_user_profile', array( $this, 'render_upload_field' ) );
		add_action( 'edit_user_profile', array( $this, 'render_upload_field' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_notices', array( $this, 'show_error_notice' ) );
	}

	/**
	 * Enqueue scripts for media uploader.
	 *
	 * @param string $hook Current admin page hook.
	 *
	 * @return void
	 */
	public function enqueue_scripts( string $hook ): void {
		if ( 'profile.php' !== $hook && 'user-edit.php' !== $hook ) {
			return;
		}

		wp_enqueue_media();

		// Get plugin base URL using the defined constant.
		$plugin_base_url = defined( 'AVATAR_STEWARD_PLUGIN_URL' ) ? AVATAR_STEWARD_PLUGIN_URL : plugin_dir_url( dirname( __DIR__, 3 ) );

		// Enqueue Avatar section CSS.
		wp_enqueue_style(
			'avatar-steward-profile',
			$plugin_base_url . 'assets/css/profile-avatar.css',
			array(),
			defined( 'AVATAR_STEWARD_VERSION' ) ? AVATAR_STEWARD_VERSION : '1.0.0'
		);

		// Enqueue Avatar section repositioning JS.
		wp_enqueue_script(
			'avatar-steward-profile',
			$plugin_base_url . 'assets/js/profile-avatar.js',
			array(),
			defined( 'AVATAR_STEWARD_VERSION' ) ? AVATAR_STEWARD_VERSION : '1.0.0',
			true
		);
	}

	/**
	 * Show error notice if upload failed.
	 *
	 * @return void
	 */
	public function show_error_notice(): void {
		$screen = get_current_screen();
		if ( ! $screen || ( 'profile' !== $screen->id && 'user-edit' !== $screen->id ) ) {
			return;
		}

		$user_id = get_current_user_id();
		$error   = get_transient( 'avatar_steward_error_' . $user_id );

		if ( $error ) {
			delete_transient( 'avatar_steward_error_' . $user_id );
			printf(
				'<div class="notice notice-error is-dismissible"><p>%s</p></div>',
				esc_html( $error )
			);
		}
	}

	/**
	 * Render the avatar upload field.
	 *
	 * @param \WP_User $user User object.
	 *
	 * @return void
	 */
	public function render_upload_field( \WP_User $user ): void {
		// Check user permissions.
		if ( ! current_user_can( 'edit_user', $user->ID ) ) {
			return;
		}

		$avatar_url = $this->upload_service->get_avatar_url( $user->ID, 96 );
		$has_avatar = false !== $avatar_url;

		?>
		<div id="avatar-steward-section" class="avatarsteward-highlight">
			<h2><?php esc_html_e( 'Avatar', 'avatar-steward' ); ?></h2>
			<table class="form-table">
				<tr>
					<th>
						<label for="avatar_steward_file"><?php esc_html_e( 'Upload Avatar', 'avatar-steward' ); ?></label>
					</th>
					<td>
						<div id="avatar-steward-container">
							<?php if ( $has_avatar ) : ?>
								<div id="avatar-steward-preview" style="margin-bottom: 10px;">
									<img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php esc_attr_e( 'Current Avatar', 'avatar-steward' ); ?>" style="max-width: 96px; max-height: 96px; display: block; margin-bottom: 10px;" />
								</div>
							<?php endif; ?>

							<input 
								type="file" 
								name="avatar_steward_file" 
								id="avatar_steward_file" 
								accept="image/jpeg,image/png,image/gif,image/webp"
							/>

							<?php if ( $has_avatar ) : ?>
								<div style="margin-top: 10px;">
									<label>
										<input 
											type="checkbox" 
											name="avatar_steward_remove" 
											id="avatar_steward_remove" 
											value="yes"
										/>
										<?php esc_html_e( 'Remove current avatar', 'avatar-steward' ); ?>
									</label>
								</div>
							<?php endif; ?>

							<?php wp_nonce_field( 'avatar_steward_update', 'avatar_steward_nonce' ); ?>

							<p class="description">
								<?php
								printf(
									/* translators: 1: file types, 2: max file size */
									esc_html__( 'Allowed file types: %1$s. Maximum file size: %2$s MB. Maximum dimensions: 2000x2000 pixels.', 'avatar-steward' ),
									'JPEG, PNG, GIF, WebP',
									'2'
								);
								?>
							</p>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<?php
	}
}
