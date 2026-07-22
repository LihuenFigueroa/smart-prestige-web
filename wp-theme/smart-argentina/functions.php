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

// ── ACF (Advanced Custom Fields) ────────────────────────────────────────────
// El theme requiere el plugin ACF (versión gratuita) para los campos del CPT
// "Concesionario". No se vendoriza en el theme (repo de terceros, ~5MB, con
// updates propios) — se instala como plugin estándar de WP:
//   Plugins → Agregar nuevo → buscar "Advanced Custom Fields" → Instalar → Activar
//   (o subiendo el .zip de wordpress.org/plugins/advanced-custom-fields si el
//   servidor no tiene salida a internet desde wp-admin).
// Mientras no esté activo, se muestra un aviso en el admin y el resto del
// código de abajo queda en no-op (guardado con function_exists) sin romper el sitio.
add_action('admin_notices', function () {
  if (function_exists('acf_add_local_field_group') || !current_user_can('activate_plugins')) return;
  echo '<div class="notice notice-error"><p><strong>smart Argentina:</strong> falta instalar y activar el plugin <strong>Advanced Custom Fields</strong> (versión gratuita). Es requerido para gestionar los concesionarios desde el admin. Instalalo desde <em>Plugins → Agregar nuevo</em>.</p></div>';
});

// ── CPT "Concesionario" ──────────────────────────────────────────────────────
add_action('init', function () {
  register_post_type('concesionario', [
    'labels' => [
      'name'          => 'Concesionarios',
      'singular_name' => 'Concesionario',
      'add_new_item'  => 'Agregar concesionario',
      'edit_item'     => 'Editar concesionario',
      'all_items'     => 'Concesionarios',
      'search_items'  => 'Buscar concesionario',
      'not_found'     => 'No se encontraron concesionarios',
    ],
    'public'          => false,
    'show_ui'         => true,
    'show_in_menu'    => true,
    'menu_icon'       => 'dashicons-location-alt',
    'supports'        => ['title'],
    'has_archive'     => false,
    'rewrite'         => false,
    'capability_type' => 'post',
  ]);
});

// ── Campos ACF del CPT "Concesionario" ──────────────────────────────────────
add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  acf_add_local_field_group([
    'key'      => 'group_concesionario',
    'title'    => 'Datos del concesionario',
    'fields'   => [
      ['key' => 'field_conc_nombre',    'label' => 'Nombre',     'name' => 'nombre',    'type' => 'text', 'required' => 1],
      ['key' => 'field_conc_direccion', 'label' => 'Dirección',  'name' => 'direccion', 'type' => 'text', 'required' => 1, 'instructions' => 'Solo calle y altura, sin localidad (ej: "Galileo Galilei 1744").'],
      ['key' => 'field_conc_localidad', 'label' => 'Localidad',  'name' => 'localidad', 'type' => 'text', 'required' => 1],
      ['key' => 'field_conc_provincia', 'label' => 'Provincia',  'name' => 'provincia', 'type' => 'text', 'required' => 1, 'instructions' => 'Ej: "Buenos Aires", "CABA", "Santa Fe", "Córdoba", "Mendoza", "Tucumán".'],
      ['key' => 'field_conc_telefono',  'label' => 'Teléfono',   'name' => 'telefono',  'type' => 'text', 'instructions' => 'Formato: +54 11 4773-0537. Dejar vacío si no hay línea directa (se muestra un botón de contacto sin número).'],
      ['key' => 'field_conc_latitud',   'label' => 'Latitud',    'name' => 'latitud',   'type' => 'number', 'step' => '0.0000001', 'required' => 1],
      ['key' => 'field_conc_longitud',  'label' => 'Longitud',   'name' => 'longitud',  'type' => 'number', 'step' => '0.0000001', 'required' => 1],
      [
        'key'           => 'field_conc_tipo_servicio',
        'label'         => 'Tipo de servicio',
        'name'          => 'tipo_servicio',
        'type'          => 'select',
        'choices'       => ['venta' => 'Venta', 'servicio' => 'Servicio', 'venta_servicio' => 'Venta y Servicio'],
        'default_value' => 'venta_servicio',
        'required'      => 1,
      ],
      ['key' => 'field_conc_horario', 'label' => 'Horario de atención', 'name' => 'horario', 'type' => 'textarea', 'rows' => 3, 'instructions' => 'Ej: "Lunes a viernes de 9 a 18 hs." (campo nuevo, completar manualmente).'],
    ],
    'location' => [
      [['param' => 'post_type', 'operator' => '==', 'value' => 'concesionario']],
    ],
  ]);
});

