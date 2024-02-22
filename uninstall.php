<?php
/**
 * Uninstall TerminEK Plugin
 *
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

//delete table for keeping track of imports


/*	$gsd_posts = get_posts( array(
		'post_type'     => array(
			'mitgliederprofil', 'veranstaltung'
		),
		'numberposts'   => -1
	) );

	foreach ( $gsd_posts as $gsd_post )
		wp_delete_post( $gsd_post->ID, 1 );
*/
