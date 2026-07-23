<?php /* Template Name: Buscador */ ?>
<?php
$smart_concesionarios = smart_get_concesionarios();
$smart_hero_buscador  = smart_get_hero('buscador');

wp_register_script('smart-buscador-data', '');
wp_enqueue_script('smart-buscador-data');
wp_localize_script('smart-buscador-data', 'smartConcesionarios', array_map(function ($c) {
  return [
    'nombre'    => $c['nombre'],
    'direccion' => $c['direccion_completa'],
    'tags'      => $c['tags'],
    'lat'       => $c['lat'],
    'lng'       => $c['lng'],
    'telefono'  => $c['telefono'],
  ];
}, $smart_concesionarios));
?>
<?php get_header(); ?>
<?php get_template_part('partials/header'); ?>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <style>
    .bus-hero-title { font-size: 45px; line-height: 1.1; margin-bottom: 8px; }
    .bus-hero-label { font-size: 18px; line-height: 1.5; margin-bottom: 16px; }
    @media (max-width: 767px) {
      .bus-hero-img {
        content: url('<?php echo esc_url($smart_hero_buscador['mobile']); ?>');
        object-position: center center !important;
      }
      .bus-hero-wrap {
        padding-left: 24px !important;
        padding-bottom: 32px !important;
      }
      .bus-hero-title {
        font-size: 36px;
        line-height: 1.15;
        letter-spacing: -0.02em;
        margin-bottom: 8px;
      }
      .bus-hero-label {
        font-size: 15px;
        line-height: 1.4;
        margin-bottom: 0;
      }
      .bus-hero-btn {
        display: none !important;
      }

      /* Sección buscador mobile */
      #buscador {
        height: auto !important;
        padding: 32px 20px 48px !important;
      }
      #buscador-inner {
        flex-direction: column !important;
        height: auto !important;
        gap: 0 !important;
      }
      #buscador-left {
        width: 100% !important;
        height: auto !important;
      }
      #buscador-map-col {
        width: 100% !important;
        height: 340px !important;
        flex: none !important;
        margin-top: 24px !important;
      }
      #lista-concesionarios {
        overflow-y: scroll !important;
        flex: none !important;
        margin-top: 24px !important;
        max-height: 560px !important;
      }
    }
    /* Contactar: desktop muestra número, mobile muestra botón */
    .bus-contact-btn   { display: none; }
    .bus-contact-phone { display: inline; font-family:'FOR_smart_Sans',sans-serif; font-size:13px; color:#141413; }
    @media (max-width: 767px) {
      .bus-contact-btn   { display: inline-flex !important; align-items:center; height:36px; padding:0 20px; border-radius:18px; font-size:12px; color:#fff; background:#141413; text-decoration:none; }
      .bus-contact-phone { display: none !important; }
    }
    .smart-marker { background: none; border: none; }
    .smart-popup .leaflet-popup-content-wrapper {
      border-radius: 4px;
      border: 1px solid #E5E7EB;
      box-shadow: 0 4px 16px rgba(0,0,0,0.10);
      padding: 0;
    }
    .smart-popup .leaflet-popup-content {
      margin: 18px 20px 16px;
    }
    .smart-popup .leaflet-popup-tip-container { display: none; }
    .smart-popup .leaflet-popup-close-button {
      color: #141413;
      font-size: 18px;
      padding: 6px 8px;
    }
  </style>
  <!-- ================================================================
       HERO
  ================================================================ -->
  <section class="relative w-full h-screen min-h-[640px] overflow-hidden">
    <div class="absolute inset-0 bg-neutral-800">
      <img src="<?php echo esc_url($smart_hero_buscador['desktop']); ?>" alt="Buscador de concesionarios" class="bus-hero-img w-full h-full object-cover" style="object-position:center top;" />
    </div>
    <div class="absolute top-0 left-0 right-0 pointer-events-none" style="height:170px; z-index:5; background:linear-gradient(to bottom,rgba(20,20,19,0.65) 0%,rgba(20,20,19,0) 100%);"></div>
    <div class="absolute bottom-0 left-0 right-0 pointer-events-none" style="height:261px; z-index:5; background:linear-gradient(to bottom,rgba(0,0,0,0) 0%,rgba(0,0,0,0.85) 100%);"></div>

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

    <div class="bus-hero-wrap absolute bottom-0 left-0 right-0 z-10" style="padding-left:56px; padding-bottom:56px;">
      <h1 class="bus-hero-title font-smart-next font-normal text-white">Buscador de concesionarios.</h1>
      <p class="bus-hero-label font-smart-sans text-white" style="opacity:0.9;">Encontrá tu concesionario más cercano.</p>
      <div>
        <a href="#buscador" class="bus-hero-btn font-smart-sans" style="display:inline-flex; align-items:center; height:40px; padding:0 24px; background:white; border-radius:9999px; font-size:14px; color:#141413; text-decoration:none; font-weight:700; letter-spacing:-0.025em;">Realizar consulta</a>
      </div>
    </div>
  </section>

  <!-- ================================================================
       BUSCADOR + MAPA
  ================================================================ -->
  <section id="buscador" class="w-full bg-white" style="padding:64px 56px 80px; height:100vh; box-sizing:border-box;">
    <div id="buscador-inner" style="display:flex; gap:48px; height:100%;">

      <!-- Columna izquierda: buscador + listado -->
      <div id="buscador-left" style="width:380px; flex-shrink:0; display:flex; flex-direction:column; height:100%;">

        <!-- Título -->
        <h2 class="font-smart-next" style="font-size:27px; color:#141413; line-height:1.2; margin-bottom:24px;">Encontrá la sucursal smart más cercana.</h2>

        <!-- Input -->
        <div style="border:1px solid #BDBDBD; padding:12px 16px; margin-bottom:6px;">
          <input type="text" placeholder="CIUDAD, CÓDIGO POSTAL O CONCESIONARIO" id="buscador-input"
            onkeydown="if(event.key==='Enter') filtrarConcesionarios(this.value)"
            class="font-smart-sans"
            style="width:100%; background:transparent; border:none; outline:none; font-size:12px; color:#141413; letter-spacing:0.06em;" />
        </div>
        <p class="font-smart-sans" style="font-size:11px; color:#BDBDBD; margin-bottom:20px;">Ingresá tu barrio o localidad</p>

        <!-- Botones -->
        <div style="display:flex; gap:12px; margin-bottom:40px;">
          <button class="font-smart-sans" style="height:40px; padding:0 24px; border:1.34px solid #141413; border-radius:20px; font-size:12px; color:#141413; background:transparent; cursor:pointer; letter-spacing:0.03em;">Filtros</button>
          <button class="font-smart-sans" onclick="filtrarConcesionarios(document.getElementById('buscador-input').value)" style="height:40px; padding:0 24px; border-radius:20px; font-size:12px; color:#fff; background:#141413; border:none; cursor:pointer; letter-spacing:0.03em;">Buscar</button>
        </div>

        <!-- Listado de concesionarios -->
        <div id="lista-concesionarios" style="overflow-y:auto; flex:1;">

          <?php foreach ($smart_concesionarios as $i => $c): $isLast = $i === count($smart_concesionarios) - 1; ?>
          <div class="concesionario-item" data-tags="<?php echo esc_attr($c['tags']); ?>" style="padding:20px 0; border-top:1px solid #E5E7EB;<?php echo $isLast ? ' border-bottom:1px solid #E5E7EB;' : ''; ?>">
            <p class="font-smart-next" style="font-size:16px; color:#141413; margin-bottom:4px;"><?php echo esc_html($c['nombre']); ?></p>
            <p class="font-smart-sans" style="font-size:13px; color:#141413; margin-bottom:2px;"><?php echo esc_html($c['direccion_completa']); ?>.</p>
            <p class="font-smart-sans" style="font-size:13px; color:#141413; margin-bottom:16px;"><?php echo esc_html($c['tipo_label']); ?></p>
            <?php if (!empty($c['telefono'])): ?>
            <a href="tel:<?php echo esc_attr(preg_replace('/[\s\-]/', '', $c['telefono'])); ?>" class="bus-contact-btn">Contactar</a>
            <span class="bus-contact-phone"><?php echo esc_html($c['telefono']); ?></span>
            <?php else: ?>
            <button class="font-smart-sans" style="height:36px; padding:0 20px; border-radius:18px; font-size:12px; color:#fff; background:#141413; border:none; cursor:pointer;">Contactar</button>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>

        <p class="font-smart-sans hidden" id="no-resultados" style="font-size:12px; color:#BDBDBD; margin-top:20px;">No se encontraron concesionarios para esa búsqueda.</p>

        </div>

      </div>

      <!-- Columna derecha: mapa -->
      <div id="buscador-map-col" style="flex:1; height:100%; overflow:hidden;">
        <div id="map" style="width:100%; height:100%;"></div>
      </div>

    </div>
  </section>

  <!-- ================================================================
       FOOTER
  ================================================================ -->
  <?php get_template_part('partials/footer'); ?>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    const sucursales = window.smartConcesionarios || [];

    const map = L.map('map');
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    function makePinIcon(size) {
      const [w, h] = size;
      const cx = w / 2, r = w * 0.2;
      return new L.DivIcon({
        className: 'smart-marker',
        html: `<svg width="${w}" height="${h}" viewBox="0 0 30 42" xmlns="http://www.w3.org/2000/svg" style="filter:drop-shadow(0 2px 4px rgba(0,0,0,0.35));display:block;">
                 <path d="M15 0C6.7 0 0 6.7 0 15c0 11.2 15 27 15 27s15-15.8 15-27C30 6.7 23.3 0 15 0z" fill="#141413"/>
                 <circle cx="15" cy="15" r="6" fill="#fff"/>
               </svg>`,
        iconSize: size,
        iconAnchor: [w / 2, h],
        popupAnchor: [0, -h + 4]
      });
    }

    const pinIcon     = makePinIcon([30, 42]);
    const pinIconHover = makePinIcon([38, 53]);

    const popupOptions = {
      className: 'smart-popup',
      maxWidth: 260,
      minWidth: 200
    };

    window.mapMarkers = sucursales.map(s => {
      const marker = L.marker([s.lat, s.lng], { icon: pinIcon }).addTo(map);
      marker.bindPopup(
        `<p class="font-smart-next" style="font-size:16px;color:#141413;margin:0 0 4px;">${s.nombre}</p>
         <p class="font-smart-sans" style="font-size:13px;color:#141413;margin:0 0 ${s.telefono ? '12px' : '0'};">${s.direccion}</p>
         ${s.telefono ? `<a href="tel:${s.telefono.replace(/[\s\-]/g,'')}" class="bus-contact-btn font-smart-sans">Contactar</a><span class="bus-contact-phone font-smart-sans">${s.telefono}</span>` : ''}`,
        popupOptions
      );
      return marker;
    });

    let activeItemIdx = null;

    const allLatLngs = sucursales.map(s => [s.lat, s.lng]);
    map.fitBounds(allLatLngs, { padding: [40, 40] });

    function activateMarker(i) {
      if (activeItemIdx !== null && activeItemIdx !== i) {
        window.mapMarkers[activeItemIdx].setIcon(pinIcon);
      }
      if (window.mapMarkers[i]) window.mapMarkers[i].setIcon(pinIconHover);
      activeItemIdx = i;
    }

    function deactivateMarker(i) {
      if (window.mapMarkers[i]) window.mapMarkers[i].setIcon(pinIcon);
      if (activeItemIdx === i) activeItemIdx = null;
    }

    // Click / hover en item → destacar marcador, centrar mapa, abrir popup
    document.querySelectorAll('.concesionario-item').forEach((item, i) => {
      item.style.cursor = 'pointer';
      item.addEventListener('mouseenter', () => activateMarker(i));
      item.addEventListener('mouseleave', () => {
        if (activeItemIdx === i && !window.mapMarkers[i]._popup.isOpen()) deactivateMarker(i);
      });
      item.addEventListener('click', function(e) {
        if (e.target.tagName === 'BUTTON') return;
        activateMarker(i);
        const marker = window.mapMarkers[i];
        if (marker) { map.setView(marker.getLatLng(), 15); marker.openPopup(); }
        if (window.innerWidth < 768) {
          document.getElementById('buscador-map-col').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      });
      const btn = item.querySelector('button');
      if (btn) btn.addEventListener('click', function() {
        activateMarker(i);
        const marker = window.mapMarkers[i];
        if (marker) { map.setView(marker.getLatLng(), 15); marker.openPopup(); }
        if (window.innerWidth < 768) {
          document.getElementById('buscador-map-col').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      });
    });

    // Al cerrar popup, vuelve al tamaño normal
    window.mapMarkers.forEach((marker, i) => {
      marker.on('popupclose', () => deactivateMarker(i));
    });

    function filtrarConcesionarios(query) {
      const items = document.querySelectorAll('.concesionario-item');
      const noRes = document.getElementById('no-resultados');
      const q = query.toLowerCase().trim();
      let visibleLatLngs = [];
      items.forEach((item, i) => {
        const tags = item.getAttribute('data-tags') || '';
        const name = item.querySelector('p').textContent.toLowerCase();
        const match = !q || tags.includes(q) || name.includes(q);
        item.style.display = match ? '' : 'none';
        const marker = window.mapMarkers[i];
        if (marker) {
          if (match) {
            marker.addTo(map);
            visibleLatLngs.push(marker.getLatLng());
          } else {
            map.removeLayer(marker);
          }
        }
      });
      noRes.classList.toggle('hidden', visibleLatLngs.length > 0 || !q);
      if (visibleLatLngs.length > 0) {
        map.fitBounds(visibleLatLngs, { padding: [40, 40] });
      } else if (q) {
        map.fitBounds(allLatLngs, { padding: [40, 40] });
      }
    }
  </script>


  <script>
    // Reordenamiento mobile: título → input → botones → mapa → lista
    (function () {
      if (window.innerWidth >= 768) return;
      var inner   = document.getElementById('buscador-inner');
      var mapCol  = document.getElementById('buscador-map-col');
      var lista   = document.getElementById('lista-concesionarios');
      var noRes   = document.getElementById('no-resultados');
      var left    = document.getElementById('buscador-left');
      if (!inner || !mapCol || !lista || !left) return;
      // sacar lista (y mensaje sin resultados) del left-col
      left.removeChild(lista);
      if (noRes) left.removeChild(noRes);
      // mapa después del left-col
      inner.appendChild(mapCol);
      // lista después del mapa
      inner.appendChild(lista);
      if (noRes) inner.appendChild(noRes);
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
<?php wp_footer(); ?>
</body>
</html>
