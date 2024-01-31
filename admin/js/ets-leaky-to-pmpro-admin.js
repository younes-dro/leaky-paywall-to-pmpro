(function( $ ) {
	'use strict';
	jQuery(document).ready(function($){

		// console.log(ets_leaky_to_pmpro_js_params);
	
			$('#ets-leaky-to-pmpro-generate-csv').on('click', function (e) {
				e.preventDefault();
				var $this = $(this);
				$.ajax({
					url: ets_leaky_to_pmpro_js_params.admin_ajax,
					type: "POST",
					context: this,
					data: {
						'action': 'ets_leaky_to_pmpro_generate_csv',
						'ets_leaky_to_pmpro_nonce': ets_leaky_to_pmpro_js_params.ets_leaky_to_pmpro_nonce
					},
					beforeSend: function () {
						$this.attr("disabled", true);
						$this.siblings('#ets-leaky-to-pmpro-spinner').addClass('loading');
					},
					success: function (data) {
						// console.log(data);
						$('#ets-leaky-to-pmpro-message').html(data);
					},
					error: function (response, textStatus, errorThrown) {
						console.log(textStatus + " :  " + response.status + " : " + errorThrown);
					},
					complete: function () {
						$this.attr("disabled", false);
						$this.siblings('#ets-leaky-to-pmpro-spinner').removeClass('loading');
					},
				});
			});
			$('#ets-leaky-to-pmpro-generate-csv-digital-access').on('click', function (e) {
				e.preventDefault();

				var $this = $(this);
				$.ajax({
					url: ets_leaky_to_pmpro_js_params.admin_ajax,
					type: "POST",
					context: this,
					data: {
						'action': 'ets_leaky_to_pmpro_generate_csv_digital_access',
						'ets_leaky_to_pmpro_nonce': ets_leaky_to_pmpro_js_params.ets_leaky_to_pmpro_nonce
					},
					beforeSend: function () {
						$this.attr("disabled", true);
						$this.siblings('#ets-leaky-to-pmpro-spinner').addClass('loading');
					},
					success: function (data) {
						// console.log(data);
						$('#ets-leaky-to-pmpro-message').html(data);
					},
					error: function (response, textStatus, errorThrown) {
						console.log(textStatus + " :  " + response.status + " : " + errorThrown);
					},
					complete: function () {
						$this.attr("disabled", false);
						$this.siblings('#ets-leaky-to-pmpro-spinner').removeClass('loading');
					},
				});
			});			
		

	});

})( jQuery );