// ── Migración one-time de los 15 concesionarios hardcodeados ────────────────
add_action('init', function () {
  if (get_option('smart_concesionarios_migrados') === 'si') return;
  if (!function_exists('update_field')) return; // esperar a que ACF esté activo

  // Marcar como migrado ANTES del loop: evita que dos requests concurrentes
  // (ej. dos pestañas o un doble hit justo al activar ACF) inserten duplicados.
  update_option('smart_concesionarios_migrados', 'si');

  $concesionarios = [
    ['slug' => 'colcar-moreno',            'nombre' => 'Colcar',       'direccion' => 'Galileo Galilei 1744',               'localidad' => 'Moreno',          'provincia' => 'Buenos Aires', 'telefono' => '+54 237 468-1233',   'lat' => -34.6315864, 'lng' => -58.7760953, 'tipo' => 'venta_servicio'],
    ['slug' => 'lonco-hue-libertador',     'nombre' => 'Lonco Hue',    'direccion' => 'Av. Del Libertador 2244',            'localidad' => 'Palermo',         'provincia' => 'CABA',         'telefono' => '+54 11 4773-0537',   'lat' => -34.5811869, 'lng' => -58.4044515, 'tipo' => 'venta'],
    ['slug' => 'lonco-hue-humboldt',       'nombre' => 'Lonco Hue',    'direccion' => 'Humboldt 2279',                      'localidad' => 'Palermo',         'provincia' => 'CABA',         'telefono' => '+54 11 4773-0537',   'lat' => -34.5887000, 'lng' => -58.4380000, 'tipo' => 'venta'],
    ['slug' => 'klasse-libertador',        'nombre' => 'Klasse',       'direccion' => 'Av. Del Libertador 1551',            'localidad' => 'Vicente López',   'provincia' => 'Buenos Aires', 'telefono' => '+54 9 11 5097-5237', 'lat' => -34.5187995, 'lng' => -58.4746415, 'tipo' => 'venta_servicio'],
    ['slug' => 'klasse-beiro',             'nombre' => 'Klasse',       'direccion' => 'Av. Francisco Beiró 4420',           'localidad' => 'Villa Devoto',    'provincia' => 'CABA',         'telefono' => '+54 11 4502-0500',   'lat' => -34.6070101, 'lng' => -58.5134585, 'tipo' => 'venta'],
    ['slug' => 'klasse-nunez',             'nombre' => 'Klasse',       'direccion' => 'Grecia 3633',                        'localidad' => 'Núñez',           'provincia' => 'CABA',         'telefono' => '+54 11 4702-2095',   'lat' => -34.5460833, 'lng' => -58.4632994, 'tipo' => 'servicio'],
    ['slug' => 'la-merced-panamericana',   'nombre' => 'La Merced',    'direccion' => 'Panamericana km 50',                 'localidad' => 'Pilar',           'provincia' => 'Buenos Aires', 'telefono' => '+54 230 447-4700',   'lat' => -34.4439420, 'lng' => -58.8776730, 'tipo' => 'venta_servicio'],
    ['slug' => 'la-merced-magnolias',      'nombre' => 'La Merced',    'direccion' => 'Las Magnolias 581',                  'localidad' => 'Pilar',           'provincia' => 'Buenos Aires', 'telefono' => '+54 230 447-4700',   'lat' => -34.4455126, 'lng' => -58.8718679, 'tipo' => 'venta_servicio'],
    ['slug' => 'besten-san-fernando',      'nombre' => 'Besten',       'direccion' => 'Av. del Libertador 2827',            'localidad' => 'San Fernando',    'provincia' => 'Buenos Aires', 'telefono' => '+54 11 3328-5335',   'lat' => -34.4482784, 'lng' => -58.5380991, 'tipo' => 'venta'],
    ['slug' => 'besten-tigre',             'nombre' => 'Besten',       'direccion' => 'Av. Juan B. Justo 2353',             'localidad' => 'Tigre',           'provincia' => 'Buenos Aires', 'telefono' => '+54 11 2372-3008',   'lat' => -34.4408562, 'lng' => -58.5752994, 'tipo' => 'venta'],
    ['slug' => 'stern-motors-rosario',     'nombre' => 'Stern Motors', 'direccion' => 'Junín 250',                          'localidad' => 'Rosario',         'provincia' => 'Santa Fe',     'telefono' => '+54 341 527-8932',   'lat' => -32.9262057, 'lng' => -60.6665938, 'tipo' => 'venta_servicio'],
    ['slug' => 'rolcar-yerba-buena',       'nombre' => 'Rolcar',       'direccion' => 'Avenida Aconquija 1238',             'localidad' => 'Yerba Buena',     'provincia' => 'Tucumán',      'telefono' => '+54 381 410-6650',   'lat' => -26.8147598, 'lng' => -65.2870540, 'tipo' => 'venta_servicio'],
    ['slug' => 'colcor-cordoba',           'nombre' => 'Colcor',       'direccion' => 'Colectora Norte Agustín Tosco S/N',  'localidad' => 'Córdoba Capital', 'provincia' => 'Córdoba',      'telefono' => '+54 351 589-2285',   'lat' => -31.3527796, 'lng' => -64.1938955, 'tipo' => 'venta_servicio'],
    ['slug' => 'yacopini-maipu',           'nombre' => 'Yacopini',     'direccion' => 'Carril Rodriguez Peña 744',          'localidad' => 'Maipú',           'provincia' => 'Mendoza',      'telefono' => '+54 261 497-8585',   'lat' => -32.9315650, 'lng' => -68.7962284, 'tipo' => 'venta_servicio'],
    ['slug' => 'meister-don-torcuato',     'nombre' => 'Meister',      'direccion' => 'Colectora Este Panamericana 27559',  'localidad' => 'Don Torcuato',    'provincia' => 'Buenos Aires', 'telefono' => '',                   'lat' => -34.4820155, 'lng' => -58.6325636, 'tipo' => 'venta_servicio'],
  ];

  foreach ($concesionarios as $c) {
    if (get_page_by_path($c['slug'], OBJECT, 'concesionario')) continue;

    $post_id = wp_insert_post([
      'post_type'   => 'concesionario',
      'post_title'  => $c['nombre'] . ' — ' . $c['localidad'],
      'post_name'   => $c['slug'],
      'post_status' => 'publish',
    ]);
    if (is_wp_error($post_id) || !$post_id) continue;

    update_field('nombre', $c['nombre'], $post_id);
    update_field('direccion', $c['direccion'], $post_id);
    update_field('localidad', $c['localidad'], $post_id);
    update_field('provincia', $c['provincia'], $post_id);
    update_field('telefono', $c['telefono'], $post_id);
    update_field('latitud', $c['lat'], $post_id);
    update_field('longitud', $c['lng'], $post_id);
    update_field('tipo_servicio', $c['tipo'], $post_id);
    update_field('horario', '', $post_id);
  }
}, 20);

