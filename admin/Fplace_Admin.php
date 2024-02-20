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
        add_filter('acf/load_field/name=booking_dates', [$this, 'flace_disable_dates_field'] );
    }

    /**
     * Disable dates field
     *
     * @param $field
     * 
     * @return void
     */
    function flace_disable_dates_field( $field ) {
        $field['disabled'] = 1;
        return $field;
    }

    /**
     * Manage Booking columns
     *
     * @param $columns
     * 
     * @return void
     */
    function fplace_booking_columns( $columns ) {

        $customColumns = array(
            'status'            => __( 'Status', 'fplace-booking' ),
            'booked_info'       => __( 'Booked Info', 'fplace-booking' ),
            'customer_info'     => __( 'Customer Info', 'fplace-booking' ),
            'address'             => __( 'Address', 'fplace-booking' ),
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
    function render_booking_columns( $column, $postId ) {
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

}

new Fplace_Admin();
