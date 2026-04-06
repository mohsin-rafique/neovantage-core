<?php
/**
 * NEOVANTAGE Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @author      PixelsPress <contact@pixelspress.com>
 * @copyright   (c) Copyright by PixelsPress
 * @link        https://pixelspress.com
 *
 * @category    Core
 * @package     Neovantage_Core
 * @subpackage  Neovantage_Core/functions
 * @version       2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

if ( ! function_exists( 'nc_plugin_url' ) ) :
	/**
	 * Get URL of plugin directory
	 *
	 * @param string $path path to append the following URL.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function nc_plugin_url( $path = '' ) {
		$url = NC_DIR_URL;
		if ( $path ) {
			$url .= $path;
		}
		return $url;
	}
endif;

if ( ! function_exists( 'nc_unparse_url' ) ) :
	/**
	 * Converts Parsed URL to Printable Link
	 *
	 * @param array $parsed_url Contain parsed URL.
	 *
	 * @return string
	 */
	function nc_unparse_url( $parsed_url ) {
		$scheme   = isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'] . '://' : '';
		$host     = isset( $parsed_url['host'] ) ? $parsed_url['host'] : '';
		$port     = isset( $parsed_url['port'] ) ? ':' . $parsed_url['port'] : '';
		$user     = isset( $parsed_url['user'] ) ? $parsed_url['user'] : '';
		$pass     = isset( $parsed_url['pass'] ) ? ':' . $parsed_url['pass'] : '';
		$pass     = ( $user || $pass ) ? "$pass@" : '';
		$path     = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';
		$query    = isset( $parsed_url['query'] ) ? '?' . $parsed_url['query'] : '';
		$fragment = isset( $parsed_url['fragment'] ) ? '#' . $parsed_url['fragment'] : '';

		// Schema has to be relative when there is no schema but host was defined!
		if ( ! empty( $parsed_url['host'] ) && empty( $parsed_url['scheme'] ) ) {
			$scheme = '//';
		}
		return "$scheme$user$pass$host$port$path$query$fragment";
	}
endif;

if ( ! function_exists( 'nc_help_tip' ) ) :
	/**
	 * Display a NEOVANTAGE Core help tip.
	 *
	 * @param   string $tip Help tip text.
	 * @param   bool   $allow_html Allow sanitized HTML if true or escape.
	 * @since   2.0.0
	 *
	 * @return  string
	 */
	function nc_help_tip( $tip, $allow_html = false ) {
		if ( $allow_html ) {
			$tip = nc_sanitize_tooltip( $tip );
		} else {
			$tip = esc_attr( $tip );
		}

		return '<span class="help_tip" data-tip="' . $tip . '">[?]</span>';
	}
endif;

if ( ! function_exists( 'nc_get_server_database_version' ) ) :
	/**
	 * Retrieves the MySQL server version. Based on $wpdb.
	 *
	 * @since 2.0.0
	 *
	 * @return array Version information.
	 */
	function nc_get_server_database_version() {
		global $wpdb;

		if ( empty( $wpdb->is_mysql ) ) {
			return array(
				'string' => '',
				'number' => '',
			);
		}

		// mysqli is required by WordPress 6.9+ / PHP 8.x — mysql_* functions were removed in PHP 7.
		$server_info = mysqli_get_server_info( $wpdb->dbh ); // phpcs:ignore WordPress.DB.RestrictedFunctions

		return array(
			'string' => $server_info,
			'number' => preg_replace( '/([^\d.]+).*/', '', $server_info ),
		);
	}
endif;

if ( ! function_exists( 'nc_is_plugin_activated' ) ) {
	/**
	 * Reset all NEOVANTAGE Caches.
	 *
	 * @since 2.0.0
	 * @param string $plugin Name of the plugin that should be checked.
	 * @return bool If plugin is active or not.
	 */
	function nc_is_plugin_activated( $plugin ) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || is_plugin_active_for_network( $plugin );
	}
}

if ( ! function_exists( 'nc_reset_all_caches' ) ) {
	/**
	 * Reset all NEOVANTAGE Caches.
	 *
	 * @since 2.0.0
	 * @param array $delete_cache An array of caches to delete.
	 *
	 * @return void
	 */
	function nc_reset_all_caches( $delete_cache = array() ) {
		// Reset fusion-caches.
		if ( ! class_exists( 'Neovantage_Core_Cache' ) ) {
			require_once NC_DIR_PATH . 'includes/class-neovantage-core-cache.php';
		}

		$nc_cache = new Neovantage_Core_Cache();
		$nc_cache->reset_all_caches( $delete_cache );

		wp_cache_flush();
	}
}

