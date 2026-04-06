<?php
/**
 * Neovantage_Core_Quote_Meta_Box class
 *
 * @link       https://pixelspress.com
 * @since      1.0.3
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes/meta-box
 */

/**
 * Neovantage_Core_Quote_Meta_Box class
 * The class is used to define Link Post Meta.
 *
 * @link        https://pixelspress.com
 * @since       1.0.3
 * @package     Neovantage_Core
 * @subpackage  Neovantage_Core/includes/meta-box
 * @author      PixelsPress <support@pixelspress.com>
 */
abstract class Neovantage_Core_Quote_Meta_Box {

	/**
	 * Add Quote Post Format Meta Box.
	 */
	public static function add() {
		$screens = array( 'post' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'neovantage-meta-box-quote',    // Unique ID.
				'Quote Settings',               // Box title.
				array( self::class, 'html' ),        // Content callback, must be of type callable.
				$screen                         // Post type.
			);
		}
	}

	public static function save( $post_id ) {
		// Verify Nonce.
		$neovantage_quote_meta_box_nonce = filter_input( INPUT_POST, 'neovantage_quote_meta_box_nonce' );
		if ( ! isset( $neovantage_quote_meta_box_nonce ) || ! wp_verify_nonce( $neovantage_quote_meta_box_nonce, 'quote-meta-box' ) ) {
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

		if ( array_key_exists( 'neovantage_quote_author', $_POST ) ) {
			$neovantage_quote_author = sanitize_text_field( filter_input( INPUT_POST, 'neovantage_quote_author' ) );
			update_post_meta(
				$post_id,
				'_neovantage_quote_author',
				$neovantage_quote_author
			);
		}
	}

	public static function html( $post ) {
		$author = get_post_meta( $post->ID, '_neovantage_quote_author', true );
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="neovantage_quote_author"><?php esc_html_e( 'Author', 'neovantage-core' ); ?></label></th>
				<td>
					<input type="hidden" name="neovantage_quote_meta_box_nonce" id="neovantage_quote_meta_box_nonce" value="<?php echo esc_attr( wp_create_nonce( 'quote-meta-box' ) ); ?>" />
					<input type="text" name="neovantage_quote_author" id="neovantage_quote_author" value="<?php echo esc_attr( $author ); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e( 'Put quote author in this field.', 'neovantage-core' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}
}
add_action( 'add_meta_boxes', array( 'Neovantage_Core_Quote_Meta_Box', 'add' ) );
add_action( 'save_post', array( 'Neovantage_Core_Quote_Meta_Box', 'save' ) );
