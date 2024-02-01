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


?>


<div class="ets-leaky-to-pmpro-container">
	<h1 class="ets-leaky-to-pmpro-title"><?php esc_html_e( 'Leaky Paywall to PMPro Migration', 'ets-leaky-to-pmpro' ); ?></h1>
	<div id="ets-leaky-to-pmpro-message"></div>
<button id="ets-leaky-to-pmpro-generate-csv" class="button button-primary ets-leaky-to-pmpro-generate-csv"><?php esc_html_e( 'Generate CSV ( Premium )', 'ets-leaky-to-pmpro' ); ?></button>
<button id="ets-leaky-to-pmpro-generate-csv-digital-access" class="button button-primary ets-leaky-to-pmpro-generate-csv"><?php esc_html_e( 'Generate CSV ( Digitital Access )', 'ets-leaky-to-pmpro' ); ?></button>
<hr>
<button id="ets-leaky-to-pmpro-check-premium" class="button button-primary ets-leaky-to-pmpro-generate-csv"><?php esc_html_e( 'Check Premium Members', 'ets-leaky-to-pmpro' ); ?></button>

<div id="ets-leaky-to-pmpro-spinner" class="ets-leaky-to-pmpro-spinner"></div>

</div>

