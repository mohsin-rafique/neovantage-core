<?php
/**
 * NEOVANTAGE Core — Self-update manager.
 *
 * Injects NEOVANTAGE Core into WordPress's native plugin update system so
 * that users see update notices in Dashboard → Updates, the Plugins list
 * row, and the admin bar badge — exactly the same experience as any plugin
 * hosted on wordpress.org, even though this plugin is distributed via
 * GitHub Releases.
 *
 * Architecture
 * ------------
 * Version data is fetched from the GitHub Releases API:
 *   https://api.github.com/repos/mohsin-rafique/neovantage-core/releases/latest
 *
 * The response is cached in a site transient for 12 hours
 * (`neovantage_core_update_info`). WordPress itself checks for plugin updates
 * every 12 hours in the background, so this TTL aligns with that cycle —
 * one GitHub API request per WordPress update check cycle per site.
 *
 * Hooks registered
 * ----------------
 * - `pre_set_site_transient_update_plugins` → injects the update row.
 * - `plugins_api`                           → populates the details popup.
 *
 * Backward compatibility
 * ----------------------
 * Earlier versions of the NEOVANTAGE theme (before 2.0.6) carried these
 * same hooks. To prevent duplicate registrations on sites with an older
 * theme, this class checks whether those functions are already defined
 * before adding its own hooks. Once the theme is updated, the theme-side
 * hooks will no longer exist and this class takes full ownership.
 *
 * This is the same update pattern used by ACF Pro, GeneratePress Premium,
 * and WP Rocket, adapted for GitHub Releases distribution.
 *
 * @link       https://pixelspress.com
 * @since      2.0.6
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Self-update manager for NEOVANTAGE Core.
 *
 * Registered as a plain hook (not via the loader) so it fires even before
 * the plugin is fully bootstrapped — WordPress checks for updates early in
 * the admin lifecycle, before `plugins_loaded` in some contexts.
 *
 * @since  2.0.6
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes
 * @author     PixelsPress <support@pixelspress.com>
 */
class Neovantage_Core_Updater {

	/**
	 * GitHub repository owner (organisation or username).
	 *
	 * @since 2.0.6
	 * @var   string
	 */
	const GITHUB_OWNER = 'mohsin-rafique';

	/**
	 * GitHub repository name.
	 *
	 * @since 2.0.6
	 * @var   string
	 */
	const GITHUB_REPO = 'neovantage-core';

	/**
	 * Transient key used to cache the GitHub release data.
	 *
	 * Uses a site transient so the cache is shared across network sites on
	 * multisite, and because WordPress's own update transients are site-level.
	 *
	 * @since 2.0.6
	 * @var   string
	 */
	const TRANSIENT_KEY = 'neovantage_core_update_info';

	/**
	 * How long to cache the GitHub release data (seconds).
	 *
	 * Set to 12 hours to align with WordPress's own plugin update check cycle,
	 * which also runs every 12 hours. This means at most one GitHub API request
	 * per WordPress update check per site.
	 *
	 * @since 2.0.6
	 * @var   int
	 */
	const CACHE_TTL = 12 * HOUR_IN_SECONDS;

	/**
	 * Plugin file path relative to the plugins directory.
	 *
	 * @since 2.0.6
	 * @var   string
	 */
	const PLUGIN_BASENAME = 'neovantage-core/neovantage-core.php';

