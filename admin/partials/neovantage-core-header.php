<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://pixelspress.com
 * @since      1.0.0
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/admin/partials
 */

$nav_array = array(
	array(
		'menu-title'      => __( 'Welcome', 'neovantage-core' ),
		'menu-page-class' => 'neovantage',
		'menu-url'        => add_query_arg( array( 'page' => 'neovantage' ), 'admin.php' ),
		'menu-class'      => 'welcome',
	),
	array(
		'menu-title'      => __( 'Plugins', 'neovantage-core' ),
		'menu-page-class' => 'neovantage-plugins',
		'menu-url'        => add_query_arg( array( 'page' => 'neovantage-plugins' ), 'admin.php' ),
		'menu-class'      => 'plugins',
	),
	array(
		'menu-title'      => __( 'Demos', 'neovantage-core' ),
		'menu-page-class' => 'neovantage-demos',
		'menu-url'        => add_query_arg( array( 'page' => 'neovantage-demos' ), 'admin.php' ),
		'menu-class'      => 'demos',
	),
	array(
		'menu-title'      => __( 'FAQ', 'neovantage-core' ),
		'menu-page-class' => 'neovantage-faq',
		'menu-url'        => add_query_arg( array( 'page' => 'neovantage-faq' ), 'admin.php' ),
		'menu-class'      => 'faq',
	),
	array(
		'menu-title'      => __( 'System Status', 'neovantage-core' ),
		'menu-page-class' => 'neovantage-system-status',
		'menu-url'        => add_query_arg( array( 'page' => 'neovantage-system-status' ), 'admin.php' ),
		'menu-class'      => 'system-status',
	),
);

// Fetch latest theme version from WordPress.org API (cached 12 hours).
$neovantage_wordpress_version_check = get_transient( 'neovantage_core_latest_theme_version' );
if ( false === $neovantage_wordpress_version_check ) {
	$api_response = wp_remote_get(
		'https://api.wordpress.org/themes/info/1.1/?action=theme_information&request[slug]=neovantage&request[fields][version]=1',
		array( 'timeout' => 5 )
	);
	if ( ! is_wp_error( $api_response ) ) {
		$api_data = json_decode( wp_remote_retrieve_body( $api_response ) );
		$neovantage_wordpress_version_check = isset( $api_data->version ) ? sanitize_text_field( $api_data->version ) : NC_VERSION;
	} else {
		$neovantage_wordpress_version_check = NC_VERSION;
	}
	set_transient( 'neovantage_core_latest_theme_version', $neovantage_wordpress_version_check, 12 * HOUR_IN_SECONDS );
}
?>

<!-- Start Header -->
<div class="neovantage-core-header">
	<div class="neovantage-core-logo">
		<img src="<?php echo NC_DIR_URL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>admin/images/neovantage-header-logo.png" alt="<?php echo esc_attr( NC_NAME ); ?>" />
	</div>
	<div class="neovantage-core-title">
		<h1><?php echo esc_attr( NC_NAME ); ?></h1>
		<div class="neovantage-core-version">
			<h4 class="neovantage-core-v-i">
				<?php esc_html_e( 'Installed: ', 'neovantage-core' ); ?>
				<strong><?php printf( esc_html__( 'v%s', 'neovantage-core' ), NC_VERSION ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
			</h4>
			<h4 class="neovantage-core-v-sep">|</h4>
			<h4 class="neovantage-core-v-l">
				<?php esc_html_e( 'Latest: ', 'neovantage-core' ); ?>
				<strong><?php printf( 'v%s', esc_html( $neovantage_wordpress_version_check ) ); ?></strong>
				<?php if ( version_compare( $neovantage_wordpress_version_check, NC_VERSION, '>' ) ) { ?>
					<a href="<?php echo esc_url( admin_url( 'update-core.php' ) ); ?>"><?php esc_html_e( 'Update(s) available!', 'neovantage-core' ); ?></a>
				<?php } ?>
			</h4>
		</div>
	</div>
</div>

<hr class="neovantage-core-section-sep">

<div class="neovantage-core-htabs">
	<?php
	$i = 1;
	if ( $nav_array ) :
		foreach ( $nav_array as $val ) :
			?>
		<a href="<?php echo esc_url( $val['menu-url'] ); ?>" class="nav-tab <?php echo sanitize_html_class( $val['menu-class'] ); ?> <?php echo ( sanitize_html_class( $val['menu-page-class'] ) == $page ) ? 'nav-tab-active' : ''; ?>"><span><?php echo intval( $i ); ?></span><?php echo esc_attr( $val['menu-title'] ); ?></a>
			<?php
			$i++;
		endforeach;
	endif;
	?>
</div>
<hr class="neovantage-core-section-sep">
<!-- End Header -->
