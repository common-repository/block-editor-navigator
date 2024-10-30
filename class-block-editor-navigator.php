<?php
/**
	* Plugin Name: Block Editor Navigator
	* Plugin URI: https://krasenslavov.com/plugins/block-editor-navigator/
	* Description: Navigate Posts & Pages with ease inside the Block Editor without the need to go to the main page; Classic Editor support incliuded.
	* Author: Krasen Slavov
	* Version: 1.1.4
	* Author URI: https://krasenslavov.com/
	* License: GPLv2 or later
	* License URI: https://www.gnu.org/licenses/gpl-2.0.html
	* Text Domain: block-editor-navigator
	* Domain Path: /lang
	*
	* Copyright 2018-2022 Krasen Slavov (email: hello@krasenslavov.com)
	*
	* This program is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License, version 2, as
	* published by the Free Software Foundation.
	*
	* This program is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with this program; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*/

namespace BEN\Block_Editor_Navigator;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'Block_Editor_Navigator' ) ) {

	class Block_Editor_Navigator {

		const DEV_MODE         = false;
		const VERSION          = '1.1.4';
		const PHP_MIN_VERSION  = '7.2';
		const WP_MIN_VERSION   = '5.0';
		const UUID             = 'ben';
		const TEXTDOMAIN       = 'block-editor-navigator';
		const PLUGIN_NAME      = 'Block Editor Navigator';
		const PLUGIN_DOCURL    = 'https://krasenslavov.com/plugins/block-editor-navigator/';
		const PLUGIN_WPORGURL  = 'https://wordpress.org/support/plugin/block-editor-navigator/';
		const PLUGIN_WPORGRATE = 'https://wordpress.org/support/plugin/block-editor-navigator/reviews/?filter=5';

		var $settings;

		public function __construct() {
			$this->settings = array(
				'dev_mode'         => self::DEV_MODE,
				'version'          => self::VERSION,
				'php_min_version'  => self::PHP_MIN_VERSION,
				'wp_min_version'   => self::WP_MIN_VERSION,
				'uuid'             => self::UUID,
				'textdomain'       => self::TEXTDOMAIN,
				'plugin_name'      => self::PLUGIN_NAME,
				'plugin_docurl'    => self::PLUGIN_DOCURL,
				'plugin_wporgurl'  => self::PLUGIN_WPORGURL,
				'plugin_wporgrate' => self::PLUGIN_WPORGRATE,
				'plugin_url'       => plugin_dir_url( __FILE__ ),
				'plugin_basename'  => plugin_basename( __FILE__ ),
				'plugin_path'      => plugin_dir_path( __FILE__ ),
			);

			if ( $this->check_dependencies() ) {
				load_plugin_textdomain( $this->settings['textdomain'], false, $this->settings['plugin_basename'] . 'lang' );
			}
		}

		public function rating_notice_display() {
			if ( ! get_option( 'ben_rating_notice' ) ) {
				?>
					<div class="notice notice-success is-dismissible">
						<h3>Block Editor Navigator</h3>
						<p>
							For the price of a cup of coffee per month, you can <a href="https://patreon.com/krasenslavov" target="_blank"><strong>help and support me on Patreon</strong></a>, every little bit helps and is greatly appreciated!
						</p>
						<p>
							Could you please kindly help the plugin in your turn by <strong>giving it 5 stars rating</strong>?
						</p>
						<p>
							<a href="<?php echo esc_url( $this->settings['plugin_wporgrate'] ); ?>" target="_blank" class="button button-primary">Rate Us @ WordPress.org</a>
							<a href="?ben_rating_notice_dismiss" class="button"><strong>I already did</strong></a>
							<a href="?ben_rating_notice_dismiss" class="button"><strong>Don't show this notice again!</strong></a>
						</p>
					</div>
				<?php
			}
		}

		public function rating_notice_dismiss() {
			if ( isset( $_GET['ben_rating_notice_dismiss'] ) ) {
				add_option( 'ben_rating_notice', 1 );
			}
		}

		public function check_dependencies() {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';

			if ( version_compare( PHP_VERSION, $this->settings['php_min_version'] ) >= 0
				&& version_compare( $GLOBALS['wp_version'], $this->settings['wp_min_version'] ) >= 0 ) {
				$check = true;
			} else {
				$check = false;
				add_action( 'admin_notices', array( $this, 'display_min_requirements_notice' ) );
			}

			if ( $check ) {
				return true;
			}

			deactivate_plugins( $this->settings['plugin_basename'] );

			return false;
		}

		public function display_min_requirements_notice() {
			?>
				<div class="notice notice-error">
					<p>
						<strong><?php echo $this->settings['plugin_name']; ?></strong> requires a minimum of <em>PHP <?php echo $this->settings['php_min_version']; ?></em> and <em>WordPress <?php echo $this->settings['wp_min_version']; ?></em>.
					</p>
					<p>
						You are currently running <strong>PHP <?php echo PHP_VERSION; ?></strong> and <strong>WordPress <?php echo $GLOBALS['wp_version']; ?></strong>.
					</p>
				</div>
			<?php
		}
	}

	new Block_Editor_Navigator();

	// Core
	require_once 'classes/core/class-ben-options.php';

	// Init
	require_once 'classes/class-ben-init.php';

	// Events
	require_once 'classes/events/class-ben-navigate-prev-post.php';
	require_once 'classes/events/class-ben-navigate-next-post.php';
	require_once 'classes/events/class-ben-search-autocomplete.php';
}
