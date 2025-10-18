<?php
/**
 * Integration Service.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Integrations;

/**
 * Service for managing social media integrations.
 *
 * Coordinates multiple social providers and handles OAuth callbacks.
 */
class IntegrationService {

	/**
	 * Registered social providers.
	 *
	 * @var SocialProviderInterface[]
	 */
	private array $providers = array();

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function init(): void {
		$this->register_providers();
		$this->register_hooks();
	}

	/**
	 * Register default social providers.
	 *
	 * @return void
	 */
	private function register_providers(): void {
		$this->register_provider( new TwitterProvider() );
		$this->register_provider( new FacebookProvider() );

		// Allow third-party providers to be registered.
		if ( function_exists( 'do_action' ) ) {
			do_action( 'avatarsteward_register_providers', $this );
		}
	}

	/**
	 * Register a social provider.
	 *
	 * @param SocialProviderInterface $provider Provider instance.
	 * @return void
	 */
	public function register_provider( SocialProviderInterface $provider ): void {
		$this->providers[ $provider->get_name() ] = $provider;
	}

	/**
	 * Get a provider by name.
	 *
	 * @param string $name Provider name.
	 * @return SocialProviderInterface|null Provider instance or null if not found.
	 */
	public function get_provider( string $name ): ?SocialProviderInterface {
		return $this->providers[ $name ] ?? null;
	}

	/**
	 * Get all registered providers.
	 *
	 * @return SocialProviderInterface[] Array of provider instances.
	 */
	public function get_providers(): array {
		return $this->providers;
	}

