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

// ── Desactiva el downscaling automático de imágenes grandes ────────────────
// Por defecto WP genera una versión "-scaled" (máx. 2560px) de cualquier
// imagen subida que la supere, y esa es la que queda como adjunto "full".
// El cliente pidió priorizar la calidad HD de las fotos por sobre el peso
// del archivo, así que se desactiva para que el campo ACF (return_format
// "url") sirva siempre el archivo original tal cual se subió.
add_filter('big_image_size_threshold', '__return_false');

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
    ['slug' => 'meister-don-torcuato',     'nombre' => 'Meister',      'direccion' => 'Colectora Este Panamericana 27559',  'localidad' => 'Don Torcuato',    'provincia' => 'Buenos Aires', 'telefono' => '0800 362 8555',      'lat' => -34.4820155, 'lng' => -58.6325636, 'tipo' => 'venta_servicio'],
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
      ['key' => 'field_ver_imagen', 'label' => 'Imagen', 'name' => 'imagen', 'type' => 'image', 'required' => 1, 'return_format' => 'url', 'preview_size' => 'medium'],
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
      'autonomia_mixta' => '415 km', 'autonomia_ciudad' => '551 km',
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
    ['slug' => 'smart-1', 'titulo' => 'smart #1', 'aceleracion' => '3,9 seg', 'bateria' => '66 kWh', 'traccion' => 'AWD', 'autonomia' => '400 km', 'potencia' => '428 CV'],
    ['slug' => 'smart-3', 'titulo' => 'smart #3', 'aceleracion' => '3,7 seg', 'bateria' => '66 kWh', 'traccion' => 'AWD', 'autonomia' => '415 km', 'potencia' => '428 CV'],
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
        'imagen'           => (string) get_field('imagen', $id),
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

// ── CPT "Tarjeta de característica" (carruseles home/conectividad/sobre-smart/movilidad) ─
add_action('init', function () {
  register_post_type('feature_card', [
    'labels' => [
      'name'          => 'Tarjetas de características',
      'singular_name' => 'Tarjeta',
      'add_new_item'  => 'Agregar tarjeta',
      'edit_item'     => 'Editar tarjeta',
      'all_items'     => 'Tarjetas de características',
      'search_items'  => 'Buscar tarjeta',
      'not_found'     => 'No se encontraron tarjetas',
    ],
    'public'          => false,
    'show_ui'         => true,
    'show_in_menu'    => true,
    'menu_icon'       => 'dashicons-images-alt2',
    'supports'        => ['title'],
    'has_archive'     => false,
    'rewrite'         => false,
    'capability_type' => 'post',
  ]);
});

// ── CPT "Ítem del acordeón de servicios" ─────────────────────────────────────
add_action('init', function () {
  register_post_type('servicio_acordeon', [
    'labels' => [
      'name'          => 'Acordeón de servicios',
      'singular_name' => 'Ítem del acordeón',
      'add_new_item'  => 'Agregar ítem',
      'edit_item'     => 'Editar ítem',
      'all_items'     => 'Acordeón de servicios',
      'search_items'  => 'Buscar ítem',
      'not_found'     => 'No se encontraron ítems',
    ],
    'public'          => false,
    'show_ui'         => true,
    'show_in_menu'    => true,
    'menu_icon'       => 'dashicons-list-view',
    'supports'        => ['title'],
    'has_archive'     => false,
    'rewrite'         => false,
    'capability_type' => 'post',
  ]);
});

// ── Campos ACF: tarjeta de característica ───────────────────────────────────
add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  acf_add_local_field_group([
    'key'      => 'group_feature_card',
    'title'    => 'Datos de la tarjeta',
    'fields'   => [
      [
        'key'      => 'field_fc_seccion',
        'label'    => 'Sección',
        'name'     => 'seccion',
        'type'     => 'select',
        'required' => 1,
        'choices'  => [
          'home'                 => 'Home — Elegí tu smart',
          'conectividad_1'       => 'Conectividad — carrusel 1',
          'conectividad_2'       => 'Conectividad — carrusel 2',
          'sobre_smart'          => 'Sobre smart — red de concesionarios',
          'movilidad_electrica'  => 'Movilidad eléctrica — cards',
          'smart1_c1'            => 'smart #1 — carrusel 1 (Exterior)',
          'smart1_c2'            => 'smart #1 — carrusel 2 (Interior)',
          'smart1_c3'            => 'smart #1 — carrusel 3 (Confort)',
          'smart1_c4'            => 'smart #1 — carrusel 4 (Seguridad)',
          'smart1_c5'            => 'smart #1 — carrusel 5 (Conectividad)',
          'smart1_c6'            => 'smart #1 — carrusel 6 (Practicidad)',
          'smart3_c1'            => 'smart #3 — carrusel 1 (Exterior)',
          'smart3_c2'            => 'smart #3 — carrusel 2 (Interior)',
          'smart3_c3'            => 'smart #3 — carrusel 3 (Confort)',
          'smart3_c4'            => 'smart #3 — carrusel 4 (Seguridad)',
          'smart3_c5'            => 'smart #3 — carrusel 5 (Conectividad)',
          'smart3_c6'            => 'smart #3 — carrusel 6 (Practicidad)',
        ],
      ],
      ['key' => 'field_fc_orden', 'label' => 'Orden', 'name' => 'orden', 'type' => 'number', 'required' => 1, 'instructions' => '0 = primera tarjeta.'],
      ['key' => 'field_fc_titulo', 'label' => 'Título', 'name' => 'titulo', 'type' => 'text', 'required' => 1],
      ['key' => 'field_fc_tag', 'label' => 'Etiqueta (opcional)', 'name' => 'tag', 'type' => 'text', 'instructions' => 'Sello chico sobre la imagen (ej: "Exterior", "Interior"). Solo lo lleva la primera tarjeta de cada carrusel.'],
      ['key' => 'field_fc_descripcion', 'label' => 'Descripción', 'name' => 'descripcion', 'type' => 'textarea', 'rows' => 3, 'required' => 1],
      ['key' => 'field_fc_imagen', 'label' => 'Imagen', 'name' => 'imagen', 'type' => 'image', 'required' => 1, 'return_format' => 'url', 'preview_size' => 'medium'],
      ['key' => 'field_fc_alt', 'label' => 'Texto alternativo (alt)', 'name' => 'alt', 'type' => 'text', 'instructions' => 'Opcional. Si se deja vacío se usa el título.'],
      ['key' => 'field_fc_disclaimer', 'label' => 'Disclaimer (opcional)', 'name' => 'disclaimer', 'type' => 'text', 'instructions' => 'Texto chico debajo de la descripción. Dejar vacío si la tarjeta no lleva.'],
      ['key' => 'field_fc_cta_texto', 'label' => 'Texto del botón (opcional)', 'name' => 'cta_texto', 'type' => 'text', 'instructions' => 'Dejar vacío si la tarjeta no lleva botón.'],
      ['key' => 'field_fc_cta_link', 'label' => 'Link del botón (opcional)', 'name' => 'cta_link', 'type' => 'text', 'instructions' => 'Si empieza con "/" se interpreta como página interna del sitio (ej: /brabus/). Si no, se usa tal cual (ej: "#" o una URL externa).'],
    ],
    'location' => [
      [['param' => 'post_type', 'operator' => '==', 'value' => 'feature_card']],
    ],
  ]);
});

// ── Campos ACF: ítem del acordeón de servicios ──────────────────────────────
add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  acf_add_local_field_group([
    'key'      => 'group_servicio_acordeon',
    'title'    => 'Datos del ítem',
    'fields'   => [
      ['key' => 'field_sa_orden', 'label' => 'Orden', 'name' => 'orden', 'type' => 'number', 'required' => 1, 'instructions' => '0 = primer ítem.'],
      ['key' => 'field_sa_titulo', 'label' => 'Título', 'name' => 'titulo', 'type' => 'text', 'required' => 1],
      ['key' => 'field_sa_contenido', 'label' => 'Contenido', 'name' => 'contenido', 'type' => 'textarea', 'rows' => 5, 'required' => 1, 'instructions' => 'Dejar una línea en blanco entre párrafos.'],
    ],
    'location' => [
      [['param' => 'post_type', 'operator' => '==', 'value' => 'servicio_acordeon']],
    ],
  ]);
});

