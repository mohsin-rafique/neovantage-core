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
<div class="neovantage-core-box alert-box <?php echo sanitize_html_class( $info ); ?>">
	<h3><?php echo wp_kses_post( $heading ); ?></h3>
	<p><?php echo wp_kses_post( $message ); ?></p>
</div>
