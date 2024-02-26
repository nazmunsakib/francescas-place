<?php
/**
 * Ajax actions
 *
 * @package FrancescasPlace
 */
namespace FrancescasPlace\Booking\Admin;

defined('ABSPATH') || die();

use \FrancescasPlace\Booking\Helper;

class Fplace_Admin{

    public function __construct(){
        add_action( "manage_fpb_booking_posts_custom_column", [$this, 'render_booking_columns'], 10, 2 );
        add_filter( "manage_fpb_booking_posts_columns", [$this, 'fplace_booking_columns'] );
        add_action( "manage_fpb_wait_list_posts_custom_column", [$this, 'render_wait_list_columns'], 10, 2 );
        add_filter( "manage_fpb_wait_list_posts_columns", [$this, 'fplace_wait_list_columns'] );
        add_filter('acf/load_field/name=booking_dates', [$this, 'flace_disable_dates_field'] );
        add_action( 'add_meta_boxes', [$this, 'fplace_add_meta_boxes'] );
        add_action('admin_menu', [$this, 'fplace_wait_list_submenu_of_booking'] );
        add_action('pre_get_posts', [$this,'booking_admin_order'] );
    }

    /**
     * Disable dates field
     *
     * @param $field
     * 
     * @return void
     */
    function flace_disable_dates_field( $field ) {
        $field['disabled'] = 0;
        return $field;
    }

    /**
     * Manage Booking columns
     *
     * @param $columns
     * 
     * @return void
     */
    public function fplace_booking_columns( $columns ) {

        $customColumns = array(
            'status'            => __( 'Status', 'fplace-booking' ),
            'booked_info'       => __( 'Booked Info', 'fplace-booking' ),
            'customer_info'     => __( 'Customer Info', 'fplace-booking' ),
            'address'           => __( 'Address', 'fplace-booking' ),
        );

        $offset  = array_search( 'date', array_keys( $columns ) ); // Set custom columns position before "DATE" column
        $columns = array_slice( $columns, 0, $offset, true ) + $customColumns + array_slice( $columns, $offset, count( $columns ) - 1, true );

        unset( $columns['date'] );
        return $columns;
    }

    /**
     * Rander Booking custom columns
     *
     * @param $columns 
     * @param $postId 
     * 
     * @return void
     */
    public function render_booking_columns( $column, $postId ) {
        $booking_date   = get_post_meta( $postId, 'booking_date', true );
        $room_id        = get_post_meta( $postId, 'room_id', true );

        switch ( $column ) {
            case 'status':
                $current_date   = strtotime(date('Y-m-d'));
                $date_object    = strtotime(str_replace('/', '-',  $booking_date ));
                $booking_status = get_post_meta( $postId, 'booking_status', true );

                $statusElement = '';
                if( 'Active' ==  $booking_status ) {
                    $statusElement .= "<div>";
                    $statusElement .= "<p style='color:green'><strong>{$booking_status}</strong></p>";
                    $statusElement .= "<a href='' class='fplace-cancel-booking' style='color:red' data-bookedId='{$postId}' data-bookingDate='{$booking_date}' data-roomId='{$room_id}'>Cancel</a>";
                    $statusElement .= "</div>";
                }else if( 'Completed' ==  $booking_status ){
                    $statusElement .= "<p style='color:blue'><strong>{$booking_status}</strong></p>";
                }else{
                    $statusElement .= "<p style='color:red'><strong>{$booking_status}</strong></p>";
                }
                echo  $statusElement;

                break;
            case 'booked_info':
                $price_total = get_post_meta( $postId, 'price_total', true );
                echo "<p><strong>Date</strong>: {$booking_date}</p>";
                echo "<p><strong>Total Price</strong>: £".$price_total."</p>";

                break;
            case 'customer_info':
                $customer_name      = get_post_meta( $postId, 'customer_name', true );
                $customer_phone     = get_post_meta( $postId, 'customer_phone', true );
                $customer_email     = get_post_meta( $postId, 'customer_email', true );
                $customer_tel       = get_post_meta( $postId, 'customer_tel', true );

                echo "<p><strong>Name</strong>: {$customer_name}</p>";
                echo "<p><strong>Phone</strong>: {$customer_phone}</p>";
                echo "<p><strong>Email</strong>: {$customer_email}</p>";
                
                break;
            case 'address':
                $address_line_1     = get_post_meta( $postId, 'customer_address_line_1', true );
                $address_line_2     = get_post_meta( $postId, 'customer_address_line_1', true );
                $customer_postcode  = get_post_meta( $postId, 'customer_postcode', true );
                $customer_country   = get_post_meta( $postId, 'customer_country', true );
                $customer_town_city = get_post_meta( $postId, 'customer_town_city', true );

                echo "<p><strong>Address</strong>: {$address_line_1 }, {$address_line_2}</p>";
                echo "<p><strong>City</strong>: {$customer_town_city}</p>";
                echo "<p><strong>Country</strong>: {$customer_country}</p>";

                break;
        }
    }

