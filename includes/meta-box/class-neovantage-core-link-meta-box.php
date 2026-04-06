<?php
/**
 * Neovantage_Core_Link_Meta_Box class
 *
 * @link       https://pixelspress.com
 * @since      1.0.3
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes/meta-box
 */

/**
 * The class is used to define Link Post Meta.
 *
 * @link        https://pixelspress.com
 * @since       1.0.3
 * @package     Neovantage_Core
 * @subpackage  Neovantage_Core/includes/meta-box
 * @author      PixelsPress <support@pixelspress.com>
 */
abstract class Neovantage_Core_Link_Meta_Box {

	/**
	 * Add Link Post Format Meta Box.
	 */
	public static function add() {
		$screens = array( 'post' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'neovantage-meta-box-link', // Unique ID.
				'Link Settings', // Box title.
				array( self::class, 'html' ), // Content callback, must be of type callable.
				$screen // Post type.
			);
		}
	}

	/**
	 * Save Link Post Format Meta Box.
	 *
	 * @param  int $post_id Post ID.
	 */
	public static function save( $post_id ) {
		// Verify Nonce.
		$neovantage_link_meta_box_nonce = filter_input( INPUT_POST, 'neovantage_link_meta_box_nonce' );
		if ( ! isset( $neovantage_link_meta_box_nonce ) || ! wp_verify_nonce( $neovantage_link_meta_box_nonce, 'link-meta-box' ) ) {
			return $post_id;
		}

		// Check Autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check Permissions.
		$neovantage_post_type = filter_input( INPUT_POST, 'post_type' );
		if ( 'page' === $neovantage_post_type ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( array_key_exists( 'neovantage_link_title', $_POST ) ) {
			$neovantage_link_title = sanitize_text_field( filter_input( INPUT_POST, 'neovantage_link_title' ) );
			update_post_meta(
				$post_id,
				'_neovantage_link_title',
				$neovantage_link_title
			);
		}
		if ( array_key_exists( 'neovantage_link_url', $_POST ) ) {
			$neovantage_link_url = esc_url_raw( filter_input( INPUT_POST, 'neovantage_link_url' ) );
			update_post_meta(
				$post_id,
				'_neovantage_link_url',
				$neovantage_link_url
			);
		}
	}

	/**
	 * Display Link Post Format Meta Box HTML.
	 *
	 * @param object $post Post object.
	 */
	public static function html( $post ) {
		$link_title = get_post_meta( $post->ID, '_neovantage_link_title', true );
		$link_url   = get_post_meta( $post->ID, '_neovantage_link_url', true );
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="neovantage_link_title"><?php esc_html_e( 'Title', 'neovantage-core' ); ?></label></th>
				<td>
					<input type="hidden" name="neovantage_link_meta_box_nonce" id="neovantage_link_meta_box_nonce" value="<?php echo esc_attr( wp_create_nonce( 'link-meta-box' ) ); ?>" />
					<input type="text" name="neovantage_link_title" id="neovantage_link_title" value="<?php echo esc_attr( $link_title ); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e( 'Insert the URL Title you wish to name to.', 'neovantage-core' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="neovantage_link_url"><?php esc_html_e( 'URL', 'neovantage-core' ); ?></label></th>
				<td>
					<input type="url" name="neovantage_link_url" id="neovantage_link_url" value="<?php echo esc_url( $link_url ); ?>" class="regular-text code" />
					<p class="description"><?php esc_html_e( 'Insert the URL you wish to link to.', 'neovantage-core' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}
}
add_action( 'add_meta_boxes', array( 'Neovantage_Core_Link_Meta_Box', 'add' ) );
add_action( 'save_post', array( 'Neovantage_Core_Link_Meta_Box', 'save' ) );
