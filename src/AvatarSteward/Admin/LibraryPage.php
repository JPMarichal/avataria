<?php
/**
 * Library Page class for avatar library admin interface.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Admin;

use AvatarSteward\Domain\Library\LibraryService;
use AvatarSteward\Domain\Uploads\UploadService;

/**
 * Handles the avatar library admin page.
 */
class LibraryPage {

	/**
	 * Library service instance.
	 *
	 * @var LibraryService
	 */
	private LibraryService $library_service;

	/**
	 * Upload service instance.
	 *
	 * @var UploadService
	 */
	private UploadService $upload_service;

	/**
	 * Page hook suffix.
	 *
	 * @var string|null
	 */
	private ?string $page_hook = null;

	/**
	 * Constructor.
	 *
	 * @param LibraryService $library_service Library service instance.
	 * @param UploadService  $upload_service  Upload service instance.
	 */
	public function __construct( LibraryService $library_service, UploadService $upload_service ) {
		$this->library_service = $library_service;
		$this->upload_service  = $upload_service;
	}

	/**
	 * Initialize the library page.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_avatar_library_upload', array( $this, 'handle_ajax_upload' ) );
		add_action( 'wp_ajax_avatar_library_delete', array( $this, 'handle_ajax_delete' ) );
		add_action( 'wp_ajax_avatar_library_search', array( $this, 'handle_ajax_search' ) );
	}

	/**
	 * Add admin menu page.
	 *
	 * @return void
	 */
	public function add_admin_menu(): void {
		$this->page_hook = add_submenu_page(
			'avatar-steward',
			__( 'Avatar Library', 'avatar-steward' ),
			__( 'Library', 'avatar-steward' ),
			'manage_options',
			'avatar-steward-library',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @param string $hook Current page hook.
	 *
	 * @return void
	 */
	public function enqueue_scripts( string $hook ): void {
		if ( $hook !== $this->page_hook ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_style(
			'avatar-steward-library',
			AVATAR_STEWARD_PLUGIN_URL . 'assets/css/library.css',
			array(),
			AVATAR_STEWARD_VERSION
		);

		wp_enqueue_script(
			'avatar-steward-library',
			AVATAR_STEWARD_PLUGIN_URL . 'assets/js/library.js',
			array( 'jquery', 'wp-util' ),
			AVATAR_STEWARD_VERSION,
			true
		);

		wp_localize_script(
			'avatar-steward-library',
			'avatarLibrary',
			array(
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				'nonce'         => wp_create_nonce( 'avatar_library_nonce' ),
				'strings'       => array(
					'uploadTitle'   => __( 'Upload Avatar to Library', 'avatar-steward' ),
					'uploadButton'  => __( 'Add to Library', 'avatar-steward' ),
					'deleteConfirm' => __( 'Are you sure you want to remove this avatar from the library?', 'avatar-steward' ),
					'errorGeneric'  => __( 'An error occurred. Please try again.', 'avatar-steward' ),
				),
			)
		);
	}

	/**
	 * Render the library page.
	 *
	 * @return void
	 */
	public function render_page(): void {
		// Handle form submission.
		if ( isset( $_POST['avatar_library_import'] ) && check_admin_referer( 'avatar_library_import' ) ) {
			$this->handle_import_submission();
		}

		$page     = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1;
		$search   = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
		$sector   = isset( $_GET['sector'] ) ? sanitize_text_field( $_GET['sector'] ) : '';
		$license  = isset( $_GET['license'] ) ? sanitize_text_field( $_GET['license'] ) : '';

		$results = $this->library_service->get_library_avatars(
			array(
				'page'     => $page,
				'per_page' => 20,
				'search'   => $search,
				'sector'   => $sector,
				'license'  => $license,
			)
		);

		$sectors  = $this->library_service->get_sectors();
		$licenses = $this->library_service->get_licenses();

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Avatar Library', 'avatar-steward' ); ?></h1>

			<div class="avatar-library-header">
				<button type="button" class="button button-primary avatar-library-upload-btn">
					<?php esc_html_e( 'Upload Avatar', 'avatar-steward' ); ?>
				</button>

				<button type="button" class="button avatar-library-import-btn">
					<?php esc_html_e( 'Import Templates', 'avatar-steward' ); ?>
				</button>
			</div>

			<!-- Search and Filters -->
			<div class="avatar-library-filters">
				<form method="get" action="">
					<input type="hidden" name="page" value="avatar-steward-library">

					<input
						type="search"
						name="s"
						value="<?php echo esc_attr( $search ); ?>"
						placeholder="<?php esc_attr_e( 'Search avatars...', 'avatar-steward' ); ?>"
					>

					<select name="sector">
						<option value=""><?php esc_html_e( 'All Sectors', 'avatar-steward' ); ?></option>
						<?php foreach ( $sectors as $s ) : ?>
							<option value="<?php echo esc_attr( $s ); ?>" <?php selected( $sector, $s ); ?>>
								<?php echo esc_html( $s ); ?>
							</option>
						<?php endforeach; ?>
					</select>

					<select name="license">
						<option value=""><?php esc_html_e( 'All Licenses', 'avatar-steward' ); ?></option>
						<?php foreach ( $licenses as $l ) : ?>
							<option value="<?php echo esc_attr( $l ); ?>" <?php selected( $license, $l ); ?>>
								<?php echo esc_html( $l ); ?>
							</option>
						<?php endforeach; ?>
					</select>

					<button type="submit" class="button"><?php esc_html_e( 'Filter', 'avatar-steward' ); ?></button>
				</form>
			</div>

			<!-- Avatar Grid -->
			<div class="avatar-library-grid">
				<?php if ( empty( $results['items'] ) ) : ?>
					<p class="avatar-library-empty">
						<?php esc_html_e( 'No avatars found in the library.', 'avatar-steward' ); ?>
					</p>
				<?php else : ?>
					<?php foreach ( $results['items'] as $item ) : ?>
						<div class="avatar-library-item" data-id="<?php echo esc_attr( $item['id'] ); ?>">
							<div class="avatar-library-item-thumb">
								<img src="<?php echo esc_url( $item['thumb'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>">
							</div>
							<div class="avatar-library-item-info">
								<h3><?php echo esc_html( $item['title'] ); ?></h3>
								<?php if ( ! empty( $item['metadata']['author'] ) ) : ?>
									<p class="avatar-library-meta">
										<strong><?php esc_html_e( 'Author:', 'avatar-steward' ); ?></strong>
										<?php echo esc_html( $item['metadata']['author'] ); ?>
									</p>
								<?php endif; ?>
								<?php if ( ! empty( $item['metadata']['license'] ) ) : ?>
									<p class="avatar-library-meta">
										<strong><?php esc_html_e( 'License:', 'avatar-steward' ); ?></strong>
										<?php echo esc_html( $item['metadata']['license'] ); ?>
									</p>
								<?php endif; ?>
								<?php if ( ! empty( $item['metadata']['sector'] ) ) : ?>
									<p class="avatar-library-meta">
										<strong><?php esc_html_e( 'Sector:', 'avatar-steward' ); ?></strong>
										<?php echo esc_html( $item['metadata']['sector'] ); ?>
									</p>
								<?php endif; ?>
								<?php if ( ! empty( $item['metadata']['tags'] ) && is_array( $item['metadata']['tags'] ) ) : ?>
									<p class="avatar-library-meta">
										<strong><?php esc_html_e( 'Tags:', 'avatar-steward' ); ?></strong>
										<?php echo esc_html( implode( ', ', $item['metadata']['tags'] ) ); ?>
									</p>
								<?php endif; ?>
							</div>
							<div class="avatar-library-item-actions">
								<button type="button" class="button button-small avatar-library-select" data-id="<?php echo esc_attr( $item['id'] ); ?>">
									<?php esc_html_e( 'Select', 'avatar-steward' ); ?>
								</button>
								<button type="button" class="button button-small button-link-delete avatar-library-delete" data-id="<?php echo esc_attr( $item['id'] ); ?>">
									<?php esc_html_e( 'Remove', 'avatar-steward' ); ?>
								</button>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

			<!-- Pagination -->
			<?php if ( $results['total_pages'] > 1 ) : ?>
				<div class="avatar-library-pagination">
					<?php
					echo paginate_links(
						array(
							'base'      => add_query_arg( 'paged', '%#%' ),
							'format'    => '',
							'current'   => $page,
							'total'     => $results['total_pages'],
							'prev_text' => __( '&laquo; Previous', 'avatar-steward' ),
							'next_text' => __( 'Next &raquo;', 'avatar-steward' ),
						)
					);
					?>
				</div>
			<?php endif; ?>

			<!-- Import Form Modal -->
			<div id="avatar-library-import-modal" style="display: none;">
				<form method="post" enctype="multipart/form-data">
					<?php wp_nonce_field( 'avatar_library_import' ); ?>

					<p>
						<label for="import-sector"><?php esc_html_e( 'Sector:', 'avatar-steward' ); ?></label>
						<input type="text" id="import-sector" name="sector" required class="regular-text">
					</p>

					<p>
						<label for="import-files"><?php esc_html_e( 'Avatar Files:', 'avatar-steward' ); ?></label>
						<input type="file" id="import-files" name="avatar_files[]" multiple accept="image/*" required>
					</p>

					<p class="submit">
						<button type="submit" name="avatar_library_import" class="button button-primary">
							<?php esc_html_e( 'Import Templates', 'avatar-steward' ); ?>
						</button>
					</p>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Handle template import submission.
	 *
	 * @return void
	 */
	private function handle_import_submission(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to import templates.', 'avatar-steward' ) );
		}

		$sector = isset( $_POST['sector'] ) ? sanitize_text_field( $_POST['sector'] ) : '';

		if ( empty( $sector ) ) {
			add_settings_error(
				'avatar_library',
				'no_sector',
				__( 'Please specify a sector.', 'avatar-steward' ),
				'error'
			);
			return;
		}

		if ( empty( $_FILES['avatar_files']['name'][0] ) ) {
			add_settings_error(
				'avatar_library',
				'no_files',
				__( 'Please select files to import.', 'avatar-steward' ),
				'error'
			);
			return;
		}

		// Handle multiple file upload.
		$files = $_FILES['avatar_files'];
		$file_paths = array();

		foreach ( $files['name'] as $key => $value ) {
			if ( $files['error'][ $key ] === UPLOAD_ERR_OK ) {
				$file_paths[] = $files['tmp_name'][ $key ];
			}
		}

		$result = $this->library_service->import_sectoral_templates( $sector, $file_paths );

		if ( $result['success'] > 0 ) {
			add_settings_error(
				'avatar_library',
				'import_success',
				sprintf(
					/* translators: %d: number of avatars imported */
					_n(
						'%d avatar imported successfully.',
						'%d avatars imported successfully.',
						$result['success'],
						'avatar-steward'
					),
					$result['success']
				),
				'success'
			);
		}

		if ( $result['failed'] > 0 ) {
			add_settings_error(
				'avatar_library',
				'import_errors',
				sprintf(
					/* translators: %d: number of avatars that failed */
					_n(
						'%d avatar failed to import.',
						'%d avatars failed to import.',
						$result['failed'],
						'avatar-steward'
					),
					$result['failed']
				),
				'error'
			);
		}

		settings_errors( 'avatar_library' );
	}

	/**
	 * Handle AJAX upload.
	 *
	 * @return void
	 */
	public function handle_ajax_upload(): void {
		check_ajax_referer( 'avatar_library_nonce', 'nonce' );

		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'avatar-steward' ) ) );
		}