// ── Helper: concesionarios normalizados (usado por buscador + form) ─────────
function smart_get_concesionarios() {
  static $cache = null;
  if ($cache !== null) return $cache;

  $provincia_display = [
    'Buenos Aires' => 'GBA',
    'CABA'         => 'CABA',
  ];
  $tags_alias = [
    'Buenos Aires' => 'gba buenos aires',
    'CABA'         => 'caba buenos aires capital',
    'Córdoba'      => 'cordoba capital',
  ];
  $tipo_labels = [
    'venta'          => 'Venta.',
    'servicio'       => 'Servicio al cliente.',
    'venta_servicio' => 'Venta y servicio al cliente.',
  ];

  $items = [];

  if (!function_exists('get_field')) return $cache = $items;

  $query = new WP_Query([
    'post_type'      => 'concesionario',
    'posts_per_page' => -1,
    'orderby'        => 'ID',
    'order'          => 'ASC',
    'no_found_rows'  => true,
  ]);

  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();
      $id        = get_the_ID();
      $nombre    = (string) get_field('nombre', $id);
      $direccion = (string) get_field('direccion', $id);
      $localidad = (string) get_field('localidad', $id);
      $provincia = (string) get_field('provincia', $id);
      $telefono  = (string) get_field('telefono', $id);
      $tipo      = (string) get_field('tipo_servicio', $id);

      $prov_label  = $provincia_display[$provincia] ?? $provincia;
      $prov_suffix = (stripos($localidad, $provincia) !== false) ? '' : ', ' . $prov_label;
      $extra_tags  = $tags_alias[$provincia] ?? strtolower($provincia);

      $items[] = [
        'id'                 => $id,
        'slug'               => get_post_field('post_name', $id),
        'nombre'             => $nombre,
        'direccion'          => $direccion,
        'localidad'          => $localidad,
        'provincia'          => $provincia,
        'direccion_completa' => $direccion . ', ' . $localidad . $prov_suffix,
        'telefono'           => $telefono,
        'lat'                => (float) get_field('latitud', $id),
        'lng'                => (float) get_field('longitud', $id),
        'horario'            => (string) get_field('horario', $id),
        'tipo_label'         => $tipo_labels[$tipo] ?? $tipo,
        'tags'               => strtolower(remove_accents($nombre . ' ' . $localidad . ' ' . $extra_tags)),
      ];
    }
    wp_reset_postdata();
  }

  return $cache = $items;
}

