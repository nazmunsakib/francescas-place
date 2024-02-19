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
    }

    public function fpalace_booking( $atts, $content = null ){
        if( ! is_user_logged_in() ){
            return $content;
        }

        $atts = shortcode_atts( array(
            'a'=> '',
        ) , $atts );

        ob_start();

        $booking_id  = isset( $_GET['booking'] ) ? intval($_GET['booking']) : null;
        $date        = isset( $_GET['date'] ) ? (string)$_GET['date'] : null;
        //update_post_meta( 212, 'booking_date', '15/02/2024');
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
                                                <i class="fas fa-calendar-alt"></i>
                                                <span><?php echo esc_html('Total nights:'); ?></span>
                                            </span>
                                            <span class="booking-info-date">1</span>
                                        </li>
                                        <li class="fplace-room-book-info-item">
                                            <input type="checkbox" class="fplace_extra_price_check" name="fplace_extra_price_check" value="<?php //echo esc_attr( $extra_price ); ?>">
                                            <label for="fplace_extra_price_check">
                                                <?php _e("Book extra person @ £", "fplace"); ?>
                                            </label>
                                            <span class='fplace_extra_price'>
                                                <?php //echo esc_html( $extra_price ); ?>
                                            </span>
                                        </li>
                                    </ul>
                                    <div class="fplace-room-book-info-footer">
                                        <h4 class="fplace-booking-info-total-price">
                                            <?php echo 'Total £' . "<span class='fplace-total-amount'>{}</span>"; ?>
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
                    <p class="warning">Something went wrong, the request is invalid please try agin!</p>
                <?php endif; ?>
            <?php else: ?>
                <div id='fplace-booking-container' class="fplace-booking-container">
                    <div class="fplace-section-heading">
                        <h2>Check Availability</h2>
                    </div>
                    <div class="fplace-room-search-wrapper">
                        <div class="fplace-room-search-area">
                            <p>Please select arrival date</p>
                            <div class="search-form-wrapper">
                                <input type="text" id="fplace-room-input" placeholder="dd/mm/yyy">
                            </div>
                            <div>
                                <button id="fplace-get-room-search" type="submit">Check Availability</button>
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
}

new Shortcode();