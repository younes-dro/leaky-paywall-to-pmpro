<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    Ets_Leaky_To_Pmpro
 * @subpackage Ets_Leaky_To_Pmpro/admin/partials
 */


 $allsettings = get_option( 'issuem-leaky-paywall' );
$levels = $allsettings['levels'];
echo '<pre>';
var_dump( $levels);
echo '</pre>';
?>


<div class="ets-leaky-to-pmpro-container">
    <h1 class="ets-leaky-to-pmpro-title"><?php esc_html_e( 'Leaky Paywall to PMPro Migration', 'ets-leaky-to-pmpro' ); ?></h1>
    <div id="ets-leaky-to-pmpro-message"></div>
<button id="ets-leaky-to-pmpro-generate-csv" class="button button-primary"><?php esc_html_e( 'Generate CSV', 'ets-leaky-to-pmpro' ); ?></button>
<div id="ets-leaky-to-pmpro-spinner" class="ets-leaky-to-pmpro-spinner"></div>
</div>

