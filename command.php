<?php

if ( class_exists( 'WP_CLI' ) ) {
	require_once dirname( __FILE__ ) . '/inc/class-command.php';
	WP_CLI::add_command( 'assign-featured-images', 'runcommand\Assign_Featured_Images\Command' );
}
