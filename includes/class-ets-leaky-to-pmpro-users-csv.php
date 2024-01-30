<?php
/**
 * Fired during plugin activation
 *
 * @link https://www.expresstechsoftwares.com/
 * @since      1.0.0
 *
 * @package    ets-leaky-paywall-to-pmpro
 * @subpackage ets-leaky-paywall-to-pmpro/includes
 */

/**
 * Class ETS_LeakyToPMPro_Users_CSV
 *
 * This class handles the generation of CSV files for user migration from Leaky Paywall to Paid Memberships Pro.
 *
 * @since      1.0.0
 * @package    ets-leaky-paywall-to-pmpro
 * @subpackage ets-leaky-paywall-to-pmpro/includes
 * @author     Your Name <your@email.com>
 */
class ETS_LeakyToPMPro_Users_CSV {

	 /**
	  * The single instance of the class.
	  *
	  * @var self
	  */
	private static $instance;


	/**
	 * Folder to save CSV
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $csv_folder   Folder name.
	 */
	private $csv_folder;

	/**
	 * The current mode obtained using leaky_paywall_get_current_mode().
	 *
	 * @var string
	 */
	public $mode;

	/**
	 * Main ETS_LeakyToPMPro_Users_CSV instance.
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return self Instance of the class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor for the class.
	 *
	 * Initializes the class and sets up the CSV folder.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->csv_folder = trailingslashit( wp_upload_dir()['basedir'] ) . ETS_LEAKY_TO_PMPRO_CSV_FOLDER;

		if ( ! is_dir( $this->csv_folder ) ) {
			wp_mkdir_p( $this->csv_folder );
			chmod( $this->csv_folder, 0777 );
		}
		$this->mode = $this->get_current_mode();
	}

	/***
	 *
	 *
	 */
	private function get_current_mode() {
		$settings = new Leaky_Paywall_Settings();
		$s        = $settings->get_settings();
		$mode     = 'off' === $s['test_mode'] ? 'live' : 'test';

		return $mode;
	}

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wp_ajax_ets_leaky_to_pmpro_generate_csv', array( $this, 'generate_csv' ), 10 );
	}

	/**
	 * Generate CSV file for user migration.
	 *
	 * Functions to use :
	 * - leaky_paywall_user_has_access()
	 * - leaky_paywall_subscriber_current_level_id() // Return level id for user
	 */
	public function generate_csv( $user_id = '' ) {
		global $wpdb;

		$mode = leaky_paywall_get_current_mode();

		$sql = $wpdb->prepare(
			"
            SELECT
                u.ID as user_id
            FROM
                {$wpdb->users} u
            JOIN
                {$wpdb->usermeta} um1 ON u.ID = um1.user_id
            JOIN
                {$wpdb->usermeta} um2 ON u.ID = um2.user_id
            WHERE
                (um1.meta_key = '_issuem_leaky_paywall_{$mode}_level_id' AND um1.meta_value LIKE %s)
                AND (um2.meta_key = '_issuem_leaky_paywall_{$mode}_payment_status' AND um2.meta_value = %s)
            ",
			'%1%',
			'active'
		);

		$active_members = $wpdb->get_col( $sql );

		$date     = date( 'd-m-y-' . substr( (string) microtime(), 1, 8 ) );
		$date     = str_replace( '.', '', $date );
		$filename = 'ets-leaky-pmpro-subscribers-' . $date . '.csv';
		$filePath = $this->csv_folder . '/' . $filename;
		$handle   = fopen( $filePath, 'w' );

		fputs( $handle, "\xEF\xBB\xBF" ); // UTF-8 BOM

		$headers = array(
			'user_login',
			'user_email',
			'user_pass',
			'first_name',
			'last_name',
			'display_name',
			'role',
			'membership_id',
			'membership_code_id',
			'membership_initial_payment',
			'membership_billing_amount',
			'membership_cycle_number',
			'membership_cycle_period',
			'membership_billing_limit',
			'membership_trial_amount',
			'membership_trial_limit',
			'membership_status',
			'membership_startdate',
			'membership_enddate',
			'membership_subscription_transaction_id',
			'membership_gateway',
			'membership_payment_transaction_id',
			'membership_affiliate_id',
			'membership_timestamp',
		);

		fputcsv( $handle, $headers );

		foreach ( $active_members as $user_id ) {
			// $user_id = $member[];
			$user = new WP_User( $user_id );
			if ( ! $user->exists() ) {
				continue;
			}

			$row = array(
				$user->user_login,
				$user->user_email,
				$user->user_pass,
				$user->first_name,
				$user->last_name,
				$user->display_name,
				$this->get_member_role( $user_id ),
				$this->get_membership_id( $user_id ),
				'',  // membership_code_id - not available in Leaky Paywall
				$this->get_membership_initial_payment( $user_id ),
				$this->get_membership_billing_amount( $user_id ),
				$this->get_membership_cycle_number(),
				$this->get_membership_cycle_period(),
				'',  // membership_billing_limit - not available in Leaky Paywall
				$this->get_membership_trial_amount(),
				'',  // membership_trial_limit - not available in Leaky Paywall
				$this->get_member_status( $user_id ),
				$this->get_membership_startdate( $user_id ),
				$this->get_membership_enddate( $user_id ),
				$this->get_membership_subscription_transaction_id( $user_id ),
				$this->get_membership_gateway(),
				$this->get_membership_payment_transaction_id( $user_id ),
				'',  // membership_affiliate_id - not available in Leaky Paywall
				$this->get_membership_timestamp( $user_id ),  // membership_timestamp - not available in Leaky Paywall
			);

			fputcsv( $handle, $row );

		}

		fclose( $handle );
		$upload_dir = wp_upload_dir();
		echo '<a href="' . $upload_dir['baseurl'] . '/' . ETS_LEAKY_TO_PMPRO_CSV_FOLDER . '/' . $filename . '"><span class="dashicons dashicons-download"></span>Download : ' . $filename . '</a>';

		exit();
	}

	/**
	 * Get Member first Role.
	 *
	 * @param int $user_id User ID.
	 *
	 * @return string Member role.
	 */
	private function get_member_role( $user_id ) {

		return 'subscriber';

		// $user_data = get_userdata( $user_id );
		// $role      = ! empty( $user_data->roles[0] ) ? $user_data->roles[0] : 'none';

		// return $role;
	}

	/**
	 * Get level ID for a user.
	 *
	 * @param int $user_id    User ID.
	 *
	 * @return int $level_id.
	 */
	private function get_membership_id( $user_id ) {

		$level_id = get_user_meta( $user_id, '_issuem_leaky_paywall_' . $this->mode . '_level_id', true );

		if ( is_numeric( $level_id ) ) {
			return $level_id;
		}
	}

	/**
	 * Get Price
	 *
	 * @param int $user_id User ID.
	 *
	 * @return float Initial payment amount.
	 */
	private function get_membership_initial_payment( $user_id ) {

		$price = get_user_meta( $user_id, '_issuem_leaky_paywall_' . $this->mode . '_price', true );
		 return $price;
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $user_id
	 * @return void
	 */
	private function get_membership_timestamp( $user_id ) {
		$created        = get_user_meta( $user_id, '_issuem_leaky_paywall_' . $this->mode . '_created', true );
		$timestamp      = strtotime( $created );
		$year           = date( 'Y', $timestamp );
		$month          = date( 'm', $timestamp );
		$day            = date( 'd', $timestamp );
		$formatted_date = $year . '-' . $month . '-' . $day;

		return $formatted_date;
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $user_id
	 * @return void
	 */
	private function get_membership_billing_amount( $user_id ) {
		$level_id    = $this->get_membership_id( $user_id );
		$allsettings = get_option( 'issuem-leaky-paywall' );
		$levels      = $allsettings['levels'];

		return $levels[ $level_id ]['price'];
	}

	/**
	 * Get membership cycle number.
	 *
	 * @return string
	 */
	private function get_membership_cycle_number() {

		return 1;
	}

	/**
	 * Get membership cycle period.
	 *
	 * @return string
	 */
	private function get_membership_cycle_period() {

		return 'Month';
	}

	/**
	 * Get membership billing limit.
	 *
	 * @return string
	 */
	private function get_membership_billing_limit() {

		return '';
	}

	/**
	 * Get membership trial amount.
	 *
	 * @return init
	 */
	private function get_membership_trial_amount() {

		return '-2.00';

	}

	/**
	 * Undocumented function
	 *
	 * @return string
	 */
	private function get_member_status( $user_id ) {
		$status = get_user_meta( $user_id, '_issuem_leaky_paywall_' . $this->mode . '_payment_status', true );
		return $status;

	}
	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	private function get_membership_startdate( $user_id ) {
		$created = get_user_meta( $user_id, '_issuem_leaky_paywall_' . $this->mode . '_created', true );
		update_option( 'leaky_date_created', $created );
		// $formatted_date = date( 'Y-m-d', strtotime( $created ) );
		$timestamp      = strtotime( $created );
		$year           = date( 'Y', $timestamp );
		$month          = date( 'm', $timestamp );
		$day            = date( 'd', $timestamp );
		$formatted_date = $year . '-' . $month . '-' . $day;
		return $formatted_date;

	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	private function get_membership_enddate( $user_id ) {
		$expires = get_user_meta( $user_id, '_issuem_leaky_paywall_' . $this->mode . '_expires', true );
		update_option( 'leaky_date_expires', $expires );
		// $formatted_date = date( 'Y-m-d', strtotime( $expires ) );
		$timestamp      = strtotime( $expires );
		$year           = date( 'Y', $timestamp );
		$month          = date( 'm', $timestamp );
		$day            = date( 'd', $timestamp );
		$formatted_date = $year . '-' . $month . '-' . $day;
		return $formatted_date;
	}


	/**
	 * Undocumented function
	 *
	 * @param [type] $user_id
	 * @return void
	 */
	private function get_membership_subscription_transaction_id( $user_id ) {
		$subscription_transaction_id = get_user_meta( $user_id, '_issuem_leaky_paywall_' . $this->mode . '_subscriber_id', true );

		return $subscription_transaction_id;
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	private function get_membership_gateway() {
		return 'stripe';
	}


	/**
	 * Get Leaky Paywall payment transaction ID for a user.
	 *
	 * @param int $user_id User ID.
	 * @return string|false Payment transaction ID or false if not found.
	 */
	private function get_membership_payment_transaction_id( $user_id ) {

		$entries = get_posts(
			array(
				'post_type'  => 'lp_transaction',
				'meta_query' => array(
					array(
						'key'   => '_email',
						'value' => get_userdata( $user_id )->user_email,
					),
				),
				'fields'     => 'ids',
				'orderby'    => 'post_date',
				'order'      => 'DESC',
			)
		);

		foreach ( $entries as $entry_id ) {
			$payment_transaction_id = get_post_meta( $entry_id, '_gateway_txn_id', true );

			if ( ! empty( $payment_transaction_id ) ) {
				return $payment_transaction_id;
			}
		}
		return '';
	}


}

// Initialize the class
ETS_LeakyToPMPro_Users_CSV::get_instance()->init();
