<?php
// Turn off auto gen <p> of contact form 7
add_filter('wpcf7_autop_or_not', false);

// add dashicons to normal page too
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style('dashicons');
});

// Add
add_action('wp_head', function () {
  ?>
  <div id="fb-root"></div>
  <script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v19.0"
    nonce="XleyWEJD"></script>
  <?php
});

// Include other php
include('shortcodes/register.php');
include('js/register.php');
include('css/register.php');