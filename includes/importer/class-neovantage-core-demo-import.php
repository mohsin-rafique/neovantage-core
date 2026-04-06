<?php
/**
 * The main import handler.
 *
 * @author      PixelsPress
 * @copyright   (c) Copyright by PixelsPress
 * @link        https://pixelspress.com
 *
 * @package     Neovantage_Core
 * @subpackage  Neovantage_Core/includes
 * @since       2.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Import a demo.
 */
class Neovantage_Core_Demo_Import {

	/**
	 * The demo type.
	 *
	 * @access private
	 * @since 2.0.0
	 * @var string
	 */
	private $demo_type;

	/**
	 * Path to the XML file.
	 *
	 * @access private
	 * @since 2.0.0
	 * @var string
	 */
	private $theme_xml;

	/**
	 * Path to the theme-options file.
	 *
	 * @access private
	 * @since 2.0.0
	 * @var string
	 */
	private $theme_options_file;

	/**
	 * Path to the widgets file.
	 *
	 * @access private
	 * @since 2.0.0
	 * @var string
	 */
	private $widgets_file;

	/**
	 * Path to the Fusion-Slider file.
	 *
	 * @access private
	 * @since 2.0.0
	 * @var string
	 */
	private $nbc_url;

	/**
	 * Whether we should fetch attachments or not.
	 *
	 * @access private
	 * @since 2.0.0
	 * @var bool
	 */
	private $fetch_attachments;

	/**
	 * Whether this is a WooCommerce site or not.
	 *
	 * @access private
	 * @since 2.0.0
	 * @var bool
	 */
	private $shop_demo;

	/**
	 * The sidebars.
	 *
	 * @access private
	 * @since 2.0.0
	 * @var array
	 */
	private $sidebars;

	/**
	 * The Homepage title.
	 *
	 * @access private
	 * @since 2.0.0
	 * @var string
	 */
	private $homepage_title;

	/**
	 * WooCommerce pages.
	 *
	 * @access private
	 * @since 2.0.0
	 * @var array
	 */
	private $woopages;

	/**
	 * Whether Fusion-Slider exists or not.
	 *
	 * @access private
	 * @since 2.0.0
	 * @var bool
	 */
	private $nbc_exists;

	/**
	 * Neovantage_Core_Importer_Data instance.
	 *
	 * @access private
	 * @since 2.0.0
	 * @var object
	 */
	private $importer_files;

	/**
	 * Neovantage_Demo_Content_Tracker instance.
	 *
	 * @access private
	 * @since 2.0.0
	 * @var object
	 */
	private $content_tracker;

	/**
	 * The content-types we'll be importing.
	 *
	 * @access private
	 * @since 2.0.0
	 * @var array
	 */
	private $import_content_types;

	/**
	 * An array of allowed post-types.
	 *
	 * @access private
	 * @since 2.0.0
	 * @var array
	 */
	private $allowed_post_types = array();

	/**
	 * An array of allowed taxonomies.
	 *
	 * @access private
	 * @since 2.0.0
	 * @var array
	 */
	private $allowed_taxonomies = array();

	/**
	 * Whether we want to import everything or not.
	 *
	 * @access private
	 * @since 2.0.0
	 * @var bool
	 */
	private $import_all;

