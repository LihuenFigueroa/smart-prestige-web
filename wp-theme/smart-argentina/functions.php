<?php
function smart_argentina_assets() {
  $tailwind_path = get_template_directory() . '/assets/css/tailwind.css';
  $styles_path   = get_template_directory() . '/assets/css/styles.css';
  $main_js_path  = get_template_directory() . '/assets/js/main.js';

  wp_enqueue_style('tailwind', get_template_directory_uri() . '/assets/css/tailwind.css', [], filemtime($tailwind_path));
  wp_enqueue_style('smart-styles', get_template_directory_uri() . '/assets/css/styles.css', ['tailwind'], filemtime($styles_path));
  wp_enqueue_script('smart-main', get_template_directory_uri() . '/assets/js/main.js', [], filemtime($main_js_path), true);
}
add_action('wp_enqueue_scripts', 'smart_argentina_assets');

add_filter('show_admin_bar', '__return_false');
register_nav_menus(['primary' => 'Menú Principal']);
