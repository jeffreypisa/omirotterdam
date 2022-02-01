<?php 
/**
 * Add Response code to video embeds in WordPress
 *
 * @refer  http://alxmedia.se/code/2013/10/make-wordpress-default-video-embeds-responsive/
 */
function abl1035_alx_embed_html( $html ) {
  
  return '<div class="video-container">' . $html . '</div>';
}
add_filter( 'embed_oembed_html', 'abl1035_alx_embed_html', 10, 3 );
add_filter( 'video_embed_html', 'abl1035_alx_embed_html' ); // Jetpack

?>