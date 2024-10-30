<?php

namespace BEN\Block_Editor_Navigator;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BEN_Options' ) ) {

	class BEN_Options extends Block_Editor_Navigator {

		public function __construct() {
			parent::__construct();
		}

		public function add_plugin_options() {
			$types = array_merge(
				array(
					'post' => 'post',
					'page' => 'page',
				),
				get_post_types(
					array(
						'public'   => true,
						'_builtin' => false,
					),
					'names',
					'and'
				)
			);

			add_settings_section(
				'ben_section',
				'',
				function() {
					?>
						<h2 class="title" id="block-editor-navigator">
							Block Editor Navigator
						</h2>
						<p>
							For the price of a cup of coffee per month, you can <a href="https://patreon.com/krasenslavov" target="_blank"><strong>help and support me on Patreon</strong></a> in continuing to develop and maintain all of my free WordPress plugins, every little bit helps and is greatly appreciated!
						</p>
						<div class="ben-notice">
							<p>
								<strong>Please rate us</strong>
								<a href="<?php echo esc_url( $this->settings['plugin_wporgrate'] ); ?>" target="_blank"><img src="<?php echo esc_url( $this->settings['plugin_url'] ); ?>assets/img/rate.png" alt="Rate us @ WordPress.org" /></a>
							</p>
							<p>
								<strong>Having issues?</strong>
								<a href="<?php echo esc_url( $this->settings['plugin_wporgurl'] ); ?>" target="_blank">Create a Support Ticket</a>
							</p>
							<p>
								<strong>Developed by</strong>
								<a href="https://krasenslavov.com/" target="_blank">Krasen Slavov @ Developry</a>
							</p>
						</div>
						<hr />
						<ul>
							<li>&bullet; Limit to display the Navigator Conrols for Classic or Block editors.</li>
							<li>&bullet; Select post types where you want to enable/disabled and use the Block Editor Navigator controls.</li>
							<li>&bullet; WooCommerce Products and Custom Post Types support is included.</li>
							<li>&bullet; All available CPT and WooCommerce will show as options if they are enabled.</li>
						</ul>
					<?php
				},
				'writing'
			);

			register_setting(
				'writing',
				'ben_editor_support',
				function( $input ) {
					if ( is_array( $input ) ) {
						$input = array_map( 'sanitize_text_field', $input );
					}
					return $input;
				}
			);

			add_settings_field(
				'ben_editor_support',
				'Supported Editors',
				function() {
					$options = get_option( 'ben_editor_support' );
					?>
						<div class="ben">
							<div class="ben-editors">
								<p>
									<label for="ben_editor_support[gutenberg]">
										<input type="checkbox" id="ben_editor_support[gutenberg]" name="ben_editor_support[gutenberg]" onclick="this.value = !(this.value != 'false');" value="<?php echo ( ! empty( $options['gutenberg'] ) ) ? esc_attr( $options['gutenberg'] ) : 'false'; ?>" <?php echo ( ! empty( $options['gutenberg'] ) && esc_attr( $options['gutenberg'] ) === 'true' ) ? 'checked' : ''; ?> />
										Block Editor (Gutenberg)
									</label>
								</p>
								<p>
									<label for="ben_editor_support[classic]">
										<input type="checkbox" id="ben_editor_support[classic]" name="ben_editor_support[classic]" onclick="this.value = !(this.value != 'false');" value="<?php echo ( ! empty( $options['classic'] ) ) ? esc_attr( $options['classic'] ) : 'false'; ?>" <?php echo ( ! empty( $options['classic'] ) && esc_attr( $options['classic'] ) === 'true' ) ? 'checked' : ''; ?> />
										Classic Editor
									</label>
								</p>
							</div>
						</div>
					<?php
				},
				'writing',
				'ben_section'
			);

			register_setting(
				'writing',
				'ben_post_types',
				function( $input ) {
					if ( is_array( $input ) ) {
						$input = array_map( 'sanitize_text_field', $input );
					}
					return $input;
				}
			);

			add_settings_field(
				'ben_post_types',
				'Supported Types',
				function( $types ) {
					foreach ( $types as $type ) {
						$options = get_option( 'ben_post_types' );

						switch ( $type ) {
							case 'post':
								$label = 'Posts';
								break;
							case 'page':
								$label = 'Pages';
								break;
							case 'product':
								$label = 'WooCommerce Products';
								break;
							default:
								$label = 'Custom Post Type (<em>' . $type . '</em>)';
								break;
						}
						?>
							<div class="ben">
								<div class="ben-post-types">
									<p>
										<label for="ben_post_types[<?php echo esc_attr( $type ); ?>]">
											<input type="checkbox" id="ben_post_types[<?php echo esc_attr( $type ); ?>]" name="ben_post_types[<?php echo esc_attr( $type ); ?>]" onclick="this.value = !(this.value != 'false');" value="<?php echo ( ! empty( $options[ $type ] ) ) ? esc_attr( $options[ $type ] ) : 'false'; ?>" <?php echo ( ! empty( $options[ $type ] ) && esc_attr( $options[ $type ] ) === 'true' ) ? 'checked' : ''; ?> />
											<?php echo $label; ?>
										</label>
									</p>
								</div>
							</div>
						<?php
					}
				},
				'writing',
				'ben_section',
				$types
			);
		}
	}
}
