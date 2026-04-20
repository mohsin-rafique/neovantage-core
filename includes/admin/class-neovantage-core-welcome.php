<?php
/**
 * Neovantage_Core_Welcome Class
 *
 * This is used to define NEOVANTAGE Welcome Page.
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
 * Admin Welcome Page.
 */
class Neovantage_Core_Welcome {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {

		// Hook - Add Welcome Menu.
		add_action( 'admin_menu', [ $this, 'nc_admin_menu' ], 10 );
	}

	/**
	 * Add Welcome Page under NEOVANTAGE Admin Menu.
	 *
	 * @since   2.0.0
	 */
	public function nc_admin_menu() {
		if ( current_user_can( 'switch_themes' ) ) {
			add_menu_page(
				'NEOVANTAGE',
				'NEOVANTAGE',
				'switch_themes',
				'neovantage',
				[ $this, 'nc_welcome_screen' ],
				'dashicons-neovantage-logo',
				2
			);
			add_submenu_page(
				'neovantage',
				esc_html__( 'Welcome', 'neovantage-core' ),
				esc_html__( 'Welcome', 'neovantage-core' ),
				'manage_options',
				'neovantage'
			);
		}
	}

	/**
	 * Welcome Admin View.
	 *
	 * @Since   2.0.0
	 */
	public function nc_welcome_screen() {
		$page = filter_input( INPUT_GET, 'page' );

		$getting_started = array(
			array(
				'box-title'       => esc_html__( 'Starter Guide', 'neovantage-core' ),
				'box-image'       => NC_DIR_URL . 'admin/images/starter-guide.png',
				'box-description' => esc_html__( 'It’s hard to start working on WordPress if you are not used to it. That’s why we have created a starter pack containing everything you need to know.', 'neovantage-core' ),
				'box-cta-url'     => 'https://pixelspress.com/articles/starter-guide/',
				'box-cta-title'   => esc_html__( 'Starter Guide', 'neovantage-core' ),
			),
			array(
				'box-title'       => esc_html__( 'Documentation', 'neovantage-core' ),
				'box-image'       => NC_DIR_URL . 'admin/images/documentation.png',
				'box-description' => esc_html__( 'You can also explore the different aspects of the theme here. The online documentation provides an easy way to learn all the basics of NEOVANTAGE.', 'neovantage-core' ),
				'box-cta-url'     => 'https://pixelspress.com/support/',
				'box-cta-title'   => esc_html__( 'Documentation', 'neovantage-core' ),
			),
			array(
				'box-title'       => esc_html__( 'Submit A Ticket', 'neovantage-core' ),
				'box-image'       => NC_DIR_URL . 'admin/images/submit-a-ticket.png',
				'box-description' => esc_html__( 'We have an advance ticket system that allows the users to access all the support services. You just have to ensure that you are registered with it.', 'neovantage-core' ),
				'box-cta-url'     => 'https://pixelspress.com/support/submit-a-ticket/',
				'box-cta-title'   => esc_html__( 'Submit A Ticket', 'neovantage-core' ),
			),
		);

		$credit_leader = array(
			'0' => array(
				'name'  => esc_html__( 'Mohsin Rafique', 'neovantage-core' ),
				'role'  => 'Backend Engineer',
				'email' => 'mohsin.rafique@gmail.com',
				'url'   => 'https://profiles.wordpress.org/mohsinrafique',
			),
		);
		?>
		<div class="neovantage-core">
			<?php require_once NC_DIR_PATH . 'admin/partials/neovantage-core-header.php'; ?>

			<?php require_once NC_DIR_PATH . 'admin/partials/neovantage-core-welcome.php'; ?>

			<?php // require_once NC_DIR_PATH . 'admin/partials/neovantage-core-rating-box.php'; ?>
		</div>
		<?php
	}

}

new Neovantage_Core_Welcome();
