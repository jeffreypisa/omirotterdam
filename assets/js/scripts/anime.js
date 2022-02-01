import inView from 'in-view';
import $ from "jquery";

export function animejs() {
		
	// Anime Reveal
	
	inView('.anime-reveal')
	.on('enter', el => {
		$(el).addClass('visible');
	});
	
	// Anime Fade in
	
	inView('.anime-fadein')
	.on('enter', el => {
		$(el).addClass('visible');
	});
	
}