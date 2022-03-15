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
	
	const settings = {
		infinite: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: true,
		centerMode: true,
		swipeToSlide: true,
		speed: 2000,
		mobileFirst: true,
		cssEase: 'cubic-bezier(.19,1,.22,1)',
		responsive: [
			{
				breakpoint: 992,
				settings: "unslick"
			}
		]
	};
	
	const sl =  $('.js-slick-events').slick(settings);
		  
	$(window).on('resize', function() {
	   if( $(window).width() < 992 &&  !sl.hasClass('slick-initialized')) {
			 $('.js-slick-events').slick(settings);
		}
	});
	
}