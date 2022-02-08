import $ from "jquery";
import 'slick-carousel';

export function slick_init() {	
	
	$('.js-slick').slick({
		infinite: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: true,
		centerMode: true,
		swipeToSlide: true,
		speed: 2000,
		cssEase: 'cubic-bezier(.19,1,.22,1)'
	});
	
}