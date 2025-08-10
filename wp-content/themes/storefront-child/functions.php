<?php
require 'functions-front.php';

add_action( 'wp_enqueue_scripts', function() {
    $parent_style = 'storefront-style';
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'storefront-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style )
    );
});
