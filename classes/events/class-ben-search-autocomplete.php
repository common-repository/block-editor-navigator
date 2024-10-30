<?php

namespace BEN\Block_Editor_Navigator;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BEN_Search_Autocomplete' ) ) {

	class BEN_Search_Autocomplete extends Block_Editor_Navigator {

		public function __construct() {
			parent::__construct();
		}

		public function init() {
			add_action( 'wp_loaded', array( $this, 'on_loaded' ) );
		}

		public function on_loaded() {
			add_action( 'wp_ajax_navigation_search', array( $this, 'navigation_search' ) );
		}

		public function navigation_search() {
			$search_input_text = sanitize_text_field( $_REQUEST['search_input_text'] );
			$current_post_type = sanitize_text_field( $_REQUEST['current_post_type'] );
			$is_classic_editor = sanitize_text_field( $_REQUEST['is_classic_editor'] );

			$search_results = array();

			// Set minimum 3 characters before we start the search
			if ( strlen( $search_input_text ) < 3 ) {
				echo json_encode(
					array(
						array(
							'guid'  => 0,
							'title' => 'No results found!',
						),
					),
				);
				exit;
			}

			$query = new \WP_Query(
				array(
					'posts_per_page' => -1,
					'post_type'      => $current_post_type,
					's'              => $search_input_text,
					'orderby'        => 'title menu_order',
					'order'          => 'ASC',
				)
			);

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();

					if ( $is_classic_editor ) {
						$search_results[] = array(
							'guid'  => admin_url( 'post.php?post=' . get_the_ID() . '&action=edit&classic-editor&classic-editor__forget' ),
							'title' => get_the_title(),
						);
					} else {
						$search_results[] = array(
							'guid'  => admin_url( 'post.php?post=' . get_the_ID() . '&action=edit&classic-editor__forget' ),
							'title' => get_the_title(),
						);
					}
				}

				echo json_encode( $search_results );
				exit;
			}

			echo json_encode(
				array(
					array(
						'guid'  => 0,
						'title' => 'No results found!',
					),
				),
			);
			exit;
		}
	}

	$ben = new BEN_Search_Autocomplete();
	$ben->init();
}