    /**
     * Wait list columns
     *
     * @param $columns
     * 
     * @return void
     */
    public function fplace_wait_list_columns( $columns ) {

        $customColumns = array(
            'status'            => __( 'Status', 'fplace-booking' ),
            'customer_info'     => __( 'Customer Info', 'fplace-booking' ),
            'waiting_date'           => __( 'Wait date', 'fplace-booking' ),
        );

        $offset  = array_search( 'date', array_keys( $columns ) ); // Set custom columns position before "DATE" column
        $columns = array_slice( $columns, 0, $offset, true ) + $customColumns + array_slice( $columns, $offset, count( $columns ) - 1, true );

        return $columns;
    }

    /**
     * Rander Booking custom columns
     *
     * @param $columns 
     * @param $postId 
     * 
     * @return void
     */
    function render_wait_list_columns( $column, $postId ) {
        switch ( $column ) {
            case 'status':
                $status = get_post_meta( $postId, 'status', true );
                if( 'removed' == $status ){
                    echo "<p style='color:red'>{$status}</p>";
                }else{
                    echo "<p style='color:green'>{$status}</p>";
                }
                break;
            case 'customer_info':
                $customer_email     = get_post_meta( $postId, 'customer_email', true );
                echo "<p><strong>Email</strong>: {$customer_email}</p>";
                
                break;
            case 'waiting_date':
                $arriving_date     = get_post_meta( $postId, 'arriving_date', true );
                $departing_date     = get_post_meta( $postId, 'departing_date', true );
                echo "<p><strong>Arriving</strong>: {$arriving_date}</p>";
                echo "<p><strong>Departing</strong>: {$departing_date}</p>";

                break;
        }
    }

    /**
    * add meta box on booking
    * 
    * @return void
    */
    public function fplace_add_meta_boxes() {
        add_meta_box( 'fplace-booking-details', __( 'Booking details', 'francescas-place' ), array( $this, 'fplace_booking_details' ), 'fpb_booking' );
    }
    
    /**
    * render meta box on booking
    * 
    * @return void
    */
    public function fplace_booking_details() {
        $postId             = get_the_ID();
        $baid               = get_post_meta( $postId, 'customer_ba_id', true );
        $booking_date       = get_post_meta( $postId, 'booking_date', true );
        $price_total        = get_post_meta( $postId, 'price_total', true );
        $customer_name      = get_post_meta( $postId, 'customer_name', true );
        $customer_phone     = get_post_meta( $postId, 'customer_phone', true );
        $customer_email     = get_post_meta( $postId, 'customer_email', true );
        $customer_tel       = get_post_meta( $postId, 'customer_tel', true );
        $address_line_1     = get_post_meta( $postId, 'customer_address_line_1', true );
        $address_line_2     = get_post_meta( $postId, 'customer_address_line_1', true );
        $customer_postcode  = get_post_meta( $postId, 'customer_postcode', true );
        $customer_country   = get_post_meta( $postId, 'customer_country', true );
        $customer_town_city = get_post_meta( $postId, 'customer_town_city', true );
        ?>
        <div class="fplace-booking-details">
            <p>Name: <?php echo esc_html( $customer_name ); ?></p>
            <p>BA ID: <?php echo esc_html( $baid ); ?></p>
            <p>Phone: <?php echo esc_html( $customer_phone ); ?></p>
            <p>Tel: <?php echo esc_html( $customer_tel ); ?></p>
            <p>Email: <?php echo esc_html($customer_email ); ?></p>
            <p>Address: <?php echo esc_html( $address_line_1 . ", " . $address_line_2 ); ?></p>
            <p>Postcode: <?php echo esc_html( $customer_postcode ); ?></p>
            <p>City: <?php echo esc_html( $customer_town_city ); ?></p>
            <p>Country: <?php echo esc_html( $customer_country ); ?></p>
            <p>Booking date: <?php echo esc_html( $booking_date ); ?></p>
            <p>Price: <?php echo esc_html( $price_total ); ?></p>
        </div>
        <?php
    }

    /**
    * add Wait list as a submenu of booking post type
    * 
    * @return void
    */
    public function fplace_wait_list_submenu_of_booking() {
        add_submenu_page(
            'edit.php?post_type=fpb_booking',
            'Wait List',
            'Wait List',
            'manage_options',
            'edit.php?post_type=fpb_wait_list',
            ''
        );
    }

    /**
    * Booking admin order
    * 
    * @return void
    */
    public function booking_admin_order( $query ) {
        global $pagenow;

        if( is_admin() && $pagenow == 'edit.php' && isset( $query->query['post_type'] ) && ( $query->query['post_type'] == 'fpb_booking' || $query->query['post_type'] == 'fpb_wait_list') ) {
            $query->set('orderby', 'date');
            $query->set('order', 'DESC');
        }
    }

}

new Fplace_Admin();
