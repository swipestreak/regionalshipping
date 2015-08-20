;(function($) {
	$('.order-form').on('change', 'select.region-code,.modifier-set-field select', function(e) {
		$('.order-form').entwine('sws').updateCart();
	});
})(jQuery);