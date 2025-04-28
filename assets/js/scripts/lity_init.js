import $ from 'jquery';
import lity from 'lity';

export function lity_init() {
	// Basis binding voor standaard links
	$(document).on('click', '[data-lightbox]', function (e) {
		e.preventDefault();
		lity($(this).attr('href'));
	});

	// Extra binding voor content die via Bootstrap popover verschijnt
	$(document).on('shown.bs.popover', function () {
		$('[data-lightbox]').off('click').on('click', function (e) {
			e.preventDefault();
			lity($(this).attr('href'));
		});
	});
}