// ── CPT "Versión de vehículo" (comparativa smart1/smart3) ───────────────────
add_action('init', function () {
  register_post_type('version_vehiculo', [
    'labels' => [
      'name'          => 'Versiones (comparativa)',
      'singular_name' => 'Versión',
      'add_new_item'  => 'Agregar versión',
      'edit_item'     => 'Editar versión',
      'all_items'     => 'Versiones (comparativa)',
      'search_items'  => 'Buscar versión',
      'not_found'     => 'No se encontraron versiones',
    ],
    'public'          => false,
    'show_ui'         => true,
    'show_in_menu'    => true,
    'menu_icon'       => 'dashicons-car',
    'supports'        => ['title'],
    'has_archive'     => false,
    'rewrite'         => false,
    'capability_type' => 'post',
  ]);
});

// ── CPT "Specs BRABUS por modelo" ────────────────────────────────────────────
add_action('init', function () {
  register_post_type('brabus_spec_modelo', [
    'labels' => [
      'name'          => 'Specs BRABUS',
      'singular_name' => 'Spec BRABUS',
      'add_new_item'  => 'Agregar spec',
      'edit_item'     => 'Editar spec',
      'all_items'     => 'Specs BRABUS',
      'search_items'  => 'Buscar spec',
      'not_found'     => 'No se encontraron specs',
    ],
    'public'          => false,
    'show_ui'         => true,
    'show_in_menu'    => true,
    'menu_icon'       => 'dashicons-dashboard',
    'supports'        => ['title'],
    'has_archive'     => false,
    'rewrite'         => false,
    'capability_type' => 'post',
  ]);
});

// ── Campos ACF: versión (comparativa) ────────────────────────────────────────
add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  acf_add_local_field_group([
    'key'      => 'group_version_vehiculo',
    'title'    => 'Datos de la versión',
    'fields'   => [
      ['key' => 'field_ver_modelo', 'label' => 'Modelo', 'name' => 'modelo', 'type' => 'select', 'choices' => ['smart1' => 'smart #1', 'smart3' => 'smart #3'], 'required' => 1],
      ['key' => 'field_ver_orden', 'label' => 'Orden (columna)', 'name' => 'orden', 'type' => 'number', 'required' => 1, 'instructions' => '0 = primera columna (izquierda).'],
      ['key' => 'field_ver_nombre', 'label' => 'Nombre de la versión', 'name' => 'nombre_version', 'type' => 'text', 'required' => 1, 'instructions' => 'Ej: Pure, Pro, Pro+, BRABUS.'],
      ['key' => 'field_ver_imagen', 'label' => 'Imagen', 'name' => 'imagen', 'type' => 'text', 'required' => 1, 'instructions' => 'Ruta relativa dentro de assets/img/ (ej: smart1/comp-pure.png). Para cambiar la foto hay que reemplazar ese archivo en el servidor.'],
      ['key' => 'field_ver_destacado', 'label' => 'Destacado (versión tope de gama)', 'name' => 'destacado', 'type' => 'true_false', 'instructions' => 'Aplica el estilo en negrita de la columna BRABUS.'],
      ['key' => 'field_ver_autonomia_mixta', 'label' => 'Autonomía WLTP ciclo mixto', 'name' => 'autonomia_mixta', 'type' => 'text', 'required' => 1],
      ['key' => 'field_ver_autonomia_ciudad', 'label' => 'Autonomía WLTP ciudad', 'name' => 'autonomia_ciudad', 'type' => 'text', 'required' => 1],
      ['key' => 'field_ver_mecanica', 'label' => 'Mecánica', 'name' => 'mecanica', 'type' => 'textarea', 'rows' => 4, 'instructions' => 'Un ítem por línea.'],
      ['key' => 'field_ver_exterior', 'label' => 'Exterior', 'name' => 'exterior', 'type' => 'textarea', 'rows' => 4, 'instructions' => 'Un ítem por línea.'],
      ['key' => 'field_ver_interior', 'label' => 'Interior', 'name' => 'interior', 'type' => 'textarea', 'rows' => 4, 'instructions' => 'Un ítem por línea.'],
      ['key' => 'field_ver_tecnologia', 'label' => 'Tecnología', 'name' => 'tecnologia', 'type' => 'textarea', 'rows' => 4, 'instructions' => 'Un ítem por línea.'],
      ['key' => 'field_ver_seguridad', 'label' => 'Seguridad', 'name' => 'seguridad', 'type' => 'textarea', 'rows' => 4, 'instructions' => 'Un ítem por línea.'],
      ['key' => 'field_ver_slug_form', 'label' => 'Slug para el formulario de contacto', 'name' => 'slug_form', 'type' => 'text', 'required' => 1, 'instructions' => 'Ej: smart1-pure. No cambiar salvo que sepas lo que hacés.'],
    ],
    'location' => [
      [['param' => 'post_type', 'operator' => '==', 'value' => 'version_vehiculo']],
    ],
  ]);
});

