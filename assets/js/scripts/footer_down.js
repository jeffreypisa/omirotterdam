import $ from "jquery";

export function footer_down() {
	function footerdown() {
    var footer = $('footer').outerHeight();
    var header = $('header').outerHeight();
    var windowheight = $(window).height();
    var contentminheight = windowheight - footer - header;
    $('main').css('min-height', contentminheight);
  }

  footerdown();
  
  $( window ).resize(function() {
    footerdown();
  });
}