import $ from "jquery";
import 'bootstrap';

// Init plugins
import { slick_init } from './scripts/slick.js';
import { matchheight_init } from './scripts/matchheight.js';
import { animejs } from './scripts/anime.js';
import { lity_init } from './scripts/lity.js';

// Scripts
import { site_is_loaded } from './scripts/site_is_loaded.js';
import { footer_down } from './scripts/footer_down.js';
import { mobilemenu } from './scripts/mobilemenu.js';
import { scrolleffects } from './scripts/scrolleffects.js';

lity_init();

$( document ).ready(function() {
	footer_down();
	mobilemenu();
	slick_init();
});

$(window).on('load', function() {
	matchheight_init();
	animejs();
	scrolleffects();
	site_is_loaded();
});