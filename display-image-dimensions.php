<?php
/*
 * Plugin Name: Display Image Dimensions in Media Library 
 * Description: Display and sort by dimensions of image in Media Library listing.
 * Author: Mike iLL/mZoo
 * Version: 1.0.2
 * Author URI: http://mzoo.org
 * Text Domain: display-full-image-dimensions
*/
// Add the column
function mzoo_didml_file_dimensions_column( $cols ) {
        $cols["image_dimensions"] = "Dimensions (width x height)";
        return $cols;
}

// Display Column Contents
function mzoo_didml_file_dimensions_contents( $column_dimensions, $id ) {
           echo mzoo_didml_return_dimensions($id) ;
}

// Register the column as sortable & sort by dimensions
function mzoo_didml_file_dimensions_column_sortable( $cols ) {
    $cols["image_dimensions"] = "dimensions";

    return $cols;
}


// Hook actions to admin_init
function mzoo_didml_hook_new_media_columns() {
    add_filter( 'manage_media_columns', 'mzoo_didml_file_dimensions_column' );
    add_action( 'manage_media_custom_column', 'mzoo_didml_file_dimensions_contents', 10, 2 );
    add_filter( 'manage_upload_sortable_columns', 'mzoo_didml_file_dimensions_column_sortable' );    
    add_filter( 'wp_generate_attachment_metadata', 'mzoo_didml_76580_update_image_meta_data', 10, 2);
    add_action( 'pre_get_posts', 'mzoo_didml_76580_size_columns_do_sort' );
}
add_action( 'admin_init', 'mzoo_didml_hook_new_media_columns' );

/* Return array of size data
source: http://justintadlock.com/archives/2011/01/28/linking-to-all-image-sizes-in-wordpress 
borrowed from display-all-image-sizes plugin */
function mzoo_didml_return_dimensions( $id ) {

	$image = wp_get_attachment_image_src( $id, 'full' );

		/* Return dimensions, link, width, height */
		if ( !empty( $image ) ) {
			return $image[1] . 'px x ' . $image[2] . 'px (' . get_post_meta( $id, '_square_pixels', true) . 'px square)';
		} else {
			return 'Error returning image data';
		}

}

/* Make columns sortable by size metadata
 * source: https://wordpress.stackexchange.com/a/54267/48604
 */
 /*
 * Save Image Attachments meta data on save
 *
 */

function mzoo_didml_76580_update_image_meta_data( $image_data, $att_id ) 
{
    $width  = $image_data['width'];
    $height  = $image_data['height'];

    // update_post_meta( $att_id, '_width', $width );
    // update_post_meta( $att_id, '_height', $height );
    update_post_meta( $att_id, '_square_pixels', $width * $height);

    return $image_data;
}


/*
 * Sort the columns
 *
 */

function mzoo_didml_76580_size_columns_do_sort(&$query)
{
    global $current_screen;
    if( 'upload' != $current_screen->id )
        return;

    $is_width = (isset( $_GET['orderby'] ) && 'dimensions' == $_GET['orderby']);

    if( !$is_width )
        return;

    if ( 'dimensions' == sanitize_key($_GET['orderby']) ) 
    {
        $query->set('meta_key', '_square_pixels');
        $query->set('orderby', 'meta_value_num');
    }

}

/** Update all images
 * source of wrapper: https://isabelcastillo.com/run-once-wp
 */
function mzoo_didml_76580_run_only_once_wrapper() {

    if ( get_option( 'mzoo_didml_display_image_dimensions_01' ) != 'completed' ) {
  		mzoo_didml_76580_update_image_meta();
        update_option( 'mzoo_didml_display_image_dimensions_01', 'completed' );
    }
    // delete_option( 'mzoo_didml_display_image_dimensions_01' );
}


/*
 * Update ALL attachments metadata with _square_pixels (Width * Height) field
 *
 * Important: Run Only Once
 * 
 */
function mzoo_didml_76580_update_image_meta()
{   

    global $wpdb;
    $attachments = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_mime_type LIKE '%image%'" );
    foreach( $attachments as $att )
    {
        list( $url, $width, $height ) = wp_get_attachment_image_src( $att->ID, 'full' );
        update_post_meta( $att->ID, '_square_pixels', $height * $width);
    }
}


// Create Custom field and save value.
add_filter('attachment_fields_to_save', 'mzoo_didml_76580_save_media_custom_field', 11, 2 );

function mzoo_didml_76580_save_media_custom_field( $post, $attachment ) {
	$image = wp_get_attachment_image_src( $id, 'full' );
	$width  = $image_data['width'];
    $height  = $image_data['height'];
    update_post_meta( $post['ID'], '_square_pixels', $height * $width );
    return $post;
}

// Activation hook
// source: https://premium.wpmudev.org/blog/activate-deactivate-uninstall-hooks/
register_activation_hook( __FILE__, 'mzoo_didml_76580_plugin_activation' );
function mzoo_didml_76580_plugin_activation() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
	check_admin_referer( "activate-plugin_{$plugin}" );
  	mzoo_didml_76580_run_only_once_wrapper();
}

?>