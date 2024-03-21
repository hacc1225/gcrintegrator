{if $BadgeVisibleGlobally}
<!-- BEGIN GCR Badge Code -->
<script src="https://apis.google.com/js/platform.js?onload=renderBadge"  async defer></script>
<script>
window.renderBadge = function() {
	var ratingBadgeContainer = document.createElement("div");
	document.body.appendChild(ratingBadgeContainer);
	window.gapi.load('ratingbadge', function() {
		window.gapi.ratingbadge.render(
			ratingBadgeContainer, {
				// REQUIRED
				"merchant_id": {$MERCHANT_ID},
				// OPTIONAL
				"position": "{$POSITION}"
			}
		);
	});
}
</script>
<!-- END GCR Badge Code -->
{else}
<script src="https://apis.google.com/js/platform.js" async defer></script>
{/if}
