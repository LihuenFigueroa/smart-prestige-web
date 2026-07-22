<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
  <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/img/favicon.svg" type="image/svg+xml" />
  <?php wp_head(); ?>
  <script>
    window.THEME_URL       = '<?php echo get_template_directory_uri(); ?>';
    window.WP_AJAX_URL     = '<?php echo admin_url('admin-ajax.php'); ?>';
    window.WP_CONTACT_NONCE = '<?php echo wp_create_nonce('smart_contacto'); ?>';
    window.WP_GRACIAS_URL  = '<?php echo home_url('/gracias/'); ?>';
  </script>
</head>
<body <?php body_class('bg-white font-smart-next'); ?>>