if ( ! function_exists( 'nc_wp_get_http' ) ) {
	/**
	 * Perform a HTTP HEAD or GET request.
	 *
	 * If $file_path is a writable filename, this will do a GET request and write
	 * the file to that path.
	 *
	 * This is a re-implementation of the deprecated wp_get_http() function from WP Core,
	 * but this time using the recommended WP_Http() class and the WordPress filesystem.
	 *
	 * @param string      $url       URL to fetch.
	 * @param string|bool $file_path Optional. File path to write request to. Default false.
	 * @param array       $args      Optional. Arguments to be passed-on to the request.
	 *
	 * @return bool|string False on failure and string of headers if HEAD request.
	 */
	function nc_wp_get_http( $url = false, $file_path = false, $args = array() ) {

		// No need to proceed if we don't have a $url or a $file_path.
		if ( ! $url || ! $file_path ) {
			return false;
		}

		$try_file_get_contents = false;

		// Make sure we normalize $file_path.
		$file_path = wp_normalize_path( $file_path );

		// Include the WP_Http class if it doesn't already exist.
		if ( ! class_exists( 'WP_Http' ) ) {
			include_once wp_normalize_path( ABSPATH . WPINC . '/class-http.php' );
		}
		// Inlude the wp_remote_get function if it doesn't already exist.
		if ( ! function_exists( 'wp_remote_get' ) ) {
			include_once wp_normalize_path( ABSPATH . WPINC . '/http.php' );
		}

		$args = wp_parse_args(
			$args,
			array(
				'timeout'    => 30,
				'user-agent' => 'pixelspress-user-agent',
			)
		);

		$response = wp_remote_get( esc_url_raw( $url ), $args );

		if ( is_wp_error( $response ) ) {
			return false;
		}
		$body = wp_remote_retrieve_body( $response );

		// Try file_get_contents if body is empty.
		if ( empty( $body ) ) {
			if ( function_exists( 'ini_get' ) && ini_get( 'allow_url_fopen' ) ) {
				$body = file_get_contents( $url );
			}
		}

		// Initialize the WordPress filesystem.
		$wp_filesystem = Neovantage_Core_Helper::init_filesystem();

		if ( ! defined( 'FS_CHMOD_DIR' ) ) {
			define( 'FS_CHMOD_DIR', ( 0755 & ~ umask() ) );
		}
		if ( ! defined( 'FS_CHMOD_FILE' ) ) {
			define( 'FS_CHMOD_FILE', ( 0644 & ~ umask() ) );
		}

		// Attempt to write the file.
		if ( ! $wp_filesystem->put_contents( $file_path, $body, FS_CHMOD_FILE ) ) {
			// If the attempt to write to the file failed, then fallback to fwrite.
			unlink( $file_path );
			$fp = fopen( $file_path, 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions

			$written = fwrite( $fp, $body ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			fclose( $fp ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			if ( false === $written ) {
				return false;
			}
		}

		// If all went well, then return the headers of the request.
		if ( isset( $response['headers'] ) ) {
			$response['headers']['response'] = $response['response']['code'];
			return $response['headers'];
		}

		// If all else fails, then return false.
		return false;
	}
}

if ( ! function_exists( 'nc_custom_contact_info' ) ) :

	/**
	 * Removes legacy contact fields and adds support for Facebook,Twitter, LinkedIn.
	 *
	 * @version 1.0.0
	 * @since 1.2.5
	 *
	 * @param array $fields  Array of default contact fields.
	 * @return array $fields Amended array of contact fields.
	 */
	function neovantage_custom_contact_info( $fields ) {

		// Remove legacy contact fields no longer used.
		unset( $fields['aim'] );
		unset( $fields['yim'] );
		unset( $fields['jabber'] );
		// Google+ was shut down in 2019 — remove the field if it still exists.
		unset( $fields['author_gplus'] );

		// Add theme-specific author page contact fields.
		$fields['author_email']    = esc_html__( 'Email (Author Page)', 'neovantage' );
		$fields['author_dribbble'] = esc_html__( 'Dribbble (Author Page)', 'neovantage' );

		// Return the amended contact fields.
		return $fields;
	}

endif;
add_filter( 'user_contactmethods', 'nc_custom_contact_info' );
