<?php
$fPb_formId = get_option('booking_form_id') ?? 1;
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
    ?>
    <script type="text/javascript">
        sessionStorage.removeItem('priceData');
    </script>
    <?php
    return $confirmation;
}
add_filter( 'gform_confirmation_'.$fPb_formId.'', 'fplace_booking_confirmation', 10, 4 );

/**
 * Populate room title
 *
 * @param $form
 * 
 * @return array
 */
function fplace_populate_room_title( $form ) {
    $booking_id     = isset( $_GET['booking'] ) ? intval($_GET['booking']) : null;
    $room_title     = get_the_title( $booking_id );
    $customer_meta  = '';
    $first_name     = '';
    $last_name      = '';
    $customer_email = '';
    
    if( $booking_id && $room_title ){
        $customer = wp_get_current_user();
        if ( $customer instanceof WP_User ) {
            $first_name     = $customer->first_name;
            $last_name      = $customer->last_name;
            $customer_email = $customer->user_email;
            $customer_meta  = get_user_meta( $customer->ID );
        }
        foreach ( $form['fields'] as &$field ) {
            if ($field->type == 'name' && $field->id == 2 ) {
                if( isset(  $field->inputs[1]['defaultValue'] ) ){
                    $field->inputs[1]['defaultValue'] =  esc_html( $first_name . " " . $last_name ); 
                }
            }
            if ( $field->id == '10' ) {
                $field->defaultValue =  $customer_email;
            }

            if ( $field->id == '15' ) {
                $field->defaultValue =  $room_title;
            }
            
            if ( $field->id == '6' && isset( $customer_meta['baid'] ) ) {
                $baid = $customer_meta['baid'][0] ?? '';
                $field->defaultValue =  $baid;
            }
            
            if ( $field->id == '8' && isset( $customer_meta['mobile'] ) ) {
                $mobile = $customer_meta['mobile'][0] ?? '';
                $field->defaultValue = $mobile;
            }
            
            if ($field->type == 'address' && $field->id == 7 ) {
                if( isset(  $field->inputs[0]['defaultValue'] ) && isset( $customer_meta['address1'] ) ){
                    $address1 = $customer_meta['address1'][0] ?? '';
                    $field->inputs[1]['defaultValue'] =  $address1; 
                }
                if( isset(  $field->inputs[1]['defaultValue'] ) && isset( $customer_meta['address2'] ) ){
                    $address2 = $customer_meta['address2'][0] ?? '';
                    $field->inputs[2]['defaultValue'] =  $address2; 
                }
                if( isset( $field->inputs[2]['defaultValue'] ) && isset( $customer_meta['city'] ) ){
                    $city = $customer_meta['city'][0] ?? '';
                    $field->inputs[3]['defaultValue'] =  $city; 
                }
            }
        }
    }

    return $form;
}
add_filter( 'gform_pre_render_'.$fPb_formId.'', 'fplace_populate_room_title' );


/**
 * Function to schedule cron event for booking expire
 *
 */
function fplace_booking_cron_schedule() {
    if ( ! wp_next_scheduled( 'booking_expire_event' ) ) {
        wp_schedule_event( strtotime( 'today 12:00' ), 'daily', 'booking_expire_event' );
    }
}
add_action( 'wp', 'fplace_booking_cron_schedule' );

/**
 * update expire booking
 *
 */
function fplace_update_completed_bookings() {
    $args = array(
        'post_type' => 'fpb_booking', 
        'posts_per_page' => -1
    );

    $bookable_items_query = new WP_Query($args);

    if ( $bookable_items_query->have_posts() ) {
        while ( $bookable_items_query->have_posts() ) {
            $bookable_items_query->the_post();

            $post_id        = get_the_ID();
            $booked_dates   = get_post_meta( $post_id, 'booking_date', true );
            $room_id        = get_post_meta( $post_id, 'room_id', true ) ?? false;
            $current_date   = strtotime(date('Y-m-d'));
            $date_object    = strtotime(str_replace('/', '-', $booked_dates));

            if( $date_object < $current_date ) {
                $booking_status = get_post_meta( $post_id, 'booking_status', true );
                if( 'Active' == $booking_status ){
                    update_post_meta( $post_id, 'booking_status', 'Completed');
                }

                /**
                 * Update booking dates of room
                 */
                if( $room_id ){
                    $booking_dates      = get_field( 'booking_dates', $room_id );
                    $new_booking_dates  = str_replace( $booked_dates .",", "", $booking_dates );
                    update_field( 'booking_dates', $new_booking_dates, $room_id );
                }
            }
        }
        wp_reset_postdata();
    }
}
add_action( 'booking_expire_event', 'fplace_update_completed_bookings' );





