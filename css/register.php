<?php
add_action('wp_enqueue_scripts', function () {
  if (is_front_page()) {
    wp_enqueue_style('homepage', get_stylesheet_directory_uri() . '/css/homepage.css', array(), rand(111, 9999), 'all');
  }
  if (is_product()) {
    wp_enqueue_style('single-product', get_stylesheet_directory_uri() . '/css/single-product.css', array(), rand(111, 9999), 'all');
  }
  if (is_page('lien-he')) {
    wp_enqueue_style('contactpage', get_stylesheet_directory_uri() . '/css/contactpage.css', array(), rand(111, 9999), 'all');
  }

  wp_enqueue_style('header-footer', get_stylesheet_directory_uri() . '/css/header-footer.css', array(), rand(111, 9999), 'all');
});