<?php
/*
Plugin Name: Display Full Image Dimensions
Description: Display and sort by dimensions of image in Media Library listing.
Author: Mike iLL/mZoo
Version: 1.0.0
Author URI: http://mzoo.org
Text Domain: display-full-image-dimensions
*/

// Check that we should be doing this
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // Exit if accessed directly
}

delete_option( 'mzoo_display_image_dimensions_01' );
?>