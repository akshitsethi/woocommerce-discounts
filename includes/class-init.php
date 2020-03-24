<?php
/**
 * WooCommerce Discounts
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   AkshitSethi/WooCommerceDiscounts/Admin
 * @author    Akshit Sethi
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace AkshitSethi\WooCommerceDiscounts;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_6_1\SV_WC_Plugin;

if ( ! class_exists( '\\AkshitSethi\\WooCommerceDiscounts\\Init' ) ) :  

/**
 * WooCommerce Discounts Init Class
 *
 * This class extends the abstract class of the framework and adds the required functionality
 * on top of it.
 *
 * @version 1.0.0
 */
class Init extends SV_WC_Plugin {

  /**
	 * Initialize the plugin.
	 *
	 * @since 1.0.0
	 */
  public function __construct() {
    // initialize the plugin admin
    add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    add_filter( 'plugin_row_meta', [ $this, 'meta_links' ], 10, 2 );
  }


  /**
	 * Initializes the plugin admin.
	 *
	 * @since 1.0.0
	 */
  public function admin_menu() {
    if ( is_admin() && current_user_can( 'manage_options' ) ) {
      add_options_page(
        esc_html__( 'WooCommerce Discounts', 'woocommerce-discounts' ),
        esc_html__( 'WooCommerce Discounts', 'woocommerce-discounts' ),
        'manage_options',
        'wc_discounts_options',
        array( $this, 'settings' )
      );
    }
  }


  /**
   * Adds custom links to the meta on the plugins page.
   * 
   * @param array  $links Array of links for the plugins
   * @param string $file  Name of the main plugin file
   * 
   * @return array
   */
  public function meta_links( $links, $file ) {
    if ( strpos( $file, 'woocommerce-discounts.php' ) !== FALSE ) {
      $new_links = [
        '<a href="https://www.facebook.com/akshitsethi" target="_blank">' . esc_html__( 'Facebook', 'widgets-bundle' ) . '</a>',
        '<a href="https://twitter.com/akshitsethi" target="_blank">' . esc_html__( 'Twitter', 'widgets-bundle' ) . '</a>'
      ];

      $links = array_merge( $links, $new_links );
    }

    return $links;
  }


  /**
	 * Implementation of the abstract method.
	 *
	 * @since 1.0.0
	 * @return string the full path and filename of the plugin file
	 */
  protected function get_file() {
    return __FILE__;
  }


  /**
	 * Returns the full name of the plugin.
	 *
	 * @since 1.0.0
	 * @return string plugin name
	 */
  public function get_plugin_name() {
    return esc_html__( 'WooCommerce Discounts', 'woocommerce-discounts' );
  }

}

endif;