// ── Campos ACF: specs BRABUS por modelo ──────────────────────────────────────
add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  acf_add_local_field_group([
    'key'      => 'group_brabus_spec_modelo',
    'title'    => 'Specs BRABUS',
    'fields'   => [
      ['key' => 'field_bs_aceleracion', 'label' => 'Aceleración (0-100 km/h)', 'name' => 'aceleracion', 'type' => 'text', 'required' => 1],
      ['key' => 'field_bs_bateria', 'label' => 'Batería', 'name' => 'bateria', 'type' => 'text', 'required' => 1],
      ['key' => 'field_bs_traccion', 'label' => 'Tracción', 'name' => 'traccion', 'type' => 'text', 'required' => 1],
      ['key' => 'field_bs_autonomia', 'label' => 'Autonomía', 'name' => 'autonomia', 'type' => 'text', 'required' => 1],
      ['key' => 'field_bs_potencia', 'label' => 'Potencia', 'name' => 'potencia', 'type' => 'text', 'required' => 1],
    ],
    'location' => [
      [['param' => 'post_type', 'operator' => '==', 'value' => 'brabus_spec_modelo']],
    ],
  ]);
});

// ── Migración one-time: versiones de la comparativa (smart1 + smart3) ──────
add_action('init', function () {
  if (get_option('smart_versiones_migradas') === 'si') return;
  if (!function_exists('update_field')) return; // esperar a que ACF esté activo

  // Marcar como migrado ANTES del loop (evita duplicados por requests concurrentes, ver Fase 1).
  update_option('smart_versiones_migradas', 'si');

  $versiones = [
    ['slug' => 'smart1-pure', 'modelo' => 'smart1', 'orden' => 0, 'nombre_version' => 'Pure', 'imagen' => 'smart1/comp-pure.png', 'destacado' => 0,
      'autonomia_mixta' => '310 km', 'autonomia_ciudad' => '427 km',
      'mecanica' => "RWD (Tracción trasera), 200 kW\nBatería de 49 kWh\nCarga CC de hasta 130 kW\nCarga CA de hasta 7.4 kW",
      'exterior' => "Luces LED CyberSparks con Asistente de Luces Altas Automático",
      'interior' => "Asientos de tela",
      'tecnologia' => "Pantalla central de 12.8\" con Navegador Apple CarPlay® y Android Auto",
      'seguridad' => "smart Pilot Assist\n4 sensores de estacionamiento traseros",
      'slug_form' => 'smart1-pure'],
    ['slug' => 'smart1-pro', 'modelo' => 'smart1', 'orden' => 1, 'nombre_version' => 'Pro', 'imagen' => 'smart1/comp-pro.png', 'destacado' => 0,
      'autonomia_mixta' => '310 km', 'autonomia_ciudad' => '427 km',
      'mecanica' => "RWD (Tracción trasera), 200 kW\nBatería de 49 kWh\nCarga CC de hasta 130 kW\nCarga CA de hasta 7.4 kW",
      'exterior' => "Luces LED CyberSparks con Asistente de Luces Altas Automático\nTecho Panorámico Halo\nPortón trasero eléctrico\nCristales de Privacidad (Tintados)",
      'interior' => "Asientos de cuero sintético\nAsientos delanteros calefactables y ajustables eléctricos\nAsiento trasero deslizable\nIluminación ambiental",
      'tecnologia' => "Pantalla central de 12.8\" con Navegador Apple CarPlay® y Android Auto\nCargador inalámbrico para teléfono",
      'seguridad' => "smart Pilot Assist\n8 sensores de estacionamiento (del. y tras.)\nCámara de estacionamiento de 360°",
      'slug_form' => 'smart1-pro'],
    ['slug' => 'smart1-proplus', 'modelo' => 'smart1', 'orden' => 2, 'nombre_version' => 'Pro+', 'imagen' => 'smart1/comp-prop.png', 'destacado' => 0,
      'autonomia_mixta' => '420 km', 'autonomia_ciudad' => '584 km',
      'mecanica' => "RWD (Tracción trasera), 200 kW\nBatería de 66 kWh\nCarga CC de hasta 150 kW\nCarga CA de hasta 22 kW",
      'exterior' => "Luces LED CyberSparks con Asistente de Luces Altas Automático\nTecho Panorámico Halo\nPortón trasero eléctrico\nCristales de Privacidad (Tintados)",
      'interior' => "Asientos de cuero sintético\nAsientos delanteros calefactables y ajustables eléctricos\nAsiento trasero deslizable\nIluminación ambiental",
      'tecnologia' => "Pantalla central de 12.8\" con Navegador Apple CarPlay® y Android Auto\nCargador inalámbrico para teléfono",
      'seguridad' => "smart Pilot Assist\n8 sensores de estacionamiento (del. y tras.)\nCámara de estacionamiento de 360°",
      'slug_form' => 'smart1-proplus'],
    ['slug' => 'smart1-brabus', 'modelo' => 'smart1', 'orden' => 3, 'nombre_version' => 'BRABUS', 'imagen' => 'smart1/comp-brabus.png', 'destacado' => 1,
      'autonomia_mixta' => '400 km', 'autonomia_ciudad' => '532 km',
      'mecanica' => "AWD (Tracción total), 315 kW\nBatería de 66 kWh\nCarga CC de hasta 150 kW\nCarga CA de hasta 22 kW",
      'exterior' => "Luces LED+ CyberSparks con Faros Matriciales y Luz de Carretera Adaptativa\nTecho Panorámico Halo\nPortón trasero con Control Gestual\nCristales de Privacidad (Tintados)\nLuces de cortesía con proyección de logo\nEstilo de carrocería y emblemas BRABUS\nPinzas de freno rojas",
      'interior' => "Asientos de gamuza de microfibra\nAsientos delanteros calefactables y ajustables eléc.\nAsiento trasero deslizable\nIluminación ambiental+\nBomba de calor\nSonido Beats® (13 altavoces)\nVolante de Alcantara®\nAsientos delanteros ventilados",
      'tecnologia' => "Pantalla central de 12.8\" con Navegador Apple CarPlay® y Android Auto\nCargador inalámbrico para teléfono\nPantalla Head-Up Display de 10\"",
      'seguridad' => "smart Pilot Assist\n8 sensores de estacionamiento (del. y tras.)\nCámara de estacionamiento de 360°",
      'slug_form' => 'smart1-brabus'],
    ['slug' => 'smart3-pro', 'modelo' => 'smart3', 'orden' => 0, 'nombre_version' => 'Pro', 'imagen' => 'smart3/comp-pro.png', 'destacado' => 0,
      'autonomia_mixta' => '325 km', 'autonomia_ciudad' => '450 km',
      'mecanica' => "RWD (tracción trasera) 200 kW\nBatería de 49 kWh\nCarga CC de hasta 130 kW\nCarga CA de hasta 7.4 kW",
      'exterior' => "Luces LED CyberSparks con Asistente automático de Luces Altas\nTecho panorámico Halo\nPortón trasero eléctrico",
      'interior' => "Asientos de cuero sintético\nAsientos delanteros calefactables y ajustables eléctricamente",
      'tecnologia' => "Pantalla central de 12.8\" con Sistema de Navegación\nApple CarPlay® y Android Auto\nCargador inalámbrico para teléfono",
      'seguridad' => "smart Pilot Assist\n8 sensores de estacionamiento (delanteros y traseros)\nCámara de estacionamiento de 360°",
      'slug_form' => 'smart3-pro'],
    ['slug' => 'smart3-proplus', 'modelo' => 'smart3', 'orden' => 1, 'nombre_version' => 'Pro+', 'imagen' => 'smart3/comp-prop.png', 'destacado' => 0,
      'autonomia_mixta' => '435 km', 'autonomia_ciudad' => '597 km',
      'mecanica' => "RWD (Tracción trasera), 200 kW\nBatería de 66 kWh\nCarga CC de hasta 150 kW\nCarga CA de hasta 22 kW",
      'exterior' => "Luces LED CyberSparks con Asistente automático de Luces Altas\nTecho panorámico Halo\nPortón trasero eléctrico",
      'interior' => "Asientos de cuero sintético\nAsientos delanteros calefactables y ajustables eléctricamente",
      'tecnologia' => "Pantalla central de 12.8\" con Sistema de Navegación\nApple CarPlay® y Android Auto\nCargador inalámbrico para teléfono",
      'seguridad' => "smart Pilot Assist\n8 sensores de estacionamiento (delanteros y traseros)\nCámara de estacionamiento de 360°",
      'slug_form' => 'smart3-proplus'],
    ['slug' => 'smart3-brabus', 'modelo' => 'smart3', 'orden' => 2, 'nombre_version' => 'BRABUS', 'imagen' => 'smart3/comp-brabus.png', 'destacado' => 1,
      'autonomia_mixta' => '310 km', 'autonomia_ciudad' => '551 km',
      'mecanica' => "AWD (Tracción total), 315 kW\nBatería de 66 kWh\nCarga CC de hasta 150 kW\nCarga CA de hasta 22 kW",
      'exterior' => "Luces LED+ CyberSparks con Faros Matriciales y Luz de Carretera Adaptativa\nTecho Panorámico Halo\nPortón trasero con Control Gestual\nCristales de Privacidad (Tintados)\nLuces de cortesía con proyección de logotipo\nEstilo de carrocería y emblemas BRABUS\nPinzas de freno pintadas en rojo",
      'interior' => "Asientos de gamuza de microfibra\nAsientos delanteros calefactables y ajustables eléctricamente\nIluminación ambiental+\nBomba de calor\nSistema de sonido Beats® con 13 altavoces\nVolante de Alcantara®\nAsientos delanteros ventilados",
      'tecnologia' => "Pantalla central de 12.8\" con Sistema de Navegación\nApple CarPlay® y Android Auto\nCargador inalámbrico para teléfono\nPantalla Head-Up Display de 10 pulgadas",
      'seguridad' => "smart Pilot Assist\n12 sensores de estacionamiento (delanteros y traseros) con Asistente de Estacionamiento Automático\nCámara de estacionamiento de 360°",
      'slug_form' => 'smart3-brabus'],
  ];

  foreach ($versiones as $v) {
    if (get_page_by_path($v['slug'], OBJECT, 'version_vehiculo')) continue;

    $post_id = wp_insert_post([
      'post_type'   => 'version_vehiculo',
      'post_title'  => ($v['modelo'] === 'smart1' ? 'smart #1' : 'smart #3') . ' — ' . $v['nombre_version'],
      'post_name'   => $v['slug'],
      'post_status' => 'publish',
    ]);
    if (is_wp_error($post_id) || !$post_id) continue;

    update_field('modelo', $v['modelo'], $post_id);
    update_field('orden', $v['orden'], $post_id);
    update_field('nombre_version', $v['nombre_version'], $post_id);
    update_field('imagen', $v['imagen'], $post_id);
    update_field('destacado', $v['destacado'], $post_id);
    update_field('autonomia_mixta', $v['autonomia_mixta'], $post_id);
    update_field('autonomia_ciudad', $v['autonomia_ciudad'], $post_id);
    update_field('mecanica', $v['mecanica'], $post_id);
    update_field('exterior', $v['exterior'], $post_id);
    update_field('interior', $v['interior'], $post_id);
    update_field('tecnologia', $v['tecnologia'], $post_id);
    update_field('seguridad', $v['seguridad'], $post_id);
    update_field('slug_form', $v['slug_form'], $post_id);
  }
}, 20);

