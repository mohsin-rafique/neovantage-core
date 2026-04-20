<?php
/**
 * Neovantage_Core_Audio_Meta_Box class
 *
 * @link       https://pixelspress.com
 * @since      1.0.3
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes/meta-box
 */

/**
 * The class is used to define Audio Post Meta.
 *
 * @link        https://pixelspress.com
 * @since       1.0.3
 * @package     Neovantage_Core
 * @subpackage  Neovantage_Core/includes/meta-box
 * @author      PixelsPress <support@pixelspress.com>
 */
abstract class Neovantage_Core_Audio_Meta_Box {

	/**
	 * Add Audio Post Format Meta Box.
	 */
	public static function add() {

		$screens = array( 'post' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'neovantage-meta-box-audio', // Unique ID.
				'Audio Settings', // Box title.
				array( self::class, 'html' ), // Content callback, must be of type callable.
				$screen                  // Post type.
			);
		}
	}

	/**
	 * Save Audio Post Format Meta Box.
	 *
	 * @param  int $post_id Post ID.
	 */
	public static function save( $post_id ) {

		// $post_id is required.
		if ( empty( $post_id ) ) {
			return;
		}

		// Check Autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Verify Nonce.
		$neovantage_audio_meta_box_nonce = filter_input( INPUT_POST, 'neovantage_audio_meta_box_nonce' );
		if ( ! isset( $neovantage_audio_meta_box_nonce ) || ! wp_verify_nonce( $neovantage_audio_meta_box_nonce, 'audio-meta-box' ) ) {
			return $post_id;
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

		if ( array_key_exists( 'neovantage_audio_url', $_POST ) ) {
			$neovantage_audio_url = esc_url_raw( filter_input( INPUT_POST, 'neovantage_audio_url' ) );
			update_post_meta(
				$post_id,
				'_neovantage_audio_url',
				$neovantage_audio_url
			);
		}
	}

	/**
	 * Display Audio Post Format Meta Box HTML.
	 *
	 * @param object $post Post object.
	 */
	public static function html( $post ) {
		$audio = get_post_meta( $post->ID, '_neovantage_audio_url', true );
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="neovantage_audio_url"><?php esc_html_e( 'Audio URL', 'neovantage-core' ); ?></label></th>
				<td>
					<input type="hidden" name="neovantage_audio_meta_box_nonce" id="neovantage_audio_meta_box_nonce" value="<?php echo esc_attr( wp_create_nonce( 'audio-meta-box' ) ); ?>" />
					<input type="url" name="neovantage_audio_url" id="neovantage_audio_url" value="<?php echo esc_url( $audio ); ?>" class="regular-text code" />
					<input type="button" name="neovantage_audio_url_button" id="neovantage_audio_url_button" value="<?php esc_html_e( 'Browse Audio File', 'neovantage-core' ); ?>" class="button" />
					<p class="description"><a target="_blank" href="https://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F"><?php esc_html_e( 'List of supported sites', 'neovantage-core' ); ?></a></p>
				</td>
			</tr>
		</table>
		<?php
	}

}

add_action( 'add_meta_boxes', array( 'Neovantage_Core_Audio_Meta_Box', 'add' ) );
add_action( 'save_post', array( 'Neovantage_Core_Audio_Meta_Box', 'save' ) );
