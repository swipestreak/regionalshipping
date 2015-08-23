;(function($) {
	$('.order-form').on('change', 'select.region-code', function (e) {
		$('.order-form').entwine('sws').updateCart();
	});
})(jQuery);