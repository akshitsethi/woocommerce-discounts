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
 * This class extends the abstract class of the framework and adds the required
 * functionality on top of it.
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

    add_filter( 'woocommerce_get_sections_products', array( $this, 'add_section' ) );
    add_filter( 'woocommerce_get_settings_products', array( $this, 'add_settings' ), 10, 2 );
  }


  /**
	 * Adds a discount section to the WooCommerce products section.
	 *
	 * @since 1.0.0
   * @return array All sections for WC product settings
	 */
  public function add_section( $sections ) {

    $sections['wc_discounts'] = esc_html__( 'Discounts', 'woocommerce-discounts' );
  	return $sections;
  }


  /**
   * Options for the admin to manage the discounts section.
   *
   * @since 1.0.0
   * @return array All settings for the WC products section
   */
  public function add_settings( $options, $current_section ) {

    // Check for the required section to add the options
    if ( 'wc_discounts' === $current_section ) {

      $section_options    = array();

      // Section title
      $section_options[]  = array(
        'name'  => esc_html__( 'WooCommerce Discounts', 'woocommerce-discounts' ),
        'type'  => 'title',
        'desc'  => esc_html__( 'Configure options for the WooCommerce Discounts plugin.', 'woocommerce-discounts' ),
        'id'    => 'wc_discounts'
      );

      // Number of profile pictures allowed
      $section_options[]  = array(
        'name'      => esc_html__( 'No. of Profile Pictures', 'woocommerce-discounts' ),
        'desc_tip'  => esc_html__( 'Maximum number of profile pictures a user can upload to their profile.', 'woocommerce-discounts' ),
        'id'        => 'wc_discounts_profile_limit',
        'type'      => 'select',
        'default'   => 5,
        'options'   => array(
          1   => '1',
          2   => '2',
          3   => '3',
          4   => '4',
          5   => '5',
          6   => '6',
          7   => '7',
          8   => '8',
          9   => '9',
          10  => '10'
        )
      );

      // Discount percentage
      $section_options[]  = array(
        'name'      => esc_html__( 'Discount Offered', 'woocommerce-discounts' ),
        'desc_tip'  => esc_html__( 'Percentage of discount offered to eligible customers at the time of checkout.', 'woocommerce-discounts' ),
        'id'        => 'wc_discounts_percentage',
        'type'      => 'select',
        'default'   => 15,
        'options'   => array(
          5    => '5%',
          10   => '10%',
          15   => '15%',
          20   => '20%',
          25   => '25%',
          30   => '30%'
        )
      );

      $section_options[] = array(
        'type'  => 'sectionend',
        'id'    => 'wc_discounts'
      );

      return $section_options;
    }

    // If not, then pass on the options
    return $options;
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
