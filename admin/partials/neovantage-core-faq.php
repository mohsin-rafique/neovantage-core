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

?>
<div class="neovantage-core-content-wrap">
	<div class="neovantage-core-content-header">
		<h2 class="neovantage-core-content-title"><?php esc_html_e( 'FAQ', 'neovantage-core' ); ?></h2>
	</div>

	<div class="neovantage-core-content-body neovantage-core-system-requirements">
		<p><?php esc_html_e( 'These are general frequently asked questions to help you get started. For more in-depth documentation, please visit our online support center to view documentation and knowledgebase tutorials.', 'neovantage-core' ); ?></p>
		<button class="neovantage-core-accordion"><?php esc_html_e( 'How Do I Get Support For NEOVANTAGE?', 'neovantage-core' ); ?></button>
		<div class="panel">
			<p><?php echo wp_kses_post( 'All support is handled through PixelsPress\'s support center. First you create an account on our website which gives you access to our support center. Our support center includes online documentation and a hands on ticket system. Our team of experts will gladly help answer questions you may have. Please see the links below. ', 'neovantage-core' ); ?></p>
		</div>
	</div>

	<div class="neovantage-core-content-footer">
		<h2 class="neovantage-core-content-title"><?php esc_html_e( 'FAQ', 'neovantage-core' ); ?></h2>
	</div>
</div>
