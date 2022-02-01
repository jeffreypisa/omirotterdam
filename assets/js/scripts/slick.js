import $ from "jquery";
import 'slick-carousel';

export function slick_init() {	
	
	$('.js-slick').slick({
		infinite: false,
	slidesToShow: 3,
	slidesToScroll: 1,
	arrows: false,
		centerMode: false,
		swipeToSlide: true,
		cssEase: 'cubic-bezier(.19,1,.22,1)',
	responsive: [
	  {
		breakpoint: 992,
		settings: {
		  slidesToShow: 2,
		  slidesToScroll: 1
		}
	  },
	  {
		breakpoint: 768,
		settings: {
		  slidesToShow: 1,
		  slidesToScroll: 1
		}
	  }
	]
	});
	
	$('.js-slick-2rows').slick({
		infinite: false,
	slidesToShow: 3,
	slidesToScroll: 1,
	arrows: false,
	centerMode: false,
	swipeToSlide: true,
		cssEase: 'cubic-bezier(.19,1,.22,1)',
	rows: 2,
	responsive: [
	  {
		breakpoint: 992,
		settings: {
		  slidesToShow: 2,
		  slidesToScroll: 1,
		  rows: 1
		}
	  },
	  {
		breakpoint: 768,
		settings: {
		  slidesToShow: 1,
		  slidesToScroll: 1,
		  rows: 1
		}
	  }
	]
	});
	
	$('.js-slick-reacties').slick({
		infinite: true,
	slidesToShow: 1,
	slidesToScroll: 1,
	arrows: false,
		centerMode: false,
		swipeToSlide: true,
		autoplay: true,
	autoplaySpeed: 6000,
	fade: true,
		cssEase: 'cubic-bezier(.19,1,.22,1)'
	});
	
	$('.js-slick-projecten').slick({
		infinite: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: true,
		dots: false,
		centerMode: false,
		fade: false,
		swipeToSlide: true,
		autoplay: false,
		speed: 1500,
		easing: 'easeInOutQuint'
	});

	const settings = {
		infinite: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: true,
		dots: false,
		centerMode: true,
		fade: true,
		swipeToSlide: true,
		autoplay: false,
		speed: 1500,
		easing: 'easeInOutQuint',
		mobileFirst: true,
		responsive: [
		{
			breakpoint: 992,
			settings: "unslick"
		}]
	};
	
	
	const sl = $('.js-slick-contentmobile').slick(settings);
	
	$(window).on('resize', function() {
	   if( $(window).width() < 992 &&  !sl.hasClass('slick-initialized')) {
			 $('.js-slick-contentmobile').slick(settings);
		}
	});
}