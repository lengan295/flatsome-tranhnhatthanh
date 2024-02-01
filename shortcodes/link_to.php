<?php
add_shortcode('link_to', function ($atts, $content = null) {
  try {
    $atts = shortcode_atts(
      array('slug' => '', 'class' => '', 'id' => '', 'is_woo_cat' => false, 'target' => '_self'),
      $atts
    );
    extract($atts);

    if ($is_woo_cat) {
      $category = get_term_by('slug', $slug, 'product_cat');
      $href = $category ? esc_url(get_term_link($category)) : 'javascript:void(0);';
      return '<a href="' . $href . '" class="' . $class . ' ' . (get_queried_object_id() === $category->term_id ? 'current' : '') . '" id="' . $id . '">' . $content . '</a>';
    } else {
      $page = get_page_by_path($slug);
      $href = $page ? esc_url(get_permalink($page->ID)) : 'javascript:void(0);';
      return '<a href="' . $href . '" class="' . $class . ' ' . (get_the_ID() === $page->ID ? 'current' : '') . '" id="' . $id . '" target="' . $target . '">' . $content . '</a>';
    }

  } catch (Exception $e) {
    return 'Error in link_to shortcode. Cause: ' . $e->getMessage();
  }
});