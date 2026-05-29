<?php
/**
 * JSON-LD structured data output.
 *
 * Outputs schema.org JSON-LD markup in the document head for pages
 * that benefit from structured data. Bails automatically when a
 * dedicated SEO plugin (Yoast, Rank Math, SEOPress, AIOSEO) is
 * active, since those plugins handle schema themselves.
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes
 * @since      2.2.0
 */

/**
 * Structured data handler.
 *
 * Hooks into wp_head and outputs JSON-LD for author archives.
 * Additional page types can be added by extending the dispatch
 * inside the output() method.
 *
 * @since 2.2.0
 */
class Neovantage_Core_Structured_Data {

	/**
	 * Register the wp_head hook.
	 *
	 * @since 2.2.0
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'wp_head', array( $this, 'output' ), 5 );
	}

	/**
	 * Dispatch structured data by page type.
	 *
	 * @since 2.2.0
	 *
	 * @return void
	 */
	public function output() {
		if ( $this->seo_plugin_active() ) {
			return;
		}

		if ( is_author() ) {
			$this->author_schema();
		}
	}

	/**
	 * Check whether a known SEO plugin is active.
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	private function seo_plugin_active() {
		$active = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

		$seo_plugins = array(
			'wordpress-seo/wp-seo.php',
			'seo-by-rank-math/rank-math.php',
			'wp-seopress/seopress.php',
			'all-in-one-seo-pack/all_in_one_seo_pack.php',
		);

		foreach ( $seo_plugins as $plugin ) {
			if ( in_array( $plugin, $active, true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Output ProfilePage + Person JSON-LD for author archives.
	 *
	 * @since 2.2.0
	 *
	 * @return void
	 */
	private function author_schema() {
		$author = get_queried_object();

		if ( ! $author instanceof WP_User ) {
			return;
		}

		$author_id  = $author->ID;
		$name       = $author->display_name;
		$author_url = get_author_posts_url( $author_id );
		$bio        = get_the_author_meta( 'description', $author_id );
		$avatar_url = get_avatar_url( $author_id, array( 'size' => 256 ) );

		$same_as = $this->collect_social_links( $author_id );

		$person = array(
			'@type' => 'Person',
			'name'  => $name,
			'url'   => $author_url,
		);

		if ( $avatar_url ) {
			$person['image'] = array(
				'@type'  => 'ImageObject',
				'url'    => $avatar_url,
				'width'  => 256,
				'height' => 256,
			);
		}

		if ( $bio ) {
			$person['description'] = wp_strip_all_tags( $bio );
		}

		if ( $same_as ) {
			$person['sameAs'] = $same_as;
		}

		$post_count = count_user_posts( $author_id, 'post', true );

		if ( $post_count > 0 ) {
			$person['interactionStatistic'] = array(
				'@type'                => 'InteractionCounter',
				'interactionType'      => 'https://schema.org/WriteAction',
				'userInteractionCount' => $post_count,
			);
		}

		$registration_date = $author->user_registered;
		$date_created      = $registration_date ? gmdate( 'c', strtotime( $registration_date ) ) : '';

		$latest_post_args = array(
			'author'         => $author_id,
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'orderby'        => 'modified',
			'order'          => 'DESC',
			'fields'         => 'ids',
		);
		$latest_posts     = get_posts( $latest_post_args );
		$date_modified    = $latest_posts ? get_post_modified_time( 'c', true, $latest_posts[0] ) : '';

		$schema = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'ProfilePage',
			'mainEntity' => $person,
		);

		if ( $date_created ) {
			$schema['dateCreated'] = $date_created;
		}

		if ( $date_modified ) {
			$schema['dateModified'] = $date_modified;
		}

		$schema['name'] = sprintf(
			/* translators: %s: Author display name. */
			__( '%s - Author Profile', 'neovantage-core' ),
			$name
		);

		$schema['url'] = $author_url;

		$site_name = get_bloginfo( 'name' );
		if ( $site_name ) {
			$schema['isPartOf'] = array(
				'@type' => 'WebSite',
				'name'  => $site_name,
				'url'   => home_url( '/' ),
			);
		}

		$this->print_json_ld( $schema );
	}

	/**
	 * Collect social profile URLs from user meta.
	 *
	 * Handles both full URLs and plain usernames (prefixed
	 * automatically for platforms that store usernames only).
	 *
	 * @since 2.2.0
	 *
	 * @param int $author_id WordPress user ID.
	 * @return string[] Array of profile URLs.
	 */
	private function collect_social_links( $author_id ) {
		$same_as = array();

		$website = get_the_author_meta( 'user_url', $author_id );
		if ( $website ) {
			$same_as[] = $website;
		}

		$social_keys = array(
			'facebook'        => '',
			'twitter'         => 'https://x.com/',
			'linkedin'        => '',
			'author_dribbble' => '',
			'instagram'       => 'https://www.instagram.com/',
			'github'          => 'https://github.com/',
			'youtube'         => '',
		);

		foreach ( $social_keys as $key => $prefix ) {
			$value = get_the_author_meta( $key, $author_id );
			if ( ! $value ) {
				continue;
			}
			if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
				$same_as[] = $value;
			} elseif ( $prefix ) {
				$same_as[] = $prefix . ltrim( $value, '@/' );
			}
		}

		return $same_as;
	}

	/**
	 * Print a JSON-LD script tag.
	 *
	 * @since 2.2.0
	 *
	 * @param array $schema Associative array representing the schema.
	 * @return void
	 */
	private function print_json_ld( $schema ) {
		$json = wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );

		if ( $json ) {
			echo '<script type="application/ld+json">' . "\n" . $json . "\n" . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}