	/**
	 * Get all configured providers.
	 *
	 * @return SocialProviderInterface[] Array of configured provider instances.
	 */
	public function get_configured_providers(): array {
		return array_filter(
			$this->providers,
			function ( SocialProviderInterface $provider ) {
				return $provider->is_configured();
			}
		);
	}

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	private function register_hooks(): void {
		add_action( 'show_user_profile', array( $this, 'render_profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'render_profile_fields' ) );
		add_action( 'admin_init', array( $this, 'handle_oauth_callback' ) );
		add_action( 'admin_post_avatarsteward_disconnect_social', array( $this, 'handle_disconnect' ) );
		add_action( 'admin_post_avatarsteward_import_social_avatar', array( $this, 'handle_import' ) );
	}

	/**
	 * Render social connections in user profile.
	 *
	 * @param \WP_User $user User object.
	 * @return void
	 */
	public function render_profile_fields( \WP_User $user ): void {
		$configured_providers = $this->get_configured_providers();
		if ( empty( $configured_providers ) ) {
			return;
		}

		wp_nonce_field( 'avatarsteward_social_action', 'avatarsteward_social_nonce' );
		?>
		<h2><?php esc_html_e( 'Social Avatar Import', 'avatar-steward' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'Connected Accounts', 'avatar-steward' ); ?></th>
				<td>
					<?php foreach ( $configured_providers as $provider ) : ?>
						<?php
						$is_connected   = $provider->is_connected( $user->ID );
						$provider_name  = $provider->get_name();
						$provider_label = $provider->get_label();
						?>
						<div class="avatarsteward-social-provider" style="margin-bottom: 15px;">
							<strong><?php echo esc_html( $provider_label ); ?>:</strong>
							<?php if ( $is_connected ) : ?>
								<span style="color: green;">✓ <?php esc_html_e( 'Connected', 'avatar-steward' ); ?></span>
								<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display: inline;">
									<?php wp_nonce_field( 'avatarsteward_social_action', 'avatarsteward_social_nonce' ); ?>
									<input type="hidden" name="action" value="avatarsteward_disconnect_social" />
									<input type="hidden" name="provider" value="<?php echo esc_attr( $provider_name ); ?>" />
									<input type="hidden" name="user_id" value="<?php echo esc_attr( (string) $user->ID ); ?>" />
									<button type="submit" class="button button-secondary">
										<?php esc_html_e( 'Disconnect', 'avatar-steward' ); ?>
									</button>
								</form>
								<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display: inline;">
									<?php wp_nonce_field( 'avatarsteward_social_action', 'avatarsteward_social_nonce' ); ?>
									<input type="hidden" name="action" value="avatarsteward_import_social_avatar" />
									<input type="hidden" name="provider" value="<?php echo esc_attr( $provider_name ); ?>" />
									<input type="hidden" name="user_id" value="<?php echo esc_attr( (string) $user->ID ); ?>" />
									<button type="submit" class="button button-primary">
										<?php esc_html_e( 'Import Avatar', 'avatar-steward' ); ?>
									</button>
								</form>
							<?php else : ?>
								<span style="color: gray;"><?php esc_html_e( 'Not connected', 'avatar-steward' ); ?></span>
								<?php
								$redirect_url = admin_url( 'profile.php?avatarsteward_oauth=' . $provider_name );
								$auth_url     = $provider->get_authorization_url( $user->ID, $redirect_url );
								?>
								<?php if ( $auth_url ) : ?>
									<a href="<?php echo esc_url( $auth_url ); ?>" class="button button-secondary">
										<?php
										/* translators: %s: Social provider name */
										echo esc_html( sprintf( __( 'Connect %s', 'avatar-steward' ), $provider_label ) );
										?>
									</a>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
					<p class="description">
						<?php esc_html_e( 'Connect your social media accounts to import your profile picture as your avatar.', 'avatar-steward' ); ?>
					</p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Handle OAuth callback from social providers.
	 *
	 * @return void
	 */
	public function handle_oauth_callback(): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['avatarsteward_oauth'] ) || ! isset( $_GET['code'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$provider_name = sanitize_text_field( wp_unslash( $_GET['avatarsteward_oauth'] ) );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$code = sanitize_text_field( wp_unslash( $_GET['code'] ) );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$state = isset( $_GET['state'] ) ? sanitize_text_field( wp_unslash( $_GET['state'] ) ) : '';

		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return;
		}

		$provider = $this->get_provider( $provider_name );
		if ( ! $provider ) {
			return;
		}

		// Validate state.
		$stored_state = get_transient( 'avatarsteward_' . $provider_name . '_state_' . $user_id );
		if ( $state !== $stored_state ) {
			wp_die( esc_html__( 'Invalid state parameter. Please try again.', 'avatar-steward' ) );
			return;
		}

		$success = $provider->handle_callback( $code, $user_id );

		$redirect_url = add_query_arg(
			array(
				'avatarsteward_social' => $success ? 'connected' : 'failed',
				'provider'             => $provider_name,
			),
			admin_url( 'profile.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Handle disconnect request.
	 *
	 * @return void
	 */
	public function handle_disconnect(): void {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! isset( $_POST['avatarsteward_social_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['avatarsteward_social_nonce'] ), 'avatarsteward_social_action' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'avatar-steward' ) );
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		if ( ! isset( $_POST['provider'] ) || ! isset( $_POST['user_id'] ) ) {
			wp_die( esc_html__( 'Invalid request.', 'avatar-steward' ) );
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$provider_name = sanitize_text_field( wp_unslash( $_POST['provider'] ) );
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$user_id = (int) wp_unslash( $_POST['user_id'] );

		if ( $user_id !== get_current_user_id() && ! current_user_can( 'edit_user', $user_id ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'avatar-steward' ) );
			return;
		}

		$provider = $this->get_provider( $provider_name );
		if ( ! $provider ) {
			wp_die( esc_html__( 'Invalid provider.', 'avatar-steward' ) );
			return;
		}

		$success = $provider->disconnect( $user_id );

		$redirect_url = add_query_arg(
			array(
				'avatarsteward_social' => $success ? 'disconnected' : 'failed',
				'provider'             => $provider_name,
			),
			admin_url( 'profile.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Handle import avatar request.
	 *
	 * @return void
	 */
	public function handle_import(): void {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! isset( $_POST['avatarsteward_social_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['avatarsteward_social_nonce'] ), 'avatarsteward_social_action' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'avatar-steward' ) );
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		if ( ! isset( $_POST['provider'] ) || ! isset( $_POST['user_id'] ) ) {
			wp_die( esc_html__( 'Invalid request.', 'avatar-steward' ) );
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$provider_name = sanitize_text_field( wp_unslash( $_POST['provider'] ) );
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$user_id = (int) wp_unslash( $_POST['user_id'] );

		if ( $user_id !== get_current_user_id() && ! current_user_can( 'edit_user', $user_id ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'avatar-steward' ) );
			return;
		}

		$provider = $this->get_provider( $provider_name );
		if ( ! $provider ) {
			wp_die( esc_html__( 'Invalid provider.', 'avatar-steward' ) );
			return;
		}

		$success = $provider->import_avatar( $user_id );

		$redirect_url = add_query_arg(
			array(
				'avatarsteward_social' => $success ? 'imported' : 'failed',
				'provider'             => $provider_name,
			),
			admin_url( 'profile.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}
}