		if ( ! isset( $_FILES['file'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No file uploaded.', 'avatar-steward' ) ) );
		}

		// Validate file.
		$validation = $this->upload_service->validate_file( $_FILES['file'] );
		if ( ! $validation['success'] ) {
			wp_send_json_error( array( 'message' => $validation['message'] ) );
		}

		// Upload file.
		if ( ! function_exists( 'media_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
		}

		$attachment_id = media_handle_upload( 'file', 0 );

		if ( is_wp_error( $attachment_id ) ) {
			wp_send_json_error( array( 'message' => $attachment_id->get_error_message() ) );
		}

		// Get metadata from request.
		$metadata = array(
			'author'  => isset( $_POST['author'] ) ? sanitize_text_field( $_POST['author'] ) : '',
			'license' => isset( $_POST['license'] ) ? sanitize_text_field( $_POST['license'] ) : '',
			'sector'  => isset( $_POST['sector'] ) ? sanitize_text_field( $_POST['sector'] ) : '',
			'tags'    => isset( $_POST['tags'] ) ? sanitize_text_field( $_POST['tags'] ) : '',
		);

		// Add to library.
		$this->library_service->add_to_library( $attachment_id, $metadata );

		wp_send_json_success(
			array(
				'message'       => __( 'Avatar added to library successfully.', 'avatar-steward' ),
				'attachment_id' => $attachment_id,
			)
		);
	}

	/**
	 * Handle AJAX delete.
	 *
	 * @return void
	 */
	public function handle_ajax_delete(): void {
		check_ajax_referer( 'avatar_library_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'avatar-steward' ) ) );
		}

		$attachment_id = isset( $_POST['attachment_id'] ) ? (int) $_POST['attachment_id'] : 0;

		if ( ! $attachment_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid attachment ID.', 'avatar-steward' ) ) );
		}

		$this->library_service->remove_from_library( $attachment_id );

		wp_send_json_success( array( 'message' => __( 'Avatar removed from library.', 'avatar-steward' ) ) );
	}

	/**
	 * Handle AJAX search.
	 *
	 * @return void
	 */
	public function handle_ajax_search(): void {
		check_ajax_referer( 'avatar_library_nonce', 'nonce' );

		$page     = isset( $_POST['page'] ) ? max( 1, (int) $_POST['page'] ) : 1;
		$search   = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
		$sector   = isset( $_POST['sector'] ) ? sanitize_text_field( $_POST['sector'] ) : '';
		$license  = isset( $_POST['license'] ) ? sanitize_text_field( $_POST['license'] ) : '';

		$results = $this->library_service->get_library_avatars(
			array(
				'page'     => $page,
				'per_page' => 20,
				'search'   => $search,
				'sector'   => $sector,
				'license'  => $license,
			)
		);

		wp_send_json_success( $results );
	}
}
