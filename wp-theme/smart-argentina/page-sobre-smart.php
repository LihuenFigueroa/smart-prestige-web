<?php /* Template Name: Sobre Smart */ ?>
<?php
$smart_cards_sobre    = smart_get_feature_cards('sobre_smart');
$smart_historia_bloque = smart_get_contenido('historia_institucional');
$smart_historia        = $smart_historia_bloque[0]['contenido'] ?? '';
?>
<?php get_header(); ?>
<?php get_template_part('partials/header'); ?>
  <style>
    .sobre-scroll p { color:#fff; font-family:'FOR_smart_Sans','Helvetica Neue',Helvetica,Arial,sans-serif; font-size:21px; line-height:140%; margin:0 0 1.25rem; }
    .sobre-scroll p:last-child { margin-bottom:0; }
    @media (max-width: 767px) {
      .sobre-hero-img {
        content: url('<?php echo get_template_directory_uri(); ?>/assets/img/sobre-smart/hero-mobile.png');
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
        font-family: 'FOR_smart_Next', 'Helvetica Neue', Helvetica, Arial, sans-serif !important;
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
      #sobre-pin-wrap,
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
    @media (min-width: 768px) {
      .sobre-scroll { padding-right: 22px; }
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
      <img src="<?php echo get_template_directory_uri(); ?>/assets/img/sobre-smart/hero.jpg" alt="Sobre smart" class="sobre-hero-img w-full h-full object-cover" />
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

    <div class="sobre-hero-text-wrap absolute bottom-0 left-0 right-0 z-10" style="padding-left:56px; padding-bottom:68px;">
      <h1 class="sobre-hero-title font-smart-next font-normal text-white" style="font-size:45px; line-height:1.15;">smart: tres décadas<br>reinventando el auto urbano.</h1>
    </div>
  </section>

  <!-- ================================================================
       BANNER — QUIÉNES SOMOS
  ================================================================ -->
  <section class="w-full bg-white px-5 md:px-14 py-10 md:py-14">
    <div class="max-w-[1320px] mx-auto bg-[#141413] overflow-hidden">
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
        <div class="sobre-banner-text-col md:w-[45%] flex flex-col justify-between gap-8" style="padding: 44px 64px 44px 48px;">

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
  <div id="sobre-pin-wrap" style="position:relative;">
  <section id="sobre-carousel" class="w-full bg-white overflow-hidden" style="height:693.42px;">
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
        <div id="track-sobre-carousel" class="flex select-none" style="gap:19px; height:561.42px; will-change:transform;">

          <?php foreach ($smart_cards_sobre as $c): ?>
          <div class="flex-shrink-0 flex flex-col" style="width:292.38px; height:561.42px;">
            <div style="height:363.01px; flex-shrink:0; overflow:hidden;">
              <img src="<?php echo esc_url($c['imagen']); ?>" alt="<?php echo esc_attr($c['alt']); ?>" class="w-full h-full object-cover" draggable="false" />
            </div>
            <div style="padding-top:20.8px; flex:1;">
              <h3 class="font-smart-sans font-normal" style="font-size:32.5px; line-height:120%; letter-spacing:-0.02em; color:#000000; margin-bottom:9.6px;"><?php echo esc_html($c['titulo']); ?></h3>
              <p class="font-smart-sans font-normal" style="font-size:13px; line-height:140%; color:#000000;"><?php echo esc_html($c['descripcion']); ?></p>
            </div>
          </div>
          <?php endforeach; ?>

        </div>
      </div>

    </div>
  </section>
  </div>

  <!-- ================================================================
       FOOTER
  ================================================================ -->
  <?php get_template_part('partials/footer'); ?>


  <script>
    /* ── Scroll-driven horizontal carousel (sobre-smart) ── */
    (function () {
      if (window.innerWidth < 768) return;

      var wrap     = document.getElementById('sobre-pin-wrap');
      var section  = document.getElementById('sobre-carousel');
      var viewport = document.getElementById('sobre-carousel-viewport');
      var track    = document.getElementById('track-sobre-carousel');
      if (!wrap || !section || !viewport || !track) return;

      var sectionH     = 693.42;
      var startHoldPx  = 300;
      var endHoldPx    = 500;
      var stickyTopPx  = -40;

      function maxTranslate() {
        return track.scrollWidth - viewport.clientWidth;
      }

      function init() {
        var extra = maxTranslate();
        if (extra <= 0) return;
        wrap.style.height      = (sectionH + extra + startHoldPx + endHoldPx) + 'px';
        section.style.position = 'sticky';
        section.style.top      = stickyTopPx + 'px';
        section.style.zIndex   = '1';
      }

      function onScroll() {
        var wrapTop  = wrap.getBoundingClientRect().top + window.scrollY;
        var progress = window.scrollY - wrapTop;
        var max      = maxTranslate();
        var offset   = Math.max(0, Math.min(progress - startHoldPx, max));
        track.style.transform = 'translateX(-' + offset + 'px)';
      }

      window.addEventListener('load', function () { init(); onScroll(); });
      window.addEventListener('scroll', onScroll, { passive: true });
      window.addEventListener('resize', function () {
        if (window.innerWidth < 768) {
          wrap.style.height      = '';
          section.style.position = '';
          section.style.top      = '';
          track.style.transform  = '';
        } else {
          init();
          onScroll();
        }
      });
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
