(function ($) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(document).ready(function () {
		$('#inputCompanyName').typeahead({
			source: function (query, result) {
				$.ajax({
					url: ajaxurl,
					data: { action: "call_api", nonce: nonce },
					dataType: "json",
					type: "POST"
				}).done(function (data) {
					console.log("test");
					result($.map(data, function (item) {
						return item;
					}));
				});
			}
		});

		var dataset = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: 'wp-admin/admin-ajax.php?action=search_company&name=%QUERY',
				wildcard: '%QUERY'
			}
		});

		$('#inputCompanyName').typeahead(null, {
			name: 'company-search',
			display: 'name',
			source: dataset
		
		});

		$('.typeahead').bind('typeahead:select', function(ev, suggestion) {
			console.log(suggestion);
			$("#inputBusinessId").val(suggestion.businessId);
			jQuery.ajax({
				type: "post",
				dataType: "json",
				url: 'wp-admin/admin-ajax.php',
				data: { action: "company_detail", bid: suggestion.businessId }
			}).done(function (e) {
				var ids= e.ids;
				ids.forEach(id => {
					if(id.idType == 'VAT') $("#inputVATNumber").val(id.idValue)
				});
				
				var address = e.addresses[0];
				$("#inputStreetAddress").val(address.street);
				$("#inputCity").val(address.city);
				$("#inputPostalCode").val(address.postalCode);
				


			});
		  });
		  
	});

})(jQuery);

//autoCom	

