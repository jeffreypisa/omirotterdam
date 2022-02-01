import $ from "jquery";

export function mobilemenu() {
	$(".js-menu").on("click", function() {
		$("body").toggleClass("menuopen");
		$("body").toggleClass("opensidemenu");
		$(".js-mobilemenu").toggleClass("open");
	});
}