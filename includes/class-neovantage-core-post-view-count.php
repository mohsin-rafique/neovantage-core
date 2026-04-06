<?php
/**
 * The file that defines the post views count.
 *
 * A class definition that includes attributes and functions used to count the
 * post views.
 *
 * @link       https://pixelspress.com
 * @since      1.0.6
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes
 */

/**
 * The post view count class.
 *
 * This is used to count the post views of posts and pages.
 *
 * @since      1.0.6
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes
 * @author     PixelsPress <support@pixelspress.com>
 */
class Neovantage_Core_Post_View_Count {
	/**
	 * Contains Better_Post_Views view count meta ID
	 *
	 * @var string
	 */
	public static $meta_id = '_neovantage_post_views_count';

	/**
	 * Initialize the class and set it's properties.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		// Increment post view count on single post page.
		add_action( 'wp_head', array( $this, 'neovantage_count_view' ) );

		if ( is_admin() ) :

			// Adding view count into WP admin columns.
			add_filter( 'manage_post_posts_columns', array( $this, 'neovantage_posts_column_views' ) );

			// Fires in each custom column in the Posts list table.
			add_action( 'manage_post_posts_custom_column', array( $this, 'neovantage_posts_custom_column_views' ) );
			add_filter( 'manage_pages_columns', array( $this, 'neovantage_posts_column_views' ) );

			// Fires in each custom column in the Pages list table.
			add_action( 'manage_pages_custom_column', array( $this, 'neovantage_posts_custom_column_views' ) );

			// Admin sortable columns manager.
			add_filter( 'manage_edit-post_sortable_columns', array( $this, 'neovantage_manage_admin_sortable_columns' ) );
			add_filter( 'manage_edit-page_sortable_columns', array( $this, 'neovantage_manage_admin_sortable_columns' ) );
		endif;
	}

	/**
	 * Count post view on single post page
	 *
	 * @since 1.0.6
	 * @access public
	 * @global object $post
	 * @return void
	 */
	public function neovantage_count_view() {

		global $post;

		// Count only for singulars not published posts.
		if ( wp_is_post_revision( $post ) || ! is_singular() ) {
			return;
		}

		if ( is_home() || is_front_page() ) {
			return;
		}

		if ( ( is_single() && 'post' === get_post_type() ) || is_page() ) {

			if ( ! $post->ID ) {
				return;
			}

			$this->neovantage_set_post_views( $post->ID );
		}
	}

	/**
	 * Count and Save Post Views
	 *
	 * @param int $post_id Contain current post id.
	 */
	public function neovantage_set_post_views( $post_id ) {

		$current_views = get_post_meta( intval( $post_id ), self::$meta_id, true );

		if ( ! $current_views ) {
			$current_views = 1;
		} else {
			$current_views++;
		}

		update_post_meta( $post_id, '_neovantage_post_views_count', $current_views );
	}

	/**
	 * Get Post Views
	 *
	 * @param int $post_id Contain current post id.
	 */
	public function neovantage_get_post_views( $post_id ) {

		$post_views = get_post_meta( intval( $post_id ), self::$meta_id, true );

		if ( ! $post_views ) {
			$post_views = 0;
		}

		return $post_views . ' Views';
	}

	/**
	 * Add Column 'Views' in the Posts list table
	 *
	 * @param array $columns An associative array of column headings.
	 * @return array $columns An associative array of column headings including new.
	 */
	public function neovantage_posts_column_views( $columns ) {

		$columns['post_views'] = __( 'Views', 'neovantage-core' );

		return $columns;
	}

	/**
	 * Show 'Views' Post Column Value
	 *
	 * @param string $column_name The name of the column to display.
	 *
	 * @return void
	 */
	public function neovantage_posts_custom_column_views( $column_name ) {

		if ( 'post_views' === $column_name ) :
			echo esc_attr( $this->neovantage_get_post_views( get_the_ID() ) );
		endif;
	}

	/**
	 * Callback: Manages admin sortable columns
	 *
	 * Filter: manage_edit-post_sortable_columns
	 * Filter: manage_edit-page_sortable_columns
	 *
	 * @param array $sortable_columns An array of sortable columns.
	 *
	 * @return mixed
	 */
	public function neovantage_manage_admin_sortable_columns( $sortable_columns ) {
		$sortable_columns['post_views'] = 'post_views';
		return $sortable_columns;
	}
}
new Neovantage_Core_Post_View_Count();
