<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    Ets_Leaky_To_Pmpro
 * @subpackage Ets_Leaky_To_Pmpro/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ets_Leaky_To_Pmpro
 * @subpackage Ets_Leaky_To_Pmpro/admin
 * @author     ExpressTech Softwares Solutions Pvt Ltd <contact@expresstechsoftwares.com>
 */
class Ets_Leaky_To_Pmpro_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ets_Leaky_To_Pmpro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ets_Leaky_To_Pmpro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ets-leaky-to-pmpro-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ets_Leaky_To_Pmpro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ets_Leaky_To_Pmpro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ets-leaky-to-pmpro-admin.js', array( 'jquery' ), $this->version, false );
		$script_params = array(
			'admin_ajax'                     => admin_url( 'admin-ajax.php' ),
			'is_admin'                       => is_admin(),
			'ets_leaky_to_pmpro_nonce' => wp_create_nonce( 'ets-leaky-to-pmpro--ajax-nonce' ),
		);
		wp_localize_script( $this->plugin_name, 'ets_leaky_to_pmpro_js_params', $script_params );

	}

	/**
	 * Function to register the submenu page under Tolls
	 *
	 * @return void
	 */
	public function ets_leaky_to_pmpro_submenu() {
		add_submenu_page(
			'tools.php',
			esc_html__( 'Leaky Paywall to PMPro Migrator', 'ets-leaky-to-pmpro' ),
			esc_html__( 'Leaky Paywall to PMPro Migrator', 'ets-leaky-to-pmpro' ),
			'manage_options',
			esc_html__( 'leaky-to-pmpro-migrator', 'ets-leaky-to-pmpro' ),
			array( $this, 'ets_leaky_to_pmpro_page' )
		);
	}

	/**
	 * Callback function to display the page content
	 *
	 * @return void
	 */
	public function ets_leaky_to_pmpro_page() {
		wp_enqueue_style( $this->plugin_name );
		wp_enqueue_script( $this->plugin_name );

		require_once ETS_LEAKY_TO_PMPRO_PLUGIN_DIR_PATH . 'admin/partials/ets-leaky-to-pmpro-admin-display.php';
	}	

}
