<!-- BEGIN GCR Opt-in Module Code -->
<script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer></script>
<script>
window.renderOptIn = function() {
	window.gapi.load('surveyoptin', function() {
		window.gapi.surveyoptin.render(
		{
			// REQUIRED
			"merchant_id": "{$MERCHANT_ID}",
			"order_id": "{$ORDER_ID}",
			"email": "{$CUSTOMER_EMAIL}",
			"delivery_country": "{$COUNTRY_CODE}",
			"estimated_delivery_date": "{$EstimatedDeliveryDate}",
			// OPTIONAL
			{if $GtinProvided}
			"products":[{foreach $GTINs as $GTIN}{ldelim}"gtin": "{$GTIN}"{rdelim}{if not $GTINs@last}, {/if}{/foreach}],
			{/if}
			"opt_in_style": "{$OPT_IN_STYLE}"
		});
	});
}
</script>
<!-- END GCR Opt-in Module Code -->