	/**
	 * The class constructor.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function __construct() {

		// Hook importer into admin init.
		add_action( 'wp_ajax_nc_import_demo_data', array( $this, 'import_demo_stage' ) );
	}

	/**
	 * The main importer function.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function import_demo_stage() {

		check_ajax_referer( 'nc_demo_ajax', 'security' );

		if ( current_user_can( 'manage_options' ) ) {

			$import_stages = array();
			if ( isset( $_POST['importStages'] ) ) {
				$import_stages = wp_unslash( $_POST['importStages'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			}

			$this->demo_type = 'classic';
			if ( isset( $_POST['demoType'] ) && '' !== sanitize_text_field( wp_unslash( $_POST['demoType'] ) ) ) {
				$this->demo_type = sanitize_text_field( wp_unslash( $_POST['demoType'] ) );
			}

			$this->fetch_attachments = false;
			if ( isset( $_POST['fetchAttachments'] ) && 'true' === sanitize_text_field( wp_unslash( $_POST['fetchAttachments'] ) ) ) {
				$this->fetch_attachments = true;
			}

			$this->import_content_types = array();
			if ( isset( $_POST['contentTypes'] ) && is_array( $_POST['contentTypes'] ) ) {
				$this->import_content_types = wp_unslash( $_POST['contentTypes'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			}

			$this->import_all = false;
			if ( isset( $_POST['allImport'] ) && 'true' === sanitize_text_field( wp_unslash( $_POST['allImport'] ) ) ) {
				$this->import_all = true;
			}

			// Include the remote file getter.
			if ( ! class_exists( 'Neovantage_Core_Importer_Data' ) ) {
				include_once NC_DIR_PATH . 'includes/importer/class-neovantage-core-importer-data.php';
			}

			if ( ! class_exists( 'Neovantage_Demo_Content_Tracker' ) ) {
				include_once NC_DIR_PATH . 'includes/importer/class-neovantage-core-demo-content-tracker.php';
			}

			$this->importer_files  = new Neovantage_Core_Importer_Data( $this->demo_type );
			$this->content_tracker = new Neovantage_Core_Demo_Content_Tracker( $this->demo_type );

			$this->before_import_stage();

			if ( ! empty( $import_stages[0] ) && method_exists( $this, 'import_' . $import_stages[0] ) ) {

				if ( 'download' !== $import_stages[0] ) {

					$this->theme_xml          = $this->importer_files->get_path( 'neovantage.xml' );
					$this->theme_options_file = $this->importer_files->get_path( 'theme_options.dat' );
					$this->widgets_file       = $this->importer_files->get_path( 'widget_data.json' );
					// $this->nbc_url            = $this->importer_files->get_path( 'nbc.zip' );
					$this->shop_demo = $this->importer_files->is_shop();

					$this->sidebars       = $this->importer_files->get_sidebars();
					$this->homepage_title = $this->importer_files->get_homepage_title();
					$this->woopages       = $this->importer_files->get_woopages();
					$this->nbc_exists     = false;

					if ( 'content' === $import_stages[0] ) {
						$this->before_content_import();

						foreach ( $this->import_content_types as $content_type ) {
							// Note import stage which is currently processed.
							$this->content_tracker->update_import_stage_data( $content_type );
						}
					} else {
						// Note import stage which is currently processed.
						$this->content_tracker->update_import_stage_data( $import_stages[0] );
					}
				}

				// Make import stage backup if needed.
				if ( method_exists( $this->content_tracker, 'set_' . $import_stages[0] ) ) {
					call_user_func( array( $this->content_tracker, 'set_' . $import_stages[0] ) );
				}

				call_user_func( array( $this, 'import_' . $import_stages[0] ) );

				// Menus are imported with the content.
				if ( 'content' === $import_stages[0] ) {

					$this->after_content_import();
				}
			}

			// We've just processed last import stage.
			if ( 1 === count( $import_stages ) ) {

				/**
				 * WIP
				$this->content_tracker->set_general_data();
				*/
				$this->after_import();