// ── Migración one-time: specs BRABUS por modelo ──────────────────────────────
add_action('init', function () {
  if (get_option('smart_brabus_specs_migrados') === 'si') return;
  if (!function_exists('update_field')) return;

  update_option('smart_brabus_specs_migrados', 'si');

  $specs = [
    ['slug' => 'smart-1', 'titulo' => 'smart #1', 'aceleracion' => '3,9 seg', 'bateria' => '66kWh', 'traccion' => 'AWD', 'autonomia' => '400km', 'potencia' => '428CV'],
    ['slug' => 'smart-3', 'titulo' => 'smart #3', 'aceleracion' => '3,7 seg', 'bateria' => '66kWh', 'traccion' => 'AWD', 'autonomia' => '415km', 'potencia' => '428CV'],
  ];

  foreach ($specs as $s) {
    if (get_page_by_path($s['slug'], OBJECT, 'brabus_spec_modelo')) continue;

    $post_id = wp_insert_post([
      'post_type'   => 'brabus_spec_modelo',
      'post_title'  => $s['titulo'],
      'post_name'   => $s['slug'],
      'post_status' => 'publish',
    ]);
    if (is_wp_error($post_id) || !$post_id) continue;

    update_field('aceleracion', $s['aceleracion'], $post_id);
    update_field('bateria', $s['bateria'], $post_id);
    update_field('traccion', $s['traccion'], $post_id);
    update_field('autonomia', $s['autonomia'], $post_id);
    update_field('potencia', $s['potencia'], $post_id);
  }
}, 20);

