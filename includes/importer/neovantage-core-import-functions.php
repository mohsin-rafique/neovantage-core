<?php
/**
 * NEOVANTAGE Core Import Functions.
 *
 * @author      PixelsPress <contact@pixelspress.com>
 * @copyright   (c) Copyright by PixelsPress
 * @link        https://pixelspress.com
 *
 * @package     Neovantage_Core
 * @subpackage  Neovantage_Core/Functions
 * @since       2.0.0
 */

/**
 * Don't resize images.
 * Returns an empty array.
 *
 * @since 2.0.0
 * @param array $sizes We don't really care in this context...
 * @return array
 */
function nc_filter_image_sizes( $sizes ) {
	return array();
}

/**
 * Rename sidebar.
 *
 * @since 2.0.0
 * @param string $name The name.
 * @return string
 */
function nc_name_to_class( $name ) {
	$class = str_replace( array( ' ', ',', '.', '"', "'", '/', '\\', '+', '=', ')', '(', '*', '&', '^', '%', '$', '#', '@', '!', '~', '`', '<', '>', '?', '[', ']', '{', '}', '|', ':' ), '', $name );
	return $class;
}

/**
 * Replace URLs.
 *
 * @since   2.0.0
 * @param   array $matches The matches.
 * @return  string
 */
function nc_fs_importer_replace_url( $matches ) {
	// Get the uploads folder.
	$wp_upload_dir = wp_upload_dir();
	if ( is_array( $matches ) ) {
		foreach ( $matches as $key => $match ) {
			if ( false !== strpos( $match, 'wp-content/uploads/sites/' ) ) {

				$meta_arr = maybe_unserialize( $match );
				if ( false !== $meta_arr && is_array( $meta_arr ) ) {
					foreach ( $meta_arr as $k => $v ) {
						if ( false !== strpos( $v, 'wp-content/uploads/sites/' ) ) {
							$parts = explode( 'wp-content/uploads/sites/', $v );
							if ( isset( $parts[1] ) ) {
								$sub_parts = explode( '/', $parts[1] );
								unset( $sub_parts[0] );
								$parts[1] = implode( '/', $sub_parts );

								// append the url to the uploads url.
								$parts[0]       = $wp_upload_dir['baseurl'];
								$meta_arr[ $k ] = implode( '/', $parts );
							}
						}
					}
					return serialize( $meta_arr ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
				} else {
					$parts = explode( 'wp-content/uploads/sites/', $match );
					if ( isset( $parts[1] ) ) {
						$sub_parts = explode( '/', $parts[1] );
						unset( $sub_parts[0] );
						$parts[1] = implode( '/', $sub_parts );

						// append the url to the uploads url.
						$parts[0] = $wp_upload_dir['baseurl'];

						return implode( '/', $parts );
					}
				}
			}
		}
	}
	return $matches;
}
