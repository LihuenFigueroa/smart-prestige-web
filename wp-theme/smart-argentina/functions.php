<?php
function smart_argentina_assets() {
  wp_enqueue_style('tailwind', get_template_directory_uri() . '/assets/css/tailwind.css', [], '1.0.0');
  wp_enqueue_style('smart-styles', get_template_directory_uri() . '/assets/css/styles.css', ['tailwind'], '1.0.0');
  wp_enqueue_script('smart-main', get_template_directory_uri() . '/assets/js/main.js', [], '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'smart_argentina_assets');

add_filter('show_admin_bar', '__return_false');
register_nav_menus(['primary' => 'Menú Principal']);
