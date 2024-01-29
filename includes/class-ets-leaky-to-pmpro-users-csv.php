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
        $s = $settings->get_settings();
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
    public function generate_csv() {
        global $wpdb;
        $mode      = $this->mode;

        $sql = $wpdb->prepare(
            "
            SELECT
                u.ID as user_id,
                u.user_login,
                u.user_email,
                um.meta_key,
                um.meta_value
            FROM
                {$wpdb->users} u
            JOIN
                {$wpdb->usermeta} um ON u.ID = um.user_id
            WHERE
                um.meta_key LIKE %s
                AND um.meta_key LIKE %s
                AND um.meta_value = %s
                AND um.meta_key LIKE %s
            ",
            '_issuem_leaky_paywall_%',
            '%_payment_status',
            'active',
            '_issuem_leaky_paywall_' . $mode . '%'
        );

        $results = $wpdb->get_results($sql, ARRAY_A);

        $date     = date( 'd-m-y-' . substr( ( string ) microtime(), 1, 8 ) );
        $date     = str_replace( '.', '', $date );
        $filename = 'ets-leaky-pmpro-subscribers-' . $date . '.csv';
        $filePath = $this->csv_folder . '/' . $filename;
        $handle   = fopen( $filePath, 'w' );

        fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

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

        foreach ($results as $result) {
            $user_id = $result['user_id'];
            $user                     = new WP_User( $user_id );
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
                $this->get_membership_timestamp(),
                $this->get_membership_billing_amount(),
                $this->get_membership_cycle_number(),
                $this->get_membership_cycle_period(),
                '',  // membership_billing_limit - not available in Leaky Paywall
                '',  // membership_trial_amount - not available in Leaky Paywall
                '',  // membership_trial_limit - not available in Leaky Paywall
                $this->get_member_status( $user_id ),
                $this->get_membership_startdate(),
                $this->get_membership_enddate(),
                $this->get_membership_timestamp(),
                $this->get_membership_subscription_transaction_id(),
                $this->get_membership_gateway(),
                $this->get_membership_payment_transaction_id(),
                '',  // membership_affiliate_id - not available in Leaky Paywall
                '',  // membership_timestamp - not available in Leaky Paywall
            );
            

            
            fputcsv( $handle, $row );

        }

        fclose($handle);
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
	 * @param int           $user_id    User ID.
	 *
	 * @return int $level_id.
	 */
	private function get_membership_id( $user_id  ) {

        $level_id = get_user_meta( $user_id, '_issuem_leaky_paywall_' . $this->mode . '_level_id' , true );

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
    private function get_membership_timestamp( $user_id ){
        $created = get_user_meta( $user_id, '_issuem_leaky_paywall_' . $this->mode . '_created', true );
        return $created;
    }

    /**
     * Undocumented function
     *
     * @param [type] $user_id
     * @return void
     */
    private function get_membership_billing_amount( $user_id ) {
        $level_id = $this->get_membership_id( $user_id );
        $allsettings = get_option( 'issuem-leaky-paywall' );
        $levels = $allsettings['levels'];

        return $levels[ $level_id ]['price'];
    }

}

// Initialize the class
ETS_LeakyToPMPro_Users_CSV::get_instance()->init();
