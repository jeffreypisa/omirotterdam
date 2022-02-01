import $ from "jquery";
import LocomotiveScroll from 'locomotive-scroll';

export function scrolleffects() {

	const scroll = new LocomotiveScroll({
		el: document.querySelector('[data-scroll-container]'),
		smooth: true,
		getDirection: true
	});
		
	scroll.on('scroll', (t) => {			
		let direction = t.direction;
		let offsettop = t.scroll.y;
		
		if ( offsettop < '100' ) {
			$('header').removeClass('nav-up');
			$('header').removeClass('nav-down');
		} else if ( direction == 'up' ) {
			$('header').addClass('nav-down');
			$('header').removeClass('nav-up');
		} else if ( direction == 'down' ) {
			$('header').addClass('nav-up');
			$('header').removeClass('nav-down');
		}
	});

}