// ── Migración one-time: tarjetas de características ─────────────────────────
add_action('init', function () {
  if (get_option('smart_feature_cards_migradas') === 'si') return;
  if (!function_exists('update_field')) return;

  update_option('smart_feature_cards_migradas', 'si');

  $cards = [
    // Home — "Elegí tu smart"
    ['slug' => 'home-1', 'seccion' => 'home', 'orden' => 0, 'titulo' => 'smart X BRABUS.', 'descripcion' => 'Rendimiento extremo. Diseño inconfundible. La versión más potente de smart lleva cada detalle al límite.', 'imagen' => 'home/carrusel/1.jpg', 'alt' => 'smart X BRABUS', 'disclaimer' => '', 'cta_texto' => 'Descubrilo', 'cta_link' => '/brabus/'],
    ['slug' => 'home-2', 'seccion' => 'home', 'orden' => 1, 'titulo' => 'Conectividad.', 'descripcion' => "Pantalla central de 12,8'', Apple CarPlay® y Android Auto® inalámbrico. El camino y tu mundo, integrados.", 'imagen' => 'home/carrusel/2.jpg', 'alt' => 'Conectividad', 'disclaimer' => '', 'cta_texto' => 'Conocé más', 'cta_link' => '/conectividad/'],
    ['slug' => 'home-3', 'seccion' => 'home', 'orden' => 2, 'titulo' => 'Movilidad eléctrica.', 'descripcion' => 'Hasta 597 km de autonomía y carga rápida en menos de 30 minutos. El futuro de la movilidad ya tiene forma.', 'imagen' => 'home/carrusel/3.jpg', 'alt' => 'Movilidad eléctrica', 'disclaimer' => 'Autonomía sujeta a tipo de conducción, condiciones del terreno y condiciones del clima.', 'cta_texto' => 'Explorá', 'cta_link' => '/movilidad-electrica/'],
    ['slug' => 'home-4', 'seccion' => 'home', 'orden' => 3, 'titulo' => 'Servicios al cliente.', 'descripcion' => 'Respaldo completo en cada kilómetro. Porque smart no termina en la compra.', 'imagen' => 'home/carrusel/4.png', 'alt' => 'Servicios al cliente', 'disclaimer' => '', 'cta_texto' => 'Ver servicios', 'cta_link' => '/servicios/'],
    // Conectividad — carrusel 1
    ['slug' => 'conectividad1-1', 'seccion' => 'conectividad_1', 'orden' => 0, 'titulo' => 'Hello smart App', 'descripcion' => 'Controlá tu auto desde tu celular: monitoreá la carga, programá la climatización antes de subir y localizá el vehículo en cualquier momento. Tu smart, siempre conectado a vos.', 'imagen' => 'conectividad/c1-1.png', 'alt' => 'Hello smart App', 'disclaimer' => '', 'cta_texto' => 'Descargar', 'cta_link' => '#'],
    ['slug' => 'conectividad1-2', 'seccion' => 'conectividad_1', 'orden' => 1, 'titulo' => 'Apple CarPlay® y Android Auto® inalámbrico', 'descripcion' => 'Tu smartphone se conecta sin cables. Llamadas, mapas, música y mensajes integrados, sin perder la vista del camino.', 'imagen' => 'conectividad/c1-2.png', 'alt' => 'Apple CarPlay', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 'conectividad1-3', 'seccion' => 'conectividad_1', 'orden' => 2, 'titulo' => 'Actualización OTA (Over-The-Air)', 'descripcion' => 'El software de tu smart se actualiza de forma remota. Sin ir al taller, sin interrupciones.', 'imagen' => 'conectividad/c1-3.png', 'alt' => 'Actualización OTA', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 'conectividad1-4', 'seccion' => 'conectividad_1', 'orden' => 3, 'titulo' => 'Keyless entry', 'descripcion' => 'Acercate a tu smart y las puertas se abren. Usar la llave es opcional.', 'imagen' => 'conectividad/c1-4.png', 'alt' => 'Keyless entry', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    // Conectividad — carrusel 2
    ['slug' => 'conectividad2-1', 'seccion' => 'conectividad_2', 'orden' => 0, 'titulo' => 'Asistente de voz', 'descripcion' => 'Controlá la navegación, el clima y los medios sin soltar el volante. El auto responde cuando hablás.', 'imagen' => 'conectividad/c2-1.png', 'alt' => 'Asistente de voz', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 'conectividad2-2', 'seccion' => 'conectividad_2', 'orden' => 1, 'titulo' => 'Pantalla central de 12,8"', 'descripcion' => 'Todo el control del vehículo en una pantalla táctil de alta resolución. Y frente al conductor, una pantalla de instrumentos de 9,2" con información de conducción a simple vista.', 'imagen' => 'conectividad/c2-2.png', 'alt' => 'Pantalla central 12.8', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 'conectividad2-3', 'seccion' => 'conectividad_2', 'orden' => 2, 'titulo' => 'Cuadro de instrumentos digital 9,2"', 'descripcion' => 'El cuadro de instrumentos de 9,2" es un panel de material LCD de alta definición que se usa para mostrar información de conducción, música y otros datos relacionados. Tiene una resolución de 1920x384 píxeles.', 'imagen' => 'conectividad/c2-3.png', 'alt' => 'Cuadro de instrumentos digital 9.2', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 'conectividad2-4', 'seccion' => 'conectividad_2', 'orden' => 3, 'titulo' => 'Head Up display 10"', 'descripcion' => 'La imagen se proyecta en la luna delantera del vehículo, por lo que el conductor no tiene que bajar la cabeza para obtener la información que necesita, lo cual ofrece una experiencia de conducción más segura.', 'imagen' => 'conectividad/c2-4.png', 'alt' => 'Head Up display 10', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    // Sobre smart — red de concesionarios
    ['slug' => 'sobre-smart-1', 'seccion' => 'sobre_smart', 'orden' => 0, 'titulo' => 'Diseño que tiene firma propia.', 'descripcion' => 'Cada modelo smart es reconocible a primera vista. El modelo ideado por Mercedes–Benz tiene líneas fluidas, proporciones equilibradas y detalles que hablan de un ADN de marca consistente a lo largo del tiempo.', 'imagen' => 'sobre-smart/carousel-1.jpg', 'alt' => '', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 'sobre-smart-2', 'seccion' => 'sobre_smart', 'orden' => 1, 'titulo' => '100% eléctrico.', 'descripcion' => 'La alianza con la preparadora alemana BRABUS le dió a smart una versión de alto rendimiento que no resigna nada del ADN eléctrico de la marca.', 'imagen' => 'sobre-smart/carousel-2.jpg', 'alt' => '', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 'sobre-smart-3', 'seccion' => 'sobre_smart', 'orden' => 2, 'titulo' => 'La colaboración BRABUS.', 'descripcion' => 'La alianza con la preparadora alemana BRABUS le dió a smart una versión de alto rendimiento que no resigna nada del ADN eléctrico de la marca.', 'imagen' => 'sobre-smart/carousel-3.jpg', 'alt' => '', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    // Movilidad eléctrica — cards
    ['slug' => 'movilidad-1', 'seccion' => 'movilidad_electrica', 'orden' => 0, 'titulo' => 'Hasta 584 km de autonomía WLTP', 'descripcion' => "En ciclo WLTP para el smart #1 Pro+.\nDistancia de sobra para la ciudad y los viajes que más importan.", 'imagen' => 'movilidad/card1.jpg', 'alt' => 'smart #1', 'disclaimer' => '*Autonomía sujeta a tipo de conducción, condiciones del terreno y condiciones del clima.', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 'movilidad-2', 'seccion' => 'movilidad_electrica', 'orden' => 1, 'titulo' => 'Sin ruido de motor. Sin combustible.', 'descripcion' => 'Sin ir a la estación de servicio. Solo manejar.', 'imagen' => 'movilidad/card2.png', 'alt' => 'smart #3', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 'movilidad-3', 'seccion' => 'movilidad_electrica', 'orden' => 2, 'titulo' => 'Recuperación de energía en cada frenada.', 'descripcion' => 'El sistema de frenos regenerativos convierte la energía cinética en carga para la batería. Menos frenadas, más kilómetros: la conducción eléctrica inteligente.', 'imagen' => 'movilidad/card3.png', 'alt' => 'BRABUS Performance', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
  ];

  foreach ($cards as $c) {
    if (get_page_by_path($c['slug'], OBJECT, 'feature_card')) continue;

    $post_id = wp_insert_post([
      'post_type'   => 'feature_card',
      'post_title'  => $c['titulo'],
      'post_name'   => $c['slug'],
      'post_status' => 'publish',
    ]);
    if (is_wp_error($post_id) || !$post_id) continue;

    update_field('seccion', $c['seccion'], $post_id);
    update_field('orden', $c['orden'], $post_id);
    update_field('titulo', $c['titulo'], $post_id);
    update_field('descripcion', $c['descripcion'], $post_id);
    update_field('imagen', $c['imagen'], $post_id);
    update_field('alt', $c['alt'], $post_id);
    update_field('disclaimer', $c['disclaimer'], $post_id);
    update_field('cta_texto', $c['cta_texto'], $post_id);
    update_field('cta_link', $c['cta_link'], $post_id);
  }
}, 20);

// ── Migración one-time: carruseles de características de smart1/smart3 ──────
add_action('init', function () {
  if (get_option('smart_feature_cards_s1s3_migradas') === 'si') return;
  if (!function_exists('update_field')) return;

  update_option('smart_feature_cards_s1s3_migradas', 'si');

  $cards = [
    // smart #1 — carrusel 1 (Exterior)
    ['slug' => 's1-c1-1', 'seccion' => 'smart1_c1', 'orden' => 0, 'titulo' => 'Los detalles son los que definen lo premium.', 'tag' => 'Exterior', 'descripcion' => 'El frente del smart #1 combina una firma lumínica LED de última generación con una parrilla sellada que anticipa su naturaleza 100% eléctrica.', 'imagen' => 'smart1/2.jpg', 'alt' => 'Vista frontal', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c1-2', 'seccion' => 'smart1_c1', 'orden' => 1, 'titulo' => 'Precisión en cada detalle.', 'tag' => '', 'descripcion' => 'Las ópticas del smart #1 integran tecnología LED en un diseño escultórico que refuerza el carácter premium del vehículo.', 'imagen' => 'smart1/3.jpg', 'alt' => 'Faros full LED', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c1-3', 'seccion' => 'smart1_c1', 'orden' => 2, 'titulo' => 'Una identidad visual que no pasa desapercibida.', 'tag' => '', 'descripcion' => 'Las manijas al ras del smart #1 se integran en la carrocería con una geometría limpia que refuerza tanto la estética como la eficiencia aerodinámica del vehículo.', 'imagen' => 'smart1/4.jpg', 'alt' => 'Manija electro-emergente', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c1-4', 'seccion' => 'smart1_c1', 'orden' => 3, 'titulo' => 'Funcionalidad sin compromisos.', 'tag' => '', 'descripcion' => 'Con hasta 313 litros de capacidad y apertura eléctrica manos libres, el baúl del smart #1 se adapta a cada necesidad con total comodidad.', 'imagen' => 'smart1/5.jpg', 'alt' => 'Portón trasero', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    // smart #1 — carrusel 2 (Interior)
    ['slug' => 's1-c2-1', 'seccion' => 'smart1_c2', 'orden' => 0, 'titulo' => 'Un espacio diseñado para quienes valoran el confort.', 'tag' => 'Interior', 'descripcion' => 'El habitáculo del smart #1 ofrece cinco plazas amplias, materiales de calidad superior y una atmósfera que eleva cada trayecto.', 'imagen' => 'smart1/teaser.png', 'alt' => 'Un espacio diseñado para quienes valoran el confort', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c2-2', 'seccion' => 'smart1_c2', 'orden' => 1, 'titulo' => 'Control total, siempre a mano.', 'tag' => '', 'descripcion' => 'El volante multifunción del smart #1 integra todos los comandos esenciales para una conducción segura, intuitiva y enfocada en el camino.', 'imagen' => 'smart1/8.jpg', 'alt' => 'Control total, siempre a mano', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c2-3', 'seccion' => 'smart1_c2', 'orden' => 2, 'titulo' => 'Tecnología al servicio del conductor.', 'tag' => '', 'descripcion' => 'La pantalla táctil de 12,8 pulgadas con smart OS centraliza navegación, conectividad y climatización en una interfaz clara y precisa.', 'imagen' => 'smart1/9.jpg', 'alt' => 'Tecnología al servicio del conductor', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c2-4', 'seccion' => 'smart1_c2', 'orden' => 3, 'titulo' => 'Conectividad continua, sin interrupciones.', 'tag' => '', 'descripcion' => 'La base de carga inalámbrica integrada mantiene el dispositivo cargado durante todo el recorrido, de forma simple y eficiente.', 'imagen' => 'smart1/10.jpg', 'alt' => 'Conectividad continua, sin interrupciones', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    // smart #1 — carrusel 3 (Confort)
    ['slug' => 's1-c3-1', 'seccion' => 'smart1_c3', 'orden' => 0, 'titulo' => 'Cámara 360.', 'tag' => 'Confort', 'descripcion' => 'Esta función permite seleccionar hasta nueve vistas diferentes con las distintas cámaras situadas alrededor del vehículo para ofrecer un control óptimo de la situación en todas las direcciones. *Disponible en todas las versiones, excepto Pure.', 'imagen' => 'smart1/confort-1.png', 'alt' => 'Cámara 360', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c3-2', 'seccion' => 'smart1_c3', 'orden' => 1, 'titulo' => 'Techo Halo panorámico.', 'tag' => '', 'descripcion' => 'El techo es una pieza de vidrio fija. El área del vidrio es de 1,5 m² y filtra el 99 % de la radiación ultravioleta. *Disponible en todas las versiones, excepto Pure.', 'imagen' => 'smart1/teaser5.png', 'alt' => 'Techo Halo panorámico', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c3-3', 'seccion' => 'smart1_c3', 'orden' => 2, 'titulo' => 'Asientos eléctricos y calefaccionados.', 'tag' => '', 'descripcion' => 'Los asientos delanteros presentan un ajuste eléctrico en seis direcciones y un soporte lumbar ajustable. El conductor cuenta también con función de memoria. *Disponible en todas las versiones, excepto Pure.', 'imagen' => 'smart1/15.jpg', 'alt' => 'Asientos eléctricos y calefaccionados', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c3-4', 'seccion' => 'smart1_c3', 'orden' => 3, 'titulo' => 'Climatización bizona.', 'tag' => '', 'descripcion' => 'Cada ocupante ajusta la temperatura de su zona de forma independiente. El sistema regula automáticamente la temperatura, el modo y el flujo de aire. *Disponible en todas las versiones, excepto Pure.', 'imagen' => 'smart1/16.jpg', 'alt' => 'Climatización bizona', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c3-5', 'seccion' => 'smart1_c3', 'orden' => 4, 'titulo' => 'Volante calefaccionado.', 'tag' => '', 'descripcion' => 'En condiciones de temperatura bajas, el volante calefactable ayuda a mejorar la experiencia de conducción.', 'imagen' => 'smart1/17.jpg', 'alt' => 'Volante calefaccionado', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c3-6', 'seccion' => 'smart1_c3', 'orden' => 5, 'titulo' => 'Sistema de audio Beats®.', 'tag' => '', 'descripcion' => '13 altavoces distribuidos estratégicamente en toda la cabina para una experiencia sonora de alta fidelidad. *Disponible para la versión BRABUS.', 'imagen' => 'smart1/18.jpg', 'alt' => 'Sistema de audio Beats', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c3-7', 'seccion' => 'smart1_c3', 'orden' => 6, 'titulo' => 'Luz ambiental.', 'tag' => '', 'descripcion' => 'Ajusta el brillo y el color de la iluminación ambiente a través de la pantalla de control central.', 'imagen' => 'smart1/19.png', 'alt' => 'Luz ambiental', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    // smart #1 — carrusel 4 (Seguridad)
    ['slug' => 's1-c4-1', 'seccion' => 'smart1_c4', 'orden' => 0, 'titulo' => 'smart pilot assist.', 'tag' => 'Seguridad', 'descripcion' => 'Ayuda al conductor a permanecer en su carril y permite configurar el intervalo de distancia con el vehículo de adelante, incluyendo ayuda para el adelantamiento de camiones. *Disponible en todas las versiones, excepto Pure.', 'imagen' => 'smart1/20.jpg', 'alt' => 'smart pilot assist', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c4-2', 'seccion' => 'smart1_c4', 'orden' => 1, 'titulo' => 'Parking Assist.', 'tag' => '', 'descripcion' => 'El sistema combina sensores ultrasónicos y cámaras para estacionar de manera 100% autónoma, controlando la dirección, la velocidad y el frenado.', 'imagen' => 'smart1/21.png', 'alt' => 'Parking Assist', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c4-3', 'seccion' => 'smart1_c4', 'orden' => 2, 'titulo' => 'Control crucero adaptativo.', 'tag' => '', 'descripcion' => 'Una vez ajustada la velocidad de crucero (de 0 a 150 km/h), el vehículo circula al valor establecido o lo ajusta automáticamente según el tráfico que tenga delante.', 'imagen' => 'smart1/22.jpg', 'alt' => 'Control crucero adaptativo', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c4-4', 'seccion' => 'smart1_c4', 'orden' => 3, 'titulo' => 'Airbags.', 'tag' => '', 'descripcion' => 'El smart #1 ofrece seis airbags: dos delanteros, dos laterales, dos de cortina lateral y uno central.', 'imagen' => 'smart1/23.jpg', 'alt' => 'Airbags', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c4-5', 'seccion' => 'smart1_c4', 'orden' => 4, 'titulo' => 'Asistencia mantenimiento de carril.', 'tag' => '', 'descripcion' => 'Previene desviaciones no intencionadas del carril a velocidades entre 60 y 180 km/h mediante advertencias y ajuste automático del volante.', 'imagen' => 'smart1/24.jpg', 'alt' => 'Asistencia mantenimiento de carril', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c4-6', 'seccion' => 'smart1_c4', 'orden' => 5, 'titulo' => 'Freno AEB.', 'tag' => '', 'descripcion' => 'Cuando el vehículo está a punto de colisionar, el sistema frena automáticamente para evitar o reducir el impacto. Disponible para peatones, bicicletas y vehículos.', 'imagen' => 'smart1/25.jpg', 'alt' => 'Freno AEB', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c4-7', 'seccion' => 'smart1_c4', 'orden' => 6, 'titulo' => 'CyberSparksLED.', 'tag' => '', 'descripcion' => 'Faros LED de bajo consumo que a más de 60 km/h y con suficiente distancia al vehículo de adelante activan automáticamente la luz larga sin deslumbrar.', 'imagen' => 'smart1/26.jpg', 'alt' => 'CyberSparksLED', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    // smart #1 — carrusel 5 (Conectividad)
    ['slug' => 's1-c5-1', 'seccion' => 'smart1_c5', 'orden' => 0, 'titulo' => 'Pantalla central 12,8".', 'tag' => 'Conectividad', 'descripcion' => 'La pantalla central de 12,8" es una pantalla táctil con una resolución de 1920×1080 píxeles y modos de día y noche. Superficie de la pantalla: 283×159 mm. Pantalla completa: 321×190 mm RAM de 12 GB, almacenamiento de 128 GB.', 'imagen' => 'smart1/27.jpg', 'alt' => 'Pantalla central 12,8', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c5-2', 'seccion' => 'smart1_c5', 'orden' => 1, 'titulo' => 'Cuadro de instrumentos digital 9,2".', 'tag' => '', 'descripcion' => 'El cuadro de instrumentos de 9,2" es un panel de material LCD de alta definición que se usa para mostrar información de conducción, música y otros datos relacionados. Tiene una resolución de 1920x384 píxeles.', 'imagen' => 'smart1/32.png', 'alt' => 'Cuadro de instrumentos digital 9,2', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c5-3', 'seccion' => 'smart1_c5', 'orden' => 2, 'titulo' => 'Head Up display 10".', 'tag' => '', 'descripcion' => 'Los grupos ópticos del smart #1 integran tecnología LED en un diseño escultórico que refuerza el carácter premium del vehículo. *Función disponible para la versión BRABUS.', 'imagen' => 'smart1/30.png', 'alt' => 'Head Up display 10', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    // smart #1 — carrusel 6 (Practicidad)
    ['slug' => 's1-c6-1', 'seccion' => 'smart1_c6', 'orden' => 0, 'titulo' => 'Baúl trasero eléctrico.', 'tag' => 'Practicidad', 'descripcion' => 'La apertura y el cierre del baúl trasero son automáticos y pueden accionarse desde el botón del portón, el comando interior o la llave con mando a distancia. Además, incorpora funciones de memoria y ajuste de apertura. Este sistema facilita el acceso a un baúl de 323 litros de capacidad, ampliable hasta 986 litros al rebatir los asientos traseros.', 'imagen' => 'smart1/33.png', 'alt' => 'Baúl trasero eléctrico', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c6-2', 'seccion' => 'smart1_c6', 'orden' => 1, 'titulo' => 'Baúl delantero.', 'tag' => '', 'descripcion' => 'Compartimento de 15 litros bajo el capó. Apertura manual y sellado con espuma.', 'imagen' => 'smart1/34.jpg', 'alt' => 'Baúl delantero', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's1-c6-3', 'seccion' => 'smart1_c6', 'orden' => 2, 'titulo' => 'Deslizamiento asientos traseros.', 'tag' => '', 'descripcion' => 'Los asientos traseros pueden deslizarse 130 mm para disponer de un espacio más amplio para las piernas o de mayor capacidad de almacenamiento en el baúl.', 'imagen' => 'smart1/35.jpg', 'alt' => 'Deslizamiento asientos traseros', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    // smart #3 — carrusel 1 (Exterior)
    ['slug' => 's3-c1-1', 'seccion' => 'smart3_c1', 'orden' => 0, 'titulo' => 'Dinamismo sin estridencias.', 'tag' => 'Exterior', 'descripcion' => 'El smart #3 ofrece un estilo deportivo refinado, con una respuesta eléctrica inmediata y una conducción ágil que hace honor a cada línea de su carrocería.', 'imagen' => 'smart3/2.png', 'alt' => 'Dinamismo sin estridencias', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c1-2', 'seccion' => 'smart3_c1', 'orden' => 1, 'titulo' => 'Una silueta que impacta desde cualquier ángulo.', 'tag' => '', 'descripcion' => 'El perfil de coupé del smart #3 combina un spoiler integrado y ópticas LED de diseño escultórico para una presencia inconfundible.', 'imagen' => 'smart3/3.png', 'alt' => 'Una silueta que impacta desde cualquier ángulo', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c1-3', 'seccion' => 'smart3_c1', 'orden' => 2, 'titulo' => 'Seguridad y garantía.', 'tag' => '', 'descripcion' => 'Cinco estrellas Euro NCAP, tres años de servicio de mantenimiento y ocho de asistencia en carretera. El smart #3 no solo redefine el diseño eléctrico: establece un nuevo estándar en seguridad y respaldo posventa.', 'imagen' => 'smart3/4.jpg', 'alt' => 'Seguridad y garantía', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c1-4', 'seccion' => 'smart3_c1', 'orden' => 3, 'titulo' => 'Los detalles son los que definen lo premium.', 'tag' => '', 'descripcion' => 'Las manijas al ras del smart #3 se integran en la carrocería con una geometría limpia que refuerza tanto la estética como la eficiencia aerodinámica del vehículo.', 'imagen' => 'smart3/5.jpg', 'alt' => 'Los detalles son los que definen lo premium', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    // smart #3 — carrusel 2 (Interior)
    ['slug' => 's3-c2-1', 'seccion' => 'smart3_c2', 'orden' => 0, 'titulo' => 'Tecnología al servicio del conductor.', 'tag' => 'Interior', 'descripcion' => 'La pantalla central del smart #3 integra Apple CarPlay™ y Android Auto™ en un sistema diseñado para mantener el foco donde corresponde: en el camino.', 'imagen' => 'smart3/8.png', 'alt' => 'Tecnología al servicio del conductor', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c2-2', 'seccion' => 'smart3_c2', 'orden' => 1, 'titulo' => 'El centro de comando a bordo.', 'tag' => '', 'descripcion' => 'El volante multifunción del smart #3 agrupa los controles principales en un diseño ergonómico y accesible, pensado para operar de forma natural sin distraer la conducción.', 'imagen' => 'smart3/9.png', 'alt' => 'El centro de comando a bordo', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c2-3', 'seccion' => 'smart3_c2', 'orden' => 2, 'titulo' => 'Conectividad continua, sin interrupciones.', 'tag' => '', 'descripcion' => 'La base de carga inalámbrica integrada mantiene el dispositivo cargado durante todo el recorrido, de forma simple y eficiente.', 'imagen' => 'smart3/10.jpg', 'alt' => 'Conectividad continua, sin interrupciones', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    // smart #3 — carrusel 3 (Confort)
    ['slug' => 's3-c3-1', 'seccion' => 'smart3_c3', 'orden' => 0, 'titulo' => 'Cámara 360.', 'tag' => 'Confort', 'descripcion' => 'Esta función permite seleccionar hasta nueve vistas diferentes con las distintas cámaras situadas alrededor del vehículo para ofrecer un control óptimo de la situación en todas las direcciones, lo cual resulta enormemente práctico tanto para estacionar como para desplazarse a velocidades reducidas.', 'imagen' => 'smart3/15.png', 'alt' => 'Cámara 360', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c3-2', 'seccion' => 'smart3_c3', 'orden' => 1, 'titulo' => 'Techo Halo panorámico.', 'tag' => '', 'descripcion' => 'El techo es una pieza de vidrio fija. El área del vidrio es de 1,3 m² y filtra el 99 % de la radiación ultravioleta.', 'imagen' => 'smart3/16.jpg', 'alt' => 'Techo Halo panorámico', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c3-3', 'seccion' => 'smart3_c3', 'orden' => 2, 'titulo' => 'Asientos eléctricos y calefaccionados.', 'tag' => '', 'descripcion' => 'Los asientos delanteros presentan un ajuste eléctrico en seis direcciones y un soporte lumbar eléctrico ajustable en cuatro direcciones. Los asientos del conductor y del acompañante son calefactables y el del conductor cuenta también con función de memoria.', 'imagen' => 'smart3/17.jpg', 'alt' => 'Asientos eléctricos y calefaccionados', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c3-4', 'seccion' => 'smart3_c3', 'orden' => 3, 'titulo' => 'Climatización bizona.', 'tag' => '', 'descripcion' => 'Los usuarios de los asientos delanteros establecen la temperatura del aire acondicionado en los lados izquierdo y derecho respectivamente. El sistema ajusta automáticamente la temperatura, el modo y el flujo de aire en las zonas izquierda y derecha.', 'imagen' => 'smart3/18.png', 'alt' => 'Climatización bizona', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c3-5', 'seccion' => 'smart3_c3', 'orden' => 4, 'titulo' => 'Volante calefaccionado.', 'tag' => '', 'descripcion' => 'En condiciones de temperatura bajas, el volante calefactable ayuda a mejorar la experiencia de conducción.', 'imagen' => 'smart3/19.jpg', 'alt' => 'Volante calefaccionado', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c3-6', 'seccion' => 'smart3_c3', 'orden' => 5, 'titulo' => 'Sistema de audio Beats®.', 'tag' => '', 'descripcion' => 'El vehículo incorpora 13 altavoces: 4 altavoces de agudos situados en los pilares A derecho e izquierdo y en la parte superior de las puertas traseras, 3 altavoces de graves y medios, 1 altavoz bajo y en el centro del panel de instrumentos, 2 altavoces de sonido envolvente en los pilares C, 2 altavoces de graves en la parte inferior de las puertas traseras, 1 altavoz de graves bajo el compartimento trasero. *Función disponible para la versión BRABUS.', 'imagen' => 'smart3/20.jpg', 'alt' => 'Sistema de audio Beats', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    // smart #3 — carrusel 4 (Seguridad)
    ['slug' => 's3-c4-1', 'seccion' => 'smart3_c4', 'orden' => 0, 'titulo' => 'smart pilot assist.', 'tag' => 'Seguridad', 'descripcion' => 'La función de asistencia en ruta (PAH) permite controlar el vehículo. Ayuda a que el conductor permanezca en su carril y permite configurar el intervalo de distancia con el vehículo de adelante. Esta función cuenta además con ayuda para el adelantamiento de camiones. (Función incluida en el equipamiento de seguridad inteligente de S4 ADAS).', 'imagen' => 'smart3/22.jpg', 'alt' => 'smart pilot assist', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c4-2', 'seccion' => 'smart3_c4', 'orden' => 1, 'titulo' => 'Parking Assist.', 'tag' => '', 'descripcion' => 'El sistema inteligente de smart combina sensores ultrasónicos y cámaras periféricas para estacionar de manera 100% autónoma (APA), controlando la dirección, la velocidad y el frenado. Además, incluye cámara de visión 360° y freno de emergencia automático para evitar colisiones a bajas velocidades.', 'imagen' => 'smart3/21-seguridad.png', 'alt' => 'Parking Assist', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c4-3', 'seccion' => 'smart3_c4', 'orden' => 2, 'titulo' => 'Control crucero adaptativo.', 'tag' => '', 'descripcion' => 'Una vez ajustada la velocidad de crucero (de 0 a 150 km/h), el vehículo circula al valor establecido o lo ajusta de forma automática en función de la velocidad y la distancia del vehículo que tenga delante. Si este frena o detiene a cero de forma repetitiva, el coche realiza la misma acción automáticamente a través de la función Stop&Go.', 'imagen' => 'smart3/24.jpg', 'alt' => 'Control crucero adaptativo', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c4-4', 'seccion' => 'smart3_c4', 'orden' => 3, 'titulo' => 'Airbags.', 'tag' => '', 'descripcion' => 'El smart #3 tiene siete airbags: dos delanteros, dos delanteros laterales, dos de cortina lateral y uno central.', 'imagen' => 'smart3/25.jpg', 'alt' => 'Airbags', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c4-5', 'seccion' => 'smart3_c4', 'orden' => 4, 'titulo' => 'Asistencia mantenimiento de carril.', 'tag' => '', 'descripcion' => 'El asistente de mantenimiento en el carril se emplea para ayudar a prevenir accidentes ocasionados por una desviación no intencionada del carril cuando el vehículo circula a velocidades de entre 60 y 180 km/h. Si el vehículo empieza a desviarse, el sistema lo indica mediante advertencias y el ajuste automático del volante, que devuelve el vehículo al carril actual.', 'imagen' => 'smart3/26.jpg', 'alt' => 'Asistencia mantenimiento de carril', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c4-6', 'seccion' => 'smart3_c4', 'orden' => 5, 'titulo' => 'Freno AEB.', 'tag' => '', 'descripcion' => 'Cuando el vehículo está a punto de chocar contra un objeto situado frente a él, el sistema lo frena por completo para reducir el daño. El freno puede alcanzar una fuerza máxima de 1 G y funciona para velocidades inferiores a los 60 km/h. Esta función está disponible para peatones, bicicletas y vehículos.', 'imagen' => 'smart3/27.jpg', 'alt' => 'Freno AEB', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c4-7', 'seccion' => 'smart3_c4', 'orden' => 6, 'titulo' => 'CyberSparksLED.', 'tag' => '', 'descripcion' => 'La tecnología LED se emplea para crear faros de bajo consumo que son duraderos y proporcionan el mayor alcance de iluminación. Cuando la velocidad supera los 60 km/h y la distancia al vehículo de delante es superior a 15 metros, el sistema controla automáticamente los faros de luz larga y baja, mejorando la visión del conductor y evitando el deslumbramiento de los demás conductores.', 'imagen' => 'smart3/28.jpg', 'alt' => 'CyberSparksLED', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    // smart #3 — carrusel 5 (Conectividad)
    ['slug' => 's3-c5-1', 'seccion' => 'smart3_c5', 'orden' => 0, 'titulo' => 'Pantalla central 12,8".', 'tag' => 'Conectividad', 'descripcion' => 'La pantalla central de 12,8" es una pantalla táctil con una resolución de 1920×1080 píxeles y modos de día y noche. Superficie de la pantalla: 283×159 mm. Pantalla completa: 321×190 mm. RAM de 12 GB, almacenamiento de 128 GB.', 'imagen' => 'smart3/30.png', 'alt' => 'Pantalla central 12,8', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c5-2', 'seccion' => 'smart3_c5', 'orden' => 1, 'titulo' => 'Cuadro de instrumentos digital 9,2".', 'tag' => '', 'descripcion' => 'El cuadro de instrumentos de 9,2" es un panel de material LCD de alta definición que se usa para mostrar información de conducción, música y otros datos relacionados. Tiene una resolución de 1920x384 píxeles.', 'imagen' => 'smart3/31.png', 'alt' => 'Cuadro de instrumentos digital 9,2', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c5-3', 'seccion' => 'smart3_c5', 'orden' => 2, 'titulo' => 'Head Up display 10".', 'tag' => '', 'descripcion' => 'Los grupos ópticos del smart #3 integran tecnología LED en un diseño escultórico que refuerza el carácter premium del vehículo. *Función disponible para la versión BRABUS.', 'imagen' => 'smart3/32.png', 'alt' => 'Head Up Display 10', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    // smart #3 — carrusel 6 (Practicidad)
    ['slug' => 's3-c6-1', 'seccion' => 'smart3_c6', 'orden' => 0, 'titulo' => 'Baúl trasero eléctrico.', 'tag' => 'Practicidad', 'descripcion' => 'La apertura y el cierre del baúl trasero son automáticos y pueden accionarse desde el botón del portón, el comando interior o la llave con mando a distancia. Además, incorpora funciones de memoria y ajuste de apertura. Este sistema facilita el acceso a un baúl de 323 litros de capacidad, ampliable hasta 986 litros al rebatir los asientos traseros.', 'imagen' => 'smart3/33.png', 'alt' => 'Baúl trasero eléctrico', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c6-2', 'seccion' => 'smart3_c6', 'orden' => 1, 'titulo' => 'Baúl delantero.', 'tag' => '', 'descripcion' => 'Compartimento de 15 litros bajo el capó. Apertura manual y sellado con espuma.', 'imagen' => 'smart3/34.jpg', 'alt' => 'Baúl delantero', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
    ['slug' => 's3-c6-3', 'seccion' => 'smart3_c6', 'orden' => 2, 'titulo' => 'Deslizamiento asientos traseros.', 'tag' => '', 'descripcion' => 'Los asientos traseros pueden deslizarse 130 mm para disponer de un espacio más amplio para las piernas o de mayor capacidad de almacenamiento en el baúl.', 'imagen' => 'smart3/35.jpg', 'alt' => 'Deslizamiento asientos traseros', 'disclaimer' => '', 'cta_texto' => '', 'cta_link' => ''],
  ];

  foreach ($cards as $c) {
    if (get_page_by_path($c['slug'], OBJECT, 'feature_card')) continue;

    $post_id = wp_insert_post([
      'post_type'   => 'feature_card',
      'post_title'  => $c['titulo'],
      'post_name'   => $c['slug'],
      'post_status' => 'publish',
    ]);
    if (is_wp_error($post_id) || !$post_id) continue;

    update_field('seccion', $c['seccion'], $post_id);
    update_field('orden', $c['orden'], $post_id);
    update_field('titulo', $c['titulo'], $post_id);
    update_field('tag', $c['tag'], $post_id);
    update_field('descripcion', $c['descripcion'], $post_id);
    // La imagen se importa aparte (una por una) para no agotar memoria en un solo request — ver Fase 6.
    update_field('alt', $c['alt'], $post_id);
    update_field('disclaimer', $c['disclaimer'], $post_id);
    update_field('cta_texto', $c['cta_texto'], $post_id);
    update_field('cta_link', $c['cta_link'], $post_id);
  }
}, 20);

// ── Migración one-time: acordeón de servicios ────────────────────────────────
add_action('init', function () {
  if (get_option('smart_servicio_acordeon_migrado') === 'si') return;
  if (!function_exists('update_field')) return;

  update_option('smart_servicio_acordeon_migrado', 'si');

  $items = [
    ['slug' => 'garantia', 'orden' => 0, 'titulo' => 'Garantía', 'contenido' => "3 años sin límites de kilometraje para componentes del vehículo.\nGarantía batería y componentes de alto voltaje: 8 años o 160.000 km. lo que ocurra primero."],
    ['slug' => 'asistencia-en-ruta', 'orden' => 1, 'titulo' => 'Asistencia en ruta', 'contenido' => 'Asistencia rápida donde te encuentres, las 24 horas del día. Frente a cualquier eventual problema en la ruta, te asistimos dentro del país y en países limítrofes.'],
    ['slug' => 'mantenimiento', 'orden' => 2, 'titulo' => 'Mantenimiento', 'contenido' => "Nuestro servicio oficial smart es tu mejor opción para mantenerlo siempre en perfecto estado. Gracias al servicio premium y la atención al detalle, podrás seguir disfrutando de tu smart con la misma confianza y placer que el primer día.\n\nEl mantenimiento debe realizarse cada 10.000 kilómetros o cada dos años, para garantizar que tu smart conserve su fiabilidad, seguridad y máximo rendimiento. En cada visita, nuestros técnicos especializados realizan un mantenimiento integral, siguiendo los estándares más exigentes de la marca y utilizando siempre piezas originales smart."],
    ['slug' => 'reparaciones', 'orden' => 3, 'titulo' => 'Reparaciones', 'contenido' => 'Podés contar con nosotros para cualquier tipo de solución que requiera tu smart.'],
    ['slug' => 'sustitucion-del-parabrisas', 'orden' => 4, 'titulo' => 'Sustitución del parabrisas', 'contenido' => 'Si el parabrisas sufre un daño por impacto de piedras, tu seguridad también se ve afectada. El parabrisas contribuye a la rigidez torsional de la carrocería. Si se rompe, no puede garantizarse plenamente su capacidad portante, sobre todo en situaciones críticas. Al sustituir el parabrisas, lo importante es la correcta ejecución del trabajo en un taller autorizado smart.'],
    ['slug' => 'frenos-y-pastillas-de-freno', 'orden' => 5, 'titulo' => 'Frenos y pastillas de freno', 'contenido' => 'Te ofrecemos un servicio especial en aras de tu seguridad: nuestro control de frenos. Para incrementar la seguridad en el tránsito vehicular es indispensable revisar periódicamente las pastillas de freno y la potencia de frenado. Por ello te recomendamos encargar la revisión de los frenos al menos una vez al año.'],
    ['slug' => 'cambio-de-bateria-12v', 'orden' => 6, 'titulo' => 'Cambio de batería 12v', 'contenido' => 'Si tu batería pierde rendimiento, la única solución suele ser cambiarla. Estaremos encantados de ayudarte y encargarnos de sustituir tu batería de 12v. Efectuaremos el cambio de forma rápida y sin complicaciones; naturalmente, con la alta calidad habitual de smart.'],
    ['slug' => 'repuestos-originales', 'orden' => 7, 'titulo' => 'Repuestos originales', 'contenido' => 'Si es necesario realizar una reparación o sustituir alguna pieza averiada, recomendamos utilizar repuestos originales smart. Todos estos repuestos están respaldados por el know-how de smart como fabricante, se desarrollan específicamente para tu modelo y se adaptan a la perfección a los demás componentes del vehículo.'],
    ['slug' => 'manuales-de-usuario', 'orden' => 8, 'titulo' => 'Manuales de usuario', 'contenido' => 'Navegá por el manual en línea o descargá el PDF del manual de usuario para acceder rápidamente cuando lo necesites.'],
  ];

  foreach ($items as $i) {
    if (get_page_by_path($i['slug'], OBJECT, 'servicio_acordeon')) continue;

    $post_id = wp_insert_post([
      'post_type'   => 'servicio_acordeon',
      'post_title'  => $i['titulo'],
      'post_name'   => $i['slug'],
      'post_status' => 'publish',
    ]);
    if (is_wp_error($post_id) || !$post_id) continue;

    update_field('orden', $i['orden'], $post_id);
    update_field('titulo', $i['titulo'], $post_id);
    update_field('contenido', $i['contenido'], $post_id);
  }
}, 20);

// ── Helper: tarjetas de características (usado por front-page/conectividad/etc) ─
function smart_get_feature_cards($seccion) {
  static $cache = [];
  if (isset($cache[$seccion])) return $cache[$seccion];

  $items = [];
  if (!function_exists('get_field')) return $cache[$seccion] = $items;

  $query = new WP_Query([
    'post_type'      => 'feature_card',
    'posts_per_page' => -1,
    'meta_query'     => [['key' => 'seccion', 'value' => $seccion]],
    'no_found_rows'  => true,
  ]);

  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();
      $id = get_the_ID();

      $titulo = (string) get_field('titulo', $id);
      $alt    = (string) get_field('alt', $id);

      $items[] = [
        'orden'       => (int) get_field('orden', $id),
        'titulo'      => $titulo,
        'tag'         => (string) get_field('tag', $id),
        'descripcion' => (string) get_field('descripcion', $id),
        'imagen'      => (string) get_field('imagen', $id),
        'alt'         => $alt !== '' ? $alt : $titulo,
        'disclaimer'  => (string) get_field('disclaimer', $id),
        'cta_texto'   => (string) get_field('cta_texto', $id),
        'cta_link'    => (string) get_field('cta_link', $id),
      ];
    }
    wp_reset_postdata();
  }

  usort($items, function ($a, $b) { return $a['orden'] <=> $b['orden']; });

  return $cache[$seccion] = $items;
}

// ── Helper: link de CTA de una tarjeta (interno vs. literal) ─────────────────
function smart_feature_card_link($cta_link) {
  if ($cta_link === '') return '';
  if ($cta_link[0] === '/') return home_url($cta_link);
  return $cta_link;
}

// ── Helper: ítems del acordeón de servicios ─────────────────────────────────
function smart_get_servicio_acordeon() {
  static $cache = null;
  if ($cache !== null) return $cache;

  $items = [];
  if (!function_exists('get_field')) return $cache = $items;

  $query = new WP_Query([
    'post_type'      => 'servicio_acordeon',
    'posts_per_page' => -1,
    'no_found_rows'  => true,
  ]);

  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();
      $id = get_the_ID();
      $items[] = [
        'orden'     => (int) get_field('orden', $id),
        'titulo'    => (string) get_field('titulo', $id),
        'contenido' => (string) get_field('contenido', $id),
      ];
    }
    wp_reset_postdata();
  }

  usort($items, function ($a, $b) { return $a['orden'] <=> $b['orden']; });

  return $cache = $items;
}

// ── CPT "Tipo de cookie" ─────────────────────────────────────────────────────
add_action('init', function () {
  register_post_type('cookie_tipo', [
    'labels' => [
      'name'          => 'Tipos de cookies',
      'singular_name' => 'Tipo de cookie',
      'add_new_item'  => 'Agregar tipo de cookie',
      'edit_item'     => 'Editar tipo de cookie',
      'all_items'     => 'Tipos de cookies',
      'search_items'  => 'Buscar tipo de cookie',
      'not_found'     => 'No se encontraron tipos de cookies',
    ],
    'public'          => false,
    'show_ui'         => true,
    'show_in_menu'    => true,
    'menu_icon'       => 'dashicons-privacy',
    'supports'        => ['title'],
    'has_archive'     => false,
    'rewrite'         => false,
    'capability_type' => 'post',
  ]);
});

// ── CPT "Contenido rico" (legales + historia institucional) ─────────────────
add_action('init', function () {
  register_post_type('contenido_wysiwyg', [
    'labels' => [
      'name'          => 'Contenido institucional',
      'singular_name' => 'Bloque de contenido',
      'add_new_item'  => 'Agregar bloque',
      'edit_item'     => 'Editar bloque',
      'all_items'     => 'Contenido institucional',
      'search_items'  => 'Buscar bloque',
      'not_found'     => 'No se encontraron bloques',
    ],
    'public'          => false,
    'show_ui'         => true,
    'show_in_menu'    => true,
    'menu_icon'       => 'dashicons-media-text',
    'supports'        => ['title'],
    'has_archive'     => false,
    'rewrite'         => false,
    'capability_type' => 'post',
  ]);
});

// ── Campos ACF: tipo de cookie ───────────────────────────────────────────────
add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  acf_add_local_field_group([
    'key'      => 'group_cookie_tipo',
    'title'    => 'Datos del tipo de cookie',
    'fields'   => [
      ['key' => 'field_ct_orden', 'label' => 'Orden', 'name' => 'orden', 'type' => 'number', 'required' => 1],
      ['key' => 'field_ct_titulo', 'label' => 'Título', 'name' => 'titulo', 'type' => 'text', 'required' => 1],
      ['key' => 'field_ct_descripcion', 'label' => 'Descripción', 'name' => 'descripcion', 'type' => 'textarea', 'rows' => 3, 'required' => 1],
      ['key' => 'field_ct_activo', 'label' => 'Activo por defecto', 'name' => 'activo_por_defecto', 'type' => 'true_false', 'instructions' => 'Muestra el círculo relleno (ej. "Necesarias", que siempre está activo).'],
    ],
    'location' => [
      [['param' => 'post_type', 'operator' => '==', 'value' => 'cookie_tipo']],
    ],
  ]);
});

// ── Campos ACF: contenido rico (legales / historia institucional) ───────────
add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  acf_add_local_field_group([
    'key'      => 'group_contenido_wysiwyg',
    'title'    => 'Datos del bloque',
    'fields'   => [
      [
        'key'      => 'field_cw_clave',
        'label'    => 'Ubicación',
        'name'     => 'clave',
        'type'     => 'select',
        'required' => 1,
        'choices'  => [
          'legal_propiedad_intelectual'     => 'Legales — Propiedad intelectual',
          'legal_afirmaciones_prospectivas' => 'Legales — Afirmaciones prospectivas',
          'historia_institucional'          => 'Sobre smart — Historia institucional',
          'proveedor_contacto'              => 'Proveedor — Datos de contacto',
          'proteccion_datos'                => 'Protección de Datos — Secciones',
          'codigo_integridad'               => 'Código de Integridad — Secciones',
        ],
      ],
      ['key' => 'field_cw_orden', 'label' => 'Orden', 'name' => 'orden', 'type' => 'number', 'required' => 1],
      ['key' => 'field_cw_titulo', 'label' => 'Título (opcional)', 'name' => 'titulo', 'type' => 'text', 'instructions' => 'Dejar vacío si el bloque no lleva título visible (ej. historia institucional).'],
      ['key' => 'field_cw_contenido', 'label' => 'Contenido', 'name' => 'contenido', 'type' => 'wysiwyg', 'required' => 1, 'tabs' => 'visual', 'toolbar' => 'full', 'media_upload' => 0],
    ],
    'location' => [
      [['param' => 'post_type', 'operator' => '==', 'value' => 'contenido_wysiwyg']],
    ],
  ]);
});

// ── Migración one-time: tipos de cookies ────────────────────────────────────
add_action('init', function () {
  if (get_option('smart_cookie_tipos_migrados') === 'si') return;
  if (!function_exists('update_field')) return;

  update_option('smart_cookie_tipos_migrados', 'si');

  $tipos = [
    ['slug' => 'necesarias', 'orden' => 0, 'titulo' => 'Cookies necesarias', 'descripcion' => 'Son imprescindibles para el funcionamiento básico de la página web. Sin estas cookies, la página web no puede funcionar correctamente.', 'activo' => 1],
    ['slug' => 'rendimiento', 'orden' => 1, 'titulo' => 'Cookies de rendimiento', 'descripcion' => 'Nos permiten analizar el uso de la página web para medir y mejorar su rendimiento. Toda la información que recopilan estas cookies es anónima.', 'activo' => 0],
    ['slug' => 'funcionalidad', 'orden' => 2, 'titulo' => 'Cookies de funcionalidad', 'descripcion' => 'Permiten que la página web recuerde las elecciones que realizás (como tu nombre de usuario o el idioma) y proporcione funciones mejoradas y más personalizadas.', 'activo' => 0],
    ['slug' => 'publicidad', 'orden' => 3, 'titulo' => 'Cookies de publicidad', 'descripcion' => 'Se utilizan para mostrarte publicidad más relevante para vos y tus intereses, en colaboración con socios seleccionados (entre otros, Google, Meta).', 'activo' => 0],
  ];

  foreach ($tipos as $t) {
    if (get_page_by_path($t['slug'], OBJECT, 'cookie_tipo')) continue;

    $post_id = wp_insert_post([
      'post_type'   => 'cookie_tipo',
      'post_title'  => $t['titulo'],
      'post_name'   => $t['slug'],
      'post_status' => 'publish',
    ]);
    if (is_wp_error($post_id) || !$post_id) continue;

    update_field('orden', $t['orden'], $post_id);
    update_field('titulo', $t['titulo'], $post_id);
    update_field('descripcion', $t['descripcion'], $post_id);
    update_field('activo_por_defecto', $t['activo'], $post_id);
  }
}, 20);

// ── Migración one-time: contenido rico (legales + historia institucional) ──
add_action('init', function () {
  if (get_option('smart_contenido_wysiwyg_migrado') === 'si') return;
  if (!function_exists('update_field')) return;

  update_option('smart_contenido_wysiwyg_migrado', 'si');

  $afirmaciones_items = [
    'una evolución desfavorable de la situación económica mundial, especialmente a causa del retroceso de la demanda en nuestros principales mercados destinatarios,',
    'un empeoramiento de nuestras posibilidades de refinanciación en los mercados crediticios y financieros,',
    'eventos inevitables de fuerza mayor, como catástrofes naturales, pandemias, actos de terrorismo, disturbios políticos, conflictos armados, accidentes industriales y sus consecuencias para nuestras actividades de venta, compra, producción o financiación,',
    'modificaciones de los tipos de cambio, las disposiciones aduaneras y de comercio exterior,',
    'cambios en los hábitos de consumo en favor de vehículos más pequeños con menor margen de beneficios o una posible pérdida de aceptación de nuestros productos y servicios que influya negativamente en la aplicación de nuestros precios y en el aprovechamiento de nuestras capacidades de producción,',
    'aumentos de los precios de los combustibles, las materias primas y la energía,',
    'interrupciones de la producción debido a dificultades de aprovisionamiento de material o energía, huelgas del personal o insolvencia de proveedores,',
    'descenso en los precios de reventa de los vehículos usados,',
    'el éxito en la implementación de medidas de reducción de costes y aumento de la eficiencia,',
    'las perspectivas comerciales de las sociedades de las que tenemos participaciones significativas,',
    'el éxito en la implementación de cooperaciones estratégicas y Joint Ventures,',
    'enmiendas de leyes, disposiciones y directivas oficiales, especialmente las relativas a las emisiones de los vehículos, el consumo de combustible y la seguridad,',
    'así como la conclusión de investigaciones realizadas por autoridades o encargadas por ellas que deriven o puedan derivar en procesos legales vinculados,',
    'y otros riesgos y factores imponderables, algunos de los cuales figuran en la memoria actual de la empresa, en la rúbrica «Informe sobre riesgos y oportunidades».',
  ];
  $afirmaciones_html = '<p>Esta página web contiene afirmaciones prospectivas, que se basan en nuestra estimación actual acerca de desarrollos futuros. Palabras como «anticipar», «asumir», «creer», «estimar», «prever», «pretender», «poder/podría», «planificar», «proyectar», «debería» y similares son características de estas afirmaciones. Estas afirmaciones están sujetas a diversos riesgos e incertidumbres. Algunos ejemplos de ello son:</p><ul>';
  foreach ($afirmaciones_items as $li) {
    $afirmaciones_html .= '<li>' . $li . '</li>';
  }
  $afirmaciones_html .= '</ul><p>En caso de que se dé uno de estos factores de inseguridad u otras incertidumbres, o en caso de que las suposiciones en que se basan tales afirmaciones prospectivas demuestren ser incorrectas, los resultados reales podrían diferir notablemente de los resultados expresados, implícita o explícitamente, en dichas afirmaciones. No pretendemos ni nos comprometemos a actualizar estas afirmaciones sobre previsiones de futuro periódicamente, puesto que estas se basan exclusivamente en las circunstancias que imperan el día en que se publican.</p>';

  $bloques = [
    [
      'slug'      => 'legal-propiedad-intelectual',
      'clave'     => 'legal_propiedad_intelectual',
      'orden'     => 0,
      'titulo'    => 'Propiedad intelectual',
      'contenido' => '<p>En el marco del uso de esta página web se debe tener en cuenta la propiedad intelectual (en particular los derechos de autor, de marca, de nombre y de patentes) de Prestige Auto SAU, smart Argentina o de terceros. El acceso a la página web no confiere ningún derecho de licencia o de uso sobre la propiedad intelectual de smart o de terceros.</p>',
    ],
    [
      'slug'      => 'legal-afirmaciones-prospectivas',
      'clave'     => 'legal_afirmaciones_prospectivas',
      'orden'     => 1,
      'titulo'    => 'Afirmaciones prospectivas',
      'contenido' => $afirmaciones_html,
    ],
    [
      'slug'      => 'historia-institucional',
      'clave'     => 'historia_institucional',
      'orden'     => 0,
      'titulo'    => '',
      'contenido' => '<p>Desde la fundación de la marca en la década de 1990, smart se ha mantenido comprometida con su visión de explorar las mejores soluciones para la movilidad urbana del futuro.</p>'
        . '<p>En 2019, Mercedes-Benz AG y Zhejiang Geely Holding Group establecieron el joint venture global de smart. Desde entonces, smart ha renovado exitosamente su marca, sus productos y su modelo de negocio, evolucionando hasta convertirse en una distintiva marca premium contemporánea de vehículos eléctricos. Actualmente, cuenta con una gama de productos en expansión y presencia global en más de 40 países y regiones.</p>'
        . '<p>Prestige Auto es el representante oficial de Mercedes-Benz (Autos y Vans) y de smart en Argentina. Lidera las operaciones de importación, distribución, ventas y posventa de estos vehículos en el país. Con una red de concesionarios en las principales ciudades, acompaña a cada cliente en cada etapa de su experiencia con la marca.</p>'
        . '<p>Nuestro compromiso es acercar la movilidad eléctrica premium a Argentina, con el respaldo de una marca global y el servicio de un equipo local dedicado a brindar la mejor experiencia de compra y posventa.</p>',
    ],
  ];

  foreach ($bloques as $b) {
    if (get_page_by_path($b['slug'], OBJECT, 'contenido_wysiwyg')) continue;

    $post_id = wp_insert_post([
      'post_type'   => 'contenido_wysiwyg',
      'post_title'  => $b['titulo'] !== '' ? $b['titulo'] : $b['slug'],
      'post_name'   => $b['slug'],
      'post_status' => 'publish',
    ]);
    if (is_wp_error($post_id) || !$post_id) continue;

    update_field('clave', $b['clave'], $post_id);
    update_field('orden', $b['orden'], $post_id);
    update_field('titulo', $b['titulo'], $post_id);
    update_field('contenido', $b['contenido'], $post_id);
  }
}, 20);

// ── Migración one-time: Proveedor + Código de Integridad ────────────────────
add_action('init', function () {
  if (get_option('smart_paginas_proveedor_integridad_migrado') === 'si') return;
  if (!function_exists('update_field')) return;

  update_option('smart_paginas_proveedor_integridad_migrado', 'si');

  $bloques = [
    [
      'slug'      => 'proveedor-datos-contacto',
      'clave'     => 'proveedor_contacto',
      'orden'     => 0,
      'titulo'    => '',
      'contenido' => '<p><strong>Prestige Auto SAU</strong><br>Lumina Panamericana – Edificio II<br>Sargento Cabral 3770, Munro.<br>(B1605EFJ) Vicente López.</p>'
        . '<p>Teléfono: 0800 66 MBENZ (62369)<br>Conmutador casa central: +5411-4469-9000<br>Atención de lunes a viernes de 8 a 17 hs.</p>'
        . '<p>Mail: <a href="mailto:hola@prestige-auto.com.ar">hola@prestige-auto.com.ar</a></p>',
    ],
    [
      'slug'      => 'codigo-integridad-1',
      'clave'     => 'codigo_integridad',
      'orden'     => 0,
      'titulo'    => 'Ámbito de aplicación',
      'contenido' => '<p>Este Código de Integridad define los lineamientos generales que esperamos rijan en la relación entre Prestige Auto y sus socios comerciales. Su finalidad es prevenir conductas inapropiadas, promover el comportamiento íntegro y transparente, y consolidar vínculos responsables y sostenibles a lo largo de toda la cadena de valor.</p>'
        . '<p>El Código promueve una actuación coherente con las normas jurídicas vigentes y con estándares éticos exigentes, fomentando una cultura de respeto, confianza y responsabilidad compartida. Se espera que cada tercero conozca, comprenda y aplique su contenido y que, ante dudas o dilemas, recurra a los canales establecidos para su interpretación o denuncia.</p>'
        . '<p><strong>¿Quiénes son nuestros Socios Comerciales?</strong> Contratistas, proveedores, concesionarios, talleres, socios comerciales en general y cualquier tercero que mantenga una relación comercial. En Prestige Auto esperamos que nuestros Socios Comerciales hagan extensivas estas obligaciones a sus colaboradores, su propia cadena de suministro y velen por su cumplimiento.</p>',
    ],
    [
      'slug'      => 'codigo-integridad-2',
      'clave'     => 'codigo_integridad',
      'orden'     => 1,
      'titulo'    => 'Misión, Visión y Valores',
      'contenido' => '<p><strong>Misión.</strong> Impulsamos una nueva era en la industria automotriz, desarrollando soluciones de movilidad que estimulen el progreso humano y económico.</p>'
        . '<p><strong>Visión.</strong> Ser referentes en movilidad inteligente en América Latina, integrando excelencia industrial con compromiso ambiental y social.</p>'
        . '<p><strong>Valores.</strong></p><ul>'
        . '<li><strong>Calidad que transforma.</strong> Nos aseguramos de que cada vehículo, cada proceso y cada decisión refleje nuestro compromiso con altos estándares de fabricación.</li>'
        . '<li><strong>Integridad que nos mueve.</strong> Actuamos con transparencia, responsabilidad y ética, construyendo relaciones de confianza duradera con todos nuestros públicos.</li>'
        . '<li><strong>Excelencia que trasciende.</strong> Buscamos superar constantemente las expectativas, promoviendo una cultura de mejora continua en cada área de nuestra operación industrial y comercial.</li>'
        . '<li><strong>Innovación con propósito.</strong> Diseñamos soluciones que respondan a los desafíos del presente y anticipen el futuro de la movilidad.</li>'
        . '<li><strong>Pasión que inspira.</strong> Somos impulsores de la transformación de la industria automotriz y motor de desarrollo humano.</li>'
        . '</ul>',
    ],
    [
      'slug'      => 'codigo-integridad-3',
      'clave'     => 'codigo_integridad',
      'orden'     => 2,
      'titulo'    => 'Integridad en Prestige Auto',
      'contenido' => '<p>Para nosotros, integridad significa tomar las decisiones correctas: es la coherencia entre lo que decimos y lo que hacemos. La integridad no es una meta sino una forma de ser y de hacer las cosas. Es el fundamento sobre el cual construimos relaciones duraderas, una reputación sólida y una cultura empresarial confiable.</p>'
        . '<p>El Código es la base de nuestro entendimiento compartido de los valores y nos guía en nuestros vínculos comerciales sostenibles a largo plazo.</p>'
        . '<p>Como Socio Comercial de Prestige Auto, se espera que tomen decisiones comerciales sobre una base correcta e íntegra.</p>',
    ],
    [
      'slug'      => 'codigo-integridad-4',
      'clave'     => 'codigo_integridad',
      'orden'     => 3,
      'titulo'    => 'Compliance Integral: Anticorrupción',
      'contenido' => '<p>En Prestige Auto no toleramos ninguna forma de corrupción ni en el ámbito público ni en el privado, ni prácticas comerciales desleales. La transparencia y la apertura son esenciales para sostener la confianza y la credibilidad en nuestras actividades y relaciones. Por ello, esperamos que nuestros Socios Comerciales (proveedores, clientes, concesionarios, contratistas, intermediarios y demás terceros) cumplan lo siguiente:</p><ul>'
        . '<li><strong>Tolerancia cero al soborno y la corrupción.</strong> No ofrecer, prometer, solicitar ni aceptar sobornos, comisiones indebidas, pagos facilitadores, ventajas personales, regalos no permitidos ni cualquier beneficio destinado a influir decisiones o asegurar tratos preferenciales.</li>'
        . '<li><strong>Conflictos de interés.</strong> Prevenir y declarar de inmediato toda situación real o aparente (vínculos personales/familiares, relaciones financieras o profesionales con personal de Prestige Auto). Las invitaciones y regalos deben ser apropiados, ocasionales y de valor simbólico; están prohibidos durante licitaciones, evaluaciones o negociaciones, y jamás se permite dinero en efectivo o equivalentes ni pagos a funcionarios públicos.</li>'
        . '<li><strong>Intermediarios y remuneraciones.</strong> Asegurar que los honorarios de consultores, agentes, brokers u otros intermediarios sean razonables, transparentes y no encubran ventajas indebidas; aplicar debida diligencia y cláusulas anticorrupción.</li>'
        . '</ul>',
    ],
    [
      'slug'      => 'codigo-integridad-5',
      'clave'     => 'codigo_integridad',
      'orden'     => 4,
      'titulo'    => 'Prevención de LA/FT y Defensa a la Competencia',
      'contenido' => '<p>En Prestige Auto esperamos que nuestros socios comerciales cumplan con normativas en materia de Prevención de Lavado de Activos y Financiación al Terrorismo, como también con normativas y buenas prácticas empresariales en materia de Defensa a la Competencia.</p><ul>'
        . '<li><strong>Prevención de LA/FT.</strong> Implementar controles acordes al riesgo (identificación y verificación de clientes/proveedores y beneficiarios finales, monitoreo de operaciones inusuales, registros y políticas internas). Abstenerse de pagos en efectivo inusuales, operar a/desde terceros no previstos o cuentas no informadas y de usar jurisdicciones de alto riesgo sin sustento legal/documental. Acreditar, cuando se requiera, cumplimientos legales e impositivos.</li>'
        . '<li><strong>Comercio exterior, sanciones y antiterrorismo.</strong> Cumplir las normas de importación/exportación, controles de exportación, sanciones y disposiciones contra el terrorismo internacional; no operar con personas o jurisdicciones restringidas.</li>'
        . '<li><strong>Defensa de la Competencia.</strong> Cumplir todas las leyes de defensa de la competencia: no realizar acuerdos o intercambios de información sensible que afecten precios, condiciones, estrategias, territorios/clientes o licitaciones, ni otras conductas que restrinjan ilegalmente la competencia.</li>'
        . '</ul>',
    ],
    [
      'slug'      => 'codigo-integridad-6',
      'clave'     => 'codigo_integridad',
      'orden'     => 5,
      'titulo'    => 'Protección de la Información y Datos Personales',
      'contenido' => '<p>En Prestige Auto esperamos que nuestros socios comerciales cuiden y adopten los recaudos necesarios para el cuidado de la información confidencial, sensible y personal que se obtenga en el marco de la relación.</p><ul>'
        . '<li><strong>Información confidencial y datos personales.</strong> Proteger la información no pública y los datos personales; usarlos solo con base legal y finalidad autorizada, aplicando necesidad/mínimo privilegio, almacenamiento seguro, controles de acceso y notificación inmediata de incidentes. Cumplir la normativa aplicable y alinearse a las políticas de Seguridad de la Información de Prestige Auto.</li>'
        . '<li><strong>Reporte y colaboración.</strong> Informar de inmediato cualquier hecho presuntamente delictivo o irregularidad que pueda afectar a Prestige Auto mediante los canales de denuncia; colaborar con debida diligencia, verificaciones o auditorías razonables y aplicar medidas correctivas cuando corresponda.</li>'
        . '<li><strong>Cadena de suministro.</strong> Seleccionar diligentemente a sus propios proveedores y subcontratistas.</li>'
        . '</ul>',
    ],
    [
      'slug'      => 'codigo-integridad-7',
      'clave'     => 'codigo_integridad',
      'orden'     => 6,
      'titulo'    => 'DD.HH. y Medio Ambiente',
      'contenido' => '<p>En Prestige Auto creemos que el éxito solo es auténtico cuando respeta la dignidad de las personas, cuida el entorno y aporta al bienestar colectivo. Por ello, esperamos que nuestros Socios Comerciales se conduzcan de acuerdo con los siguientes lineamientos y estándares:</p><ul>'
        . '<li><strong>Respeto de los Derechos Humanos y la legislación aplicable.</strong> Cumplir las leyes laborales y de Derechos Humanos, los convenios OIT y marcos como los Principios Rectores de la ONU.</li>'
        . '<li><strong>Trabajo digno y prohibiciones absolutas.</strong> No permitir trabajo infantil, forzoso o trata; no retener documentos; asegurar salarios y horarios acordes a ley.</li>'
        . '<li><strong>No discriminación y trato respetuoso.</strong> Prohibir toda discriminación, acoso o violencia; promover entornos seguros e inclusivos, respetando identidad u orientación sexual, género, edad, religión, nacionalidad, discapacidad, opiniones u otras condiciones.</li>'
        . '<li><strong>Libertad de asociación.</strong> Respetar la libertad de asociación y la negociación colectiva conforme la normativa aplicable.</li>'
        . '<li><strong>Seguridad y salud.</strong> Poner la seguridad de las personas primero: ambientes de trabajo seguros, entrenamiento adecuado y EPP; incorporar la prevención como práctica cotidiana.</li>'
        . '<li><strong>Debida diligencia en Derechos Humanos.</strong> Identificar y mitigar riesgos, contar con canales de denuncia sin represalias, cooperar con verificaciones/auditorías y remediar impactos si se detectan.</li>'
        . '<li><strong>Cumplimiento ambiental.</strong> Contar con los permisos, licencias y reportes ambientales vigentes, y disponer de sistemas de gestión ambiental certificados cuando corresponda. Acreditar evidencia cuando sea requerida.</li>'
        . '<li><strong>Gestión responsable de impactos.</strong> Gestionar residuos, efluentes y emisiones (incluidos ruidos/olores), manipular de forma segura sustancias peligrosas y contar con planes de contingencia.</li>'
        . '<li><strong>Eficiencia de recursos.</strong> Optimizar el uso de energía y agua y priorizar prevención, reducción, reutilización y reciclado a lo largo de las operaciones.</li>'
        . '<li><strong>Enfoque de ciclo de vida.</strong> Asegurar que productos y servicios sean seguros y minimicen impactos desde el diseño hasta la fabricación, logística, uso y disposición final.</li>'
        . '<li><strong>Incidentes y mejoras.</strong> Notificar de inmediato incidentes o no conformidades ambientales/ocupacionales, cooperar en la contención e implementar medidas correctivas con registros verificables.</li>'
        . '<li><strong>Cadena de suministro.</strong> Extender estas exigencias a subcontratistas y proveedores críticos y verificar su cumplimiento.</li>'
        . '</ul><p>Estas pautas son la base para proteger a las personas, preservar el ambiente y mantener la confianza que sostiene nuestra relación.</p>',
    ],
    [
      'slug'      => 'codigo-integridad-8',
      'clave'     => 'codigo_integridad',
      'orden'     => 7,
      'titulo'    => 'Canal de Denuncias',
      'contenido' => '<p>El contenido de este Código refleja el propio Código de Conducta de Prestige Auto, donde fomentamos una cultura de transparencia y confianza; por eso toda violación o indicio de violación al presente Código debe ser denunciada directamente a través de los canales de recepción de denuncias habilitados, ya sea a la gerencia de Compliance &amp; Auditoría Interna, o bien a través del Canal de Denuncias &ldquo;Línea Ética&rdquo;. El canal de denuncias está a disposición de empleados, proveedores, concesionarios y otros terceros vinculados a la compañía. Está gestionado de manera tercerizada por KPMG para garantizar confidencialidad, imparcialidad, transparencia y profesionalismo.</p>'
        . '<p>Este Canal de Denuncias está disponible para informar situaciones que puedan constituir:</p><ul>'
        . '<li>Violaciones a este Código.</li>'
        . '<li>Conductas irregulares o contrarias a las normativas, políticas, procedimientos y procesos.</li>'
        . '<li>Sospechas de fraude, corrupción, acoso, discriminación, conflicto de interés, entre otros.</li>'
        . '<li>Incumplimientos en materia de derechos laborales, protección de datos, seguridad ambiental o regulaciones legales.</li>'
        . '</ul><p><strong>¿Cómo acceder al Canal de Denuncias?</strong></p><ul>'
        . '<li>Línea telefónica gratuita: 0800-122-0396</li>'
        . '<li>Formulario web: prestige.lineaseticas.com</li>'
        . '<li>Correo electrónico: prestigelineaseticas@kpmg.com.ar</li>'
        . '<li>Escaneando el código QR disponible en el documento del Código de Integridad</li>'
        . '</ul><p><strong>Confidencialidad y protección contra represalias.</strong> Toda denuncia será tratada con el máximo nivel de reserva. No se tolerarán represalias, amenazas ni consecuencias adversas contra quienes reporten hechos de buena fe. La protección contra represalias incluye tanto al denunciante como a cualquier persona que colabore en una investigación. Si se realiza un informe sobre la conducta de un Socio Comercial, el Socio Comercial en cuestión deberá ayudar con la investigación y proporcionar acceso a cualquier información que razonablemente solicite Prestige Auto. También se espera que los Socios Comerciales tomen medidas para prevenir, detectar y corregir cualquier acción de represalia contra los denunciantes.</p>',
    ],
    [
      'slug'      => 'codigo-integridad-9',
      'clave'     => 'codigo_integridad',
      'orden'     => 8,
      'titulo'    => 'Cumplimiento del Código',
      'contenido' => '<p>Nuestros Socios Comerciales se asegurarán de que los principios de este Código se cumplan, comuniquen y comprendan en sus propias cadenas de suministro. Para ello, deberán conocer, aplicar y difundir estas pautas. También esperamos que el personal de nuestros Proveedores esté capacitado y en conocimiento de las políticas y buenas prácticas, como así también que mantengan sus políticas internas, libros y registros fieles y completos; acrediten cumplimientos legales e impositivos y permisos/licencias vigentes; y colaboren con auditorías, verificaciones y requerimientos razonables de información que disponga Prestige Auto, resguardando la confidencialidad.</p>'
        . '<p>En caso de desviaciones menores, el Socio Comercial deberá implementar medidas correctivas adecuadas y presentar evidencia de su ejecución dentro de un plazo razonable acordado con Prestige Auto.</p>'
        . '<p>En caso de incumplimientos graves o reiterados (en especial, violaciones legales o de alto riesgo), Prestige Auto podrá aplicar medidas contractuales proporcionales, que incluyen —sin limitarse a— advertencias formales, planes de remediación, auditorías (on/off-site), suspensión de accesos/pedidos/servicios, retención o diferimiento de pagos, rescisión con causa e inhabilitación para futuras contrataciones, reclamos por daños y perjuicios, y denuncias ante autoridades. Prestige Auto podrá adoptar medidas preventivas inmediatas cuando esté comprometida la seguridad, el cumplimiento normativo, la protección de datos/información, la calidad o la reputación.</p>',
    ],
  ];

  foreach ($bloques as $b) {
    if (get_page_by_path($b['slug'], OBJECT, 'contenido_wysiwyg')) continue;

    $post_id = wp_insert_post([
      'post_type'   => 'contenido_wysiwyg',
      'post_title'  => $b['titulo'] !== '' ? $b['titulo'] : $b['slug'],
      'post_name'   => $b['slug'],
      'post_status' => 'publish',
    ]);
    if (is_wp_error($post_id) || !$post_id) continue;

    update_field('clave', $b['clave'], $post_id);
    update_field('orden', $b['orden'], $post_id);
    update_field('titulo', $b['titulo'], $post_id);
    update_field('contenido', $b['contenido'], $post_id);
  }
}, 21);

// ── Migración one-time: Protección de Datos ──────────────────────────────────
add_action('init', function () {
  if (get_option('smart_pagina_proteccion_datos_migrado') === 'si') return;
  if (!function_exists('update_field')) return;

  update_option('smart_pagina_proteccion_datos_migrado', 'si');

  $bloques = [
    [
      'slug'      => 'proteccion-datos-0',
      'clave'     => 'proteccion_datos',
      'orden'     => 0,
      'titulo'    => 'Responsable del tratamiento',
      'contenido' => '<p><strong>Delegado de Protección de Datos:</strong><br>Prestige Auto SAU<br>Sargento Cabral N° 3770. Edificio II. Lumina Panamericana. Munro.<br>Vicente López. Pcia. de Buenos Aires. Argentina.<br>E-mail: <a href="mailto:compliance@prestige-auto.com.ar">compliance@prestige-auto.com.ar</a></p>'
        . '<p>De acuerdo con lo dispuesto en la Ley N.º 25.326 de Protección de Datos Personales y su normativa complementaria, Prestige Auto S.A.U. asume el compromiso de proteger la privacidad de los usuarios y de garantizar un tratamiento adecuado de la información personal que se recolecta a través de sus sitios web.</p>',
    ],
    [
      'slug'      => 'proteccion-datos-1',
      'clave'     => 'proteccion_datos',
      'orden'     => 1,
      'titulo'    => 'Alcance del tratamiento de los datos',
      'contenido' => '<p>Nos alegra que visites nuestra página web y agradecemos tu interés en nuestros productos y servicios. La protección de tus datos personales es una prioridad para nosotros.</p>'
        . '<p>En esta política te explicamos:</p><ul>'
        . '<li>Qué datos recolectamos.</li>'
        . '<li>Cómo y para qué fines los utilizamos.</li>'
        . '<li>La base legal que nos habilita a tratarlos.</li>'
        . '<li>Con quién podemos compartirlos.</li>'
        . '<li>Qué derechos tenés en relación con tus datos personales.</li>'
        . '</ul><p>Esta política se aplica únicamente a las actividades desarrolladas en el sitio web de Prestige Auto S.A.U. No cubre a sitios de terceros ni a redes sociales que puedan estar enlazadas desde nuestra web, los cuales cuentan con sus propias políticas de privacidad.</p>',
    ],
    [
      'slug'      => 'proteccion-datos-2',
      'clave'     => 'proteccion_datos',
      'orden'     => 2,
      'titulo'    => 'Obtención y tratamiento de datos personales',
      'contenido' => '<ul>'
        . '<li><strong>Datos de navegación.</strong> Al visitar nuestro sitio podemos registrar información sobre tu navegador, sistema operativo, fecha y hora de acceso, dirección IP, proveedor de internet, páginas consultadas y enlaces utilizados. Estos datos se conservan por un tiempo limitado con fines de seguridad y para mejorar la experiencia de navegación.</li>'
        . '<li><strong>Datos proporcionados voluntariamente.</strong> También recolectamos la información que nos brindes a través de formularios de contacto, encuestas, registros, concursos o para la ejecución de contratos y servicios.</li>'
        . '<li><strong>Carácter opcional.</strong> No estás obligado a proporcionar datos personales. Sin embargo, es posible que determinadas funciones de nuestras páginas web dependan de la cesión de datos personales. En estos casos, si no deseas cedernos tus datos personales, es posible que no puedas utilizar determinadas funciones, o que estén a tu disposición solo con ciertas restricciones.</li>'
        . '</ul>',
    ],
    [
      'slug'      => 'proteccion-datos-3',
      'clave'     => 'proteccion_datos',
      'orden'     => 3,
      'titulo'    => 'Uso previsto',
      'contenido' => '<ul>'
        . '<li>Utilizamos los datos personales obtenidos durante una visita a nuestras páginas web para operar estas páginas de un modo que resulte confortable para los visitantes, y para proteger nuestros sistemas informáticos de ataques y otras acciones ilegales.</li>'
        . '<li>En la medida en que nos comuniques otros datos personales, por ejemplo, en el marco de un registro, un chat, al cumplimentar un formulario de toma de contacto, en relación con una encuesta o un concurso, o para la conclusión y ejecución de un contrato, utilizamos estos datos para el uso mencionado, para tareas de administración de clientes y, si resulta necesario, para la tramitación y la liquidación de transacciones comerciales, y solamente en la medida en que sea necesario.</li>'
        . '<li>En relación con otros fines (por ejemplo, mostrar contenidos o publicidad personalizados, basados en tu comportamiento como usuario), utilizamos tus datos y, si es necesario, también los utilizan determinados terceros, siempre que hayas otorgado tu consentimiento (= autorización) en el marco de nuestro Sistema de gestión de consentimiento. Para la visualización de publicidad o contenidos personalizados de conformidad con el o los consentimiento(s) que hayas otorgado, podrán aplicarse procedimientos de análisis, incluida la elaboración de perfiles y el scoring. La elaboración de perfiles y el scoring nos permiten, por ejemplo, proporcionarte información personalizada y acorde con tus intereses, así como recomendaciones individuales sobre determinados modelos de vehículo.</li>'
        . '<li>Además, utilizamos datos personales en la medida en que estemos obligados a ello (por ejemplo, memorización de datos si es necesario para cumplir las obligaciones comerciales o fiscales de conservación de datos, comunicación en cumplimiento de las disposiciones administrativas o judiciales, por ejemplo, si así lo requieren los órganos de seguridad del estado).</li>'
        . '<li>Por lo demás, tratamos tus datos personales en la medida en que ello sea necesario para preservar nuestros intereses legítimos. En consecuencia, adaptamos continuamente las funciones y la oferta de nuestras páginas web a los requisitos que se desprenden de tu comportamiento como usuario, y utilizamos tus datos personales para fines internos de liquidación.</li>'
        . '</ul>',
    ],
    [
      'slug'      => 'proteccion-datos-4',
      'clave'     => 'proteccion_datos',
      'orden'     => 4,
      'titulo'    => 'Entrega de datos personales a terceros',
      'contenido' => '<p>Nuestras páginas web pueden contener también ofertas de terceros. Si hacés clic en una oferta de este tipo, transferiremos datos al proveedor correspondiente en la extensión necesaria (por ejemplo, la indicación de que has encontrado esta oferta en nuestras páginas y, en su caso, otras informaciones que hayas comunicado para este fin en nuestras páginas web).</p>'
        . '<p>Recurrimos además a proveedores cualificados para la operación, la optimización y la protección de nuestras páginas web (por ejemplo, proveedores de tecnologías informáticas, agencias de marketing). Solo transferimos datos personales a dichos proveedores en la medida en que sea necesario para la puesta a disposición y el uso de las páginas web y sus funciones, la satisfacción de intereses legítimos, el cumplimiento de obligaciones legales o en la medida en que hayas dado tu consentimiento.</p>',
    ],
    [
      'slug'      => 'proteccion-datos-5',
      'clave'     => 'proteccion_datos',
      'orden'     => 5,
      'titulo'    => 'Cookies',
      'contenido' => '<p>Nuestro sitio utiliza cookies y tecnologías similares para optimizar tu experiencia de navegación. Algunas son estrictamente necesarias para el funcionamiento del sitio; otras pueden usarse con fines estadísticos o publicitarios, siempre que hayas otorgado tu consentimiento.</p>'
        . '<p>Podés configurar tu navegador para bloquear, restringir o eliminar cookies en cualquier momento. Tené en cuenta que, al hacerlo, ciertas funcionalidades podrían no estar disponibles o verse limitadas.</p>',
    ],
    [
      'slug'      => 'proteccion-datos-6',
      'clave'     => 'proteccion_datos',
      'orden'     => 6,
      'titulo'    => 'Seguridad',
      'contenido' => '<p>Prestige Auto S.A.U. aplica medidas técnicas y organizativas adecuadas para proteger los datos personales contra el acceso no autorizado, pérdida, alteración o divulgación indebida, en línea con los estándares de la industria y la normativa vigente.</p>',
    ],
    [
      'slug'      => 'proteccion-datos-7',
      'clave'     => 'proteccion_datos',
      'orden'     => 7,
      'titulo'    => 'Base legal para el tratamiento de datos',
      'contenido' => '<ul>'
        . '<li>El tratamiento de datos personales realizado por Prestige Auto S.A.U. se encuentra amparado por la Ley N.º 25.326 de Protección de Datos Personales, su Decreto Reglamentario y por las disposiciones y criterios emitidos por la Agencia de Acceso a la Información Pública (AAIP), autoridad de aplicación en la materia.</li>'
        . '<li>El tratamiento de los datos se efectuará, en primer lugar, sobre la base del consentimiento previo, expreso, libre e informado del titular, conforme lo dispuesto en el artículo 5 de la Ley N.º 25.326. Dicho consentimiento podrá ser retirado en cualquier momento por el titular, sin que ello afecte la legitimidad de los tratamientos realizados con anterioridad a su revocación.</li>'
        . '<li>Los datos personales también podrán ser tratados en la medida en que ello resulte necesario para el cumplimiento de obligaciones legales, regulatorias, fiscales o administrativas que resulten aplicables a Prestige Auto S.A.U., así como para la ejecución, desarrollo y cumplimiento de relaciones contractuales asumidas con los titulares de los datos.</li>'
        . '<li>El tratamiento podrá igualmente efectuarse respecto de datos provenientes de registros, listas o bases de acceso público irrestricto, siempre que tales datos resulten adecuados, pertinentes y no excesivos en relación con la finalidad legítima que motivó su recolección, conforme lo autorizado por la normativa vigente.</li>'
        . '<li>Prestige Auto S.A.U. podrá asimismo tratar datos personales cuando dicho tratamiento resulte necesario para la satisfacción de intereses legítimos vinculados a la seguridad informática, la prevención del fraude, la mejora de la experiencia de los usuarios en el sitio web, la administración interna, la gestión de auditorías y controles internos, o cualquier otro fin compatible con su objeto social, siempre que tales intereses no prevalezcan sobre los derechos y libertades fundamentales de los titulares de los datos.</li>'
        . '<li>En todos los supuestos, Prestige Auto S.A.U. garantiza el respeto a los principios de licitud, lealtad, transparencia, finalidad, proporcionalidad y calidad de los datos, previstos en el artículo 4 de la Ley N.º 25.326, así como la aplicación de medidas técnicas y organizativas adecuadas que aseguren la confidencialidad, integridad y seguridad de la información, de conformidad con lo establecido en los artículos 9 y 10 de la normativa aplicable.</li>'
        . '</ul>',
    ],
    [
      'slug'      => 'proteccion-datos-8',
      'clave'     => 'proteccion_datos',
      'orden'     => 8,
      'titulo'    => 'Supresión de datos personales',
      'contenido' => '<p>Tu dirección IP y el nombre del proveedor de servicios de Internet se memorizan solamente por motivos de seguridad y se borran al cabo de siete días. Por lo demás, borramos tus datos personales en el momento en que expira el motivo para el que hemos obtenido y tratado tus datos. Una vez finalizado este periodo de tiempo, solamente memorizamos datos si ello es obligatorio según las leyes, los reglamentos y otras prescripciones legales aplicables, siempre que pueda asegurarse un nivel adecuado de protección de datos. En caso de que, en un caso específico, no sea posible eliminar los datos personales, dichos datos se marcarán con el objetivo de restringir su tratamiento futuro.</p>',
    ],
    [
      'slug'      => 'proteccion-datos-9',
      'clave'     => 'proteccion_datos',
      'orden'     => 9,
      'titulo'    => 'Derechos de los interesados',
      'contenido' => '<ul>'
        . '<li>El titular de los datos personales tiene derecho a solicitar y obtener información sobre sus propios datos que se encuentren en las bases de datos de Prestige Auto S.A.U., en cumplimiento de lo establecido en el artículo 14 de la Ley N.º 25.326.</li>'
        . '<li>El titular podrá asimismo solicitar la rectificación, actualización y, cuando corresponda, la supresión o confidencialidad de los datos personales que resulten incorrectos, incompletos o inexactos, de conformidad con lo previsto en el artículo 16 de la misma ley.</li>'
        . '<li>El ejercicio de estos derechos podrá realizarse en forma gratuita a intervalos no inferiores a seis meses, salvo que se acredite un interés legítimo al efecto conforme lo dispuesto en el artículo 14, inciso 3, de la Ley N.º 25.326.</li>'
        . '<li>En todos los casos, el titular podrá revocar el consentimiento previamente otorgado para el tratamiento de sus datos personales, sin efectos retroactivos sobre el tratamiento legítimamente efectuado con anterioridad a la revocación.</li>'
        . '<li>Para ejercer los derechos reconocidos en la Ley N.º 25.326, el titular podrá dirigir una solicitud a Prestige Auto S.A.U. a través del correo electrónico <a href="mailto:compliance@prestige-auto.com.ar">compliance@prestige-auto.com.ar</a>, o mediante presentación escrita en el domicilio indicado.</li>'
        . '<li>Se hace saber al titular de los datos que la Agencia de Acceso a la Información Pública, en su carácter de órgano de control de la Ley N.º 25.326, tiene la atribución de atender denuncias y reclamos que se interpongan en relación con el incumplimiento de las normas sobre protección de datos personales.</li>'
        . '</ul>',
    ],
    [
      'slug'      => 'proteccion-datos-10',
      'clave'     => 'proteccion_datos',
      'orden'     => 10,
      'titulo'    => 'Transmisión de datos a destinatarios fuera del EEE',
      'contenido' => '<ul>'
        . '<li>En caso de ser necesario transferir datos personales a destinatarios ubicados en países extranjeros, Prestige Auto S.A.U. garantizará el cumplimiento del artículo 12 de la Ley N.º 25.326, asegurando que dichos países proporcionen un nivel de protección adecuado o, en su defecto, que se suscriban acuerdos contractuales que aseguren condiciones equivalentes de seguridad y confidencialidad.</li>'
        . '<li>Desde el punto de vista de la Unión Europea, existe un nivel adecuado de protección en el tratamiento de datos personales, conforme con los estándares de la UE (según la llamada decisión de adecuación) en los países indicados a continuación: Andorra, Argentina, Canadá (restringida), Islas Feroe, Guernsey, Israel, Isla of Man, Japón, Jersey, Nueva Zelanda, Suiza y Uruguay. Hemos acordado con destinatarios de otros países que apliquen las cláusulas contractuales estándar de la UE, que asuman regulaciones corporativas vinculantes o que adopten otros mecanismos para crear un nivel apropiado de protección, conforme con las exigencias legales.</li>'
        . '</ul>',
    ],
  ];

  foreach ($bloques as $b) {
    if (get_page_by_path($b['slug'], OBJECT, 'contenido_wysiwyg')) continue;

    $post_id = wp_insert_post([
      'post_type'   => 'contenido_wysiwyg',
      'post_title'  => $b['titulo'] !== '' ? $b['titulo'] : $b['slug'],
      'post_name'   => $b['slug'],
      'post_status' => 'publish',
    ]);
    if (is_wp_error($post_id) || !$post_id) continue;

    update_field('clave', $b['clave'], $post_id);
    update_field('orden', $b['orden'], $post_id);
    update_field('titulo', $b['titulo'], $post_id);
    update_field('contenido', $b['contenido'], $post_id);
  }
}, 22);

// ── Helper: tipos de cookies (usado por page-cookies.php) ───────────────────
function smart_get_cookie_tipos() {
  static $cache = null;
  if ($cache !== null) return $cache;

  $items = [];
  if (!function_exists('get_field')) return $cache = $items;

  $query = new WP_Query([
    'post_type'      => 'cookie_tipo',
    'posts_per_page' => -1,
    'no_found_rows'  => true,
  ]);

  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();
      $id = get_the_ID();
      $items[] = [
        'orden'              => (int) get_field('orden', $id),
        'titulo'             => (string) get_field('titulo', $id),
        'descripcion'        => (string) get_field('descripcion', $id),
        'activo_por_defecto' => (bool) get_field('activo_por_defecto', $id),
      ];
    }
    wp_reset_postdata();
  }

  usort($items, function ($a, $b) { return $a['orden'] <=> $b['orden']; });

  return $cache = $items;
}

// ── Helper: bloques de contenido rico por clave (legales / historia) ────────
function smart_get_contenido($clave) {
  static $cache = [];
  if (isset($cache[$clave])) return $cache[$clave];

  $items = [];
  if (!function_exists('get_field')) return $cache[$clave] = $items;

  $query = new WP_Query([
    'post_type'      => 'contenido_wysiwyg',
    'posts_per_page' => -1,
    'meta_query'     => [['key' => 'clave', 'value' => $clave]],
    'no_found_rows'  => true,
  ]);

  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();
      $id = get_the_ID();
      $items[] = [
        'orden'     => (int) get_field('orden', $id),
        'titulo'    => (string) get_field('titulo', $id),
        'contenido' => (string) get_field('contenido', $id),
      ];
    }
    wp_reset_postdata();
  }

  usort($items, function ($a, $b) { return $a['orden'] <=> $b['orden']; });

  return $cache[$clave] = $items;
}

// ── Helper: importar un archivo del theme como adjunto de la Biblioteca de Medios ─
function smart_import_attachment_from_theme_path($ruta_relativa, $titulo = '') {
  $ruta_relativa = ltrim((string) $ruta_relativa, '/');
  if ($ruta_relativa === '') return 0;

  $existente = get_posts([
    'post_type'      => 'attachment',
    'posts_per_page' => 1,
    'meta_key'       => '_smart_ruta_original',
    'meta_value'     => $ruta_relativa,
    'fields'         => 'ids',
  ]);
  if (!empty($existente)) return (int) $existente[0];

  $archivo_origen = get_template_directory() . '/assets/img/' . $ruta_relativa;
  if (!file_exists($archivo_origen)) return 0;

  $contenido = file_get_contents($archivo_origen);
  $nombre    = basename($ruta_relativa);
  $subido    = wp_upload_bits($nombre, null, $contenido);
  if (!empty($subido['error'])) return 0;

  $tipo          = wp_check_filetype($subido['file'], null);
  $attachment_id = wp_insert_attachment([
    'post_mime_type' => $tipo['type'],
    'post_title'      => $titulo !== '' ? $titulo : preg_replace('/\.[^.]+$/', '', $nombre),
    'post_status'     => 'inherit',
  ], $subido['file']);
  if (is_wp_error($attachment_id) || !$attachment_id) return 0;

  if (!function_exists('wp_generate_attachment_metadata')) require_once ABSPATH . 'wp-admin/includes/image.php';
  $metadata = wp_generate_attachment_metadata($attachment_id, $subido['file']);
  wp_update_attachment_metadata($attachment_id, $metadata);
  update_post_meta($attachment_id, '_smart_ruta_original', $ruta_relativa);

  return $attachment_id;
}

// ── Migración one-time: convertir las imágenes de versión/tarjeta de ruta a adjunto real ─
add_action('init', function () {
  if (get_option('smart_imagenes_migradas_v1') === 'si') return;
  if (!function_exists('update_field')) return;

  update_option('smart_imagenes_migradas_v1', 'si');

  foreach (['version_vehiculo', 'feature_card'] as $post_type) {
    $q = new WP_Query(['post_type' => $post_type, 'posts_per_page' => -1, 'no_found_rows' => true]);
    if (!$q->have_posts()) continue;

    foreach ($q->posts as $post) {
      $id           = $post->ID;
      $valor_actual = get_post_meta($id, 'imagen', true);
      if (!is_string($valor_actual) || $valor_actual === '' || is_numeric($valor_actual)) continue;

      $attachment_id = smart_import_attachment_from_theme_path($valor_actual, get_the_title($id));
      if ($attachment_id) {
        update_field('imagen', $attachment_id, $id);
      }
    }
  }
}, 30);

// ── CPT "Hero de página" ─────────────────────────────────────────────────────
add_action('init', function () {
  register_post_type('hero_pagina', [
    'labels' => [
      'name'          => 'Heroes de página',
      'singular_name' => 'Hero',
      'add_new_item'  => 'Agregar hero',
      'edit_item'     => 'Editar hero',
      'all_items'     => 'Heroes de página',
      'search_items'  => 'Buscar hero',
      'not_found'     => 'No se encontraron heroes',
    ],
    'public'          => false,
    'show_ui'         => true,
    'show_in_menu'    => true,
    'menu_icon'       => 'dashicons-format-image',
    'supports'        => ['title'],
    'has_archive'     => false,
    'rewrite'         => false,
    'capability_type' => 'post',
  ]);
});

// ── Campos ACF: hero de página ───────────────────────────────────────────────
add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  acf_add_local_field_group([
    'key'      => 'group_hero_pagina',
    'title'    => 'Datos del hero',
    'fields'   => [
      [
        'key'      => 'field_hero_pagina',
        'label'    => 'Página',
        'name'     => 'pagina',
        'type'     => 'select',
        'required' => 1,
        'choices'  => [
          'home'                => 'Home',
          'smart1'              => 'smart #1',
          'smart3'              => 'smart #3',
          'brabus'              => 'smart x BRABUS',
          'conectividad'        => 'Conectividad',
          'servicios'           => 'Servicios al cliente',
          'movilidad_electrica' => 'Movilidad eléctrica',
          'sobre_smart'         => 'Sobre smart',
          'buscador'            => 'Buscador de concesionarios',
        ],
      ],
      ['key' => 'field_hero_desktop', 'label' => 'Imagen desktop', 'name' => 'hero_desktop', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],
      ['key' => 'field_hero_mobile', 'label' => 'Imagen mobile', 'name' => 'hero_mobile', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'instructions' => 'Opcional. Si se deja vacío se usa la imagen desktop también en mobile.'],
    ],
    'location' => [
      [['param' => 'post_type', 'operator' => '==', 'value' => 'hero_pagina']],
    ],
  ]);
});

// ── Migración one-time: heroes de página ─────────────────────────────────────
add_action('init', function () {
  if (get_option('smart_heroes_migrados') === 'si') return;
  if (!function_exists('update_field')) return;

  update_option('smart_heroes_migrados', 'si');

  $heroes = [
    ['slug' => 'hero-home',                 'pagina' => 'home',                'titulo' => 'Home',                       'desktop' => '',                             'mobile' => ''],
    ['slug' => 'hero-smart1',               'pagina' => 'smart1',              'titulo' => 'smart #1',                   'desktop' => 'smart1/hero.jpg',              'mobile' => 'smart1/hero-mobile.png'],
    ['slug' => 'hero-smart3',               'pagina' => 'smart3',              'titulo' => 'smart #3',                   'desktop' => 'smart3/hero.jpg',              'mobile' => 'smart3/hero-mobile.png'],
    ['slug' => 'hero-brabus',               'pagina' => 'brabus',              'titulo' => 'smart x BRABUS',             'desktop' => '',                             'mobile' => ''],
    ['slug' => 'hero-conectividad',         'pagina' => 'conectividad',        'titulo' => 'Conectividad',               'desktop' => 'conectividad/hero.jpg',        'mobile' => 'conectividad/hero-mobile.png'],
    ['slug' => 'hero-servicios',            'pagina' => 'servicios',           'titulo' => 'Servicios al cliente',       'desktop' => 'servicios/hero.jpg',           'mobile' => 'servicios/hero-mobile.png'],
    ['slug' => 'hero-movilidad-electrica',  'pagina' => 'movilidad_electrica', 'titulo' => 'Movilidad eléctrica',        'desktop' => 'movilidad/hero.jpg',           'mobile' => 'movilidad/hero-mobile.png'],
    ['slug' => 'hero-sobre-smart',          'pagina' => 'sobre_smart',         'titulo' => 'Sobre smart',                'desktop' => 'sobre-smart/hero.jpg',         'mobile' => 'sobre-smart/hero-mobile.png'],
    ['slug' => 'hero-buscador',             'pagina' => 'buscador',            'titulo' => 'Buscador de concesionarios', 'desktop' => 'buscador/hero.jpg',            'mobile' => 'buscador/hero-mobile.png'],
  ];

  foreach ($heroes as $h) {
    if (get_page_by_path($h['slug'], OBJECT, 'hero_pagina')) continue;

    $post_id = wp_insert_post([
      'post_type'   => 'hero_pagina',
      'post_title'  => $h['titulo'],
      'post_name'   => $h['slug'],
      'post_status' => 'publish',
    ]);
    if (is_wp_error($post_id) || !$post_id) continue;

    update_field('pagina', $h['pagina'], $post_id);

    if ($h['desktop'] !== '') {
      $attachment_id = smart_import_attachment_from_theme_path($h['desktop'], $h['titulo'] . ' — desktop');
      if ($attachment_id) update_field('hero_desktop', $attachment_id, $post_id);
    }
    if ($h['mobile'] !== '') {
      $attachment_id = smart_import_attachment_from_theme_path($h['mobile'], $h['titulo'] . ' — mobile');
      if ($attachment_id) update_field('hero_mobile', $attachment_id, $post_id);
    }
  }
}, 30);

// ── Migración: agrega la imagen mobile de servicios a un post hero-servicios
//    que ya existía sin ella (el array de arriba solo corre en la creación) ──
add_action('init', function () {
  if (!function_exists('update_field') || !function_exists('get_field')) return;
  $post = get_page_by_path('hero-servicios', OBJECT, 'hero_pagina');
  if (!$post || get_field('hero_mobile', $post->ID)) return;
  $attachment_id = smart_import_attachment_from_theme_path('servicios/hero-mobile.png', 'Servicios al cliente — mobile');
  if ($attachment_id) update_field('hero_mobile', $attachment_id, $post->ID);
}, 31);

// ── Migración: agrega la imagen mobile de conectividad a un post hero-conectividad
//    que ya existía sin ella (el array de arriba solo corre en la creación) ──
add_action('init', function () {
  if (!function_exists('update_field') || !function_exists('get_field')) return;
  $post = get_page_by_path('hero-conectividad', OBJECT, 'hero_pagina');
  if (!$post || get_field('hero_mobile', $post->ID)) return;
  $attachment_id = smart_import_attachment_from_theme_path('conectividad/hero-mobile.png', 'Conectividad — mobile');
  if ($attachment_id) update_field('hero_mobile', $attachment_id, $post->ID);
}, 31);

// ── Helper: hero de página (usado por todos los templates con hero) ────────
function smart_get_hero($pagina) {
  static $cache = [];
  if (isset($cache[$pagina])) return $cache[$pagina];

  $vacio = ['desktop' => '', 'mobile' => ''];
  if (!function_exists('get_field')) return $cache[$pagina] = $vacio;

  $post = get_page_by_path('hero-' . str_replace('_', '-', $pagina), OBJECT, 'hero_pagina');
  if (!$post) return $cache[$pagina] = $vacio;

  $desktop = (string) get_field('hero_desktop', $post->ID);
  $mobile  = (string) get_field('hero_mobile', $post->ID);

  return $cache[$pagina] = [
    'desktop' => $desktop,
    'mobile'  => $mobile !== '' ? $mobile : $desktop,
  ];
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
    . '<tr><td style="background:#141413;padding:18px 36px;"><p style="margin:0;color:#ffffff;opacity:0.35;font-size:11px;">© ' . esc_html(date('Y')) . ' smart Argentina</p></td></tr>'
    . '</table></td></tr></table></body></html>';

  $ok = wp_mail($to, $subject, $body, $headers);

  if ($ok) {
    wp_send_json_success();
  } else {
    wp_send_json_error(['message' => 'No se pudo enviar el mail'], 500);
  }
}
