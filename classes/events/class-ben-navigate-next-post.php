<?php

namespace BEN\Block_Editor_Navigator;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BEN_Navigate_Next_Post' ) ) {

	class BEN_Navigate_Next_Post extends Block_Editor_Navigator {

		public function __construct() {
			parent::__construct();
		}

		public function init() {
			add_action( 'wp_loaded', array( $this, 'on_loaded' ) );
		}

		public function on_loaded() {
			add_action( 'wp_ajax_navigate_next_post', array( $this, 'navigate_next_post' ) );
		}

		public function navigate_next_post() {
			global $post;
			$post_copy = $post;

			$current_post_id   = sanitize_text_field( $_REQUEST['current_post_id'] );
			$current_post_type = sanitize_text_field( $_REQUEST['current_post_type'] );
			$is_classic_editor = sanitize_text_field( $_REQUEST['is_classic_editor'] );

			$post      = get_post( $current_post_id );
			$next_post = get_adjacent_post( false, '', false );

			// Return global post to back to default.
			$post = $post_copy;

			if ( is_a( $next_post, 'WP_Post' ) ) {
				if ( $is_classic_editor ) {
					echo json_encode( admin_url( 'post.php?post=' . $next_post->ID . '&action=edit&classic-editor&classic-editor__forget' ) );
				} else {
					echo json_encode( admin_url( 'post.php?post=' . $next_post->ID . '&action=edit&classic-editor__forget' ) );
				}
				exit;
			}

			echo json_encode( $next_post );
			exit;
		}
	}

	$ben = new BEN_Navigate_Next_Post();
	$ben->init();
}
