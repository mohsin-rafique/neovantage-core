<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://pixelspress.com
 * @since      0.1
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/admin/partials
 */

?>
<div class="neovantage-core-content-wrap">
	<div class="neovantage-core-content-header">
		<h2 class="neovantage-core-content-title"><?php esc_html_e( 'Getting Started', 'neovantage-core' ); ?></h2>
	</div>

	<div class="neovantage-core-content-body">
		<h3><?php esc_html_e( 'Thank you for installing our plugin.', 'neovantage-core' ); ?></h3>
		<p><?php esc_html_e( 'NEOVANTAGE has been successfully installed. You can now use it. Just register yourself so that you can receive automatic theme updates. You will also be able to import NEOVANTAGE demos and install all the plugins that are available. If you want to get product support, check out our Support Tab. We hope that the experience is a good one for you and you’re able to create something great.', 'neovantage-core' ); ?></p>

		<hr class="neovantage-core-section-sep">

		<div class="neovantage-core-welcome">
			<div class="neovantage-core-boxes neovantage-core-boxes-3-col">
				<?php
				$i = 1;
				foreach ( $getting_started as $value ) :
					?>
					<div class="neovantage-core-box neovantage-core-box-num-icon">
						<div class="neovantage-core-num-icon-title">
							<div class="neovantage-core-num"><?php echo intval( $i ); ?></div>
							<div class="neovantage-core-icon-title">
								<img src="<?php echo esc_attr( $value['box-image'] ); ?>" alt="<?php echo esc_attr( $value['box-title'] ); ?>" />
								<h2><?php echo esc_attr( $value['box-title'] ); ?></h2>
							</div>
						</div>
						<p><?php echo wp_kses_post( $value['box-description'] ); ?></p>
						<div class="neovantage-core-action-buttons">
							<a href="<?php echo esc_url( $value['box-cta-url'] ); ?>" class="button-primary" target="_blank"><?php echo esc_attr( $value['box-cta-title'] ); ?></a>
						</div>
					</div>
					<?php
					$i++;
				endforeach;
				?>
			</div>
		</div>

		<hr class="neovantage-core-section-sep">
	</div>
</div>

<hr class="neovantage-core-section-sep">

<div class="neovantage-core-content-wrap">
	<div class="neovantage-core-content-footer">
		<h2 class="neovantage-core-content-title"><?php esc_html_e( 'Credits', 'neovantage-core' ); ?></h2>
	</div>

	<div class="neovantage-core-content-body">
		<p><?php echo esc_attr( NC_NAME ) . esc_html__( ' was developed with ❤ by PixelsPress.', 'neovantage-core' ); ?></p>
		<hr class="neovantage-core-section-sep">
		<h3><?php esc_html_e( 'Project Leader', 'neovantage-core' ); ?></h3>
		<hr class="neovantage-core-section-sep">

		<div class="neovantage-core-team">
			<?php
			if ( $credit_leader ) :
				foreach ( $credit_leader as $leader ) :
					?>
					<a href="<?php echo esc_url( $leader['url'] ); ?>" target="_blank" class="neovantage-core-team-member">
						<?php echo wp_kses_post( get_avatar( sanitize_email( $leader['email'] ) ) ); ?>
						<div class="neovantage-core-member-info">
							<h4><?php echo esc_attr( $leader['name'] ); ?></h4>
							<p><?php echo esc_attr( $leader['role'] ); ?></p>
						</div>
					</a>
					<?php
				endforeach;
			endif;
			?>
		</div>
		<hr class="neovantage-core-section-sep">
	</div>
</div>
