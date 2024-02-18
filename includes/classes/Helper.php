<?php
/**
 * Register Custom post type
 *
 * @package FrancescasPlace
 */
namespace FrancescasPlace\Booking;

defined('ABSPATH') || die();

class Helper{

    /**
     * String to date formate
     */
    public static function formattingDate( $date = null ){

        if( ! $date ){
            return false;
        }

        $date_object    = strtotime(str_replace('/', '-', $date));
        $next_date      = strtotime('+1 day', $date_object);
        $arriving_date  = date('F d, Y', $date_object);
        $arriving_day   = date('l', $date_object);
        $departing_date = date('F d, Y', $next_date);
        $departing_day  = date('l', $next_date);

        return [
            'arriving_date' => $arriving_date,
            'arriving_day'  => $arriving_day,
            'departing_date'=> $departing_date,
            'departing_day' => $departing_day,
        ];
    }
    
}
