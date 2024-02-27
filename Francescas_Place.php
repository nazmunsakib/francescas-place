<?php
/**
 * Plugin Main class
 *
 * @package FrancescasPlace
 */
namespace FrancescasPlace\Booking;

final class Francescas_Place {
    /**
	 * Plugin instance
	 */
    private static $instance = null;

	private function __construct() {
		add_action( 'init', [ $this, 'load_textdomain' ] );
	}

    /**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * @access public
     * 
     * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'francescas-place',
			false,
			dirname( FPB_PLUGIN_BASENAME ) . '/languages/'
		);
	}

    /**
	 * Plugin instence
	 *
	 * @access public
	 * @static
	 *
	 * @return \Francescas_Place
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

    /**
	 * Inisialize Plugin
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function init() {
		$this->includes();

		add_action('wp_enqueue_scripts', [$this, 'assets_enqueue'], 100);
		add_action('admin_enqueue_scripts', [$this, 'admin_assets_enqueue'], 100); 
	}

	public function includes() {
		include_once( FPB_DIR_PATH . 'includes/classes/Post_Types.php' );
		include_once( FPB_DIR_PATH . 'includes/classes/Shortcode.php' );
		include_once( FPB_DIR_PATH . 'includes/classes/Ajax_Actions.php' );
		include_once( FPB_DIR_PATH . 'includes/classes/Helper.php' );
		include_once( FPB_DIR_PATH . 'includes/metaboxes.php' );
		include_once( FPB_DIR_PATH . 'includes/functions.php' );

		if( is_admin() ){
			include_once( FPB_DIR_PATH . 'admin/Fplace_Admin.php' );
			include_once( FPB_DIR_PATH . 'admin/Fplace_Settings.php' );
		}
    }

	/**
	 * Enqueue Admin assets.
	 *
	 * @return void
	 */
	public function admin_assets_enqueue(){
		wp_enqueue_script(
			'francescas-place-booking',
			FPB_ASSETS . 'js/scripts.js',
			['jquery'],
			'1.0',
			true
		);

		//Localize scripts
		wp_localize_script(
			'francescas-place-booking',
			'fPlace',
			[
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce'    => wp_create_nonce('francescas_place_booking'),
			]
		);
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * Frontend assets handler will be used in widgets map
	 * to load widgets assets on demand.
	 *
	 * @return void
	 */
	public function assets_enqueue() {
	
		wp_enqueue_style(
			'jquery-ui',
			FPB_ASSETS . 'css/jquery-ui.css',
			null,
			'1.13.2'
		);

		wp_enqueue_style(
			'francescas-place-booking',
			FPB_ASSETS . 'css/style.css',
			null,
			FPB_PLUGIN_VERSION
		);

		wp_enqueue_script(
			'jquery-ui',
			FPB_ASSETS . 'js/jquery-ui.js',
			['jquery'],
			FPB_PLUGIN_VERSION,
			true
		);

		wp_enqueue_script(
			'francescas-place-booking',
			FPB_ASSETS . 'js/scripts.js',
			['jquery'],
			time(),
			true
		);

		//Localize scripts
		wp_localize_script(
			'francescas-place-booking',
			'fPlace',
			[
				'ajax_url' => admin_url('admin-ajax.php'),
				'site_url' => site_url('/'),
				'nonce'    => wp_create_nonce('francescas_place_booking'),
			]
		);

	}

}