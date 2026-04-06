<?php
/**
 * Neovantage_Core_User_Meta_Box class
 * The class is used to define User Post Meta.
 *
 * @link        https://pixelspress.com
 * @since       1.0.5
 * @package     Neovantage_Core
 * @subpackage  Neovantage_Core/includes/meta-box
 * @author      PixelsPress <support@pixelspress.com>
 */
abstract class Neovantage_Core_User_Meta_Box {
	public static function add() {
		$screens = array( 'post' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'neovantage-meta-box-user',   // Unique ID.
				'Author Options',             // Box title.
				array( self::class, 'html' ),      // Content callback, must be of type callable.
				$screen                       // Post type.
			);
		}
	}

	public static function save( $post_id ) {
		// Verify Nonce.
		$neovantage_audio_meta_box_nonce = filter_input( INPUT_POST, 'neovantage_audio_meta_box_nonce' );
		if ( ! isset( $neovantage_audio_meta_box_nonce ) || ! wp_verify_nonce( $neovantage_audio_meta_box_nonce, 'audio-meta-box' ) ) {
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

		if ( array_key_exists( 'neovantage_audio_url', $_POST ) ) {
			$neovantage_audio_url = esc_url_raw( filter_input( INPUT_POST, 'neovantage_audio_url' ) );
			update_user_meta(
				$post_id,
				'_neovantage_audio_url',
				$neovantage_audio_url
			);
		}
	}

	public static function html( $post ) {
		$audio = get_user_meta( $post->ID, '_neovantage_audio_url', true );
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
add_action( 'add_meta_boxes', array( 'Neovantage_Core_User_Meta_Box', 'add' ) );
add_action( 'save_post', array( 'Neovantage_Core_User_Meta_Box', 'save' ) );
