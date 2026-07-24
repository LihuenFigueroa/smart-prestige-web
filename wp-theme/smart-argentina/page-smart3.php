<?php /* Template Name: Smart 3 */ ?>
<?php
$smart_versiones_s3 = smart_get_versiones('smart3');
$smart_hero_s3       = smart_get_hero('smart3');
$smart_carruseles_s3 = [
  smart_get_feature_cards('smart3_c1'),
  smart_get_feature_cards('smart3_c2'),
  smart_get_feature_cards('smart3_c3'),
  smart_get_feature_cards('smart3_c4'),
  smart_get_feature_cards('smart3_c5'),
  smart_get_feature_cards('smart3_c6'),
];
?>
<?php get_header(); ?>
<?php get_template_part('partials/header'); ?>
  <!-- ================================================================
       HERO MOBILE — smart #3
  ================================================================ -->
  <section id="hero-s3-mobile" style="position:relative; width:100%; aspect-ratio:375/812; overflow:hidden;">
    <img src="<?php echo esc_url($smart_hero_s3['mobile']); ?>" alt="smart #3" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; object-position:center;" />
    <div style="position:absolute; top:0; left:0; right:0; height:170px; z-index:5; pointer-events:none; background:linear-gradient(to bottom,rgba(20,20,19,0.65) 0%,rgba(20,20,19,0) 100%);"></div>
    <div style="position:absolute; bottom:0; left:0; right:0; height:55%; z-index:5; pointer-events:none; background:linear-gradient(to bottom,rgba(0,0,0,0) 0%,rgba(0,0,0,0.88) 100%);"></div>
    <nav style="position:absolute; top:0; left:0; right:0; z-index:20; padding:0 1.25rem; height:56px; display:flex; align-items:center;">
      <button onclick="openNavMenu()" class="flex flex-col gap-[5px] cursor-pointer" aria-label="Menú">
        <span class="w-5 h-px bg-white block"></span>
        <span class="w-5 h-px bg-white block"></span>
        <span class="w-5 h-px bg-white block"></span>
      </button>
      <div style="position:absolute; left:50%; transform:translateX(-50%);">
        <a href="<?php echo home_url('/'); ?>"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/Logonavbar.svg" alt="smart" style="height:1rem; width:auto;" /></a>
      </div>
    </nav>
    <div style="position:absolute; bottom:0; left:0; right:0; z-index:10; padding:0 24px 32px;">
      <h1 class="font-smart-next font-normal text-white" style="font-size:32px; line-height:1.15; margin-bottom:6px;">smart #3</h1>
      <p class="font-smart-sans text-white" style="font-size:14px; line-height:1.4; opacity:0.9; margin-bottom:2px;">SUV fastback 100% eléctrica.</p>
      <p class="font-smart-sans text-white" style="font-size:14px; line-height:1.4; opacity:0.9; margin-bottom:12px;">Hasta 597 km de autonomía WLTP y 0 a 100 en 3,7 segundos en su versión BRABUS.</p>
      <div>
        <a href="#contacto" class="font-smart-sans" style="display:inline-flex; align-items:center; height:40px; padding:0 24px; background:white; border-radius:9999px; font-size:14px; color:#141413; text-decoration:none; font-weight:700; letter-spacing:-0.025em;">Realizar consulta</a>
      </div>
    </div>
  </section>

  <!-- ================================================================
       HERO
  ================================================================ -->
  <section id="hero-s3" class="relative w-full h-screen min-h-[640px] overflow-hidden">
    <div class="absolute inset-0 bg-neutral-700">
      <img src="<?php echo esc_url($smart_hero_s3['desktop']); ?>" alt="smart #3" class="w-full h-full object-cover" />
    </div>
    <div class="absolute top-0 left-0 right-0 pointer-events-none" style="height:170px; z-index:5; background:linear-gradient(to bottom,rgba(20,20,19,0.65) 0%,rgba(20,20,19,0) 100%);"></div>


    <nav class="absolute top-0 left-0 right-0 z-20 px-5 md:px-14 h-14 md:h-16 flex items-center justify-between">
      <div class="flex items-center gap-6">
        <button onclick="openNavMenu()" class="flex flex-col gap-[5px] cursor-pointer" aria-label="Menú">
          <span class="w-5 h-px bg-white block"></span>
          <span class="w-5 h-px bg-white block"></span>
          <span class="w-5 h-px bg-white block"></span>
        </button>
        <div class="relative hidden md:block" id="modelos-dropdown">
          <button onclick="toggleModelosDropdown()" class="flex items-center gap-1 text-white text-sm font-normal uppercase tracking-wide leading-6">
            MODELOS
            <svg id="modelos-chevron" class="w-3 h-3 ml-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </button>
          <div id="modelos-menu" class="absolute top-full left-0 mt-1 min-w-[152px] z-50">
            <a href="<?php echo home_url('/smart-1/'); ?>" class="block px-6 py-4 text-[#141413] text-base font-smart-sans border-b border-neutral-200 hover:bg-neutral-100 transition-colors">smart #1</a>
            <a href="<?php echo home_url('/smart-3/'); ?>" class="block px-6 py-4 text-[#141413] text-base font-smart-sans hover:bg-neutral-100 transition-colors">smart #3</a>
          </div>
        </div>
      </div>
      <div class="absolute left-1/2 -translate-x-1/2">
        <a href="<?php echo home_url('/'); ?>"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/Logonavbar.svg" alt="smart" class="h-4 md:h-8 w-auto" /></a>
      </div>
    </nav>

    <div class="absolute bottom-0 left-0 right-0 pointer-events-none" style="height:320px; z-index:5; background:linear-gradient(to bottom,rgba(0,0,0,0) 0%,rgba(0,0,0,0.85) 100%);"></div>
    <div class="absolute bottom-0 left-0 right-0 z-10" style="padding-left:56px; padding-bottom:56px;">
      <h1 class="font-smart-next font-normal text-white" style="font-size:45px; line-height:1.1; margin-bottom:8px;">smart #3</h1>
      <p class="font-smart-sans text-white" style="font-size:18px; line-height:1.4; opacity:0.9; margin-bottom:2px;">SUV fastback 100% eléctrica.</p>
      <p class="font-smart-sans text-white" style="font-size:18px; line-height:1.4; opacity:0.9; margin-bottom:8px;">Hasta 597 km de autonomía WLTP y 0 a 100 en 3,7 segundos en su versión BRABUS.</p>
      <p class="font-smart-sans text-white" style="font-size:11px; line-height:1.4; opacity:0.65; margin-bottom:16px;">Autonomía sujeta a tipo de conducción, condiciones del terreno y condiciones del clima.</p>
      <div>
        <a href="#contacto" class="font-smart-sans" style="display:inline-flex; align-items:center; height:40px; padding:0 24px; background:white; border-radius:9999px; font-size:14px; color:#141413; text-decoration:none; font-weight:700; letter-spacing:-0.025em;">Realizar consulta</a>
      </div>
    </div>
  </section>

  <!-- ================================================================
       INTRO
  ================================================================ -->
  <section class="w-full bg-white px-5 md:px-14 py-2 md:py-16">
    <div class="max-w-[1320px] mx-auto">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/statement.svg" alt="" class="mob-only w-full" draggable="false" />
      <img src="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/headline.svg" alt="Hacé la diferencia. El smart #3 combina el arte y el rendimiento." class="hidden md:block w-full" draggable="false" />
    </div>
  </section>

  <!-- ================================================================
       CARRUSELES DE CARACTERÍSTICAS
  ================================================================ -->
  <section class="w-full bg-white pb-10">

    <?php foreach ($smart_carruseles_s3 as $i => $carrusel): $track_num = $i + 1; ?>
    <!-- ── Carrusel <?php echo $track_num; ?> ── -->
    <div style="padding-bottom:1rem; overflow:hidden;">
      <div id="track-c<?php echo $track_num; ?>" class="flex select-none" style="overflow-x:scroll; scrollbar-width:none; -ms-overflow-style:none; padding-left:1.25rem; padding-right:1.25rem; gap:1rem; cursor:grab;">

        <?php foreach ($carrusel as $card): ?>
        <div class="c-card">
          <img src="<?php echo esc_url($card['imagen']); ?>" alt="<?php echo esc_attr($card['alt']); ?>" class="c-card__img" />
          <?php if (!empty($card['tag'])): ?>
          <span class="c-card__tag"><?php echo esc_html($card['tag']); ?></span>
          <?php endif; ?>
          <div class="c-card__gradient"></div>
          <div class="c-card__body">
            <div class="c-card__text">
              <p class="c-card__title"><?php echo esc_html($card['titulo']); ?></p>
              <p class="c-card__desc"><?php echo esc_html($card['descripcion']); ?></p>
            </div>
          </div>
        </div>
        <?php endforeach; ?>

      </div>
    </div>
    <?php endforeach; ?>

  </section>

  <!-- ================================================================
       CARACTERÍSTICAS GENERALES
  ================================================================ -->
  <section id="caracteristicas" class="w-full bg-white px-5 md:px-14 py-10 md:py-16">
    <div class="max-w-[1320px] mx-auto">

      <p class="comp-header-label">smart #3</p>
      <h2 class="comp-header-title">Características generales</h2>

      <!-- Track de imágenes — solo mobile -->
      <div id="comp-s3-img-track" style="overflow-x:hidden;">
        <?php foreach ($smart_versiones_s3 as $v): ?>
        <img src="<?php echo esc_url($v['imagen']); ?>" alt="smart #3 <?php echo esc_attr($v['nombre_version']); ?>" style="width:calc(100vw - 2.5rem); aspect-ratio:3/2; object-fit:cover; object-position:center; flex-shrink:0; display:block;" />
        <?php endforeach; ?>
      </div>

      <!-- Nav flechas — solo mobile -->
      <div id="comp-s3-nav" style="align-items:center; justify-content:space-between; margin-bottom:1rem;">
        <div>
          <p class="comp-model">smart #3</p>
          <p class="comp-version" id="comp-s3-label">Pro</p>
        </div>
        <div style="display:flex; gap:1.5rem; align-items:center;">
          <button id="comp-s3-btn-prev" onclick="compS3Prev()" style="background:none;border:none;padding:0;cursor:pointer;line-height:0;opacity:0.25;transition:opacity 0.2s;pointer-events:none;" aria-label="Anterior">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.5 10.7949L7.14648 4.14844L7.85352 4.85547L1.20728 11.502H24V12.502H1.20703L7.85352 19.1484L7.14648 19.8555L0.5 13.2092C0.177734 12.887 0 12.4583 0 12.002C0 11.5457 0.177734 11.1169 0.5 10.7949Z" fill="#141413"/></svg>
          </button>
          <button id="comp-s3-btn-next" onclick="compS3Next()" style="background:none;border:none;padding:0;cursor:pointer;line-height:0;transition:opacity 0.2s;" aria-label="Siguiente">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g transform="translate(24,0) scale(-1,1)"><path d="M0.5 10.7949L7.14648 4.14844L7.85352 4.85547L1.20728 11.502H24V12.502H1.20703L7.85352 19.1484L7.14648 19.8555L0.5 13.2092C0.177734 12.887 0 12.4583 0 12.002C0 11.5457 0.177734 11.1169 0.5 10.7949Z" fill="#141413"/></g></svg>
          </button>
        </div>
      </div>

      <div id="comp-s3-container" style="overflow-x:auto; -webkit-overflow-scrolling:touch; scrollbar-width:none;">
        <div id="comp-s3-grid" style="display:grid; row-gap:12px; align-items:start;">

          <!-- ROW: imágenes -->
          <?php foreach ($smart_versiones_s3 as $v): ?>
          <img src="<?php echo esc_url($v['imagen']); ?>" alt="smart #3 <?php echo esc_attr($v['nombre_version']); ?>" style="width:100%; aspect-ratio:3/2; object-fit:cover; object-position:center;" />
          <?php endforeach; ?>

          <!-- ROW: modelo / versión -->
          <?php foreach ($smart_versiones_s3 as $v): ?>
          <div class="comp-ver-cell"><p class="comp-model">smart #3</p><p class="comp-version"><?php echo esc_html($v['nombre_version']); ?></p></div>
          <?php endforeach; ?>

          <!-- ROW: divider -->
          <?php foreach ($smart_versiones_s3 as $v): ?><hr class="comp-divider" /><?php endforeach; ?>

          <!-- ROW: autonomía -->
          <?php foreach ($smart_versiones_s3 as $v): ?>
          <div>
            <p class="comp-range-label">Autonomía</p>
            <p class="comp-range-num"><?php echo esc_html($v['autonomia_mixta']); ?></p>
            <p class="comp-range-sub">Autonomía WLTP<br>ciclo mixto</p>
            <p class="comp-range-num"><?php echo esc_html($v['autonomia_ciudad']); ?></p>
            <p class="comp-range-sub">Autonomía WLTP<br>ciudad</p>
          </div>
          <?php endforeach; ?>

          <!-- ROW: divider -->
          <?php foreach ($smart_versiones_s3 as $v): ?><hr class="comp-divider" /><?php endforeach; ?>

          <!-- ROW: mecánica -->
          <?php foreach ($smart_versiones_s3 as $v): ?>
          <ul class="comp-features<?php echo $v['destacado'] ? ' comp-features--bold' : ''; ?>">
            <?php foreach ($v['mecanica'] as $item): ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?>
          </ul>
          <?php endforeach; ?>

          <!-- ROW: divider -->
          <?php foreach ($smart_versiones_s3 as $v): ?><hr class="comp-divider" /><?php endforeach; ?>

          <!-- ROW: exterior -->
          <?php foreach ($smart_versiones_s3 as $v): ?>
          <ul class="comp-features<?php echo $v['destacado'] ? ' comp-features--bold' : ''; ?>">
            <?php foreach ($v['exterior'] as $item): ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?>
          </ul>
          <?php endforeach; ?>

          <!-- ROW: divider -->
          <?php foreach ($smart_versiones_s3 as $v): ?><hr class="comp-divider" /><?php endforeach; ?>

          <!-- ROW: interior -->
          <?php foreach ($smart_versiones_s3 as $v): ?>
          <ul class="comp-features<?php echo $v['destacado'] ? ' comp-features--bold' : ''; ?>">
            <?php foreach ($v['interior'] as $item): ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?>
          </ul>
          <?php endforeach; ?>

          <!-- ROW: divider -->
          <?php foreach ($smart_versiones_s3 as $v): ?><hr class="comp-divider" /><?php endforeach; ?>

          <!-- ROW: tecnología -->
          <?php foreach ($smart_versiones_s3 as $v): ?>
          <ul class="comp-features<?php echo $v['destacado'] ? ' comp-features--bold' : ''; ?>">
            <?php foreach ($v['tecnologia'] as $item): ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?>
          </ul>
          <?php endforeach; ?>

          <!-- ROW: divider -->
          <?php foreach ($smart_versiones_s3 as $v): ?><hr class="comp-divider" /><?php endforeach; ?>

          <!-- ROW: seguridad -->
          <?php foreach ($smart_versiones_s3 as $v): ?>
          <ul class="comp-features<?php echo $v['destacado'] ? ' comp-features--bold' : ''; ?>">
            <?php foreach ($v['seguridad'] as $item): ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?>
          </ul>
          <?php endforeach; ?>

        </div>
      </div>

    </div>
  </section>

  <!-- ================================================================
       ELEGÍ TU VERSIÓN (CONFIGURADOR)
  ================================================================ -->
  <section id="elegi-tu-version" class="w-full bg-white" style="overflow:hidden;">

    <!-- Top: título + info LEFT, toggle RIGHT -->
    <div id="vis-top-bar">

      <!-- Izquierda: título + descripción + CTA -->
      <div id="vis-left-content">
        <h2 class="font-smart-next" style="font-weight:400; color:#141413; line-height:1.1; margin:0 0 16px;">Elegí tu versión</h2>
        <p class="font-smart-sans" style="font-size:16px; color:#000000; line-height:1.5; margin:0 0 4px;">Coupé SUV 100% eléctrico. Hasta 597 km de autonomía WLTP y 428 CV en su versión BRABUS.</p>
        <p class="font-smart-sans" style="font-size:10px; color:#9ca3af; line-height:1.4; margin:0 0 12px;">Autonomía sujeta a tipo de conducción, condiciones del terrreno y condiciones del clima.</p>
        <div id="vis-btn-row">
          <button class="font-smart-sans" style="height:40px; padding:0 22px; border-radius:999px; background:#1A1A1A; font-size:13px; font-weight:700; color:#fff; cursor:pointer; border:none;">Ficha técnica</button>
          <a class="vis-manual-link font-smart-sans" href="<?php echo get_template_directory_uri(); ?>/assets/pdf/manual-smart3.pdf" download style="font-size:12px; color:#6B747B; text-decoration:underline;">Manual de usuario</a>
        </div>
      </div>

      <!-- Derecha: toggle exterior/interior -->
      <div id="vis-switcher" style="display:flex; align-items:center; background:white; border:1px solid #141413; border-radius:9999px; position:relative; overflow:hidden;">
        <div id="vis-indicator" style="position:absolute; top:0; left:0; height:100%; border-radius:9999px; background:#141413; z-index:0; transition:transform 0.4s cubic-bezier(0.4,0,0.2,1), width 0.4s cubic-bezier(0.4,0,0.2,1);"></div>
        <button id="vis-btn-ext" onclick="switchVis('ext')" class="font-smart-sans" style="font-weight:400; position:relative; z-index:1; color:#fff; border:none; background:transparent; cursor:pointer; transition:color 0.4s ease;">exterior</button>
        <button id="vis-btn-int" onclick="switchVis('int')" class="font-smart-sans" style="font-weight:400; position:relative; z-index:1; color:#141413; border:none; background:transparent; cursor:pointer; transition:color 0.4s ease;">interior</button>
      </div>

    </div>

    <!-- Stage: imagen full-width + selector debajo — interior cubre ambos -->
    <div id="vis-stage" style="overflow:visible;">

      <!-- Car wrapper: da la altura al stage -->
      <div id="vis-car-wrapper" style="position:relative; width:100%;">
        <img id="vis-car-back" src="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-c3.png" alt="" style="width:100%; max-height:62vh; display:block; object-fit:contain;" />
        <img id="vis-car-front" src="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-c3.png" alt="smart #3" style="position:absolute; inset:0; width:100%; height:100%; object-fit:contain; transition:opacity 0.4s cubic-bezier(0.25,0,0,1);" />
      </div>

      <!-- Interior carousel: cubre todo el stage (auto + barra) sin recortar -->
      <div id="vis-interior" style="position:absolute; z-index:5; transform:translateX(100%); transition:transform 0.52s cubic-bezier(0.25,0,0,1); overflow:hidden; cursor:grab;">
        <div id="vis-int-track" style="display:flex; height:100%; will-change:transform;"></div>
      </div>

      <!-- Barra inferior: z-index alto para flotar sobre el interior -->
      <div id="vis-bottom-bar" style="position:relative; z-index:20; display:flex; justify-content:space-between; align-items:flex-end;">

        <div id="vis-controls-card">
          <!-- Línea -->
          <div id="vis-linea-row" style="display:flex; align-items:center; gap:20px; margin-bottom:14px; transition:margin-bottom 0.4s cubic-bezier(0.25,0,0,1);">
            <span class="font-smart-sans" style="font-size:11px; color:#6B747B; font-weight:700; min-width:44px;">Línea</span>
            <button class="vis-linea-btn font-smart-sans" data-linea="BRABUS" style="font-size:13px; font-weight:700; color:#141413; background:none; border:none; cursor:pointer; padding:0;">BRABUS</button>
            <button class="vis-linea-btn font-smart-sans" data-linea="Pro"    style="font-size:13px; font-weight:400; color:#6B747B; background:none; border:none; cursor:pointer; padding:0;">Pro</button>
            <button class="vis-linea-btn font-smart-sans" data-linea="Pro+"   style="font-size:13px; font-weight:400; color:#6B747B; background:none; border:none; cursor:pointer; padding:0;">Pro+</button>
          </div>
          <!-- Color -->
          <div id="vis-color-row" style="display:flex; align-items:center; gap:10px; overflow:hidden; max-height:68px; padding:4px 5px 4px 0; transition:max-height 0.4s cubic-bezier(0.25,0,0,1), padding-top 0.4s cubic-bezier(0.25,0,0,1), padding-bottom 0.4s cubic-bezier(0.25,0,0,1);">
            <span class="font-smart-sans" style="font-size:11px; color:#6B747B; font-weight:700; min-width:44px;">Color</span>
            <div id="vis-color-wrap" style="display:flex; gap:10px; transition:opacity 0.3s ease;">
              <div id="vis-swatches-BRABUS" style="display:flex; gap:10px;">
                <button class="vis-color-btn" data-color="3" data-img="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-c3.png" style="width:28px;height:28px;border-radius:50%;background:#e05a1a;border:none;cursor:pointer;box-shadow:0 0 0 2px #fff,0 0 0 3.5px #141413;"></button>
                <button class="vis-color-btn" data-color="1" data-img="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-c1.png" style="width:28px;height:28px;border-radius:50%;background:linear-gradient(to bottom,#f0f0f0 50%,#1c1c1c 50%);border:none;cursor:pointer;"></button>
                <button class="vis-color-btn" data-color="2" data-img="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-c2.png" style="width:28px;height:28px;border-radius:50%;background:#252525;border:none;cursor:pointer;"></button>
                <button class="vis-color-btn" data-color="4" data-img="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-c6.png" style="width:28px;height:28px;border-radius:50%;background:linear-gradient(to bottom,#c41920 50%,#1c1c1c 50%);border:none;cursor:pointer;"></button>
                <button class="vis-color-btn" data-color="5" data-img="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-c5.png" style="width:28px;height:28px;border-radius:50%;background:#4c5058;border:none;cursor:pointer;"></button>
                <button class="vis-color-btn" data-color="6" data-img="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-c4.png" style="width:28px;height:28px;border-radius:50%;background:linear-gradient(to bottom,#1c1c1c 50%,#c0201e 50%);border:none;cursor:pointer;"></button>
                <button class="vis-color-btn" data-color="7" data-img="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-c7.png" style="width:28px;height:28px;border-radius:50%;background:linear-gradient(to bottom,#4c5058 50%,#c0201e 50%);border:none;cursor:pointer;"></button>
              </div>
              <div id="vis-swatches-Pro" style="display:none; gap:10px;">
                <button class="vis-color-btn" data-color="pro1" data-img="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-pro-c1.png" style="width:28px;height:28px;border-radius:50%;background:linear-gradient(to bottom,#c4c020 50%,#1c1c1c 50%);border:none;cursor:pointer;"></button>
                <button class="vis-color-btn" data-color="pro2" data-img="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-pro-c2.png" style="width:28px;height:28px;border-radius:50%;background:#b8bcc0;border:none;cursor:pointer;"></button>
                <button class="vis-color-btn" data-color="pro3" data-img="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-pro-c3.png" style="width:28px;height:28px;border-radius:50%;background:#232327;border:none;cursor:pointer;"></button>
                <button class="vis-color-btn" data-color="pro4" data-img="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-pro-c4.png" style="width:28px;height:28px;border-radius:50%;background:#f0f0f0;border:1px solid #ddd;cursor:pointer;"></button>
                <button class="vis-color-btn" data-color="pro5" data-img="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-pro-c5.png" style="width:28px;height:28px;border-radius:50%;background:#8aaa8e;border:none;cursor:pointer;"></button>
              </div>
              <div id="vis-swatches-Pro+" style="display:none; gap:10px;">
                <button class="vis-color-btn" data-color="pp1" data-img="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-prop-c1.png" style="width:28px;height:28px;border-radius:50%;background:linear-gradient(to bottom,#c4c020 50%,#1c1c1c 50%);border:none;cursor:pointer;"></button>
                <button class="vis-color-btn" data-color="pp2" data-img="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-prop-c2.png" style="width:28px;height:28px;border-radius:50%;background:#b8bcc0;border:none;cursor:pointer;"></button>
                <button class="vis-color-btn" data-color="pp3" data-img="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-prop-c3.png" style="width:28px;height:28px;border-radius:50%;background:#232327;border:none;cursor:pointer;"></button>
                <button class="vis-color-btn" data-color="pp4" data-img="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-prop-c4.png" style="width:28px;height:28px;border-radius:50%;background:#f0f0f0;border:1px solid #ddd;cursor:pointer;"></button>
                <button class="vis-color-btn" data-color="pp5" data-img="<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-prop-c5.png" style="width:28px;height:28px;border-radius:50%;background:#8aaa8e;border:none;cursor:pointer;"></button>
              </div>
            </div>
          </div>
        </div>

        <a id="vis-manual-link" href="<?php echo get_template_directory_uri(); ?>/assets/pdf/manual-smart3.pdf" download class="font-smart-sans" style="font-size:12px; color:#141413; text-decoration:underline; padding-bottom:1px; transition:color 0.3s;">Manual de usuario</a>

      </div><!-- end vis-bottom-bar -->

    </div><!-- end vis-stage -->

  </section>

  <!-- ================================================================
       FORMULARIO
  ================================================================ -->
  <?php get_template_part('partials/form-contacto'); ?>

  <!-- ================================================================
       FOOTER
  ================================================================ -->
  <?php get_template_part('partials/footer'); ?>


  <script>
    // ── Características generales — nav flechas mobile ───────────────────────
    var compS3Index = 0;
    var compS3Labels = <?php echo wp_json_encode(array_map(function ($v) { return $v['nombre_version']; }, $smart_versiones_s3)); ?>;
    function compS3UpdateArrows() {
      var p = document.getElementById('comp-s3-btn-prev');
      var n = document.getElementById('comp-s3-btn-next');
      if (p) { p.style.opacity = compS3Index === 0 ? '0.25' : '1'; p.style.pointerEvents = compS3Index === 0 ? 'none' : ''; }
      if (n) { n.style.opacity = compS3Index === 2 ? '0.25' : '1'; n.style.pointerEvents = compS3Index === 2 ? 'none' : ''; }
    }
    function compS3Prev() {
      if (compS3Index > 0) { compS3Index--; compS3Update(); }
    }
    function compS3Next() {
      if (compS3Index < 2) { compS3Index++; compS3Update(); }
    }
    function compS3Update() {
      var c = document.getElementById('comp-s3-container');
      var t = document.getElementById('comp-s3-img-track');
      c.scrollTo({ left: compS3Index * (c.scrollWidth / 3), behavior: 'smooth' });
      if (t) t.scrollTo({ left: compS3Index * (t.scrollWidth / 3), behavior: 'smooth' });
      document.getElementById('comp-s3-label').textContent = compS3Labels[compS3Index];
      compS3UpdateArrows();
    }
    // Sync imagen y label al swipear manualmente
    (function() {
      var timer;
      var c = document.getElementById('comp-s3-container');
      if (!c) return;
      c.addEventListener('scroll', function() {
        clearTimeout(timer);
        timer = setTimeout(function() {
          var t = document.getElementById('comp-s3-img-track');
          var colW = c.scrollWidth / 3;
          var idx = Math.round(c.scrollLeft / colW);
          idx = Math.max(0, Math.min(2, idx));
          if (idx !== compS3Index) {
            compS3Index = idx;
            document.getElementById('comp-s3-label').textContent = compS3Labels[idx];
            if (t) t.scrollTo({ left: idx * (t.scrollWidth / 3), behavior: 'smooth' });
            compS3UpdateArrows();
          }
        }, 80);
      });
    })();

    // ── Carruseles drag-to-scroll con inercia ────────────────────────────────
    (function () {
      function initDragCarousel(trackId) {
        const track = document.getElementById(trackId);
        if (!track) return;
        track.style.willChange = 'scroll-position';
        track.style.webkitOverflowScrolling = 'touch';

        let isDragging  = false;
        let hasMoved    = false;
        let startX      = 0;
        let startScroll = 0;
        let lastX       = 0;
        let lastTime    = 0;
        let velocity    = 0;
        let rafId       = null;

        function momentum() {
          velocity *= 0.94;
          track.scrollLeft -= velocity;
          if (Math.abs(velocity) > 0.3) rafId = requestAnimationFrame(momentum);
        }

        // ── Mouse ──
        track.addEventListener('mousedown', function (e) {
          cancelAnimationFrame(rafId);
          isDragging  = true;
          hasMoved    = false;
          startX      = e.clientX;
          startScroll = track.scrollLeft;
          lastX       = e.clientX;
          lastTime    = performance.now();
          velocity    = 0;
          track.style.cursor = 'grabbing';
        });

        window.addEventListener('mouseup', function () {
          if (!isDragging) return;
          isDragging = false;
          track.style.cursor = 'grab';
          rafId = requestAnimationFrame(momentum);
        });

        track.addEventListener('mousemove', function (e) {
          if (!isDragging) return;
          const delta = e.clientX - startX;
          if (Math.abs(delta) > 4) hasMoved = true;
          const now = performance.now();
          const dt  = now - lastTime || 1;
          velocity  = ((e.clientX - lastX) / dt) * 16;
          lastX     = e.clientX;
          lastTime  = now;
          track.scrollLeft = startScroll - delta;
        });

        track.addEventListener('click', function (e) {
          if (hasMoved) e.preventDefault();
        }, true);

        // ── Touch ──
        track.addEventListener('touchstart', function (e) {
          cancelAnimationFrame(rafId);
          isDragging  = true;
          hasMoved    = false;
          startX      = e.touches[0].clientX;
          startScroll = track.scrollLeft;
          lastX       = e.touches[0].clientX;
          lastTime    = performance.now();
          velocity    = 0;
        }, { passive: true });

        track.addEventListener('touchmove', function (e) {
          if (!isDragging) return;
          const delta = e.touches[0].clientX - startX;
          if (Math.abs(delta) > 4) hasMoved = true;
          const now = performance.now();
          const dt  = now - lastTime || 1;
          velocity  = ((e.touches[0].clientX - lastX) / dt) * 16;
          lastX     = e.touches[0].clientX;
          lastTime  = now;
          track.scrollLeft = startScroll - delta;
        }, { passive: true });

        track.addEventListener('touchend', function () {
          isDragging = false;
          rafId = requestAnimationFrame(momentum);
        }, { passive: true });
      }

      ['track-c1', 'track-c2', 'track-c3', 'track-c4', 'track-c5', 'track-c6'].forEach(initDragCarousel);

      // ── Flechas estáticas (solo indicativas) + hover-scroll en las franjas donde viven ─────
      (function () {
        if (window.innerWidth < 768) return; // en mobile la navegación es con botones táctiles

        var dragging = false;
        document.addEventListener('mousedown', function () { dragging = true; });
        document.addEventListener('mouseup',   function () { dragging = false; }, { passive: true });

        var HOT_WIDTH  = 90;  // px — ancho de la franja donde vive cada flecha
        var GRAD_WIDTH = 110; // px — ancho del gradiente que resalta esa franja

        var ARROW_RIGHT_SVG = '<svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M2 6.5h9M8 3l3.5 3.5L8 10" stroke="#141413" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        var ARROW_LEFT_SVG  = '<svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M11 6.5h-9M5 3L1.5 6.5 5 10" stroke="#141413" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

        function initHoverScroll(trackId) {
          var track = document.getElementById(trackId);
          if (!track) return;
          var wrapper = track.parentElement;
          wrapper.style.position = 'relative';

          var gradRight = document.createElement('div');
          gradRight.style.cssText = 'position:absolute; top:0; bottom:0; right:0; width:' + GRAD_WIDTH + 'px; background:linear-gradient(to left, rgba(0,0,0,0.35), rgba(0,0,0,0)); pointer-events:none; z-index:4; opacity:1; transition:opacity 0.2s ease;';
          wrapper.appendChild(gradRight);

          var arrowRight = document.createElement('div');
          arrowRight.style.cssText = 'position:absolute; top:50%; right:16px; width:36px; height:36px; background:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 12px rgba(0,0,0,0.18); pointer-events:none; z-index:5; opacity:1; transform:translateY(-50%) translateX(0); transition:opacity 0.2s ease, transform 0.2s ease;';
          arrowRight.innerHTML = ARROW_RIGHT_SVG;
          wrapper.appendChild(arrowRight);

          var gradLeft = document.createElement('div');
          gradLeft.style.cssText = 'position:absolute; top:0; bottom:0; left:0; width:' + GRAD_WIDTH + 'px; background:linear-gradient(to right, rgba(0,0,0,0.35), rgba(0,0,0,0)); pointer-events:none; z-index:4; opacity:0; transition:opacity 0.2s ease;';
          wrapper.appendChild(gradLeft);

          var arrowLeft = document.createElement('div');
          arrowLeft.style.cssText = 'position:absolute; top:50%; left:16px; width:36px; height:36px; background:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 12px rgba(0,0,0,0.18); pointer-events:none; z-index:5; opacity:0; transform:translateY(-50%) translateX(-16px); transition:opacity 0.2s ease, transform 0.2s ease;';
          arrowLeft.innerHTML = ARROW_LEFT_SVG;
          wrapper.appendChild(arrowLeft);

          function updateEdges() {
            var maxScroll = track.scrollWidth - track.clientWidth;
            var atEnd     = track.scrollLeft >= maxScroll - 2;
            var atStart   = track.scrollLeft <= 2;

            arrowRight.style.opacity   = atEnd ? '0' : '1';
            arrowRight.style.transform = 'translateY(-50%) translateX(' + (atEnd ? '16px' : '0') + ')';
            gradRight.style.opacity    = atEnd ? '0' : '1';

            arrowLeft.style.opacity   = atStart ? '0' : '1';
            arrowLeft.style.transform = 'translateY(-50%) translateX(' + (atStart ? '-16px' : '0') + ')';
            gradLeft.style.opacity    = atStart ? '0' : '1';
          }
          updateEdges();
          track.addEventListener('scroll', updateEdges);
          window.addEventListener('resize', updateEdges);

          var raf = null;
          var dir = 0; // 1 = derecha, -1 = izquierda, 0 = quieto

          function stop() {
            dir = 0;
            cancelAnimationFrame(raf); raf = null;
          }

          function scrollStep() {
            if (!dir || dragging) { raf = null; return; }
            var maxScroll = track.scrollWidth - track.clientWidth;
            if (dir > 0 && track.scrollLeft >= maxScroll) { stop(); return; }
            if (dir < 0 && track.scrollLeft <= 0)         { stop(); return; }
            track.scrollLeft += dir * 3;
            raf = requestAnimationFrame(scrollStep);
          }

          wrapper.addEventListener('mousemove', function (e) {
            if (dragging) return;
            var rect      = wrapper.getBoundingClientRect();
            var x         = e.clientX - rect.left;
            var maxScroll = track.scrollWidth - track.clientWidth;
            var inRightHot = x > rect.width - HOT_WIDTH && maxScroll > 2 && track.scrollLeft < maxScroll - 2;
            var inLeftHot  = x < HOT_WIDTH             && maxScroll > 2 && track.scrollLeft > 2;

            if (inRightHot) {
              if (dir !== 1) { dir = 1; cancelAnimationFrame(raf); raf = requestAnimationFrame(scrollStep); }
            } else if (inLeftHot) {
              if (dir !== -1) { dir = -1; cancelAnimationFrame(raf); raf = requestAnimationFrame(scrollStep); }
            } else if (dir !== 0) {
              stop();
            }
          });

          wrapper.addEventListener('mouseleave', stop);
        }

        ['track-c1','track-c2','track-c3','track-c4','track-c5','track-c6'].forEach(initHoverScroll);
      })();

      // ── Visualizador: toggle exterior / interior ──
      (function() {
        var indicator = document.getElementById('vis-indicator');
        var btnExt    = document.getElementById('vis-btn-ext');
        var btnInt    = document.getElementById('vis-btn-int');
        if (!indicator || !btnExt || !btnInt) return;
        function initVis() {
          indicator.style.width = btnExt.offsetWidth + 'px';
          indicator.style.transform = 'translateX(0)';
        }
        initVis();
        window.switchVis = function(view) {
          var intPanel  = document.getElementById('vis-interior');
          var colorWrap = document.getElementById('vis-color-wrap');
          window._visMode = view;

          var carBack    = document.getElementById('vis-car-back');
          var carFront   = document.getElementById('vis-car-front');
          var manualLink = document.getElementById('vis-manual-link');
          if (view === 'ext') {
            indicator.style.width = btnExt.offsetWidth + 'px';
            indicator.style.transform = 'translateX(0)';
            btnExt.style.color = '#fff';
            btnInt.style.color = '#141413';
            if (intPanel) {
              intPanel.style.transition = 'transform 0.52s cubic-bezier(0.25,0,0,1)';
              intPanel.style.transform  = 'translateX(100%)';
            }
            if (carBack)    carBack.style.visibility    = '';
            if (carFront)   carFront.style.visibility   = '';
            if (manualLink) { manualLink.style.color = '#141413'; manualLink.style.borderBottomColor = '#141413'; }
            var colorRow = document.getElementById('vis-color-row');
            var lineaRow = document.getElementById('vis-linea-row');
            if (colorRow) { colorRow.style.maxHeight = '68px'; colorRow.style.paddingTop = '4px'; colorRow.style.paddingBottom = '4px'; }
            if (lineaRow) { lineaRow.style.marginBottom = '14px'; }
          } else {
            indicator.style.width = btnInt.offsetWidth + 'px';
            indicator.style.transform = 'translateX(' + btnExt.offsetWidth + 'px)';
            btnInt.style.color = '#fff';
            btnExt.style.color = '#141413';
            if (carBack)    carBack.style.visibility    = 'hidden';
            if (carFront)   carFront.style.visibility   = 'hidden';
            if (manualLink) { manualLink.style.color = '#fff'; manualLink.style.borderBottomColor = 'rgba(255,255,255,0.6)'; }
            var colorRow = document.getElementById('vis-color-row');
            var lineaRow = document.getElementById('vis-linea-row');
            if (colorRow) { colorRow.style.maxHeight = '0'; colorRow.style.paddingTop = '0'; colorRow.style.paddingBottom = '0'; }
            if (lineaRow) { lineaRow.style.marginBottom = '0'; }
            if (intPanel && window.loadInteriorFor) {
              var linea = window.getVisLinea ? window.getVisLinea() : 'BRABUS';
              window.loadInteriorFor(linea);
              intPanel.style.transition = 'none';
              intPanel.style.transform  = 'translateX(100%)';
              requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                  intPanel.style.transition = 'transform 0.52s cubic-bezier(0.25,0,0,1)';
                  intPanel.style.transform  = 'translateX(0)';
                });
              });
            }
          }
        };
      })();

      // ── Visualizador: Línea + Color — sistema unificado ──
      (function() {
        var front       = document.getElementById('vis-car-front');
        var back        = document.getElementById('vis-car-back');
        var activeSrc   = '<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-c3.png';
        var activeLinea = 'BRABUS';
        var fading      = false;
        window.getVisLinea = function() { return activeLinea; };

        var ZOOM_SRCS = ['<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-c1.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-c2.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-c3.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-c4.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-c5.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-c6.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-c7.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-pro-c1.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-pro-c2.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-pro-c3.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-pro-c4.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-pro-c5.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-prop-c1.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-prop-c2.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-prop-c3.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-prop-c4.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/normalized/vis-prop-c5.png'];
        // All normalized images have identical car alignment — no per-image position adjustment needed
        function applyMobilePos(src) { /* no-op: images are normalized */ }

        function carFade(newSrc) {
          if (!newSrc || newSrc === activeSrc || fading) return;
          activeSrc = newSrc;
          fading = true;
          var scale = (ZOOM_SRCS.indexOf(newSrc) !== -1 && window.innerWidth > 767) ? 'scale(1.14)' : 'scale(1)';
          applyMobilePos(newSrc);
          back.src = newSrc;
          back.style.transform = scale;
          front.style.opacity = '0';
          setTimeout(function() {
            front.src = newSrc;
            front.style.transform = scale;
            front.style.transition = 'none';
            front.style.opacity    = '1';
            requestAnimationFrame(function() {
              requestAnimationFrame(function() {
                front.style.transition = 'opacity 0.4s cubic-bezier(0.25,0,0,1)';
                fading = false;
              });
            });
          }, 450);
        }

        function selectColorBtn(btn) {
          document.querySelectorAll('.vis-color-btn').forEach(function(b) {
            b.style.boxShadow = 'none';
          });
          btn.style.boxShadow = '0 0 0 2px #fff, 0 0 0 3.5px #141413';
          carFade(btn.getAttribute('data-img'));
        }

        // Aplicar zoom inicial
        (function() {
          var initScale = (ZOOM_SRCS.indexOf(activeSrc) !== -1 && window.innerWidth > 767) ? 'scale(1.14)' : 'scale(1)';
          back.style.transform = initScale;
          front.style.transform = initScale;
        })();

        document.addEventListener('click', function(e) {
          var btn = e.target.closest('.vis-color-btn');
          if (btn) selectColorBtn(btn);
        });

        document.querySelectorAll('.vis-linea-btn').forEach(function(btn) {
          btn.addEventListener('click', function() {
            var newLinea = this.getAttribute('data-linea');
            if (newLinea === activeLinea) return;

            document.querySelectorAll('.vis-linea-btn').forEach(function(b) {
              b.style.fontWeight = '400'; b.style.color = '#6B747B';
            });
            this.style.fontWeight = '700'; this.style.color = '#141413';

            var currentGroup = document.getElementById('vis-swatches-' + activeLinea);
            var newGroup     = document.getElementById('vis-swatches-' + newLinea);
            if (!newGroup) { activeLinea = newLinea; return; }

            fading = true;

            // Fade out current swatches in place + car simultaneously
            if (currentGroup) {
              currentGroup.style.transition = 'opacity 0.18s ease';
              currentGroup.style.opacity = '0';
            }
            front.style.opacity = '0';

            setTimeout(function() {
              // Reset & hide current group
              if (currentGroup) {
                currentGroup.style.display = 'none';
                currentGroup.style.transition = 'none';
                currentGroup.style.opacity = '1';
                currentGroup.style.transform = '';
              }

              // Prepare new group: invisible, shifted right
              newGroup.style.transition = 'none';
              newGroup.style.opacity = '0';
              newGroup.style.transform = 'translateX(36px)';
              newGroup.style.display = 'flex';

              // Select first swatch & update car
              var firstBtn = newGroup.querySelector('.vis-color-btn');
              if (firstBtn) {
                document.querySelectorAll('.vis-color-btn').forEach(function(b) { b.style.boxShadow = 'none'; });
                firstBtn.style.boxShadow = '0 0 0 2px #fff, 0 0 0 3.5px #111111';
                var newSrc = firstBtn.getAttribute('data-img');
                activeSrc = newSrc;
                var scale = (ZOOM_SRCS.indexOf(newSrc) !== -1 && window.innerWidth > 767) ? 'scale(1.14)' : 'scale(1)';
                applyMobilePos(newSrc);
                back.src  = newSrc;
                back.style.transform = scale;
                front.src = newSrc;
                front.style.transform = scale;
              }

              // Force reflow, then animate in
              newGroup.offsetHeight;
              newGroup.style.transition = 'opacity 0.25s ease, transform 0.25s cubic-bezier(0.25,0,0,1)';
              newGroup.style.opacity = '1';
              newGroup.style.transform = 'translateX(0)';

              // Fade in car
              front.style.transition = 'none';
              front.style.opacity = '1';
              requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                  front.style.transition = 'opacity 0.4s cubic-bezier(0.25,0,0,1)';
                  fading = false;
                });
              });

              activeLinea = newLinea;
              // Si estamos en interior, recargar el carousel con la nueva línea
              if (window._visMode === 'int' && window.loadInteriorFor) {
                window.loadInteriorFor(newLinea);
              }
            }, 200);
          });
        });
      })();
    })();

      // ── Interior carousel ──
      (function() {
        var panel = document.getElementById('vis-interior');
        var track = document.getElementById('vis-int-track');
        if (!panel || !track) return;

        var idx  = 0;
        var imgs = [];
        var STD  = ['<?php echo get_template_directory_uri(); ?>/assets/img/smart3/int-std-1.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/int-std-2.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/int-std-3.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/int-std-4.png'];
        var lineaMap = {
          'BRABUS': ['<?php echo get_template_directory_uri(); ?>/assets/img/smart3/int-brabus-1.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/int-brabus-2.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/int-brabus-3.png','<?php echo get_template_directory_uri(); ?>/assets/img/smart3/int-brabus-4.png'],
          'Pro': STD, 'Pro+': STD
        };

        window.loadInteriorFor = function(linea) {
          imgs = lineaMap[linea] || lineaMap['BRABUS'];
          idx  = 0;
          var W = panel.offsetWidth;
          track.innerHTML = '';
          imgs.forEach(function(src) {
            var el = document.createElement('img');
            el.src = src;
            el.alt = '';
            el.draggable = false;
            el.style.cssText = 'flex-shrink:0;width:' + W + 'px;height:100%;object-fit:cover;display:block;pointer-events:none;user-select:none;';
            track.appendChild(el);
          });
          track.style.transition = 'none';
          track.style.transform  = 'translateX(0)';
        };

        function goTo(n) {
          n   = Math.max(0, Math.min(imgs.length - 1, n));
          idx = n;
          track.style.transition = 'transform 0.4s cubic-bezier(0.25,0,0,1)';
          track.style.transform  = 'translateX(-' + (idx * panel.offsetWidth) + 'px)';
        }

        var dragging = false, sx = 0, cx = 0;
        function dStart(x) {
          dragging = true; sx = cx = x;
          track.style.transition = 'none';
          panel.style.cursor = 'grabbing';
        }
        function dMove(x) {
          if (!dragging) return;
          cx = x;
          var raw  = -idx * panel.offsetWidth + (x - sx);
          var minX = -(imgs.length - 1) * panel.offsetWidth;
          track.style.transform = 'translateX(' + Math.max(minX, Math.min(0, raw)) + 'px)';
        }
        function dEnd() {
          if (!dragging) return;
          dragging = false;
          panel.style.cursor = 'grab';
          var delta = cx - sx;
          if (delta < -40) goTo(idx + 1);
          else if (delta > 40) goTo(idx - 1);
          else goTo(idx);
        }

        panel.addEventListener('mousedown', function(e) { dStart(e.clientX); e.preventDefault(); });
        window.addEventListener('mousemove', function(e) { dMove(e.clientX); });
        window.addEventListener('mouseup', dEnd);
        panel.addEventListener('touchstart', function(e) { dStart(e.touches[0].clientX); }, {passive:true});
        panel.addEventListener('touchmove',  function(e) { dMove(e.touches[0].clientX); },  {passive:true});
        panel.addEventListener('touchend', dEnd, {passive:true});
      })();

    function toggleModelosDropdown() {
      const menu    = document.getElementById('modelos-menu');
      const chevron = document.getElementById('modelos-chevron');
      menu.classList.toggle('is-open');
      chevron.style.transform = menu.classList.contains('is-open') ? 'rotate(180deg)' : '';
    }
    document.addEventListener('click', function(e) {
      const dd = document.getElementById('modelos-dropdown');
      if (dd && !dd.contains(e.target)) {
        const menu    = document.getElementById('modelos-menu');
        const chevron = document.getElementById('modelos-chevron');
        if (menu)    menu.classList.remove('is-open');
        if (chevron) chevron.style.transform = '';
      }
    });
    function openNavMenu() {
      const menu     = document.getElementById('nav-menu');
      const drawer   = document.getElementById('nav-drawer');
      const backdrop = document.getElementById('nav-backdrop');
      menu.classList.remove('pointer-events-none');
      backdrop.classList.remove('opacity-0');
      backdrop.classList.add('opacity-100');
      drawer.classList.remove('-translate-x-full');
      document.body.style.overflow = 'hidden';
    }
    function closeNavMenu() {
      const menu     = document.getElementById('nav-menu');
      const drawer   = document.getElementById('nav-drawer');
      const backdrop = document.getElementById('nav-backdrop');
      backdrop.classList.remove('opacity-100');
      backdrop.classList.add('opacity-0');
      drawer.classList.add('-translate-x-full');
      setTimeout(() => {
        menu.classList.add('pointer-events-none');
        document.body.style.overflow = '';
      }, 300);
    }
  </script>
  <script>
    document.addEventListener('click', function(e) {
      var a = e.target.closest('a[href^="#"]');
      if (!a) return;
      var id = a.getAttribute('href');
      var target = document.querySelector(id);
      if (!target) return;
      e.preventDefault();
      var start = window.pageYOffset;
      var end = target.getBoundingClientRect().top + start;
      var duration = 480;
      var startTime = null;
      function step(ts) {
        if (!startTime) startTime = ts;
        var progress = Math.min((ts - startTime) / duration, 1);
        var ease = progress < 0.5 ? 2*progress*progress : -1+(4-2*progress)*progress;
        window.scrollTo(0, start + (end - start) * ease);
        if (progress < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    });
  </script>

  <!-- ── Carruseles mobile: navegación por flechas ── -->
  <style>
    @media (max-width: 767px) {
      .c-card { width: calc(100vw - 2.5rem) !important; }
    }
  </style>
  <script>
    (function () {
      if (window.innerWidth >= 768) return;

      var SVG_PREV = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.5 10.7949L7.14648 4.14844L7.85352 4.85547L1.20728 11.502H24V12.502H1.20703L7.85352 19.1484L7.14648 19.8555L0.5 13.2092C0.177734 12.887 0 12.4583 0 12.002C0 11.5457 0.177734 11.1169 0.5 10.7949Z" fill="#141413"/></svg>';
      var SVG_NEXT = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g transform="translate(24,0) scale(-1,1)"><path d="M0.5 10.7949L7.14648 4.14844L7.85352 4.85547L1.20728 11.502H24V12.502H1.20703L7.85352 19.1484L7.14648 19.8555L0.5 13.2092C0.177734 12.887 0 12.4583 0 12.002C0 11.5457 0.177734 11.1169 0.5 10.7949Z" fill="#141413"/></g></svg>';

      var indices = {};
      var GAP_PX = 64; /* 4rem */

      function step() {
        /* card width (100vw - 2.5rem = vw-40px) + gap (4rem = 64px) = vw + 24px */
        return window.innerWidth + 24;
      }

      function goTo(track, i, btnPrev, btnNext) {
        var cards = track.querySelectorAll('.c-card');
        i = Math.max(0, Math.min(i, cards.length - 1));
        indices[track.id] = i;
        track.style.transform = 'translateX(-' + (i * step()) + 'px)';
        var atStart = i === 0;
        var atEnd   = i === cards.length - 1;
        btnPrev.style.opacity = atStart ? '0.25' : '1';
        btnNext.style.opacity = atEnd   ? '0.25' : '1';
        btnPrev.style.pointerEvents = atStart ? 'none' : '';
        btnNext.style.pointerEvents = atEnd   ? 'none' : '';
      }

      ['track-c1','track-c2','track-c3','track-c4','track-c5','track-c6'].forEach(function (id) {
        var track = document.getElementById(id);
        if (!track) return;

        track.style.overflow    = 'visible';
        track.style.touchAction = 'none';
        track.style.transition  = 'transform 0.32s cubic-bezier(0.4,0,0.2,1)';
        track.style.gap         = '4rem';
        indices[id] = 0;

        var nav = document.createElement('div');
        nav.style.cssText = 'display:flex;justify-content:flex-end;gap:1.5rem;align-items:center;padding:0.75rem 1.25rem 0;';

        var btnPrev = document.createElement('button');
        btnPrev.innerHTML = SVG_PREV;
        btnPrev.setAttribute('aria-label', 'Anterior');
        btnPrev.style.cssText = 'background:none;border:none;padding:0;cursor:pointer;line-height:0;opacity:0.25;transition:opacity 0.2s;pointer-events:none;';
        btnPrev.addEventListener('click', function () { goTo(track, (indices[id] || 0) - 1, btnPrev, btnNext); });

        var btnNext = document.createElement('button');
        btnNext.innerHTML = SVG_NEXT;
        btnNext.setAttribute('aria-label', 'Siguiente');
        btnNext.style.cssText = 'background:none;border:none;padding:0;cursor:pointer;line-height:0;transition:opacity 0.2s;';
        btnNext.addEventListener('click', function () { goTo(track, (indices[id] || 0) + 1, btnPrev, btnNext); });

        nav.appendChild(btnPrev);
        nav.appendChild(btnNext);
        track.parentNode.insertBefore(nav, track.nextSibling);
      });
    })();
  </script>
<?php wp_footer(); ?>
<script>
  setFddOptions('modelo',<?php echo wp_json_encode(array_map(function ($v) {
    return [$v['slug_form'], 'smart #3 ' . $v['nombre_version']];
  }, $smart_versiones_s3)); ?>);
</script>
</body>
</html>
