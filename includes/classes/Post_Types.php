<?php
/**
 * Register Custom post type
 *
 * @package FrancescasPlace
 */
namespace FrancescasPlace\Booking;

defined('ABSPATH') || die();

class Post_Types{
    public function __construct(){
        add_action('init', array( $this,'register_post_type') );
    }

    /**
     * Register the post type.
     *
     * @return void
     */
    public function register_post_type() {
        $labels = [
            'name'               => _x( 'Rooms', 'Post Type General Name', 'francescas-place' ),
            'singular_name'      => _x( 'Room', 'Post Type Singular Name', 'francescas-place' ),
            'menu_name'          => __( 'Rooms', 'francescas-place' ),
            'parent_item_colon'  => __( 'Parent Room', 'francescas-place' ),
            'all_items'          => __( 'All Rooms', 'francescas-place' ),
            'view_item'          => __( 'View Room', 'francescas-place' ),
            'add_new_item'       => __( 'Add Room', 'francescas-place' ),
            'add_new'            => __( 'Add New', 'francescas-place' ),
            'edit_item'          => __( 'Edit Room', 'francescas-place' ),
            'update_item'        => __( 'Update Room', 'francescas-place' ),
            'search_items'       => __( 'Search Room', 'francescas-place' ),
            'not_found'          => __( 'Not Room found', 'francescas-place' ),
            'not_found_in_trash' => __( 'Not found in Trash', 'francescas-place' ),
        ];

        $args = [
            'labels'              => $labels,
            'supports'            => [ 'title', 'revisions', 'custom-fields' ],
            'hierarchical'        => true,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-building',
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'show_in_rest'        => true,
            'rewrite'             => false,
            'capability_type'     => 'post',
        ];

        register_post_type( 'rooms', $args );

        $labels = [
            'name'               => _x( 'Bookings', 'Post Type General Name', 'francescas-place' ),
            'singular_name'      => _x( 'Booking', 'Post Type Singular Name', 'francescas-place' ),
            'menu_name'          => __( 'Bookings', 'francescas-place' ),
            'parent_item_colon'  => __( 'Parent Booking', 'francescas-place' ),
            'all_items'          => __( 'All Bookings', 'francescas-place' ),
            'view_item'          => __( 'View Booking', 'francescas-place' ),
            'add_new_item'       => __( 'Add Booking', 'francescas-place' ),
            'add_new'            => __( 'Add New', 'francescas-place' ),
            'edit_item'          => __( 'Edit Booking', 'francescas-place' ),
            'update_item'        => __( 'Update Booking', 'francescas-place' ),
            'search_items'       => __( 'Search Booking', 'francescas-place' ),
            'not_found'          => __( 'Not Booking found', 'francescas-place' ),
            'not_found_in_trash' => __( 'Not found in Trash', 'francescas-place' ),
        ];

        $args = [
            'labels'              => $labels,
            'supports'            => [ 'title', 'revisions', 'custom-fields' ],
            'hierarchical'        => true,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-calendar-alt',
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'show_in_rest'        => true,
            'rewrite'             => false,
            'capability_type'     => 'post',
        ];

        register_post_type( 'fpb_booking', $args );

        $labels = [
            'name'               => _x( 'Wait List', 'Post Type General Name', 'francescas-place' ),
            'singular_name'      => _x( 'Wait List', 'Post Type Singular Name', 'francescas-place' ),
            'menu_name'          => __( 'Wait List', 'francescas-place' ),
            'parent_item_colon'  => __( 'Wait List', 'francescas-place' ),
            'all_items'          => __( 'All Wait List', 'francescas-place' ),
            'view_item'          => __( 'View Wait List', 'francescas-place' ),
            'add_new_item'       => __( 'Add Wait List', 'francescas-place' ),
            'add_new'            => __( 'Add New', 'francescas-place' ),
            'edit_item'          => __( 'Edit Wait List', 'francescas-place' ),
            'update_item'        => __( 'Update Wait List', 'francescas-place' ),
            'search_items'       => __( 'Search Wait List', 'francescas-place' ),
            'not_found'          => __( 'Not Wait List found', 'francescas-place' ),
            'not_found_in_trash' => __( 'Not found in Trash', 'francescas-place' ),
        ];

        $args = [
            'labels'              => $labels,
            'supports'            => [ 'title', 'revisions', 'custom-fields' ],
            'hierarchical'        => true,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-nametag',
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'show_in_rest'        => true,
            'rewrite'             => false,
            'capability_type'     => 'post',
        ];

        register_post_type( 'fpb_wait_list', $args );
    }

    
}

new Post_Types();