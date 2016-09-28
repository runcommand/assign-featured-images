<?php

namespace runcommand\Assign_Featured_Images;

use WP_CLI;
use WP_CLI\Utils;

class Command {

	/**
	 * Assign featured images to posts that support thumbnails.
	 *
	 * **WARNING**: This command will irrevocably change your database. Please
	 * make sure to `wp db export --tables=wp_postmeta` before running.
	 *
	 * Default behavior is to randomly assign attachments as featured images
	 * to all posts of post types that support thumbnails.
	 *
	 * WXR import get mangled and only some of the posts in your dev environment
	 * have featured images? Use `--only-missing` to only replace featured
	 * images on posts where the existing value is missing or invalid.
	 *
	 * ## OPTIONS
	 *
	 * [--attachment=<attachment>]
	 * : Assign a specified attachment. Defaults to randomly-selected attachments.
	 *
	 * [--only-missing]
	 * : Only replace featured images where existing value is missing or invalid.
	 *
	 * [--dry-run]
	 * : Test the operation without performing database alterations.
	 */
	public function __invoke( $args, $assoc_args ) {

		$only_missing = Utils\get_flag_value( $assoc_args, 'only-missing' );
		$dry_run = Utils\get_flag_value( $assoc_args, 'dry-run' );

		$post_types = array();
		foreach( get_post_types() as $post_type ) {
			if ( post_type_supports( $post_type, 'thumbnail' ) ) {
				$post_types[] = $post_type;
			}
		}

		$query = new \WP_Query( array(
			'post_type'       => $post_types,
			'post_status'     => 'any',
			'posts_per_page'  => -1,
			'fields'          => 'ids',
			'orderby'         => 'ID',
			'order'           => 'ASC',
		) );
		$post_ids = $query->posts;
		if ( empty( $post_ids ) ) {
			WP_CLI::error( 'No posts found.' );
		}

		$attachment_ids = Utils\get_flag_value( $assoc_args, 'attachment' );
		if ( is_null( $attachment_ids ) || 'random' === $attachment_ids ) {
			$query = new \WP_Query( array(
				'post_type'       => 'attachment',
				'post_status'     => 'any',
				'posts_per_page'  => -1,
				'fields'          => 'ids',
			) );
			$attachment_ids = $query->posts;
		} else if ( $attachment_ids ) {
			$attachment_ids = explode( ',', $attachment_ids );
		}

		if ( empty( $attachment_ids ) ){
			WP_CLI::error( 'No attachments found.' );
		}

		$count = count( $post_ids );
		WP_CLI::log( "Found {$count} posts to inspect for featured images." );

		$modified = 0;
		foreach( $post_ids as $post_id ) {
			$missing = $invalid = false;
			$thumbnail_id = get_post_thumbnail_id( $post_id );
			if ( $thumbnail_id ) {
				$attachment = get_post( $thumbnail_id );
				if ( ! $attachment || 'attachment' !== $attachment->post_type ) {
					$invalid = true;
				}
			} else {
				$missing = true;
			}

			if ( ! $missing && ! $invalid && $only_missing ) {
				WP_CLI::log( "Post {$post_id} has a valid featured image. Skipping replacement." );
				continue;
			}

			shuffle( $attachment_ids );
			$attachment_ids = array_values( $attachment_ids );
			$attachment_id = $attachment_ids[0];
			if ( ! $dry_run ) {
				set_post_thumbnail( $post_id, $attachment_id );
			}
			if ( $invalid ) {
				$message = "Post {$post_id} has an invalid featured image.";
			} else if ( $missing ) {
				$message = "Post {$post_id} is missing a featured image.";
			} else {
				$message = "Post {$post_id} has a valid featured image.";
			}
			WP_CLI::log( "{$message} Replacing with attachment {$attachment_id}." );
			$modified++;
		}
		WP_CLI::success( "Assigned featured images to {$modified} posts." );
	}

}