				// Reset all caches, don't remove demo data.
				nc_reset_all_caches(
					array(
						'demo_data' => false,
					)
				);
				echo 'imported';
			} else {
				echo 'import partially completed: ' . $import_stages[0]; // phpcs:ignore WordPress.Security.EscapeOutput
			}

			// Save data after import, for example imported terms.
			$this->content_tracker->save_demo_history();

			exit;
		}
	}

	/**
	 * Just some stuff that needs to be set before any import stage is run.
	 *
	 * @access private
	 * @since 2.0.0
	 */
	private function before_import_stage() {

		add_filter( 'intermediate_image_sizes_advanced', 'nc_filter_image_sizes' );

		if ( function_exists( 'ini_get' ) ) {
			if ( 300 < ini_get( 'max_execution_time' ) ) {
				set_time_limit( 300 );
			}
			if ( 512 < intval( ini_get( 'memory_limit' ) ) ) {
				wp_raise_memory_limit();
			}
		}

	}

	/**
	 * Just some stuff that needs to be set after any import stage is run.
	 *
	 * @access private
	 * @since 2.0.0
	 */
	private function after_import() {

		if ( true === $this->import_all ) {
			$this->assign_menus_to_locations();

			$this->content_tracker->update_import_stage_data( 'all' );
		}

	}

	/**
	 * Downloads demo package (zip) file.
	 *
	 * @access private
	 * @since 2.0.0
	 */
	private function import_download() {

		// Get remote files and save locally.
		if ( ! $this->importer_files->remote_files_downloaded() ) {

			$this->importer_files->download_remote_files();
		}
	}

	/**
	 * This is called before 'content' import stages are run.
	 * Mostly used to add hooks which will filter allowed post types and taxonomies from neovantage.xml file.
	 *
	 * Currently 'content' import stages are: posts, pages, images, CPT.
	 *
	 * @access private
	 * @since 2.0.0
	 */
	private function before_content_import() {

		add_filter( 'wxr_importer.pre_process.user', array( $this, 'skip_authors' ), 10, 2 );
		add_action( 'wxr_importer.processed.post', array( $this, 'add_neovantage_demo_import_meta' ), 10, 5 );
		add_filter( 'wxr_importer.pre_process.post', array( $this, 'trim_post_content' ), 10, 4 );

		if ( ! $this->import_all ) {
			if ( ! empty( $this->import_content_types ) ) {
				foreach ( $this->import_content_types as $content_type ) {
					if ( method_exists( $this, 'allow_import_' . $content_type ) ) {
						call_user_func( array( $this, 'allow_import_' . $content_type ) );
					}
				}
			}

			add_filter( 'wxr_importer.pre_process.post', array( $this, 'skip_not_allowed_post_types' ), 10, 4 );
			add_filter( 'wxr_importer.pre_process.term', array( $this, 'skip_not_allowed_taxonomies' ), 10, 2 );
		} else {
			// Slides are imported separately, not from neovantage.xml file.
			add_filter( 'wxr_importer.pre_process.post', array( $this, 'skip_slide_post_type' ), 10, 4 );
			add_filter( 'wxr_importer.pre_process.term', array( $this, 'skip_slide_taxonomy' ), 10, 2 );
		}
	}

	/**
	 * This is called after 'content' import stages are run.
	 *
	 * @access private
	 * @since 2.0.0
	 */
	private function after_content_import() {

		remove_filter( 'wxr_importer.pre_process.user', array( $this, 'skip_authors' ), 10 );
		remove_action( 'wxr_importer.processed.post', array( $this, 'add_neovantage_demo_import_meta' ), 10 );
		remove_filter( 'wxr_importer.pre_process.post', array( $this, 'trim_post_content' ), 10 );

		if ( ! $this->import_all ) {
			remove_filter( 'wxr_importer.pre_process.post', array( $this, 'skip_not_allowed_post_types' ), 10 );
			remove_filter( 'wxr_importer.pre_process.term', array( $this, 'skip_not_allowed_taxonomies' ), 10 );
		} else {
			remove_filter( 'wxr_importer.pre_process.post', array( $this, 'skip_slide_post_type' ), 10 );
			remove_filter( 'wxr_importer.pre_process.term', array( $this, 'skip_slide_taxonomy' ), 10 );
		}
	}

	/**
	 * We don't want to import demo authors.
	 *
	 * @access public
	 * @since 2.0.0
	 * @param array $data User importer data.
	 * @param array $meta User meta.
	 * @return bool
	 */
	public function skip_authors( $data, $meta ) {
		return false;
	}

	/**
	 * Adds import meta to demos.
	 *
	 * @access public
	 * @since 2.0.0
	 * @param int   $post_id  The Post ID.
	 * @param array $data     The Post importer data.
	 * @param array $meta     The Post meta.
	 * @param array $comments The Post comments.
	 * @param array $terms    The Post terms.
	 */
	public function add_neovantage_demo_import_meta( $post_id, $data, $meta, $comments, $terms ) {
		update_post_meta( $post_id, 'neovantage_demo_import', $this->demo_type );
	}

	/**
	 * Allow importing a post.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function allow_import_post() {

		$this->allowed_post_types = array_merge( $this->allowed_post_types, array( 'post' ) );
		$this->allowed_taxonomies = array_merge( $this->allowed_taxonomies, array( 'category', 'post_tag' ) );
	}

	/**
	 * Allow importing a page.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function allow_import_page() {

		$this->allowed_post_types = array_merge( $this->allowed_post_types, array( 'page', 'wpcf7_contact_form' ) );
		$this->allowed_taxonomies = array_merge( $this->allowed_taxonomies, array( 'element_category' ) );
	}

	/**
	 * Allow importing a portfolio.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function allow_import_neovantage_portfolio() {

		$this->allowed_post_types = array_merge( $this->allowed_post_types, array( 'neovantage_portfolio' ) );
		$this->allowed_taxonomies = array_merge( $this->allowed_taxonomies, array( 'portfolio_category', 'portfolio_skills', 'portfolio_tags' ) );
	}

	/**
	 * Allow importing an FAQ.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function allow_import_neovantage_faq() {

		$this->allowed_post_types = array_merge( $this->allowed_post_types, array( 'neovanage_faq' ) );
		$this->allowed_taxonomies = array_merge( $this->allowed_taxonomies, array( 'faq_category' ) );
	}

	/**
	 * Allow importing a product.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function allow_import_product() {

		$this->allowed_post_types = array_merge( $this->allowed_post_types, array( 'product', 'shop_order', 'shop_coupon' ) );
		$this->allowed_taxonomies = array_merge( $this->allowed_taxonomies, array( 'product_cat', 'product_tag', 'product_visibility', 'product_type' ) );
	}

	/**
	 * Allow importing an event.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function allow_import_event() {

		$this->allowed_post_types = array_merge( $this->allowed_post_types, array( 'tribe_events', 'tribe_venue', 'tribe_organizer' ) );
		$this->allowed_taxonomies = array_merge( $this->allowed_taxonomies, array( 'tribe_events_cat' ) );
	}

	/**
	 * Allow importing a forum.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function allow_import_forum() {

		$this->allowed_post_types = array_merge( $this->allowed_post_types, array( 'forum', 'topic', 'reply' ) );
		$this->allowed_taxonomies = array_merge( $this->allowed_taxonomies, array( 'topic-tag' ) );
	}

	/**
	 * Allow importing an attachment.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function allow_import_attachment() {

		$this->allowed_post_types = array_merge( $this->allowed_post_types, array( 'attachment' ) );
	}

	/**
	 * Main content importer method.
	 *
	 * @access private
	 * @since 2.0.0
	 */
	private function import_content() {

		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
			define( 'WP_LOAD_IMPORTERS', true ); // We are loading importers.
		}

		if ( ! class_exists( 'WP_Importer' ) ) { // If main importer class doesn't exist.
			$wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			include $wp_importer;
		}

		if ( ! class_exists( 'WXR_Importer' ) ) { // If WP importer doesn't exist.
			include NC_DIR_PATH . 'includes/importer/lib/class-logger.php';
			include NC_DIR_PATH . 'includes/importer/lib/class-logger-html.php';
			include NC_DIR_PATH . 'includes/importer/lib/class-wxr-importer.php';
		}

		if ( ! class_exists( 'Neovantage_WXR_Importer' ) ) {
			include NC_DIR_PATH . 'includes/importer/lib/class-neovantage-wxr-importer.php';
		}

		// Check for main import class and wp import class.
		if ( class_exists( 'WP_Importer' ) && class_exists( 'WXR_Importer' ) && class_exists( 'Neovantage_WXR_Importer' ) ) {

			$logger = new WP_Importer_Logger_HTML();

			// It's important to disable 'prefill_existing_posts'.
			// In case GUID of importing post matches GUID of an existing post it won't be imported.
			$importer = new Neovantage_WXR_Importer(
				array(
					'fetch_attachments'      => $this->fetch_attachments,
					'prefill_existing_posts' => false,
					'aggressive_url_search'  => true,
				)
			);
			$importer->set_logger( $logger );

			ob_start();
			$importer->import( $this->theme_xml );
			ob_end_clean();

			// Import WooCommerce if WooCommerce Exists.
			if ( class_exists( 'WooCommerce' ) && $this->shop_demo ) {
				foreach ( $this->woopages as $woo_page_name => $woo_page_title ) {
					$woopage = get_page_by_title( $woo_page_title );
					if ( isset( $woopage ) && $woopage->ID ) {
						update_option( $woo_page_name, $woopage->ID ); // Front Page.
					}
				}
				// We no longer need to install pages.
				delete_option( '_wc_needs_pages' );
				delete_transient( '_wc_activation_redirect' );
			}
			// Flush rules after install.
			flush_rewrite_rules();
		}
	}

	/**
	 * Skips post-types that are not allowed.
	 *
	 * @access public
	 * @since 2.0.0
	 * @param array $data     The Post importer data.
	 * @param array $meta     The Post meta.
	 * @param array $comments The Post comments.
	 * @param array $terms    The Post terms.
	 * @return bool|array
	 */
	public function skip_not_allowed_post_types( $data, $meta, $comments, $terms ) {

		if ( ! in_array( $data['post_type'], $this->allowed_post_types ) ) {
			return false;
		}
		return $data;
	}

	/**
	 * Trim post content which seems to be added by WP 5.1+ exporter.
	 *
	 * @access public
	 * @since 2.0.0
	 * @param array $data     The Post importer data.
	 * @param array $meta     The Post meta.
	 * @param array $comments The Post comments.
	 * @param array $terms    The Post terms.
	 * @return bool|array
	 */
	public function trim_post_content( $data, $meta, $comments, $terms ) {
		$data['post_content'] = trim( $data['post_content'] );
		return $data;
	}

	/**
	 * Skip non-allowed taxonomies.
	 *
	 * @access public
	 * @since 2.0.0
	 * @param array $data     The Post importer data.
	 * @param array $meta     The Post meta.
	 * @return bool|array
	 */
	public function skip_not_allowed_taxonomies( $data, $meta ) {

		if ( ! in_array( $data['taxonomy'], $this->allowed_taxonomies ) ) {
			return false;
		}
		return $data;
	}

	/**
	 * Skips 'slide' post type.
	 * This is used to skip importing 'slides' from neovantage.xml file.
	 *
	 * @access public
	 * @since 2.0.0
	 * @param array $data     The Post importer data.
	 * @param array $meta     The Post meta.
	 * @param array $comments The Post comments.
	 * @param array $terms    The Post terms.
	 * @return bool|array
	 */
	public function skip_slide_post_type( $data, $meta, $comments, $terms ) {

		if ( 'slide' === $data['post_type'] ) {
			return false;
		}
		return $data;
	}

	/**
	 * Skip 'slide-page' terms.
	 *
	 * @access public
	 * @since 2.0.0
	 * @param array $data The Post importer data.
	 * @param array $meta The Post meta.
	 * @return bool|array
	 */
	public function skip_slide_taxonomy( $data, $meta ) {

		if ( 'slide-page' === $data['taxonomy'] ) {
			return false;
		}
		return $data;
	}

	/**
	 * Assigns imported menus to correct locations.
	 * Called from 'import_content' method.
	 *
	 * @access private
	 * @since 2.0.0
	 */
	private function assign_menus_to_locations() {

		// Set imported menus to registered theme locations.
		$locations = maybe_unserialize( get_theme_mod( 'nav_menu_locations' ) ); // Registered menu locations in theme.
		$menus     = wp_get_nav_menus(); // Registered menus.

		if ( $menus ) {
			foreach ( $menus as $menu ) { // Assign menus to theme locations.
				if ( ucwords( str_replace( '_', ' ', $this->demo_type ) ) . ' Primary Menu' === $menu->name ) {
					$locations['primary'] = $menu->term_id;
					$locations['mobile']  = $menu->term_id;
				}
			}
		}

		set_theme_mod( 'nav_menu_locations', $locations ); // Set menus to locations.

	}

	/**
	 * Imports Theme Options.
	 *
	 * @access private
	 * @since 2.0.0
	 */
	private function import_theme_options() {

		// Get the upload data.
		$raw  = file_get_contents( $this->theme_options_file );
		$data = @unserialize( $raw );

		if ( isset( $data['template'] ) || isset( $data['mods'] ) ) {
			$theme_options_db_name = 'theme_mods_' . esc_attr( $data['template'] );
			update_option( $theme_options_db_name, $data['mods'] );
		}
	}

	/**
	 * Imports widgets.
	 *
	 * @access private
	 * @since 2.0.0
	 */
	private function import_widgets() {

		// Add data to widgets.
		if ( isset( $this->widgets_file ) && $this->widgets_file ) {
			$widget_data    = file_get_contents( $this->widgets_file ); // Widgets data file.
			$import_widgets = $this->import_widget_data( $widget_data );
		}
	}

	/**
	 * Parsing Widgets Function
	 *
	 * @since 2.0.0
	 * @see http://wordpress.org/plugins/widget-settings-importexport/
	 * @param string $widget_data The widget data, JSON-formatted.
	 */
	public function import_widget_data( $widget_data ) {

		$json_data    = json_decode( $widget_data, true );
		$sidebar_data = $json_data[0];
		$widget_data  = $json_data[1];

		foreach ( $widget_data as $widget_data_title => $widget_data_value ) {
			$widgets[ $widget_data_title ] = array();
			foreach ( $widget_data_value as $widget_data_key => $widget_data_array ) {
				if ( is_int( $widget_data_key ) ) {
					$widgets[ $widget_data_title ][ $widget_data_key ] = 'on';
				}
			}
		}
		unset( $widgets[''] );

		foreach ( $sidebar_data as $title => $sidebar ) {
			$count = count( $sidebar );
			for ( $i = 0; $i < $count; $i++ ) {
				$widget               = array();
				$widget['type']       = trim( substr( $sidebar[ $i ], 0, strrpos( $sidebar[ $i ], '-' ) ) );
				$widget['type-index'] = trim( substr( $sidebar[ $i ], strrpos( $sidebar[ $i ], '-' ) + 1 ) );
				if ( ! isset( $widgets[ $widget['type'] ][ $widget['type-index'] ] ) ) {
					unset( $sidebar_data[ $title ][ $i ] );
				}
			}
			$sidebar_data[ $title ] = array_values( $sidebar_data[ $title ] );
		}

		foreach ( $widgets as $widget_title => $widget_value ) {
			foreach ( $widget_value as $widget_key => $widget_value ) {
				$widgets[ $widget_title ][ $widget_key ] = $widget_data[ $widget_title ][ $widget_key ];
			}
		}

		$sidebar_data = array( array_filter( $sidebar_data ), $widgets );

		self::parse_import_data( $sidebar_data );
	}

	/**
	 * Import data.
	 *
	 * @since 2.0.0
	 * @param array $import_array The array of data to be imported.
	 */
	public static function parse_import_data( $import_array ) {

		global $wp_registered_sidebars;
		$sidebars_data    = $import_array[0];
		$widget_data      = $import_array[1];
		$current_sidebars = get_option( 'sidebars_widgets' );
		$new_widgets      = array();

		foreach ( $sidebars_data as $import_sidebar => $import_widgets ) :

			foreach ( $import_widgets as $import_widget ) :

				// if the sidebar exists.
				if ( array_key_exists( $import_sidebar, $current_sidebars ) ) :

					$title               = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
					$index               = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
					$current_widget_data = get_option( 'widget_' . $title );
					$new_widget_name     = self::get_new_widget_name( $title, $index );
					$new_index           = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );

					if ( ! empty( $new_widgets[ $title ] ) && is_array( $new_widgets[ $title ] ) ) {
						while ( array_key_exists( $new_index, $new_widgets[ $title ] ) ) {
							$new_index++;
						}
					}
					$current_sidebars[ $import_sidebar ][] = $title . '-' . $new_index;
					if ( array_key_exists( $title, $new_widgets ) ) {
						$new_widgets[ $title ][ $new_index ] = $widget_data[ $title ][ $index ];
						$multiwidget                         = $new_widgets[ $title ]['_multiwidget'];
						unset( $new_widgets[ $title ]['_multiwidget'] );
						$new_widgets[ $title ]['_multiwidget'] = $multiwidget;
					} else {
						$current_widget_data[ $new_index ] = $widget_data[ $title ][ $index ];
						$current_multiwidget               = array_key_exists( '_multiwidget', $current_widget_data ) ? $current_widget_data['_multiwidget'] : false;
						$new_multiwidget                   = array_key_exists( '_multiwidget', $widget_data[ $title ] ) ? $widget_data[ $title ]['_multiwidget'] : false;
						$multiwidget                       = ( $current_multiwidget != $new_multiwidget ) ? $current_multiwidget : 1;
						unset( $current_widget_data['_multiwidget'] );
						$current_widget_data['_multiwidget'] = $multiwidget;
						$new_widgets[ $title ]               = $current_widget_data;
					}

				endif;
			endforeach;
		endforeach;

		if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
			update_option( 'sidebars_widgets', $current_sidebars );

			foreach ( $new_widgets as $title => $content ) {
				$content = apply_filters( 'widget_data_import', $content, $title );
				update_option( 'widget_' . $title, $content );
			}

			return true;
		}

		return false;
	}

	/**
	 * Get the new widget name.
	 *
	 * @since 2.0.0
	 * @param string $widget_name  The widget-name.
	 * @param int    $widget_index The index of the widget.
	 * @return array
	 */
	public static function get_new_widget_name( $widget_name, $widget_index ) {
		$current_sidebars = get_option( 'sidebars_widgets' );
		$all_widget_array = array();
		foreach ( $current_sidebars as $sidebar => $widgets ) {
			if ( ! empty( $widgets ) && is_array( $widgets ) && 'wp_inactive_widgets' != $sidebar ) {
				foreach ( $widgets as $widget ) {
					$all_widget_array[] = $widget;
				}
			}
		}
		while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
			$widget_index++;
		}
		$new_widget_name = $widget_name . '-' . $widget_index;
		return $new_widget_name;
	}

	/**
	 * Calls NBC, Rev and Layer sliders import methods.
	 *
	 * @access private
	 * @since 2.0.0
	 */
	private function import_sliders() {
		add_action( 'wxr_importer.processed.post', array( $this, 'add_neovantage_demo_import_meta' ), 10, 5 );
		// $this->import_neo_bootstrap_carousel();
		remove_action( 'wxr_importer.processed.post', array( $this, 'add_neovantage_demo_import_meta' ), 10 );

		// $this->import_layer_sliders();
		// $this->import_revolution_sliders();
	}

	/**
	 * Imports LayerSlider.
	 *
	 * @access private
	 * @since 2.0.0
	 */
	private function import_layer_sliders() {
		global $wpdb;

		// Import Layerslider.
		if ( defined( 'LS_PLUGIN_VERSION' ) && file_exists( WP_PLUGIN_DIR . '/LayerSlider/classes/class.ls.importutil.php' ) && false !== $this->importer_files->get_layerslider() ) {
			// Get importUtil.
			include WP_PLUGIN_DIR . '/LayerSlider/classes/class.ls.importutil.php';

			foreach ( $this->importer_files->get_layerslider() as $layer_file ) {
				// Finally import rev slider data files.
				$filepath = $this->importer_files->get_path( 'layersliders/' . $layer_file );
				$import   = new LS_ImportUtil( $filepath );
			}

			// Get sliders.
			$sliders = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}layerslider WHERE flag_hidden = '0' AND flag_deleted = '0' ORDER BY date_c ASC" );
			$slides  = array();
			if ( ! empty( $sliders ) ) {
				foreach ( $sliders as $key => $item ) {
					$slides[ $item->id ] = $item->name;

					$this->content_tracker->add_layer_slider_to_stack( $item->id );
				}
			}

			if ( $slides ) {
				foreach ( $slides as $key => $val ) {
					$slides_array[ $val ] = $key;
				}
			}
		}

	}

	/**
	 * Imports revsliders.
	 *
	 * @access private
	 * @since 2.0.0
	 */
	private function import_revolution_sliders() {

		// Import Revslider.
		if ( class_exists( 'RevSliderSliderImport' ) && false != $this->importer_files->get_revslider() ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			// If revslider is activated.
			add_action( 'wp_generate_attachment_metadata', array( $this, 'add_rev_slider_demo_import_meta' ), 10, 2 );

			$slider = new RevSliderSliderImport();
			foreach ( $this->importer_files->get_revslider() as $rev_file ) {
				// Finally import rev slider data files.
				$filepath = $this->importer_files->get_path( 'revsliders/' . $rev_file );
				ob_start();
				$result = $slider->import_slider( true, $filepath );
				ob_clean();
				ob_end_clean();

				if ( true === $result['success'] ) {
					$this->content_tracker->add_rev_slider_to_stack( $result['sliderID'] );
				}
			}

			remove_action( 'wp_generate_attachment_metadata', array( $this, 'add_rev_slider_demo_import_meta' ), 10 );
		}
	}

	/**
	 * Add meta data for media imported by Rev Slider importer.
	 *
	 * @access public
	 * @since 5.4.1
	 *
	 * @param mixed $metadata      Metadata for attachment.
	 * @param int   $attachment_id ID of the attachment.
	 */
	public function add_rev_slider_demo_import_meta( $metadata, $attachment_id ) {
		update_post_meta( $attachment_id, 'nbc_demo_import', $this->demo_type );
	}

	/**
	 * Import NEO Bootstrap Carousel.
	 *
	 * @access private
	 * @since 2.0.0
	 */
	private function import_neo_bootstrap_carousel() {

		// NEO Bootstrap Carousel Import.
		if ( true === $this->nbc_exists && class_exists( 'Neo_Bootstrap_Carousel' ) && file_exists( $this->nbc_url ) ) {

			add_action( 'nbc_import_image_attached', array( $this, 'add_nbc_demo_import_meta' ), 10, 2 );
			// $nbc = new Neo_Bootstrap_Carousel();
			// $nbc->import_sliders( $this->nbc_url, $this->demo_type );
			remove_action( 'nbc_import_image_attached', array( $this, 'add_nbc_demo_import_meta' ), 10 );
		}

	}

	/**
	 * Adds meta to NEO Bootstrap Carousel.
	 *
	 * @access public
	 * @since 2.0.0
	 * @param int $attachment_id The attachment-ID.
	 * @param int $post_id       The post-ID.
	 */
	public function add_nbc_demo_import_meta( $attachment_id, $post_id ) {
		update_post_meta( $attachment_id, 'nbc_demo_import', $this->demo_type );
	}

	/**
	 * Sets home page, site title and imports menus.
	 *
	 * @access private
	 * @since 2.0.0
	 */
	private function import_general_data() {

		// Menus are imported with the rest of the content.
		// Set reading options.
		$homepage = get_page_by_title( $this->homepage_title );

		if ( isset( $homepage ) && $homepage->ID ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $homepage->ID ); // Front Page.
		}

		// Import site title.
		$site_title = 'NEOVANTAGE ' . ucwords( str_replace( '_', ' ', $this->demo_type ) );
		update_option( 'blogname', $site_title );

		$this->content_tracker->set( 'general_data', 'imported' );
	}
}
new Neovantage_Core_Demo_Import();
