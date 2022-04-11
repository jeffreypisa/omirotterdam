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
import { sticky_header } from './scripts/sticky_header.js';
import { loadmoreposts } from './scripts/loadmoreposts.js';

lity_init();

$( document ).ready(function() {
	footer_down();
	mobilemenu();
	slick_init();
	loadmoreposts();
});

$(window).on('load', function() {
	matchheight_init();
	animejs();
	sticky_header();
	site_is_loaded();
});