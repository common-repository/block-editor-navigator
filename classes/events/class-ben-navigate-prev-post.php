<?php

namespace BEN\Block_Editor_Navigator;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BEN_Navigate_Prev_Post' ) ) {

	class BEN_Navigate_Prev_Post extends Block_Editor_Navigator {

		public function __construct() {
			parent::__construct();
		}

		public function init() {
			add_action( 'wp_loaded', array( $this, 'on_loaded' ) );
		}

		public function on_loaded() {
			add_action( 'wp_ajax_navigate_prev_post', array( $this, 'navigate_prev_post' ) );
		}

		public function navigate_prev_post() {
			global $post;

			$post_copy = $post;

			$current_post_id   = sanitize_text_field( $_REQUEST['current_post_id'] );
			$current_post_type = sanitize_text_field( $_REQUEST['current_post_type'] );
			$is_classic_editor = sanitize_text_field( $_REQUEST['is_classic_editor'] );

			$post          = get_post( $current_post_id );
			$previous_post = get_adjacent_post( false, '', true );

			// Return global post to back to default.
			$post = $post_copy;

			if ( is_a( $previous_post, 'WP_Post' ) ) {
				if ( $is_classic_editor ) {
					echo json_encode( admin_url( 'post.php?post=' . $previous_post->ID . '&action=edit&classic-editor&classic-editor__forget' ) );
				} else {
					echo json_encode( admin_url( 'post.php?post=' . $previous_post->ID . '&action=edit&classic-editor__forget' ) );
				}
				exit;
			}

			echo json_encode( $previous_post );
			exit;
		}
	}

	$ben = new BEN_Navigate_Prev_Post();
	$ben->init();
}
