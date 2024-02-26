<?php
/**
 * Plugin Name: Francesca's Place Booking
 * Description: Francesca's Place Booking plugin!
 * Plugin URI: https://nazmunsakib.com/
 * Author: Nazmun Sakib
 * Version: 1.0.0
 * Author URI: https://nazmunsakib.com/
 * Text Domain: francescas-place
 * Domain Path: /languages
 * 
 * @package FrancescasPlace
 */

defined('ABSPATH') || die();

/**
 * Defining plugin constants.
 *
 * @since 1.0.0
 */
define('FPB_PLUGIN_VERSION', time());

define('FPB_PLUGIN_FILE', __FILE__ );
define('FPB_DIR_PATH', plugin_dir_path( FPB_PLUGIN_FILE ) );
define('FPB_DIR_URL', plugin_dir_url( FPB_PLUGIN_FILE ) );
define('FPB_PLUGIN_BASENAME', plugin_basename( FPB_PLUGIN_FILE ) );
define('FPB_ASSETS', trailingslashit( FPB_DIR_URL . 'assets'));
define('FPB_ADMIN_URL', trailingslashit( FPB_DIR_URL . 'admin'));
define('FPB_ADMIN', FPB_DIR_PATH . 'admin/');

define('FPB_MINIMUM_ELEMENTOR_VERSION', '3.5.0');
define('FPB_MINIMUM_PHP_VERSION', '5.4');

/**
 * The journey starts here.
 *
 * @return void Some voids are not really void, you have to explore to figure out why not!
 */
function fpb_start(){
    /**
     * Check for required PHP version
     */
    if ( version_compare( PHP_VERSION, FPB_MINIMUM_PHP_VERSION, '<' ) ){
        add_action('admin_notices', 'fpb_admin_notice_required_php_version');
        return;
    }

    require FPB_DIR_PATH . 'Francescas_Place.php';
    \FrancescasPlace\Booking\Francescas_Place::instance();
}

add_action('plugins_loaded', 'fpb_start');

/**
 * Admin notice for required php version
 *
 * @return void
 */
function fpb_admin_notice_required_php_version() {
    $notice = sprintf(
        esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'francescas-place'),
        '<strong>' . esc_html__('Addons Kit Elementor', 'francescas-place') . '</strong>',
        '<strong>' . esc_html__('PHP', 'francescas-place') . '</strong>',
        FPB_MINIMUM_PHP_VERSION
    );

    printf( '<div class="notice notice-warning is-dismissible"><p style="padding: 13px 0">%1$s</p></div>', $notice );
}





