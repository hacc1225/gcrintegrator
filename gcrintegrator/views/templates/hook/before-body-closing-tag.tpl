{if $BadgeVisibleGlobally}
<!-- BEGIN MerchantWidget Code -->
<script id='merchantWidgetScript'
	src="https://www.gstatic.com/shopping/merchant/merchantwidget.js"
	defer>
</script>
<script type="text/javascript">
	merchantWidgetScript.addEventListener('load', function () {
		merchantwidget.start({
			merchant_id: {$MERCHANT_ID},
			position: "{$POSITION}"
		});
	});
</script>
<!-- END MerchantWidget Code -->
{else}
<script src="https://apis.google.com/js/platform.js" async defer></script>
{/if}