// ── Helper: versiones de la comparativa (usado por page-smart1/page-smart3) ─
function smart_get_versiones($modelo) {
  static $cache = [];
  if (isset($cache[$modelo])) return $cache[$modelo];

  $items = [];
  if (!function_exists('get_field')) return $cache[$modelo] = $items;

  $query = new WP_Query([
    'post_type'      => 'version_vehiculo',
    'posts_per_page' => -1,
    'meta_query'     => [['key' => 'modelo', 'value' => $modelo]],
    'no_found_rows'  => true,
  ]);

  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();
      $id = get_the_ID();

      $bullets = function ($campo) use ($id) {
        $texto  = (string) get_field($campo, $id);
        $lineas = array_map('trim', explode("\n", $texto));
        return array_values(array_filter($lineas, function ($l) { return $l !== ''; }));
      };

      $items[] = [
        'orden'            => (int) get_field('orden', $id),
        'nombre_version'   => (string) get_field('nombre_version', $id),
        'imagen'           => get_template_directory_uri() . '/assets/img/' . ltrim((string) get_field('imagen', $id), '/'),
        'destacado'        => (bool) get_field('destacado', $id),
        'autonomia_mixta'  => (string) get_field('autonomia_mixta', $id),
        'autonomia_ciudad' => (string) get_field('autonomia_ciudad', $id),
        'mecanica'         => $bullets('mecanica'),
        'exterior'         => $bullets('exterior'),
        'interior'         => $bullets('interior'),
        'tecnologia'       => $bullets('tecnologia'),
        'seguridad'        => $bullets('seguridad'),
        'slug_form'        => (string) get_field('slug_form', $id),
      ];
    }
    wp_reset_postdata();
  }

  usort($items, function ($a, $b) { return $a['orden'] <=> $b['orden']; });

  return $cache[$modelo] = $items;
}

// ── Helper: specs BRABUS por modelo (usado por page-brabus.php) ─────────────
function smart_get_brabus_specs() {
  static $cache = null;
  if ($cache !== null) return $cache;

  $vacio = ['aceleracion' => '', 'bateria' => '', 'traccion' => '', 'autonomia' => '', 'potencia' => ''];
  $specs = ['1' => $vacio, '3' => $vacio];

  if (!function_exists('get_field')) return $cache = $specs;

  foreach (['1' => 'smart-1', '3' => 'smart-3'] as $num => $slug) {
    $post = get_page_by_path($slug, OBJECT, 'brabus_spec_modelo');
    if (!$post) continue;

    $specs[$num] = [
      'aceleracion' => (string) get_field('aceleracion', $post->ID),
      'bateria'     => (string) get_field('bateria', $post->ID),
      'traccion'    => (string) get_field('traccion', $post->ID),
      'autonomia'   => (string) get_field('autonomia', $post->ID),
      'potencia'    => (string) get_field('potencia', $post->ID),
    ];
  }

  return $cache = $specs;
}

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
