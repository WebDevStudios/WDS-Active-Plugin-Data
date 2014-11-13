<?php
/*
Plugin Name: WDS Active Plugin Data
Plugin URI: http://www.webdevstudios.com
Description: Get active status of available plugins in WordPress Multisite
Version: 1.0.0
Author: WebDevStudios
Author URI: http://www.webdevstudios.com
License: GPLv2
Text Domain: wds-apd
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'WDS_Active_Plugin_Data' ) ) {
	class WDS_Active_Plugin_Data {

		/**
		 * @var array Available plugins in /wp-content/plugins/
		 *
		 * @since 1.0.0
		 */
		public $available_plugins = array();

		public function __construct() {
			$this->do_hooks();
		}

		/**
		 * Run our necessary hooks.
		 *
		 * @since 1.0.0
		 */
		public function do_hooks() {
			add_action( 'network_admin_menu', array( $this, 'network_menu') );
			add_action( 'admin_init', array( $this, 'get_available_plugins' ) );
			add_action( 'admin_footer', array( $this, 'scripts' ) );
			add_action( 'admin_head', array( $this, 'styles' ) );
			add_action( 'plugins_loaded', array( $this, 'languages' ) );
		}

		/**
		 * Load our textdomain.
		 *
		 * @since 1.0.0
		 */
		public function languages() {
			load_plugin_textdomain( 'wds-apd', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Query for our available plugins and construct our custom array of data.
		 *
		 * @since 1.0.0
		 */
		public function get_available_plugins() {
			$plugins = get_plugins();

			if ( ! empty ( $plugins ) ) {
				foreach ( $plugins as $plugin_file => $plugin_data ) {
					$this->available_plugins[ $plugin_data['Name'] ] = $plugin_file;
				}
			}
		}

		/**
		 * Add our Network Admin menu item.
		 *
		 * @since 1.0.0
		 */
		function network_menu() {
			add_submenu_page( 'settings.php', __( 'WDS Active Plugins Data', 'wds-apd' ), __( 'WDS Active Plugins Data', 'wds-apd' ), 'manage_options', 'wds-apd', array( $this, 'display_plugin_data' ) );
		}

		/**
		 * Callback for our Network Admin menu page.
		 *
		 * @since 1.0.0
		 */
		public function display_plugin_data() { ?>
			<div class="wrap">
			<h1><?php _e( 'WDS Active Plugin Data', 'wds-apd' ); ?></h1>
			<?php
			echo $this->get_toggle_links();

			echo $this->get_simple_list();

			$this->get_advanced_list();
		}

		/**
		 * Output our "Advanced" List.
		 *
		 * @since 1.0.0
		 */
		public function get_advanced_list() { ?>
			<div id="advanced" class="display-none">
				<h2><?php _e( 'Advanced', 'wds-apd' ); ?></h2>
				<table class="wds-form-table">
					<tr>
						<th><?php _e( 'Plugin Name', 'wds-apd' ); ?></th>
						<th><?php _e( 'Active', 'wds-apd' ); ?></th>
						<th><?php _e( 'Network Active', 'wds-apd' ); ?></th>
					</tr>
					<?php
						foreach( $this->available_plugins as $plugin_name => $plugin_file ) { ?>
							<tr>
								<td><?php echo $plugin_name; ?></td>
								<td><?php ( is_plugin_active( $plugin_file ) ) ? _e( 'true', 'wds-apd' ) : _e( 'false', 'wds-apd' ); ?></td>
								<td><?php ( is_plugin_active_for_network( $plugin_file ) ) ? _e( 'true', 'wds-apd' ) : _e( 'false', 'wds-apd' ); ?></td>
							</tr>
						<?php
						}
					?>
				</table>
			</div>
		<?php
		}

		/**
		 * Output our "Simple" list.
		 *
		 * @since 1.0.0
		 */
		public function get_simple_list() { ?>
			<div id="simple">
				<h2><?php _e( 'Simple', 'wds-apd' ); ?></h2>
				<?php
				$text = '';
				foreach( $this->available_plugins as $plugin_name => $plugin_file ) {
					$text .= $plugin_name . ' ';
					$text .= ( is_plugin_active( $plugin_file ) ) ? __( '[A]', 'wds-apd' ) : '';
					$text .= ( is_plugin_active_for_network( $plugin_file ) ) ? __( '[NA]', 'wds-apd' ) : '';
					$text .= "\n";
				}
				?>
				<textarea onclick="this.focus();this.select()" readonly="readonly"><?php echo trim( $text ); ?></textarea>
			</div>
			</div>

			<?php
		}

		/**
		 * Display out toggle links
		 *
		 * @since 1.0.0
		 */
		public function get_toggle_links() { ?>
			<p><a class="simple" href="#"><?php _e( 'Toggle Simple', 'wds-apd' ); ?></a> | <a class="advanced" href="#"><?php _e( 'Toggle Advanced', 'wds-apd' ); ?></a></p>

		<?php
		}

		/**
		 * Output some jQuery goodness
		 *
		 * @since 1.0.0.
		 */
		public function scripts() {
			?>
			<script>
				(function($) {
					$('.advanced,.simple').on( 'click', function(e){
						e.preventDefault();
						$('#advanced').toggleClass('display-none');
						$('#simple').toggleClass('display-none');
					});
				})(jQuery);
			</script>
		<?php
		}

		/**
		 * Make it pretty-er
		 *
		 * @since 1.0.0
		 */
		public function styles() { ?>
			<style>
			.display-none {
				display: none;
			}
			.wds-form-table {
				width: 100%;
			}
			.wds-form-table th {
				text-align: left;
			}
			.wds-form-table td {
				width: 33%;
			}
			#simple textarea {
				width: 500px;
				height: 500px;
			}
			</style>
		<?php
		}
	}
}

$list_it = new WDS_Active_Plugin_Data();
