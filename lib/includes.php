<?php
    function add_theme_scripts() {
        wp_enqueue_style( 'styles', get_template_directory_uri() . '/assets/css/main.css');
        wp_enqueue_script( 'script', get_template_directory_uri() . '/assets/js/main-min.js', array ( 'jquery' ), 1.1, true);
    }
      
    add_action( 'wp_enqueue_scripts', 'add_theme_scripts' );
?>