	/**
	 * Register WordPress hooks.
	 *
	 * Called once from the plugin bootstrap. Guards against duplicate
	 * registration when the older theme-side functions are still present
	 * (backward compatibility for sites not yet on theme 2.0.6+).
	 *
	 * @since  2.0.6
	 * @return void
	 */
	public function register() {

		// Backward compat: if the theme still carries these functions from
		// before the logic was moved here, skip registering duplicates.
		// Once the theme is updated, these functions will no longer exist
		// and this class takes full ownership.
		if ( ! function_exists( 'neovantage_inject_core_plugin_update' ) ) {
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'inject_update' ) );
		}

		if ( ! function_exists( 'neovantage_core_plugin_info' ) ) {
			add_filter( 'plugins_api', array( $this, 'plugin_info' ), 20, 3 );
		}
	}

	/**
	 * Fetch and cache the latest release data from the GitHub Releases API.
	 *
	 * Queries the GitHub Releases API for the latest release of this plugin
	 * and extracts the version number and download URL. The result is cached
	 * in a site transient for {@see CACHE_TTL} seconds.
	 *
	 * The download URL is taken from the first `.zip` release asset. If the
	 * release has no attached assets, the GitHub-generated `zipball_url` is
	 * used as a fallback (note: auto-generated zips may have a different
	 * directory structure than a manually packaged plugin zip).
	 *
	 * GitHub API rate limits: 60 unauthenticated requests per hour per IP.
	 * With a 12-hour cache TTL this equates to at most 2 requests per day per
	 * site, well within the limit for any realistic deployment.
	 *
	 * @since  2.0.6
	 * @return array {
	 *     Parsed release data, or an empty array on failure.
	 *
	 *     @type string $version      The version string (e.g. '2.0.6').
	 *     @type string $download_url Direct URL to the plugin zip archive.
	 * }
	 */
	public function get_remote_data() {

		$cached = get_site_transient( self::TRANSIENT_KEY );

		if ( false !== $cached && ! empty( $cached ) ) {
			return $cached;
		}

		$api_url = sprintf(
			'https://api.github.com/repos/%s/%s/releases/latest',
			self::GITHUB_OWNER,
			self::GITHUB_REPO
		);

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout'    => 10,
				'user-agent' => 'pixelspress-user-agent',
				'headers'    => array( 'Accept' => 'application/vnd.github+json' ),
			)
		);

		if ( is_wp_error( $response ) ) {
			return array();
		}

		if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return array();
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! is_array( $data ) || empty( $data['tag_name'] ) ) {
			return array();
		}

		// Strip the leading 'v' from tag names like 'v2.0.6'.
		$version = ltrim( $data['tag_name'], 'v' );

		// Prefer an explicitly attached .zip asset over the auto-generated zipball.
		$download_url = '';

		if ( ! empty( $data['assets'] ) ) {
			foreach ( $data['assets'] as $asset ) {
				if (
					isset( $asset['browser_download_url'] ) &&
					str_ends_with( $asset['browser_download_url'], '.zip' )
				) {
					$download_url = $asset['browser_download_url'];
					break;
				}
			}
		}

		// Fall back to the auto-generated source zip when no assets are attached.
		if ( empty( $download_url ) && ! empty( $data['zipball_url'] ) ) {
			$download_url = $data['zipball_url'];
		}

		if ( ! $version || ! $download_url ) {
			return array();
		}

		$result = array(
			'version'      => $version,
			'download_url' => $download_url,
		);

		set_site_transient( self::TRANSIENT_KEY, $result, self::CACHE_TTL );

		return $result;
	}

	/**
	 * Inject NEOVANTAGE Core into the WordPress native update transient.
	 *
	 * WordPress calls `pre_set_site_transient_update_plugins` every 12 hours
	 * in the background and immediately when the user visits Dashboard → Updates.
	 * Adding data to `$transient->response` causes WordPress to show the update
	 * badge, the Plugins list row notice, and the Updates page entry.
	 *
	 * @since  2.0.6
	 *
	 * @param  object $transient The plugin update transient.
	 * @return object Transient, with NEOVANTAGE Core injected when an update is available.
	 */
	public function inject_update( $transient ) {

		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		if ( ! isset( $transient->checked[ self::PLUGIN_BASENAME ] ) ) {
			return $transient;
		}

		$installed_version = $transient->checked[ self::PLUGIN_BASENAME ];

		$release = $this->get_remote_data();

		if ( empty( $release['version'] ) || empty( $release['download_url'] ) ) {
			return $transient;
		}

		// Only inject when the remote version is strictly newer.
		if ( version_compare( $installed_version, $release['version'], '>=' ) ) {
			return $transient;
		}

		$transient->response[ self::PLUGIN_BASENAME ] = (object) array(
			'id'           => self::PLUGIN_BASENAME,
			'slug'         => self::GITHUB_REPO,
			'plugin'       => self::PLUGIN_BASENAME,
			'new_version'  => $release['version'],
			'url'          => 'https://github.com/mohsin-rafique/neovantage-core',
			'package'      => $release['download_url'],
			'icons'        => array(),
			'banners'      => array(),
			'requires'     => '5.3',
			'requires_php' => '8.0',
			'tested'       => '6.9',
		);

		return $transient;
	}

	/**
	 * Provide plugin information for the "View version details" popup.
	 *
	 * Without this hook, WordPress queries wordpress.org for plugin details
	 * and returns "Plugin not found" because neovantage-core is not listed there.
	 * This hook intercepts only requests for `neovantage-core` and returns a
	 * populated object so the popup shows useful information.
	 *
	 * @since  2.0.6
	 *
	 * @param  false|object|WP_Error $result The result object, or false.
	 * @param  string                $action The API action being requested.
	 * @param  object                $args   API arguments including `$args->slug`.
	 * @return false|object Passes through for all other plugins; object for neovantage-core.
	 */
	public function plugin_info( $result, $action, $args ) {

		if ( 'plugin_information' !== $action ) {
			return $result;
		}

		if ( self::GITHUB_REPO !== $args->slug ) {
			return $result;
		}

		$release = $this->get_remote_data();
		$version = ! empty( $release['version'] ) ? $release['version'] : NC_VERSION;
		$package = ! empty( $release['download_url'] ) ? $release['download_url'] : '';

		return (object) array(
			'name'          => 'NEOVANTAGE Core',
			'slug'          => self::GITHUB_REPO,
			'version'       => $version,
			'author'        => '<a href="https://pixelspress.com">PixelsPress</a>',
			'homepage'      => 'https://github.com/mohsin-rafique/neovantage-core',
			'requires'      => '5.3',
			'requires_php'  => '8.0',
			'tested'        => '6.9',
			'download_link' => $package,
			'sections'      => array(
				'description' => esc_html__( 'Companion plugin for the NEOVANTAGE WordPress theme. Adds post view counts, custom widgets, and author contact fields.', 'neovantage-core' ),
				'changelog'   => esc_html__( 'See the full changelog at pixelspress.com/free-wordpress-theme/', 'neovantage-core' ),
			),
		);
	}
}
