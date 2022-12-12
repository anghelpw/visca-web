// general scripts

$(function() {
	var clicked_time = 0;

	$('#sort-items').on('click', function() {
		clicked_time++;

		$('#widget-items .col-lg').each(function() {
			var position = 'order-' + $(this).attr('position');

			if ( clicked_time % 2 == 0 ) {
				$(this).removeClass(position);

				$('.sort-asc').addClass('hidden');
				$('.sort-alpha').removeClass('hidden');

			} else {
				$(this).addClass(position);

				$('.sort-alpha').addClass('hidden');
				$('.sort-asc').removeClass('hidden');
			}
		});
	});

	$('#change-view').on('click', function() {
		if ( $(this).is(':checked') ) {
			$('#widget-items').addClass('vertical');

		} else {
			$('#widget-items').removeClass('vertical');
		}
	});
});
