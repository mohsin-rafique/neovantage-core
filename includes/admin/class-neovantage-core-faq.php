<?php
/**
 * Neovantage_Core_Faq Class
 *
 * This is used to define NEOVANTAGE FAQ Page.
 *
 * @author      PixelsPress <contact@pixelspress.com>
 * @copyright   (c) Copyright by PixelsPress
 * @link        https://pixelspress.com
 *
 * @package     Neovantage_Core
 * @subpackage  Neovantage_Core/includes/admin
 * @since       2.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Admin FAQ Page.
 */
class Neovantage_Core_Faq {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   2.0.0
	 */
	public function __construct() {

		// Hook - Add FAQ Menu.
		add_action( 'admin_menu', array( $this, 'nc_admin_menu' ), 40 );
	}

	/**
	 * Add FAQ Page Under Neovantage Core Menu.
	 *
	 * @since   2.0.0
	 */
	public function nc_admin_menu() {

		add_submenu_page(
			'neovantage',
			__( 'FAQ', 'neovantage-core' ),
			__( 'FAQ', 'neovantage-core' ),
			'manage_options',
			'neovantage-faq',
			[ $this, 'nc_faq_screen' ]
		);
	}

	/**
	 * FAQ Admin View.
	 *
	 * @Since   2.0.0
	 */
	public function nc_faq_screen() {

		$page = filter_input( INPUT_GET, 'page' );
		?>
		<div class="neovantage-core">
			<?php require_once NC_DIR_PATH . 'admin/partials/neovantage-core-header.php'; ?>

			<?php require_once NC_DIR_PATH . 'admin/partials/neovantage-core-faq.php'; ?>
		</div>
		<?php
	}

}
new Neovantage_Core_Faq();
