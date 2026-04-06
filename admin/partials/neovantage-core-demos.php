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
<script type="text/javascript">
    var DemoImportNonce = '<?php echo esc_attr( wp_create_nonce( 'nc_demo_ajax' ) ); ?>';
</script>
<div class="neovantage-core-content-wrap">
	<div class="neovantage-core-content-body">
		<div class="neovantage-core-boxes neovantage-core-boxes-3-col">
		<?php foreach ( $demos as $demo => $demo_details ) : ?>

			<?php
            // Make sure we don't show demos that can't be applied to this version.
            if ( isset( $demo_details['minVersion'] ) ) {
                $min_version = Neovantage_Core_Helper::normalize_version( $demo_details['minVersion'] );
                if ( version_compare( $theme_version, $min_version ) < 0 ) {
                    continue;
                }
            }
            
            // Set tags (WIP).
            if ( ! isset( $demo_details['tags'] ) ) {
                $demo_details['tags'] = [];
            } else {
                $tags = array_keys( $demo_details['tags'] );
            }

            if ( empty( $demo_details['plugin_dependencies'] ) ) {
                $demo_details['plugin_dependencies'] = [];
            }

            $demo_details['plugin_dependencies']['neovantage-core'] = true;
            $demo_imported = false;
            
            // Generate Import / Remove forms.
            $import_form  = '<form id="import-' . esc_attr( strtolower( $demo ) ) . '" data-demo-id=' . esc_attr( strtolower( $demo ) ) . '>';
            $import_form .= '<p><input type="checkbox" value="all" id="import-all-' . esc_attr( strtolower( $demo ) ) . '"/> <label for="import-all-' . esc_attr( strtolower( $demo ) ) . '">' . esc_html__( 'All', 'neovantage-core' ) . '</label></p>';
            $remove_form  = '<form id="remove-' . esc_attr( strtolower( $demo ) ) . '" data-demo-id=' . esc_attr( strtolower( $demo ) ) . '>';

            foreach ( $import_stages as $import_stage ) {

                $import_checked  = '';
                $remove_disabled = 'disabled';
                $data            = '';
                
                if ( ! empty( $import_stage['plugin_dependency'] ) && empty( $demo_details['plugin_dependencies'][ $import_stage['plugin_dependency'] ] ) ) {
                    continue;
                }

                if ( ! empty( $import_stage['feature_dependency'] ) && ! in_array( $import_stage['feature_dependency'], $demo_details['features'] ) ) {
                    continue;
                }
                
                if ( ! empty( $imported_data[ $import_stage['value'] ] ) ) {
                    //echo $import_stage['value'].'<br />'.$imported_data[ $import_stage['value'] ].'<br />';
                    //wp_die();
                    if ( in_array( strtolower( $demo ), $imported_data[ $import_stage['value'] ] ) ) {
                        $import_checked  = 'checked="checked" disabled';
                        $remove_disabled = 'checked="checked"';
                        $demo_imported   = true;
                    }
                }
                if ( ! empty( $import_stage['data'] ) ) {
                    $data = 'data-type="' . esc_attr( $import_stage['data'] ) . '"';
                }
                $import_form .= '<p><input type="checkbox" value="' . esc_attr( $import_stage['value'] ) . '" ' . $import_checked . ' ' . $data . ' id="import-' . esc_attr( $import_stage['value'] ) . '-' . esc_attr( strtolower( $demo ) ) . '" /> <label for="import-' . esc_attr( $import_stage['value'] ) . '-' . esc_attr( strtolower( $demo ) ) . '">' . $import_stage['label'] . '</label></p>';
                $remove_form .= '<p><input type="checkbox" value="' . esc_attr( $import_stage['value'] ) . '" ' . $remove_disabled . ' ' . $data . ' id="remove-' . esc_attr( $import_stage['value'] ) . '-' . esc_attr( strtolower( $demo ) ) . '" /> <label for="remove-' . esc_attr( $import_stage['value'] ) . '-' . esc_attr( strtolower( $demo ) ) . '">' . $import_stage['label'] . '</label></p>';
            }
            $import_form .= '</form>';
            $remove_form .= '</form>';

            $install_button_label = ! $demo_imported ? esc_html__( 'Import', 'neovantage-core' ) : esc_html__( 'Modify', 'neovantage-core' );

            if ( ! empty( $imported_data['all'] ) && in_array( strtolower( $demo ), $imported_data['all'] ) ) {
                $demo_import_badge = __( 'Full Import', 'neovantage-core' );
            } else {
                $demo_import_badge = __( 'Partial Import', 'neovantage-core' );
            }

            $new_imported = '';
            ?>
			<div class="neovantage-core-box neovantage-core-box-w-img">
				<div class="neovantage-core-box-header">
					<h3><?php echo esc_html( ucwords( str_replace( '_', ' ', $demo ) ) ); ?></h3>
				</div>
                
                <img src="<?php echo esc_url( $demo_details['preview-image'] ); ?>" <?php echo ( ! empty( $demo_details['preview-image'] ) ) ? 'data-src="' . esc_url_raw( $demo_details['preview-image'] ) . '"' : ''; ?> <?php echo ( ! empty( $demo_details['preview-image-retina'] ) ) ? 'data-src-retina="' . esc_url_raw( $demo_details['preview-image-retina'] ) . '"' : ''; ?>>
                <noscript>
                    <img src="<?php echo esc_url_raw( $demo_details['preview-image'] ); ?>" width="325" height="244"/>
                </noscript>

				<div class="neovantage-core-box-body">
					<div class="neovantage-core-action-buttons dib">
						<a href="javascript:void(0);" class="button button-primary button-install-open-modal" data-demo-id="<?php echo esc_attr( strtolower( $demo ) ); ?>"><?php esc_html_e( 'Import', 'neovantage-core' ); ?></a>
                        <?php $preview_url = 'https://neovantage.pixelspress.com/' . str_replace( '_', '-', $demo ); ?>
                        <a class="button button-primary" target="_blank" href="<?php echo esc_url( $preview_url ); ?>"><?php esc_html_e( 'Preview', 'neovantage-core' ); ?></a>
					</div>
				</div>
			</div>
            <div id="demo-modal-<?php echo esc_attr( strtolower( $demo ) ); ?>" class="demo-update-modal-wrap" style="display:none;">
                <div class="demo-update-modal-inner">
                    <div class="demo-modal-thumbnail" style="background-image:url(<?php echo esc_attr( $demo_details['preview-image'] ); ?>);">
                        <a class="demo-modal-preview" target="_blank" href="<?php echo esc_url( $preview_url ); ?>"><?php esc_html_e( 'Live Preview', 'neovantage-core' ); ?></a>
                    </div>

                    <div class="demo-update-modal-content">
                        <?php if ( in_array( true, $demo_details['plugin_dependencies'] ) ) : ?>
                            <div class="demo-required-plugins">
                                <h3><?php esc_html_e( 'The following plugins are required to import content.', 'neovantage-core' ); ?></h3>
                                <ul class="required-plugins-list">
                                    <?php foreach ( $demo_details['plugin_dependencies'] as $slug => $required ) : ?>
                                        <?php if ( true === $required ) : ?>
                                            <li>
                                                <span class="required-plugin-name">
                                                    <?php echo isset( $plugin_dependencies[ $slug ] ) ? esc_html( $plugin_dependencies[ $slug ]['name'] ) : esc_html( $slug ); ?>
                                                </span>
                                                <?php
                                                $label  = esc_html__( 'Install', 'neovantage-core' );
                                                $status = 'install'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
                                                if ( isset( $plugin_dependencies[ $slug ] ) && $plugin_dependencies[ $slug ]['active'] ) {
                                                    $label  = esc_html__( 'Active', 'neovantage-core' );
                                                    $status = 'active'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
                                                } elseif ( isset( $plugin_dependencies[ $slug ] ) && $plugin_dependencies[ $slug ]['installed'] ) {
                                                    $label  = esc_html__( 'Activate', 'neovantage-core' );
                                                    $status = 'activate'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
                                                }
                                                ?>
                                                <span class="required-plugin-status <?php echo esc_attr( $status ); ?> ">
                                                    <?php if ( 'activate' === $status ) : ?>
                                                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=neovantage-plugins' ) ); ?>"
                                                            data-nonce="<?php echo esc_attr( wp_create_nonce( 'nc-activate' ) ); ?>"
                                                            data-plugin="<?php echo esc_attr( $slug ); ?>"
                                                            data-plugin_name="<?php echo esc_attr( $plugin_dependencies[ $slug ]['name'] ); ?>"
                                                        >
                                                    <?php elseif ( 'install' === $status ) : ?>
                                                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=neovantage-plugins' ) ); ?>"
                                                            data-nonce="<?php echo esc_attr( wp_create_nonce( 'nc-activate' ) ); ?>"
                                                            data-plugin="<?php echo esc_attr( $slug ); ?>"
                                                            data-plugin_name="<?php echo esc_attr( $plugin_dependencies[ $slug ]['name'] ); ?>"
                                                            data-tgmpa_nonce="<?php echo esc_attr( wp_create_nonce( 'tgmpa-install' ) ); ?>"
                                                        >
                                                    <?php endif; ?>
                                                        <?php echo esc_html( $label ); ?>
                                                    <?php if ( 'active' !== $status ) : ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </span>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="demo-update-form-wrap">
                            <div class="demo-import-form">
                                <h4 class="demo-form-title"><?php esc_html_e( 'Import Content', 'neovantage-core' ); ?> <span><?php esc_html_e( '(menus only import with "All")', 'neovantage-core' ); ?></span></h4>
                                <?php echo $import_form; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                            </div>

                            <div class="demo-remove-form">
                                <h4 class="demo-form-title"><?php esc_html_e( 'Remove Content', 'neovantage-core' ); ?></h4>
                                <p><input type="checkbox" value="uninstall" id="uninstall-<?php echo esc_attr( strtolower( $demo ) ); ?>" /> <label for="uninstall-<?php echo esc_attr( strtolower( $demo ) ); ?>"><?php esc_html_e( 'Remove', 'neovantage-core' ); ?></label></p>
                                <?php echo $remove_form; // phpcs:ignore WordPress.Security.EscapeOutput; ?>
                            </div>
                        </div>
                    </div>

                    <div class="demo-update-modal-status-bar">
                        <div class="demo-update-modal-status-bar-label"><span></span></div>
                        <div class="demo-update-modal-status-bar-progress-bar"></div>

                        <a href="javascript:void(0);" class="button-install-demo" data-demo-id="<?php echo esc_attr( strtolower( $demo ) ); ?>"><?php esc_html_e( 'Import', 'neovantage-core' ); ?></a>
                        <a href="javascript:void(0);" class="button-uninstall-demo" data-demo-id="<?php echo esc_attr( strtolower( $demo ) ); ?>"><?php esc_html_e( 'Remove', 'neovantage-core' ); ?></a>
                        <a href="javascript:void(0);" class="button-done-demo demo-update-modal-close"><?php esc_html_e( 'Done', 'neovantage-core' ); ?></a>
                    </div>
                </div>

                <a href="#" class="demo-update-modal-corner-close demo-update-modal-close"><span class="dashicons dashicons-no-alt"></span></a>
            </div> <!-- .demo-update-modal-wrap -->
            <?php endforeach; ?>
		</div>
		<div id="dialog-plugin-confirm" title="<?php esc_attr_e( 'Error ', 'neovantage-core' ); ?>"></div>
	</div>
</div>
<div class="demo-import-overlay preview-all" style="display: none;"></div>
<div id="dialog-demo-confirm" title="<?php esc_attr_e( 'Warning ', 'neovantage-core' ); ?>"></div>