<?php
add_shortcode('single_product_sc', function () {
  try {
    global $product;

    if (is_a($product, 'WC_Product')) {
      // var_dump($product);
      ob_start();

      echo '<div class="product__detail">
        <h2 class="product__title">' . $product->get_name() . '</h2>';

      if ($product->get_sku()) {
        echo '<div class="product__sku">
          <span class="sku__title">Mã số:</span>
          <span class="sku__text">' . $product->get_sku() . '</span>
        </div>';
      }

      echo '<div class="product__price">' . ($product->get_price() ? wc_price($product->get_price()) : 'Liên hệ') . '</div>';

      echo '<div class="product__attributes">';
      $prd_attrs = $product->get_attributes();
      if ($prd_attrs) {
        foreach ($prd_attrs as $prd_attr) {
          $attr = wc_get_attribute($prd_attr->get_ID());
          $terms = wp_get_post_terms($product->get_ID(), $prd_attr->get_name());
          $values = array();
          foreach ($terms as $term) {
            $values[] = $term->name;
          }
          echo '<div class="product__attribute ' . $prd_attr->get_name() . '">
            <span class="attribute__name">' . $attr->name . ':</span>
            <span class="attribute__value">' . implode(', ', $values) . '</span>
          </div>';
        }
      }

      // Close product__atributes
      echo '</div>';

      if (is_user_logged_in()) {
        $dl_files = reset($product->get_files());
        if ($dl_files) {
          $file_url = $dl_files['file'];
          $target = '_self';
        }
      } else {
        $file_url = wc_get_page_permalink('myaccount');
        $target = '_blank';
      }

      echo '<div class="product__buttons">';
      // <a class="button download-button" href="' . esc_url($file_url) . '" target="' . $target . '">
      //   <span class="dashicons dashicons-download"></span>
      //   <span>Tải xuống tệp</span>
      // </a>';
      woocommerce_template_loop_add_to_cart(array('id' => $product->get_ID()));

      // Close product__buttons
      echo '</div>';

      echo '<div class="product__short-guide">
        <p>Sản phẩm kỹ thuật số, hình gốc sử dụng cho in ấn.</p>
        <p>Nhấn chọn tải xuống tệp để tải về sản phẩm</p>
      </div>';

      $prd_tags = wp_get_post_terms($product->get_id(), 'product_tag');

      if (!empty($prd_tags) && !is_wp_error($prd_tags)) {
        echo '<div class="product__tags">';
        foreach ($prd_tags as $tag) {
          echo '<a class="product__tag" href="' . get_term_link($tag) . '">
          ' . $tag->name . '
          </a>';
        }
        // Close product__tags
        echo '</div>';
      }

      // Close product__detail
      echo '</div>';

      echo '<div class="product__order">
        <h2 class="order__title">Đặt in tranh</h2>
        <p class="order__notice">Phần này chúng tôi dành riêng cho khách hàng muốn đặt in tranh, không liên quan đến tệp tin kỹ thuật số </p>
        <div class="order__material">
          <h3>chọn chất liệu</h3>
          <div class="material__options">
            <label class="checked"><span>Lụa 3d</span><input type="radio" name="material" value="Lụa 3D" checked ></label>
            <label><span>canvas</span><input type="radio" name="material" value="Canvas" ></label>
            <label><span>pp decal</span><input type="radio" name="material" value="PP Decal" ></label>
          </div>
        </div>
        <div class="order__frame-color">
          <h3>Chọn màu khung</h3>
          <div class="frame-color__options">
            <label class="checked"><span>Đen</span><input type="radio" name="frameColor" value="Đen" checked/></label>
            <label><span>Trắng</span><input type="radio" name="frameColor" value="Trắng" /></label>
            <label><span>Vàng gỗ</span><input type="radio" name="frameColor" value="Vàng gỗ" /></label>
            <label><span>Vàng đồng</span><input type="radio" name="frameColor" value="Vàng đồng" /></label>
          </div>
        </div>
        <a class="order__button button-with-icon" href="#orderForm">Đặt in ngay</a>';

      // Order form
      $order_form_html = '<div class="order-form">
        <header class="order-form__header">
          <h2>hoàn tất đặt in</h2>
        </header>
        <main class="order-form__main">
          <div class="form__pre-info">
            <div class="info__product-thumb">
              ' . $product->get_image() . '
            </div>
            <div class="info__content">
              <div class="info__product-name">' . $product->get_name() . '</div>
              <div class="info__product-sku"><span>- Mã số: </span><span>' . $product->get_sku() . '</span></div>
              <div class="info__material">
                <span>- Chất liệu: </span>
                <span class="material__text"></span>
              </div>
              <div class="info__frame-color">
                <span>- Màu khung: </span>
                <span class="frame-color__text"></span>
              </div>
            </div>
          </div>';
      $order_form_html .= do_shortcode('[contact-form-7 id="f443eeb" title="Order Form"]');
      $order_form_html .= '</main>
        <footer class="order-form__footer"></footer>
      </div>';

      echo do_shortcode('[lightbox id="orderForm"]' . $order_form_html . '[/lightbox]');
      // Close product__order
      echo '</div>';

      return ob_get_clean();
    } else {
      return 'Wrong product type!';
    }
  } catch (Exception $e) {
    return "Error in single product shortcode: " . $e->getMessage();
  }
});

// Change add to cart text
add_filter('woocommerce_product_add_to_cart_text', function ($text) {
  $text = 'Thêm vào giỏ';
  return $text;
});

// Change related product title

add_filter('gettext', function ($translated_text, $text, $domain) {
  if ($text === 'Related products') {
    $translated_text = __('Sản phẩm liên quan', 'woocommerce');
  }
  return $translated_text;
}, 10, 3);