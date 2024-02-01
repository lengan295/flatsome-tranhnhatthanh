<?php
add_action('wp_enqueue_scripts', function () {
  if (is_product()) {
    wp_enqueue_script('single-product', get_stylesheet_directory_uri() . '/js/single-product.js', array('jquery'), rand(111, 9999), true);
  }
});