<?php
add_shortcode('img_by_id', function ($atts) {
  try {
    // size: thumbnail, medium, medium_large, large, full
    $atts = shortcode_atts(array('img_id' => null, 'size' => 'full', alt => '', 'class' => '', 'is_icon' => false), $atts);
    extract($atts);

    if (wp_attachment_is_image($img_id)) {
      $html = '<img class="' . $class . '" src="' . wp_get_attachment_image_url($img_id, $size, $is_icon) . '" alt="' . $alt . '"/>';
    } else {
      $html = '';
    }

    return $html;
  } catch (Exception $e) {
    return 'Error at image by id shortcode: ' . $e->getMessage();
  }
});