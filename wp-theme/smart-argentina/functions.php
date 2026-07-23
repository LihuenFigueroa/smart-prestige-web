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

// ── Formulario de contacto — envío de mail vía wp_mail() (WP Mail SMTP) ────
add_action('wp_ajax_smart_enviar_formulario', 'smart_enviar_formulario');
add_action('wp_ajax_nopriv_smart_enviar_formulario', 'smart_enviar_formulario');
function smart_enviar_formulario() {
  check_ajax_referer('smart_contacto', 'nonce');

  $nombre        = sanitize_text_field(wp_unslash($_POST['nombre'] ?? ''));
  $apellido      = sanitize_text_field(wp_unslash($_POST['apellido'] ?? ''));
  $ciudad        = sanitize_text_field(wp_unslash($_POST['ciudad'] ?? ''));
  $email         = sanitize_email(wp_unslash($_POST['email'] ?? ''));
  $celular       = sanitize_text_field(wp_unslash($_POST['celular'] ?? ''));
  $concesionario = sanitize_text_field(wp_unslash($_POST['concesionario'] ?? ''));
  $modelo        = sanitize_text_field(wp_unslash($_POST['modelo'] ?? ''));
  $consulta      = sanitize_textarea_field(wp_unslash($_POST['consulta'] ?? ''));

  if (!$nombre || !$apellido || !$ciudad || !$email || !$celular || !$concesionario || !$modelo) {
    wp_send_json_error(['message' => 'Faltan campos obligatorios'], 400);
  }

  $to      = 'hola@prestige-auto.com.ar';
  $subject = sprintf('Nueva consulta — %s %s', $nombre, $apellido);
  $headers = [
    'Content-Type: text/html; charset=UTF-8',
    'Bcc: lifi.soluciones@gmail.com',
    'Bcc: clindstrom@prestige-auto.com.ar',
  ];

  $campo = function($label, $valor) {
    return '<tr>'
      . '<td style="padding:14px 0;border-bottom:1px solid #f3f4f6;width:110px;vertical-align:top;"><span style="color:#9ca3af;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;">' . esc_html($label) . '</span></td>'
      . '<td style="padding:14px 0 14px 20px;border-bottom:1px solid #f3f4f6;vertical-align:top;"><span style="color:#141413;font-size:13px;">' . esc_html($valor ?: '—') . '</span></td>'
      . '</tr>';
  };

  $body = '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>'
    . '<body style="margin:0;padding:0;background-color:#f0efe9;font-family:\'Helvetica Neue\',Arial,sans-serif;">'
    . '<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0efe9;padding:40px 16px;"><tr><td align="center">'
    . '<table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;">'
    . '<tr><td style="background:#141413;padding:24px 36px 22px;"><p style="margin:0;color:#ffffff;font-size:11px;letter-spacing:0.18em;text-transform:uppercase;">smart Argentina</p></td></tr>'
    . '<tr><td style="padding:36px 36px 20px;">'
    . '<p style="margin:0 0 10px;color:#9ca3af;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;">Nueva consulta recibida</p>'
    . '<h1 style="margin:0;color:#141413;font-size:26px;font-weight:400;line-height:1.1;letter-spacing:-0.02em;">' . esc_html($nombre . ' ' . $apellido) . '</h1>'
    . '</td></tr>'
    . '<tr><td style="padding:0 36px;"><div style="border-top:1px solid #e5e7eb;"></div></td></tr>'
    . '<tr><td style="padding:0 36px;"><table width="100%" cellpadding="0" cellspacing="0">'
    . $campo('Ciudad', $ciudad)
    . $campo('Email', $email)
    . $campo('Celular', $celular)
    . $campo('Concesionario', $concesionario)
    . $campo('Modelo', $modelo)
    . '</table></td></tr>'
    . '<tr><td style="padding:24px 36px 32px;">'
    . '<p style="margin:0 0 10px;color:#9ca3af;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;">Consulta</p>'
    . '<p style="margin:0;color:#141413;font-size:13px;line-height:1.7;">' . nl2br(esc_html($consulta ?: '—')) . '</p>'
    . '</td></tr>'
    . '<tr><td style="background:#141413;padding:18px 36px;"><p style="margin:0;color:#ffffff;opacity:0.35;font-size:11px;">© 2026 smart Argentina</p></td></tr>'
    . '</table></td></tr></table></body></html>';

  $ok = wp_mail($to, $subject, $body, $headers);

  if ($ok) {
    wp_send_json_success();
  } else {
    wp_send_json_error(['message' => 'No se pudo enviar el mail'], 500);
  }
}
