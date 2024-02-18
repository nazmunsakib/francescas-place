<?php
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
add_filter('acf/load_field/name=booking_dates', 'flace_disable_dates_field');

/**
 * Add new booking date on meta 
 *
 * @param $post_id
 * @param $new_date
 * 
 * @return void
 */
function fplace_get_booked( $post_id, $new_date){

    if( ! $post_id || ! $new_date ){
        return false;
    }

    $get_old_data = get_field('booking_dates', $post_id) ?? '';

    if( ! str_contains( $get_old_data, $new_date ) ){
        $new_value = (string)$get_old_data . $new_date . ", ";
        update_field( 'booking_dates', $new_value ,$post_id);
    }
}

/**
 * Booking confirmation
 *
 * @param $confirmation
 * @param $form
 * @param $entry
 * @param $ajax
 * 
 * @return void
 */
function fplace_booking_confirmation( $confirmation, $form, $entry, $ajax ) {

    $booking_id = $entry['13'] ?? '';
    $get_date   = $entry['14'] ?? '';

    //Set booking date
    if( $booking_id && $get_date ){
        fplace_get_booked( $booking_id, $get_date );
    }

    return $confirmation;
}
add_filter( 'gform_confirmation_1', 'fplace_booking_confirmation', 10, 4 );

/**
 * Populate room title
 *
 * @param $form
 * 
 * @return array
 */
function fplace_populate_room_title( $form ) {
    $booking_id = isset( $_GET['booking'] ) ? intval($_GET['booking']) : null;
    $room_title = get_the_title( $booking_id );

    if( $booking_id && $room_title ){
        foreach ( $form['fields'] as &$field ) {
            if ( $field->id == '15' ) {
                $field->defaultValue =  $room_title;
            }
        }
    }

    return $form;
}
add_filter( 'gform_pre_render_1', 'fplace_populate_room_title' );

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
        'booked_date'       => __( 'Booked Date', 'fplace-booking' ),
        'customer_info'     => __( 'Customer Info', 'fplace-booking' ),
        'address'             => __( 'Address', 'fplace-booking' ),
    );

    $offset  = array_search( 'date', array_keys( $columns ) ); // Set custom columns position before "DATE" column
    $columns = array_slice( $columns, 0, $offset, true ) + $customColumns + array_slice( $columns, $offset, count( $columns ) - 1, true );

    unset( $columns['date'] );
    return $columns;
}
add_filter( "manage_fpb_booking_posts_columns", 'fplace_booking_columns' );

function render_booking_columns( $column, $postId ) {
    $booking_date   = get_post_meta( $postId, 'booking_date', true );

    switch ( $column ) {
        case 'status':
            $current_date   = strtotime(date('Y-m-d'));
            $date_object    = strtotime(str_replace('/', '-',  $booking_date ));
            $booking_status = get_post_meta( $postId, 'booking_status', true );

            $statusElement = '';
            if( $date_object < $current_date ) {
                $statusElement .= "<p style='color:red'><strong>Expired</strong></p>";
            }else{
                $statusElement .= "<p style='color:green'><strong>Active</strong></p>";
            }

            echo $statusElement;

            break;
        case 'booked_date':

            echo "<p>{$booking_date }</p>";

            break;
        case 'customer_info':
            $customer_name      = get_post_meta( $postId, 'customer_name', true );
            $customer_phone     = get_post_meta( $postId, 'customer_phone', true );
            $customer_email     = get_post_meta( $postId, 'customer_email', true );
            $customer_tel       = get_post_meta( $postId, 'customer_tel', true );

            echo "<p><strong>Name</strong>: {$customer_name}</p>";
            echo "<p><strong>Phone</strong>: {$customer_phone}</p>";
            echo "<p><strong>Tel</strong>: {$customer_tel}</p>";
            echo "<p><strong>Email</strong>: {$customer_email}</p>";
            
            break;
        case 'address':
            $address_line_1     = get_post_meta( $postId, 'customer_address_line_1', true );
            $address_line_2     = get_post_meta( $postId, 'customer_address_line_1', true );
            $customer_postcode  = get_post_meta( $postId, 'customer_postcode', true );
            $customer_country   = get_post_meta( $postId, 'customer_country', true );
            $customer_town_city = get_post_meta( $postId, 'customer_town_city', true );

            echo "<p><strong>Address</strong>: {$address_line_1 }, {$address_line_2}</p>";
            echo "<p><strong>Postcode</strong>: {$customer_postcode}</p>";
            echo "<p><strong>City</strong>: {$customer_town_city}</p>";
            echo "<p><strong>Country</strong>: {$customer_country}</p>";

            break;
    }
}
add_action( "manage_fpb_booking_posts_custom_column", 'render_booking_columns', 10, 2 );

function update_expired_bookings() {
    $args = array(
        'post_type' => 'fpb_booking', 
        'posts_per_page' => -1
    );

    $bookable_items_query = new WP_Query($args);

    if ( $bookable_items_query->have_posts() ) {
        while ( $bookable_items_query->have_posts() ) {
            $bookable_items_query->the_post();

            $post_id = get_the_ID();
            $current_date   = strtotime(date('Y-m-d'));
            $booked_dates   = get_post_meta( $post_id, 'booking_date', true );
            $date_object    = strtotime(str_replace('/', '-', $booked_dates));

            if( $date_object < $current_date ) {
                echo "Sorry! this is the invalid request.";
            }
        }
        wp_reset_postdata();
    }
}

// Schedule the cron job to run update_expired_bookings() function daily
// add_action('update_expired_bookings_cron', 'update_expired_bookings');
// if (!wp_next_scheduled('update_expired_bookings_cron')) {
//     wp_schedule_event(time(), 'daily', 'update_expired_bookings_cron');
// }

