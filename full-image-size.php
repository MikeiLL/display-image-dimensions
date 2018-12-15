<?php
/*
Plugin dimensions: Display Full Image Size
Description: Displays dimensions of image in Media Library listing.
Author: mZoo
Version: 1.0.0
Author URI: http://mzoo.org
Text Domain: display-full-image-size
*/
// Add the column
function mzoo_filedimensions_column( $cols ) {
        $cols["image_dimensions"] = "Dimensions (width x height)";
        return $cols;
}

// Display Column Contents
function mzoo_filedimensions_contents( $column_dimensions, $id ) {
           echo mzoo_return_dimensions($id) ;
}

// Register the column as sortable & sort by dimensions
function mzoo_filedimensions_column_sortable( $cols ) {
    $cols["image_dimensions"] = "dimensions";

    return $cols;
}


// Hook actions to admin_init
function hook_new_media_columns() {
    add_filter( 'manage_media_columns', 'mzoo_filedimensions_column' );
    add_action( 'manage_media_custom_column', 'mzoo_filedimensions_contents', 10, 2 );
    add_filter( 'manage_upload_sortable_columns', 'mzoo_filedimensions_column_sortable' );    
    add_filter( 'wp_generate_attachment_metadata', 'mzoo_76580_update_image_meta_data', 10, 2);
    add_action( 'pre_get_posts', 'mzoo_76580_size_columns_do_sort' );
}
add_action( 'admin_init', 'hook_new_media_columns' );

/* Return array of size data
source: http://justintadlock.com/archives/2011/01/28/linking-to-all-image-sizes-in-wordpress 
borrowed from display-all-image-sizes plugin */
function mzoo_return_dimensions( $id ) {
    //global $wpdb;
    //$attachments = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_mime_type LIKE '%image%'" );
    //foreach( $attachments as $att )
    //{
    //	echo "<pre>";
    //	print_r(get_post_meta( $att->ID));
    //	echo "</pre>";
    //}

	$image = wp_get_attachment_image_src( $id, 'full' );

		/* Return dimensions, link, width, height */
		if ( !empty( $image ) ) {
		echo "<pre>";
			$attachment_meta = wp_get_attachment_metadata( $id );
			print_r($attachment_meta);
		echo "</pre>";
			// return $image[1];
			return $image[1] . 'px x ' . $image[2] . 'px';
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

function mzoo_76580_update_image_meta_data( $image_data, $att_id ) 
{
    $width  = $image_data['width'];
    $height  = $image_data['height'];

    update_post_meta( $att_id, '_width', $width );
    update_post_meta( $att_id, '_height', $height );
    update_post_meta( $att_id, '_square_pixels', $width * $height);

    return $image_data;
}


/*
 * Sort the columns
 *
 */

function mzoo_76580_size_columns_do_sort(&$query)
{
    global $current_screen;
    if( 'upload' != $current_screen->id )
        return;

    $is_width = (isset( $_GET['orderby'] ) && 'dimensions' == $_GET['orderby']);

    if( !$is_width )
        return;

    if ( 'dimensions' == $_GET['orderby'] ) 
    {
        // $query->set('meta_key', '_width');
        // $query->set('orderby', 'meta_value_num');
    }

}

/** Update all images
 * source of wrapper: https://isabelcastillo.com/run-once-wp
 */
function mzoo_run_only_once_wrapper() {

    if ( get_option( 'mzoo_run_only_once_01' ) != 'completed' ) {
  		add_action('admin_init','mzoo_76580_update_image_meta');
        update_option( 'mzoo_run_only_once_01', 'completed' );
    }
    // delete_option( 'mzoo_run_only_once_01' );
}
add_action( 'admin_init', 'mzoo_run_only_once_wrapper' );

/*
 * Update ALL attachments metada with Width and Height
 *
 * Important: Run Only Once
 * 
 */
function mzoo_76580_update_image_meta()
{   
die("i ran");
    global $wpdb;
    $attachments = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_mime_type LIKE '%image%'" );
    foreach( $attachments as $att )
    {
        list( $url, $width, $height ) = wp_get_attachment_image_src( $att->ID, 'full' );
        //update_post_meta( $att->ID, '_width', $width );
        //update_post_meta( $att->ID, '_height', $height );
        update_post_meta( $att->ID, '_square_pixels', $height * $width);
    }
}

add_filter('attachment_fields_to_edit', 'mzoo_76580_edit_media_custom_field', 11, 2 );
add_filter('attachment_fields_to_save', 'mzoo_76580_save_media_custom_field', 11, 2 );

function mzoo_76580_edit_media_custom_field( $form_fields, $post ) {
    $form_fields['square_pixels'] = array( 'label' => 'Image Square Pixel Value', 'input' => 'text', 'value' => get_post_meta( $post->ID, '_square_pixels', true ) );
    return $form_fields;
}

function mzoo_76580_save_media_custom_field( $post, $attachment ) {
    update_post_meta( $post['ID'], '_square_pixels', $attachment['square_pixels'] );
    return $post;
}
?>