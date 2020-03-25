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

    // New section for managing profile pictures
    add_action( 'show_user_profile', array( $this, 'profile_section' ), 10, 1 );
    add_action( 'edit_user_profile', array( $this, 'profile_section' ), 10, 1 );
    add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    add_action( 'wp_ajax_wc_discounts_media_upload', array( $this, 'media_upload' ) );
    add_action( 'wp_ajax_wc_discounts_set_default', array( $this, 'set_as_default' ) );

    add_filter( 'woocommerce_get_sections_products', array( $this, 'add_section' ) );
    add_filter( 'woocommerce_get_settings_products', array( $this, 'add_settings' ), 10, 2 );

    // Disable regular avatars for user's profile
    // We have a new section for it
    add_filter( 'option_show_avatars', '__return_false' );
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

      $section_options[]  = array(
        'type'  => 'sectionend',
        'id'    => 'wc_discounts'
      );

      return $section_options;
    }

    // If not, then pass on the options
    return $options;
  }


  /**
   * New section for management of multiple profile pictures.
   *
   * @since 1.0.0
   */
  public function profile_section( $user ) {

    // Default profile photo for the user
    $default_photo  = get_user_meta( $user->ID, 'default_photo', true );

    // Get all profile photos for the user
    $photos         = get_user_meta( $user->ID, 'profile_photos', true );
  ?>

    <h2><?php esc_html_e( 'Profile Picture', 'woocommerce-discounts' ); ?></h2>
    <table class="form-table" role="presentation">
      <tr>
        <th>
          <label for="default_photo">
            <?php esc_html_e( 'Default Profile Photo', 'woocommerce-discounts' ); ?>
          </label>
        </th>
        <td>
          <div class="wc-default-photo">
          <?php

            // Default photo for the user
            if ( $default_photo ) {
              // Get thumb URL using the attachment ID
              $photo_url = wp_get_attachment_thumb_url( $default_photo );

              // Show if exists
              if ( $photo_url ) {
                echo '<img src="' . $photo_url . '" alt="' . $user->user_nicename . '" class="wc-profile-photo">';
              } else {
                echo '<img src="' . $this->get_plugin_url() . '/assets/images/user.png" alt="' . $user->user_nicename . '" class="wc-profile-photo">';
              }
            } else {
              echo '<img src="' . $this->get_plugin_url() . '/assets/images/user.png" alt="' . $user->user_nicename . '" class="wc-profile-photo">';
            }

          ?>
          </div><!-- .wc-default-photo -->
        </td>
      </tr>
      <tr>
        <th>
          <label for="profile_photos">
            <?php esc_html_e( 'Profile Photos', 'woocommerce-discounts' ); ?>
          </label>
        </th>
        <td>
          <div class="wc-response"></div><!-- .wc-response -->
          <div class="wc-profile-photos">
            <?php

              // Loop over the array of profile photos
              if ( $photos ) {
                if ( is_array( $photos ) ) {
                  echo '<ul class="wc-photo-list">';

                  foreach ( $photos as $id => $url ) {
                    // Show photos over here
                    ?>
                      <li>
                        <img src="<?php echo $url; ?>" class="wc-profile-photo">
                        <a href="javascript:;" data-id="<?php echo $id; ?>" class="button button-link wc-set-default">Set as Default</a>
                      </li>
                    <?php
                  }

                  echo '</ul>';
                }
              } else {
                echo '<p class="no-photos">' . esc_html__( 'No profile photos have been added so far.', 'woocommerce-discounts' ) . '</p>';
              }

            ?>
          </div><!-- .wc-profile-photos -->

          <?php if ( current_user_can( 'upload_files' ) && did_action( 'wp_enqueue_media' ) ) { ?>
            <a href="#" class="button hide-if-no-js" id="wc-discounts-media">
              <?php esc_html_e( 'Upload Profile Photo', 'woocommerce-discounts' ); ?>
            </a>
          <?php } ?>
        </td>
      </tr>
    </table>

  <?php
  }


  /**
	 * Admin scripts to help in the photo uploading and management.
	 *
	 * @param string $page Page hook
	 */
  public function admin_enqueue_scripts( $page ) {

    // If we are not on either of these two pages, stop!
    if ( 'profile.php' !== $page && 'user-edit.php' !== $page ) {
      return;
    }

    // User must have the rights to upload media
		if ( current_user_can( 'upload_files' ) ) {
      wp_enqueue_media();
    }

    // Grab user_id and pass it to the JS
    if ( 'profile.php' === $page ) {
      $user_id = get_current_user_id();
    } else {
      $user_id = absint( $_GET['user_id'] );
    }

    // We need a sweet CSS file for styling as well
    wp_enqueue_style( \WooCommerce_Discounts::PLUGIN_SLUG . '-admin', $this->get_plugin_url() . '/assets/admin/css/admin.css', false, \WooCommerce_Discounts::PLUGIN_VERSION );

    // Pass important data to JS
    wp_register_script( \WooCommerce_Discounts::PLUGIN_SLUG . '-admin', $this->get_plugin_url() . '/assets/admin/js/admin.js', array( 'jquery' ), \WooCommerce_Discounts::PLUGIN_VERSION, true );

    $localize = array(
      'max_photos'        => get_option( 'wc_discounts_profile_limit', 5 ),
      'discount_percent'  => get_option( 'wc_discounts_percentage', 15 ),
			'user_id'			      => $user_id,
			'title'	            => esc_html__( 'Choose a Profile Photo','woocommerce-discounts' ),
      'insert'	          => esc_html__( 'Add Profile Photo','woocommerce-discounts' ),
      'default'           => esc_html__( 'Set as Default', 'woocommerce-discounts' ),
      'nonce'		          => wp_create_nonce( 'wc_discounts_profile_nonce' )
    );

    wp_enqueue_script( \WooCommerce_Discounts::PLUGIN_SLUG . '-admin' );
    wp_localize_script( \WooCommerce_Discounts::PLUGIN_SLUG . '-admin', 'wc_discounts_l10n', $localize );
  }


  /**
   * Processes the uploaded media via the AJAX call and sends back the updated data in
   * JSON format.
   *
   * @since 1.0.0
   */
  public function media_upload() {

    // Default response
    $response = esc_html__( 'There was an error uploading your profile photo.', 'woocommerce-discounts' );

    // Check for data
		if (
      empty( $_POST['user_id'] ) ||
      empty( $_POST['media_id'] ) ||
      empty( $_POST['_wpnonce'] ) ||
      ! wp_verify_nonce( $_POST['_wpnonce'], 'wc_discounts_profile_nonce' )
    ) {
      exit();
    }

    // Check for permissions
    if (
      ! current_user_can( 'upload_files' ) ||
      ! current_user_can( 'edit_user', $_POST['user_id'] )
    ) {
      exit();
    }

    // Grab the media_id and associated user_id
		$media_id = (int) $_POST['media_id'];
    $user_id  = (int) $_POST['user_id'];

		// Verify the received media is an image
		if ( wp_attachment_is_image( $media_id ) ) {
      $response = $this->add_profile_photo( $media_id, $user_id );
    }

    header( "Content-Type: application/json" );
    echo json_encode(
      array(
        'response'  => $response,
        'photos'    => $this->fetch_profile_photos( $user_id )
      )
    );

    // Required for AJAX functions
		exit();
  }


  /**
   * Adds the profile photo to the ones already uploaded for the user.
   *
   * @since 1.0.0
   *
   * @param int $media_id Media ID to be added to the profile photos
	 * @param int $user_id  ID of the user
   * @return string Response of the upload.
   */
  public function add_profile_photo( $media_id, $user_id ) {

    // Fetch the current photos for the user.
    $photos = get_user_meta( $user_id, 'profile_photos', true );

    if ( ! empty( $photos ) ) {
      if ( is_array( $photos ) ) {
        $photos[ $media_id ] = wp_get_attachment_thumb_url( $media_id );
      }
    } else {
      $photos = array(
        $media_id => wp_get_attachment_thumb_url( $media_id )
      );
    }

    update_user_meta( $user_id, 'profile_photos', $photos );
    return esc_html__( 'Profile photo has been added successfully.', 'woocommerce-discounts' );
  }


  /**
   * Grab profile photos for the user.
   *
   * @since 1.0.0
   *
   * @param int $user_id User ID for which the photos are fetched
   * @return array Profile photos of the user
   */
  public function fetch_profile_photos( $user_id ) {

    // User ID is required
    if ( ! $user_id ) {
      return;
    }

    // Grab photos from the usermeta table
    $photos = get_user_meta( $user_id, 'profile_photos', true );

    return $photos;
  }


  /**
   * Sets a particular image as default for the specified user.
   *
   * @param int $media_id Media ID to be added as the default photo
	 * @param int $user_id  ID of the user
   * @return string Response of the change
   */
  public function set_as_default() {

    // Default response
    $response = esc_html__( 'There was an error saving the default photo.', 'woocommerce-defaults' );

    // Check for data
		if (
      empty( $_POST['user_id'] ) ||
      empty( $_POST['media_id'] ) ||
      empty( $_POST['_wpnonce'] ) ||
      ! wp_verify_nonce( $_POST['_wpnonce'], 'wc_discounts_profile_nonce' )
    ) {
      exit();
    }

    // Grab the media_id and associated user_id
		$media_id = (int) $_POST['media_id'];
    $user_id  = (int) $_POST['user_id'];

    update_user_meta( $user_id, 'default_photo', $media_id );
    $response = esc_html__( 'Default photo has been set.', 'woocommerce-defaults' );

    header( "Content-Type: application/json" );
    echo json_encode(
      array(
        'response'       => $response,
        'default_photo'  => $this->fetch_default_photo( $user_id )
      )
    );

    // Required for AJAX functions
		exit();
  }


  /**
   * Grab default photo for the user.
   *
   * @since 1.0.0
   *
   * @param int $user_id User ID for which the default photo is fetched
   * @return array Containing the default photo url with id
   */
  public function fetch_default_photo( $user_id ) {

    // User ID is required
    if ( ! $user_id ) {
      return;
    }

    // Grab photos from the usermeta table
    $photo_id = get_user_meta( $user_id, 'default_photo', true );

    // Prepare an array with media id and photo url
    return array(
      $photo_id => wp_get_attachment_thumb_url( $photo_id )
    );
  }


  /**
	 * Implementation of the abstract method.
	 *
	 * @since 1.0.0
	 * @return string the full path and filename of the plugin file
	 */
  protected function get_file() {
    return dirname( __FILE__ );
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


  /**
   * Fetches plugin url for use in the admin scripts.
   *
   * @since 1.0.0
   * @return string plugin url from the parent class
   */
  public function get_plugin_url() {
    return parent::get_plugin_url();
  }

}

endif;
