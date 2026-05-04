<?php
/**
 * Asset manifest for the NEOVANTAGE Button block editor script.
 *
 * Returned to WordPress by `register_block_type()` to declare which core
 * script handles `editor.js` depends on. Hand-authored because this block
 * ships build-free (no @wordpress/scripts pipeline).
 *
 * Bump the `version` value when `editor.js` changes so browsers bypass cache.
 *
 * @package Neovantage_Core
 */

return array(
	'dependencies' => array(
		'wp-blocks',
		'wp-block-editor',
		'wp-components',
		'wp-element',
		'wp-i18n',
		'wp-server-side-render',
	),
	'version'      => '1.0.0',
);
