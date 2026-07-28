<?php /* Template Name: Sobre Smart */ ?>
<?php
$smart_cards_sobre    = smart_get_feature_cards('sobre_smart');
$smart_historia_bloque = smart_get_contenido('historia_institucional');
$smart_historia        = $smart_historia_bloque[0]['contenido'] ?? '';
$smart_hero_sobre      = smart_get_hero('sobre_smart');
?>
<?php get_header(); ?>
<?php get_template_part('partials/header'); ?>
  <style>
    .sobre-scroll p { color:#fff; font-family:'FOR_smart_Sans','Helvetica Neue',Helvetica,Arial,sans-serif; font-size:19px; line-height:140%; margin:0 0 1.25rem; }
    .sobre-scroll p:last-child { margin-bottom:0; }
    @media (max-width: 767px) {
      .sobre-hero-img {
        content: url('<?php echo esc_url($smart_hero_sobre['mobile']); ?>');
        object-position: center center !important;
      }
      .sobre-hero-text-wrap {
        bottom: unset !important;
        top: 110px !important;
        padding-left: 24px !important;
        padding-right: 24px !important;
        padding-bottom: 0 !important;
      }
      .sobre-hero-title {
        font-size: 36px !important;
        line-height: 1.2 !important;
        letter-spacing: -0.02em !important;
        font-family: 'FOR_smart_Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif !important;
      }
      .sobre-banner-img-wrap {
        padding: 0 !important;
      }
      .sobre-banner-text-col {
        padding-left: 28px !important;
        padding-right: 0 !important;
      }
      .sobre-banner-text-col .sobre-scroll {
        padding-right: 25px !important;
      }
      .sobre-banner-text-col .sobre-scroll p {
        font-size: 17px !important;
      }

      /* Carrusel — apilado vertical */
      #sobre-carousel {
        height: auto !important;
        position: static !important;
      }
      #sobre-carousel > .flex {
        flex-direction: column !important;
        height: auto !important;
      }
      #sobre-carousel > .flex > div:first-child {
        width: 100% !important;
        padding-left: 24px !important;
        padding-right: 24px !important;
        padding-top: 40px !important;
        padding-bottom: 0 !important;
      }
      #sobre-carousel-viewport {
        overflow: visible !important;
        padding-top: 24px !important;
        padding-bottom: 40px !important;
        padding-left: 24px !important;
        padding-right: 24px !important;
      }
      #track-sobre-carousel {
        flex-direction: column !important;
        height: auto !important;
        transform: none !important;
        gap: 2rem !important;
      }
      #track-sobre-carousel > div {
        width: 100% !important;
        height: auto !important;
      }
      #track-sobre-carousel > div > div:first-child {
        height: auto !important;
      }
      #track-sobre-carousel > div > div:first-child img {
        min-height: 260px !important;
        max-height: 320px !important;
      }
    }
    .sobre-scroll::-webkit-scrollbar { width: 3px; }
    .sobre-scroll::-webkit-scrollbar-track { background: transparent; }
    .sobre-scroll::-webkit-scrollbar-thumb { background: #4b4b4b; border-radius: 2px; }
    #track-sobre-carousel h3 { font-weight: 700; }
    @media (min-width: 768px) {
      .sobre-scroll { padding-right: 6px; }
      #track-sobre-carousel h3 { font-weight: 400; }
    }
    @media (max-width: 767px) {
      .sobre-scroll::-webkit-scrollbar { width: 7px; }
      .sobre-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.75); border-radius: 9999px; }
    }
  </style>
  <!-- ================================================================
       HERO
  ================================================================ -->
  <section class="relative w-full h-screen min-h-[640px] overflow-hidden">
    <div class="absolute inset-0 bg-neutral-800">
      <img src="<?php echo esc_url($smart_hero_sobre['desktop']); ?>" alt="Sobre smart" class="sobre-hero-img w-full h-full object-cover" />
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
          <button onclick="toggleModelosDropdown()" class="flex items-center gap-1 text-white text-sm font-smart-sans font-normal uppercase tracking-wide leading-6">
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

    <div class="sobre-hero-text-wrap absolute bottom-0 left-0 right-0 z-10" style="padding-left:56px; padding-bottom:68px;">
      <h1 class="sobre-hero-title font-smart-next font-bold text-white" style="font-size:45px; line-height:1.15;">smart: tres décadas<br>reinventando el auto urbano.</h1>
    </div>
  </section>

  <!-- ================================================================
       BANNER — QUIÉNES SOMOS
  ================================================================ -->
  <section class="w-full bg-white px-5 md:px-14 py-10 md:py-14">
    <div class="bg-[#141413] overflow-hidden">
      <div class="flex flex-col md:flex-row">

        <!-- Imagen izquierda -->
        <div class="sobre-banner-img-wrap md:w-[50%] flex-shrink-0" style="padding: 44px 0 44px 40px;">
          <img
            src="<?php echo get_template_directory_uri(); ?>/assets/img/sobre-smart/banner.png"
            alt="smart"
            class="w-full h-full object-cover"
            style="min-height:400px;"
          />
        </div>

        <!-- Texto + botón derecha -->
        <div class="sobre-banner-text-col md:w-[45%] flex flex-col justify-between gap-8" style="padding: 44px 0 44px 48px;">

          <!-- Texto con scroll interno -->
          <div class="sobre-scroll overflow-y-auto pr-3" style="width:100%; height:432px;">
            <?php echo $smart_historia; ?>
          </div>

          <!-- Botón -->
          <div class="flex-shrink-0">
            <a
              href="<?php echo home_url('/buscador/'); ?>"
              class="inline-flex items-center justify-center bg-white text-black font-bold rounded-full hover:bg-neutral-200 transition-colors font-smart-sans" style="width:242px; height:50px; font-size:14px; letter-spacing:0.02em;"
            >
              Encontrá tu concesionario
            </a>
          </div>

        </div>
      </div>
    </div>
  </section>

  <!-- ================================================================
       RED DE CONCESIONARIOS — CARRUSEL
  ================================================================ -->
  <section id="sobre-carousel" class="w-full bg-white overflow-hidden" style="height:861.85px;">
    <div class="flex flex-col md:flex-row" style="height:100%;">

      <!-- Texto izquierda -->
      <div class="px-5 md:flex-shrink-0 flex flex-col justify-start py-10 md:py-0" style="width:471px; padding-left:56px; padding-right:48px; padding-top:69px;">
        <div style="width:375px; max-width:100%;">
          <div class="flex flex-col gap-3">
            <p class="font-smart-sans font-normal" style="font-size:20px; line-height:120%; letter-spacing:-0.01em; color:#6B747B;">smart en Argentina.</p>
            <p class="font-smart-sans font-normal" style="font-size:29px; line-height:1.25; color:#141413;">
              La red de concesionarios smart te acompaña desde la primera consulta hasta el primer kilómetro y todos los que siguen. Encontrá el punto de venta más cercano y agendá tu prueba de manejo.
            </p>
          </div>
        </div>
      </div>

      <!-- Carrusel -->
      <div id="sobre-carousel-viewport" class="flex-1 min-w-0 flex items-center" style="padding-top:69px; padding-bottom:63px; overflow:hidden;">
        <div id="track-sobre-carousel" class="flex select-none" style="gap:19px; height:729.85px; overflow-x:scroll; scrollbar-width:none; -ms-overflow-style:none; cursor:grab; touch-action:pan-y;">

          <?php foreach ($smart_cards_sobre as $c): ?>
          <div class="flex-shrink-0 flex flex-col" style="width:380.09px; height:729.85px;">
            <div style="height:471.91px; flex-shrink:0; overflow:hidden;">
              <img src="<?php echo esc_url($c['imagen']); ?>" alt="<?php echo esc_attr($c['alt']); ?>" class="w-full h-full object-cover" draggable="false" />
            </div>
            <div style="padding-top:27.04px; flex:1;">
              <h3 class="font-smart-next" style="font-size:42.25px; line-height:120%; letter-spacing:-0.02em; color:#000000; margin-bottom:12.48px;"><?php echo esc_html($c['titulo']); ?></h3>
              <p class="font-smart-sans font-normal" style="font-size:16.9px; line-height:140%; color:#000000;"><?php echo esc_html($c['descripcion']); ?></p>
            </div>
          </div>
          <?php endforeach; ?>

        </div>
      </div>

    </div>
  </section>

  <!-- ================================================================
       FOOTER
  ================================================================ -->
  <?php get_template_part('partials/footer'); ?>

  <script>
    /* ── Carrusel "Red de concesionarios" (desktop) — mismo drag-to-scroll
         con inercia y hover-scroll en los bordes que home/smart1/smart3. ── */
    (function () {
      if (window.innerWidth < 768) return;

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

      initDragCarousel('track-sobre-carousel');

      // ── Flechas estáticas (solo indicativas) + hover-scroll en las franjas donde viven ─────
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

      initHoverScroll('track-sobre-carousel');
    })();
  </script>

  <script>
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
