<?php
/**
 * Ajax actions
 *
 * @package FrancescasPlace
 */
namespace FrancescasPlace\Booking;

defined('ABSPATH') || die();

class Francescas_Place_Settings{

    public function __construct(){
        add_action('admin_menu',  [$this, 'fplace_settings_page'] );
        add_action('admin_init', [$this, 'fplace_register_settings'] );
    }

    /**
     * Sattings page
     */
    public function fplace_settings_page() {
        add_options_page(
            __('Place Booking Settings', 'fplace-booking'),
            __('Place Booking Settings', 'fplace-booking'),
            'manage_options',
            'place-booking-settings',
            [$this, 'place_booking_settings_callback']
        );
    }

    /**
     * Place booking page settings
     */
    public function place_booking_settings_callback() {
        ?>
        <div class="wrap">
            <h2><?php _e("Francesca's Place Settings"); ?></h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('fplace_settings_group');
                do_settings_sections('fplace-settings');
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register settings and fields
     */
    public function fplace_register_settings() {
        register_setting('fplace_settings_group', 'fplace_admin_email', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_email',
            'validate_callback' => [$this, 'validate_email']
        ));

        register_setting('fplace_settings_group', 'booking_form_id', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'validate_callback' => 'validate_form_id'
        ));

        add_settings_section('fplace_settings_section', 'Booking system settings', [$this, 'fplace_section_callback'], 'fplace-settings');
        add_settings_field('fplace_admin_email', 'Admin Email', [$this, 'fplace_admin_email_field_callback'], 'fplace-settings', 'fplace_settings_section');
        add_settings_field('booking_form_id', 'Booking Form ID', [$this, 'booking_form_id_field_callback'], 'fplace-settings', 'fplace_settings_section');
    }

    /**
     * Settings section
     */
    public function fplace_section_callback() {
        echo '<p>Need to provide admin email for receive booking notification. Also need booking form ID!</p>';
        echo "<p>";
        echo "<label>Booking Shortcode: <label>";
        echo "<input type='text' name='' value='[fpalace_booking]' disabled />";
        echo "</p>";
        echo "<p>";
        echo "<label>Wait List Shortcode: <label>";
        echo "<input type='text' name='' value='[wait_list]' disabled />";
        echo "</p>";
    }

    /**
     * Callback function for email field
     */
    public function fplace_admin_email_field_callback() {
        $email = get_option('fplace_admin_email');
        echo "<input type='text' style='width: 250px;' name='fplace_admin_email' value='" . esc_attr($email) . "' />";
    }

    /**
     * Callback function for form ID field
     */
    public function booking_form_id_field_callback() {
        $form_id = get_option('booking_form_id');
        echo "<input type='text' style='width: 250px;' name='booking_form_id' value='" . esc_attr($form_id) . "' />";
    }

    /**
     * Callback function for form ID field
     */
    public function validate_email( $value ) {
        if ( ! is_email( $value ) ) {
            add_settings_error('fplace_admin_email', 'invalid_email', 'Please enter a valid email address.');
            return get_option('fplace_admin_email'); 
        }

        return $value;
    }

    /**
     * Callback function for form ID field
     */
    public function validate_form_id( $value ) {
        if ( ! is_numeric( $value ) ) {
            add_settings_error('booking_form_id', 'invalid_form_id', 'Please enter a numeric value for Form ID.');
            return get_option('booking_form_id');
        }

        return $value;
    }

}

new Francescas_Place_Settings();


