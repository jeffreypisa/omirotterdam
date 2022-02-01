import $ from "jquery";

export function site_is_loaded() {
  $('body').addClass('loaded');
  $('.preloader-wrapper').fadeOut();
  $('.mod-foto').addClass('autoheight');
}