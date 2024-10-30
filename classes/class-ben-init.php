<?php

namespace BEN\Block_Editor_Navigator;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BEN_Init' ) ) {

	class BEN_Init extends Block_Editor_Navigator {

		public function __construct() {
			parent::__construct();
			$this->opt = new BEN_Options;
		}

		public function init() {
			add_action( 'activated_plugin', array( $this, 'activate_plugin' ) );
			add_action( 'deactivated_plugin', array( $this, 'deactivate_plugin' ) );
			add_action( 'wp_loaded', array( $this, 'on_loaded' ) );
		}

		public function on_loaded() {
			// Rating notices
			add_action( 'admin_notices', array( $this, 'rating_notice_display' ) );
			add_action( 'admin_init', array( $this, 'rating_notice_dismiss' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'localize_plugin_urls' ) );
			add_action( 'admin_init', array( $this, 'add_plugin_links' ) );
			add_action( 'admin_init', array( $this->opt, 'add_plugin_options' ) );
			add_filter( 'add_meta_boxes', array( $this, 'add_classic_editor_support' ) );
		}

		public function activate_plugin( $plugin ) {
			if ( $plugin === $this->settings['plugin_basename'] ) {
				$this->activate_block_editor_navigator();
			}
		}

		public function deactivate_plugin( $plugin ) {
			if ( $plugin === $this->settings['plugin_basename'] ) {
				$this->deactivate_block_editor_navigator();
			}
		}

		public function enqueue_admin_scripts() {
			if ( true === $this->settings['dev_mode'] ) {
				wp_register_script(
					'block-editor-navigator',
					$this->settings['plugin_url'] . 'assets/js/block-editor-navigator-init.js',
					array(
						'jquery',
						'wp-plugins',
						'wp-edit-post',
						'wp-element',
						'wp-data',
					),
					'1.0',
					true
				);
				wp_register_style(
					'block-editor-navigator',
					$this->settings['plugin_url'] . 'assets/css/block-editor-navigator.css',
					array(),
					'1.0',
					'all'
				);
			} else {
				wp_register_script(
					'block-editor-navigator',
					$this->settings['plugin_url'] . 'assets/build/js/block-editor-navigator.min.js',
					array(
						'jquery',
						'wp-plugins',
						'wp-edit-post',
						'wp-element',
						'wp-data',
					),
					'1.0',
					true
				);
				wp_register_style(
					'block-editor-navigator',
					$this->settings['plugin_url'] . 'assets/build/css/block-editor-navigator.min.css',
					array(),
					'1.0',
					'all'
				);
			}
			wp_enqueue_script( 'block-editor-navigator' );
			wp_enqueue_style( 'block-editor-navigator' );
		}

		public function localize_plugin_urls() {
			wp_localize_script(
				'block-editor-navigator',
				'ben',
				array(
					'plugin_url' => $this->settings['plugin_url'],
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
				)
			);
		}

		public function add_plugin_links() {
			add_action( 'plugin_action_links', array( $this, 'add_action_links' ), 10, 2 );
			add_action( 'plugin_row_meta', array( $this, 'add_meta_links' ), 10, 2 );
		}

		public function add_action_links( $links, $file_path ) {
			if ( $file_path === $this->settings['plugin_basename'] ) {
				$links['settings'] = '<a href="' . esc_url( admin_url( 'options-writing.php#block-editor-navigator' ) ) . '">Settings</a>';
				return array_reverse( $links );
			}
			return $links;
		}

		public function add_meta_links( $links, $file_path ) {
			if ( $file_path === $this->settings['plugin_basename'] ) {
				$links['docmentation'] = '<a href="' . esc_url( $this->settings['plugin_docurl'] ) . '" target="_blank">Documentation</a>';
			}
			return $links;
		}

		function add_classic_editor_support() {
			global $post;

			$post_types      = get_option( 'ben_post_types' );
			$editor_support  = get_option( 'ben_editor_support' );
			$enabled_screens = array();

			// Don't want to add the meta box for Gutenberg, if both Block and Classic editors are enabled.
			// We have duplicate meta boxes it is already loaded via assets/js/block-editor-navigator.mjs
			if ( ! isset( $_GET['classic-editor'] )
				&& isset( $_GET['classic-editor__forget'] ) ) {
				return false;
			}

			if ( ! $this->is_classic_editor_active() ) {
				return false;
			}

			if ( ! is_array( $post_types ) ) {
				$post_types = array();
			}

			if ( ! is_array( $editor_support ) ) {
				$editor_support = array();
			}

			// Create an array with enabled screens to show the Navigato Controls.
			// Available for Posts, Pages, WooCommerce Products & Custom Post Types.
			foreach ( $post_types as $post_type => $enabled ) {
				array_push( $enabled_screens, $post_type );
			}

			if ( key_exists( 'classic', $editor_support )
				&& true === $editor_support['classic']
				&& key_exists( $post->post_type, $post_types )
				&& true === $post_types[ $post->post_type ] ) {

				add_meta_box(
					'classic_navigator_metabox',
					'Navigator Controls',
					function() {
						global $post;
						?>
						<div class="ben">
							<div class="ben-block-editor-ui">
								<p>Block editor navigator will take you to your desired Post/Page without the need to navigate back to the main page.</p>
								<form name="ben-block-editor" id="ben-block-editor" type="post">
									<input type="hidden" name="ben-current-post-type" id="ben-current-post-type" value="<?php echo $post->post_type; ?>">
									<input type="hidden" name="ben-current-post-id" id="ben-current-post-id" value="<?php echo $post->ID; ?>">
									<input type="hidden" name="ben-classic-editor" id="ben-classic-editor" value="1">
									<div class="button-group">
										<p>
											<button class="button button-primary ben-prev-post" title="Previous Post...">
												<i class="dashicons dashicons-arrow-left-alt2"></i> Previous
											</button>
											<button class="button button-primary ben-next-post" title="Next Post...">
												Next <i class="dashicons dashicons-arrow-right-alt2"></i>
											</button>
											<button class="button button-primary ben-open-search-box">
												<i class="dashicons dashicons-search"></i> Search
											</button>
										</p>
									</div>
									<div class="search-group d-none">
										<input type="text" name="ben-search-autocomplete" id="ben-search-autocomplete" class="ben-search-autocomplete" value="" placeholder="Start typing to see the results..." />
										<ul class="search-results d-none">
										</ul>
									</div>
								</form>
							</div>
						</div>
						<?php
					},
					$enabled_screens,
					'side',
					'high'
				);
			}
		}

		// Add temporary plugin options.
		public function activate_block_editor_navigator() {
			// Activate plugin for the first time add default permanent options.
			if ( get_option( 'block_editor_navigator' ) === false ) {
				add_option( 'block_editor_navigator', 1 );
				add_option(
					'ben_editor_support',
					array(
						'gutenberg' => 'true',
						'classic'   => 'true',
					),
				);
				add_option(
					'ben_post_types',
					array(
						'page' => 'true',
						'post' => 'true',
					),
				);
			}
		}

		// Remove temporary plugin options.
		public function deactivate_block_editor_navigator() {
			if ( get_option( 'ben_rating_notice' ) ) {
				delete_option( 'ben_rating_notice' );
			}
		}

		private function is_classic_editor_active() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if ( is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
				return true;
			}

			return false;
		}
	}

	$ben = new BEN_Init();
	$ben->init();
}
