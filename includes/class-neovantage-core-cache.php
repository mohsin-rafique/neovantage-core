<?php
/**
 * The main cache class.
 *
 * @package Neovantage_Core
 * @subpackage Neovantage_Core
 */

/**
 * The cache handler.
 *
 * @since 2.0.0
 */
class Neovantage_Core_Cache {

	/**
	 * Resets all caches.
	 *
	 * @since 2.0.0
	 * @access public
	 * @param array $delete_cache An array of caches to delete.
	 * @return void
	 */
	public function reset_all_caches( $delete_cache = array() ) {

		$all_caches = apply_filters(
			'reset_all_caches',
			array(
				'demo_data'    => true,
				'transients'   => true,
				'other_caches' => true,
			)
		);

		$delete_cache = wp_parse_args(
			$delete_cache,
			$all_caches
		);

		if ( ! in_array( true, $delete_cache, true ) ) {
			// Early exit if all set to false.
			return;
		}

		// Get the upload directory for this site.
		$upload_dir = wp_upload_dir();

		if ( ! defined( 'FS_METHOD' ) ) {
			define( 'FS_METHOD', 'direct' );
		}

		// The WordPress filesystem.
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once wp_normalize_path( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		if ( true === $delete_cache['demo_data'] ) {
			$delete_demo_files = $wp_filesystem->delete( $upload_dir['basedir'] . '/neovantage-demo-data', true, 'd' );
		}

		if ( true === $delete_cache['transients'] ) {
			// Delete transients with dynamic names.
			$dynamic_transients = array(
				'_transient_neovantage_%',
				'_transient_neovantage_wordpress_org_plugins',
				'_site_transient_timeout_neovantage_%',
				'_site_transient_timeout_neovantage_wordpress_org_plugins',
			);
			global $wpdb;
			foreach ( $dynamic_transients as $transient ) {
				$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
					$wpdb->prepare(
						"DELETE FROM $wpdb->options WHERE option_name LIKE %s",
						$transient
					)
				);
			}

			// Cleanup other transients.
			$transients = array(
				'_neovantage_ajax_works',
				'neovantage_premium_plugins_info',
			);
			foreach ( $transients as $transient ) {
				delete_transient( $transient );
				delete_site_transient( $transient );
			}
		}

		if ( true === $delete_cache['other_caches'] ) {
			// Delete 3rd-party caches.
			$this->clear_third_party_caches();
		}
		do_action( 'neovantage_cache_reset_after' );
	}

	/**
	 * Clear cache from:
	 *  - W3TC,
	 *  - WordPress Total Cache
	 *  - WPEngine
	 *  - Varnish
	 *
	 * @access protected
	 * @since 1.0.0
	 */
	protected function clear_third_party_caches() {

		// If W3 Total Cache is being used, clear the cache.
		if ( function_exists( 'w3tc_flush_posts' ) ) {
			w3tc_flush_posts();
		}
		// if WP Super Cache is being used, clear the cache.
		if ( function_exists( 'wp_cache_clean_cache' ) ) {
			global $file_prefix;
			wp_cache_clean_cache( $file_prefix );
		}
		// If SG CachePress is installed, rese its caches.
		if ( class_exists( 'SG_CachePress_Supercacher' ) ) {
			if ( method_exists( 'SG_CachePress_Supercacher', 'purge_cache' ) ) {
				SG_CachePress_Supercacher::purge_cache();
			}
		}
		// Clear caches on WPEngine-hosted sites.
		if ( class_exists( 'WpeCommon' ) ) {
			if ( method_exists( 'WpeCommon', 'purge_memcached' ) ) {
				WpeCommon::purge_memcached();
			}
			if ( method_exists( 'WpeCommon', 'clear_maxcdn_cache' ) ) {
				WpeCommon::clear_maxcdn_cache();
			}
			if ( method_exists( 'WpeCommon', 'purge_varnish_cache' ) ) {
				WpeCommon::purge_varnish_cache();
			}
		}
		// if Autoptimize Cache is being used, clear the cache.
		if ( class_exists( 'autoptimizeCache' ) && method_exists( 'autoptimizeCache', 'clearall' ) ) {
			autoptimizeCache::clearall();
		}
		// if LiteSpeed Cache is being used, clear the cache.
		if ( class_exists( 'LiteSpeed_Cache_API' ) && method_exists( 'LiteSpeed_Cache_API', 'purge_all' ) ) {
			LiteSpeed_Cache_API::purge_all();
		}
	}

	/**
	 * Clear varnish cache for the dynamic CSS file.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @return void
	 */
	protected function clear_varnish_cache() {

		// Parse the URL for proxy proxies.
		$p = wp_parse_url( home_url() );

		$varnish_x_purgemethod = ( isset( $p['query'] ) && ( 'vhp=regex' === $p['query'] ) ) ? 'regex' : 'default';

		if ( ! class_exists( 'Fusion_Settings' ) ) {
			include_once 'class-fusion-settings.php';
		}
		// Build a varniship.
		$varniship = get_option( 'vhp_varnish_ip' );
		$settings  = Fusion_Settings::get_instance();
		if ( $settings->get( 'cache_server_ip' ) ) {
			$varniship = $settings->get( 'cache_server_ip' );
		} elseif ( defined( 'VHP_VARNISH_IP' ) && VHP_VARNISH_IP ) {
			$varniship = VHP_VARNISH_IP;
		}

		// If we made varniship, let it sail.
		$purgeme = ( isset( $varniship ) && null !== $varniship ) ? $varniship : $p['host'];

		wp_remote_request(
			'http://' . $purgeme,
			array(
				'method'  => 'PURGE',
				'headers' => array(
					'host'           => $p['host'],
					'X-Purge-Method' => $varnish_x_purgemethod,
				),
			)
		);
	}

	/**
	 * Handles resetting caches.
	 *
	 * @access public
	 * @since 1.1.2
	 */
	public function reset_caches_handler() {

		if ( is_multisite() && is_main_site() ) {
			$sites = get_sites();
			foreach ( $sites as $site ) {
				switch_to_blog( $site->blog_id );
				$this->reset_all_caches();
				restore_current_blog();
			}
			return;
		}
		$this->reset_all_caches();
	}
}
