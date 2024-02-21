<?php
/**
 * Register Custom post type
 *
 * @package FrancescasPlace
 */
namespace FrancescasPlace\Booking;

defined('ABSPATH') || die();

use \FrancescasPlace\Booking\Helper;

class Shortcode{
    public function __construct(){
        add_shortcode('fpalace_booking', array( $this,'fpalace_booking') );
        add_shortcode('wait_list', array( $this,'fpalace_wait_list') );
    }

    public function fpalace_booking( $atts, $content = null ){
        if( ! is_user_logged_in() ){
            return $content;
        }

        $atts = shortcode_atts( array(
            'settings'=> '',
        ) , $atts );

        ob_start();

        $booking_id  = isset( $_GET['booking'] ) ? intval($_GET['booking']) : null;
        $date        = isset( $_GET['date'] ) ? (string)$_GET['date'] : null;
        ?>
        <section class="fplace-booking-main-wrapper">
            <?php 
            if( $booking_id &&  $date ) : 
                $date_object    = strtotime(str_replace('/', '-', $date));
                $id_validation  = get_post( $booking_id );
                $current_date   = strtotime(date('Y-m-d'));

                $get_date       = Helper::formattingDate($date);
                $arriving_date  = $get_date['arriving_date']    ?? '';
                $arriving_day   = $get_date['arriving_day']     ?? '';
                $departing_date = $get_date['departing_date']   ?? '';
                $departing_day  = $get_date['departing_day']    ?? '';

                if( $date_object && $id_validation && ( $date_object >= $current_date ) ) : 
                    ?>
                    <div class="fplace-proposed-booking-area">
                        <div class="fplace-booking-item-header">
                            <h2><?php _e('View Proposed Booking Details', 'fplace-booking'); ?></h2>
                        </div>
                        <div class="fplace-booking-item">
                            <div class="fplace-item-header">
                                <h3><?php echo get_the_title($booking_id); ?></h3>
                            </div>

                            <div class="fplace-proposed-booking-form">
                                <div class="fplace-booking-info-wrap">
                                    <ul class="fplace-booking-info">
                                        <li>
                                            <span class="booking-info-icon">
                                                <i class="fas fa-calendar-alt"></i>
                                                <span><?php echo esc_html('Arriving PM:'); ?></span>
                                            </span>
                                            <span class="booking-info-date">
                                                <?php echo $arriving_day . ", " . $arriving_date; ?>
                                            </span>
                                        </li>
                                        <li>
                                            <span class="booking-info-icon">
                                                <i class="fas fa-calendar-alt"></i>
                                                <span><?php echo esc_html('Departing AM:'); ?></span>
                                            </span>
                                            <span class="booking-info-date">
                                                <?php echo $departing_day . ", " . $departing_date; ?>
                                            </span>
                                        </li>
                                        <li>
                                            <span class="booking-info-icon">
                                                <i class="fas fa-suitcase"></i>
                                                <span><?php echo esc_html('Total nights:'); ?></span>
                                            </span>
                                            <span class="booking-info-date">1</span>
                                        </li>
                                        <li class="fplace-room-book-info-item">
                                            <input type="checkbox" class="fplace_proposed_extra_price_check" name="fplace_extra_price_check" value="">
                                            <label for="fplace_extra_price_check"><?php _e("Book extra person @ £", "fplace"); ?></label>
                                            <span class='fplace-proposed-extra-price'>5</span>
                                        </li>
                                    </ul>
                                    <div class="fplace-room-book-info-footer">
                                        <h4 class="fplace-booking-info-total-price">
                                            <?php echo 'Total £' . "<span class='fplace-proposed-total-amount'></span>"; ?>
                                        </h4>
                                    </div>
                                </div>

                                <div class="fplace-proposed-booking-form-header">
                                    <h4><?php _e('Required Booking Information', 'fplace-booking'); ?></h4>
                                </div>
                                <div class="fplace-proposed-booking-form-container">
                                    <?php echo do_shortcode('[gravityform id="1" title="false" description="false" ajax="true"]'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="warning"><?php _e('Something went wrong, the request is invalid please try agin!', 'fplace-booking'); ?></p>
                <?php endif; ?>
            <?php else: ?>
                <div id='fplace-booking-container' class="fplace-booking-container">
                    <div class="fplace-section-heading">
                        <h2><?php _e('Check Availability', 'francescas-place'); ?></h2>
                    </div>
                    <div class="fplace-room-search-wrapper">
                        <div class="fplace-room-search-area">
                            <p><?php _e('Please select arrival date', 'francescas-place'); ?></p>
                            <div class="search-form-wrapper">
                                <input type="text" id="fplace-room-input" placeholder="dd/mm/yyy">
                            </div>
                            <div>
                                <button id="fplace-get-room-search" type="submit"><?php _e('Check Availability', 'francescas-place'); ?></button>
                            </div>
                        </div>
                    </div>

                    <div class="fplace-search-result-wrapper">
                        <div id="fplace-search-result-container"></div>
                    </div>
                </div>
            <?php endif; ?>
        </section>
        <?php
        return ob_get_clean();
    }

    public function fpalace_wait_list( $atts, $content = null ){
        if( ! is_user_logged_in() ){
            return $content;
        }

        $atts = shortcode_atts( array(
            'settings'=> '',
        ) , $atts );

        ob_start();

        $booking_id  = isset( $_GET['booking'] ) ? intval($_GET['booking']) : null;
        $date        = isset( $_GET['date'] ) ? (string)$_GET['date'] : null;
        ?>
        <section class="fplace-accommodation-cancellation-section">
            <div class="fplace-accommodation-cancellation">
                <h2><?php _e('Current Accommodation Wait List', 'fplace-booking'); ?></h2>
                <h3><?php _e('** Thank you for registering on the accommodation cancellation wait list **', 'fplace-booking'); ?></h3>
                <p><?php _e('If a room becomes available for your chosen dates you will be sent a text message', 'fplace-booking'); ?></p>
                <p>Room bookings are on a first come first served basis so please check the BOOKING STATUS on your <a href="#">manage bookings</a> page and
confirmation email to confirm your booking has been successful.</p>
            </div>
            <div class="fplace-wait-list-wrapper">
                <table class="fplace-wait-list-table">
                    <thead>
                        <tr>
                            <th><?php _e('Arriving', 'fplace-booking'); ?></th>
                            <th><?php _e('Departing', 'fplace-booking'); ?></th>
                            <th><?php _e('Action', 'fplace-booking'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Thursday, January 4, 2024</td>
                            <td>Tuesday, February 20, 2024</td>
                            <td><a href="" class="fplace-wait-action">Remove</a></td>
                        </tr>
                        <tr>
                            <td>Friday, January 5, 2024</td>
                            <td>Wednesday, February 21, 2024</td>
                            <td><a href="" class="fplace-wait-action">Remove</a></td>
                        </tr>
                        <tr>
                            <td>Saturday, March 2, 2024</td>
                            <td>Sunday, March 3, 2024 </td>
                            <td><a href="" class="fplace-wait-action">Remove</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}

new Shortcode();