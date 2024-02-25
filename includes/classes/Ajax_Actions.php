<?php
/**
 * Ajax actions
 *
 * @package FrancescasPlace
 */
namespace FrancescasPlace\Booking;

defined('ABSPATH') || die();

use \FrancescasPlace\Booking\Helper;

class Ajax_Actions{

    public function __construct(){
        add_action('wp_ajax_fplace_search_room', [$this, 'fplace_search_room'] );
        add_action('wp_ajax_nopriv_fplace_search_room', [$this, 'fplace_search_room'] );

        add_action('wp_ajax_cancel_booking', [$this, 'fplace_cancel_booking']);
        add_action('wp_ajax_nopriv_cancel_booking', [$this, 'fplace_cancel_booking'] );

        add_action('wp_ajax_submit_wait_list', [$this, 'submit_wait_list_form'] );
        add_action('wp_ajax_nopriv_submit_wait_list',  [$this, 'submit_wait_list_form'] );

        add_action('wp_ajax_remove_wait_list', [$this, 'remove_from_wait_list'] );
        add_action('wp_ajax_nopriv_remove_wait_list',  [$this, 'remove_from_wait_list'] );
    }

    /**
     * Availability search
     */
    public function fplace_search_room(){ 
        $date       = (string)$_POST['get_date'] ?? '';
        $user_id    = intval( $_POST['user_id'] ) ?? '';

        //compare date 
        $date_object    = strtotime(str_replace('/', '-', $date));
        $current_date   = strtotime(date('Y-m-d'));

        check_ajax_referer('francescas_place_booking', 'nonce');

        if( empty( $date ) || $date_object < $current_date ) {
            echo "Sorry! this is the invalid request.";
            die(0);
        }

        $get_date = Helper::formattingDate($date);
        $arriving_date  = $get_date['arriving_date']    ?? '';
        $arriving_day   = $get_date['arriving_day']     ?? '';
        $departing_date = $get_date['departing_date']   ?? '';
        $departing_day  = $get_date['departing_day']    ?? '';

        $args = array(
            'post_type'     => 'rooms',
            'posts_per_pages' => -1,
            'meta_query'    => array(
                'relation' => 'AND',
                array( 
                    'key'       => 'booking_dates',
                    'value'     =>  $date,
                    'compare'   => 'NOT LIKE'
                ),
                array( 
                    'key'       => 'availability',
                    'value'     =>  '1',
                    'compare'   => '=='
                )
            )
        );
        
        $query = new \WP_Query( $args );
        ?>
        <div class="fplace-arriving-departing">
            <div>
                <p class="fplace-query-date arriving">
                    <span class="fplace-icon"><i class="fas fa-calendar-alt"></i></span>
                    <span class="fplace-bold-text"><?php echo esc_html('Arriving PM:'); ?></span>
                    <?php echo $arriving_day . ", " . $arriving_date; ?>
                </p>
                <p class="fplace-query-date departing">
                    <span class="fplace-icon"><i class="fas fa-calendar-alt"></i></span>
                    <span class="fplace-bold-text"><?php echo esc_html('Departing AM:'); ?></span>
                    <?php echo $departing_day . ", " . $departing_date; ?>
                </p>
            </div>
        </div>
        <div class="fplace-available-room-list">
            <?php
            if ( $query->have_posts() ) :
                while ( $query->have_posts() ) : $query->the_post();
                    $id             =  $query->post->ID;
                    $book_url       = "?booking={$id}&date={$date}";
                    $pros_cons      = get_field('pros_cons') ?? '';
                    $check_in       = get_field('check_in') ? get_field('check_in') : 'Check in 15:00';
                    $check_out      = get_field('check_out') ? get_field('check_out') : 'Check out 12:00'; 
                    $occupancy      = get_field('standard_occupancy') ?? ''; 
                    $price          = get_field('price') ?? ''; 
                    $extra_price    = get_field('extra_person') ?? ''; 
                    ?>
                    <div class="fplace-room-wrapper">
                        <div class="fplace-room-item">
                            <div class="fplace-item-header">
                                <h3><?php echo get_the_title(); ?></h3>
                            </div>
                            <div class="fplace-room-info-wrapper">
                                <div class="fplace-room-facilities">
                                    <ul class="fplace-pros_cons-list">
                                        <?php
                                        if ( ! empty( $pros_cons ) ) :
                                            foreach ( $pros_cons as $item ) :
                                            ?>
                                            <li class="fplace-pros_cons-item">
                                                <?php 
                                                    echo $item['icon'] == 'check'    ? '<span class="fplace-icons check"><i class="fas fa-check"></i></span>' : '';
                                                    echo $item['icon'] == 'parking'  ? '<span class="fplace-icons parking"><i class="fas fa-car"></i></span>' : '';
                                                    echo $item['icon'] == 'notice'   ? '<span class="fplace-icons notice"><i class="fas fa-exclamation-circle"></i></span>' : '';
                                                ?>
                                                <span class="fplace-pros_cons_title">
                                                    <?php echo esc_html( $item['title'] ); ?>
                                                </span>
                                            </li>
                                            <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </ul>
                                </div>
                                <div class="fplace-room-book-info">
                                    <div class="fplace-room-book-info-inner">
                                        <ul class="fplace-room-book-info-list">
                                            <li class="fplace-room-book-info-item">
                                                <span class="fplace-room-book-info-icon">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </span>
                                                <span class="fplace-room-book-info_title">
                                                    <?php echo $arriving_day . ", " . $arriving_date; ?>
                                                </span>
                                            </li>
                                            <?php if( ! empty( $check_in ) ): ?>
                                                <li class="fplace-room-book-info-item">
                                                    <span class="fplace-room-book-info-icon">
                                                        <i class="fas fa-calendar-alt"></i>
                                                    </span>
                                                    <span class="fplace-room-book-info_title">
                                                        <?php echo esc_html( $check_in ); ?>
                                                    </span>
                                                </li>
                                            <?php endif; ?>
                                            <?php if( ! empty( $check_out ) ): ?>
                                                <li class="fplace-room-book-info-item">
                                                    <span class="fplace-room-book-info-icon">
                                                        <i class="fas fa-calendar-alt"></i>
                                                    </span>
                                                    <span class="fplace-room-book-info_title">
                                                        <?php echo esc_html( $check_out ); ?>
                                                    </span>
                                                </li>
                                            <?php endif; ?>
                                            <?php if( ! empty( $occupancy ) ): ?>
                                                <l class="fplace-room-book-info-item">
                                                    <span class="fplace-room-book-info-icon">
                                                        <i class="fas fa-user"></i>
                                                    </span>
                                                    <span class="fplace-room-book-info_title">
                                                        <?php echo esc_html( 'Standard Occupancy: ' . $occupancy ); ?>
                                                    </span>
                                                </l i>
                                            <?php endif; ?>
                                            <?php if( ! empty( $price ) ): ?>
                                                <li class="fplace-room-book-info-item">
                                                    <span class="fplace-room-book-info-icon">
                                                        <i class="fas fa-shopping-cart"></i>
                                                    </span>
                                                    <span class="fplace-room-book-info_title">
                                                        <?php echo "£" . "<span class='fplace-price'>{$price}</span>"; ?>
                                                    </span>
                                                </li>
                                            <?php endif; ?>
                                            <?php if( ! empty( $extra_price ) ): ?>
                                                <li class="fplace-room-book-info-item">
                                                    <input type="checkbox" class="fplace_extra_price_check" name="fplace_extra_price_check" value="<?php echo esc_attr( $extra_price ); ?>">
                                                    <label for="fplace_extra_price_check">
                                                        <?php _e("Book extra person @ £", "fplace"); ?>
                                                    </label>
                                                    <span class='fplace_extra_price'>
                                                        <?php echo esc_html( $extra_price ); ?>
                                                    </span>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                        <div class="fplace-room-book-info-footer">
                                            <h4 class="fplace-total-price">
                                                <?php echo 'Total £' . "<span class='fplace-total-amount'>{$price}</span>"; ?>
                                            </h4>
                                            <div>
                                                <a href="<?php echo esc_url( $book_url ); ?>" class="fplace-book-btn" data-id="<?php echo esc_attr( $id ); ?>" data-date="<?php echo esc_attr( $date ); ?>" data-price="<?php echo esc_attr( $price ); ?>">
                                                    <?php _e('Book room'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                $current_user = wp_get_current_user();
                ?>
                <div class="fplace-data-not-found">
                    <h3><?php _e('** Sorry - we don’t currently have any availability for these dates **', 'fplace-booking'); ?></h3>

                    <form id="wait-list-request-form">
                        <div>
                            <input type="hidden" id="waiting-date" value="<?php echo esc_attr( $date ); ?>">
                            <input type="hidden" id="customer-id" value="<?php echo esc_attr( $user_id ); ?>">
                            <input type="hidden" id="customer-email" value="<?php echo esc_attr( $current_user->user_email ); ?>">
                            <input type="hidden" id="customer-name" value="<?php echo esc_attr( $current_user->display_name ); ?>">
                        </div>
                        <button type="submit" class="fplace-red-btn fplace-request-waitlist"><?php _e('Please put me on the accommodation cancellation wait list', 'fplace-booking'); ?></button>
                    </form>
                </div>
                <?php
            endif;
            ?>
        </div>
        <?php
        wp_die();
    }

    /**
     * Get cancel booking
     */
    public function fplace_cancel_booking(){
        $date       = (string)$_POST['get_date'] ?? '';
        $booked_id  = $_POST['booked_id'] ? intval( $_POST['booked_id'] ) : '';
        $room_id    = $_POST['room_id'] ? intval( $_POST['room_id'] ) : '';

        if( empty( $date ) || empty( $booked_id ) ){
            echo "Sorry this is the invalid request";
            die(0);
        }

        check_ajax_referer('francescas_place_booking', 'nonce');

        /**
         * Update booking dates of room
         */
        $booking_dates      = get_field( 'booking_dates', $room_id );
        $new_booking_dates  = str_replace( $date .",", "", $booking_dates );
        update_field( 'booking_dates', $new_booking_dates, $room_id );

        /**
         * Canceled booking
         */
        update_post_meta( $booked_id, 'booking_status', 'Canceled');
        echo "<p style='color:red'><strong>Canceled</strong></p>";

        wp_die();
    }

    /**
     * Add waiting list
     */
    public function submit_wait_list_form() {
        $date = (string)$_POST['get_date'] ?? '';
        $user_id = intval( $_POST['user_id'] ) ?? '';
        $customer_name = $_POST['customer_name'] ?? '';
        $customer_email = $_POST['customer_email'] ?? '';
        
        if( empty( $date ) || empty( $customer_name ) ){
                echo json_encode( array('success' => false, 'message' => 'error') );
                wp_die();
        }

        $get_date = Helper::formattingDate($date);
        $arriving_date  = $get_date['arriving_date']    ?? '';
        $arriving_day   = $get_date['arriving_day']     ?? '';
        $departing_date = $get_date['departing_date']   ?? '';
        $departing_day  = $get_date['departing_day']    ?? '';

        $post_data = array(
            'post_title' => sanitize_text_field( $customer_name ) . " is waiting",
            'post_type' => 'fpb_wait_list',
            'post_status' => 'publish'
        );

        // Insert the post
        $post_id = wp_insert_post( $post_data );

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            $arriving       =   $arriving_day . ", " . $arriving_date;
            $departing      =   $departing_day . ", " . $departing_date;
            $waiting_date   =   $get_date['timestamp'] ?? '';

            //send mail to admin
            $admin_email = 'nazmun.sakib1042@gmail.com';
            $subject = "Francesca's Place Wait list";
            $message = $customer_name . " is waiting for booking cancellation";
            wp_mail( $admin_email, $subject, $message );

            //Set post meta 
            update_post_meta( $post_id, 'arriving_date', $arriving );
            update_post_meta( $post_id, 'departing_date', $departing );
            update_post_meta( $post_id, 'waiting_date',  $waiting_date );
            update_post_meta( $post_id, 'customer_email', $customer_email );
            update_post_meta( $post_id, 'status', 'waiting');

            echo json_encode( array('success' => true, 'post_id' => $post_id, 'message' => 'success' ) );
        } else {
            echo json_encode( array('success' => false, 'message' => 'error') );
        }

        wp_die();
    }

    /**
     * Remove waiting list
     */
    public function remove_from_wait_list() {
        $post_id = intval( $_POST['post_id'] ) ?? '';
        
        if( empty( $post_id ) ){
            echo json_encode( array('success' => false, 'message' => 'Something Wrong' ) );
            wp_die();
        }

        update_post_meta( $post_id, 'status', 'removed');
        echo json_encode( array('success' => true, 'post_id' => $post_id, 'message' => 'Remove form wait' ) );

        wp_die();
    }

}

new Ajax_Actions();


