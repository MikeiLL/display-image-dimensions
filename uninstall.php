<?php
/**
 * Runs on Uninstall of Display Image Dimensions in Media Library 
 *
 * @package   Display Image Dimensions in Media Library 
 * @author    Mike iLL/mZoo
 * @license   GPL-2.0+
 * @link      http://mzoo.org
 */
 
if ( ! current_user_can( 'activate_plugins' ) ) {
	return;
}
check_admin_referer( 'bulk-plugins' );

// Check that we should be doing this
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // Exit if accessed directly
}

delete_option( 'mzoo_display_image_dimensions_01' );
?>