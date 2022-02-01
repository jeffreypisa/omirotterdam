import $ from "jquery";
import lity from 'lity';

export function lity_init() {
	$(document).on('click', '[data-lightbox]', lity);
}