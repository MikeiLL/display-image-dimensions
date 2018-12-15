<?php
/**
 * Runs on Uninstall of Display Full Image Dimensions
 *
 * @package   Display Full Image Dimensions
 * @author    Mike iLL/mZoo
 * @license   GPL-2.0+
 * @link      http://mzoo.org
 */

// Check that we should be doing this
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // Exit if accessed directly
}

delete_option( 'mzoo_display_image_dimensions_01' );
?>